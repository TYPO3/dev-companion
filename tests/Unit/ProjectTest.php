<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\ProjectDescribe;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Fixture;

/**
 * What the repository around the installation consists of.
 *
 * A recommendation is worth as much as its fit: a check that this project does
 * not declare does not exist here, whatever the core does with the same name.
 */
final class ProjectTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetTheInstance(): void
    {
        // The two the project answer reads to say what runs it. Left set, they
        // would decide the environment of every test after this one.
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        putenv('IS_DDEV_PROJECT');
        Instance::discoverFrom(null);
        Typo3Cli::forget();
        Typo3Runtime::forget();
    }

    #[Requirement('R-PRJ-001')]
    #[Test]
    public function theProjectIsDescribedFromItsFilesAlone(): void
    {
        $root = $this->composerProject('vendor', '13.4.33');
        $this->manifest($root, [
            'require' => ['php' => '^8.4', 'typo3/cms-core' => '^13.4'],
            'scripts' => ['t3g:cgl' => 'php-cs-fixer fix', 't3g:phpstan' => 'phpstan analyse'],
        ]);
        $this->site($root, 'events-site', [
            'base' => 'https://events.example/',
            'rootPageId' => 1,
            'dependencies' => ['acme/events-set'],
            'languages' => [['title' => 'Deutsch', 'languageId' => 0]],
        ]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('13.4.33', $project['typo3Version']);
        self::assertSame('^8.4', $project['phpConstraint']);
        self::assertSame('^13.4', $project['coreConstraint']);

        // The system extension is TYPO3's; the sitepackage is what this
        // repository is working on.
        self::assertSame(
            [[
                'key' => 'my_sitepackage',
                'path' => 'packages/my_sitepackage',
                'origin' => Project::ORIGIN_PROJECT,
                // Read for this one because it is the repository's own; a
                // dependency's files are its maintainer's.
                'deprecatedFiles' => [],
            ]],
            $project['extensions'],
        );

        self::assertSame('events-site', $project['sites'][0]['identifier']);
        self::assertSame(['acme/events-set'], $project['sites'][0]['sets']);
        self::assertSame(['Deutsch'], $project['sites'][0]['languages']);

        self::assertSame(
            ['composer t3g:cgl', 'composer t3g:phpstan'],
            array_column($project['commands'], 'command'),
        );
    }

    #[Decision('D-ANS-102')]
    #[Test]
    public function theAnswerNamesThePackagesTheInstallAndTheLockDisagreeOn(): void
    {
        // A review of an impexp patch reset onto origin/main, which brought a
        // changed composer.lock while vendor/symfony/yaml stayed a month old.
        // The unit run then failed three times in SiteWriterTest, and four
        // calls and two full suite runs went into "not my patch, stale vendor"
        // (feedback/2026-08-24-110908).
        $root = $this->composerProject('vendor', '13.4.33');
        $this->lock($root, [
            ['name' => 'typo3/cms-core', 'version' => '13.4.33'],
            ['name' => 'symfony/console', 'version' => '7.3.1'],
            ['name' => 'symfony/yaml', 'version' => '7.3.1'],
        ]);
        $this->installed($root, [['name' => 'symfony/yaml', 'version' => '7.2.0']]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertTrue($project['installed'], 'the boolean stays what it was: the packages are there');
        self::assertSame([
            'state' => Instance::LOCK_DIFFERS,
            'packages' => [
                ['package' => 'symfony/console', 'locked' => '7.3.1', 'installed' => null],
                ['package' => 'symfony/yaml', 'locked' => '7.3.1', 'installed' => '7.2.0'],
            ],
        ], $project['installedAgainstLock']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('symfony/yaml — locked 7.3.1, installed 7.2.0', $text);
        self::assertStringContainsString('symfony/console — locked 7.3.1, not installed', $text);
        self::assertStringContainsString('"composer install"', $text, 'the sentence names no command to fix it');
    }

    /**
     * @param array<int, array<string, string>>|null $locked what composer.lock names beside the core
     * @param array<int, array<string, string>> $installed what is installed beside it
     * @param array<int, array{package: string, locked: ?string, installed: ?string}> $packages
     */
    #[DataProvider('installsAgainstTheirLock')]
    #[Decision('D-ANS-102')]
    #[Test]
    public function theInstallIsHeldAgainstTheLockPackageByPackage(
        ?array $locked,
        array $installed,
        bool $tookDevPackages,
        string $state,
        array $packages,
    ): void {
        $root = $this->composerProject('vendor', '13.4.33');
        if ($locked !== null) {
            $this->lock($root, [['name' => 'typo3/cms-core', 'version' => '13.4.33'], ...$locked], [
                ['name' => 'phpunit/phpunit', 'version' => '11.5.0'],
            ]);
        }
        $this->installed($root, $installed, $tookDevPackages);
        Instance::discoverFrom($root);

        self::assertSame(
            ['state' => $state, 'packages' => $packages],
            Project::describe()['installedAgainstLock'],
        );
    }

    /** @return iterable<string, array{0: array<int, array<string, string>>|null, 1: array<int, array<string, string>>, 2: bool, 3: string, 4: array<int, array{package: string, locked: ?string, installed: ?string}>}> */
    public static function installsAgainstTheirLock(): iterable
    {
        yield 'every locked package installed at the version it names' => [
            [['name' => 'symfony/yaml', 'version' => '7.3.1']],
            [['name' => 'symfony/yaml', 'version' => '7.3.1']],
            false,
            Instance::LOCK_MATCHES,
            [],
        ];
        // What --no-dev leaves behind: every dev package locked and none of
        // them installed, which is a deployment rather than a drift.
        yield 'a dev package the install was told to leave out' => [
            [],
            [],
            false,
            Instance::LOCK_MATCHES,
            [],
        ];
        yield 'a dev package missing from an install that took them' => [
            [],
            [],
            true,
            Instance::LOCK_DIFFERS,
            [['package' => 'phpunit/phpunit', 'locked' => '11.5.0', 'installed' => null]],
        ];
        yield 'a package installed that the lock names nowhere' => [
            [],
            [['name' => 'symfony/yaml', 'version' => '7.3.1']],
            false,
            Instance::LOCK_DIFFERS,
            [['package' => 'symfony/yaml', 'locked' => null, 'installed' => '7.3.1']],
        ];
        yield 'no lock stating which versions this project fixed' => [
            null,
            [],
            false,
            Instance::LOCK_ABSENT,
            [],
        ];
    }

    #[Decision('D-ANS-102')]
    #[Test]
    public function aCheckoutWhoseLockedPackagesAreNotOnDiskIsToldWhatInstallsThem(): void
    {
        // The core checkout is the one where nothing below vendor/ is not the
        // same thing as nothing installed: its system extensions are tracked
        // files, so `installed` is true in a clone that has never run an
        // install — and the suite run fails for the vendor rather than for the
        // patch either way.
        $root = $this->coreCheckout('15.0.0-dev');
        $this->lock($root, [['name' => 'symfony/yaml', 'version' => '7.3.1']]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertTrue($project['installed']);
        self::assertSame(
            ['state' => Instance::LOCK_NOT_INSTALLED, 'packages' => []],
            $project['installedAgainstLock'],
        );
        self::assertStringContainsString(
            '"CI=true ./Build/Scripts/runTests.sh -s composerInstall"',
            Registry::call('typo3_project_describe', [])->text,
            'the core installs its dependencies through the script its suites are run by',
        );
    }

    /**
     * What composer.lock states, in the two sections Composer writes it in.
     *
     * @param array<int, array<string, string>> $packages
     * @param array<int, array<string, string>> $development
     */
    private function lock(string $root, array $packages, array $development = []): void
    {
        $this->declare($root . '/composer.lock', json_encode(
            ['packages' => $packages, 'packages-dev' => $development],
            JSON_THROW_ON_ERROR,
        ));
    }

    /**
     * What Composer wrote about the install, the core package included so the
     * root is still one this server locates.
     *
     * @param array<int, array<string, string>> $packages
     */
    private function installed(string $root, array $packages, bool $tookDevPackages = false): void
    {
        $this->declare($root . '/vendor/composer/installed.json', json_encode([
            'packages' => [[
                'name' => 'typo3/cms-core',
                'version' => '13.4.33',
                'type' => 'typo3-cms-framework',
                'install-path' => '../typo3/cms-core',
                'extra' => ['typo3/cms' => ['extension-key' => 'core']],
            ], ...$packages],
            'dev' => $tookDevPackages,
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * The one call every task opens with says which guides exist.
     *
     * Four sessions in one week finished without learning they do: one client
     * rendered no resource listing and never called `typo3_server_scope`, which
     * names them, because the task looked legible without orientation.
     * `D-ANS-061` decided the lever is the tool a session does call, and this is
     * that argument one step earlier — the instructions open every task with
     * `typo3_project_describe`, which was the one call that session made.
     */
    #[Decision('D-GUI-012')]
    #[Test]
    public function theCallEveryTaskOpensWithNamesTheGuidesThereAre(): void
    {
        Instance::discoverFrom($this->composerProject('vendor', '14.3.5'));

        $answer = Registry::call('typo3_project_describe', [])->data;

        self::assertSame(
            array_column(Documents::documents(), 'id'),
            array_column($answer['guides'] ?? [], 'id'),
            'the inventory is not the documents there are',
        );
        // Last in the answer. What this tool is called for is the installation,
        // and an inventory above those facts has traded one discovery problem
        // for another.
        self::assertSame('guides', array_key_last(array_diff_key($answer, ['answeredBy' => null])));

        // And every entry says what the caller has to be doing for it to be the
        // page to read, in the page's own words. An id and a title were what
        // six sessions read from four surfaces before opening none of them, and
        // three of those read this array — `D-GUI-012`.
        self::assertSame(
            array_column(Documents::documents(), 'whenToUse', 'id'),
            array_column($answer['guides'] ?? [], 'when', 'id'),
        );
        // And which call takes the id, because nothing joins the three names:
        // the field is `guides`, the argument is a `documentId`, and the tool
        // is neither.
        self::assertSame(
            ['typo3_rule_lookup'],
            array_values(array_unique(array_column($answer['guides'] ?? [], 'tool'))),
        );
        $text = Registry::call('typo3_project_describe', [])->text;
        foreach ($answer['guides'] ?? [] as $guide) {
            self::assertStringContainsString($guide['when'], $text, $guide['id']);
        }
    }

    #[Decision('D-ANS-013')]
    #[Requirement('R-PRJ-008')]
    #[Decision('D-KNW-055')]
    #[Decision('D-ANS-126')]
    #[Test]
    public function theAnswerSaysWhatRunsTheProject(): void
    {
        // A conformance audit reported "PHP version mismatch blocks all tests"
        // from a host at 8.3.23 against a declared ^8.4, while the suite it
        // meant runs in a container at 8.4 and was never blocked
        // (feedback/2026-07-31-193611). Two machines, one number in the answer
        // — `D-KNW-055`.
        $root = $this->composerProject('vendor', '14.3.5');
        $this->manifest($root, [
            'require' => ['php' => '^8.4'],
            'scripts' => ['test:unit' => 'phpunit -c Build/phpunit/UnitTests.xml'],
        ]);
        $this->declare($root . '/.ddev/config.yaml', "name: site-new\ntype: typo3\nphp_version: \"8.1\"\n");
        // DDEV merges config.yaml first and every config.*.yaml after it in
        // filename order, so the last statement holds — measured against DDEV
        // v1.25.1 on 2026-08-02. Reading the base file alone would report 8.1
        // in every project that keeps its local settings where DDEV's own
        // gitignore puts them. Unquoted, because quoting is optional there and
        // (string) on the float YAML makes of 8.0 is "8".
        $this->declare($root . '/.ddev/config.local.yaml', "php_version: 8.4\n");
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('^8.4', $project['phpConstraint'], 'the declared constraint stays what it was');
        self::assertSame([
            'via' => Typo3Cli::VIA_DDEV,
            'php' => '8.4',
            'node' => null,
            'source' => '.ddev/config.local.yaml',
            'project' => 'site-new',
            'hostnames' => ['site-new.ddev.site'],
            'entered' => false,
            'hooks' => [],
            'providers' => [],
        ], $project['environment']);

        // And the same thing where the payload is what is read: the sentence
        // above the list is prose, and a caller acting on the field ran the
        // declared command into Composer's platform check (`D-ANS-126`).
        self::assertSame(
            ['ddev composer test:unit'],
            array_column($project['commands'], 'invocation'),
        );

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('PHP ^8.4 declared and 8.4 in DDEV', $text);
        // The command list is what a task is sent to run, and nothing beside
        // it said the shell it has is not where these run.
        self::assertStringContainsString('not in the shell you have', $text);
        self::assertStringContainsString('ddev composer', $text);

        // Inside the container the shell is the environment, so a `ddev` in
        // front of it would name a binary that is not there.
        putenv('IS_DDEV_PROJECT=true');
        self::assertSame(
            ['composer test:unit'],
            array_column(Project::describe()['commands'], 'invocation'),
        );
    }

    #[Test]
    public function theDdevProjectNamesTheSiteItsFilesDeclare(): void
    {
        // A session that had this answer and nothing else spent four shell
        // round trips finding the name and the hostname, and one wrong attempt
        // in between (feedback/2026-08-10-101723). All of it is in the files.
        //
        // The rules are DDEV's own, from `ddev config --help` and the comments
        // its config.yaml carries: --project-name defaults to the last part of
        // the directory name, --project-tld to ddev.site, an
        // additional_hostnames entry gets that same tld, and an
        // additional_fqdns entry is served as written.
        $root = $this->composerProject();
        $this->declare($root . '/.ddev/config.yaml', <<<'YAML'
            type: typo3
            project_tld: ddev.local
            additional_hostnames:
                - editors
            additional_fqdns:
                - example.com
            YAML);
        Instance::discoverFrom($root);

        $environment = Project::describe()['environment'];

        self::assertSame(basename($root), $environment['project'], 'the directory is the name DDEV falls back to');
        self::assertSame(
            [basename($root) . '.ddev.local', 'editors.ddev.local', 'example.com'],
            $environment['hostnames'],
        );

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('ddev describe -j', $text, 'nothing says where the running ports come from');
    }

    #[Decision('D-KNW-055')]
    #[Test]
    public function theFloorTheInstalledCoreDeclaresIsBesideTheProjectsOwn(): void
    {
        // A session asked to declare a 14.3 extension's own `php` constraint
        // had this answer in hand and neither of its two PHP numbers was the
        // one: the project declared none, and the DDEV container in front of it
        // ran 8.4. Written from that, the extension would have claimed ^8.4
        // against a core requiring ^8.2 — two minors narrower, with every check
        // still green — so it was read out of the vendor tree by hand instead
        // (feedback/2026-08-04-055638, D-KNW-055). The major does not carry it:
        // 12.4 requires ^8.1, 13.4 and 14.3 both ^8.2, main ^8.5, read in
        // .checkouts/ and in the installed package below .environments/ alike
        // on 2026-08-04.
        $root = $this->composerProject('vendor', '14.3.5');
        $this->declare($root . '/vendor/typo3/cms-core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'require' => ['php' => '^8.2'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/.ddev/config.yaml', "name: ext-guidedtour\ntype: typo3\nphp_version: \"8.4\"\n");
        $this->manifest($root, ['require' => ['typo3/cms-core' => '^14.3']]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertNull($project['phpConstraint'], 'declaring none is what this project declares');
        self::assertSame('^8.2', $project['corePhpConstraint']);
        self::assertStringContainsString(
            'PHP unconstrained declared and 8.4 in DDEV, and the installed core requires ^8.2 — the lowest a '
                . 'package here may declare',
            Registry::call('typo3_project_describe', [])->text,
        );

        // The other shape: a project whose own floor is above the core's. Both
        // are stated and neither is judged against the other — a tool that
        // reported two declarations as disagreeing would be judging rather than
        // answering, which is D-ANS-011.
        $this->manifest($root, ['require' => ['php' => '^8.4', 'typo3/cms-core' => '^14.3']]);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('^8.4', $project['phpConstraint']);
        self::assertSame('^8.2', $project['corePhpConstraint']);

        // And where the installed core's manifest requires no PHP at all, the
        // answer is null rather than a floor derived from the major.
        $bare = $this->composerProject();
        Instance::discoverFrom($bare);

        self::assertNull(Project::describe()['corePhpConstraint']);
        self::assertStringNotContainsString(
            'the installed core requires',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    #[Decision('D-ANS-082')]
    #[Requirement('R-PRJ-010')]
    #[Test]
    public function theThreePhpNumbersAreRelated(): void
    {
        // The exact project feedback/2026-08-17-211157 was written in: ^8.3
        // declared, a core requiring ^8.2, a container at 8.4. Above the floor
        // it could have declared, below the interpreter every command was run
        // on, so no line of the package was ever executed on the version it
        // claims to support — and the answer that held all three numbers said
        // nothing about them relating (D-ANS-082).
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/vendor/typo3/cms-core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'require' => ['php' => '^8.2'],
        ], JSON_THROW_ON_ERROR));
        $this->manifest($root, ['require' => ['php' => '^8.3', 'typo3/cms-core' => '^14.3']]);
        $this->declare($root . '/.ddev/config.yaml', "name: site-demo\ntype: typo3\nphp_version: \"8.4\"\n");
        Instance::discoverFrom($root);

        self::assertSame([
            'floor' => '8.3',
            'coreFloor' => '8.2',
            'againstCore' => Project::ABOVE,
            'inEnvironment' => Project::ABOVE,
            'bound' => null,
            'environmentAgainstBound' => null,
        ], Project::describe()['phpRelation']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('This project promises 8.3.', $text);
        self::assertStringContainsString('asks for more than its dependency needs', $text);
        self::assertStringContainsString('nothing configured here ever executes', $text);
        // What it may not claim. Nothing here ran anything, so the line is what
        // the files say and never evidence that the floor works.
        self::assertStringContainsString('Nothing was executed on any of these versions', $text);
    }

    #[Decision('D-ANS-082')]
    #[Requirement('R-PRJ-010')]
    #[Test]
    public function aFloorTheEnvironmentRunsIsSaidToBeRun(): void
    {
        // Stated even where the three agree, for the reason ProjectDescribe
        // states the core's number where it repeats the project's own: a line
        // the answer drops when nothing is wrong cannot be told from one it
        // never computed (D-ANS-082).
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/vendor/typo3/cms-core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'require' => ['php' => '^8.2'],
        ], JSON_THROW_ON_ERROR));
        $this->manifest($root, ['require' => ['php' => '>=8.2']]);
        $this->declare($root . '/.ddev/config.yaml', "name: site-demo\ntype: typo3\nphp_version: \"8.2\"\n");
        Instance::discoverFrom($root);

        self::assertSame([
            'floor' => '8.2',
            'coreFloor' => '8.2',
            'againstCore' => Project::SAME,
            'inEnvironment' => Project::SAME,
            'bound' => null,
            'environmentAgainstBound' => null,
        ], Project::describe()['phpRelation']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('The installed core requires 8.2 as well, so the two agree.', $text);
        self::assertStringContainsString('which is that floor', $text);
    }

    #[Requirement('R-PRJ-010')]
    #[Test]
    public function aFloorTheCoreRefusesAndAnEnvironmentUnderItAreBothSaid(): void
    {
        // The two shapes the other way round, and the only one of the four that
        // is a defect rather than a choice: a project promising a PHP its own
        // installed core will not accept, in a container that runs it anyway.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/vendor/typo3/cms-core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'require' => ['php' => '^8.2'],
        ], JSON_THROW_ON_ERROR));
        $this->manifest($root, ['require' => ['php' => '^8.1']]);
        $this->declare($root . '/.ddev/config.yaml', "name: site-old\ntype: typo3\nphp_version: \"8.0\"\n");
        Instance::discoverFrom($root);

        self::assertSame([
            'floor' => '8.1',
            'coreFloor' => '8.2',
            'againstCore' => Project::BELOW,
            'inEnvironment' => Project::BELOW,
            'bound' => null,
            'environmentAgainstBound' => null,
        ], Project::describe()['phpRelation']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('that promise cannot be kept here', $text);
        self::assertStringContainsString('says it does not support', $text);
    }

    #[Requirement('R-PRJ-010')]
    #[Test]
    public function aProjectWithNoReadableFloorIsRelatedToNothing(): void
    {
        // Declaring no PHP is what phpConstraint already says, and a relation
        // built on a floor of 0 would be three numbers where there are two.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/vendor/typo3/cms-core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'require' => ['php' => '^8.2'],
        ], JSON_THROW_ON_ERROR));
        $this->manifest($root, ['require' => ['typo3/cms-core' => '^14.3']]);
        $this->declare($root . '/.ddev/config.yaml', "name: site-demo\ntype: typo3\nphp_version: \"8.4\"\n");
        Instance::discoverFrom($root);

        self::assertNull(Project::describe()['phpRelation']);
        self::assertStringNotContainsString(
            'as they stand to each other',
            Registry::call('typo3_project_describe', [])->text,
        );

        // A project with a floor and nothing else readable still relates what it
        // has: no core to compare against, and no environment stating a version.
        $bare = $this->composerProject();
        $this->manifest($bare, ['require' => ['php' => '^8.2']]);
        Instance::discoverFrom($bare);

        self::assertSame([
            'floor' => '8.2',
            'coreFloor' => null,
            'againstCore' => null,
            'inEnvironment' => null,
            'bound' => null,
            'environmentAgainstBound' => null,
        ], Project::describe()['phpRelation']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('no second floor to hold that against', $text);
        self::assertStringContainsString('No environment here states a PHP', $text);
    }

    #[Decision('D-ANS-086')]
    #[Requirement('R-PRJ-012')]
    #[Test]
    public function aDeclaredCommandSaysWhetherItStartsOnThePhpThatWouldRunIt(): void
    {
        // The shape feedback/2026-08-18-113412 reported: a `cgl:ci` marked a
        // check a review may run, aborting in the platform check before the
        // fixer started, because a package installed for development alone
        // requires a PHP the interpreter in front of it does not have. What
        // runs says about the sources; this says whether it gets that far
        // (D-ANS-086).
        $root = $this->composerProject('vendor', '14.3.6');
        $this->manifest($root, [
            'require' => ['php' => '^8.2'],
            'scripts' => ['cgl:ci' => 'php-cs-fixer fix --dry-run'],
        ]);
        $this->declare($root . '/.ddev/config.yaml', "name: sitepackage\ntype: typo3\nphp_version: \"8.3\"\n");
        $this->installedRequiring($root, 80400);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('8.4.0', $project['installedPhpBound']);
        self::assertSame('8.4', $project['phpRelation']['bound']);
        self::assertSame(Project::BELOW, $project['phpRelation']['environmentAgainstBound']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('The install itself is bounded at 8.4.0, over the 8.3', $text);
        self::assertStringContainsString('They do not start there', $text);
        self::assertStringContainsString('composer/platform_check.php', $text);
    }

    #[Requirement('R-PRJ-012')]
    #[Test]
    public function aBoundTheInterpreterClearsIsSaidToBeCleared(): void
    {
        // Stated for the reason every other number here is stated where nothing
        // is wrong: an answer that goes quiet when the commands start cannot be
        // told from one that never read the check.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->manifest($root, ['require' => ['php' => '^8.2'], 'scripts' => ['cgl:ci' => 'php-cs-fixer check']]);
        $this->declare($root . '/.ddev/config.yaml', "name: sitepackage\ntype: typo3\nphp_version: \"8.4\"\n");
        $this->installedRequiring($root, 80400);
        Instance::discoverFrom($root);

        self::assertSame(
            Project::SAME,
            Project::describe()['phpRelation']['environmentAgainstBound'],
        );

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('which the 8.4 the environment runs clears', $text);
        self::assertStringContainsString('nothing in that check stops them', $text);
    }

    #[Decision('D-ANS-086')]
    #[Requirement('R-PRJ-012')]
    #[Test]
    public function whereNothingConfiguresAnEnvironmentTheBoundIsStated(): void
    {
        // The commands run in the caller's shell, and this server does not read
        // that shell: it is not the process this answer is composed in, and a
        // version named from here would be the one failure the bound is
        // reported against (D-ANS-086). So the bound is stated and the one
        // command that settles it is named.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->manifest($root, ['require' => ['php' => '^8.2'], 'scripts' => ['cgl:ci' => 'php-cs-fixer check']]);
        $this->installedRequiring($root, 80400);
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame('8.4.0', $project['installedPhpBound']);
        self::assertNull($project['phpRelation']['environmentAgainstBound']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('"php -v" there is what says whether it clears the bound', $text);
        self::assertStringNotContainsString(PHP_VERSION, $text, 'no interpreter on this machine was read');
    }

    #[Requirement('R-PRJ-012')]
    #[Test]
    public function anInstallThatBoundsNothingIsSaidToBoundNothing(): void
    {
        // Composer leaves the file out where nothing requires a PHP version and
        // deletes it where platform-check is off, so absent has to read as no
        // bound rather than as none found.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->manifest($root, ['require' => ['php' => '^8.2'], 'scripts' => ['cgl:ci' => 'php-cs-fixer check']]);
        Instance::discoverFrom($root);

        self::assertNull(Project::describe()['installedPhpBound']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('Nothing here bounds the interpreter', $text);
        self::assertStringNotContainsString('They do not start', $text);
    }

    #[Decision('D-SCO-013')]
    #[Requirement('R-PRJ-013')]
    #[Test]
    public function theNodeThoseNpmCommandsRunOnIsStatedBesideThem(): void
    {
        // feedback/2026-08-18-113501: five sixths of a sitepackage maintenance
        // session were Node, npm and GitHub Actions work, and the defect was
        // the machine and CI running different releases of one Node major. The
        // composer half of the command list has carried its interpreter since
        // R-PRJ-008; the npm half beside it said nothing (D-SCO-013).
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/package.json', json_encode([
            'engines' => ['node' => '^24.0.0'],
            'scripts' => ['build' => 'grunt build'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/.nvmrc', "24\n");
        $this->declare($root . '/.github/workflows/ci.yml', <<<'YAML'
            name: ci
            jobs:
                build:
                    steps:
                        - uses: actions/checkout@v5
                        - uses: actions/setup-node@v4
                          with:
                            node-version: 24.19.0
                        - run: npm ci
                lint:
                    steps:
                        - uses: actions/setup-node@v5
                          with:
                            node-version: "24.19.0"
            YAML);
        $this->declare($root . '/.ddev/config.yaml', "name: sitepackage\nnodejs_version: \"20\"\n");
        Instance::discoverFrom($root);

        self::assertSame([
            'engines' => '^24.0.0',
            'enginesIn' => 'package.json',
            'nvmrc' => '24',
            'nvmrcIn' => '.nvmrc',
            'environment' => '20',
            // Two jobs setting the same version up are one fact about this
            // repository, and the second line saying it says nothing.
            'ci' => [[
                'workflow' => '.github/workflows/ci.yml',
                'from' => 'node-version',
                'states' => '24.19.0',
                'version' => '24.19.0',
            ]],
            'relation' => [
                'declared' => '24',
                'declaredBy' => '.nvmrc',
                // Only the segments both files spell are compared: a pin naming
                // a major and a workflow naming a patch level agree wherever
                // the major does.
                'nvmrcAgainstEngines' => Project::SAME,
                'inEnvironment' => Project::BELOW,
                'ci' => '24.19.0',
                'inCi' => Project::SAME,
            ],
        ], Project::describe()['node']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('This repository declares 24, in .nvmrc.', $text);
        self::assertStringContainsString('Its package.json admits ^24.0.0', $text);
        self::assertStringContainsString('Its workflows set up 24.19.0', $text);
        self::assertStringContainsString('The environment runs 20, below it.', $text);
        self::assertStringContainsString('- .github/workflows/ci.yml — node-version: 24.19.0', $text);
        // What it may not claim, for the reason the PHP relation may not claim
        // it: nothing here was run, and the interpreter the caller's own shell
        // has is in none of these files.
        self::assertStringContainsString('Nothing was run to find it out', $text);
    }

    #[Decision('D-SCO-013')]
    #[Requirement('R-PRJ-013')]
    #[Test]
    public function aNodeAWorkflowDecidesElsewhereIsStatedBack(): void
    {
        // The third Wrong if of D-SCO-013: a resolved wrong number carries this
        // server's authority, which is worse than the silence it replaced. A
        // matrix entry, a file and a step that states nothing are each handed
        // back as the workflow writes them — and two workflows naming different
        // versions are a disagreement no single relation states.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/package.json', json_encode([
            'engines' => ['node' => '>=20.9.0'],
            'scripts' => ['build' => 'vite build'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/.github/workflows/ci.yml', <<<'YAML'
            jobs:
                test:
                    strategy:
                        matrix:
                            node: [20, 22]
                    steps:
                        - uses: actions/setup-node@v4
                          with:
                            node-version: ${{ matrix.node }}
                docs:
                    steps:
                        - uses: actions/setup-node@v4
            YAML);
        $this->declare(
            $root . '/.github/workflows/lint.yml',
            "jobs:\n  lint:\n    steps:\n      - uses: actions/setup-node@v4\n        with:\n          node-version: 22\n",
        );
        $this->declare(
            $root . '/.github/workflows/publish.yml',
            "jobs:\n  npm:\n    steps:\n      - uses: actions/setup-node@v4\n        with:\n          node-version-file: .nvmrc\n",
        );
        $this->declare(
            $root . '/.github/workflows/release.yml',
            "jobs:\n  build:\n    steps:\n      - uses: actions/setup-node@v4\n        with:\n          node-version: 20.9.0\n",
        );
        Instance::discoverFrom($root);

        $node = Project::describe()['node'];

        self::assertSame([
            ['workflow' => '.github/workflows/ci.yml', 'from' => 'node-version', 'states' => '${{ matrix.node }}', 'version' => null],
            ['workflow' => '.github/workflows/ci.yml', 'from' => 'none', 'states' => '', 'version' => null],
            ['workflow' => '.github/workflows/lint.yml', 'from' => 'node-version', 'states' => '22', 'version' => '22'],
            ['workflow' => '.github/workflows/publish.yml', 'from' => 'node-version-file', 'states' => '.nvmrc', 'version' => null],
            ['workflow' => '.github/workflows/release.yml', 'from' => 'node-version', 'states' => '20.9.0', 'version' => '20.9.0'],
        ], $node['ci']);
        self::assertSame('20.9', $node['relation']['declared'], 'the range the manifest admits has a lowest version');
        self::assertSame('package.json', $node['relation']['declaredBy']);
        self::assertNull($node['relation']['ci'], 'two workflows naming different versions name no version');
        self::assertNull($node['relation']['inCi']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('What its workflows set up is below, as each of them states it.', $text);
        self::assertStringContainsString('node-version: ${{ matrix.node }}, which names no version outright', $text);
        self::assertStringContainsString('node-version-file: .nvmrc, so the version is whatever that file says', $text);
        self::assertStringContainsString('without stating a version, so the runner image\'s own', $text);
    }

    #[Requirement('R-PRJ-013')]
    #[Test]
    public function aRepositoryThatDeclaresNoNodeIsSaidToDeclareNone(): void
    {
        // The silence is the finding: npm commands are in the list above, and
        // what runs them is whatever the shell has. An .nvmrc naming an alias
        // is the second state and not the same one — there is a file to open,
        // and what it resolves to is nvm's download list rather than anything
        // here.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/package.json', json_encode([
            'scripts' => ['build' => 'grunt build'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $node = Project::describe()['node'];
        self::assertNull($node['engines']);
        self::assertNull($node['enginesIn']);
        self::assertNull($node['relation']);
        self::assertStringContainsString(
            'Nothing here declares which Node those npm commands run on: no package.json here states an engines.node',
            Registry::call('typo3_project_describe', [])->text,
        );

        $this->declare($root . '/.nvmrc', "lts/iron\n");
        Instance::discoverFrom($root);

        self::assertSame('lts/iron', Project::describe()['node']['nvmrc']);
        self::assertStringContainsString(
            'its .nvmrc says lts/iron',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    #[Requirement('R-PRJ-013')]
    #[Test]
    public function aRepositoryWithNoNpmSurfaceIsToldNothingAboutNode(): void
    {
        // The one silence that says something: no package.json, no workflow, no
        // environment stating one. There are no npm commands to run, so a
        // paragraph about their interpreter is a sentence every PHP-only
        // project pays for and nobody reads.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->manifest($root, ['require' => ['php' => '^8.2'], 'scripts' => ['cgl' => 'php-cs-fixer fix']]);
        Instance::discoverFrom($root);

        self::assertNull(Project::describe()['node']);
        self::assertStringNotContainsString('Node', Registry::call('typo3_project_describe', [])->text);
    }

    #[Requirement('R-PRJ-013')]
    #[Decision('D-SCO-014')]
    #[Test]
    public function theManifestBelowBuildIsReadWhereTheRepositoryKeepsItThere(): void
    {
        // The TYPO3 layout keeps the frontend build one directory down, and
        // the core has no root package.json at all on any covered branch: read
        // from the root alone, such a repository had no npm command in the
        // list and no Node under it, and nothing said which of the two it was
        // (D-SCO-014).
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/Build/package.json', json_encode([
            'engines' => ['node' => '>=24.14.0 <25.0.0'],
            'scripts' => ['build' => 'grunt build'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/Build/.nvmrc', "v24.14\n");
        Instance::discoverFrom($root);

        $project = Project::describe();

        // npm run reads the manifest of the directory it is called in, so the
        // command carries the prefix that points it at this one.
        self::assertSame(
            [['command' => 'npm --prefix Build run build', 'source' => 'Build/package.json',
                'invocation' => 'npm --prefix Build run build',
                'declares' => 'grunt build', 'runs' => Project::RUNS_AS_CHANGE]],
            $project['commands'],
        );
        self::assertSame('>=24.14.0 <25.0.0', $project['node']['engines']);
        self::assertSame('Build/package.json', $project['node']['enginesIn']);
        self::assertSame('v24.14', $project['node']['nvmrc']);
        self::assertSame('Build/.nvmrc', $project['node']['nvmrcIn']);
        self::assertSame('Build/.nvmrc', $project['node']['relation']['declaredBy']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('- npm --prefix Build run build (Build/package.json)', $text);
        self::assertStringContainsString('This repository declares 24.14, in Build/.nvmrc.', $text);
        self::assertStringContainsString('Its Build/package.json admits >=24.14.0 <25.0.0', $text);
    }

    #[Decision('D-SCO-014')]
    #[Test]
    public function twoManifestsDeclaringOneNameAreTwoCommandsThatCanBeToldApart(): void
    {
        // What made the second location worth deciding rather than assuming: a
        // repository declaring a build in both manifests would have had one
        // name for two commands. The prefix is what settles which is meant, and
        // it is what the core's own runTests.sh puts in front of its playwright
        // scripts — `D-SCO-014`.
        $root = $this->composerProject('vendor', '14.3.6');
        $this->declare($root . '/package.json', json_encode([
            'scripts' => ['build' => 'vite build'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/Build/package.json', json_encode([
            'engines' => ['node' => '^22.0.0'],
            'scripts' => ['build' => 'grunt build'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertSame(
            ['npm run build', 'npm --prefix Build run build'],
            array_column($project['commands'], 'command'),
        );
        // The root manifest states no engines, so the one below it answers —
        // and the answer names the file rather than leaving the caller to
        // assume the root's.
        self::assertSame('Build/package.json', $project['node']['enginesIn']);
        self::assertSame('Build/package.json', $project['node']['relation']['declaredBy']);
    }

    #[Requirement('R-PRJ-008')]
    #[Test]
    public function aVersionTheEnvironmentDoesNotStateIsNotAVersionItDoesNotHave(): void
    {
        // DDEV takes php_version as major.minor and falls back to the default
        // of the DDEV that is installed — a number these files do not carry and
        // one release changes from the next. Guessing it here would be the one
        // failure this whole field exists against: a stated version that is not
        // the one running.
        $root = $this->composerProject();
        $this->declare($root . '/.ddev/config.yaml', "name: site-new\ntype: typo3\n");
        $this->manifest($root, ['scripts' => ['ci' => 'phpunit']]);
        Instance::discoverFrom($root);

        self::assertNull(Project::describe()['environment']['php']);
        self::assertStringContainsString(
            'states no php_version',
            Registry::call('typo3_project_describe', [])->text,
        );

        // Unquoted it is a YAML float, and casting that to a string would
        // answer "8" — a PHP version DDEV does not ship.
        $other = $this->composerProject();
        $this->declare($other . '/.ddev/config.yaml', "name: old\nphp_version: 8.0\n");
        Instance::discoverFrom($other);

        self::assertSame('8.0', Project::describe()['environment']['php']);
    }

    #[Decision('D-ANS-044')]
    #[Requirement('R-PRJ-009')]
    #[Test]
    public function theAnswerStatesWhatTheEnvironmentRunsWithoutBeingAsked(): void
    {
        // A boot of a demo site read its schema update, its extension setup and
        // its backend user out of .ddev/config.yaml by hand, beside an answer
        // that had opened the same file for one PHP version and reported one
        // composer script — feedback/2026-08-03-154501, R-PRJ-009. Every task
        // type DDEV v1.25.1 supports is here, because which of them a hook uses
        // decides where its command runs.
        $root = $this->composerProject();
        $this->declare($root . '/.ddev/config.yaml', <<<'YAML'
            name: site-new
            type: typo3
            php_version: "8.3"
            hooks:
                post-start:
                    - exec: composer install
                    - exec:
                      exec_raw: [ls, -lR, /var/www/html]
                post-import-db:
                    - exec: |
                        bin/typo3 extension:setup
                        bin/typo3 cache:flush
                    - exec: mysql -e "SELECT 1"
                      service: db
                post-pull:
                    - exec-host: ddev restart
                    - composer: install --no-dev
            YAML);
        Instance::discoverFrom($root);

        self::assertSame(
            [
                ['stage' => 'post-start', 'command' => 'composer install', 'service' => 'web'],
                // The argument list form, which is a command with no shell
                // around it rather than a task that states none.
                ['stage' => 'post-start', 'command' => 'ls -lR /var/www/html', 'service' => 'web'],
                // A block is several lines in one shell and nothing stops it at
                // the first failure, so ";" is the join and "&&" would not be.
                ['stage' => 'post-import-db', 'command' => 'bin/typo3 extension:setup; bin/typo3 cache:flush', 'service' => 'web'],
                ['stage' => 'post-import-db', 'command' => 'mysql -e "SELECT 1"', 'service' => 'db'],
                ['stage' => 'post-pull', 'command' => 'ddev restart', 'service' => null],
                ['stage' => 'post-pull', 'command' => 'composer install --no-dev', 'service' => 'web'],
            ],
            Project::describe()['environment']['hooks'],
        );

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('runs without being asked', $text);
        self::assertStringContainsString('- post-start, in the web container: composer install', $text);
        // The two that are not the web container, and the pair that would read
        // as the same machine if only the command were carried.
        self::assertStringContainsString('- post-import-db, in the db container: mysql -e "SELECT 1"', $text);
        self::assertStringContainsString('- post-pull, on the host: ddev restart', $text);

        // A DDEV project that declares none says so. An answer that names no
        // hook reads as "there is none" whether this looked or not, which is
        // the silence the environment field already exists against.
        $bare = $this->composerProject();
        $this->declare($bare . '/.ddev/config.yaml', "name: bare\ntype: typo3\n");
        Instance::discoverFrom($bare);

        self::assertSame([], Project::describe()['environment']['hooks']);
        self::assertStringContainsString(
            'declares no hooks',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    #[Requirement('R-PRJ-009')]
    #[Test]
    public function aHookAConfigBesideTheBaseOneTakesAwayIsNotStillReported(): void
    {
        // DDEV merges hooks per stage across config.yaml and every
        // config.*.yaml beside it, and override_config: true replaces instead —
        // per stage, and only for the stages the file carrying it names.
        // Measured against DDEV v1.25.1 through `ddev debug configyaml` on
        // 2026-08-03. Reading the base file alone would state a lifecycle the
        // container does not run.
        $root = $this->composerProject();
        $this->declare($root . '/.ddev/config.yaml', <<<'YAML'
            name: site-new
            hooks:
                post-start:
                    - exec: composer install
                post-import-db:
                    - exec: bin/typo3 database:updateschema
            YAML);
        $this->declare($root . '/.ddev/config.aaa.yaml', "hooks:\n    post-start:\n        - exec: npm ci\n");
        Instance::discoverFrom($root);

        self::assertSame(
            ['composer install', 'npm ci', 'bin/typo3 database:updateschema'],
            array_column(Project::describe()['environment']['hooks'], 'command'),
            'a merged stage concatenates in filename order, which is the order the container runs it in',
        );

        // The same project with the stage taken away again, which is what a
        // developer's own config.local.yaml is for and the one shape a merge
        // cannot express.
        $this->declare($root . '/.ddev/config.zzz.yaml', "override_config: true\nhooks:\n    post-start: []\n");
        Instance::discoverFrom($root);

        self::assertSame(
            [['stage' => 'post-import-db', 'command' => 'bin/typo3 database:updateschema', 'service' => 'web']],
            Project::describe()['environment']['hooks'],
            'override_config replaces the stages its own file names and leaves the others alone',
        );
    }

    #[Requirement('R-PRJ-009')]
    #[Test]
    public function aPullRecipeDdevWroteIsNotOneThisRepositoryDecidedOn(): void
    {
        // The other half of the demo site's import: pre-pull fetches the zip,
        // `ddev pull dump` copies the dump out of it, post-pull clears up. DDEV
        // writes nine recipes of its own into every project, so listing them
        // all would report ten integrations in a project that has one — and
        // #ddev-generated is DDEV's own statement of which those are, the
        // signature it replaces the file by while the marker is there.
        $root = $this->composerProject();
        $this->declare($root . '/.ddev/config.yaml', "name: site-new\ntype: typo3\n");
        $this->declare(
            $root . '/.ddev/providers/dump.yaml',
            "db_pull_command:\n  command: cp dump.sql.gz .ddev/.downloads/db.sql.gz\nfiles_pull_command:\n  command: 'true'\n",
        );
        $this->declare(
            $root . '/.ddev/providers/live.yaml',
            "db_pull_command:\n  command: 'true'\ndb_push_command:\n  command: 'true'\n",
        );
        $this->declare($root . '/.ddev/providers/acquia.yaml', "#ddev-generated\ndb_pull_command:\n  command: 'true'\n");
        // Neither of these is a recipe: `ddev pull` offered the .yaml files in
        // that directory and neither the .yml nor the .yaml.example beside them.
        $this->declare($root . '/.ddev/providers/rsync.yaml.example', "db_pull_command:\n  command: 'true'\n");
        $this->declare($root . '/.ddev/providers/other.yml', "db_pull_command:\n  command: 'true'\n");
        Instance::discoverFrom($root);

        self::assertSame(
            [
                ['name' => 'dump', 'source' => '.ddev/providers/dump.yaml', 'operations' => ['pull']],
                ['name' => 'live', 'source' => '.ddev/providers/live.yaml', 'operations' => ['pull', 'push']],
            ],
            Project::describe()['environment']['providers'],
        );

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('- ddev pull dump (.ddev/providers/dump.yaml)', $text);
        // A recipe with no push command is one nothing can be pushed upstream
        // with, and the difference is worth the word.
        self::assertStringContainsString('- ddev pull live, ddev push live (.ddev/providers/live.yaml)', $text);
        self::assertStringNotContainsString('acquia', $text);
    }

    #[Requirement('R-PRJ-008')]
    #[Decision('D-KNW-055')]
    #[Test]
    public function anEnvironmentThatIsNotDdevIsSaidToBeUnread(): void
    {
        // A stated console is how a layout this server could not work out gets
        // named at all, and it is evidence that the installation is reached
        // somewhere other than this shell. Answering null there would say
        // "these run where you are", which is the claim that went wrong —
        // `D-KNW-055`.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => ['ci' => 'phpunit']]);
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=docker compose exec web bin/typo3');
        Instance::discoverFrom($root);

        self::assertSame([
            'via' => Typo3Cli::VIA_OVERRIDE,
            'php' => null,
            'node' => null,
            'source' => Typo3Cli::CONSOLE_VARIABLE,
            'project' => null,
            'hostnames' => [],
            'entered' => false,
            'hooks' => [],
            'providers' => [],
        ], Project::describe()['environment']);
        self::assertStringContainsString(
            'nothing readable here says which PHP that is',
            Registry::call('typo3_project_describe', [])->text,
        );

        // An interpreter on this machine is not another environment: it is the
        // shell the declared commands already run in.
        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' /some/where/typo3');

        self::assertNull(Project::describe()['environment']);
        self::assertStringContainsString(
            'Nothing in this repository configures an environment of its own',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    /**
     * Each site is named with the base and the root page it carries.
     *
     * Two feedback from one session name that pair as what gave a frontend 404
     * its shape before a file was opened — `2026-08-18-074305` and
     * `2026-08-18-074200`, on an installation whose second site had base `/`
     * and a root page that was gone. `base` and `rootPageId` are required keys
     * of `ProjectDescribe::outputSchema()`; the sentence that carries them was
     * in no assertion.
     */
    #[Test]
    public function everySiteIsNamedWithTheBaseAndTheRootPageItCarries(): void
    {
        $root = $this->composerProject();
        $this->site($root, 'main', ['base' => 'https://blog.ddev.site/', 'rootPageId' => 1]);
        $this->site($root, 'blog', ['base' => '/', 'rootPageId' => 2]);
        Instance::discoverFrom($root);

        $sites = array_column(Project::describe()['sites'], null, 'identifier');
        self::assertSame('https://blog.ddev.site/', $sites['main']['base']);
        self::assertSame(2, $sites['blog']['rootPageId']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('- main at https://blog.ddev.site/, root page 1', $text);
        self::assertStringContainsString('- blog at /, root page 2', $text);
    }

    #[Requirement('R-PRJ-002')]
    #[Test]
    public function aSiteConfigurationThatCannotBeParsedCostsThatSiteAndNoOther(): void
    {
        // Mid-edit, or with a placeholder a parser rejects. The other sites are
        // still the answer.
        $root = $this->composerProject();
        $this->site($root, 'good', ['base' => 'https://good.example/']);
        mkdir($root . '/config/sites/broken', 0o777, true);
        file_put_contents($root . '/config/sites/broken/config.yaml', "base: [unclosed\n\tmixed: tabs");
        Instance::discoverFrom($root);

        $identifiers = array_column(Project::describe()['sites'], 'identifier');
        self::assertContains('good', $identifiers);
        self::assertContains('broken', $identifiers, 'the site exists even when its configuration does not parse');
    }

    #[Requirement('R-PRJ-001')]
    #[Test]
    public function withoutAnInstallationThereIsNoProjectToDescribe(): void
    {
        Instance::discoverFrom(null);

        self::assertNull(Project::describe());

        $result = Registry::call('typo3_project_describe', []);
        self::assertSame(['unsupported'], array_keys($result->data));
    }

    #[Decision('D-ANS-085')]
    #[Requirement('R-PRJ-001')]
    #[Decision('D-DIS-019')]
    #[Test]
    public function theRepositoryIsDescribedBeforeAnythingIsInstalledInIt(): void
    {
        // The clone of `github.com/TYPO3GmbH/blog` feedback/2026-08-18-070333
        // was recorded in: no vendor directory, no .build/, no config/. The
        // whole answer was gated on Composer's installed metadata, so the one
        // call this server prescribes first came back with `no-installation`
        // and nothing else — and the session read composer.json, package.json
        // and .ddev/config.yaml out of the checkout by hand, in the state the
        // installation workflow declares as its entry condition (`D-ANS-085`) —
        // `D-DIS-019`.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 't3g/blog',
            'type' => 'typo3-cms-extension',
            'require' => ['php' => '^8.2', 'typo3/cms-core' => '^13.4.15 || ^14.3'],
            'extra' => ['typo3/cms' => ['extension-key' => 'blog']],
            'scripts' => ['ci' => ['@cgl:ci'], 'cgl:ci' => 'php-cs-fixer fix --dry-run'],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/package.json', json_encode([
            'scripts' => ['build' => 'vite build'],
        ], JSON_THROW_ON_ERROR));
        $this->declare($root . '/.ddev/config.yaml', "name: blog\ntype: typo3\nphp_version: \"8.2\"\n");
        Instance::discoverFrom($root);

        $project = Project::describe();

        self::assertFalse($project['installed']);
        self::assertSame('^8.2', $project['phpConstraint']);
        self::assertSame('^13.4.15 || ^14.3', $project['coreConstraint']);
        self::assertSame('8.2', $project['environment']['php']);
        self::assertSame('blog', $project['environment']['project']);
        self::assertSame(
            ['composer ci', 'composer cgl:ci', 'npm run build'],
            array_column($project['commands'], 'command'),
        );

        // What the installed tree owns stays withheld, and the answer says
        // which state it is in rather than leaving a null and an empty list to
        // read as an installation that has neither.
        self::assertNull($project['typo3Version']);
        self::assertNull($project['corePhpConstraint']);
        self::assertSame([], $project['extensions']);

        $result = Registry::call('typo3_project_describe', []);
        self::assertArrayNotHasKey('unsupported', $result->data);
        self::assertStringContainsString('not installed here yet', $result->text);
        self::assertStringContainsString('Nothing is installed below this root yet', $result->text);
        self::assertStringContainsString('Extensions: not readable until the install has run', $result->text);
    }

    #[Requirement('R-PRJ-011')]
    #[Decision('D-DIS-019')]
    #[Test]
    public function aRepositoryThatSaysNothingAboutTypo3IsNotDescribedAsOne(): void
    {
        // The other half of the same rule. The walk goes up twelve directories,
        // so admitting any composer.json would answer for whatever PHP
        // repository a session happens to be standing below — and this tool is
        // the one every task opens with — `D-DIS-019`.
        $root = $this->temporaryDirectory();
        file_put_contents($root . '/composer.json', json_encode([
            'name' => 'acme/toolkit',
            'type' => 'library',
            'require' => ['symfony/finder' => '^7.4'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        self::assertNull(Project::describe());
        self::assertSame(['unsupported'], array_keys(Registry::call('typo3_project_describe', [])->data));
    }

    #[Test]
    public function theAnswerNamesTheCommandsThatExistHere(): void
    {
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => ['ci' => 'phpunit']]);
        Instance::discoverFrom($root);

        $text = Registry::call('typo3_project_describe', [])->text;

        self::assertStringContainsString('composer ci', $text);
        self::assertStringContainsString('testing suites do not', $text);
        // Nothing here runs a core suite, so there is nothing to point at:
        // runTests.sh is not in this repository. Read of the half that is about
        // this repository, because the guides below it are the same corpus in
        // every answer and `core/testing/scripts` names that tool in the
        // sentence saying it holds inside a core checkout.
        self::assertStringNotContainsString('typo3_test_run_guide', self::aboutThisRepository($text));
    }

    #[Decision('D-ANS-031')]
    #[Requirement('R-ANS-018')]
    #[Test]
    public function whatACoreCheckoutDoesNotDeclareIsSaidWithTheToolThatHasIt(): void
    {
        // The four gerrit hook installers are the whole of what a core checkout
        // declares, and the sentence beside them said the suites are not among
        // them and named nothing that has them. A session read that, went
        // looking by hand, and reported preferring a `Build/bin/phpunit` the
        // checkout has no directory for — `D-ANS-031`.
        $root = $this->coreCheckout('15.0.0-dev');
        $this->manifest($root, ['scripts' => ['gerrit:setup' => 'Acme\\Scripts::install']]);
        Instance::discoverFrom($root);

        $text = Registry::call('typo3_project_describe', [])->text;

        self::assertStringContainsString('testing suites do not', $text);
        self::assertStringContainsString('Build/Scripts/runTests.sh', $text);
        self::assertStringContainsString('typo3_test_run_guide', $text);

        // And where the manifest declares nothing at all, because the pointer
        // is what the checkout is rather than what it happens to have declared.
        $bare = $this->coreCheckout();
        Instance::discoverFrom($bare);

        self::assertStringContainsString(
            'typo3_test_run_guide',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    /**
     * The same sentence for every repository that is not the core, which it
     * had for none of them. A session in an extension repository spent eight
     * round trips working out what `composer test:php:functional` needed —
     * credentials, an account that may create a database per test class, and
     * which PHP the run is on — while this answer listed the command and said
     * none of it (`D-ANS-092`).
     */
    #[Decision('D-ANS-092')]
    #[Test]
    public function aDeclaredSuiteOutsideTheCoreIsToldWhatARunNeedsFirst(): void
    {
        $root = $this->composerProject('vendor', '13.4.33');
        $this->manifest($root, ['scripts' => [
            'test:php:functional' => 'phpunit -c Build/FunctionalTests.xml',
            'cgl' => 'php-cs-fixer fix',
        ]]);
        Instance::discoverFrom($root);

        $text = Registry::call('typo3_project_describe', [])->text;

        self::assertStringContainsString('id=project-extension-tests', $text);
        // Not the core's pointer: runTests.sh is in the core repository, and
        // handing it over here is the widening `D-ANS-086` rejected.
        self::assertStringNotContainsString('typo3_test_run_guide', self::aboutThisRepository($text));

        // And a repository declaring no suite is told nothing, because the
        // pointer answers a command that is there rather than a kind of
        // checkout.
        $without = $this->composerProject('vendor', '13.4.33');
        $this->manifest($without, ['scripts' => ['cgl' => 'php-cs-fixer fix']]);
        Instance::discoverFrom($without);

        self::assertStringNotContainsString(
            'project-extension-tests',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    #[Requirement('R-PRJ-007')]
    #[Test]
    public function aDeclaredCommandSaysWhetherRunningItChangesTheSources(): void
    {
        // Three recorded REVIEW-02 runs were told not to change files and ran
        // none of the fifteen commands they were offered — among them a
        // php-cs-fixer line and a phplint line that change nothing. A name
        // cannot carry that: cgl and cgl:ci are the same tool one flag apart,
        // so the body is what is read.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => [
            'cgl' => ['php-cs-fixer --diff -v fix'],
            'cgl:ci' => ['php-cs-fixer --diff -v --dry-run fix'],
            'phpstan' => ['phpstan analyze --configuration Build/phpstan.neon'],
            'phpstan:baseline' => ['phpstan analyze --generate-baseline Build/phpstan-baseline.neon'],
            'test:php:lint' => ['phplint'],
            'test:php:unit' => ['phpunit -c Build/phpunit-unit.xml'],
            'lint' => ['@test:php:lint'],
            'test' => ['@test:php:lint', '@test:php:unit'],
            'set-version' => ['extension-helper version:set'],
        ]]);
        file_put_contents($root . '/package.json', json_encode([
            'scripts' => ['lint:js' => 'eslint Resources/Private', 'build' => 'vite build'],
        ], JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $runs = array_column(Project::describe()['commands'], 'runs', 'command');

        self::assertSame([
            'composer cgl' => Project::RUNS_AS_CHANGE,
            'composer cgl:ci' => Project::RUNS_AS_CHECK,
            'composer phpstan' => Project::RUNS_AS_CHECK,
            'composer phpstan:baseline' => Project::RUNS_AS_CHANGE,
            'composer test:php:lint' => Project::RUNS_AS_CHECK,
            // It runs the project's own code, and no declaration says what that
            // writes. Undeclared is not a quiet no.
            'composer test:php:unit' => Project::RUNS_UNDECLARED,
            // A reference is followed, so a wrapper is worth what it wraps —
            // and a script that reaches one undeclared line is undeclared.
            'composer lint' => Project::RUNS_AS_CHECK,
            'composer test' => Project::RUNS_UNDECLARED,
            'composer set-version' => Project::RUNS_AS_CHANGE,
            'npm run lint:js' => Project::RUNS_AS_CHECK,
            'npm run build' => Project::RUNS_AS_CHANGE,
        ], $runs);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('composer cgl:ci (composer.json) — check: php-cs-fixer --diff -v --dry-run fix', $text);
        self::assertStringContainsString('A task told not to change files can run the checks and nothing else', $text);
    }

    #[Requirement('R-PRJ-007')]
    #[Test]
    public function aCommandThatDeclaresNothingReadableIsNotCalledSafe(): void
    {
        // The failure that matters is the other direction: a body nobody can
        // read reported as a check would send a review into a script that
        // rewrites the checkout it was told to leave alone.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => [
            'shell' => ["find src -name '*.php' -print0 | xargs -0 -n1 php -l"],
            'handler' => 'Acme\\Composer\\Scripts::install',
            'console' => ['@php vendor/bin/typo3 extension:setup'],
            'itself' => ['@itself'],
            'linted' => ['@php vendor/bin/phplint'],
        ]]);
        Instance::discoverFrom($root);

        self::assertSame(
            [
                'composer shell' => Project::RUNS_UNDECLARED,
                'composer handler' => Project::RUNS_UNDECLARED,
                'composer console' => Project::RUNS_UNDECLARED,
                // A script that references itself ends, and ends undeclared.
                'composer itself' => Project::RUNS_UNDECLARED,
                // @php is which PHP, not what is run: the tool behind it is
                // still read.
                'composer linted' => Project::RUNS_AS_CHECK,
            ],
            array_column(Project::describe()['commands'], 'runs', 'command'),
        );
    }

    #[Requirement('R-PRJ-007')]
    #[Test]
    public function anEnvironmentAssignmentInFrontOfACommandIsNotTheCommand(): void
    {
        // The `news` run of 2026-07-31 was offered six commands and told
        // `unknown` about all six. `PHP_CS_FIXER_IGNORE_ENV=1` is the documented
        // way to run the fixer on a PHP it does not claim support for yet, so
        // the shape is common in exactly the repositories that span two majors —
        // and read as the tool, it makes `cs` and `csfix` the same answer.
        $root = $this->composerProject();
        $this->manifest($root, ['scripts' => [
            'cs' => ['PHP_CS_FIXER_IGNORE_ENV=1 php ./.Build/bin/php-cs-fixer fix --dry-run -v --config ./Build/php-cs-fixer.php ./'],
            'csfix' => ['PHP_CS_FIXER_IGNORE_ENV=1 php ./.Build/bin/php-cs-fixer fix -v --config ./Build/php-cs-fixer.php ./'],
            // Behind a composer prefix, and with a value that has a space in it.
            'stan' => ['@php XDEBUG_MODE=off vendor/bin/phpstan analyse'],
            'lint' => ['PHPLINT_ARGS="-c Build/phplint.yml" vendor/bin/phplint'],
        ]]);
        Instance::discoverFrom($root);

        self::assertSame(
            [
                'composer cs' => Project::RUNS_AS_CHECK,
                'composer csfix' => Project::RUNS_AS_CHANGE,
                'composer stan' => Project::RUNS_AS_CHECK,
                'composer lint' => Project::RUNS_AS_CHECK,
            ],
            array_column(Project::describe()['commands'], 'runs', 'command'),
            'the reporter and the rewriter must not classify the same',
        );
        // The assignment is out of the tool name, not out of the declaration:
        // it is part of what running the command means.
        self::assertStringContainsString(
            'composer cs (composer.json) — check: PHP_CS_FIXER_IGNORE_ENV=1 php',
            Registry::call('typo3_project_describe', [])->text,
        );
    }

    #[Requirement('R-PRJ-007')]
    #[Decision('D-EVI-003')]
    #[Test]
    public function aCommandThatWritesIsNeverReportedAsACheck(): void
    {
        // `D-EVI-003` is wrong if a run reports a checkout modified by a command
        // marked `check` — and by the time a run reports it, the checkout it
        // was told to leave alone is modified. So the writers are listed here
        // instead. Each of them rewrites what it is pointed at, and `check` is
        // the one answer none of them may have, because it is the answer a
        // review acts on unasked. `unknown` is not a failure: a body nobody can
        // read is allowed to be undecided.
        $writes = [
            'php-cs-fixer --diff -v fix',
            'php-cs-fixer fix --config Build/php-cs-fixer.php',
            'ecs check --fix',
            'phpcbf --standard=Build/phpcs.xml',
            'rector process src',
            'phpstan analyse --generate-baseline Build/phpstan-baseline.neon',
            'psalm --set-baseline=psalm-baseline.xml',
            'psalm --alter --issues=MissingReturnType',
            'eslint --fix Resources/Private/JavaScript',
            'stylelint --fix Resources/Private/Scss',
            'tsc',
            'vite build',
            'composer install',
            'composer update --no-progress',
            'composer dump-autoload',
            'npm ci',
            'extension-helper version:set 2.0.0',
            'extension-helper changelog:create',
            'git checkout -- .',
            'rm -rf var/cache',
            // The writer is not always in front. A line chains, every command
            // on it runs, and an npm script chains by convention — read as its
            // first tool, each of these is the check that precedes the write.
            'phpstan analyse && php-cs-fixer fix',
            'php-cs-fixer fix --dry-run && rector process src',
            'php -l src/Extension.php; rm -rf var/log',
            'phpcs || phpcbf',
            'tsc --noEmit && vite build',
            // And a wrapper is worth what it reaches.
            '@cgl',
        ];

        $answers = [];
        foreach ($writes as $declaration) {
            $answers[$declaration] = Project::runs($declaration, ['cgl' => 'php-cs-fixer fix']);
        }

        self::assertSame(
            [],
            array_keys(array_filter($answers, static fn(string $runs): bool => $runs === Project::RUNS_AS_CHECK)),
            'a command that rewrites the sources was offered to a task told not to change files',
        );
        // The other direction of the same reading, so that "chained" does not
        // become an answer of its own: a line that chains two checks is one.
        self::assertSame(
            Project::RUNS_AS_CHECK,
            Project::runs('phpstan analyse && phplint -c Build/phplint.yml'),
        );
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function aPatchedDependencyIsPartOfWhatThisProjectIs(): void
    {
        // A patched package does not behave as its version says, and the next
        // composer update either reapplies the patch or fails on it. Nothing
        // else about this project matters more to an upgrade.
        $root = $this->composerProject();
        $this->manifest($root, ['extra' => ['patches' => [
            'typo3/cms-core' => ['Keep the old redirect behaviour' => 'patches/core-redirects.patch'],
        ]]]);
        Instance::discoverFrom($root);

        self::assertSame(
            [['package' => 'typo3/cms-core', 'description' => 'Keep the old redirect behaviour', 'file' => 'patches/core-redirects.patch']],
            Project::describe()['patches'],
        );
        self::assertStringContainsString('Patched dependencies', Registry::call('typo3_project_describe', [])->text);
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function whatAnExtensionRegistersIsReadFromItsOwnFiles(): void
    {
        // The project scope names an extension and its path. A maintenance
        // question is about what is inside it, and all of it is declarative.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Configuration/TCA/tx_acme_event.php', "<?php\nreturn ['ctrl' => []];\n");
        // Numbered, because that is what fixes the order overrides load in —
        // so the file name says nothing and the table has to be read.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/102_tt_content.php',
            "<?php\n\$GLOBALS['TCA']['tt_content']['columns']['header']['label'] = 'x';\n"
        );
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/900_pages.php',
            "<?php\nExtensionManagementUtility::addToAllTCAtypes(\n 'pages',\n '--div--;Acme',\n);\n"
        );
        $this->declare($extension . '/Configuration/Icons.php', "<?php\nreturn [\n 'acme-event' => ['provider' => 'x'],\n];\n");
        $this->declare(
            $extension . '/Configuration/Backend/Modules.php',
            "<?php\nreturn [\n 'acme_events' => ['parent' => 'web', 'labels' => 'acme.modules.events'],\n];\n"
        );
        $this->declare(
            $extension . '/Configuration/RequestMiddlewares.php',
            "<?php\nreturn [\n 'frontend' => [\n  'acme/tracking' => ['target' => 'X'],\n ],\n];\n"
        );
        $this->declare(
            $extension . '/Configuration/Services.yaml',
            "services:\n  Acme\\\\SitePackage\\\\Processor:\n    tags:\n      - name: 'data.processor'\n        identifier: 'acme-events'\n"
        );
        $this->declare($extension . '/Configuration/Sets/AcmeEvents/config.yaml', "name: acme/events-set\n");
        $this->declare($extension . '/Resources/Private/Partials/Event.fluid.html', '');
        $this->declare($extension . '/Classes/DataProcessing/EventProcessor.php', "<?php\n");
        $this->declare($extension . '/ext_localconf.php', "<?php\n");
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(['tx_acme_event'], $result->data['tcaTables']);
        self::assertSame(
            ['pages', 'tt_content'],
            $result->data['tcaOverrides'],
            'the table is read from what the file does, not from what it is called',
        );
        self::assertSame(['acme-event'], $result->data['icons']);
        self::assertSame(['acme_events'], $result->data['backendModules']);
        self::assertSame(['acme/tracking'], $result->data['middlewares']);
        self::assertSame(['data.processor'], $result->data['serviceTags']);
        self::assertSame(
            [['name' => 'acme/events-set', 'path' => 'Configuration/Sets/AcmeEvents/', 'files' => []]],
            $result->data['siteSets'],
        );
        self::assertSame(['Resources/Private/Partials/'], $result->data['fluidRoots']);
        self::assertSame(
            ['directories' => [['name' => 'DataProcessing', 'files' => 1]], 'looseFiles' => 0, 'total' => 1],
            $result->data['classes'],
        );
        self::assertContains('ext_localconf.php', $result->data['files']);
        self::assertSame(Project::ORIGIN_PROJECT, $result->data['origin']);

        // What is declared is here; what ext_localconf.php does at runtime is
        // not, and the answer says so rather than letting it be assumed.
        self::assertStringContainsString('not what it does at runtime', $result->text);
    }

    #[Decision('D-ANS-008')]
    #[Test]
    public function aClassCountSaysWhatItCounted(): void
    {
        // The count is the whole subtree, and bootstrap_package is where that
        // shows: 21 files in Classes/Updates/ and six below Criteria/, reported
        // as one number. A caller counted the top level, got 21, and filed the
        // answer as wrong — so the number says which it is — `D-ANS-008`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Classes/Updates/AbstractUpdate.php', "<?php\n");
        $this->declare($extension . '/Classes/Updates/BackendLayoutUpdate.php', "<?php\n");
        $this->declare($extension . '/Classes/Updates/Criteria/HasBackendLayout.php', "<?php\n");
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            ['directories' => [['name' => 'Updates', 'files' => 3]], 'looseFiles' => 0, 'total' => 3],
            $result->data['classes'],
        );
        self::assertStringContainsString('Updates (3)', $result->text);
        self::assertStringContainsString('its own subdirectories included', $result->text);
    }

    #[Decision('D-ANS-045')]
    #[Requirement('R-ANS-020')]
    #[Test]
    public function everyDirectoryBelowClassesIsInTheAnswer(): void
    {
        // Thirteen recognised names left Classes/Utility/ in no line of the
        // answer, and the audit that trusted the section never opened the one
        // class that decided its question — R-ANS-020. A file lying directly
        // in Classes/ was dropped the same way.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Classes/EventListener/LoginTourEventListener.php', "<?php\n");
        $this->declare($extension . '/Classes/Utility/MascotResolver.php', "<?php\n");
        $this->declare($extension . '/Classes/Utility/Path/Resolver.php', "<?php\n");
        $this->declare($extension . '/Classes/SingletonInterface.php', "<?php\n");
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                'directories' => [
                    ['name' => 'EventListener', 'files' => 1],
                    ['name' => 'Utility', 'files' => 2],
                ],
                'looseFiles' => 1,
                'total' => 4,
            ],
            $result->data['classes'],
        );
        self::assertStringContainsString('Utility (2)', $result->text);
        self::assertStringContainsString('1 directly in Classes/', $result->text);
        // The number the caller checks the section with, in the one command
        // they would check it by.
        self::assertStringContainsString('4 PHP files in total', $result->text);
        self::assertStringContainsString("find Classes -name '*.php' | wc -l", $result->text);
    }

    #[Decision('D-ANS-045')]
    #[Requirement('R-ANS-020')]
    #[Test]
    public function aFluidRootIsRenderedAsADirectory(): void
    {
        // The audited extension of feedback/2026-08-03-164651 declares no root
        // at all: it appends its layout root to setLayoutRootPaths() while an
        // event runs, and got the line an extension that declares one gets.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Resources/Private/Layouts/Login.html', '');
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(['Resources/Private/Layouts/'], $result->data['fluidRoots']);
        self::assertStringContainsString(
            'Fluid root directories it ships: Resources/Private/Layouts/',
            $result->text,
        );
        self::assertStringContainsString('rather than a root something declared', $result->text);
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function theContentElementsAnExtensionAddsAreNamed(): void
    {
        // "It extends tt_content" says where they are registered. What a
        // sitepackage question is about is which ones — and both item shapes
        // are in use, because an extension is written for the line it supports.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/102_tt_content.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:teaser',
                    'value' => 'acme_teaser',
                    'icon' => 'acme-teaser',
                ], 'header', 'after');
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:slider',
                    'acme_slider',
                    'acme-slider',
                ]);
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'label' => 'Built somewhere else',
                    'value' => self::CTYPE,
                ]);
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'header_layout', [
                    'label' => 'Quiet',
                    'value' => 'acme_quiet',
                ]);
                PHP
        );
        // Which template one renders through is the next question after which
        // ones there are, and both TypoScript shapes are in use.
        $this->declare(
            $extension . '/Configuration/Sets/AcmeSite/setup.typoscript',
            <<<'TYPOSCRIPT'
                tt_content.acme_teaser =< lib.contentElement
                tt_content.acme_teaser {
                    templateName = Teaser
                }
                tt_content {
                    acme_quiet =< lib.contentElement
                    acme_quiet.templateName = Quiet
                }
                TYPOSCRIPT
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                ['identifier' => 'acme_slider', 'kind' => 'element', 'templateName' => null, 'source' => null, 'pluginSettings' => null, 'flexForm' => null],
                ['identifier' => 'acme_teaser', 'kind' => 'element', 'templateName' => 'Teaser', 'source' => 'Configuration/Sets/AcmeSite/setup.typoscript', 'pluginSettings' => null, 'flexForm' => null],
            ],
            $result->data['contentElements'],
            'both item shapes are read, and a value that is no literal is left out rather than guessed',
        );
        self::assertSame(['tt_content'], $result->data['tcaOverrides']);
        // An item of another field is a value in that field, not a content
        // element — not even when the TypoScript renders one under that name.
        self::assertNotContains('acme_quiet', array_column($result->data['contentElements'], 'identifier'));
        self::assertStringContainsString('acme_teaser — renders through Teaser', $result->text);
        self::assertStringContainsString('at runtime, takes from a constant', $result->text);
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function aContentElementRegisteredWithAddRecordTypeIsFoundAsWell(): void
    {
        // The call that carries no table in front of it: since 13.4 the
        // registration is one addRecordType() whose table argument is the fifth
        // and defaults to tt_content — and it is written in a file per element,
        // so the file name is the one thing that must not be believed.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_hero_carousel.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addRecordType(
                    [
                        'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:hero_carousel',
                        'value' => 'acme_hero_carousel',
                        'icon' => 'acme-hero-carousel',
                        'group' => 'default',
                    ],
                    '--div--;General,header,acme_slides',
                );
                PHP
        );
        // The same call registers record types of other tables, and those are
        // page types rather than content elements.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/pages_landing.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addRecordType(
                    ['label' => 'Landing page', 'value' => '117', 'icon' => 'acme-landing'],
                    '--div--;General,title',
                    [],
                    '',
                    'pages',
                );
                PHP
        );
        $this->declare(
            $extension . '/Configuration/Sets/AcmeSite/setup.typoscript',
            "tt_content.acme_hero_carousel =< lib.contentElement\ntt_content.acme_hero_carousel.templateName = HeroCarousel\n"
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [[
                'identifier' => 'acme_hero_carousel',
                'kind' => 'element',
                'templateName' => 'HeroCarousel',
                'source' => 'Configuration/Sets/AcmeSite/setup.typoscript',
                'pluginSettings' => null,
                'flexForm' => null,
            ]],
            $result->data['contentElements'],
        );
        self::assertSame(
            ['pages', 'tt_content'],
            $result->data['tcaOverrides'],
            'the table comes from the call, so the per-element file name is never mistaken for one',
        );
    }

    #[Decision('D-ANS-015')]
    #[Requirement('R-ANS-012')]
    #[Decision('D-ANS-018')]
    #[Test]
    public function anExtbasePluginIsToldApartFromAnElementWithoutATemplate(): void
    {
        // An audit of a real sitepackage on 2026-07-31 was told both of its
        // plugins had "no templateName in this extension's TypoScript" and
        // wrote a finding about two TypoScript files nobody was going to write:
        // configurePlugin() generates the rendering definition, and the
        // plugin's own templates are configured under plugin.tx_<signature> —
        // D-ANS-015 — `D-ANS-018`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content.php',
            <<<'PHP'
                <?php
                ExtensionUtility::registerPlugin(
                    'MySitepackage',
                    'Catalogue',
                    'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:catalogue',
                    'acme-catalogue',
                );
                ExtensionManagementUtility::addTcaSelectItem('tt_content', 'CType', [
                    'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:teaser',
                    'value' => 'acme_teaser',
                ]);
                PHP
        );
        // The plugin arrives at its configuration by reference, which is a line
        // no store of assignments alone would hold.
        $this->declare(
            $extension . '/Configuration/TypoScript/setup.typoscript',
            "plugin.tx_mysitepackage_catalogue < lib.acmePlugin\n",
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                [
                    'identifier' => 'acme_teaser',
                    'kind' => 'element',
                    'templateName' => null,
                    'source' => null,
                    'pluginSettings' => null,
                    'flexForm' => null,
                ],
                [
                    'identifier' => 'mysitepackage_catalogue',
                    'kind' => 'plugin',
                    'templateName' => null,
                    'source' => null,
                    'pluginSettings' => 'Configuration/TypoScript/setup.typoscript',
                    'flexForm' => null,
                ],
            ],
            $result->data['contentElements'],
            'the signature both plugin calls derive is the identifier, and the kind is what the two answers differ by',
        );
        self::assertStringContainsString(
            'mysitepackage_catalogue — Extbase plugin, renders through the dispatcher; configured under '
                . 'plugin.tx_mysitepackage_catalogue in Configuration/TypoScript/setup.typoscript',
            $result->text,
        );
        self::assertStringContainsString('acme_teaser — no templateName in this extension\'s TypoScript', $result->text);
        self::assertStringContainsString('no templateName here is no missing template', $result->text);
    }

    #[Requirement('R-ANS-012')]
    #[Test]
    public function anIdentifierThatTookADetourThroughAVariableIsStillRead(): void
    {
        // A forward review of a real sitepackage on 2026-07-31 was told the
        // extension had three content elements. It had four: the fourth wrote
        // `$contentType = '…'` at the top of its override and used the variable
        // in the item, and the parser only saw literals. A tool that answers
        // three when there are four is worse than one that declines — the
        // session that trusts it concludes the template is dead code.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_hero_carousel.php',
            <<<'PHP'
                <?php
                $contentType = 'acme_hero_carousel';
                ExtensionManagementUtility::addTCAcolumns('tt_content', ['acme_slides' => []]);
                ExtensionManagementUtility::addRecordType(
                    [
                        'label' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:carousel',
                        'value' => $contentType,
                        'icon' => 'acme-hero-carousel',
                    ],
                    '--div--;General,header,acme_slides',
                );
                PHP
        );
        // Reassigned, so what it holds at the call depends on the order the file
        // runs in — which is the one thing reading cannot establish.
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content_reused.php',
            <<<'PHP'
                <?php
                $type = 'acme_first';
                ExtensionManagementUtility::addRecordType(['label' => 'First', 'value' => $type], 'header');
                $type = 'acme_second';
                ExtensionManagementUtility::addRecordType(['label' => 'Second', 'value' => $type], 'header');
                PHP
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            ['acme_hero_carousel'],
            array_column($result->data['contentElements'], 'identifier'),
            'a single-assignment string variable resolves, and a reassigned one is still declined',
        );
    }

    #[Requirement('R-ANS-014')]
    #[Decision('D-ANS-019')]
    #[Test]
    public function theFlexFormAContentElementBindsIsOnItsEntry(): void
    {
        // A conformance audit of a real sitepackage described its two elements
        // least of all four, and those two were the ones whose FlexForm nothing
        // opened: the file that says what the element is configured by was in
        // no answer. All three bindings core writes are here, because an
        // extension supporting two majors ships more than one of them — the
        // third argument of addPiFlexFormValue() until v14.3, where it is
        // deprecated, and the data structure argument of addPlugin() and
        // registerPlugin() from v14.2 — `D-ANS-019`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addRecordType(
                    ['label' => 'Catalogue', 'value' => 'acme_catalogue'],
                    '--div--;General,header',
                );
                ExtensionManagementUtility::addPiFlexFormValue(
                    '*',
                    'FILE:EXT:my_sitepackage/Configuration/FlexForms/Catalogue.xml',
                    'acme_catalogue',
                );
                ExtensionManagementUtility::addRecordType(
                    ['label' => 'Teaser', 'value' => 'acme_teaser'],
                    '--div--;General,header',
                );
                PHP
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            ['FILE:EXT:my_sitepackage/Configuration/FlexForms/Catalogue.xml', null],
            array_column($result->data['contentElements'], 'flexForm'),
            'the binding sits on the element it names, and an element without one binds none',
        );
        self::assertSame([], $result->data['unlistedFlexForms']);
        self::assertStringContainsString(
            'acme_catalogue — no templateName in this extension\'s TypoScript; another extension or the site may '
            . 'set it, FlexForm FILE:EXT:my_sitepackage/Configuration/FlexForms/Catalogue.xml',
            $result->text,
        );
    }

    #[Requirement('R-ANS-014')]
    #[Decision('D-ANS-019')]
    #[Test]
    public function aFlexFormBoundThroughACallThisDoesNotReadIsStillReported(): void
    {
        // A binding is read by one parser and the identifiers by another, and
        // the second does not recognise every call the first does. Where they
        // disagree the binding is reported rather than dropped: a FlexForm read
        // and then not mentioned is the same silence as one never opened.  The
        // example used to be registerPlugin(), whose signature this reads out
        // of a variable while the element list did not carry it. It carries it
        // now — that half arrived on its own branch the same day — so the case
        // is made with a content type nothing in the file registers, which is
        // what is left of the disagreement this guards — `D-ANS-019`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content.php',
            <<<'PHP'
                <?php
                ExtensionManagementUtility::addPiFlexFormValue(
                    '*',
                    'FILE:EXT:my_sitepackage/Configuration/FlexForms/Catalogue.xml',
                    'mysitepackage_catalogue',
                );
                PHP
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame([], $result->data['contentElements']);
        self::assertSame(
            [[
                'identifier' => 'mysitepackage_catalogue',
                'flexForm' => 'FILE:EXT:my_sitepackage/Configuration/FlexForms/Catalogue.xml',
            ]],
            $result->data['unlistedFlexForms'],
            'a binding whose content type no registration call in the file declares is reported, not dropped',
        );
        self::assertStringContainsString('FlexForms bound to a content type none of the above names', $result->text);
    }

    #[Requirement('R-ANS-014')]
    #[Decision('D-ANS-019')]
    #[Test]
    public function aSiteSetIsAnsweredByTheFilesCoreReadsItFor(): void
    {
        // "Site sets: acme/site (Configuration/Sets/Acme/)" names the directory
        // and says nothing about what is in it, so a route enhancer shipped in
        // a set was in no answer this server gives. Core reads the directory
        // for a fixed list of names, and which of them are there is what the
        // set carries — `D-ANS-019`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Configuration/Sets/Acme/config.yaml', "name: acme/site\n");
        $this->declare($extension . '/Configuration/Sets/Acme/route-enhancers.yaml', "routeEnhancers: {}\n");
        $this->declare($extension . '/Configuration/Sets/Acme/settings.definitions.yaml', "settings: {}\n");
        $this->declare($extension . '/Configuration/Sets/Acme/setup.typoscript', "page = PAGE\n");
        // A name core does not read there is not a registration, whatever it
        // looks like beside the ones that are.
        $this->declare($extension . '/Configuration/Sets/Acme/tsconfig.yaml', "x: 1\n");
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [[
                'name' => 'acme/site',
                'path' => 'Configuration/Sets/Acme/',
                'files' => ['settings.definitions.yaml', 'route-enhancers.yaml', 'setup.typoscript'],
            ]],
            $result->data['siteSets'],
        );
        self::assertStringContainsString(
            'beside config.yaml: settings.definitions.yaml, route-enhancers.yaml, setup.typoscript',
            $result->text,
        );
        // The one file in the list that is not read on every covered major.
        self::assertStringContainsString('route-enhancers.yaml is read from v14.1', $result->text);
    }

    #[Requirement('R-ANS-014')]
    #[Decision('D-ANS-019')]
    #[Test]
    public function aFormSetIsAnsweredWithTheDefinitionsItStores(): void
    {
        // Since v14.2 a directory below Configuration/Form/ carrying a
        // config.yaml is collected from every active extension and registered
        // nowhere, so nothing in the file tree says the form definitions beside
        // it are loaded at all. The way before it registers a YAML file through
        // this extension's own TypoScript, which is read from the same place
        // the content element templates are — `D-ANS-019`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/Form/Acme/config.yaml',
            <<<'YAML'
                name: acme/forms
                priority: 200
                persistenceManager:
                  allowedExtensionPaths:
                    10: 'EXT:my_sitepackage/Resources/Private/Forms/'
                YAML
        );
        $this->declare($extension . '/Resources/Private/Forms/ProductRequest.form.yaml', "identifier: product\n");
        $this->declare(
            $extension . '/Configuration/Yaml/FormSetup.yaml',
            "persistenceManager:\n  allowedExtensionPaths:\n    20: 'EXT:my_sitepackage/Resources/Private/Legacy/'\n",
        );
        $this->declare(
            $extension . '/Configuration/TypoScript/setup.typoscript',
            <<<'TYPOSCRIPT'
                plugin.tx_form.settings.yamlConfigurations {
                    1732785702 = EXT:my_sitepackage/Configuration/Yaml/FormSetup.yaml
                }
                module.tx_form.settings.yamlConfigurations {
                    1732785702 = EXT:my_sitepackage/Configuration/Yaml/FormSetup.yaml
                }
                TYPOSCRIPT
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                [
                    'path' => 'Configuration/Form/Acme/config.yaml',
                    'name' => 'acme/forms',
                    'registeredBy' => 'set',
                    'storagePaths' => ['EXT:my_sitepackage/Resources/Private/Forms/'],
                    'formDefinitions' => ['Resources/Private/Forms/ProductRequest.form.yaml'],
                ],
                [
                    'path' => 'Configuration/Yaml/FormSetup.yaml',
                    'name' => null,
                    'registeredBy' => 'typoscript',
                    'storagePaths' => ['EXT:my_sitepackage/Resources/Private/Legacy/'],
                    // The storage is declared and the directory is not there:
                    // a form nothing stores is the finding, not a shorter list.
                    'formDefinitions' => [],
                ],
            ],
            $result->data['formConfigurations'],
            'both ways in, and the file the plugin and the module registration name is one entry',
        );
        self::assertStringContainsString('form set acme/forms', $result->text);
        self::assertStringContainsString('registered by TypoScript, the way deprecated in v14.2', $result->text);
        self::assertStringContainsString('Resources/Private/Forms/ProductRequest.form.yaml', $result->text);
    }

    #[Requirement('R-ANS-012')]
    #[Requirement('R-PRJ-005')]
    #[Test]
    public function aRegistrationBuiltInALoopIsNotDeterminable(): void
    {
        // The `news` run of 2026-07-31 was told the extension registers 26
        // icons, two of which are `provider` and `source`: its Icons.php builds
        // the list in a foreach, and the literal describing a single icon sits
        // at the same bracket depth as the returned one. Two plausible names are
        // worse than none — a review comparing them against
        // Resources/Public/Icons reports two missing files nobody is missing.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/Icons.php',
            <<<'PHP'
                <?php
                $iconList = [];
                foreach ([
                    'acme-event' => 'event.svg',
                    'acme-venue' => 'venue.svg',
                ] as $identifier => $path) {
                    $iconList[$identifier] = [
                        'provider' => SvgIconProvider::class,
                        'source' => 'EXT:my_sitepackage/Resources/Public/Icons/' . $path,
                    ];
                }
                return $iconList;
                PHP
        );
        // The other half of the same rule: a literal beside the returned one is
        // a different array, and its keys are not module identifiers.
        $this->declare(
            $extension . '/Configuration/Backend/Modules.php',
            <<<'PHP'
                <?php
                $shared = ['parent' => 'web', 'access' => 'user'];
                return [
                    'acme_events' => $shared,
                ];
                PHP
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [],
            $result->data['icons'],
            'a list only running the loop would give is not determinable, and an empty answer reads as that',
        );
        self::assertSame(['acme_events'], $result->data['backendModules']);
        // An empty section is left out of the answer, so nothing above tells
        // "there is no Icons.php" from "there is one and it is a foreach". The
        // file that came back empty is named, and the one that was read is not.
        self::assertSame(['Configuration/Icons.php'], $result->data['notReadStatically']);
        self::assertStringContainsString('Nothing could be read statically from Configuration/Icons.php', $result->text);
    }

    #[Requirement('R-ANS-012')]
    #[Test]
    public function theFilesThatRegisterByRunningAreSaidToBeUnread(): void
    {
        // notReadStatically names a declaration file the parser could not
        // follow, and `bootstrap_package` has none — so a session read its
        // empty list as nothing more to read, while ext_localconf.php was
        // registering a global Fluid namespace it was never told about.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/ext_localconf.php',
            <<<'PHP'
                <?php
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['acme'][] = 'Acme\\ViewHelpers';
                PHP
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame([], $result->data['fluidNamespaces']);
        // The file is not a casualty of the degradation: no reading of it ever
        // produced a section, so it is said where the answer states its
        // boundary rather than beside the files that came back empty.
        self::assertSame([], $result->data['notReadStatically']);
        self::assertStringContainsString('ext_localconf.php is named above and read by nothing here', $result->text);
        self::assertStringContainsString('a global Fluid namespace it sets is in none of the lists above', $result->text);
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function whatTheInstallationHasBeatsWhatTheFilesCouldBeReadFor(): void
    {
        // The same extension the parser answered for, with the installation
        // booted: the icons its loop builds, the table it adds from PHP and the
        // content element whose value came out of a variable are all in TCA and
        // in the registry, and each carries the EXT: reference that says whose
        // they are. The file half of the answer is unchanged.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Configuration/TCA/tx_acme_event.php', "<?php\nreturn ['ctrl' => []];\n");
        $this->declare(
            $extension . '/Configuration/Icons.php',
            "<?php\n\$list = [];\nforeach (['acme-teaser'] as \$i) {\n \$list[\$i] = ['provider' => 'x'];\n}\nreturn \$list;\n"
        );
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        Fixture::bootsInto(
            $root,
            [
                'acme-teaser' => 'EXT:my_sitepackage/Resources/Public/Icons/teaser.svg',
                'ext-other-thing' => 'EXT:other/Resources/Public/Icons/thing.svg',
                'actions-add' => 'EXT:core/Resources/Public/Icons/T3Icons/actions/add.svg',
            ],
            [
                'tx_acme_event' => 'LLL:EXT:my_sitepackage/Resources/Private/Language/locallang_db.xlf:event',
                'tx_other_thing' => 'LLL:EXT:other/Resources/Private/Language/locallang_db.xlf:thing',
            ],
            [
                'acme_teaser' => ['LLL:EXT:my_sitepackage/Resources/Private/Language/locallang.xlf:teaser', 'acme-teaser'],
                'other_thing' => ['LLL:EXT:other/Resources/Private/Language/locallang.xlf:thing', 'ext-other-thing'],
            ],
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame(['acme-teaser'], $result->data['icons'], 'the loop-built list the parser cannot follow');
        self::assertSame(['tx_acme_event'], $result->data['tcaTables']);
        self::assertSame(
            ['acme_teaser'],
            array_column($result->data['contentElements'], 'identifier'),
            'attributed by the reference the item carries, so another extension\'s is not this one\'s',
        );
        self::assertStringContainsString('what the booted installation has', $result->text);
    }

    #[Decision('D-ANS-018')]
    #[Test]
    public function aPluginTheInstallationReportsIsStillToldApart(): void
    {
        // Where the answer comes from the booted installation, the CType list
        // is one list for every extension and says nothing about what kind of
        // registration put an entry in it. The call that says so is in the
        // override file either way, which is the only place core allows it —
        // `D-ANS-018`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content.php',
            "<?php\nExtensionUtility::registerPlugin('MySitepackage', 'Catalogue', 'Catalogue', 'acme-catalogue');\n",
        );
        mkdir($root . '/bin');
        file_put_contents($root . '/bin/typo3', "#!/usr/bin/env php\n<?php\n");
        Fixture::bootsInto(
            $root,
            ['acme-catalogue' => 'EXT:my_sitepackage/Resources/Public/Icons/catalogue.svg'],
            [],
            ['mysitepackage_catalogue' => ['Catalogue', 'acme-catalogue']],
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame(
            [['identifier' => 'mysitepackage_catalogue', 'kind' => 'plugin', 'templateName' => null, 'source' => null, 'pluginSettings' => null, 'flexForm' => null]],
            $result->data['contentElements'],
        );
        self::assertStringContainsString('renders through the dispatcher', $result->text);
        self::assertStringContainsString(
            'this extension\'s TypoScript sets nothing under plugin.tx_mysitepackage_catalogue',
            $result->text,
        );
    }

    #[Decision('D-ANS-014')]
    #[Requirement('R-PRJ-006')]
    #[Decision('D-KNW-055')]
    #[Test]
    public function whatAnExtensionDoesNotShipIsAnswered(): void
    {
        // Three forward reviews of the same site package missed that it has no
        // manual, because there is no file to trip over: `find` cannot list a
        // document nobody wrote. The same three read its XLF headers and none
        // reported the German source language. Both are facts about the files,
        // both are cheap, and neither is discoverable by reading further — so
        // they are told rather than left to be found — `D-KNW-055`.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/Tests/Unit/SomeTest.php', "<?php\n");
        $this->declare(
            $extension . '/Resources/Private/Language/locallang.xlf',
            '<?xml version="1.0"?><xliff version="1.0"><file source-language="de" datatype="plaintext"></file></xliff>',
        );
        $this->declare(
            $extension . '/Resources/Private/Language/de.locallang.xlf',
            '<?xml version="1.0"?><xliff version="1.0"><file source-language="en" target-language="de"></file></xliff>',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertSame(
            [
                'manual' => null,
                'readme' => null,
                'tests' => ['Unit'],
                'languageFiles' => [[
                    'path' => 'Resources/Private/Language/locallang.xlf',
                    'sourceLanguage' => 'de',
                    // The prefixed file is this one's translation, not a file of
                    // its own — which is what makes a missing one visible.
                    'translations' => ['de'],
                ]],
            ],
            $result->data['artifacts'],
        );
        self::assertStringContainsString('Ships: manual none, readme none, tests Unit, language files 1', $result->text);
        self::assertStringContainsString('source-language de, translated into de', $result->text);
        // The fact is here; whether it is allowed to be German is not.
        self::assertStringContainsString('not what it should declare', $result->text);

        // The absence, in the text as well as in the data. It was in the data
        // alone until D-FBK-018: the three above are rendered present or absent
        // and the language files were rendered only where there were some, so
        // the one artifact the reporting session was praising was the one an
        // extension shipping none said nothing about. The fixture's system
        // extension is the package that ships none of the four.
        $bare = Registry::call('typo3_extension_describe', ['extension' => 'core']);

        self::assertSame(
            ['manual' => null, 'readme' => null, 'tests' => [], 'languageFiles' => []],
            $bare->data['artifacts'],
        );
        self::assertStringContainsString(
            'Ships: manual none, readme none, tests none, language files none',
            $bare->text,
        );
    }

    #[Test]
    public function anAbsentArtifactNamesTheWorkflowThatOwnsIt(): void
    {
        // A session read manual: null, readme: null and tests: [] twice, wrote
        // three READMEs by hand and shipped no test at all — while holding the
        // closing sentence of the skill it was following, which names both of
        // these workflows. The name arrives on the object it was already
        // looking at instead — D-SKL-053.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        Instance::discoverFrom($root);

        $bare = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertStringContainsString(
            'It ships no manual and no README: `typo3-extension-documentation` is the workflow that writes them.',
            $bare->text,
        );
        self::assertStringContainsString(
            'It ships no test: `typo3-extension-testing` is the workflow that sets the first one up.',
            $bare->text,
        );

        $this->declare($extension . '/README.md', "# my_sitepackage\n");
        $partial = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertStringContainsString(
            'It ships no manual: `typo3-extension-documentation` is the workflow that writes one.',
            $partial->text,
        );

        // Nothing where the extension ships all three, because the name hangs
        // on the absence: an answer about registrations does not carry a route
        // to a workflow that has nothing left to do here.
        $this->declare($extension . '/Documentation/Index.rst', "=====\nTitle\n=====\n");
        $this->declare($extension . '/Tests/Unit/SomeTest.php', "<?php\n");
        $shipped = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        self::assertStringContainsString(
            'Ships: manual Documentation/Index.rst, readme README.md, tests Unit',
            $shipped->text,
        );
        self::assertStringNotContainsString('typo3-extension-documentation', $shipped->text);
        self::assertStringNotContainsString('typo3-extension-testing', $shipped->text);
    }

    /**
     * The orientation answer carries the same verdict, unasked.
     *
     * `feedback/2026-08-24-140421` held `ExtensionDescribe::description()`
     * complete and in context under a deferring client — the deprecated-files
     * verdict named in full, the four files and the predicate each turns on —
     * and made no call. Naming the call again was the alternative and it is the
     * one that had already failed, so the verdict moves to the call the
     * instructions open every task with.
     */
    #[Decision('D-ANS-009')]
    #[Test]
    public function theOrientationAnswerCarriesTheVerdictForTheRepositorysOwnExtensions(): void
    {
        $root = $this->composerProject();
        $this->declare($root . '/packages/my_sitepackage/ext_tables.php', "<?php\n");
        // A dependency shipping the same file is its maintainer's to fix, so it
        // is not read: what this block is for is what somebody here can change.
        $this->declare($root . '/vendor/acme/other/ext_tables.php', "<?php\n");
        $installed = (array) json_decode((string) file_get_contents($root . '/vendor/composer/installed.json'), true);
        $installed['packages'][] = [
            'name' => 'acme/other',
            'type' => 'typo3-cms-extension',
            'install-path' => '../acme/other',
            'extra' => ['typo3/cms' => ['extension-key' => 'other']],
        ];
        $this->declare($root . '/vendor/composer/installed.json', json_encode($installed, JSON_THROW_ON_ERROR));
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_project_describe', []);
        $own = array_column($result->data['extensions'], 'deprecatedFiles', 'key');

        self::assertSame(['ext_tables.php'], array_column($own['my_sitepackage'], 'file'));
        self::assertSame([], $own['other']);
        self::assertStringContainsString('my_sitepackage/ext_tables.php (#109438)', $result->text);
        self::assertStringNotContainsString('other/ext_tables.php', $result->text);
        self::assertStringContainsString('From v15.0 nothing reads the file', $result->text);

        // And where nothing fired, the block says so rather than being absent —
        // an answer that volunteers deprecations read as a compatibility
        // verdict is this entry's second **Wrong if**.
        $clean = $this->composerProject();
        Instance::discoverFrom($clean);

        $quiet = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('None: not one of the four predicates holds in any of them.', $quiet);
        self::assertStringContainsString('It is not an upgrade check', $quiet);
    }

    #[Decision('D-ANS-009')]
    #[Test]
    public function theDeprecatedFilesBlockNamesEveryPredicateItLookedAt(): void
    {
        // The audit of 2026-08-03 got this block for ext_emconf.php alone and
        // could not tell from it that ext_tables.php had been checked too, so
        // it confirmed the absent sibling by hand. The block closed on "these
        // two entries whole" — a set of two rendered as one entry, naming
        // neither file. What the check covers is a fixed list, so the sentence
        // names it; a file that did not fire gets no line of its own, which
        // under this heading would read as the compatibility verdict D-ANS-009
        // keeps out of the empty case.
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/ext_emconf.php', "<?php\n\$EM_CONF[\$_EXTKEY] = [];\n");
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        // One entry, because the package ships none of the other three — the
        // case that was reported.
        self::assertSame(['ext_emconf.php'], array_column($result->data['deprecatedFiles'], 'file'));
        self::assertStringContainsString(
            'Four predicates are checked: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif, and '
                . 'ext_typoscript_setup.txt beside ext_typoscript_constants.txt.',
            $result->text,
        );
        self::assertStringContainsString('looked at rather than skipped', $result->text);
        // The changelog of a file that did not fire is in no rendered entry, so
        // the pointer carries every number rather than a count.
        self::assertStringContainsString('#109438, #108345, #98093 and #96518 whole', $result->text);
    }

    #[Requirement('R-PRJ-005')]
    #[Test]
    public function anExtensionTheInstallationLacksIsAMissWithTheKeysItHas(): void
    {
        $root = $this->composerProject();
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'news']);

        self::assertNull($result->data['path']);
        self::assertContains('my_sitepackage', $result->data['installed']);
        self::assertStringContainsString('my_sitepackage', $result->text);
    }

    /**
     * A package that took the rendering frame over renders what it registers
     * and what it does not.
     *
     * The reported session rebuilt `tt_content.shortcut` and a `Generic`
     * template and neither appeared anywhere in the answer, so a session
     * inheriting the repository would read it as six elements and delete two
     * files nothing points at — `D-ANS-149`.
     */
    #[Decision('D-ANS-149')]
    #[Test]
    public function whatAPackageRendersAndDoesNotRegisterIsItsOwnList(): void
    {
        $root = $this->composerProject();
        $extension = $root . '/packages/my_sitepackage';
        $this->declare($extension . '/composer.json', (string) json_encode([
            'name' => 'acme/sitepackage',
            'type' => 'typo3-cms-extension',
            'extra' => ['typo3/cms' => ['extension-key' => 'my_sitepackage']],
        ], JSON_THROW_ON_ERROR));
        $this->declare(
            $extension . '/Configuration/TCA/Overrides/tt_content.php',
            "<?php
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addRecordType(['value' => 'acme_teaser']);
",
        );
        $this->declare(
            $extension . '/Configuration/Sets/Acme/TypoScript/ContentElement/Teaser.typoscript',
            'tt_content.acme_teaser =< lib.contentElement
tt_content.acme_teaser {
    templateName = Teaser
}
',
        );
        $this->declare(
            $extension . '/Configuration/Sets/Acme/TypoScript/ContentElement/Shortcut.typoscript',
            'tt_content.shortcut =< lib.contentElement
tt_content.shortcut {
    templateName = Shortcut
}
',
        );
        $this->declare($extension . '/Resources/Private/Templates/Content/Generic.fluid.html', '');
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_extension_describe', ['extension' => 'my_sitepackage']);

        // What it registers stays where it was.
        self::assertSame(['acme_teaser'], array_column($result->data['contentElements'], 'identifier'));

        // What it renders and does not register is beside it, with the file a
        // removal would have to keep.
        self::assertSame(
            [[
                'identifier' => 'shortcut',
                'templateName' => 'Shortcut',
                'source' => 'Configuration/Sets/Acme/TypoScript/ContentElement/Shortcut.typoscript',
                'registeredBy' => null,
            ]],
            $result->data['renderedContentTypes'],
        );
        self::assertStringContainsString('Content types it renders and does not register:', $result->text);

        // And the one file nothing in the package points at.
        self::assertSame('Resources/Private/Templates/Content/Generic.fluid.html', $result->data['pluginFrame']);
        self::assertStringContainsString('every Extbase plugin renders through', $result->text);
    }

    private function declare(string $file, string $content): void
    {
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0o777, true);
        }
        file_put_contents($file, $content);
    }

    /**
     * The half of the answer that is about the caller's own repository.
     *
     * Everything from the guides block down is the same corpus in every
     * answer, and each entry there says which kind of checkout it holds for.
     */
    private static function aboutThisRepository(string $text): string
    {
        $split = strstr($text, ProjectDescribe::GUIDES_INTRO, true);

        return $split === false ? $text : $split;
    }

    /**
     * The kind of file a repository ships and checks nothing of.
     *
     * A sitepackage with one hand-written stylesheet and no linter for it had
     * to be told so by its owner, and everything the finding needs was already
     * in this answer — `D-ANS-148`.
     */
    #[Decision('D-ANS-148')]
    #[Test]
    public function aKindOfFileNoDeclaredCommandChecksIsNamed(): void
    {
        $root = $this->composerProject('vendor', '14.3.0');
        $this->manifest($root, [
            'require' => ['typo3/cms-core' => '^14.3'],
            'scripts' => ['cs:check' => 'php-cs-fixer fix --dry-run', 'test' => 'phpunit'],
        ]);
        $package = $root . '/packages/my_sitepackage';
        file_put_contents($package . '/ext_localconf.php', '<?php');
        mkdir($package . '/Resources/Public/Css', 0o777, true);
        file_put_contents($package . '/Resources/Public/Css/site.css', 'body { color: red; }');
        Instance::discoverFrom($root);

        $project = Project::describe();

        // PHP is declared as checked and the stylesheet is not.
        self::assertSame(['CSS'], $project['uncheckedKinds']);

        $text = Registry::call('typo3_project_describe', [])->text;
        self::assertStringContainsString('These packages ship CSS and no declared command names a checker', $text);
        // What it does not do is say what to add.
        self::assertStringNotContainsString('stylelint', $text);
    }

    /** A checker the repository does declare takes its kind off the list. */
    #[Decision('D-ANS-148')]
    #[Test]
    public function aDeclaredCheckerTakesItsKindOffTheList(): void
    {
        $root = $this->composerProject('vendor', '14.3.0');
        $this->manifest($root, [
            'require' => ['typo3/cms-core' => '^14.3'],
            'scripts' => ['lint:css' => 'npx stylelint "packages/**/*.css"'],
        ]);
        $package = $root . '/packages/my_sitepackage';
        file_put_contents($package . '/ext_localconf.php', '<?php');
        mkdir($package . '/Resources/Public/Css', 0o777, true);
        file_put_contents($package . '/Resources/Public/Css/site.css', 'body { color: red; }');
        Instance::discoverFrom($root);

        self::assertSame(['PHP'], Project::describe()['uncheckedKinds']);
    }

    /** @param array<string, mixed> $manifest */
    private function manifest(string $root, array $manifest): void
    {
        $existing = is_file($root . '/composer.json')
            ? (array) json_decode((string) file_get_contents($root . '/composer.json'), true)
            : [];
        file_put_contents(
            $root . '/composer.json',
            json_encode($manifest + $existing + ['name' => 'acme/site'], JSON_THROW_ON_ERROR),
        );
    }

    /** @param array<string, mixed> $configuration */
    private function site(string $root, string $identifier, array $configuration): void
    {
        $path = $root . '/config/sites/' . $identifier;
        mkdir($path, 0o777, true);
        file_put_contents($path . '/config.yaml', \Symfony\Component\Yaml\Yaml::dump($configuration, 4));
    }
}
