<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Project;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What is in a table of one of this project's own extensions: how many rows,
 * where they sit, and the rows themselves.
 *
 * A session maintained 3101 records through the generic record list, read the
 * count twice and drew nothing from it, because nothing connected a number to
 * the question of whether that table needs a backend module of its own
 * (`feedback/archive/2026-08-31-233952`). The count is one half of the answer
 * and the rows are the other: what a table holds cannot be judged from a number
 * alone, and opening the backend to see it is the round trip this exists to
 * take off the caller — `D-AUD-017`.
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
        return 'Read the rows of a table belonging to one of this project\'s own extensions: how many there are, which page they sit on, whether they are live, hidden or deleted, and the rows themselves — uid, the label the table names in its own TCA, the timestamps and the two flags. That is what a backend visit would have told you and the one question a schema answer cannot: what is actually stored. It is also the fact that decides where records are maintained, because a table with a few dozen rows is edited in the generic record list and one with three thousand on a single storage folder needs a module with its own filtering and paging. Narrow it with where, which takes exact values for any column of the table, pid among them; pass count to get the numbers without the rows, and limit to say how many rows come back. It refuses every table a project-owned extension does not register: pages, tt_content, the user tables and everything a dependency brings are outside it, and reading those is the backend\'s or the installation\'s own console. Omit the table to see which ones it will read. It never writes.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'description' => 'The table to read, for example "tx_myext_animal". Omit to list the tables this project\'s own extensions register, which are the ones that can be read.'],
                'where' => [
                    'type' => 'object',
                    'description' => 'Exact values to narrow by, one per column: {"pid": 2, "status": "adopted"}. Every column of the table can be named, pid and uid among them, and a column the table does not have is an answer saying so rather than an empty result. Exact equality only — there is no operator, no wildcard and no range, which is what keeps this a lookup rather than a query language.',
                    'additionalProperties' => ['type' => ['string', 'number', 'boolean']],
                ],
                'count' => ['type' => 'boolean', 'description' => 'True to answer with the numbers alone and read no row. Use it where the question is how much is in there rather than what.', 'default' => false],
                'limit' => ['type' => 'integer', 'description' => 'How many rows to return, ordered by uid. Defaults to 20, which is one page of the record list. Zero means every matching row, which on a full table is the whole table in one answer.', 'minimum' => 0],
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
            ], ['uid', 'pid', 'label', 'changed', 'created', 'deleted', 'hidden']), 'The rows read, ordered by uid. Empty where count was asked for, where no table was named, and where nothing matched.'),
            'countable' => Schema::listOf(Schema::object([
                'table' => Schema::string(),
                'extension' => Schema::string('The project-owned extension whose TCA registers it.'),
            ], ['table', 'extension']), 'Every table this tool will read in this installation.'),
            'readWith' => Schema::string('What the reading was made with. Said on every answer that carries one, because a number or a row reported onwards is read as a backend user\'s view of the table unless it says otherwise.'),
        ], ['table', 'matchCount', 'answeredBy', 'where', 'counts', 'pages', 'records', 'countable', 'readWith'], ['table']);
    }

    public static function answer(array $args): ToolResult
    {
        $table = trim((string) ($args['table'] ?? ''));
        $where = is_array($args['where'] ?? null) ? $args['where'] : [];
        $counting = ($args['count'] ?? false) === true;
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
            'pages' => [],
            'records' => [],
            'countable' => $countable,
            'readWith' => self::READ_WITH,
        ];

        if ($table === '') {
            return ToolResult::create(
                implode("\n", [
                    $countable === []
                        ? 'No extension of this project registers a table of its own, so there is nothing here to '
                            . 'read. A project whose content lives in pages and tt_content is that case.'
                        : sprintf(
                            'This project\'s own extensions register %d tables. Name one to read what is in it.',
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
                        '"%s" is not read here. This tool answers for the tables this project\'s own extensions '
                        . 'register, and a row of any other table is the installation\'s own — the backend and '
                        . 'vendor/bin/typo3 are where those are read, with the permissions that belong to them.',
                        $table,
                    ),
                    $countable === []
                        ? 'No extension of this project registers a table of its own.'
                        : 'What it does read: ' . implode(', ', array_column($countable, 'table')) . '.',
                ]),
                $echo + $empty,
            );
        }

        $unknown = self::unknownColumns($table, $where);
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
        $read = Typo3Runtime::records($table, $where, $limit === 0 && !$counting ? -1 : $limit);
        if (!is_array($read) || isset($read['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not read ' . $table . ': '
                . (is_array($read) ? (string) $read['unavailable'] : Typo3Runtime::reason()),
                $echo,
            );
        }

        $pages = self::pages($read['groups']);
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
                ...($read['rows'] === [] ? [] : [
                    "\n" . self::rowHeading(count($read['rows']), $counts['total'], $read),
                    ...array_map(static fn(array $row): string => sprintf(
                        '- [%d] %s%s',
                        $row['uid'],
                        $row['label'] === '' ? '(no label column)' : $row['label'],
                        $row['deleted'] ? ' — deleted' : ($row['hidden'] ? ' — hidden' : ''),
                    ), $read['rows']),
                ]),
            ]),
            $echo + [
                'matchCount' => $counts['total'],
                'answeredBy' => 'installation',
                'where' => $echoedFilter,
                'counts' => $counts,
                'pages' => $pages,
                'records' => $read['rows'],
                'countable' => $countable,
                'readWith' => self::READ_WITH,
            ],
        );
    }

    /**
     * The tables a project-owned extension registers, in the order a listing
     * reads.
     *
     * TCA says which tables there are and not whose they are, so the extension
     * is read out of the reference every ctrl title carries — the same
     * attribution the icons and the content elements are made by. A table whose
     * title names no extension belongs to the installation and is not read,
     * which is the conservative side of the boundary.
     *
     * @param array<string, mixed> $tables
     * @return array<int, array{table: string, extension: string}>
     */
    private static function countable(array $tables): array
    {
        $own = [];
        foreach (Instance::packages() as $key => $path) {
            if (Project::origin($path) === Project::ORIGIN_PROJECT) {
                $own[] = $key;
            }
        }

        $countable = [];
        foreach ($tables as $table => $title) {
            $extension = Typo3Runtime::extensionIn(is_string($title) ? $title : '');
            if ($extension !== null && in_array($extension, $own, true)) {
                $countable[] = ['table' => (string) $table, 'extension' => $extension];
            }
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
