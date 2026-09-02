<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;

/**
 * The environments a scenario is run in, and which of them this checkout makes
 * for itself.
 *
 * Where what a run needs from the directory is a property this repository can
 * state — a Composer installation under DDEV, on a covered version, whose
 * console answers — it is made here below `.environments/`, gitignored and
 * re-creatable the way `.checkouts/` is. Where it is a property of somebody
 * else's repository it stays declared, because a scaffold of it would be this
 * repository grading itself against its own idea of the thing — `D-EVI-004`. So
 * each id carries how it is come by, and asking for one that is not made here
 * answers with the reason rather than with a directory.
 */
final class Environments
{
    /** Made here, by `bin/cli environment:create`. */
    public const MADE = 'made';

    /** Named in `todo/reference/`, because no scaffold produces what it plays. */
    public const DECLARED = 'declared';

    /** Made here, by a command of its own. */
    public const ELSEWHERE = 'elsewhere';

    /** Not a directory at all: a state the environment above it is put into. */
    public const STATE = 'state';

    /** What `knowledge/versions.json` calls the line that has no release. */
    private const DEVELOPMENT = 'development';

    /** What DDEV writes into a file it owns and regenerates. */
    private const DDEV_MARKER = '#ddev-generated';

    /**
     * What DDEV registers a created project under, one name per covered line.
     *
     * The name is global to the machine — `ddev list` is one namespace — while
     * the directory is per checkout, so two checkouts asking for the same
     * environment ask for one name, which is refused rather than taken over. The
     * version and a database that is not the default are in the name because
     * they are in the installation — `D-EVI-006`.
     */
    public static function project(string $branch, string $driver = self::DEFAULT_DRIVER): string
    {
        return 'typo3-mcp-e-site-' . str_replace('.', '-', $branch) . self::suffix($driver);
    }

    /**
     * What a driver adds to the name and the directory, which is nothing for
     * the default one.
     */
    private static function suffix(string $driver): string
    {
        self::driver($driver);

        return $driver === self::DEFAULT_DRIVER ? '' : '-' . $driver;
    }

    /**
     * The password the created installation's admin user gets.
     *
     * Written down rather than generated: the environment exists to be logged
     * into by whoever runs a scenario in it, and a secret nobody can read is a
     * step back to the machine this is replacing. It guards a throwaway site on
     * `*.ddev.site`, reachable from the machine that made it.
     */
    public const ADMIN_PASSWORD = 'Environment.Created.Here.1';

    /**
     * TYPO3's own starting site, which is what the installation is built from.
     *
     * Not a `composer.json` written here. The shape of a site installation is
     * TYPO3's to decide and it moves with the major; a copy of it in this
     * repository would be one more thing to keep true, and wrong in the way
     * that is hardest to see — plausibly out of date.
     */
    public const DISTRIBUTION = 'typo3/cms-base-distribution';

    /**
     * What the base distribution does not bring and this server asks for.
     *
     * `typo3_label_lookup` runs `language:domain:search` and the scope tools
     * run `configuration:show`; both are `EXT:lowlevel` commands, and the base
     * distribution does not require it. Without this the environment is a site
     * whose console answers "There are no commands defined in the
     * language:domain namespace", which is the one thing `scenarios/readme.md`
     * says an `E-SITE` has to have.
     *
     * @var array<int, string>
     */
    public const REQUIRED = ['typo3/cms-lowlevel'];

    /**
     * The PHP the containers run, on every released line an installation is
     * made of.
     *
     * Pinned rather than left to DDEV, which defaults to whatever is current
     * when it is installed. One pin covers every released covered line: each
     * one's own `Build/Scripts/runTests.sh` runs this version — 12.4 accepts
     * `8.1` to `8.5`, 13.4 and 14.3 accept `8.2` to `8.5`, read in
     * `.checkouts/` on 2026-08-03.
     */
    public const PHP = '8.4';

    /**
     * The PHP the development line's containers run, which is not that one.
     *
     * `.checkouts/main/composer.json` declares `"php": "^8.5"` and a platform
     * of `8.5.0`, so the released pin does not install that line at all — the
     * constraint is refused before anything is downloaded. DDEV 1.25.1 offers
     * `5.6` through `8.5`, so the version is there to ask for; both read on
     * 2026-08-03.
     *
     * Two pins rather than one at 8.5 for every line: what a released line's
     * environment is for is answering the way an installation of it answers,
     * and its own test script runs 8.4.
     */
    public const DEVELOPMENT_PHP = '8.5';

