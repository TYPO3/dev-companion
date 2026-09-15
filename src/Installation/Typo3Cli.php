<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Process\SystemRunner;

/**
 * Runs the TYPO3 console of the discovered installation and hands back what it
 * printed.
 *
 * A question to TYPO3 beats a second implementation of it. The registries this
 * server answers about, which labels exist, which namespaces exist, assemble at
 * runtime from the active packages. The console commands that expose them
 * (language:domain:list, fluid:namespaces) already know things no file scan can
 * work out. Which packages are active rather than merely installed, and which
 * language files an installation overrides through resourceOverrides.
 *
 * It runs as a subprocess, never in this process. A load of the installation's
 * autoloader here would put two Composer autoloaders into one process. Two sets
 * of Symfony and PSR classes under the same class names would come with them. A
 * failed platform check would take the whole MCP session down instead of one
 * answer. A subprocess brings its own interpreter and its own autoloader, and
 * fails as an exit code.
 */
final class Typo3Cli
{
    /** Invoked through DDEV, which supplies the PHP version the project declares. */
    public const VIA_DDEV = 'ddev';

    /** Invoked directly with an interpreter on this machine. */
    public const VIA_PHP = 'php';

    /** Invoked exactly as the caller said to invoke it. */
    public const VIA_OVERRIDE = 'override';

    /** The command that reaches the console, for the layouts autodiscovery cannot. */
    public const CONSOLE_VARIABLE = 'TYPO3_DEV_COMPANION_CONSOLE';

    /** A TYPO3 bootstrap can hang on a broken configuration; an MCP session must not. */
    private const TIMEOUT_SECONDS = 90;

    /**
     * What leaves this process, and the seam a unit test takes instead.
     *
     * Static because everything on this class is, and settable because
     * `R-COD-003` says a unit test stubs what it needs from outside rather than
     * starts it. `forget()` leaves it alone on purpose. That one drops the
     * memoized resolution, which a test does between installations and which
     * would otherwise take the stub with it.
     */
    private static ?CommandRunner $runner = null;

    /** What a test hands in, so nothing it drives has to exist on the machine. */
    public static function useRunner(?CommandRunner $runner): void
    {
        self::$runner = $runner;
    }

    /**
     * Where DDEV mounts the project inside its web container.
     *
     * The invocation has to be absolute. `ddev exec` runs in the container's
     * configured working directory, which a project may point at its docroot,
     * and `D-DIS-002` measured exit 127 for exactly that. `$DDEV_APPROOT` is
     * not the safer form. DDEV sets it in the web container only from v1.24.5,
     * and below that it expands to nothing.
     */
    private const DDEV_APPROOT = '/var/www/html';

    /** @var array{command: array<int, string>, via: string, php: string}|null|false */
    private static array|false|null $resolved = false;

    private static string $reason = '';

    private static string $caveat = '';

