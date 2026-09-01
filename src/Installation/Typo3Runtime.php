<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

/**
 * What the installation itself says, once it has booted.
 *
 * Some registries are only ever assembled at runtime. An icon list built in a
 * foreach, a table added by a PHP call, a content element whose identifier
 * comes out of a variable — none of them exists in a file a reader could parse,
 * and a review that compares a parsed list against the tree reports defects
 * nobody has. So TYPO3 is booted and its container is asked, which is the same
 * move `Typo3Cli` makes for the questions a console command covers.
 *
 * Three answers, and the middle one is why this class exists:
 *
 * - **full** — the container came up with every extension in it, and what it
 *   reports is the truth about this installation.
 * - **failsafe** — TYPO3 booted, but without essential configuration, so only
 *   core packages are in the container. Every registry still answers, and
 *   every answer is a subset that looks like the whole. It is never handed on
 *   as a result; it is a reason to fall back and say so.
 * - **unreachable** — no console could be resolved, no interpreter derived, or
 *   the boot failed. Also a reason, never silence.
 *
 * The reading is memoized for the length of one tool call, because it costs a
 * boot and one reading answers every topic. `Registry::call` drops it when the
 * call ends, and that is what keeps an answer from describing the installation
 * as it was before the caller's own edit. It is also why nothing here has to
 * notice a project that was started, or configured, since the last reading:
 * between two calls there is no reading left to be stale.
 */
final class Typo3Runtime
{
    /** The container came up whole; what it reports is this installation. */
    public const STATE_FULL = 'full';

    /** TYPO3 booted without its configuration: core only, and it looks complete. */
    public const STATE_FAILSAFE = 'failsafe';

    /** Nothing was asked, and the reason says what stood in the way. */
    public const STATE_UNREACHABLE = 'unreachable';

    /** @var array{state: string, reason: string, topics: array<string, mixed>}|null */
    private static ?array $answer = null;

    /**
     * What the topics that take an argument were asked with; empty means none
     * of them is read at all.
     *
     * @var array<string, mixed>
     */
    private static array $parameters = [];

    /**
     * What the running installation reports, or why it did not.
     *
     * @return array{state: string, reason: string, topics: array<string, mixed>}
     */
    public static function ask(): array
    {
        // Every state is kept alike, because within one call there is nothing
        // for any of them to become: the console that was resolved stays
        // resolved, and a project the caller starts on reading "the DDEV
        // project is stopped" is started between two calls, where no reading
        // survives to be corrected.
        if (self::$answer !== null) {
            return self::$answer;
        }

        return self::$answer = self::read();
    }

