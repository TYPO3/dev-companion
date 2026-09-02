<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Environments;
use TYPO3\DevCompanion\Upkeep\SiteExtension;

/**
 * What `bin/cli environment:create` would do, held without doing it.
 *
 * No case here starts a container, and that is the same rule `D-DIS-007` was
 * written under: what fails in a build of this kind is the command that gets
 * run, and the command is readable without a docker daemon. A suite that
 * needed one would be a suite that does not run in CI, which is a suite that
 * holds nothing.
 */
final class EnvironmentsTest extends TestCase
{
    /**
     * `scenarios/readme.md` defines the environments and this list says where
     * each one comes from, so an environment added there and forgotten here is
     * an id `environment:create` answers `null` for — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function everyEnvironmentAScenarioNamesSaysWhereItComesFrom(): void
    {
        self::assertNotSame([], Environments::ids(), 'scenarios/readme.md defines no environments');
        self::assertEqualsCanonicalizing(
            Environments::ids(),
            array_keys(Environments::sources()),
            'the environments in scenarios/readme.md and the ones Environments places are not the same set',
        );
    }

    /**
     * An environment this repository does not make is one somebody has to get
     * hold of, and a refusal that does not say where from leaves them exactly
     * where the machine-bound reference left them — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function everyEnvironmentThatIsNotMadeHereSaysWhereItComesFromInstead(): void
    {
        foreach (Environments::sources() as $id => $source) {
            if ($source === Environments::MADE) {
                continue;
            }

            $reason = Environments::reason($id);
            self::assertNotSame('Nothing here makes it.', $reason, $id . ' declines without saying where it comes from');
            self::assertNotSame('', trim($reason), $id . ' declines with nothing at all');
        }
    }

    /**
     * The installation is built at the version this server answers for. A
     * repository that starts covering a new stable major and keeps making
     * environments of the old one measures itself against the wrong TYPO3, and
     * nothing about the environment would say so — `D-EVI-006`, `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Decision('D-EVI-006')]
    #[Test]
    public function theInstallationIsBuiltAtTheCoveredStableVersion(): void
    {
        $stable = array_values(array_filter(
            Versions::covered(),
            static fn(array $version): bool => $version['status'] === 'stable',
        ));

        self::assertCount(1, $stable, 'knowledge/versions.json covers no single stable version');
        self::assertSame($stable[0]['branch'], Environments::branch());
        self::assertStringContainsString(
            Environments::DISTRIBUTION . ':^' . $stable[0]['branch'],
            implode(' ', array_merge(...array_values(Environments::build(Environments::branch())))),
        );
    }

    /**
     * One installation runs one version, so what a client on another covered
     * line would be answered is shown by an installation of that line or by
     * nothing. `SITE-02` names `E-SITE` on the previous major, which is the
     * case a single installation cannot be run.
     *
     * Every covered line, the development one included: it was the line whose
     * answers about the next major nothing here could show, and a version
     * `versions.json` covers while `create` declines is the same gap under a
     * different name — `D-EVI-006`.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function everyCoveredLineIsOneAnInstallationIsMadeOf(): void
    {
        $made = Environments::branches();

        self::assertContains(Environments::branch(), $made, 'the covered stable line is not made');
        foreach (Versions::covered() as $version) {
            self::assertContains(
                $version['branch'],
                $made,
                $version['branch'] . ' is covered and is no line an installation is made of',
            );
        }
    }

    /**
     * A version argument is a thing somebody types wrong, and what the mistake
     * needs back is which versions there are. Nothing covered declines any
     * more, so a refusal naming a covered line is this drifting apart from
     * `knowledge/versions.json` — `D-EVI-006`.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function aVersionNoInstallationIsMadeOfSaysWhy(): void
    {
        foreach (Versions::covered() as $version) {
            self::assertSame(
                '',
                Environments::refusal($version['branch']),
                $version['branch'] . ' is covered and still declines',
            );
        }

        self::assertStringContainsString('14.9', Environments::refusal('14.9'), 'a version nothing covers is not named back');
        self::assertStringContainsString(
            implode(', ', Environments::branches()),
            Environments::refusal('14.9'),
            'a refusal does not say which versions there are instead',
        );
    }

    /**
     * The development line is a different build rather than a different
     * version argument, and every part of that difference is here: the
     * distribution has no release above the newest stable so it comes from
     * `dev-main`, whose twenty-four `dev-main` requires need a minimum
     * stability the default refuses, and the core there declares PHP `^8.5`
     * where the released lines are pinned to 8.4.
     *
     * Asserted against the built command rather than against the constants,
     * because a step that dropped one of the three comes out looking finished:
     * the stability flag missing is a resolver error, but the PHP pin missing
     * is an installation that builds and then answers as the wrong PHP —
     * `D-EVI-006`.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function theDevelopmentLineIsBuiltFromDevMainOnThePhpItsCoreDeclares(): void
    {
        $development = array_values(array_filter(
            Versions::covered(),
            static fn(array $version): bool => $version['status'] === 'development',
        ));

        self::assertCount(1, $development, 'knowledge/versions.json covers no single development line');
        $branch = $development[0]['branch'];

        self::assertTrue(Environments::development($branch));
        self::assertSame(Environments::DEVELOPMENT_PHP, Environments::php($branch));
        self::assertNotSame(Environments::PHP, Environments::php($branch));

        $build = Environments::build($branch);
        $flat = implode(' ', array_merge(...array_values($build)));

        self::assertStringContainsString('--php-version=' . Environments::DEVELOPMENT_PHP, $flat);
        self::assertStringContainsString(Environments::DISTRIBUTION . ':dev-main', $flat);
        self::assertStringContainsString('--stability=dev', $flat);
        foreach (Environments::REQUIRED as $package) {
            self::assertStringContainsString('require ' . $package . ':dev-main', $flat, $package . ' is required at a release');
        }

        // Every released line keeps the pin and the caret, which is the half
        // this could break without failing anywhere else.
        foreach (Environments::branches() as $released) {
            if ($released === $branch) {
                continue;
            }

            $other = implode(' ', array_merge(...array_values(Environments::build($released))));
            self::assertStringContainsString('--php-version=' . Environments::PHP, $other, $released);
            self::assertStringContainsString(Environments::DISTRIBUTION . ':^' . $released, $other, $released);
            self::assertStringNotContainsString('--stability', $other, $released . ' is built at a stability of its own');
        }
    }

    /**
     * A build nobody asks a database of is on sqlite, and starts no database
     * container. The two halves are one fact and have to move together: an
     * installation left on a database driver with `--omit-containers=db` in
     * front of it builds its containers, installs a hundred packages and dies
     * at the setup step against a service that was never started.
     */
    #[Test]
    public function everyLineIsSetUpOnAFile(): void
    {
        self::assertSame('sqlite', Environments::DEFAULT_DRIVER);

        foreach (Environments::branches() as $branch) {
            $build = implode(' ', array_merge(...array_values(Environments::build($branch))));

            self::assertStringContainsString('--driver=sqlite', $build, $branch);
            self::assertStringContainsString('--omit-containers=db', $build, $branch . ' starts a database it does not use');
            foreach (['--host=db', '--port=3306', '--dbname=db', '--username=db', '--password=db'] as $option) {
                self::assertStringNotContainsString(
                    $option,
                    $build,
                    $branch . ' passes ' . $option . ' to a setup that takes none',
                );
            }
        }
    }