    /**
     * How to invoke this installation's console, or null when nobody can.
     *
     * @return array{command: array<int, string>, via: string, php: string}|null
     */
    public static function resolve(): ?array
    {
        // Only a success with no limit on it stays in memory. A failure and a
        // caveated success both run again on the next call. The usual reason
        // for either is a DDEV project that is not up yet. The caller who reads
        // that and starts it has to be able to ask again in the same session.
        if (is_array(self::$resolved)) {
            return self::$resolved;
        }

        self::$resolved = null;
        self::$reason = '';
        self::$caveat = '';

        $instance = Instance::describe();
        if ($instance === null) {
            self::$reason = 'no TYPO3 installation was found to run it in';

            return null;
        }

        $root = $instance['root'];

        [$invocation, $overrideReason] = self::viaOverride();
        if ($invocation !== null) {
            return self::$resolved = $invocation;
        }
        if ($overrideReason !== '') {
            // A stated command that does not work gets no quiet replacement by
            // a discovered one. The caller would then get an answer from
            // something other than what it named, and never learn its setting
            // went unread.
            self::$reason = $overrideReason;

            return null;
        }

        $binary = self::consoleBinary($root);
        if ($binary === null) {
            // Every probed path by name rather than the two defaults. A console
            // that sits somewhere else is the likely case here. A reason that
            // lists where this looked is one the caller can act on.
            self::$reason = sprintf(
                '%s has no TYPO3 console — none of %s exists%s%s',
                $root,
                implode(', ', self::consoleCandidates($root)),
                self::withoutDependencies($root),
                self::unreachableBinDirectory($root)
            );

            return null;
        }

        [$invocation, $ddevReason] = self::viaDdev($root, $binary);
        if ($invocation !== null) {
            return self::$resolved = $invocation;
        }

        [$invocation, $phpReason] = self::viaPhp($root, $binary);

        // A project that ships a DDEV setup runs there by design, so its reason
        // wins over the direct one. "Start the project" is something the caller
        // can act on, "install another PHP" is not.
        if ($invocation === null) {
            self::$reason = $ddevReason !== '' ? $ddevReason : $phpReason;

            return null;
        }

        // An interpreter on this machine reaches the console of a project that
        // runs elsewhere by design. That is not "unreachable", and it is not
        // "reachable" either. But what it costs is not the list of tools this
        // sentence used to name. Driven against `.environments/e-site-14.3`
        // with its DDEV project down on 2026-08-04, all seven
        // installation-backed tools answered. Byte for byte what the same calls
        // answered through `ddev exec` with the project up. That was on a host
        // PHP 8.3 with no database driver compiled in at all. The boot of TYPO3
        // is not what a stopped project takes away; the runtime the project
        // declares is. Which answer meets that limit is a property of the
        // installation rather than of the tool in question. Naming the way out
        // is half of it. The caller that acts on it holds an answer from the
        // weaker source, and nothing revises that answer where it stands. So
        // the step that ends the state is the second call. A caveat that names
        // only the first leaves the session at work on what it heard to stop to
        // trust. `R-DIS-010`.
        self::$caveat = $ddevReason === '' ? '' : $ddevReason
            . '. Until then the console runs on an interpreter of this machine rather than in the runtime the '
            . 'project declares. What TYPO3 assembles from its own files answers as it would there; what needs '
            . 'the services that runtime brings, its database first of all, may not. An answer that needs one '
            . 'says so rather than coming back thin. Ask again once it is up: this answer stands as it was '
            . 'given, and the runtime the project declares reaches only the calls that come after the start';

        // A caveated resolution is the weaker of two and the stronger one
        // arrives during the session, so it stays out of memory. `R-DIS-009`,
        // one state over from the negative behind it. Measured in one process
        // against `.environments/e-site-13.4` down on 2026-08-04. Host PHP 8.3
        // satisfies the 8.2.0 that installation pins, so the resolution
        // succeeded through it and stayed in memory. `ddev start` changed
        // nothing until the process ended. What it costs is one `ddev describe
        // -j` per call while the project is down, 0.25s there. That is what a
        // failed resolution already pays on that path.
        if (self::$caveat !== '') {
            return $invocation;
        }

        return self::$resolved = $invocation;
    }

    public static function isAvailable(): bool
    {
        return self::resolve() !== null;
    }

    /**
     * Drops the memoized resolution. Discovery of the installation happens once
     * per process in normal use, so this exists for tests that move between
     * installations within one.
     */
    public static function forget(): void
    {
        self::$resolved = false;
        self::$reason = '';
        self::$caveat = '';
    }

    /** Why nobody can invoke the console. Empty once it can. */
    public static function reason(): string
    {
        self::resolve();

        return self::$reason;
    }

    /**
     * What limits the console that turned up. Empty when nothing does.
     *
     * Reachable is not one state. An interpreter of this machine answers for a
     * project whose containers are down, and that is not the runtime the
     * project declares. What it says is what the boot cannot reach, never which
     * lookups are lost. Which answer a stopped project costs depends on the
     * installation, so a sentence that names tools is wrong somewhere
     * (`D-DIS-012`).
     */
    public static function caveat(): string
    {
        self::resolve();

        return self::$caveat;
    }