    /**
     * How each environment in `scenarios/readme.md` is come by.
     *
     * The ids are not repeated here — `EnvironmentsTest` holds these keys to
     * the table that defines them, so an environment added there and forgotten
     * here is a failure rather than a `null`.
     *
     * @return array<string, string>
     */
    public static function sources(): array
    {
        return [
            'E-CORE' => self::ELSEWHERE,
            'E-SITE' => self::MADE,
            'E-EXT' => self::DECLARED,
            'E-NONE' => self::MADE,
            'E-STOPPED' => self::STATE,
        ];
    }

    /**
     * Every environment id, as `scenarios/readme.md` defines them.
     *
     * @return array<int, string>
     */
    public static function ids(): array
    {
        return Scenarios::vocabulary('Id');
    }

    /**
     * Why an environment that is not made here is not, in the words somebody
     * asking for it needs — which is where to get it instead.
     */
    public static function reason(string $id): string
    {
        return match ($id) {
            'E-CORE' => 'A core checkout is `bin/cli checkouts:update`, which puts one worktree per' . "\n"
                . 'covered branch below `.checkouts/`. Those are read rather than run: nothing'
                . "\n" . 'installs their dependencies, so a case needing a booted core still needs a'
                . "\n" . 'checkout of its own.',
            'E-EXT' => 'An extension repository stays declared. What a run needs from one is its real'
                . "\n" . 'infrastructure at a real revision — complete in one, incomplete in another, a'
                . "\n" . 'major behind in the third — and a scaffold would supply this repository\'s own'
                . "\n" . 'idea of all three. `todo/reference/` names the checkouts that play it.',
            'E-STOPPED' => sprintf(
                "Not a directory of its own: it is `E-SITE` with its DDEV project down. Make\n"
                . "that one, then stop it.\n\n    ddev stop %s",
                self::project(self::branch()),
            ),
            default => 'Nothing here makes it.',
        };
    }

    /** Where a made environment lives, below the checkout and ignored by git. */
    public static function directory(): string
    {
        return Paths::root() . '/.environments';
    }

    /**
     * Where one environment lives, whether or not it is there yet.
     *
     * An `E-SITE` is one directory per covered line and per database, because
     * it is one installation per covered line and per database. `E-NONE` has
     * neither to be of.
     */
    public static function path(string $id, ?string $branch = null, string $driver = self::DEFAULT_DRIVER): string
    {
        return self::directory() . '/' . strtolower($id)
            . ($branch === null ? '' : '-' . $branch . self::suffix($driver));
    }

    /** Whether the installation of a covered line is there to be run in. */
    public static function installed(string $branch, string $driver = self::DEFAULT_DRIVER): bool
    {
        return is_file(self::path('E-SITE', $branch, $driver) . '/config/system/settings.php');
    }

    /**
     * The line a made installation is built on where nobody says which: the
     * covered version that is stable.
     *
     * Read off `knowledge/versions.json` rather than written down, because a
     * run that names no version validates against the version this server
     * answers for. A repository that starts covering a new stable and keeps
     * making installations of the old one measures itself in the wrong place.
     */
    public static function branch(): string
    {
        foreach (Versions::covered() as $version) {
            if ($version['status'] === 'stable') {
                return $version['branch'];
            }
        }

        throw new \RuntimeException('knowledge/versions.json covers no stable version to build an installation of');
    }

    /**
     * Every covered line an installation is made of, oldest first.
     *
     * One installation runs one version, so a client asking about another
     * covered line is answered by nothing this repository has — `SITE-02` is the
     * case that says so out loud, and it is the reason this is a list rather
     * than `branch()`. Every covered line, the development one included, which
     * is a judgement about what this repository is for — `D-EVI-006`.
     *
     * @return array<int, string>
     */
    public static function branches(): array
    {
        $branches = [];
        foreach (Versions::covered() as $version) {
            $branches[] = $version['branch'];
        }

        return $branches;
    }

    /** Whether a covered line is the one that has no release. */
    public static function development(string $branch): bool
    {
        foreach (Versions::covered() as $version) {
            if ($version['branch'] === $branch) {
                return $version['status'] === self::DEVELOPMENT;
            }
        }

        return false;
    }

    /** The PHP the containers of one line run. */
    public static function php(string $branch): string
    {
        return self::development($branch) ? self::DEVELOPMENT_PHP : self::PHP;
    }