    /**
     * Every database an installation can be made on, and the values the two
     * tools take for it.
     *
     * The tools disagree on every name, which is what this holds: `ddev config
     * --database` takes a `type:version` and refuses a bare type, while
     * `vendor/bin/typo3 setup --driver` takes a connection type out of
     * `SetupCommand::$connectionLabels` — `mysqli` for both MySQL lines,
     * `postgres` for PostgreSQL — and not the DBAL driver it resolves to. A
     * table that passed one where the other belongs configures cleanly and
     * fails minutes later, at the step that has already installed a hundred
     * packages.
     *
     * The connection values are DDEV's own, measured on 2026-08-04 against
     * v1.25.1 by building a project on each and reading it back: host `db`,
     * database `db`, user `db`, password `db`, and only the port moves —
     * `D-EVI-006`.
     *
     * @param array<int, string> $expected
     */
    #[Decision('D-EVI-006')]
    #[Test]
    #[DataProvider('everyDatabaseAnInstallationCanBeMadeOn')]
    public function eachDriverPassesTheValuesItsOwnToolsTake(
        string $driver,
        string $configured,
        array $expected,
    ): void {
        $build = Environments::build(Environments::branch(), $driver);
        $configure = $build[array_key_first($build)];
        $setup = [];
        foreach ($build as $command) {
            if (in_array('setup', $command, true)) {
                $setup = $command;
            }
        }

        self::assertContains($configured, $configure, $driver . ' configures the container for another database');
        foreach ($expected as $option) {
            self::assertContains($option, $setup, $driver . ' does not pass ' . $option . ' to the setup');
        }
    }

