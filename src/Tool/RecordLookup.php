<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What is in a table this installation has TCA for: how many rows, where they
 * sit, what one column holds across them, and the rows themselves.
 *
 * A session maintained 3101 records through the generic record list, read the
 * count twice and drew nothing from it, because nothing connected a number to
 * the question of whether that table needs a backend module of its own
 * (`feedback/archive/2026-08-31-233952`). The count is one half of the answer
 * and the rows are the other: what a table holds cannot be judged from a number
 * alone, and opening the backend to see it is the round trip this exists to
 * take off the caller.
 *
 * A second session then decided the markup of a whole replacement layout from
 * six `ddev mysql` queries and never called this at all, because the table it
 * was asking about was refused (`feedback/archive/2026-09-04-053618`). So the
 * boundary is TCA and nothing narrower, the columns a row carries are the
 * caller's to name, and a distribution says which rows depart from the default
 * — `D-AUD-018`.
 */
final class RecordLookup extends ReadOnlyTool
{
    /**
     * What the answer says it was read with, on every path that reads the
     * database.
     *
     * The client launches this server as a stdio subprocess, so what came out
     * of the database came out with whatever access the shell user has. That is
     * not a backend user's view of the table and never becomes one, and a
     * caller reporting a number or a row onwards is the reason it is said here
     * rather than assumed.
     */
    public const READ_WITH = 'Read with the shell user\'s database access, with no backend permissions applied '
        . 'and no workspace or language filter, so this is every row in the table rather than what a backend user '
        . 'would see.';

    /**
     * Rows returned where the caller names no limit.
     *
     * It is the record list's own page size, which makes the first answer the
     * same size as the surface the caller is deciding about.
     */
    private const ROWS = 20;

