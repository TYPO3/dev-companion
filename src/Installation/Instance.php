<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;

/**
 * The TYPO3 installation the caller works in, if there is one.
 *
 * Most of this server answers from bundled knowledge. This class supplies the
 * exception for questions whose answer belongs to an installation. Registered
 * icons and labels, and the backend CSS, JavaScript, and styleguide files that
 * define its component contract. A snapshot of one core revision cannot be the
 * primary answer for those.
 *
 * Discovery is opt-in per entrypoint and never derives from getcwd() on its
 * own. Only bin/typo3-dev-companion hands its working directory in, because
 * only there is that directory the agent's own. The client launches the server
 * as a subprocess of the session it works in. An HTTP endpoint has no such
 * relationship to its callers. Its document root may itself sit inside a TYPO3
 * installation, and an answer from cwd would then report that installation to
 * every remote caller. Hence the flag rather than a lookup.
 */
final class Instance
{
    /** A core monorepo checkout: system extensions live in typo3/sysext/. */
    public const KIND_CORE_CHECKOUT = 'core-checkout';

    /** A Composer-installed project: packages live where Composer put them. */
    public const KIND_COMPOSER_PROJECT = 'composer-project';

    /**
     * A repository whose root manifest declares an extension. Says where the
     * work is and never that there is an installation to read.
     */
    public const KIND_EXTENSION_REPOSITORY = 'extension-repository';

    /**
     * Every package composer.lock names is below the vendor directory at that
     * version.
     */
    public const LOCK_MATCHES = 'matches';

    /** One of them is at another version, absent, or installed and locked nowhere. */
    public const LOCK_DIFFERS = 'differs';

    /** There is a lock and no installed metadata below the vendor directory to hold it against. */
    public const LOCK_NOT_INSTALLED = 'not-installed';

    /** This root has no composer.lock, so nothing here states which versions it fixed. */
    public const LOCK_ABSENT = 'no-lock';

    /** Found by a walk up from the directory the server started in. */
    public const VIA_DISCOVERY = 'discovery';

    /** Named outright by the caller, and then not searched for at all. */
    public const VIA_ENVIRONMENT = 'environment';

    /** Names the installation to read, whatever the working directory says. */
    public const ROOT_VARIABLE = 'TYPO3_DEV_COMPANION_ROOT';

    /** How far up from the start directory to look before the search stops. */
    private const MAX_DEPTH = 12;

    /**
     * The two package types TYPO3's Composer installer places.
     *
     * @var array<int, string>
     */
    private const PACKAGE_TYPES = ['typo3-cms-framework', 'typo3-cms-extension'];

    /**
     * The name of every package TYPO3 itself ships: typo3/cms-core,
     * typo3/cms-fluid.
     */
    private const CORE_PACKAGE_PREFIX = 'typo3/cms-';

    private static ?string $startingDirectory = null;

    /** @var array{root: string, kind: string, startedFrom: string, via: string}|null|false false = not resolved yet */
    private static array|false|null $resolved = false;

    private static string $misconfiguration = '';

    /** @var array<int, string> The directories the last search walked. */
    private static array $searched = [];

    private static ?string $startedIn = null;

    private static ?string $startedInPackage = null;

    /**
     * Hands the working directory the server started in to the discovery.
     * Called by the stdio entrypoint and by nothing else.
     */
    public static function discoverFrom(?string $directory): void
    {
        self::$startingDirectory = $directory === null || $directory === '' ? null : $directory;
        self::$resolved = false;
        self::$startedIn = null;
        self::$startedInPackage = null;
    }

    /**
     * The working directory the server started in, when an entrypoint handed
     * one in, and null otherwise.
     *
     * Unlike root() this says nothing about an installation. It is only where
     * the caller's session was, which is worth a note even when nothing turned
     * up there. The same restriction applies as everywhere else in this class.
     * It is the agent's directory only because the stdio entrypoint says so. An
     * endpoint that never calls discoverFrom() gets null rather than its own
     * document root.
     */
    public static function startedFrom(): ?string
    {
        return self::$startingDirectory;
    }