    /** @return array<string, array{0: string, 1: string, 2: array<int, string>}> */
    public static function everyDatabaseAnInstallationCanBeMadeOn(): array
    {
        $connection = ['--host=db', '--dbname=db', '--username=db', '--password=db'];

        return [
            // No connection options at all: `sqliteManualConfigurationOptions`
            // carries the driver alone, and there is no service for a host to
            // name.
            'sqlite is a file, so no container is started' => [
                'sqlite',
                '--omit-containers=db',
                ['--driver=sqlite'],
            ],
            'mariadb, on the newest version 12.4 also accepts' => [
                'mariadb',
                '--database=mariadb:11.4',
                ['--driver=mysqli', '--port=3306', ...$connection],
            ],
            'mysql, whose connection type is the same one' => [
                'mysql',
                '--database=mysql:8.4',
                ['--driver=mysqli', '--port=3306', ...$connection],
            ],
            'postgres, the one service driver every line can be built on today' => [
                'postgres',
                '--database=postgres:16',
                ['--driver=postgres', '--port=5432', ...$connection],
            ],
        ];
    }

    /**
     * Every driver this offers is one both tools have, and a name neither of
     * them takes is refused rather than configured.
     *
     * The version half is what this is written against. `ddev config` accepts
     * `--database=mariadb:99.9` and writes it, and `ddev start` is where it
     * fails — measured on 2026-08-04 against v1.25.1 — so a version that is
     * wrong here costs the whole configure step before it says so —
     * `D-EVI-006`.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function aDatabaseNothingIsMadeOnIsRefusedWithTheOnesThereAre(): void
    {
        self::assertContains(Environments::DEFAULT_DRIVER, Environments::drivers());

        foreach (Environments::drivers() as $driver) {
            $database = Environments::driver($driver);
            self::assertNotSame('', $database['setup'], $driver . ' names no connection type');
            if ($database['ddev'] === null) {
                self::assertNull($database['port'], $driver . ' has no container and a port to reach it on');

                continue;
            }
            self::assertMatchesRegularExpression(
                '/^(mariadb|mysql|postgres):\d/',
                $database['ddev'],
                $driver . ' is not a type and a version DDEV takes',
            );
            self::assertIsInt($database['port']);
        }

        $this->expectExceptionMessage('no installation is made on "mssql"');
        Environments::driver('mssql');
    }

    /**
     * Each installation is registered under a name of its own, and lives in a
     * directory of its own. One name for all of them is one installation for
     * all of them, which is the state `D-EVI-006` was written against.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function eachCoveredLineIsItsOwnProjectAndItsOwnDirectory(): void
    {
        $projects = array_map(Environments::project(...), Environments::branches());
        $paths = array_map(
            static fn(string $branch): string => Environments::path('E-SITE', $branch),
            Environments::branches(),
        );

        self::assertSame($projects, array_unique($projects), 'two covered lines share one DDEV project name');
        self::assertSame($paths, array_unique($paths), 'two covered lines share one directory');
        foreach (Environments::branches() as $branch) {
            self::assertSame(
                Environments::project($branch),
                str_replace('.', '-', 'typo3-mcp-e-site-' . $branch),
                'a DDEV project name carries a character DDEV does not take',
            );
        }
    }

    /**
     * An installation on a second database is a second installation, so it is
     * its own DDEV project and its own directory — and the default one keeps
     * the name it has.
     *
     * Both halves matter. Without the first, the MariaDB 13.4 and the sqlite
     * 13.4 are one project, which is the state `D-EVI-006` was written against
     * one version earlier. Without the second, every environment on this
     * machine and every path `todo/reference/` names is renamed to say what
     * asking for nothing already means.
     */
    #[Decision('D-EVI-006')]
    #[Test]
    public function anInstallationOnASecondDatabaseIsItsOwnProject(): void
    {
        $branch = Environments::branch();

        self::assertSame(Environments::project($branch), Environments::project($branch, 'sqlite'));
        self::assertSame(Environments::path('E-SITE', $branch), Environments::path('E-SITE', $branch, 'sqlite'));

        $names = [];
        $paths = [];
        foreach (Environments::drivers() as $driver) {
            $names[] = Environments::project($branch, $driver);
            $paths[] = Environments::path('E-SITE', $branch, $driver);
        }

        self::assertSame($names, array_unique($names), 'two databases of one line share a DDEV project name');
        self::assertSame($paths, array_unique($paths), 'two databases of one line share a directory');
        foreach ($names as $name) {
            self::assertMatchesRegularExpression(
                '/^[a-z0-9-]+$/',
                $name,
                'a DDEV project name carries a character DDEV does not take',
            );
        }
    }