    /**
     * One topic of a full reading, or null when there was none.
     *
     * Null and an empty topic are different answers, and the caller that falls
     * back needs the difference: nothing registered is a fact, nothing asked is
     * a gap with a reason attached.
     */
    public static function topic(string $name): mixed
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? ($answer['topics'][$name] ?? null) : null;
    }

    /**
     * One path out of TYPO3_CONF_VARS as the installation has it, or null where
     * there was no full reading to take it from.
     *
     * Asked for rather than read with everything else, because the whole of
     * TYPO3_CONF_VARS is around 50 kB of JSON before an extension has added to
     * it and every other reading would carry it for nothing.
     *
     * @return array{found: bool, value: mixed}|array{unavailable: string}|null
     */
    public static function configuration(string $path): ?array
    {
        /** @var array{found: bool, value: mixed}|array{unavailable: string}|null $read */
        $read = self::asked('configuration', ['configurationPath' => $path]);

        return $read;
    }

    /**
     * The service definitions this installation assembles, or null where there
     * was no full reading to take them from.
     *
     * Asked for, because it builds the container a second time — `D-DIS-023`.
     *
     * @return array{definitionCount: int, aliasCount: int, compilationFailure: string, services: array<int, array<string, mixed>>}|array{unavailable: string}|null
     */
    public static function services(string $query, string $tag): ?array
    {
        /** @var array{definitionCount: int, aliasCount: int, compilationFailure: string, services: array<int, array<string, mixed>>}|array{unavailable: string}|null $read */
        $read = self::asked('services', ['services' => ['query' => $query, 'tag' => $tag]]);

        return $read;
    }

    /**
     * What the database has for a table, or the tables it has at all, or null
     * where there was no full reading to take it from.
     *
     * Asked for, because it opens a connection and lists a schema — the derived
     * columns beside it say what TYPO3 would create, and a caller who asked
     * about an icon should pay for neither. An empty table name lists the
     * tables and nothing else; a table the schema does not have comes back
     * `present: false` rather than as a failure — `D-DIS-022`.
     *
     * @return array{tables: array<int, string>, statementCount: int, suggestions: array<int, array{connection: string, change: string, tables: array<int, string>}>, table?: string, present?: bool, columns?: array<int, array<string, mixed>>, indexes?: array<int, array<string, mixed>>}|array{unavailable: string}|null
     */
    public static function liveSchema(string $table): ?array
    {
        /** @var array{tables: array<int, string>, statementCount: int, suggestions: array<int, array{connection: string, change: string, tables: array<int, string>}>, table?: string, present?: bool, columns?: array<int, array<string, mixed>>, indexes?: array<int, array<string, mixed>>}|array{unavailable: string}|null $read */
        $read = self::asked('liveSchema', ['liveSchema' => ['table' => $table]]);

        return $read;
    }

    /**
     * What one `type=flex` column of this installation resolves to, or null
     * where there was no full reading to take it from.
     *
     * Asked for, because the resolution costs the events, the file reads and
     * the preparation behind one column, and no reading taken for anything else
     * has a use for it.
     *
     * @param array<string, mixed> $record the values the row is emulated from,
     *     since which structure it is can depend on them and nothing here loads
     *     one
     * @return array<string, mixed>|null
     */
    public static function flexForm(string $table, string $field, array $record): ?array
    {
        return self::asked('flexForm', ['flexForm' => [
            'table' => $table,
            'field' => $field,
            'record' => $record,
        ]]);
    }

    /** Why there is no full reading. Empty when there is one. */
    public static function reason(): string
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? '' : $answer['reason'];
    }

    /**
     * Drops the memoized reading.
     *
     * Called at the end of every tool call, which is what bounds the reading to
     * the answer it was taken for — `Registry::call` carries the reason. Also
     * what a recording and a test move between two installations with.
     */
    public static function forget(): void
    {
        self::$answer = null;
        self::$parameters = [];
    }

    /**
     * One topic the probe reads only where a caller asked for it, with what it
     * was asked with.
     *
     * A reading taken before this was asked does not carry the topic, so asking
     * discards it and takes another. That is the whole of the ordering: no
     * caller has to ask its parameterized topic first, and two of them in one
     * call cost two boots rather than a wrong answer.
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>|null
     */
    private static function asked(string $topic, array $parameters): ?array
    {
        if (self::$parameters !== $parameters) {
            self::$parameters = $parameters;
            self::$answer = null;
        }

        $read = self::topic($topic);

        return is_array($read) ? $read : null;
    }

    /**
     * The extension key a runtime entry names, or null where it names none.
     *
     * TCA and the icon registry are the installation's, not any package's, and
     * an answer about one extension cannot be assembled from a list belonging
     * to all of them. What every entry does carry is a reference into the
     * package that owns it — `LLL:EXT:news/…locallang.xlf:plugin.list` on a
     * label or a ctrl title, `EXT:news/Resources/Public/Icons/list.svg` on an
     * icon — and that reference is evidence rather than a naming convention.
     * Where there is none, the entry belongs to the installation.
     */
    public static function extensionIn(string $reference): ?string
    {
        return preg_match('#(?:^|:)EXT:([a-z0-9_]+)/#i', $reference, $match) === 1
            ? strtolower($match[1])
            : null;
    }

    /** @return array{state: string, reason: string, topics: array<string, mixed>} */
    private static function read(): array
    {
        $root = Instance::root();
        if ($root === null) {
            return self::nothing('no TYPO3 installation was found to boot');
        }

        $result = Typo3Cli::php(self::payload($root));
        if (!$result['ok']) {
            $error = trim($result['error']) !== '' ? trim($result['error']) : trim($result['output']);

            return self::nothing($error === '' ? 'the installation could not be booted' : $error);
        }

        $decoded = json_decode(trim($result['output']), true);
        if (!is_array($decoded) || !isset($decoded['state'])) {
            return self::nothing('the installation booted and answered with something other than JSON');
        }

        return [
            'state' => (string) $decoded['state'],
            'reason' => (string) ($decoded['reason'] ?? ''),
            'topics' => is_array($decoded['topics'] ?? null) ? $decoded['topics'] : [],
        ];
    }

    /**
     * The probe with the autoloader of this installation and what this call
     * asked for written into it.
     *
     * The opening tag goes because the body is delivered through `php -r`,
     * which supplies its own.
     */
    private static function payload(string $root): string
    {
        $probe = (string) file_get_contents(__DIR__ . '/probe.php');
        $probe = (string) preg_replace('/^<\?php\s/', '', $probe, 1);

        return str_replace(
            ["'vendor/autoload.php'", '$parameters = []'],
            [
                var_export(Typo3Cli::autoloader($root), true),
                '$parameters = ' . var_export(self::$parameters, true),
            ],
            $probe
        );
    }

    /** @return array{state: string, reason: string, topics: array<string, mixed>} */
    private static function nothing(string $reason): array
    {
        return ['state' => self::STATE_UNREACHABLE, 'reason' => $reason, 'topics' => []];
    }
}