    /**
     * The database an installation is set up against where nobody asks for
     * another.
     *
     * sqlite is a file below `var/sqlite/` in the project directory, so an
     * environment is its directory and nothing else and `rm -rf` is the whole of
     * taking one away — which is what `--omit-containers=db` in the build pays
     * for. What it says nothing about is what a database server does under the
     * same schema, so it is a default rather than the only one — `D-EVI-006`.
     */
    public const DEFAULT_DRIVER = 'sqlite';

    /**
     * The databases an installation can be made on, and what each is called by
     * the two tools that have to agree about it.
     *
     * They disagree on every name: `ddev config --database` takes a
     * `type:version` and checks the version at `ddev start` rather than at
     * `config`, so a wrong one configures cleanly and fails minutes later, while
     * `setup --driver` takes a connection type that is not the DBAL driver it
     * resolves to. `port` is `null` where there is no service to connect to. The
     * versions and the connection values are `D-EVI-006`.
     *
     * @var array<string, array{setup: string, ddev: ?string, port: ?int}>
     */
    private const DRIVERS = [
        'sqlite' => ['setup' => 'sqlite', 'ddev' => null, 'port' => null],
        'mariadb' => ['setup' => 'mysqli', 'ddev' => 'mariadb:11.4', 'port' => 3306],
        'mysql' => ['setup' => 'mysqli', 'ddev' => 'mysql:8.4', 'port' => 3306],
        'postgres' => ['setup' => 'postgres', 'ddev' => 'postgres:16', 'port' => 5432],
    ];

    /**
     * What the connection is, on either database service.
     *
     * One set of values for both, because DDEV gives every project the same
     * ones and only the port moves. Written down rather than read out of the
     * container, because the build has to pass them to a `setup` that runs
     * before there is a project to ask.
     */
    private const SERVICE_HOST = 'db';
    private const SERVICE_DATABASE = 'db';
    private const SERVICE_USER = 'db';
    private const SERVICE_PASSWORD = 'db';

    /**
     * The databases an installation can be asked for, for the command's own
     * help and for whoever typed one that is not among them.
     *
     * @return array<int, string>
     */
    public static function drivers(): array
    {
        return array_keys(self::DRIVERS);
    }

    /**
     * The one a name stands for, or a refusal naming the ones there are.
     *
     * @return array{setup: string, ddev: ?string, port: ?int}
     */
    public static function driver(string $driver): array
    {
        if (!isset(self::DRIVERS[$driver])) {
            throw new \RuntimeException(
                'no installation is made on "' . $driver . '"; there is ' . implode(', ', self::drivers()),
            );
        }

        return self::DRIVERS[$driver];
    }

    /**
     * Takes DDEV's generated `additional.php` over where its database block is
     * wrong, and says what it did.
     *
     * DDEV writes that block from its own database container and has no variant
     * that reads the driver the installation was set up with. An installation
     * on SQLite runs with `omit_containers: [db]`, so the block points `mysqli`
     * at a host that does not exist — and because it is merged over the
     * connection `settings.php` carries, the installation talks to nothing. The
     * backend then answers "your login attempt did not succeed" to correct
     * credentials, which names neither the file nor the container.
     *
     * The block goes and the rest stays. GFX, MAIL and SYS are what DDEV knows
     * and this does not: the ImageMagick in that container, its mail catcher,
     * and the trusted hosts pattern without which TYPO3 refuses the host name
     * the router forwards. Disabling settings management would end the same
     * collision and leave all three to be written by hand.
     *
     * The marker goes with it, because a file DDEV still owns is regenerated on
     * the next start and the block comes back.
     */
    public static function takeOverGeneratedSettings(string $path): ?string
    {
        $file = $path . '/config/system/additional.php';
        if (!is_file($file)) {
            return null;
        }

        $before = (string) file_get_contents($file);
        if (!str_contains($before, self::DDEV_MARKER)) {
            return null;
        }

        // The closing bracket is matched at the indentation the block opened
        // at. Without that backreference the first `],` in the file ends the
        // match, which is two levels in, and what is left behind does not
        // parse — measured on 2026-08-04, on two environments this repaired.
        $after = preg_replace("/^(\h*)'DB' => \[\R(?:.*\R)*?\\1\],\R/m", '', $before, 1);
        if ($after === null || $after === $before) {
            return null;
        }

        $after = str_replace(
            self::DDEV_MARKER,
            'Taken over by bin/cli environment:create: the generated DB block named a'
                . "\n * database container this project does not run.",
            $after,
        );
        file_put_contents($file, $after);

        return 'config/system/additional.php: the generated DB block is gone and DDEV no longer owns the file';
    }

