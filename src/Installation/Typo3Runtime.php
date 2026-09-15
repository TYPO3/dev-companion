<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

/**
 * What the installation itself says, once it has booted.
 *
 * Some registries are only ever assembled at runtime. An icon list built in a
 * foreach, a table added by a PHP call, a content element whose identifier
 * comes out of a variable. None of them exists in a file a reader could parse.
 * A review that compares a parsed list against the tree reports defects nobody
 * has. So TYPO3 boots and its container answers, which is the same move
 * `Typo3Cli` makes for the questions a console command covers.
 *
 * Three answers, and the middle one is why this class exists:
 *
 * - **full**: the container came up with every extension in it, and what it
 *   reports is the truth about this installation.
 * - **failsafe**: TYPO3 booted, but without essential configuration, so only
 *   core packages are in the container. Every registry still answers, and every
 *   answer is a subset that looks like the whole. It never goes on as a result;
 *   it is a reason to fall back and say so.
 * - **unreachable**: no console resolved, no interpreter derived, or the boot
 *   failed. Also a reason, never silence.
 *
 * The read stays in memory for the length of one tool call, because it costs a
 * boot and one read answers every topic. `Registry::call` drops it when the
 * call ends. That is what keeps an answer from a description of the
 * installation as it was before the caller's own edit. It is also why nothing
 * here has to notice a project that started, or got its configuration, since
 * the last read. Between two calls no read remains to go stale.
 */
final class Typo3Runtime
{
    /** The container came up whole; what it reports is this installation. */
    public const STATE_FULL = 'full';

    /** TYPO3 booted without its configuration: core only, and it looks complete. */
    public const STATE_FAILSAFE = 'failsafe';

    /** No question went out, and the reason says what stood in the way. */
    public const STATE_UNREACHABLE = 'unreachable';

    /** @var array{state: string, reason: string, topics: array<string, mixed>}|null */
    private static ?array $answer = null;

    /**
     * The arguments of the topics that take one; empty means none of them reads
     * at all.
     *
     * @var array<string, mixed>
     */
    private static array $parameters = [];

    /**
     * What the live installation reports, or why it did not.
     *
     * @return array{state: string, reason: string, topics: array<string, mixed>}
     */
    public static function ask(): array
    {
        // Every state stays alike, because within one call there is nothing for
        // any of them to become. The resolved console stays resolved. A project
        // the caller starts on "the DDEV project is stopped" starts between two
        // calls, where no read survives for a correction.
        if (self::$answer !== null) {
            return self::$answer;
        }

        return self::$answer = self::read();
    }

    /**
     * One topic of a full read, or null when there was none.
     *
     * Null and an empty topic are different answers, and the caller that falls
     * back needs the difference. Nothing registered is a fact, nothing asked is
     * a gap with a reason attached.
     */
    public static function topic(string $name): mixed
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? ($answer['topics'][$name] ?? null) : null;
    }

    /**
     * One path out of TYPO3_CONF_VARS as the installation has it, or null where
     * there was no full read to take it from.
     *
     * On request rather than with everything else. The whole of TYPO3_CONF_VARS
     * is around 50 kB of JSON before an extension has added to it. Every other
     * read would carry it for nothing.
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
     * was no full read to take them from.
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
     * What the database has for a table, or the tables it has at all. Null
     * where there was no full read to take it from.
     *
     * On request, because it opens a connection and lists a schema. The derived
     * columns beside it say what TYPO3 would create, and a caller who asked
     * about an icon should pay for neither. An empty table name lists the
     * tables and nothing else. A table the schema does not have comes back
     * `present: false` rather than as a failure, `D-DIS-022`.
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
     * The rows of a table, and how many there are, or null where there was no
     * full read to take them from.
     *
     * On request, because it is the only read this server takes over rows and
     * no read for anything else wants it. What a row always carries is the
     * probe's to decide (`D-AUD-017`). The columns beside it are the caller's,
     * named and checked against the table first, `D-AUD-019`.
     *
     * @param array<string, scalar> $where exact matches, one per column
     * @param int $limit rows to read, 0 for none and -1 for all of them
     * @param int $departing rows departing from the grouped column's default to name
     * @param array<int, string> $columns columns each row carries beside the ones it always has
     * @return array{table: string, deleteField: string, hiddenField: string, labelField: string, groups: array<int, array{pid: int, deleted: bool, hidden: bool, rows: int}>, groupDefault?: mixed, departing?: array<int, array{uid: int, pid: int, value: mixed}>, rows: array<int, array{uid: int, pid: int, label: string, changed: int, created: int, deleted: bool, hidden: bool, values?: array<int, array{column: string, value: mixed}>}>}|array{unavailable: string}|null
     */
    public static function records(string $table, array $where, int $limit, string $groupBy = '', int $departing = 20, array $columns = []): ?array
    {
        /** @var array{table: string, deleteField: string, hiddenField: string, labelField: string, groups: array<int, array{pid: int, deleted: bool, hidden: bool, rows: int, value?: mixed}>, groupDefault?: mixed, departing?: array<int, array{uid: int, pid: int, value: mixed}>, rows: array<int, array{uid: int, pid: int, label: string, changed: int, created: int, deleted: bool, hidden: bool, values?: array<int, array{column: string, value: mixed}>}>}|array{unavailable: string}|null $read */
        $read = self::asked('records', ['records' => [
            'table' => $table,
            'where' => $where,
            'limit' => $limit,
            'groupBy' => $groupBy,
            'departing' => $departing,
            'columns' => $columns,
        ]]);

        return $read;
    }

    /**
     * What one `type=flex` column of this installation resolves to, or null
     * where there was no full read to take it from.
     *
     * On request, because the resolution costs the events, the file reads and
     * the preparation behind one column. No read for anything else has a use
     * for it.
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

    /** Why there is no full read. Empty when there is one. */
    public static function reason(): string
    {
        $answer = self::ask();

        return $answer['state'] === self::STATE_FULL ? '' : $answer['reason'];
    }

    /**
     * Drops the memoized reading.
     *
     * Called at the end of every tool call, which is what bounds the read to
     * the answer it came for. `Registry::call` carries the reason. Also what a
     * recording and a test move between two installations with.
     */
    public static function forget(): void
    {
        self::$answer = null;
        self::$parameters = [];
    }

    /**
     * One topic the probe reads only where a caller asked for it, with the
     * argument of the ask.
     *
     * A read from before this ask does not carry the topic, so the ask discards
     * it and takes another. That is the whole of the order. No caller has to
     * ask its parameterized topic first, and two of them in one call cost two
     * boots rather than a wrong answer.
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
     * TCA and the icon registry are the installation's, not any package's. An
     * answer about one extension cannot come from a list that belongs to all of
     * them. What every entry does carry is a reference into the package that
     * owns it. `LLL:EXT:news/…locallang.xlf:plugin.list` on a label or a ctrl
     * title, `EXT:news/Resources/Public/Icons/list.svg` on an icon. That
     * reference is evidence rather than a name convention. Where there is none,
     * the entry belongs to the installation.
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
     * The open tag goes because the body travels through `php -r`, which
     * supplies its own.
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