    public static function name(): string
    {
        return 'typo3_record_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'Read the rows of any table this installation has TCA for — pages, tt_content, a table of one of this project\'s own extensions, or one a dependency brings: how many there are, which page they sit on, whether they are live, hidden or deleted, and the rows themselves — uid, the label the table names in its own TCA, the timestamps, the two flags, and any column the call names in columns. That is what a backend visit would have told you and the one question typo3_schema_lookup cannot answer: it returns the shape of the table, this returns what is in it. It is also the fact that decides where records are maintained, because a table with a few dozen rows is edited in the generic record list and one with three thousand on a single storage folder needs a module with its own filtering and paging. Pass groupBy to get what values a column actually holds across the table, with the TCA default beside them and the uid of every row departing from it — that is the answer that decides whether a CType, a TCA default, a markup class or a template branch can be dropped, and the single row that departs is the one a cleanup breaks. Narrow it with where, which takes exact values for any column of the table, pid among them; pass count to get the numbers without the rows, and limit to say how many rows come back. A table TCA does not describe is refused, which is the caches, the queues and the session store. Omit the table to see which ones it will read. It reads with the shell user\'s database access rather than a backend user\'s, so no permission, workspace or language filter narrows what comes back. It never writes.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'description' => 'The table to read, for example "tt_content" or "tx_myext_animal". Omit to list the tables this installation has TCA for, which are the ones that can be read.'],
                'where' => [
                    'type' => 'object',
                    'description' => 'Exact values to narrow by, one per column: {"pid": 2, "status": "adopted"}. Every column of the table can be named, pid and uid among them, and a column the table does not have is an answer saying so rather than an empty result. Exact equality only — there is no operator, no wildcard and no range, which is what keeps this a lookup rather than a query language.',
                    'additionalProperties' => ['type' => ['string', 'number', 'boolean']],
                ],
                'count' => ['type' => 'boolean', 'description' => 'True to answer with the numbers alone and read no row. Use it where the question is how much is in there rather than what.', 'default' => false],
                'groupBy' => ['type' => 'string', 'description' => 'One column to count per distinct value of, for example "CType", "header_layout" or "status". The answer then carries one line per value with how many rows carry it, which is the distribution a call per value asks thirteen times for. It also carries the column\'s TCA default and the uid and pid of the rows departing from it, capped, which is what says whether a value is the site\'s convention or its one exception. Combines with where, which narrows what is counted. A column the table does not have is an answer saying so.'],
                'columns' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Columns each row carries beside the ones it always has: ["CType", "frame_class", "header_layout"]. Name as many as the question needs, and typo3_schema_lookup lists what the table has. A column the table does not have is an answer saying so rather than an empty value.',
                ],
                'limit' => ['type' => 'integer', 'description' => 'How many rows to return, ordered by uid. The default is one page of the record list. Zero means every matching row, which on a full table is the whole table in one answer.', 'minimum' => 0, 'default' => self::ROWS],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'table' => Schema::nullableString('The table asked about. Null where none was named and the answer is the list of readable ones.'),
            'matchCount' => Schema::integer('Rows matching the filter, whatever the limit returned. Tables for a call that named none. Zero on a table that is refused as well as on one that is empty, and the text says which.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'where' => Schema::listOf(Schema::object([
                'column' => Schema::string(),
                'value' => ['description' => 'The value the column was matched against, exactly as it was passed.'],
            ], ['column', 'value']), 'The filter the answer was read under, echoed so a count reported onwards carries what it counted. A list rather than a map keyed by column, because an empty map is [] in JSON and a schema saying object refuses it — a client reads one shape either way. Empty where the whole table was read.'),
            'counts' => ['type' => ['object', 'null']] + Schema::object([
                'total' => Schema::integer('Every matching row, whatever state it is in.'),
                'live' => Schema::integer('Rows that are neither hidden nor deleted.'),
                'hidden' => Schema::integer('Rows the disable field hides. Zero where the table declares no such field.'),
                'deleted' => Schema::integer('Rows the delete field marks. They are still in the table until the garbage collection runs.'),
            ], ['total', 'live', 'hidden', 'deleted'], 'Null where no table was read.'),
            'groups' => Schema::listOf(Schema::object([
                'value' => ['description' => 'The value of the grouped column, as the database stores it. Null is a row that has none, which on a select column is the empty string rather than null.'],
                'total' => Schema::integer('Rows carrying that value, deleted and hidden included.'),
                'live' => Schema::integer(),
                'hidden' => Schema::integer(),
                'deleted' => Schema::integer(),
            ], ['value', 'total', 'live', 'hidden', 'deleted']), 'One entry per distinct value of the grouped column, the fullest first. A value with no rows is not here: the distribution is what the table holds, and a status nothing carries is read off its absence. Empty where groupBy was not passed.'),
            'groupDefault' => ['description' => 'What the grouped column\'s TCA declares as its default, so a value can be read as the convention or as a departure from it. Null where groupBy was not passed and where the column declares no default, which is not the same answer as a default of zero.'],
            'departing' => Schema::listOf(Schema::object([
                'uid' => Schema::integer('What the backend edits the row by.'),
                'pid' => Schema::integer(),
                'value' => ['description' => 'What that row carries instead of the default.'],
            ], ['uid', 'pid', 'value']), 'The rows whose grouped column is not the TCA default, by uid, capped at one page of the record list. This is the half of a distribution that decides something: a value one row in a hundred carries is what a cleanup drops and then breaks. Empty where groupBy was not passed, where the column declares no default, and where every row carries it.'),
            'pages' => Schema::listOf(Schema::object([
                'pid' => Schema::integer('The page the rows sit on. Zero is the root, which is where records that belong to no page end up.'),
                'total' => Schema::integer(),
                'live' => Schema::integer(),
                'hidden' => Schema::integer(),
                'deleted' => Schema::integer(),
            ], ['pid', 'total', 'live', 'hidden', 'deleted']), 'One entry per page that holds a matching row, the fullest first. Empty where no table was read.'),
            'records' => Schema::listOf(Schema::object([
                'uid' => Schema::integer('What the backend edits the record by, and what a URL into it carries.'),
                'pid' => Schema::integer(),
                'label' => Schema::string('The column the table names as its label in ctrl. Empty where it names none, which is a property of the table rather than of the row.'),
                'changed' => Schema::integer('Unix time of the last change, 0 where the table has no tstamp column.'),
                'created' => Schema::integer('Unix time of creation, 0 where the table has no crdate column.'),
                'deleted' => ['type' => 'boolean'],
                'hidden' => ['type' => 'boolean'],
                'values' => Schema::listOf(Schema::object([
                    'column' => Schema::string(),
                    'value' => ['description' => 'What the row stores in that column, as the database has it.'],
                ], ['column', 'value']), 'The columns the call named, in the order it named them. A list rather than a map keyed by column, for the reason where gives. Empty where none were named.'),
            ], ['uid', 'pid', 'label', 'changed', 'created', 'deleted', 'hidden', 'values']), 'The rows read, ordered by uid. Empty where count was asked for, where no table was named, and where nothing matched.'),
            'countable' => Schema::listOf(Schema::object([
                'table' => Schema::string(),
                'extension' => Schema::string('The extension whose TCA registers it, read from the EXT: reference in its ctrl title. Empty where the title names none.'),
            ], ['table', 'extension']), 'Every table this tool will read in this installation, which is every one TCA describes.'),
            'readWith' => Schema::string('What the reading was made with. Said on every answer that carries one, because a number or a row reported onwards is read as a backend user\'s view of the table unless it says otherwise.'),
        ], ['table', 'matchCount', 'answeredBy', 'where', 'counts', 'groups', 'groupDefault', 'departing', 'pages', 'records', 'countable', 'readWith'], ['table']);
    }

    public static function answer(array $args): ToolResult
    {
        $table = trim((string) ($args['table'] ?? ''));
        $where = is_array($args['where'] ?? null) ? $args['where'] : [];
        $counting = ($args['count'] ?? false) === true;
        $groupBy = trim((string) ($args['groupBy'] ?? ''));
        $columns = array_values(array_filter(
            array_map(
                static fn(mixed $column): string => trim((string) (is_scalar($column) ? $column : '')),
                is_array($args['columns'] ?? null) ? $args['columns'] : [],
            ),
            static fn(string $column): bool => $column !== '',
        ));
        $echoedFilter = [];
        foreach ($where as $column => $value) {
            $echoedFilter[] = ['column' => (string) $column, 'value' => $value];
        }
        $limit = $counting ? 0 : max(0, (int) ($args['limit'] ?? self::ROWS));
        $echo = ['table' => $table === '' ? null : $table];

        if (!Instance::isAvailable()) {
            return Unsupported::because(
                'no TYPO3 installation was found from the directory this server was started in',
                $echo,
            );
        }

        $tables = Typo3Runtime::topic('tables');
        if (!is_array($tables)) {
            return Unsupported::because(Typo3Runtime::reason(), $echo);
        }

        $countable = self::countable($tables);
        $empty = [
            'matchCount' => 0,
            'answeredBy' => 'installation',
            'where' => $echoedFilter,
            'counts' => null,
            'groups' => [],
            'groupDefault' => null,
            'departing' => [],
            'pages' => [],
            'records' => [],
            'countable' => $countable,
            'readWith' => self::READ_WITH,
        ];

        if ($table === '') {
            return ToolResult::create(
                implode("\n", [
                    $countable === []
                        ? 'This installation reports no TCA at all, so there is nothing here to read.'
                        : sprintf(
                            'This installation has TCA for %d tables. Name one to read what is in it.',
                            count($countable),
                        ),
                    '',
                    ...array_map(
                        static fn(array $entry): string => sprintf('- %s (%s)', $entry['table'], $entry['extension']),
                        $countable,
                    ),
                ]),
                ['matchCount' => count($countable)] + $echo + $empty,
            );
        }

        if (!in_array($table, array_column($countable, 'table'), true)) {
            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        '"%s" is not a table this installation has TCA for, so nothing was read. What TCA does '
                        . 'not describe is the caches, the queues and the session store, and none of those holds '
                        . 'a record.',
                        $table,
                    ),
                    $countable === []
                        ? 'This installation reports no TCA at all.'
                        : sprintf('It has TCA for %d tables, listed by the same call with no table named.', count($countable)),
                ]),
                $echo + $empty,
            );
        }

        $named = $where + array_fill_keys($columns, null);
        $unknown = self::unknownColumns($table, $groupBy === '' ? $named : $named + [$groupBy => null]);
        if ($unknown !== []) {
            return ToolResult::create(
                sprintf(
                    '%s has no column %s, so nothing was read. A filter names columns of the table it filters, and '
                    . 'typo3_schema_lookup with table="%s" lists the ones it has.',
                    $table,
                    implode(' and no column ', $unknown),
                    $table,
                ),
                $echo + $empty,
            );
        }

        /** @var array<string, scalar> $where */
        $read = Typo3Runtime::records(
            $table,
            $where,
            $limit === 0 && !$counting ? -1 : $limit,
            $groupBy,
            self::ROWS,
            $columns,
        );
        if (!is_array($read) || isset($read['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not read ' . $table . ': '
                . (is_array($read) ? (string) $read['unavailable'] : Typo3Runtime::reason()),
                $echo,
            );
        }

        $pages = self::pages($read['groups']);
        $groups = $groupBy === '' ? [] : self::grouped($read['groups']);
        $default = $groupBy === '' ? null : ($read['groupDefault'] ?? null);
        $departing = $groupBy === '' ? [] : ($read['departing'] ?? []);
        $counts = [
            'total' => array_sum(array_column($pages, 'total')),
            'live' => array_sum(array_column($pages, 'live')),
            'hidden' => array_sum(array_column($pages, 'hidden')),
            'deleted' => array_sum(array_column($pages, 'deleted')),
        ];

        return ToolResult::create(
            implode("\n", [
                sprintf(
                    '%s%s holds %d rows: %d live, %d hidden, %d deleted%s.',
                    $table,
                    $where === [] ? '' : ' ' . self::filterSentence($where),
                    $counts['total'],
                    $counts['live'],
                    $counts['hidden'],
                    $counts['deleted'],
                    $read['deleteField'] === '' ? ' (the table declares no delete field)' : '',
                ),
                self::verdict($counts['total'], $pages, $where !== []),
                self::READ_WITH,
                '',
                ...array_map(static fn(array $page): string => sprintf(
                    '- pid %d: %d rows (%d live, %d hidden, %d deleted)',
                    $page['pid'],
                    $page['total'],
                    $page['live'],
                    $page['hidden'],
                    $page['deleted'],
                ), $pages),
                ...($groups === [] ? [] : [
                    "\n" . sprintf('By %s, %d value(s):', $groupBy, count($groups)),
                    ...array_map(static fn(array $group): string => sprintf(
                        '- %s: %d rows (%d live, %d hidden, %d deleted)',
                        is_scalar($group['value']) && (string) $group['value'] !== ''
                            ? (string) $group['value']
                            : '(empty)',
                        $group['total'],
                        $group['live'],
                        $group['hidden'],
                        $group['deleted'],
                    ), $groups),
                    ...self::departure($groupBy, $default, $departing),
                ]),
                ...($read['rows'] === [] ? [] : [
                    "\n" . self::rowHeading(count($read['rows']), $counts['total'], $read),
                    ...array_map(static fn(array $row): string => sprintf(
                        '- [%d] %s%s%s',
                        $row['uid'],
                        $row['label'] === '' ? '(no label column)' : $row['label'],
                        $row['deleted'] ? ' — deleted' : ($row['hidden'] ? ' — hidden' : ''),
                        self::valueSentence($row['values'] ?? []),
                    ), $read['rows']),
                ]),
            ]),
            $echo + [
                'matchCount' => $counts['total'],
                'answeredBy' => 'installation',
                'where' => $echoedFilter,
                'counts' => $counts,
                'groups' => $groups,
                'groupDefault' => $default,
                'departing' => $departing,
                'pages' => $pages,
                'records' => $read['rows'],
                'countable' => $countable,
                'readWith' => self::READ_WITH,
            ],
        );
    }

    /**
     * Every table this installation has TCA for, in the order a listing reads.
     *
     * TCA is the boundary and the whole of it: a row a `ctrl` describes is a
     * record, and a table nothing describes is the caches, the queues and the
     * session store, which no reading here is about — `D-AUD-018`. The
     * extension beside each one is read out of the reference every `ctrl` title
     * carries, the same attribution the icons and the content elements are made
     * by, and it says whose table it is rather than deciding whether it is read.
     *
     * @param array<string, mixed> $tables
     * @return array<int, array{table: string, extension: string}>
     */
    private static function countable(array $tables): array
    {
        $countable = [];
        foreach ($tables as $table => $title) {
            $countable[] = [
                'table' => (string) $table,
                'extension' => Typo3Runtime::extensionIn(is_string($title) ? $title : '') ?? '',
            ];
        }
        usort($countable, static fn(array $one, array $other): int => $one['table'] <=> $other['table']);

        return $countable;
    }

    /**
     * The filter's columns the table does not have.
     *
     * Checked here rather than left to the database, because a column name goes
     * into the SQL as an identifier where the value beside it is bound. What
     * TYPO3 derives for the table is the list it is checked against, since that
     * is one column per TCA field plus the technical ones — the same answer
     * `typo3_schema_lookup` hands the caller to write the filter from.
     *
     * @param array<mixed, mixed> $where
     * @return array<int, string>
     */
    private static function unknownColumns(string $table, array $where): array
    {
        if ($where === []) {
            return [];
        }

        $derived = Typo3Runtime::topic('derivedColumns');
        $columns = is_array($derived['tables'][$table]['columns'] ?? null)
            ? array_column($derived['tables'][$table]['columns'], 'name')
            : [];

        $unknown = [];
        foreach (array_keys($where) as $column) {
            if (!in_array((string) $column, $columns, true)) {
                $unknown[] = (string) $column;
            }
        }

        return $unknown;
    }

    /**
     * The groupings the probe returned, folded to one entry per page.
     *
     * The probe groups by every flag the table declares, so one page arrives as
     * up to four rows. A deleted row that is also hidden is counted as deleted:
     * a caller asking what is still there wants one number per row, and deleted
     * is the state that answers it.
     *
     * @param array<int, array{pid: int, deleted: bool, hidden: bool, rows: int}> $groups
     * @return array<int, array{pid: int, total: int, live: int, hidden: int, deleted: int}>
     */
    private static function pages(array $groups): array
    {
        $pages = [];
        foreach ($groups as $group) {
            $pid = $group['pid'];
            $pages[$pid] ??= ['pid' => $pid, 'total' => 0, 'live' => 0, 'hidden' => 0, 'deleted' => 0];
            $pages[$pid]['total'] += $group['rows'];
            $state = $group['deleted'] ? 'deleted' : ($group['hidden'] ? 'hidden' : 'live');
            $pages[$pid][$state] += $group['rows'];
        }
        usort($pages, static fn(array $one, array $other): int => [$other['total'], $one['pid']] <=> [$one['total'], $other['pid']]);

        return $pages;
    }

    /**
     * The distribution of one column, out of the same grouped read.
     *
     * A session established one with thirteen counted calls, one per value,
     * and said six of them would have been this — `D-ANS-141`. The probe
     * already grouped by page and by the two state flags, so the column the
     * caller names is a third one on the same query rather than a second read.
     *
     * @param array<int, array<string, mixed>> $groups
     * @return array<int, array<string, mixed>>
     */
    private static function grouped(array $groups): array
    {
        $values = [];
        foreach ($groups as $group) {
            $value = $group['value'] ?? null;
            $key = is_scalar($value) ? (string) $value : '';
            $values[$key] ??= ['value' => $value, 'total' => 0, 'live' => 0, 'hidden' => 0, 'deleted' => 0];
            $values[$key]['total'] += $group['rows'];
            $state = $group['deleted'] ? 'deleted' : ($group['hidden'] ? 'hidden' : 'live');
            $values[$key][$state] += $group['rows'];
        }
        usort($values, static fn(array $one, array $other): int => $other['total'] <=> $one['total']);

        return $values;
    }

    /**
     * The default the grouped column declares, and the rows that are not it.
     *
     * A distribution says what the table holds and not which of it is the
     * exception, and the exception is what a cleanup breaks: one row of 137
     * carried a header layout no other row did and was the site's only h1
     * (`feedback/archive/2026-09-04-053618`). A column with no TCA default has
     * nothing to depart from and says that instead — `D-AUD-018`.
     *
     * @param array<int, array{uid: int, pid: int, value: mixed}> $departing
     * @return array<int, string>
     */
    private static function departure(string $groupBy, mixed $default, array $departing): array
    {
        if ($groupBy === '') {
            return [];
        }
        if ($default === null) {
            return ['', sprintf('%s declares no default in TCA, so no value here is a departure from one.', $groupBy)];
        }
        if ($departing === []) {
            return ['', sprintf('Every row carries the TCA default %s.', var_export($default, true))];
        }

        return [
            '',
            sprintf(
                'The TCA default is %s. %s of them by uid depart from it, and one of those is what a branch '
                . 'written for the default alone would drop:',
                var_export($default, true),
                count($departing) < self::ROWS ? 'All ' . count($departing) : 'The first ' . self::ROWS,
            ),
            ...array_map(static fn(array $row): string => sprintf(
                '- [%d] on pid %d: %s',
                $row['uid'],
                $row['pid'],
                is_scalar($row['value']) && (string) $row['value'] !== '' ? (string) $row['value'] : '(empty)',
            ), $departing),
        ];
    }

    /**
     * The columns the call named, on the line of the row they belong to.
     *
     * @param array<int, array{column: string, value: mixed}> $values
     */
    private static function valueSentence(array $values): string
    {
        if ($values === []) {
            return '';
        }

        return ' — ' . implode(', ', array_map(static fn(array $value): string => sprintf(
            '%s = %s',
            $value['column'],
            is_scalar($value['value']) && (string) $value['value'] !== ''
                ? (string) $value['value']
                : '(empty)',
        ), $values));
    }

    /**
     * The filter as the sentence reads it, so the number in front of it is not
     * mistaken for the table's own.
     *
     * @param array<mixed, mixed> $where
     */
    private static function filterSentence(array $where): string
    {
        $parts = [];
        foreach ($where as $column => $value) {
            $parts[] = sprintf('%s = %s', (string) $column, var_export($value, true));
        }

        return 'where ' . implode(' and ', $parts);
    }

    /**
     * What the row list is, said before it.
     *
     * A list that is the first page of a longer one and a list that is the
     * whole table read alike, and the difference decides whether the caller has
     * seen what is in there.
     *
     * @param array{rows: array<int, array<string, mixed>>, labelField: string} $read
     */
    private static function rowHeading(int $shown, int $total, array $read): string
    {
        return sprintf(
            '%s%s:',
            $shown < $total
                ? sprintf('The first %d of them by uid', $shown)
                : sprintf('All %d of them', $shown),
            $read['labelField'] === ''
                ? ', with no label because the table names no label column in its ctrl'
                : ', labelled by ' . $read['labelField'],
        );
    }

    /**
     * The sentence the count exists for, and the one the sighted session did
     * not have.
     *
     * A number on its own was in that session's hands twice and changed
     * nothing, so the answer says what the number means for where the records
     * are edited. The threshold is the record list's own page size: one page of
     * it is a table nobody has to leave the list for, and a storage folder that
     * runs to dozens of pages is the case the sighting was. It is withheld
     * under a filter, where the number is about a slice and says nothing about
     * the surface the editor faces.
     *
     * @param array<int, array{pid: int, total: int, live: int, hidden: int, deleted: int}> $pages
     */
    private static function verdict(int $total, array $pages, bool $filtered): string
    {
        $fullest = $pages[0] ?? null;
        if ($fullest === null || $total === 0) {
            return $filtered
                ? 'Nothing matches, which says nothing about how full the table is.'
                : 'Nothing is stored in it yet, so where it is edited is still open.';
        }
        if ($filtered) {
            return 'That is the filtered set. How full the page an editor opens actually is takes the same call '
                . 'without where.';
        }
        if ($fullest['total'] <= self::ROWS) {
            return sprintf(
                'The fullest page holds %d, which is one page of the record list. Nothing here asks for an editing '
                . 'surface of its own.',
                $fullest['total'],
            );
        }

        return sprintf(
            'The fullest page holds %d, which is %d pages of the record list at %d rows each. An editor '
            . 'maintaining that through the generic list has no filtering and no search over it, so this is where a '
            . 'backend module of its own is the question — typo3_backend_module_lookup reports what this '
            . 'installation already registers.',
            $fullest['total'],
            (int) ceil($fullest['total'] / self::ROWS),
            self::ROWS,
        );
    }
}