    /**
     * What Composer is asked for on one line, of the distribution and of every
     * package required beside it.
     *
     * `typo3/cms-base-distribution` publishes no release above the newest
     * stable — `v14.3.0` is its top tag — so the development line is its
     * `dev-main`, which requires `dev-main` of all twenty-four core packages.
     * Read from packagist on 2026-08-03.
     */
    public static function constraint(string $branch): string
    {
        return self::development($branch) ? 'dev-main' : '^' . $branch;
    }

    /**
     * Why no installation is made of a version, and an empty string where one
     * is.
     *
     * Every covered line is made now, so what is left to decline is a version
     * this server does not cover — which is a thing somebody types wrong, and
     * the answer it needs is which ones there are.
     */
    public static function refusal(string $branch): string
    {
        if (in_array($branch, self::branches(), true)) {
            return '';
        }

        return sprintf(
            "%s is no version this server covers. `knowledge/versions.json` says which,\n"
            . 'and an installation is made of these: %s',
            $branch,
            implode(', ', self::branches()),
        );
    }

    /**
     * What `create` does with an installation that is already there.
     *
     * Never the build again, and `ddev start` where the containers are down —
     * which is every state but `running`, the pause DDEV puts an idle project
     * into included. A registration that is gone is the same case: `start` in
     * the directory registers it back. An installation is minutes and a
     * hundred packages; the containers are seconds.
     *
     * @return list<string>|null
     */
    public static function resume(?string $status): ?array
    {
        return $status === 'running' ? null : ['ddev', 'start', '-y'];
    }

    /**
     * Every DDEV project this machine knows, by name.
     *
     * `ddev list` is the one place a project registered somewhere else is
     * visible, and both commands here need it: one to refuse a name that is
     * taken, the other to say whether the environment it made is up.
     *
     * @return array<string, array{name: string, status: string, approot: string, url: string}>
     */
    public static function projects(): array
    {
        [$exitCode, $said] = self::run(['ddev', 'list', '--json-output']);
        if ($exitCode !== 0) {
            return [];
        }

        // `--json-output` is one JSON document per line and more than one of
        // them: the human table is a `msg` on the error stream, the projects are
        // a `raw` on the standard one, and both arrive here as one string.
        // Decoding from the first brace therefore reads whichever came first
        // and fails on the rest, so the lines are taken one at a time.
        $listed = null;
        foreach (preg_split('/\R/', $said) ?: [] as $line) {
            $decoded = json_decode(trim($line), true);
            if (is_array($decoded) && is_array($decoded['raw'] ?? null)) {
                $listed = $decoded['raw'];
                break;
            }
        }

        if ($listed === null) {
            return [];
        }

        $projects = [];
        foreach ($listed as $project) {
            if (!is_array($project) || !is_string($project['name'] ?? null)) {
                continue;
            }
            $projects[$project['name']] = [
                'name' => $project['name'],
                'status' => (string) ($project['status'] ?? ''),
                'approot' => (string) ($project['approot'] ?? ''),
                'url' => (string) ($project['primary_url'] ?? ''),
            ];
        }

        return $projects;
    }

    /**
     * Whether a registration points at a checkout that is no longer there.
     *
     * The project name is global to this machine and the directory is per
     * checkout, so a worktree that made an environment and was then removed
     * leaves the name held by an approot DDEV itself reports as `project
     * directory missing`. Nothing can reach what it names: the code, the
     * settings file and the DDEV config went with the directory, and what is
     * left is a name and a database volume. An `rm -rf .environments` in this
     * checkout leaves the same thing behind, which is why the question is
     * whether the approot is there rather than whose it was.
     *
     * @param array{name: string, status: string, approot: string, url: string} $project
     */
    public static function abandoned(array $project): bool
    {
        return !is_dir($project['approot']);
    }

