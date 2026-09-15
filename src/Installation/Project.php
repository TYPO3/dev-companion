<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Search\Text;

/**
 * What the repository the session stands in consists of, read from its files,
 * whether or not an installation exists below it.
 *
 * The knowledge base describes TYPO3; this describes the repository the caller
 * stands in. Which extensions are its own, which sites it configures, which
 * commands it declares, and which environment it declares them to run in. None
 * of that could ship as a bundle. All of it is what an answer has to be right
 * about before it can recommend anything. A check that does not exist here is
 * worse than no check.
 *
 * Files only. No console, no database, nothing started. That is the same rule
 * the rest of this server follows, and the reason this works on a fresh clone.
 * Four fields are the exception and wait for the install. The TYPO3 version,
 * the PHP the installed core requires, the PHP bound of the install as a whole,
 * and the extensions. Those are Composer's metadata rather than anything the
 * manifest declares.
 */
final class Project
{
    /** The extension is the repository's own, or one of its path repositories. */
    public const ORIGIN_PROJECT = 'project';

    /** Installed as a dependency, and not a TYPO3 system extension. */
    public const ORIGIN_THIRD_PARTY = 'third-party';

    /**
     * Shipped by the repository's test setup, not by the repository.
     *
     * An extension repository routinely installs a package of its own from
     * below Tests/, a fixture the functional suite loads, a demo package a
     * scenario needs. Composer lists it like any other path repository. The
     * name "the project's own" says "this is the work" about something that
     * exists for a load and a throw away. Reported as its own thing rather than
     * dropped. A fixture the answer omits is one nobody can account for when it
     * shows up in an installed package list.
     */
    public const ORIGIN_FIXTURE = 'fixture';

    /** A declared command that reports on the sources and leaves them as they are. */
    public const RUNS_AS_CHECK = 'check';

    /** One that rewrites something. */
    public const RUNS_AS_CHANGE = 'change';

    /** One whose declaration does not say which of the two it is. */
    public const RUNS_UNDECLARED = 'unknown';

    /**
     * Where one version sits against another — the whole vocabulary of every
     * relation this answer states, the PHP numbers and the Node ones alike.
     *
     * One word for one idea. The declared floor stands `below`, on the `same`
     * version as, or `above` what the core requires. The environment's
     * interpreter stands the same three ways against that floor. What each of
     * them means for the project is the schema's to say, not the value's.
     */
    public const BELOW = 'below';
    public const SAME = 'same';
    public const ABOVE = 'above';

    /**
     * The keys a DDEV hook task states its command under.
     *
     * `exec` runs it in a container, `exec-host` on the machine of the DDEV
     * call, `composer` in the web container with `composer` in front.
     */
    private const DDEV_TASKS = ['exec', 'exec-host', 'composer'];

    /**
     * The mark DDEV puts on the files it owns, and replaces them by while the
     * mark is there. `nodeps.DdevFileSignature` in v1.25.1, matched as a
     * literal anywhere in the file the way `fileutil.FgrepStringInFile` does.
     */
    private const DDEV_SIGNATURE = '#ddev-generated';

    /**
     * The top-level domain a project's URLs build from where no file names one,
     * the default of `ddev config --project-tld`.
     *
     * A machine whose global DDEV configuration sets another one gets no read
     * here. This answer is the project's own files (`R-PRJ-001`). The hostnames
     * it states say what the configuration declares rather than what a live
     * router answers on.
     */
    private const DDEV_TLD = 'ddev.site';

    /**
     * @return array{
     *     root: string,
     *     kind: string,
     *     installed: bool,
     *     installedAgainstLock: array{state: string, packages: array<int, array{package: string, locked: ?string, installed: ?string}>},
     *     typo3Version: ?string,
     *     phpConstraint: ?string,
     *     coreConstraint: ?string,
     *     corePhpConstraint: ?string,
     *     installedPhpBound: ?string,
     *     phpRelation: array{floor: string, coreFloor: ?string, againstCore: ?string, inEnvironment: ?string, bound: ?string, environmentAgainstBound: ?string}|null,
     *     node: array{engines: ?string, enginesIn: ?string, nvmrc: ?string, nvmrcIn: ?string, environment: ?string, ci: array<int, array{workflow: string, from: string, states: string, version: ?string}>, relation: array{declared: string, declaredBy: string, nvmrcAgainstEngines: ?string, inEnvironment: ?string, ci: ?string, inCi: ?string}|null}|null,
     *     environment: array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null,
     *     extensions: array<int, array{key: string, path: string, origin: string, deprecatedFiles: array<int, array{file: string, changelog: string, predicate: string, cost: string}>}>,
     *     sites: array<int, array{identifier: string, base: string, rootPageId: ?int, sets: array<int, string>, languages: array<int, string>}>,
     *     commands: array<int, array{command: string, source: string, declares: string, runs: string, runThrough: string|null}>,
     *     uncheckedKinds: array<int, string>,
     *     patches: array<int, array{package: string, description: string, file: string}>
     * }|null
     */
    public static function describe(): ?array
    {
        $project = Instance::project();
        if ($project === null) {
            return null;
        }

        $root = $project['root'];
        $manifest = Data::json($root . '/composer.json');
        $php = self::requirement($manifest, 'php');
        $corePhp = self::corePhpConstraint();
        $bound = Instance::installedPhpBound($root);
        $environment = self::environment($root);
        $commands = self::commands($root, $project['kind'], $manifest, $environment);

        return [
            'root' => $root,
            'kind' => $project['kind'],
            // The four fields below that read the installed tree answer null
            // and empty here. None of them says on its own that nothing has
            // installed rather than that there was nothing to find
            // (`D-ANS-085`).
            'installed' => Instance::packages() !== [],
            // What that boolean cannot say. A vendor directory older than the
            // lock satisfies it, and the drift is a field beside it rather than
            // a third state inside it (`D-ANS-102`).
            'installedAgainstLock' => Instance::installedAgainstLock($root),
            'typo3Version' => Instance::typo3Version(),
            'phpConstraint' => $php,
            'coreConstraint' => self::requirement($manifest, 'typo3/cms-core'),
            'corePhpConstraint' => $corePhp,
            'installedPhpBound' => $bound,
            'phpRelation' => self::phpRelation($php, $corePhp, $environment['php'] ?? null, $bound),
            'node' => Node::describe($root, $environment['node'] ?? null),
            'environment' => $environment,
            'extensions' => self::extensions($root),
            'sites' => self::sites($root),
            'commands' => $commands,
            'uncheckedKinds' => self::uncheckedKinds($commands),
            'patches' => self::patches($manifest),
        ];
    }