    /**
     * A build is minutes and a hundred packages and the containers are seconds.
     * An environment that is kept between runs is only kept if asking for it
     * again starts what is there — including out of the pause DDEV puts an idle
     * project into by itself — `D-EVI-006`.
     */
    /** @param array<int, string>|null $expected */
    #[Decision('D-EVI-006')]
    #[Test]
    #[DataProvider('everyStateDdevReportsAProjectIn')]
    public function anInstallationThatIsThereIsStarted(?string $status, ?array $expected): void
    {
        self::assertSame($expected, Environments::resume($status));
    }

    /** @return array<string, array{0: ?string, 1: ?array<int, string>}> */
    public static function everyStateDdevReportsAProjectIn(): array
    {
        $start = ['ddev', 'start', '-y'];

        return [
            'running, so nothing is started a second time' => ['running', null],
            'stopped' => ['stopped', $start],
            'the pause DDEV puts an idle project into by itself' => ['paused', $start],
            'not registered on this machine at all' => [null, $start],
        ];
    }

    /**
     * `scenarios/readme.md` says an `E-SITE` is an installation whose console
     * has `language:domain:search`, and the base distribution does not require
     * the extension that carries it. A build that dropped this step would come
     * out looking finished and answer nothing the label lookup asks —
     * `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function theBuildRequiresTheExtensionsThisServerAsksFor(): void
    {
        $build = implode(' ', array_merge(...array_values(Environments::build(Environments::branch()))));

        self::assertNotSame([], Environments::REQUIRED);
        foreach (Environments::REQUIRED as $package) {
            self::assertStringContainsString('require ' . $package . ':', $build, $package . ' is never required');
        }
    }

    /**
     * The project's own extension is required out of `packages/`, its table is
     * created and then filled, in that order.
     *
     * A base distribution owns no package, so an installation made here
     * answered nothing about what this project registers and the one call
     * `typo3_record_lookup` exists for could be recorded nowhere —
     * `D-EVI-010`.
     */
    #[Decision('D-EVI-010')]
    #[Test]
    public function theProjectsOwnExtensionIsRequiredThenSetUpThenFilled(): void
    {
        $steps = array_values(Environments::ownExtension());

        self::assertCount(3, $steps, 'a step of the extension phase went missing or was added silently');
        self::assertSame(
            ['ddev', 'composer', 'require', SiteExtension::PACKAGE . ':*', '--no-interaction'],
            $steps[0],
        );
        self::assertContains('extension:setup', $steps[1], 'the table is never created');
        self::assertContains(SiteExtension::SEED, $steps[2], 'the table is never filled');
        foreach (Environments::ownExtension() as $what => $command) {
            self::assertNotSame('', trim((string) $what), 'a step says nothing about itself');
            self::assertSame('ddev', $command[0] ?? '', $what . ' runs outside the project');
        }
    }