    /**
     * What a failure means, where the message alone does not say it.
     *
     * A console that starts and then fails on an absent table has a specific
     * remedy, and the caller cannot see it in the stack trace. The code is
     * there, the database behind it is not. Empty for everything else, because
     * a guess at the rest would bury the one diagnosis worth its place.
     */
    public static function diagnose(string $error): string
    {
        $needles = ['doesn\'t exist', 'base table or view not found', '42s02', 'no such table'];
        $haystack = mb_strtolower($error);
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return 'The console runs, so the code is installed — the database it connects to has no TYPO3 '
                    . 'schema yet. Import the dump, or run the setup, before asking again.';
            }
        }

        return '';
    }

    /**
     * Runs a console command and returns what it printed.
     *
     * @param array<int, string> $arguments
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public static function run(array $arguments): array
    {
        $invocation = self::resolve();
        if ($invocation === null) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => self::reason()];
        }

        // Non-interactive and unstyled, so the output is the data and not a
        // terminal painting of it.
        $arguments = array_merge($arguments, ['--no-interaction', '--no-ansi']);

        return self::execute(
            array_merge($invocation['command'], self::pastTheShell($invocation['via'], $arguments)),
            Instance::root() ?? getcwd() ?: '.',
        );
    }

    /**
     * Arguments that survive the one shell on the way to the console.
     *
     * `proc_open` gets an array and runs no shell, so nothing here would need
     * quotes. Except that `ddev exec` joins its arguments back into a line and
     * hands that to bash inside the container. An argument with a character
     * bash acts on is then bash's rather than the console's, and the command
     * dies before it reaches TYPO3. Measured against DDEV v1.25.1 on
     * 2026-08-02. `--regex=/(save)/i`, the argument `typo3_label_lookup` builds
     * for `language:domain:search`, comes back exit 2 with "syntax error near
     * unexpected token `('", every time, in every DDEV project. So that tool
     * never once answered from a booted installation there. It fell back to the
     * package files and said `answeredBy: "packages"`, which reads as a console
     * out of reach rather than as a quote fault.
     *
     * Only the DDEV transport gets quotes. The direct one reaches the console
     * through `proc_open` with no shell between, where a quoted argument would
     * arrive with its quotes. A stated one stays alone for a reason rather than
     * an oversight. What `TYPO3_DEV_COMPANION_CONSOLE` names may put a shell in
     * the way or may not, `docker compose exec` does not, another wrapper
     * might, and this cannot tell. Quotes would break every stated command that
     * needs none, to fix the ones that do. The caller who names a transport is
     * the one who can quote for it.
     *
     * @param array<int, string> $arguments
     * @return array<int, string>
     */
    private static function pastTheShell(string $via, array $arguments): array
    {
        return $via === self::VIA_DDEV ? array_map(escapeshellarg(...), $arguments) : $arguments;
    }

    /**
     * Runs PHP inside the installation and returns what it printed.
     *
     * The console answers what a command exists for. Everything else an
     * installation knows, its icon registry, its TCA, has no command. A read of
     * it means a boot of TYPO3 and a question to the container. That happens
     * here, in a subprocess, for the same reason every console call does. The
     * installation's autoloader, its PHP version and its extensions stay on the
     * other side of a process boundary.
     *
     * The code travels base64-encoded inside an `eval`, and that is not
     * decoration. `ddev exec` joins its arguments and hands the line to bash,
     * so a payload travels through one shell whose quotes nobody controls from
     * here. An encoded one carries no character that shell could act on.
     *
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    public static function php(string $code): array
    {
        $invocation = self::resolve();
        if ($invocation === null) {
            return ['ok' => false, 'exitCode' => -1, 'output' => '', 'error' => self::reason()];
        }

        $interpreter = self::interpreter($invocation);
        if ($interpreter === null) {
            return [
                'ok' => false,
                'exitCode' => -1,
                'output' => '',
                'error' => sprintf(
                    '%s names "%s", and no interpreter can be derived from it — only what a console command '
                        . 'answers is available here',
                    self::CONSOLE_VARIABLE,
                    implode(' ', $invocation['command'])
                ),
            ];
        }

        $command = array_merge($interpreter, ['-r', sprintf('eval(base64_decode("%s"));', base64_encode($code))]);

        return self::execute($command, Instance::root() ?? getcwd() ?: '.');
    }

    /**
     * The same way in, pointed at PHP itself rather than at the console.
     *
     * A stated console is a transport plus a binary, `ddev exec
     * .build/bin/typo3`, `docker compose exec web bin/typo3`. The transport is
     * what this server could never have worked out on its own. So the transport
     * stays and the binary changes, which is the opposite of an answer from
     * somewhere the caller did not name. It is the same machine, the same
     * container, one program along.
     *
     * @param array{command: array<int, string>, via: string, php: string} $invocation
     * @return array<int, string>|null
     */
    private static function interpreter(array $invocation): ?array
    {
        if ($invocation['via'] === self::VIA_PHP) {
            return [$invocation['command'][0]];
        }
        if ($invocation['via'] === self::VIA_DDEV) {
            return ['ddev', 'exec', '--', 'php'];
        }

        $command = $invocation['command'];
        // An invocation with nothing to run is not one an interpreter can come
        // out of, and every read below assumes there is a first word.
        if ($command === []) {
            return null;
        }
        // Already an interpreter in front: the console argument simply goes.
        if (str_starts_with(basename($command[0]), 'php')) {
            return [$command[0]];
        }
        $last = array_key_last($command);
        if ($last === 0 || !str_contains(basename($command[$last]), 'typo3')) {
            return null;
        }
        $command[$last] = 'php';

        return $command;
    }

    /**
     * Runs a command that speaks JSON and returns what it decoded.
     *
     * Some commands print a SymfonyStyle title before the payload and some
     * answer with a bare scalar. So a search finds the payload rather than an
     * assumption that it starts at the first byte or at a brace.
     *
     * What the console printed comes back whether or not it decoded. The exit
     * code cannot tell a caller which of two things happened when no payload
     * arrived. A console that exits 0 without one may have said "there is
     * nothing" in its own words, or may have said nothing at all. Only the
     * caller knows what its command prints in the first case.
     *
     * @param array<int, string> $arguments
     * @return array{ok: bool, data: mixed, error: string, exitCode: int, output: string}
     */
    public static function json(array $arguments): array
    {
        $result = self::run($arguments);
        $decoded = self::decode($result['output']);

        if ($decoded === null) {
            $error = $result['error'] !== '' ? $result['error'] : trim($result['output']);

            return [
                'ok' => false,
                'data' => null,
                'error' => $error !== '' ? $error : 'the console answered with something other than JSON',
                'exitCode' => $result['exitCode'],
                'output' => $result['output'],
            ];
        }

        return [
            'ok' => $result['ok'],
            'data' => $decoded,
            'error' => $result['error'],
            'exitCode' => $result['exitCode'],
            'output' => $result['output'],
        ];
    }

    /** Returns the decoded payload, or null when the output carries none. */
    private static function decode(string $output): mixed
    {
        $output = trim($output);
        if ($output === '') {
            return null;
        }

        foreach ([$output, self::fromFirstStructure($output)] as $candidate) {
            if ($candidate === null) {
                continue;
            }
            $decoded = json_decode($candidate, true);
            if ($decoded !== null || $candidate === 'null') {
                return $decoded;
            }
        }

        return null;
    }

    private static function fromFirstStructure(string $output): ?string
    {
        $offsets = array_filter([strpos($output, '{'), strpos($output, '[')], static fn($o): bool => $o !== false);

        return $offsets === [] ? null : substr($output, min($offsets));
    }

    /**
     * The command the caller stated, before any discovery.
     *
     * Discovery is a chain, a binary at a known path, then DDEV, then an
     * interpreter that satisfies the platform. Every link is something about a
     * machine this server does not control. When one breaks nothing remains to
     * try, and five tools go quiet over a layout its owner could have described
     * in a sentence. TYPO3_DEV_COMPANION_CONSOLE="ddev exec .build/bin/typo3".
     * Lando, a compose stack, a container this server has never heard of are
     * then all one setting rather than a feature request.
     *
     * The command runs as given, never through a shell, so quotes around a
     * multi-word argument are all the syntax there is.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaOverride(): array
    {
        $configured = getenv(self::CONSOLE_VARIABLE);
        if (!is_string($configured) || trim($configured) === '') {
            return [null, ''];
        }

        $command = self::tokenize($configured);
        if ($command === []) {
            return [null, sprintf('%s is set but empty', self::CONSOLE_VARIABLE)];
        }

        // Only that the program exists counts here, not that it answers. A run
        // to find out would put a subprocess in front of every lookup, and a
        // wrong argument surfaces as the failed call it is.
        $program = $command[0];
        $found = str_contains($program, '/') ? is_file($program) : self::locateBinary($program) !== null;
        if (!$found) {
            return [null, sprintf(
                '%s starts with "%s", which is not a program on this machine',
                self::CONSOLE_VARIABLE,
                $program
            )];
        }

        return [['command' => $command, 'via' => self::VIA_OVERRIDE, 'php' => ''], ''];
    }

    /**
     * Splits a command line into arguments, with respect for quotes. proc_open
     * gets an array and runs no shell, so nothing else in a shell's syntax
     * would do anything here.
     *
     * @return array<int, string>
     */
    private static function tokenize(string $commandLine): array
    {
        preg_match_all('/"([^"]*)"|\'([^\']*)\'|(\S+)/', trim($commandLine), $matches, PREG_SET_ORDER);

        $arguments = [];
        foreach ($matches as $match) {
            // Whichever alternative matched; the groups after it are not set.
            $bare = $match[3] ?? '';
            $single = $match[2] ?? '';
            $arguments[] = $bare !== '' ? $bare : ($single !== '' ? $single : ($match[1] ?? ''));
        }

        return $arguments;
    }

    /**
     * DDEV first. The project declares the PHP version it needs and DDEV has
     * it, while this machine may not. A paused or stopped project goes into the
     * report rather than starts. An agent that asks about a label must not
     * bring containers up as a side effect.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaDdev(string $root, string $binary): array
    {
        if (!is_file($root . '/.ddev/config.yaml')) {
            return [null, ''];
        }
        // The server may itself have started with `ddev exec`. There is no
        // nested DDEV binary in the web container, on purpose. The direct PHP
        // process is already the project's declared runtime and reaches its
        // services. The absent host-side binary read as a stopped project adds
        // a false database caveat to an otherwise ready console.
        if (filter_var(getenv('IS_DDEV_PROJECT'), FILTER_VALIDATE_BOOL)) {
            return [null, ''];
        }
        if (self::locateBinary('ddev') === null) {
            return [null, 'the installation is a DDEV project but ddev is not installed on this machine'];
        }

        $described = self::execute(['ddev', 'describe', '-j'], $root);
        $status = '';
        $php = '';
        if ($described['ok']) {
            $decoded = json_decode($described['output'], true);
            $raw = is_array($decoded) ? ($decoded['raw'] ?? []) : [];
            $status = is_array($raw) ? (string) ($raw['status'] ?? '') : '';
            $php = is_array($raw) ? (string) ($raw['php_version'] ?? '') : '';
        }

        if ($status !== 'running') {
            return [null, sprintf(
                'the DDEV project is %s — start it with "ddev start" in %s to answer from the installation',
                $status === '' ? 'not reachable' : $status,
                $root
            )];
        }

        return [[
            'command' => ['ddev', 'exec', '--', self::DDEV_APPROOT . '/' . $binary],
            'via' => self::VIA_DDEV,
            'php' => $php,
        ], ''];
    }

    /**
     * Directly, with an interpreter that satisfies what the installation
     * declares. Composer pins the platform, so a run of the console with a PHP
     * below it aborts in platform_check.php before it even reaches TYPO3.
     *
     * @return array{0: array{command: array<int, string>, via: string, php: string}|null, 1: string}
     */
    private static function viaPhp(string $root, string $binary): array
    {
        $required = self::requiredPhpVersion($root);

        foreach (self::interpreterCandidates($required) as $interpreter) {
            $version = self::interpreterVersion($interpreter);
            if ($version === null) {
                continue;
            }
            if ($required !== null && version_compare($version, $required, '<')) {
                continue;
            }

            return [['command' => [$interpreter, $root . '/' . $binary], 'via' => self::VIA_PHP, 'php' => $version], ''];
        }

        return [null, $required === null
            ? 'no PHP interpreter on this machine could run the console'
            : sprintf(
                'the installation requires PHP %s and no interpreter on this machine provides it (running %s)',
                $required,
                PHP_VERSION
            )];
    }

    private static function consoleBinary(string $root): ?string
    {
        foreach (self::consoleCandidates($root) as $candidate) {
            if (is_file($root . '/' . $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Where the console may sit, what the installation declares first.
     *
     * Composer's default bin-dir is vendor/bin, so an installation that
     * declares nothing gets the probe exactly as before. One that declares
     * `"bin-dir": ".build/bin"` has its console there and nowhere else. That is
     * the layout the TYPO3 extension test setup produces, and with it a large
     * share of the published extensions. A probe of only the default meant
     * every question the installation alone can answer came back empty. That
     * was in a checkout whose console was one directory away.
     *
     * @return array<int, string>
     */
    private static function consoleCandidates(string $root): array
    {
        $candidates = [];
        $declared = self::binDirectory($root);
        if ($declared !== null) {
            $candidates[] = $declared . '/typo3';
        }

        return array_values(array_unique(array_merge($candidates, ['vendor/bin/typo3', 'bin/typo3'])));
    }

    /**
     * The bin directory the installation declares, relative to its root.
     *
     * Composer expands `$vendor-dir` inside bin-dir, so that form expands here
     * too. It also accepts an absolute bin-dir and puts the binaries there,
     * verified with Composer 2.9.5 against a project that declares one. So an
     * absolute declaration below the root turns relative to it rather than
     * drops out. It is the same directory the relative form names, and the
     * console answers the same way. One outside the root has no relative form,
     * and the invocation has to have one. Inside a DDEV container the host path
     * would not exist anyway.
     *
     * `Installer` asks the same question about a different binary: where this
     * server's own entrypoint sits once a project has required it. One rule,
     * because a project that moved its bin directory moved both.
     */
    public static function binDirectory(string $root): ?string
    {
        $binDir = self::declaredBinDirectory($root);

        return $binDir === null || !str_starts_with($binDir, '/')
            ? $binDir
            : self::belowRoot($root, $binDir);
    }

    /**
     * What to add where nothing has installed rather than installed elsewhere.
     *
     * A checkout whose dependencies never installed has no autoloader either. A
     * reason that names only the empty paths reads as a property of the
     * checkout. The core monorepo declares `bin-dir: bin` and answers every
     * installation-backed tool once `composer install` has run in it.
     */
    private static function withoutDependencies(string $root): string
    {
        $autoloader = self::autoloader($root);
        if (is_file($root . '/' . $autoloader)) {
            return '';
        }

        return sprintf(
            '. Its dependencies are not installed — %s is not there either, and composer install writes both',
            $autoloader
        );
    }

    /**
     * What to add to "no console turned up" when the installation declares a
     * bin directory outside its root. Without it the caller reads a list of two
     * default paths while its own composer.json names a third. Nothing says the
     * declaration got a read at all.
     */
    private static function unreachableBinDirectory(string $root): string
    {
        $declared = self::declaredBinDirectory($root);
        if ($declared === null || !str_starts_with($declared, '/') || self::binDirectory($root) !== null) {
            return '';
        }

        return sprintf(
            '. The declared bin-dir %s is outside the installation, so a console there cannot be invoked from it — set %s to the command that reaches it',
            $declared,
            self::CONSOLE_VARIABLE
        );
    }

    /**
     * The bin directory as the manifest spells it, absolute declaration and
     * all, so a console out of reach still has a name.
     */
    private static function declaredBinDirectory(string $root): ?string
    {
        $config = self::manifest($root)['config'] ?? [];
        if (!is_array($config)) {
            return null;
        }

        $binDir = $config['bin-dir'] ?? null;
        if (!is_string($binDir) || trim($binDir) === '') {
            return null;
        }

        $vendorDir = $config['vendor-dir'] ?? null;
        $binDir = str_replace(
            '$vendor-dir',
            is_string($vendorDir) && trim($vendorDir) !== '' ? trim($vendorDir) : 'vendor',
            trim($binDir)
        );
        $binDir = rtrim($binDir, '/');

        return $binDir === '' ? null : $binDir;
    }

    /**
     * The installation's autoloader, relative to its root.
     *
     * Relative because the two sides of DDEV do not share absolute paths. The
     * subprocess starts in the root, and inside the container that same root is
     * /var/www/html. An absolute vendor directory counts like an absolute bin
     * directory. Relative to the root where it is below it, and at the default
     * where no relative form exists.
     */
    public static function autoloader(string $root): string
    {
        $config = self::manifest($root)['config'] ?? [];
        $vendorDir = is_array($config) ? ($config['vendor-dir'] ?? null) : null;
        $vendorDir = is_string($vendorDir) ? rtrim(trim($vendorDir), '/') : '';
        if (str_starts_with($vendorDir, '/')) {
            $vendorDir = (string) self::belowRoot($root, $vendorDir);
        }

        return ($vendorDir === '' ? 'vendor' : $vendorDir) . '/autoload.php';
    }

    /**
     * An absolute path as the root sees it, or null when it is not below the
     * root at all. Both sides resolve, because a declaration comes by hand
     * while the root has been through realpath().
     */
    private static function belowRoot(string $root, string $path): ?string
    {
        $root = realpath($root) ?: rtrim($root, '/');
        $absolute = realpath($path) ?: rtrim($path, '/');

        return str_starts_with($absolute, $root . '/') ? substr($absolute, strlen($root) + 1) : null;
    }

    /**
     * The lowest PHP the installation accepts. The pinned Composer platform
     * first, then what the install itself resolved to, then the root's own
     * requirement.
     *
     * The manifest alone was not enough, and the base distribution is why. Its
     * `composer.json` carries `"platform": {}` and no `require.php` at all. The
     * PHP bound lives in the packages it pulls in, not in the file that pulls
     * them. So this read `null`, every interpreter satisfied it, and host PHP
     * 8.3 passed for an installation whose dependencies require 8.5.0.
     * `typo3_server_scope` then reported that console reachable while every
     * boot through it aborted in Composer's own platform check.
     *
     * That check is where the bound actually is, and `Instance` is what reads
     * it out of the install.
     *
     * It goes above `require.php` and below the pin, which follows from what
     * each of the three knows. `config.platform.php` is what Composer resolves
     * against and will resolve against again. The generated check cannot be
     * higher than it in an install that check came out of. `require.php` is the
     * root package's own bound and says nothing about the twenty-five packages
     * it requires. So it is the weakest of the three and only answers where
     * nothing has installed to read.
     */
    private static function requiredPhpVersion(string $root): ?string
    {
        $decoded = self::manifest($root);

        $platform = $decoded['config']['platform']['php'] ?? null;
        if (is_string($platform) && $platform !== '') {
            return $platform;
        }

        $installed = Instance::installedPhpBound($root);
        if ($installed !== null) {
            return $installed;
        }

        $required = $decoded['require']['php'] ?? null;
        if (!is_string($required) || preg_match('/(\d+)\.(\d+)/', $required, $matches) !== 1) {
            return null;
        }

        return $matches[1] . '.' . $matches[2] . '.0';
    }

    /**
     * What the installation declares about itself. Its manifest answers both
     * where the console is and which PHP may run it, so one place reads it.
     *
     * @return array<string, mixed>
     */
    private static function manifest(string $root): array
    {
        $path = $root . '/composer.json';
        if (!is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<int, string> */
    private static function interpreterCandidates(?string $required): array
    {
        $candidates = [PHP_BINARY];

        // A machine that carries several interpreters names them by version.
        if ($required !== null && preg_match('/^(\d+)\.(\d+)/', $required, $matches) === 1) {
            $major = (int) $matches[1];
            for ($minor = (int) $matches[2]; $minor <= (int) $matches[2] + 4; ++$minor) {
                $named = self::locateBinary(sprintf('php%d.%d', $major, $minor));
                if ($named !== null) {
                    $candidates[] = $named;
                }
            }
        }

        return array_values(array_unique($candidates));
    }

    private static function interpreterVersion(string $interpreter): ?string
    {
        $result = self::execute([$interpreter, '-r', 'echo PHP_VERSION;'], getcwd() ?: '.');

        return $result['ok'] && $result['output'] !== '' ? trim($result['output']) : null;
    }

    /** Where an executable of this name is, asked of the same seam as a run. */
    private static function locateBinary(string $name): ?string
    {
        return self::runner()->locate($name);
    }

    /** The real one, unless a test put something else in its place. */
    private static function runner(): CommandRunner
    {
        return self::$runner ?? new SystemRunner();
    }

    /**
     * @param list<string> $command
     * @return array{ok: bool, exitCode: int, output: string, error: string}
     */
    private static function execute(array $command, string $workingDirectory): array
    {
        $result = self::runner()->run($command, $workingDirectory, self::TIMEOUT_SECONDS);

        // The error trims here rather than in the runner. A caller of this
        // class puts it into an answer a client reads. A newline at the end of
        // one of those is a blank line in a tool result.
        $result['error'] = trim($result['error']);

        return $result;
    }
}