    /**
     * The environment this repository configures to run itself in, where it
     * configures one at all.
     *
     * A containerised project has two PHP versions, the one its manifest
     * constrains and the one its container runs. The commands below make that
     * worse rather than better, as the ones a task runs. Read from the
     * environment's own files, so `R-PRJ-001` still holds on a fresh clone and
     * nothing starts to find out (`R-DIS-006`). A stopped project reads exactly
     * like a live one here. The interpreter is half of it, and what the
     * environment runs by itself is `R-PRJ-009`.
     *
     * @return array{via: string, php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, entered: bool, hooks: array<int, array{stage: string, command: string, service: ?string}>, providers: array<int, array{name: string, source: string, operations: array<int, string>}>}|null
     */
    private static function environment(string $root): ?array
    {
        if (is_file($root . '/.ddev/config.yaml')) {
            $ddev = self::ddev($root);

            return [
                'via' => Typo3Cli::VIA_DDEV,
                'php' => $ddev['php'],
                'node' => $ddev['node'],
                'source' => $ddev['source'],
                'project' => $ddev['project'],
                'hostnames' => $ddev['hostnames'],
                // DDEV sets this inside the web container, and there the shell
                // the caller has is the environment. Telling it to put `ddev`
                // in front of a command would name a binary that is not there.
                'entered' => filter_var(getenv('IS_DDEV_PROJECT'), FILTER_VALIDATE_BOOL),
                'hooks' => $ddev['hooks'],
                'providers' => self::ddevProviders($root),
            ];
        }

        // Nothing in the files says DDEV, and one thing outside them may still
        // say there is an environment. The console command the caller stated
        // for `Typo3Cli`. A `docker compose exec web bin/typo3` is a machine
        // this server cannot read a PHP version from. No environment in the
        // report there says "these run in your shell", which is the claim this
        // whole field exists to keep silence from.
        $stated = getenv(Typo3Cli::CONSOLE_VARIABLE);
        $program = is_string($stated) ? (self::firstToken($stated) ?? '') : '';
        if ($program !== '' && !str_starts_with(basename($program), 'php')) {
            return [
                'via' => Typo3Cli::VIA_OVERRIDE,
                'php' => null,
                'node' => null,
                'source' => Typo3Cli::CONSOLE_VARIABLE,
                // A command line names no project and no site.
                'project' => null,
                'hostnames' => [],
                'entered' => false,
                // A command line names a way in and no files, so there is
                // nothing here to read a lifecycle out of.
                'hooks' => [],
                'providers' => [],
            ];
        }

        // An interpreter on this machine is not another environment. A stated
        // `php /some/where/typo3` reaches the console from the same shell every
        // declared command would run in, and there is one PHP.
        return null;
    }

    /** The program a stated command line starts with, quoted or not. */
    private static function firstToken(string $commandLine): ?string
    {
        $tokens = preg_split('/\s+/', trim($commandLine)) ?: [];

        return ($tokens[0] ?? '') === '' ? null : trim($tokens[0], '"\'');
    }