    /**
     * What clears a registration nothing can reach, and whatever DDEV named
     * after the project with it.
     *
     * `ddev stop --unlist` is the smaller command and the wrong one: it frees
     * the name and leaves the volumes, which are named after the project
     * rather than after the directory, so the next build registers the name
     * again and attaches to them. That mattered most while the installation
     * was in a database volume — measured on 2026-08-02 against a registration
     * whose approot had been removed, on DDEV 1.25.1 — and it is smaller now
     * that the database is a file in the directory and `--omit-containers=db`
     * means there is no database volume to leave. `delete` is still the one
     * that takes what a name is holding, and taking the directory is `rm -rf`.
     *
     * @return list<string>
     */
    public static function discard(string $project): array
    {
        return ['ddev', 'delete', '--omit-snapshot', '-y', $project];
    }

    /** Whether the tool a made environment is built with is on this machine. */
    public static function ddev(): bool
    {
        [$exitCode] = self::run(['ddev', '--version']);

        return $exitCode === 0;
    }

    /**
     * What leaves this process, and the seam a unit test takes instead.
     *
     * `R-COD-003`: a unit test mocks what it needs from outside rather than
     * starting it. Nothing in the suite drives a build today — the tests read
     * the commands `build()` returns — and the seam is here so that the one
     * that wants to can.
     */
    private static ?CommandRunner $runner = null;

    /** What a test hands in, so nothing it drives has to exist on the machine. */
    public static function useRunner(?CommandRunner $runner): void
    {
        self::$runner = $runner;
    }

    /**
     * One step of a build, with both its streams as one string.
     *
     * `Checkouts::run` is the same shape and is not reused because of stdin: it
     * leaves it inherited, which is right for git and wrong here — `ddev` and
     * the TYPO3 console read stdin where they think a person is there, and
     * `SystemRunner` is where that is got right for both (`R-DIS-018`). No
     * timeout, unlike the console: a step here is a `composer create-project` of
     * a hundred packages.
     *
     * @param list<string> $command
     *
     * @return array{0: int, 1: string}
     */
    public static function run(array $command, ?string $cwd = null): array
    {
        $result = (self::$runner ?? new SystemRunner())->run($command, $cwd);

        return [$result['exitCode'], $result['output'] . $result['error']];
    }

    /**
     * The steps that put this project's own extension into the installation.
     *
     * They run after the build and after a resume alike, so an environment
     * made before this extension existed gains it by being asked for again.
     * All three cost seconds and none of them is skipped on a state read from
     * the directory: a require that is already satisfied, a setup that finds
     * its table and a seed that finds rows each say so and change nothing,
     * which is cheaper than a check that can be wrong — `D-EVI-010`.
     *
     * @return array<string, list<string>>
     */
    public static function ownExtension(): array
    {
        return [
            'The extension of the project\'s own, out of packages/' => [
                'ddev', 'composer', 'require', SiteExtension::PACKAGE . ':*', '--no-interaction',
            ],
            'Its table, which the extension setup creates' => [
                'ddev', 'exec', 'vendor/bin/typo3', 'extension:setup',
            ],
            'The rows in it, where it has none yet' => [
                'ddev', 'exec', 'php', SiteExtension::SEED,
            ],
        ];
    }