    /**
     * The kind of repository the session stands in, or null where nothing
     * places it. Not always the installation under read, and not always an
     * installation at all.
     *
     * `TYPO3_DEV_COMPANION_ROOT` names the one to read, and which repository
     * the work is in is a different question. A read of both off one value let
     * the variable move that boundary too (`D-SCO-005`). Where the walk-up
     * reaches no installation the named one is the only evidence there is and
     * it answers (`D-DIS-006`).
     */
    public static function startedIn(): ?string
    {
        if (self::$startedIn !== null) {
            return self::$startedIn;
        }

        // The root manifest before anything installed under it. It is a
        // decision somebody wrote down rather than a directory that has to fill
        // up. So it places an extension repository in the state of a fresh
        // clone, and it places it the same way once it fills (`D-SCO-012`).
        if (self::startedInPackage() !== null) {
            return self::$startedIn = self::KIND_EXTENSION_REPOSITORY;
        }

        $instance = self::describe();
        if (($instance['via'] ?? '') !== self::VIA_ENVIRONMENT) {
            return self::$startedIn = $instance['kind'] ?? null;
        }

        $walked = [];
        $located = self::$startingDirectory === null
            ? null
            : self::locate(self::$startingDirectory, $walked);

        return self::$startedIn = $located['kind'] ?? $instance['kind'];
    }

    /**
     * The extension key the repository the session stands in declares at its
     * own root. Null where the walk up from the start directory reaches no such
     * root.
     *
     * A repository is not an installation. What the manifest says is enough to
     * place the work and never enough to report one. So nothing here starts to
     * speak for a checkout with no console behind it (`D-DIS-001`). Only
     * `typo3-cms-extension`. `typo3-cms-framework` would place a contributor
     * who stands in `typo3/sysext/backend/` outside the core.
     */
    public static function startedInPackage(): ?string
    {
        if (self::$startedInPackage !== null) {
            return self::$startedInPackage;
        }

        $directory = self::$startingDirectory === null ? false : realpath(self::$startingDirectory);
        for ($depth = 0; $directory !== false && $depth < self::MAX_DEPTH; ++$depth) {
            $manifest = self::readJson($directory . '/composer.json');
            if (($manifest['type'] ?? '') === 'typo3-cms-extension') {
                return self::$startedInPackage = self::extensionKey($manifest);
            }

            $parent = dirname($directory);
            if ($parent === $directory) {
                break;
            }
            $directory = $parent;
        }

        return null;
    }

    /** Whether an installation turned up to read from. */
    public static function isAvailable(): bool
    {
        return self::describe() !== null;
    }

    /** Absolute path of the installation, or null when there is none to read. */
    public static function root(): ?string
    {
        return self::describe()['root'] ?? null;
    }