    /**
     * What a DDEV project's configuration says. The PHP it runs, the file that
     * states it, and the tasks it runs at a stage of its own.
     *
     * `.ddev/config.yaml` comes first, then every `.ddev/config.*.yaml` and
     * `.ddev/config.*.yml` beside it in filename order. The last statement of
     * the version is the one that holds. The hooks of one stage join in that
     * same order, and a stage no later file mentions keeps what it had.
     * `override_config: true` replaces instead, per stage and only for the
     * stages the file with it names. So `post-start: []` under it erases that
     * one stage and leaves the rest, and a plain file after it adds to what it
     * left. A read of the base file alone would report a lifecycle the
     * container does not run. That is every project that keeps its local
     * settings in the `config.local.yaml` DDEV gitignores for that.
     *
     * All of it measured against DDEV v1.25.1 through `ddev debug configyaml`,
     * on 2026-08-02 for the version and 2026-08-03 for the hooks.
     *
     * Null where nothing states a version. DDEV then uses the default of the
     * installed DDEV. That is not in these files and is not the same number
     * from one release to the next.
     *
     * The name and the hostnames come from the same files, and they are what a
     * caller needs to reach the site at all. A session that had the environment
     * in the report and nothing else spent four shell round trips on the
     * project name. One wrong attempt lay in between
     * (`feedback/2026-08-10-101723`).
     *
     * @return array{php: ?string, node: ?string, source: string, project: ?string, hostnames: array<int, string>, hooks: array<int, array{stage: string, command: string, service: ?string}>}
     */
    private static function ddev(string $root): array
    {
        $files = ['.ddev/config.yaml'];
        foreach (Finder::create()->files()->in($root . '/.ddev')->depth(0)
            ->name('config.*.yaml')->name('config.*.yml')->sortByName() as $file) {
            $files[] = '.ddev/' . $file->getFilename();
        }

        $php = null;
        $node = null;
        $source = '.ddev/config.yaml';
        $stages = [];
        // What DDEV falls back to when no file names one. `ddev config
        // --project-name` is "normally the same as the last part of directory
        // name", and `--project-tld` defaults to ddev.site.
        $project = basename($root);
        $tld = '';
        $hostnames = [];
        $fqdns = [];
        foreach ($files as $file) {
            $configuration = Data::yaml($root . '/' . $file);
            $version = self::configuredVersion($configuration['php_version'] ?? null);
            if ($version !== null) {
                $php = $version;
                $source = $file;
            }
            $node = self::configuredVersion($configuration['nodejs_version'] ?? null) ?? $node;

            $project = is_string($configuration['name'] ?? null) && $configuration['name'] !== ''
                ? $configuration['name']
                : $project;
            $tld = is_string($configuration['project_tld'] ?? null) && $configuration['project_tld'] !== ''
                ? $configuration['project_tld']
                : $tld;
            $hostnames = [...$hostnames, ...self::names($configuration['additional_hostnames'] ?? null)];
            $fqdns = [...$fqdns, ...self::names($configuration['additional_fqdns'] ?? null)];

            $replaces = ($configuration['override_config'] ?? null) === true;
            foreach (is_array($configuration['hooks'] ?? null) ? $configuration['hooks'] : [] as $stage => $tasks) {
                if (is_array($tasks)) {
                    $stages[(string) $stage] = $replaces ? $tasks : [...$stages[(string) $stage] ?? [], ...$tasks];
                }
            }
        }

        $hooks = [];
        foreach ($stages as $stage => $tasks) {
            foreach ($tasks as $task) {
                $hook = self::hook($stage, $task);
                if ($hook !== null) {
                    $hooks[] = $hook;
                }
            }
        }

        $tld = $tld === '' ? self::DDEV_TLD : $tld;
        $sites = [$project . '.' . $tld];
        foreach ($hostnames as $hostname) {
            $sites[] = $hostname . '.' . $tld;
        }

        return [
            'php' => $php,
            'node' => $node,
            'source' => $source,
            'project' => $project,
            // A fully qualified name is the caller's own and takes no tld.
            'hostnames' => array_values(array_unique([...$sites, ...$fqdns])),
            'hooks' => $hooks,
        ];
    }

    /**
     * The non-empty strings of a list a configuration file states, or none.
     *
     * @return array<int, string>
     */
    private static function names(mixed $stated): array
    {
        if (!is_array($stated)) {
            return [];
        }

        return array_values(array_filter(
            $stated,
            static fn(mixed $name): bool => is_string($name) && $name !== '',
        ));
    }

    /**
     * One task of one stage, or null where it states no command.
     *
     * No mark, unlike the declared commands. `runs()` answers whether a caller
     * may run something, and a hook is not the caller's to run. What it does to
     * the sources is in the command beside it, which is short and prints whole.
     * The mark earns its place on a `cgl` whose body nobody sees.
     *
     * @return array{stage: string, command: string, service: ?string}|null
     */
    private static function hook(string $stage, mixed $task): ?array
    {
        if (!is_array($task)) {
            return null;
        }

        foreach (self::DDEV_TASKS as $type) {
            if (!array_key_exists($type, $task)) {
                continue;
            }
            $command = self::hookCommand($task[$type], $task['exec_raw'] ?? null);
            if ($command === '') {
                continue;
            }

            return [
                'stage' => $stage,
                'command' => $type === 'composer' ? 'composer ' . $command : $command,
                // The one task type that runs on the machine of the DDEV call
                // rather than inside the project.
                'service' => $type === 'exec-host'
                    ? null
                    : (is_string($task['service'] ?? null) ? $task['service'] : 'web'),
            ];
        }

        return null;
    }

    /**
     * The command one task runs, from the string it states or the `exec_raw`
     * argument list it states instead.
     *
     * A block command is several lines in one shell and nothing stops it at the
     * first failure, so `;` joins the lines. The `&&` that joins the composer
     * scripts would say the file does something it does not.
     */
    private static function hookCommand(mixed $stated, mixed $raw): string
    {
        if (is_string($stated) && trim($stated) !== '') {
            $lines = array_filter(array_map(trim(...), explode("\n", trim($stated))));

            return implode('; ', $lines);
        }

        return is_array($raw) ? implode(' ', array_map(strval(...), $raw)) : '';
    }