    /**
     * The setup step ran non-interactively and died on a `TypeError` until
     * `--server-type` was passed: 14.3.5 reads that option's default through
     * the same fallback as its environment variable, hands the validator
     * `false` where neither is set, and only asks the question where a person
     * is there to answer it. Nothing about the option's own definition says so
     * — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function theSetupStepPassesEveryOptionItCannotBeAskedFor(): void
    {
        $setup = [];
        foreach (Environments::build(Environments::branch()) as $command) {
            if (in_array('setup', $command, true)) {
                $setup = $command;
            }
        }

        self::assertNotSame([], $setup, 'nothing in the build sets the installation up');
        self::assertContains('--no-interaction', $setup);
        self::assertContains('--server-type=other', $setup);
        // `--host=` and `--dbname=` were here while the installation was in a
        // database service. On sqlite the setup asks for neither, and
        // `everyLineIsSetUpOnAFile` is what
        // holds them out.
        foreach (['--driver=', '--admin-username=', '--admin-user-password=', '--create-site='] as $option) {
            self::assertNotSame(
                [],
                array_filter($setup, static fn(string $argument): bool => str_starts_with($argument, $option)),
                $setup === [] ? '' : $option . ' is left for a person to be asked for',
            );
        }
    }

    /**
     * The site the installation is created for is the one DDEV routes to it. A
     * base URL naming another project is a frontend that answers nothing, and
     * every case that opens a page in it fails for a reason none of them is
     * about.
     *
     * Every driver, because the name is where the second database was dropped:
     * a build that asked DDEV for the default project name in a directory of
     * its own was refused by `ddev config` for a project root it does not own,
     * minutes before the installation it was after — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function theSiteIsCreatedForTheAddressDdevGivesTheProject(): void
    {
        foreach (Environments::drivers() as $driver) {
            $project = Environments::project(Environments::branch(), $driver);
            $created = [];
            foreach (Environments::build(Environments::branch(), $driver) as $command) {
                foreach ($command as $argument) {
                    if (str_starts_with($argument, '--create-site=')) {
                        $created[] = $argument;
                    }
                    if (str_starts_with($argument, '--project-name=') && in_array('config', $command, true)) {
                        self::assertSame('--project-name=' . $project, $argument, $driver);
                    }
                }
            }

            self::assertSame(['--create-site=https://' . $project . '.ddev.site/'], $created, $driver);
        }
    }

    /**
     * Every step is `ddev`, which is what keeps the build inside the containers
     * it declares. A `composer` or a `php` among them would run on whatever the
     * machine happens to have, and the version the environment is of would stop
     * being the one it was asked for — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function everyStepOfTheBuildRunsInTheProject(): void
    {
        foreach (Environments::build(Environments::branch()) as $what => $command) {
            self::assertNotSame('', trim((string) $what), 'a step of the build says nothing about itself');
            self::assertSame('ddev', $command[0] ?? '', $what . ' runs outside the project');
        }
    }

    /**
     * The project name is global to the machine and the directory is per
     * checkout, so a worktree that made an environment and was then removed
     * leaves the name held for an approot that is gone. Measured on 2026-08-02:
     * `typo3-mcp-e-site` registered at a `.worktrees/` path DDEV itself
     * reported as `project directory missing`, and `environment:create`
     * refusing in the name of a checkout nobody could visit — `D-EVI-005`.
     */
    #[Decision('D-EVI-005')]
    #[Test]
    public function aRegistrationWhoseCheckoutIsGoneHoldsNothingBack(): void
    {
        self::assertTrue(Environments::abandoned([
            'name' => Environments::project(Environments::branch()),
            'status' => 'project directory missing',
            'approot' => Paths::root() . '/.worktrees/a-checkout-that-was-removed/.environments/e-site',
            'url' => '',
        ]), 'an approot that is gone is read as a checkout still holding the name');

        self::assertFalse(Environments::abandoned([
            'name' => Environments::project(Environments::branch()),
            'status' => 'running',
            'approot' => Paths::root(),
            'url' => '',
        ]), 'a checkout that is there would have its environment taken over');
    }