    /**
     * What turned up, how, and where the search started, so a caller can tell
     * whether the server reads the installation it means. A silently wrong
     * instance would be worse than none at all.
     *
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    public static function describe(): ?array
    {
        // Only a success stays in memory. A failure runs again on the next
        // call, because an installation that appears during a session is the
        // ordinary case, not the exotic one. The agent runs composer install,
        // or starts the containers, and does it precisely because the answer
        // said there was nothing to read. A memory of "nothing" outlives the
        // reason for it, and the caller has no way to tell. The session would
        // have to restart to get an answer that is already true.
        if (is_array(self::$resolved)) {
            return self::$resolved;
        }

        self::$searched = [];
        [$configured, self::$misconfiguration] = self::fromEnvironment();
        if ($configured !== null || self::$misconfiguration !== '') {
            // A configured root that does not work gets no quiet replacement by
            // a discovered one. The caller stated which installation it means,
            // and an answer about a different one is the failure this whole
            // class exists to avoid.
            return self::$resolved = $configured;
        }

        $located = self::$startingDirectory === null
            ? null
            : self::locate(self::$startingDirectory, self::$searched);

        return $located === null ? null : self::$resolved = $located;
    }

    /**
     * The repository the session stands in, whether or not anything has
     * installed in it. The installation where there is one, and otherwise the
     * nearest root whose own composer.json declares TYPO3.
     *
     * A gate of the whole answer on installed metadata left a fresh clone as
     * the one state it answered nothing in. That is the state the installation
     * workflow starts in (`D-ANS-085`). Not an installation, and nothing else
     * may read it as one. `describe()` stays what every other tool asks
     * (`D-DIS-001`). Nothing stays in memory here, because the answer has to
     * change the moment the install this state exists to prompt has run.
     *
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    public static function project(): ?array
    {
        $instance = self::describe();
        // A named root that does not work gets no quiet replacement by a
        // discovered one here either. The caller said which repository it
        // means.
        if ($instance !== null || self::$misconfiguration !== '') {
            return $instance;
        }
        if (self::$startingDirectory === null) {
            return null;
        }

        $walked = [];

        return self::walkUp(
            self::$startingDirectory,
            $walked,
            static fn(string $directory): ?string => self::declaresTypo3($directory) ? self::KIND_COMPOSER_PROJECT : null,
        );
    }

    /**
     * Whether this directory's own composer.json declares TYPO3, which is what
     * identifies a project root before anything installs in it.
     *
     * A declaration in all three shapes rather than a name or a layout. The
     * root is a TYPO3 package itself, it requires one of TYPO3's own packages,
     * or it carries the `extra.typo3/cms` block TYPO3's Composer installer
     * reads. A rule that admits any composer.json would report a TYPO3 project
     * for every PHP repository up to twelve directories above the caller.
     *
     * Read on 2026-08-18 against the two shapes it has to cover. The `t3g/blog`
     * extension repository of `feedback/2026-08-18-070333` declares
     * `typo3-cms-extension`, `extra.typo3/cms.extension-key` and a required
     * `typo3/cms-core`. The site installations below `.environments/` declare
     * `"type": "project"` and twenty-six required `typo3/cms-*` packages with
     * no `extra` block at all. So the package types alone would walk past a
     * site.
     */
    private static function declaresTypo3(string $directory): bool
    {
        $manifest = self::readJson($directory . '/composer.json');
        if (in_array($manifest['type'] ?? '', self::PACKAGE_TYPES, true) || isset($manifest['extra']['typo3/cms'])) {
            return true;
        }

        // What it needs of TYPO3 for development at all counts as much as what
        // it needs to run. An extension that installs the core for its test
        // setup alone is a TYPO3 repository. A requirement on one of TYPO3's
        // tools, typo3/coding-standards, typo3/tailor, is not a requirement on
        // TYPO3.
        foreach (['require', 'require-dev'] as $section) {
            $required = $manifest[$section] ?? null;
            foreach (is_array($required) ? array_keys($required) : [] as $package) {
                if (str_starts_with((string) $package, self::CORE_PACKAGE_PREFIX)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * What is wrong with the configuration, if anything. Empty otherwise.
     *
     * A variable somebody set and that did not work has to stand out loud.
     * Silence would look exactly like a variable never set, which is the one
     * thing the caller knows it did.
     */
    public static function misconfiguration(): string
    {
        self::describe();

        return self::$misconfiguration;
    }

    /**
     * The directories the search walked, so a failure can be told apart from a
     * server started in the wrong place.
     *
     * "No installation turned up" is two very different situations in one
     * sentence. A layout this cannot read, and a client that launched the
     * server somewhere else entirely. Which one it is follows from where it
     * looked, and from nothing else the caller has access to.
     *
     * @return array<int, string>
     */
    public static function searched(): array
    {
        self::describe();

        return self::$searched;
    }

    /**
     * The installation named outright, for the layouts discovery cannot reach.
     * An installation in a subdirectory, a checkout the client starts the
     * server beside rather than inside, a stack this server has never heard of.
     *
     * Unlike the working directory this derives from nothing, it is a decision
     * someone made. So it holds for every entrypoint, the HTTP one included,
     * where it is the only way to name an installation at all.
     *
     * @return array{0: array{root: string, kind: string, startedFrom: string, via: string}|null, 1: string}
     */
    private static function fromEnvironment(): array
    {
        $configured = getenv(self::ROOT_VARIABLE);
        if (!is_string($configured) || trim($configured) === '') {
            return [null, ''];
        }

        $configured = trim($configured);
        $root = realpath($configured);
        if ($root === false || !is_dir($root)) {
            return [null, sprintf(
                '%s is set to "%s", which is not a directory on this machine',
                self::ROOT_VARIABLE,
                $configured
            )];
        }

        return [[
            'root' => $root,
            'kind' => (self::readJson($root . '/composer.json')['type'] ?? '') === 'typo3-cms-core'
                ? self::KIND_CORE_CHECKOUT
                : self::KIND_COMPOSER_PROJECT,
            'startedFrom' => $configured,
            'via' => self::VIA_ENVIRONMENT,
        ], ''];
    }

    /**
     * The installed TYPO3 packages, extension key to absolute path.
     *
     * This is what makes the answer instance-correct rather than core-only: a
     * project's own extensions register icons and ship labels exactly like a
     * system extension does.
     *
     * @return array<string, string>
     */
    public static function packages(): array
    {
        $instance = self::describe();
        if ($instance === null) {
            return [];
        }

        $packages = $instance['kind'] === self::KIND_CORE_CHECKOUT
            ? self::systemExtensions($instance['root'])
            : self::composerPackages($instance['root']);

        ksort($packages);

        return $packages;
    }

    /**
     * The TYPO3 version of this installation, or null when there is none to
     * read.
     *
     * Read from the core package's Typo3Version class rather than from the
     * console. The version decides whether an answer holds, so it has to be
     * there exactly when the console is not. An installation whose database has
     * no schema still has a version, and that number is what keeps a v15 answer
     * away from a v13 caller.
     */
    public static function typo3Version(): ?string
    {
        $core = self::packages()['core'] ?? null;
        if ($core === null) {
            return null;
        }

        $file = $core . '/Classes/Information/Typo3Version.php';
        if (!is_file($file)) {
            return null;
        }

        if (preg_match('/const\s+VERSION\s*=\s*[\'"]([^\'"]+)[\'"]/', (string) file_get_contents($file), $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }

    /**
     * The lowest PHP the packages installed below this root accept, or null
     * where nothing bounds them.
     *
     * `composer install` writes the lower bound over every package it installed
     * into `composer/platform_check.php` below the vendor directory, and the
     * autoloader includes it. So this is not a second opinion about which
     * interpreter may run here, it is the code that stops one. No manifest
     * field states it. A fixer required for development alone raises it in a
     * repository whose own `require.php` says two minors less.
     *
     * Read against Composer 2.9.5 on 2026-08-04, in its own
     * `AutoloadGenerator::getPlatformCheck()` and in the four installations
     * under `.environments/`. The expression is `PHP_VERSION_ID >= 80500`, an
     * integer of major*10000 + minor*100 + patch, with `>` where the bound is
     * exclusive.
     *
     * Absent is a real answer and not a failure. Composer leaves the file out
     * when nothing requires a PHP version, and deletes it where
     * `platform-check` is off or the install ignored the platform requirements.
     * In none of those does a version stop anything, so nothing here may say
     * one might.
     */
    public static function installedPhpBound(string $root): ?string
    {
        $path = self::vendorDirectory($root) . '/composer/platform_check.php';
        if (!is_file($path)) {
            return null;
        }

        $generated = (string) file_get_contents($path);
        if (preg_match('/PHP_VERSION_ID\s*(>=|>)\s*(\d+)/', $generated, $matches) !== 1) {
            return null;
        }

        // PHP_VERSION_ID is an integer, so "greater than 80500" is "at least
        // 80501" exactly rather than approximately. The operator does not have
        // to travel any further than this line.
        $id = (int) $matches[2] + ($matches[1] === '>' ? 1 : 0);

        return $id <= 0 ? null : sprintf('%d.%d.%d', intdiv($id, 10000), intdiv($id % 10000, 100), $id % 100);
    }

    /**
     * Where the packages installed below this root and the ones composer.lock
     * names disagree, which nothing else in an answer about this project says.
     *
     * A vendor directory older than the lock satisfies `installed`. The suite
     * run that follows fails in classes the caller's own change never touched
     * (`D-ANS-102`). The comparison is the versions, not the modification
     * times. A lock a rebase wrote that changed nothing in it is newer than the
     * install it describes, and nothing there is stale.
     *
     * Locked dev packages count only where `installed.json` says the install
     * took them. `composer install --no-dev` leaves every one of them locked and
     * absent, which is a deployment rather than a drift.
     *
     * @return array{state: string, packages: array<int, array{package: string, locked: ?string, installed: ?string}>}
     */
    public static function installedAgainstLock(string $root): array
    {
        $lock = self::readJson($root . '/composer.lock');
        if ($lock === []) {
            return ['state' => self::LOCK_ABSENT, 'packages' => []];
        }

        $file = self::vendorDirectory($root) . '/composer/installed.json';
        if (!is_file($file)) {
            return ['state' => self::LOCK_NOT_INSTALLED, 'packages' => []];
        }

        $metadata = self::readJson($file);
        $installed = self::lockedVersions($metadata['packages'] ?? $metadata);
        $locked = self::lockedVersions($lock['packages'] ?? []);
        $lockedForDevelopment = self::lockedVersions($lock['packages-dev'] ?? []);
        $expected = ($metadata['dev'] ?? true) === false ? $locked : $locked + $lockedForDevelopment;

        $drift = [];
        foreach ($expected as $package => $version) {
            if (!array_key_exists($package, $installed)) {
                $drift[] = ['package' => $package, 'locked' => $version, 'installed' => null];

                continue;
            }
            // An entry that states no version is one this cannot hold against
            // anything, and it is there either way.
            if ($installed[$package] !== null && $version !== null && $installed[$package] !== $version) {
                $drift[] = ['package' => $package, 'locked' => $version, 'installed' => $installed[$package]];
            }
        }
        foreach ($installed as $package => $version) {
            // Held against both halves of the lock whichever of them installed.
            // A dev package the install left out is the absent case above, and
            // a report of it here as well would say it twice.
            if (!array_key_exists($package, $locked) && !array_key_exists($package, $lockedForDevelopment)) {
                $drift[] = ['package' => $package, 'locked' => null, 'installed' => $version];
            }
        }
        usort($drift, static fn(array $one, array $other): int => strcmp($one['package'], $other['package']));

        return ['state' => $drift === [] ? self::LOCK_MATCHES : self::LOCK_DIFFERS, 'packages' => $drift];
    }

    /**
     * The version each entry states, by package name — the shape composer.lock
     * and installed.json both write their package lists in.
     *
     * @return array<string, ?string>
     */
    private static function lockedVersions(mixed $entries): array
    {
        $versions = [];
        foreach (is_array($entries) ? $entries : [] as $entry) {
            $name = is_array($entry) ? $entry['name'] ?? null : null;
            if (is_string($name) && $name !== '') {
                $versions[$name] = is_string($entry['version'] ?? null) ? $entry['version'] : null;
            }
        }

        return $versions;
    }

    /** The major of that version as a number, for a comparison with another. */
    public static function typo3Major(): ?int
    {
        $version = self::typo3Version();

        return $version === null ? null : (int) $version;
    }

    /**
     * Whether an extension key is a system extension of this installation, or
     * null when the installation does not have it, or when there is none.
     *
     * Where a package lives is what decides it, because the key alone cannot. A
     * system extension is below typo3/sysext/ in a core checkout and comes from
     * typo3/cms-* in a Composer project, and everything else is somebody's
     * extension.
     */
    public static function isSystemExtension(string $key): ?bool
    {
        $path = self::packages()[$key] ?? null;
        if ($path === null) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);

        return str_contains($normalized, '/typo3/sysext/') || str_contains($normalized, '/typo3/cms-');
    }

    /**
     * The installation itself declares both its kind and the packages in it, so
     * neither is a guess here. The monorepo root declares "type":
     * "typo3-cms-core". A Composer installation lists every TYPO3 package in
     * composer/installed.json below the vendor directory it declares, the same
     * source TYPO3's own PackageArtifactBuilder reads.
     *
     * @param array<int, string> $walked the directories it looked in, in order
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    private static function locate(string $startingDirectory, array &$walked): ?array
    {
        return self::walkUp($startingDirectory, $walked, static fn(string $directory): ?string => match (true) {
            (self::readJson($directory . '/composer.json')['type'] ?? '') === 'typo3-cms-core' => self::KIND_CORE_CHECKOUT,
            self::composerPackages($directory) !== [] => self::KIND_COMPOSER_PROJECT,
            default => null,
        });
    }

    /**
     * The nearest directory up from the start one that the caller's rule
     * recognises, and what it recognised it as.
     *
     * The walk reports through $walked rather than into the diagnostic itself.
     * startedIn() walks for a question of its own, and the directories the
     * caller sees have to stay the ones searched for the installation under
     * read.
     *
     * @param array<int, string> $walked the directories it looked in, in order
     * @param callable(string): ?string $kindOf the kind that directory is, or null where it is none
     * @return array{root: string, kind: string, startedFrom: string, via: string}|null
     */
    private static function walkUp(string $startingDirectory, array &$walked, callable $kindOf): ?array
    {
        $directory = realpath($startingDirectory);
        if ($directory === false) {
            return null;
        }
        $startedFrom = $directory;

        for ($depth = 0; $depth < self::MAX_DEPTH; ++$depth) {
            $walked[] = $directory;
            $kind = $kindOf($directory);
            if ($kind !== null) {
                return [
                    'root' => $directory,
                    'kind' => $kind,
                    'startedFrom' => $startedFrom,
                    'via' => self::VIA_DISCOVERY,
                ];
            }

            $parent = dirname($directory);
            if ($parent === $directory) {
                break;
            }
            $directory = $parent;
        }

        return null;
    }

    /**
     * The system extensions of a monorepo checkout, each under the extension
     * key it declares rather than under its directory name.
     *
     * @return array<string, string>
     */
    private static function systemExtensions(string $root): array
    {
        $sysext = $root . '/typo3/sysext';
        if (!is_dir($sysext)) {
            return [];
        }

        $packages = [];
        foreach (Finder::create()->files()->in($sysext)->depth(1)->name('composer.json')->sortByName() as $manifest) {
            $declared = self::readJson($manifest->getPathname());
            if (($declared['type'] ?? '') !== 'typo3-cms-framework') {
                continue;
            }

            $path = dirname($manifest->getPathname());
            $key = $declared['extra']['typo3/cms']['extension-key'] ?? basename($path);
            $packages[(string) $key] = $path;
        }

        return $packages;
    }

    /** @return array<string, mixed> */
    private static function readJson(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Where this root keeps its dependencies.
     *
     * Composer's default is vendor/, and a root that declares nothing resolves
     * exactly as before. But the layout the TYPO3 extension test setup produces
     * moves it, `"vendor-dir": ".build/vendor"`. An installation whose metadata
     * the search looks for in the wrong place does not exist for this server.
     * Discovery walks past it and every question only it could answer stays
     * open.
     */
    private static function vendorDirectory(string $root): string
    {
        $configured = self::readJson($root . '/composer.json')['config']['vendor-dir'] ?? null;
        if (!is_string($configured) || trim($configured) === '') {
            return $root . '/vendor';
        }

        $configured = rtrim(trim($configured), '/');

        return str_starts_with($configured, '/') ? $configured : $root . '/' . $configured;
    }

    /**
     * The TYPO3 packages a Composer installation declares, read from the same
     * metadata TYPO3's own PackageArtifactBuilder reads. Only the two TYPO3
     * package types count; every other dependency is irrelevant here.
     *
     * A project's own extensions appear alongside the system extensions, which
     * is the point: they register icons and ship labels in exactly the same way.
     *
     * @return array<string, string>
     */
    private static function composerPackages(string $root): array
    {
        $vendor = self::vendorDirectory($root);
        $decoded = self::readJson($vendor . '/composer/installed.json');
        $entries = $decoded['packages'] ?? $decoded;
        if (!is_array($entries)) {
            return [];
        }

        $packages = [];
        foreach ($entries as $entry) {
            if (!is_array($entry) || !in_array($entry['type'] ?? '', self::PACKAGE_TYPES, true)) {
                continue;
            }

            $key = self::extensionKey($entry);
            $relative = (string) ($entry['install-path'] ?? '');
            $path = $relative === '' ? false : realpath($vendor . '/composer/' . $relative);
            if ($key !== null && $path !== false) {
                $packages[$key] = $path;
            }
        }

        // The extension under work is the root package, and Composer lists
        // dependencies rather than the root. So in an extension development
        // checkout the one package the agent edits would be the only one absent
        // from its own answers. It joins only when the installation around it
        // is real. A package list of nothing but the root would report an
        // installation where there is only a repository.
        if ($packages !== []) {
            $rootKey = self::rootPackage($root);
            if ($rootKey !== null) {
                $packages[$rootKey] = $root;
            }
        }

        return $packages;
    }

    /**
     * The extension key the root itself declares, when the root is a TYPO3
     * package rather than a project.
     */
    private static function rootPackage(string $root): ?string
    {
        $manifest = self::readJson($root . '/composer.json');

        return in_array($manifest['type'] ?? '', self::PACKAGE_TYPES, true)
            ? self::extensionKey($manifest)
            : null;
    }

    /**
     * The extension key a TYPO3 package declares, wherever it came from: a root
     * manifest, or one entry of Composer's installed metadata.
     *
     * @param array<string, mixed> $manifest
     */
    private static function extensionKey(array $manifest): ?string
    {
        $key = $manifest['extra']['typo3/cms']['extension-key'] ?? null;
        if (is_string($key) && $key !== '') {
            return $key;
        }

        // Without a declared key, Composer's TYPO3 installer derives one from
        // the second half of the package name.
        $name = (string) ($manifest['name'] ?? '');
        $derived = str_replace('-', '_', substr($name, (int) strrpos($name, '/') + 1));

        return $derived === '' ? null : $derived;
    }
}