    /**
     * The pull and push recipes this repository wrote, which are what
     * `ddev pull <name>` and `ddev push <name>` run.
     *
     * `.ddev/providers/<name>.yaml` exactly, measured against DDEV v1.25.1 on
     * 2026-08-03. `ddev pull` offered the two `.yaml` files in that directory
     * and neither the `.yml` nor the `.yaml.example` beside them.
     *
     * DDEV writes its own recipes into every project, Acquia, Lagoon, Upsun,
     * platform.sh and four examples. It marks each with the signature it
     * replaces the file by while the mark is there. So a marked one says what
     * DDEV puts everywhere and one without a mark says what this repository
     * decided. A list of both would report ten integrations in a project that
     * has one.
     *
     * @return array<int, array{name: string, source: string, operations: array<int, string>}>
     */
    private static function ddevProviders(string $root): array
    {
        $directory = $root . '/.ddev/providers';
        if (!is_dir($directory)) {
            return [];
        }

        $providers = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.yaml')->sortByName() as $file) {
            if (str_contains((string) file_get_contents($file->getPathname()), self::DDEV_SIGNATURE)) {
                continue;
            }

            $declared = ['pull' => false, 'push' => false];
            foreach (array_keys(Data::yaml($file->getPathname())) as $stanza) {
                // A recipe may obtain the dump in one stanza and import it in
                // another, and either half makes it one you can pull with.
                $declared['pull'] = $declared['pull'] || preg_match('/_(pull|import)_command$/', (string) $stanza) === 1;
                $declared['push'] = $declared['push'] || str_ends_with((string) $stanza, '_push_command');
            }

            $providers[] = [
                'name' => $file->getBasename('.yaml'),
                'source' => '.ddev/providers/' . $file->getFilename(),
                'operations' => array_values(array_filter(
                    ['pull', 'push'],
                    static fn(string $operation): bool => $declared[$operation],
                )),
            ];
        }

