<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Result\Unsupported;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Whether the installation's console runs, and, when it does not, whether the
 * caller hears something it can act on.
 */
final class Typo3CliTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        putenv('IS_DDEV_PROJECT');
        Instance::discoverFrom(null);
        Typo3Cli::useRunner(null);
        Typo3Cli::forget();
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function aFailureIsDiagnosedOnlyWhereTheMessageDoesNotSayEnough(): void
    {
        self::assertStringContainsString(
            'no TYPO3 schema yet',
            Typo3Cli::diagnose("An exception occurred while executing a query: Table 'db.tx_scheduler_task' doesn't exist")
        );
        self::assertStringContainsString('no TYPO3 schema yet', Typo3Cli::diagnose('SQLSTATE[42S02]: Base table or view not found'));
        self::assertSame('', Typo3Cli::diagnose('php_network_getaddresses: getaddrinfo for db failed'));
    }

    #[Test]
    public function withoutAnInstallationThereIsNothingToRun(): void
    {
        Instance::discoverFrom(null);
        Typo3Cli::forget();

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('no TYPO3 installation', Typo3Cli::reason());
    }

    #[Test]
    public function anInstallationWithoutAConsoleSaysSo(): void
    {
        $this->discover($this->installation());

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('bin/typo3', Typo3Cli::reason());
    }

    #[Test]
    public function aCheckoutThatWasNeverInstalledSaysThat(): void
    {
        // The core checkouts below .checkouts/ are worktrees with no install in
        // them, and the core monorepo declares `bin-dir: bin`. So the paths the
        // reason lists are exactly the ones composer install writes. Naming
        // only them reads as "a core checkout cannot answer this".
        $root = $this->installation();
        $this->discover($root);

        self::assertStringContainsString('dependencies are not installed', Typo3Cli::reason());
        self::assertStringContainsString('vendor/autoload.php', Typo3Cli::reason());
    }

    #[Test]
    public function anInstalledCheckoutWhoseConsoleSitsElsewhereIsNotBlamed(): void
    {
        // The other half: the autoloader is there, so the console is somewhere
        // this did not look, and the caller is told that and nothing else.
        $root = $this->installation();
        mkdir($root . '/vendor', 0o777, true);
        file_put_contents($root . '/vendor/autoload.php', '<?php
');
        $this->discover($root);

        self::assertStringContainsString('has no TYPO3 console', Typo3Cli::reason());
        self::assertStringNotContainsString('dependencies are not installed', Typo3Cli::reason());
    }

    #[Requirement('R-DIS-003')]
    #[Test]
    public function aConsoleInTheDeclaredBinDirectoryIsFound(): void
    {
        // What the TYPO3 extension test setup produces: the console exists and
        // runs, but not under either Composer default. Probing only those left
        // five installation-backed tools without an answer in a checkout whose
        // console was one directory away.
        $root = $this->installation([
            'config' => ['bin-dir' => '.build/bin', 'vendor-dir' => '.build/vendor'],
        ]);
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        // Nothing here says the console runs — that depends on the machine. It
        // says the reason is no longer that the search found none.
        self::assertStringNotContainsString('has no TYPO3 console', Typo3Cli::reason());
    }

    #[Requirement('R-DIS-003')]
    #[Test]
    public function anAbsoluteBinDirectoryBelowTheRootIsTheSameDirectory(): void
    {
        // Composer accepts an absolute bin-dir and installs the binaries there
        // (2.9.5, checked against a project that declares one). In absolute
        // form it names the very directory the relative form names, so a drop
        // of it lost a console that was there. That is the failure the declared
        // bin-dir read exists for in the first place, under a second form.
        $root = $this->installation();
        $root = (string) realpath($root);
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'typo3/cms',
            'type' => 'typo3-cms-core',
            'config' => ['bin-dir' => $root . '/.build/bin', 'vendor-dir' => $root . '/.build/vendor'],
        ], JSON_THROW_ON_ERROR));
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        self::assertSame('.build/bin', Typo3Cli::binDirectory($root));
        self::assertSame('.build/vendor/autoload.php', Typo3Cli::autoloader($root));
        self::assertStringNotContainsString('has no TYPO3 console', Typo3Cli::reason());
    }

    #[Requirement('R-DIS-003')]
    #[Test]
    public function aBinDirectoryOutsideTheRootIsNamed(): void
    {
        // It has no form the invocation can use. The console runs relative to
        // the root, and in a container the host path is not there. What the
        // caller gets instead is the declaration it wrote and the setting that
        // replaces it.
        $this->discover($this->installation(['config' => ['bin-dir' => '/opt/typo3/bin']]));

        $reason = Typo3Cli::reason();
        self::assertStringContainsString('/opt/typo3/bin', $reason);
        self::assertStringContainsString(Typo3Cli::CONSOLE_VARIABLE, $reason);
    }

    #[Test]
    public function aMissingConsoleNamesEveryPathThatWasProbed(): void
    {
        $this->discover($this->installation(['config' => ['bin-dir' => '.build/bin']]));

        $reason = Typo3Cli::reason();
        self::assertStringContainsString('.build/bin/typo3', $reason);
        self::assertStringContainsString('vendor/bin/typo3', $reason);
    }

    #[Requirement('R-DIS-007')]
    #[Test]
    public function aStatedCommandIsUsedInsteadOfWorkingOneOut(): void
    {
        // Autodiscovery is a chain of guesses about a machine this server does
        // not control. When a link breaks, this is the whole repair.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' /some/where/typo3');
        $this->discover($root);

        $console = Typo3Cli::resolve();
        self::assertSame(Typo3Cli::VIA_OVERRIDE, $console['via']);
        self::assertSame([PHP_BINARY, '/some/where/typo3'], $console['command']);
    }

    #[Requirement('R-DIS-007')]
    #[Test]
    public function aStatedCommandThatIsNoProgramIsReported(): void
    {
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=/nowhere/at/all typo3');
        $this->discover($root);

        // Falling through to the discovered console would answer from
        // something other than what the caller named.
        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString(Typo3Cli::CONSOLE_VARIABLE, Typo3Cli::reason());
    }

    #[Test]
    public function aQuotedArgumentInAStatedCommandStaysOneArgument(): void
    {
        $this->discover($this->installation());
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' "-d memory_limit=512M" typo3');
        Typo3Cli::forget();

        self::assertSame(
            [PHP_BINARY, '-d memory_limit=512M', 'typo3'],
            Typo3Cli::resolve()['command']
        );
    }

    #[Test]
    public function aPhpBelowWhatTheInstallationPinsIsNotUsed(): void
    {
        // Composer pins the platform, so a lower interpreter aborts in
        // platform_check.php before it reaches TYPO3. Saying that beats a
        // fatal.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('requires PHP 99.0.0', Typo3Cli::reason());
    }

    #[Requirement('R-DIS-006')]
    #[Test]
    public function aDdevProjectThatIsNotRunningIsReported(): void
    {
        // An agent that asks about a label must not bring containers up as a
        // side effect. So the answer names the command the caller may choose to
        // run.
        $root = $this->installation(['config' => ['platform' => ['php' => '99.0.0']]]);
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('ddev', Typo3Cli::reason());
    }

    /**
     * `D-DIS-002`: `ddev exec` runs in the container's configured working
     * directory. So a console named relative to the project root is exit 127 in
     * a project whose `working_dir.web` is the docroot. The container mounts
     * the project at one place whatever that setting says, and that is the
     * place the invocation names.
     *
     * DDEV is a stub rather than a run. This holds the command that would run,
     * which is where the failure was — `D-COD-004`, `D-DIS-007`.
     */
    #[Decision('D-COD-004')]
    #[Decision('D-DIS-007')]
    #[Test]
    public function theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove(): void
    {
        $root = $this->installation([
            'config' => ['bin-dir' => '.build/bin', 'vendor-dir' => '.build/vendor'],
        ]);
        mkdir($root . '/.build/bin', 0o777, true);
        file_put_contents($root . '/.build/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatIsRunning());
        $this->discover($root);

        self::assertSame(
            ['ddev', 'exec', '--', '/var/www/html/.build/bin/typo3'],
            Typo3Cli::resolve()['command'],
        );
    }

    /**
     * `ddev exec` joins its arguments into a line and hands that to bash inside
     * the container. So an argument with a character bash acts on never reaches
     * the console. `typo3_label_lookup` builds one, and against every DDEV
     * installation it came back exit 2 and reported `answeredBy: "packages"` as
     * though the console were unreachable. The first record against a booted
     * TYPO3 found it, which is what `D-EVI-004` said an installation of this
     * repository's own would be for.
     *
     * What this holds is the quotes, which are this class's part. That the join
     * then happens is DDEV's behaviour and this does not reproduce it. The
     * measurement stands in `Typo3Cli::pastTheShell()` — `D-COD-004`.
     *
     * @param array<int, string> $arguments
     */
    #[Decision('D-COD-004')]
    #[Test]
    #[DataProvider('argumentsAShellWouldActOn')]
    public function everyArgumentReachesTheContainerAsTheShellLeavesIt(array $arguments): void
    {
        $root = $this->installation();
        mkdir($root . '/vendor/bin', 0o777, true);
        file_put_contents($root . '/vendor/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatIsRunning($commands));
        $this->discover($root);

        Typo3Cli::run($arguments);

        $expected = array_map(
            escapeshellarg(...),
            array_merge($arguments, ['--no-interaction', '--no-ansi']),
        );
        self::assertContains(
            'ddev exec -- /var/www/html/vendor/bin/typo3 ' . implode(' ', $expected),
            $commands,
        );
    }

    /**
     * @return array<string, array{0: array<int, string>}>
     */
    public static function argumentsAShellWouldActOn(): array
    {
        return [
            'a regex in parentheses, which bash reads as a subshell' => [
                ['language:domain:search', '--regex=/(save)/i'],
            ],
            'a semicolon, which bash reads as the end of the command' => [
                ['language:domain:search', '--regex=/a;b/'],
            ],
            'a dollar, which bash expands' => [
                ['configuration:show', 'BE/$installToolPassword'],
            ],
            'a space, which bash reads as two arguments' => [
                ['language:domain:search', '--regex=/two words/'],
            ],
            'nothing bash acts on, which is quoted the same way' => [
                ['site:list'],
            ],
        ];
    }

    #[Requirement('R-DIS-010')]
    #[Test]
    public function aStoppedProjectOnHostPhpIsReportedAsAHalfAnswer(): void
    {
        // The console answers, on an interpreter of this machine, because the
        // project meant for containers has none up. The answers then come from
        // outside the runtime that project declares, and "reachable" said none
        // of that.
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame('', Typo3Cli::reason(), 'it can be run, so nothing says it cannot');
        self::assertStringContainsString('database', Typo3Cli::caveat());

        // What the caveat may not do is name the answers it believes are lost.
        // It named three lookups until 2026-08-04, when all seven
        // installation-backed tools answered a stopped
        // `.environments/e-site-14.3` exactly as the started one did. Which
        // answer a stopped project costs is a property of the installation,
        // since its database driver settles it. So a sentence that lists
        // lookups is wrong on some installation.
        self::assertStringNotContainsString(
            'lookup',
            Typo3Cli::caveat(),
            'the caveat says what the boot cannot reach, never which lookups are lost',
        );

        $scope = Registry::call('typo3_server_scope', []);
        self::assertNotNull($scope->data['installation']['console']['caveat']);
        self::assertStringContainsString('Reachable is not the same as ready', $scope->text);
    }

    /**
     * The same **From** one layer down. There the console really did run on an
     * interpreter of this machine and only the database was out of reach. Here
     * it never starts at all, and "reachable via host PHP" was still what
     * `typo3_server_scope` said.
     *
     * The manifest is the base distribution's, which is what made this
     * reachable. `"platform": {}` and no `require.php`. The PHP bound of a
     * TYPO3 project lives in the packages it requires rather than in the file
     * that requires them. `composer install` had already written that bound
     * where it decides anything — the platform check the autoloader includes.
     */
    #[Requirement('R-DIS-010')]
    #[Test]
    public function thePhpBoundComesFromTheInstallWhereTheManifestStatesNone(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        $this->installedRequiring($root, 990000);
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('requires PHP 99.0.0', Typo3Cli::reason());
    }

    /**
     * Composer leaves the check out when nothing needs a version and deletes it
     * on an ignore of the platform requirements. So its absence has to mean "no
     * bound to read" rather than "no interpreter will do".
     */
    #[Requirement('R-DIS-010')]
    #[Test]
    public function anInstallWithNoPlatformCheckBoundsNothing(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        mkdir($root . '/vendor/composer', 0o777, true);
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame(Typo3Cli::VIA_PHP, Typo3Cli::resolve()['via']);
    }

    /**
     * `R-DIS-009` one step further in: the caller who reads "start the project"
     * is the one who starts it, and asks again in the same session.
     *
     * Measured in one server process before the bound came from the install.
     * Host PHP satisfied a bound nothing stated, so the resolution succeeded
     * through it and stayed, and `ddev start` changed nothing until a client
     * restart. With the bound at what the packages require, the stopped project
     * has no answer at all. An answer nothing gave is one nothing keeps.
     */
    #[Requirement('R-DIS-009')]
    #[Test]
    public function aStoppedProjectIsAskedAgainAfterItStarts(): void
    {
        $root = $this->installation(['config' => ['platform' => []]]);
        $this->console($root);
        $this->installedRequiring($root, 990000);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status));
        $this->discover($root);

        self::assertFalse(Typo3Cli::isAvailable());
        self::assertStringContainsString('ddev start', Typo3Cli::reason());

        $status = 'running';

        self::assertTrue(Typo3Cli::isAvailable(), 'the failure was retried rather than remembered');
        self::assertSame(Typo3Cli::VIA_DDEV, Typo3Cli::resolve()['via']);
        self::assertSame('', Typo3Cli::caveat());
    }

    /**
     * The same state one step weaker, and the one the three released
     * environments are in. An interpreter here does satisfy what the
     * installation pins. So the stopped project resolves through it, and the
     * answer carries the caveat that says to start the containers. Remembering
     * that remembers the weaker of two answers.
     *
     * Measured in one process against `.environments/e-site-13.4` on
     * 2026-08-04, where host PHP 8.3 satisfies the 8.2.0 it pins. The first
     * resolution came back through host PHP with the caveat, and `ddev start`
     * in the same process changed nothing. The memo answered every later call
     * until the process ended — `META-02`'s third **How it fails**.
     */
    #[Requirement('R-DIS-009')]
    #[Test]
    public function aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts(): void
    {
        $root = $this->installation();
        $this->console($root);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status));
        $this->discover($root);

        self::assertSame(Typo3Cli::VIA_PHP, Typo3Cli::resolve()['via']);
        self::assertStringContainsString('ddev start', Typo3Cli::caveat());

        $status = 'running';

        self::assertSame(
            Typo3Cli::VIA_DDEV,
            Typo3Cli::resolve()['via'],
            'the caveated resolution was asked again rather than remembered',
        );
        self::assertSame('', Typo3Cli::caveat());
    }

    /**
     * Naming the start is half of it. The resolution above makes the second
     * call return the stronger answer, and nothing asked the caller to make
     * that call. A session that reads "start it with ddev start" starts it and
     * works on from the answer it already holds. It never reaches the
     * resolution this state exists to reach. That is `META-02`'s third **How it
     * fails**, a session restart after the installation became reachable, alive
     * past the fix for it. What changed was what a second call returns rather
     * than whether one happens.
     */
    #[Test]
    public function theCaveatAsksForTheCallThatEndsTheState(): void
    {
        $root = $this->installation();
        $this->console($root);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status));
        $this->discover($root);

        $caveat = Typo3Cli::caveat();

        self::assertStringContainsString('ddev start', $caveat, 'the caveat names no way out of the state');
        self::assertStringContainsString(
            'Ask again',
            $caveat,
            'the caveat names the start and not the call that ends the state',
        );
    }

    /**
     * What that state costs the tool that reports it. Nothing keeps a
     * resolution with a caveat, so every read of the console state resolves
     * again and pays a `ddev describe -j` for it. That is 0.25s against
     * `.environments/e-site-13.4` with its project down on 2026-08-04. This
     * answer read it six times, a resolution and two caveat reads in each of
     * its halves, and took 2.648s there. It reads it into locals now and takes
     * 0.869s.
     *
     * Two rather than one because `reason()` and `caveat()` resolve on their
     * own. What limits a console is out of reach from outside `Typo3Cli`
     * without a question, and a question is a resolution.
     */
    #[Requirement('R-DIS-009')]
    #[Test]
    public function theScopeAnswerDescribesAStoppedProjectOncePerHalf(): void
    {
        $root = $this->installation();
        $this->console($root);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status, $commands));
        $this->discover($root);

        $scope = Registry::call('typo3_server_scope', []);

        self::assertNotNull($scope->data['installation']['console']['caveat'], 'the state this is about');
        self::assertSame(
            2,
            count(array_keys($commands, 'ddev describe -j', true)),
            'both halves of the answer are written from one reading of the console state',
        );
    }

    /**
     * The same cost one caller over, and every `unsupported` answer paid it.
     * The guard that asks whether a caveat exists and the sentence that names
     * it were two reads. So a failed lookup against a stopped project resolved
     * twice for one sentence. It stays behind the diagnosis, which is what
     * decides whether the answer wants the caveat at all. A resolution before
     * that would charge the failure that diagnosed itself for a sentence it
     * does not print.
     */
    #[Requirement('R-DIS-009')]
    #[Test]
    public function anUnsupportedAnswerReadsTheCaveatOnce(): void
    {
        $root = $this->installation();
        $this->console($root);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status, $commands));
        $this->discover($root);

        $answer = Unsupported::because('the console answered with something other than JSON');

        self::assertStringContainsString('What is known about this console', $answer->text, 'the state this is about');
        self::assertSame(
            1,
            count(array_keys($commands, 'ddev describe -j', true)),
            'the caveat this answer names is resolved once',
        );
    }

    /**
     * A diagnosis of its own is what the caveat would have stood in for. So the
     * console does not resolve a second time to fetch one nothing prints.
     */
    #[Test]
    public function aFailureThatDiagnosesItselfDoesNotAskWhatLimitsTheConsole(): void
    {
        $root = $this->installation();
        $this->console($root);
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        Typo3Cli::useRunner($this->ddevThatStartsAfterTheFirstLook($status, $commands));
        $this->discover($root);

        $answer = Unsupported::because("Table 'db.pages' doesn't exist");

        self::assertStringContainsString('no TYPO3 schema yet', $answer->text);
        self::assertSame(
            0,
            count(array_keys($commands, 'ddev describe -j', true)),
            'a diagnosis of its own asked nothing about the console',
        );
    }

    #[Requirement('R-DIS-010')]
    #[Test]
    public function aConsoleAlreadyInsideDdevIsReadyThroughItsDirectPhp(): void
    {
        $root = $this->installation();
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        mkdir($root . '/.ddev');
        file_put_contents($root . '/.ddev/config.yaml', "name: fixture\ntype: typo3\n");
        putenv('IS_DDEV_PROJECT=true');
        $this->discover($root);

        self::assertTrue(Typo3Cli::isAvailable());
        self::assertSame(Typo3Cli::VIA_PHP, Typo3Cli::resolve()['via']);
        self::assertSame('', Typo3Cli::caveat());

        $scope = Registry::call('typo3_server_scope', []);
        self::assertNull($scope->data['installation']['console']['caveat']);
        self::assertStringNotContainsString('Reachable is not the same as ready', $scope->text);
    }

    #[Test]
    public function runningWithoutAConsoleFailsWithTheReason(): void
    {
        $this->discover($this->installation());

        $result = Typo3Cli::run(['language:domain:list']);

        self::assertFalse($result['ok']);
        self::assertSame(Typo3Cli::reason(), $result['error']);
    }

    /** @param array<string, mixed> $manifest */
    private function installation(array $manifest = []): string
    {
        $root = $this->removeAfterwards(sys_get_temp_dir() . '/typo3-dev-companion-cli-' . bin2hex(random_bytes(6)));
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', json_encode(
            $manifest + ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));

        return $root;
    }

    private function discover(string $root): void
    {
        Instance::discoverFrom($root);
        Typo3Cli::forget();
    }

    /** A console at the default path, which several cases need and none is about. */
    private function console(string $root): void
    {
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
    }

    /**
     * A DDEV whose project comes up between two calls, so what one call
     * remembered is what the next one answers with.
     *
     * `locate` answers for `ddev` alone. The interpreter search asks it for
     * every `php99.x` it might use. A stub that says yes to all of them would
     * put the describe payload where a PHP version belongs.
     *
     * @param array<int, string>|null $commands filled with every command the code under test ran
     *
     * @param-out string $status
     * @param-out array<int, string> $commands
     */
    private function ddevThatStartsAfterTheFirstLook(?string &$status = null, ?array &$commands = null): CommandRunner&Stub
    {
        $status = 'stopped';
        $commands = [];
        $ddev = self::createStub(CommandRunner::class);
        $ddev->method('locate')->willReturnCallback(
            static fn(string $name): ?string => $name === 'ddev' ? '/usr/local/bin/ddev' : null
        );
        $ddev->method('run')->willReturnCallback(
            static function (array $command) use (&$status, &$commands): array {
                $commands[] = implode(' ', $command);
                $output = $command === ['ddev', 'describe', '-j']
                    ? sprintf('{"raw": {"status": "%s", "php_version": "8.3"}}', $status)
                    : PHP_VERSION;

                return ['ok' => true, 'exitCode' => 0, 'output' => $output, 'error' => ''];
            },
        );

        return $ddev;
    }

    /**
     * A DDEV that says its project is up, in place of the real one.
     *
     * Two things are stubs and both are the machine: that a `ddev` exists at
     * all, and what `ddev describe -j` answers. Neither stands on the `PATH`
     * any more. A test that writes an executable into a temporary directory
     * depends on a writable directory, on `chmod`, and on a `/tmp` nobody
     * mounted `noexec`. None of which is what it tests.
     */
    /**
     * @param array<int, string>|null $commands filled with every command the code under test ran
     *
     * @param-out array<int, string> $commands
     */
    private function ddevThatIsRunning(?array &$commands = null): CommandRunner&Stub
    {
        $commands = [];
        $ddev = self::createStub(CommandRunner::class);
        $ddev->method('locate')->willReturn('/usr/local/bin/ddev');
        $ddev->method('run')->willReturnCallback(
            static function (array $command) use (&$commands): array {
                $commands[] = implode(' ', $command);

                // The one call this class makes of its own accord is `describe
                // -j`, and everything else here is the console. Both get the
                // description, because what the cases read is the command
                // rather than what came back from it.
                return [
                    'ok' => true,
                    'exitCode' => 0,
                    'output' => '{"raw": {"status": "running", "php_version": "8.3"}}',
                    'error' => '',
                ];
            },
        );

        return $ddev;
    }
}