    /**
     * `ddev stop --unlist` frees the name and is the wrong command. Stop is
     * documented as non-destructive, and the database is a volume named after
     * the project rather than after the directory — so the next build under the
     * same name attaches to it and the setup step meets the tables the last
     * installation left. `--force` does not reach that: it forces the settings
     * file alone. `delete` takes the volume with the name — `D-EVI-005`.
     */
    #[Decision('D-EVI-005')]
    #[Test]
    public function clearingARegistrationTakesTheDatabaseThatWouldOutliveIt(): void
    {
        $discard = Environments::discard(Environments::project(Environments::branch()));

        self::assertSame('ddev', $discard[0] ?? '', 'the registration is cleared outside the project');
        self::assertSame('delete', $discard[1] ?? '', 'stop leaves the database the next build fails on');
        self::assertNotContains('--unlist', $discard, 'unlisting frees the name and keeps the database');
        self::assertContains('--omit-snapshot', $discard);
        self::assertContains(Environments::project(Environments::branch()), $discard);
    }

    /**
     * A made environment is a TYPO3 installation and its database dump, and it
     * belongs in a commit as little as `.checkouts/` does. This is the one
     * failure here that is unrecoverable rather than annoying — `D-EVI-004`.
     */
    #[Decision('D-EVI-004')]
    #[Test]
    public function whatIsMadeHereIsNeverCommitted(): void
    {
        $ignored = preg_split('/\R/', (string) file_get_contents(Paths::root() . '/.gitignore')) ?: [];

        self::assertContains('/.environments', array_map(trim(...), $ignored));
        self::assertStringStartsWith(
            Environments::directory() . '/',
            Environments::path('E-SITE', Environments::branch()),
        );
    }

    #[Test]
    public function theGeneratedDatabaseBlockIsTakenOutAndTheRestIsKept(): void
    {
        // What DDEV writes for a project running omit_containers: [db]. The
        // block names a container that is not there, and it is merged over the
        // connection settings.php carries, so the installation talks to
        // nothing and the backend refuses every login without saying why.
        $path = sys_get_temp_dir() . '/typo3-mcp-environment-' . bin2hex(random_bytes(6));
        mkdir($path . '/config/system', 0777, true);
        file_put_contents($path . '/config/system/additional.php', <<<'PHP'
            <?php

            /**
             * #ddev-generated: Automatically generated TYPO3 additional.php file.
             */

            if (getenv('IS_DDEV_PROJECT') == 'true') {
                $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive(
                    $GLOBALS['TYPO3_CONF_VARS'],
                    [
                        'DB' => [
                            'Connections' => [
                                'Default' => [
                                    'driver' => 'mysqli',
                                    'host' => 'db',
                                ],
                            ],
                        ],
                        'GFX' => [
                            'processor' => 'ImageMagick',
                        ],
                        'SYS' => [
                            'trustedHostsPattern' => '.*.*',
                        ],
                    ]
                );
            }
            PHP);

        $said = Environments::takeOverGeneratedSettings($path);
        $written = (string) file_get_contents($path . '/config/system/additional.php');

        self::assertNotNull($said);
        self::assertStringNotContainsString("'DB' =>", $written);
        // The whole block, not its first line. A pattern that ends at the
        // first `],` in the file stops two levels in and leaves brackets
        // behind that do not parse — which is what happened on 2026-08-04, and
        // what the assertions below this one did not see.
        self::assertStringNotContainsString('Connections', $written);
        self::assertSame(
            substr_count($written, '['),
            substr_count($written, ']'),
            'the file it left behind does not parse',
        );
        self::assertStringNotContainsString('#ddev-generated', $written, 'DDEV owns it and regenerates the block');
        // The three sections DDEV knows and this does not: without the trusted
        // hosts pattern TYPO3 refuses the host name the router forwards.
        self::assertStringContainsString("'GFX' =>", $written);
        self::assertStringContainsString('trustedHostsPattern', $written);
        self::assertNull(Environments::takeOverGeneratedSettings($path), 'a file already taken over is left alone');

        unlink($path . '/config/system/additional.php');
        rmdir($path . '/config/system');
        rmdir($path . '/config');
        rmdir($path);
    }
}