        return $providers;
    }

    /**
     * A version a DDEV configuration states, as the file spells it —
     * `php_version` and `nodejs_version` alike.
     *
     * Quotes are optional there and the difference reaches here. Without them,
     * `8.0` is a YAML float and a cast to a string gives "8", a version that
     * exists nowhere. `nodejs_version` is why the form stays rather than cuts
     * to major.minor. DDEV v1.25.1 takes a bare major there, and a `20.19`
     * beside a workflow's `20.19.0` is the pair the relation reads from.
     */
    private static function configuredVersion(mixed $stated): ?string
    {
        if (is_float($stated)) {
            // A float carries no zero at the end, so 8.0 comes back "8", a
            // version that exists nowhere. Everything else it spells as typed.
            $spelled = (string) $stated;

            return str_contains($spelled, '.') ? $spelled : $spelled . '.0';
        }

        return match (true) {
            is_int($stated) => (string) $stated,
            is_string($stated) && trim($stated) !== '' => trim($stated),
            default => null,
        };
    }

    /**
     * The patches this project applies to its dependencies.
     *
     * A patched package is a package whose behaviour is not what its version
     * says. The next composer update either applies the patch again or fails on
     * it. Nothing else in an answer about this project matters more to an
     * upgrade. It is one entry in composer.json.
     *
     * @param array<string, mixed> $manifest
     * @return array<int, array{package: string, description: string, file: string}>
     */
    private static function patches(array $manifest): array
    {
        $declared = $manifest['extra']['patches'] ?? null;
        if (!is_array($declared)) {
            return [];
        }

        $patches = [];
        foreach ($declared as $package => $entries) {
            foreach (is_array($entries) ? $entries : [] as $description => $file) {
                $patches[] = [
                    'package' => (string) $package,
                    // The list form carries no description, only the file.
                    'description' => is_string($description) ? $description : '',
                    'file' => (string) $file,
                ];
            }
        }

        return $patches;
    }

    /**
     * The extensions that are not TYPO3's own, with where they come from.
     *
     * A system extension is TYPO3. Everything else is what this project brought
     * with it, and the ones inside the repository are the ones under work.
     *
     * The deprecated files come in for the ones inside the repository alone.
     * They are the work of the project, so a file core has stopped to read is a
     * defect somebody here can fix. In a dependency it is the dependency's,
     * `D-ANS-009`.
     *
     * @return array<int, array{key: string, path: string, origin: string, deprecatedFiles: array<int, array{file: string, changelog: string, predicate: string, cost: string}>}>
     */
    private static function extensions(string $root): array
    {
        $extensions = [];
        foreach (Instance::packages() as $key => $path) {
            if (Instance::isSystemExtension($key) === true) {
                continue;
            }
            $origin = self::origin($path);
            $extensions[] = [
                'key' => $key,
                'path' => self::relative($root, $path),
                'origin' => $origin,
                'deprecatedFiles' => $origin === self::ORIGIN_PROJECT ? Extension::deprecatedFilesOf($path) : [],
            ];
        }

        return $extensions;
    }

    /**
     * The kinds of file this project's own packages ship, by the pattern that
     * finds each one.
     *
     * JavaScript is not among them, on purpose. A `.js` a package ships is as
     * often build output or a vendored library as it is source, so an unchecked
     * one says nothing, `D-ANS-148`.
     *
     * @var array<string, string>
     */
    private const KINDS = [
        'PHP' => '*.php',
        'CSS' => '*.css',
        'Sass' => '*.scss',
        'TypeScript' => '*.ts',
        'XLIFF' => '*.xlf',
    ];

    /**
     * The kinds a checker checks, by the name it runs under.
     *
     * A tool that is not here contributes no coverage, which is what keeps a
     * gap this names to the ones it can see. A test runner checks no kind of
     * file, and a checker nobody has listed looks the same as one
     * (`D-ANS-148`).
     *
     * @var array<string, array<int, string>>
     */
    private const CHECKERS = [
        'php-cs-fixer' => ['PHP'],
        'phpcs' => ['PHP'],
        'phpcbf' => ['PHP'],
        'phpstan' => ['PHP'],
        'psalm' => ['PHP'],
        'rector' => ['PHP'],
        'stylelint' => ['CSS', 'Sass'],
        'prettier' => ['CSS', 'Sass', 'TypeScript'],
        'eslint' => ['TypeScript'],
        'tsc' => ['TypeScript'],
        'xliff-lint' => ['XLIFF'],
        'xmllint' => ['XLIFF'],
    ];

    /**
     * The kinds of file the project's own packages ship that no declared
     * command names a checker for.
     *
     * What it does not say is what to add. A repository's standards are its
     * own, and this answer's worth is that it reports the declared rather than
     * the customary. That is the same reason it recommends no undeclared check
     * (`D-ANS-148`).
     *
     * @param array<int, array<string, mixed>> $commands
     * @return array<int, string>
     */
    private static function uncheckedKinds(array $commands): array
    {
        $declared = mb_strtolower(implode("\n", array_column($commands, 'declares')));
        $checked = [];
        foreach (self::CHECKERS as $tool => $kinds) {
            if (Text::containsWord($declared, $tool)) {
                $checked = [...$checked, ...$kinds];
            }
        }

        $unchecked = [];
        foreach (self::KINDS as $kind => $pattern) {
            if (in_array($kind, $checked, true) || !self::shipped($pattern)) {
                continue;
            }
            $unchecked[] = $kind;
        }

        return $unchecked;
    }

    /** Whether a package that is this project's own holds a file of that shape. */
    private static function shipped(string $pattern): bool
    {
        foreach (Instance::packages() as $key => $path) {
            if (Instance::isSystemExtension($key) === true || self::origin($path) !== self::ORIGIN_PROJECT) {
                continue;
            }
            if (!is_dir($path)) {
                continue;
            }
            $found = Finder::create()->files()->in($path)->name($pattern)
                ->exclude(['node_modules', 'vendor'])->hasResults();
            if ($found) {
                return true;
            }
        }

        return false;
    }

    /**
     * Where an extension in this installation comes from, read off its path.
     *
     * Below the vendor directory it came in as a dependency. Below a Tests/
     * directory it belongs to the test setup, whatever Composer's install path
     * says. A package repository under Tests/Packages/ resolves to a real
     * directory in the repository, and nothing else tells it apart from the
     * extension under development.
     */
    public static function origin(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        if (str_contains($path, '/vendor/')) {
            return self::ORIGIN_THIRD_PARTY;
        }

        return preg_match('#(^|/)[Tt]ests?/#', $path) === 1 ? self::ORIGIN_FIXTURE : self::ORIGIN_PROJECT;
    }

    /**
     * The sites this project configures, with the sets each of them depends on.
     *
     * The dependencies are where a site says which TypoScript it gets, so they
     * are the first thing to look at when a template renders nothing.
     *
     * @return array<int, array{identifier: string, base: string, rootPageId: ?int, sets: array<int, string>, languages: array<int, string>}>
     */
    private static function sites(string $root): array
    {
        $directory = $root . '/config/sites';
        if (!is_dir($directory)) {
            return [];
        }

        $sites = [];
        foreach (Finder::create()->files()->in($directory)->depth(1)->name('config.yaml')->sortByName() as $file) {
            $configuration = Data::yaml($file->getPathname());
            $languages = [];
            foreach ($configuration['languages'] ?? [] as $language) {
                if (is_array($language)) {
                    $languages[] = (string) ($language['title'] ?? $language['locale'] ?? '');
                }
            }

            $sites[] = [
                'identifier' => $file->getRelativePath(),
                'base' => (string) ($configuration['base'] ?? ''),
                'rootPageId' => isset($configuration['rootPageId']) ? (int) $configuration['rootPageId'] : null,
                'sets' => array_map('strval', $configuration['dependencies'] ?? []),
                'languages' => $languages,
            ];
        }

        return $sites;
    }

    /**
     * The commands this repository declares, which are the only ones worth a
     * recommendation in it.
     *
     * Composer scripts and npm scripts are where a project writes down what it
     * runs. The core's own test suites are not there, which is the whole point
     * of the question.
     *
     * @param array<string, mixed> $manifest
     * @param array{via: string, entered: bool}|null $environment
     * @return array<int, array{command: string, source: string, invocation: string, declares: string, runs: string, runThrough: string|null}>
     */
    private static function commands(string $root, string $kind, array $manifest, ?array $environment): array
    {
        $commands = [];
        $scripts = is_array($manifest['scripts'] ?? null) ? $manifest['scripts'] : [];
        foreach ($scripts as $name => $declaration) {
            $command = 'composer ' . $name;
            $commands[] = [
                'command' => $command,
                'source' => 'composer.json',
                'invocation' => self::invocation($command, $environment),
                'declares' => self::declaration($declaration),
                'runs' => self::runs($declaration, $scripts),
                'runThrough' => null,
            ];
        }

        foreach (self::npmManifests($root) as $manifest) {
            $packageScripts = Data::json($root . '/' . $manifest)['scripts'] ?? [];
            foreach (is_array($packageScripts) ? $packageScripts : [] as $name => $declaration) {
                $command = self::npm($manifest) . $name;
                $commands[] = [
                    'command' => $command,
                    'source' => $manifest,
                    'invocation' => self::invocation($command, $environment),
                    'declares' => self::declaration($declaration),
                    'runs' => self::runs($declaration, []),
                    'runThrough' => self::runThrough($kind),
                ];
            }
        }

        return $commands;
    }

    /**
     * The command the repository's own contract runs an npm script through.
     *
     * Only the core has one. Its `AGENTS.md` forbids a direct npm call on
     * `main`, `14.3` and `13.4`, `12.4` carries no such file, and all four ship
     * the dispatcher. So the sentence names the dispatcher rather than the file
     * (`D-ANS-152`). It rides on the entry because a session read the paragraph
     * above the list and reported the pointer as absent.
     */
    private static function runThrough(string $kind): ?string
    {
        if ($kind !== Instance::KIND_CORE_CHECKOUT) {
            return null;
        }

        return 'Build/Scripts/runTests.sh -s <suite>';
    }

    /**
     * The command as it runs from where the caller stands.
     *
     * The prose above the list has named both forms since 2026-08-04, and a
     * session ran the declared one into Composer's platform check anyway. The
     * half it read was the payload, `D-ANS-126`. DDEV's own `composer` command
     * is what carries a composer script in, and `ddev exec` the rest, which is
     * what that sentence says.
     *
     * It is the command unchanged wherever nothing can go in front of it. No
     * environment, a shell that is already inside one, and an environment
     * `TYPO3_DEV_COMPANION_CONSOLE` names, which reaches this installation's
     * console rather than an arbitrary script.
     *
     * @param array{via: string, entered: bool}|null $environment
     */
    private static function invocation(string $command, ?array $environment): string
    {
        if ($environment === null
            || $environment['entered']
            || $environment['via'] !== Typo3Cli::VIA_DDEV
        ) {
            return $command;
        }

        return str_starts_with($command, 'composer ')
            ? 'ddev ' . $command
            : 'ddev exec ' . $command;
    }

    /**
     * The npm manifests this repository has, relative to its root and in the
     * order of the read.
     *
     * The root one, and the `Build/package.json` beside it. The TYPO3 layout
     * keeps the frontend build one directory down. The core has its manifest,
     * its `.nvmrc` and its Gruntfile there on every covered branch and no root
     * manifest at all. A read of the root alone left such a repository with no
     * npm command in this list and no Node under it, `D-SCO-014`.
     *
     * @return array<int, string>
     */
    public static function npmManifests(string $root): array
    {
        return array_values(array_filter(
            ['package.json', 'Build/package.json'],
            static fn(string $manifest): bool => is_file($root . '/' . $manifest),
        ));
    }

    /**
     * What runs a script of one manifest, from the root the caller stands in.
     *
     * `npm run` reads the manifest of the directory of the call, so a script
     * declared below the root carries the `--prefix` that points it there. That
     * is what the core's own runTests.sh puts in front of its playwright
     * scripts. It is what makes two manifests that declare a `build` two
     * commands a caller can tell apart.
     */
    private static function npm(string $manifest): string
    {
        $directory = dirname($manifest);

        return $directory === '.' ? 'npm run ' : 'npm --prefix ' . $directory . ' run ';
    }

    /**
     * What a run of a declared command does to the sources, read off what it
     * declares.
     *
     * A task told not to change files still wants the checks, and no script
     * name carries the difference. `cgl` and `cgl:ci` are one `--dry-run` apart
     * and are the same tool. So it comes out of the body: the tool the script
     * invokes, and the flags that decide which way that tool runs.
     *
     * Three answers rather than two, because a `no` that covers everything
     * unrecognised makes the undecided look decided. A test suite is the
     * ordinary undeclared case. It runs the project's own code, and nothing in
     * a composer.json says what that code writes.
     *
     * "The sources", not "nothing". A checker may still write a cache of its
     * own, `php-cs-fixer --dry-run` writes `.php-cs-fixer.cache` unless told
     * not to. This answers whether the code it points at comes back different,
     * which is what a review is about.
     *
     * @param array<int, mixed>|string $declaration one composer or npm script, as declared
     * @param array<string, mixed> $scripts the declaring manifest's scripts, for `@name` references
     * @param array<int, string> $seen the references already followed, so a cycle ends
     */
    public static function runs(array|string $declaration, array $scripts = [], array $seen = []): string
    {
        $lines = array_filter(is_array($declaration) ? $declaration : [$declaration], is_string(...));
        if ($lines === []) {
            return self::RUNS_UNDECLARED;
        }

        $answers = [];
        foreach ($lines as $line) {
            $answers[] = self::runsLine($line, $scripts, $seen);
        }

        // The strongest claim any line makes is the claim about all of them. A
        // script that lints and then fixes changes the sources. One that lints
        // and then runs a suite is as undeclared as the suite is.
        return match (true) {
            in_array(self::RUNS_AS_CHANGE, $answers, true) => self::RUNS_AS_CHANGE,
            in_array(self::RUNS_UNDECLARED, $answers, true) => self::RUNS_UNDECLARED,
            default => self::RUNS_AS_CHECK,
        };
    }

    /**
     * @param array<string, mixed> $scripts
     * @param array<int, string> $seen
     */
    private static function runsLine(string $line, array $scripts, array $seen): string
    {
        $line = trim($line);

        // One line is one command only where nothing chains another onto it.
        // Read as the tool in front, `phpstan analyse && php-cs-fixer fix` is
        // the analyser and the rewriter never comes up. An npm script chains by
        // convention, so the shape is the ordinary one there.
        $chained = self::chained($line);
        if (count($chained) > 1) {
            return self::runs($chained, $scripts, $seen);
        }

        // Composer's own prefixes come before any tool. @php picks the PHP the
        // project runs on, @putenv sets a variable for the lines after it. A
        // bare @name is another script of the same manifest.
        while ($line !== '' && $line[0] === '@') {
            [$prefix, $rest] = array_pad(preg_split('/\s+/', $line, 2) ?: [], 2, '');
            if ($prefix === '@php' || $prefix === '@php_binary' || $prefix === '@composer') {
                $line = $prefix === '@composer' ? 'composer ' . $rest : $rest;
                continue;
            }
            if ($prefix === '@putenv') {
                return self::RUNS_AS_CHECK;
            }

            $name = substr($prefix, 1);

            return isset($scripts[$name]) && !in_array($name, $seen, true)
                && (is_array($scripts[$name]) || is_string($scripts[$name]))
                ? self::runs($scripts[$name], $scripts, [...$seen, $name])
                : self::RUNS_UNDECLARED;
        }

        // A `NAME=value` in front is the environment a command gets, not the
        // command. `PHP_CS_FIXER_IGNORE_ENV=1 php-cs-fixer fix --dry-run` is
        // the fixer in report mode, and a read of the assignment as the tool
        // loses every flag behind it. So the reporter and the rewriter come
        // back the same.
        $line = (string) preg_replace('/^(?:[A-Za-z_][A-Za-z0-9_]*=(?:"[^"]*"|\'[^\']*\'|\S*)\s+)+/', '', $line);

        $tokens = array_values(array_filter(preg_split('/\s+/', $line) ?: []));
        if ($tokens === []) {
            return self::RUNS_UNDECLARED;
        }

        $tool = self::tool(array_shift($tokens));
        // `php vendor/bin/phpstan` is phpstan; `php -l` is the linter itself.
        if ($tool === 'php' && $tokens !== [] && !str_starts_with($tokens[0], '-')) {
            $tool = self::tool((string) array_shift($tokens));
        }

        $carries = static function (string ...$flags) use ($tokens): bool {
            foreach ($tokens as $token) {
                foreach ($flags as $flag) {
                    if (strcasecmp($token, $flag) === 0 || stripos($token, $flag . '=') === 0) {
                        return true;
                    }
                }
            }

            return false;
        };
        $first = strtolower($tokens[0] ?? '');

        return match ($tool) {
            // Linters and analysers: they read, they report, and their exit
            // code is the whole of what they do to the checkout.
            'phplint', 'parallel-lint', 'typoscript-lint', 'phpcs', 'phpmd', 'phpcpd',
            'composer-require-checker', 'composer-unused', 'composer-dependency-analyser' => self::RUNS_AS_CHECK,
            'php' => $carries('-l') ? self::RUNS_AS_CHECK : self::RUNS_UNDECLARED,
            'phpstan' => $carries('--generate-baseline') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'psalm' => $carries('--set-baseline', '--alter') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            // Both directions of one tool, and the flag is the only difference.
            'php-cs-fixer' => $first === 'check' || $carries('--dry-run') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'ecs' => $carries('--fix') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'rector' => $carries('--dry-run') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'eslint', 'stylelint' => $carries('--fix') ? self::RUNS_AS_CHANGE : self::RUNS_AS_CHECK,
            'tsc' => $carries('--noEmit') ? self::RUNS_AS_CHECK : self::RUNS_AS_CHANGE,
            'phpcbf' => self::RUNS_AS_CHANGE,
            // Build steps exist to produce files, and the composer commands
            // that write are the ones that touch the tree the review is about.
            'vite', 'webpack', 'rollup', 'esbuild', 'sass', 'postcss', 'gulp', 'grunt',
            'git', 'rm', 'cp', 'mv', 'mkdir', 'touch' => self::RUNS_AS_CHANGE,
            'composer' => match ($first) {
                'validate', 'audit', 'show', 'outdated', 'licenses', 'diagnose', 'check-platform-reqs' => self::RUNS_AS_CHECK,
                'install', 'update', 'require', 'remove', 'dump-autoload', 'dumpautoload' => self::RUNS_AS_CHANGE,
                default => self::RUNS_UNDECLARED,
            },
            'npm', 'yarn', 'pnpm' => match ($first) {
                'install', 'ci', 'update', 'add' => self::RUNS_AS_CHANGE,
                default => self::RUNS_UNDECLARED,
            },
            // Its two write subcommands are what TYPO3 extensions declare it
            // for; the rest of it gets no read here.
            'extension-helper' => in_array($first, ['version:set', 'changelog:create'], true)
                ? self::RUNS_AS_CHANGE
                : self::RUNS_UNDECLARED,
            // A suite runs the project's own code, and `bin/typo3` runs
            // whatever command it gets. Neither is readable from the
            // declaration.
            default => self::RUNS_UNDECLARED,
        };
    }

    /**
     * The commands one declared line puts on the shell.
     *
     * `&&`, `||`, `;`, `|` and a trailing `&` each start another one, and every
     * one of them runs. An operator in quotes does not, because a filter
     * pattern carries `|` and a message carries `;`.
     *
     * @return array<int, string>
     */
    private static function chained(string $line): array
    {
        $commands = [];
        $command = '';
        $quote = '';
        foreach (str_split($line) as $character) {
            if ($quote !== '') {
                $quote = $character === $quote ? '' : $quote;
            } elseif ($character === '"' || $character === "'") {
                $quote = $character;
            } elseif ($character === '&' || $character === '|' || $character === ';') {
                $commands[] = $command;
                $command = '';

                continue;
            }
            $command .= $character;
        }
        $commands[] = $command;

        return array_values(array_filter(array_map(trim(...), $commands), static fn(string $command): bool => $command !== ''));
    }

    /** The tool a declared line invokes, without the path, the extension, or the runner in front of it. */
    private static function tool(string $token): string
    {
        $token = basename(str_replace('\\', '/', $token));
        $token = (string) preg_replace('/\.(phar|bat|cmd|sh)$/i', '', $token);

        return strtolower($token);
    }

    /** @param array<int, mixed>|string $declaration */
    private static function declaration(array|string $declaration): string
    {
        $lines = array_filter(is_array($declaration) ? $declaration : [$declaration], is_string(...));

        return implode(' && ', array_map(trim(...), $lines));
    }

    /** @param array<string, mixed> $manifest */
    private static function requirement(array $manifest, string $package): ?string
    {
        $constraint = $manifest['require'][$package] ?? null;

        return is_string($constraint) ? $constraint : null;
    }

    /**
     * The PHP the installed core requires, out of that package's own manifest.
     *
     * A third number rather than a second read of the first two. The project
     * states what it accepts, the environment states what it runs, and this is
     * the lowest a package installed beside this core may declare. It does not
     * derive from the major. So an extension with the container's PHP as its
     * minimum narrows its own range with every check still green (`D-KNW-055`).
     */
    private static function corePhpConstraint(): ?string
    {
        $core = Instance::packages()['core'] ?? null;

        return $core === null ? null : self::requirement(Data::json($core . '/composer.json'), 'php');
    }

    /**
     * How the four PHP numbers beside it stand to each other.
     *
     * The first two comparisons are against what this project declares, because
     * that is what a rewrite of a manifest can change (`D-ANS-082`). The third
     * is against the bound instead, because that one is not a declaration at
     * all. It is the line the declared commands abort on, and what decides
     * whether they abort is the interpreter that would run them (`D-ANS-086`).
     * Nothing ran to find any of it out. This is what the files say, never
     * evidence that the floor works. Null where the declared constraint names
     * no floor.
     *
     * @return array{floor: string, coreFloor: ?string, againstCore: ?string, inEnvironment: ?string, bound: ?string, environmentAgainstBound: ?string}|null
     */
    private static function phpRelation(?string $declared, ?string $core, ?string $environment, ?string $bound): ?array
    {
        $floor = Versions::floor($declared);
        if ($floor === null) {
            return null;
        }
        $coreFloor = Versions::floor($core);
        // The bound is an exact version and a DDEV php_version is major.minor,
        // so both read to the depth the shallower one has.
        $boundFloor = Versions::floor($bound);

        return [
            'floor' => $floor,
            'coreFloor' => $coreFloor,
            'againstCore' => $coreFloor === null ? null : self::sits($floor, $coreFloor),
            'inEnvironment' => $environment === null ? null : self::sits($environment, $floor),
            'bound' => $boundFloor,
            'environmentAgainstBound' => self::againstBound($environment, $bound),
        ];
    }

    /**
     * Where the interpreter that would run the declared commands sits against
     * the bound the install carries, or null where either is absent.
     *
     * Public because the answer states this twice and may compute it once. The
     * relation belongs among the numbers, and whether a command starts belongs
     * beside the commands. That also stands where no declared floor left a
     * `phpRelation` to carry it.
     */
    public static function againstBound(?string $environment, ?string $bound): ?string
    {
        $boundFloor = Versions::floor($bound);

        return $boundFloor === null || $environment === null ? null : self::sits($environment, $boundFloor);
    }

    /**
     * Where one version sits against another, read to the depth both of them
     * state.
     *
     * The PHP numbers are `major.minor` on both sides and the depth never
     * bites. A Node version is where it does. An `.nvmrc` says 24 and a
     * workflow says 24.19.0, and compared as written the pin would come out
     * below the version that satisfies it.
     */
    public static function sits(string $version, string $against): string
    {
        $depth = min(substr_count($version, '.'), substr_count($against, '.')) + 1;
        $shared = static fn(string $stated): string => implode(
            '.',
            array_slice(explode('.', $stated), 0, $depth),
        );

        return match (version_compare($shared($version), $shared($against))) {
            -1 => self::BELOW,
            1 => self::ABOVE,
            default => self::SAME,
        };
    }

    /**
     * What this repository declares it needs of the core, straight from its
     * root manifest.
     *
     * `describe()` returns it too, but everything else there costs file reads
     * this has no use for. The version an answer composes for settles before
     * any of it.
     */
    public static function coreConstraint(): ?string
    {
        $instance = Instance::describe();
        if ($instance === null) {
            return null;
        }

        return self::requirement(Data::json($instance['root'] . '/composer.json'), 'typo3/cms-core');
    }

    private static function relative(string $root, string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $root = str_replace('\\', '/', $root) . '/';

        return str_starts_with($path, $root) ? substr($path, strlen($root)) : $path;
    }
}