    /**
     * The build, as the commands in the order they are run.
     *
     * Every one of them is idempotent or forced, so a build that failed
     * halfway is finished by running this again rather than by deleting the
     * directory. That is not a convenience: a `composer create-project` of a
     * TYPO3 is minutes and a hundred packages, and a step that has to start
     * over is a step nobody repeats.
     *
     * The setup step is where that stops holding, and `--force` is not the
     * exception it reads as. It forces the settings file and nothing else —
     * `prepareSystemSettings()` is its only use in 14.3 — while the database
     * is guarded by a validator that refuses any table at all, on the
     * non-interactive path as much as the asked one. A build that meets a
     * populated database is finished by taking the directory away, which on
     * sqlite is the whole of it: the file is `var/sqlite/` below the project
     * and nothing named after the project outlives an `rm -rf`.
     *
     * @return array<string, list<string>>
     */
    public static function build(string $branch, string $driver = self::DEFAULT_DRIVER): array
    {
        $database = self::driver($driver);
        $project = self::project($branch, $driver);
        $constraint = self::constraint($branch);
        $create = ['ddev', 'composer', 'create-project', self::DISTRIBUTION . ':' . $constraint];
        if (self::development($branch)) {
            // The distribution's `dev-main` declares no `minimum-stability` of
            // its own, so the twenty-four `dev-main` requires it carries are
            // refused by the default `stable` before anything is downloaded.
            // The flag is on `create-project` alone: the requires below name a
            // dev version outright, and a root requirement's own constraint is
            // what sets its stability flag.
            $create[] = '--stability=dev';
            // And nothing is installed yet, because what create-project would
            // install cannot resolve until the step after this one has run.
            $create[] = '--no-install';
        }

        $configure = [
            'ddev', 'config', '--auto',
            '--project-name=' . $project,
            '--project-type=typo3',
            '--docroot=public',
            '--php-version=' . self::php($branch),
            '--disable-upload-dirs-warning',
        ];
        // The two halves of the driver are one fact and have to move together.
        // An installation left on a database driver with `--omit-containers=db`
        // in front of it builds its containers, installs a hundred packages and
        // dies at the setup step against a service that was never started; one
        // on sqlite with a database container started pays for a second
        // container on every line, on a machine that holds one per covered
        // version.
        $configure[] = $database['ddev'] === null
            ? '--omit-containers=db'
            : '--database=' . $database['ddev'];

        $steps = [
            'A DDEV project of the type TYPO3, serving from public/, on ' . $driver => $configure,
            'The container it serves from' => ['ddev', 'start', '-y'],
            'TYPO3 ' . $branch . ', from its own base distribution' => $create,
        ];

        if (self::development($branch)) {
            // The distribution pins `config.platform.php` to 8.2.0 on the same
            // branch whose `typo3/cms-core: dev-main` requires `php ^8.5`, so
            // Composer resolves against a PHP its own root requirement refuses
            // and the install ends in "your php version (8.2.0; overridden via
            // config.platform, actual: 8.5.3) does not satisfy that
            // requirement". Measured on 2026-08-03 at `dev-main` c374dbc.
            //
            // Unset rather than raised to the pin above: what this environment
            // is for is answering the way an installation of that line
            // answers, which is against the PHP the container actually runs.
            // A released line keeps the pin, where it is not in conflict.
            $steps['The platform pin the distribution outgrew on this branch'] = [
                'ddev', 'composer', 'config', '--unset', 'platform.php',
            ];
        }

        foreach (self::REQUIRED as $package) {
            $steps['The console commands this server asks for, from ' . $package] = [
                'ddev', 'composer', 'require', $package . ':' . $constraint, '--no-interaction',
            ];
        }

        $setup = [
            'ddev', 'exec', 'vendor/bin/typo3', 'setup',
            '--no-interaction',
            // The settings file and nothing else. This is what lets a
            // half-built environment be finished rather than stopping on the
            // file the first attempt wrote, and it does not reach the database
            // an earlier installation populated.
            '--force',
            // The connection type out of `SetupCommand::$connectionLabels`,
            // which is not the DBAL driver it resolves to — see self::DRIVERS.
            '--driver=' . $database['setup'],
        ];
        // What the connection needs, and only where there is one to make.
        // sqlite is a file the installation places below `var/sqlite/` itself,
        // and `SetupCommand` skips every one of these for it: its
        // `sqliteManualConfigurationOptions` carry the driver alone, so a host
        // passed there names a service that does not exist.
        if ($database['port'] !== null) {
            $setup = [...$setup,
                '--host=' . self::SERVICE_HOST,
                '--port=' . $database['port'],
                '--dbname=' . self::SERVICE_DATABASE,
                '--username=' . self::SERVICE_USER,
                // Passed rather than left to `TYPO3_DB_PASSWORD`, because the
                // setup forces the password question even under
                // `--no-interaction` where neither the option nor the variable
                // is set: `getFallbackValueEnvOrOption` reads the option first,
                // so the flag is what keeps the build unattended.
                '--password=' . self::SERVICE_PASSWORD,
            ];
        }

        $steps['The installation itself: database, admin user, site configuration'] = [...$setup,
            '--admin-username=admin',
            '--admin-user-password=' . self::ADMIN_PASSWORD,
            '--admin-email=admin@example.com',
            '--project-name=TYPO3 MCP scenario environment ' . $branch,
            // Not optional, whatever the option definition says. Its default is
            // read through the same fallback as the environment variable, so
            // with --no-interaction and nothing passed the validator is handed
            // `false` and 14.3.5 dies on the type — measured on 2026-08-02.
            // `other` is the answer for the nginx DDEV runs anyway.
            '--server-type=other',
            '--create-site=https://' . $project . '.ddev.site/',
        ];
        $steps['The extensions set up against that database'] = [
            'ddev', 'exec', 'vendor/bin/typo3', 'extension:setup',
        ];

        return $steps;
    }
}
