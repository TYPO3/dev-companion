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
 * How many rows a project-owned table holds, and where they sit.
 *
 * A session maintained 3101 records through the generic record list, read the
 * count twice and drew nothing from it, because nothing connected a number to
 * the question of whether that table needs a backend module of its own
 * (`feedback/archive/2026-08-31-233952`). The number is the answer, and it is
 * the whole of it: no column of any row is selected, so the boundary
 * `D-AUD-010` drew moves by a query and not by a field — `D-AUD-016`.
 */
final class RecordLookup extends ReadOnlyTool
{
    /**
     * What the answer says it was read with, on every path that carries a
     * count.
     *
     * The client launches this server as a stdio subprocess, so the count came
     * out of the database with whatever access the shell user has. That is not
     * a backend user's view of the table and never becomes one, and a caller
     * reporting the number to somebody else is the reason it is said here
     * rather than assumed.
     */
    public const READ_WITH = 'Counted with the shell user\'s database access, with no backend permissions applied '
        . 'and no workspace or language filter, so this is every row in the table rather than what a backend user '
        . 'would see.';

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
        return 'Count the rows a table of one of this project\'s own extensions holds, broken down by the page they sit on and by whether they are live, hidden or deleted. That is the fact that decides where records are maintained: a table with a few dozen rows is edited in the generic record list, and one with three thousand on a single storage folder needs a backend module with its own filtering and paging, which nothing else in this server will tell you. It counts and nothing more — no column of any row is read, so it answers nothing about what a record contains, and asking what one says is the backend\'s or the installation\'s own console. It refuses every table a project-owned extension does not register: pages, tt_content, the user tables and everything a dependency brings are outside it, because a count of those is an inventory of somebody else\'s installation rather than a fact about the work. Omit the table to see which ones it will count.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'description' => 'The table to count, for example "tx_myext_animal". Omit to list the tables this project\'s own extensions register, which are the ones that can be counted.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'table' => Schema::nullableString('The table asked about. Null where none was named and the answer is the list of countable ones.'),
            'matchCount' => Schema::integer('Rows for a named table, countable tables for a call that named none. Zero on a table that is refused as well as on one that is empty, and the text says which.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'rows' => ['type' => ['object', 'null']] + Schema::object([
                'total' => Schema::integer('Every row in the table, whatever state it is in.'),
                'live' => Schema::integer('Rows that are neither hidden nor deleted.'),
                'hidden' => Schema::integer('Rows the disable field hides. Zero where the table declares no such field.'),
                'deleted' => Schema::integer('Rows the delete field marks. They are still in the table until the garbage collection runs.'),
            ], ['total', 'live', 'hidden', 'deleted'], 'Null where no table was counted.'),
            'pages' => Schema::listOf(Schema::object([
                'pid' => Schema::integer('The page the rows sit on. Zero is the root, which is where records that belong to no page end up.'),
                'total' => Schema::integer(),
                'live' => Schema::integer(),
                'hidden' => Schema::integer(),
                'deleted' => Schema::integer(),
            ], ['pid', 'total', 'live', 'hidden', 'deleted']), 'One entry per page that holds a row, the fullest first. Empty where no table was counted.'),
            'countable' => Schema::listOf(Schema::object([
                'table' => Schema::string(),
                'extension' => Schema::string('The project-owned extension whose TCA registers it.'),
            ], ['table', 'extension']), 'Every table this tool will count in this installation.'),
            'readWith' => Schema::string('What the count was read with. Said on every answer that carries one, because a number reported onwards is read as a backend user\'s view of the table unless it says otherwise.'),
        ], ['table', 'matchCount', 'answeredBy', 'rows', 'pages', 'countable', 'readWith'], ['table']);
    }

    public static function answer(array $args): ToolResult
    {
        $table = trim((string) ($args['table'] ?? ''));
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

        if ($table === '') {
            return ToolResult::create(
                implode("\n", [
                    $countable === []
                        ? 'No extension of this project registers a table of its own, so there is nothing here to '
                            . 'count. A project whose content lives in pages and tt_content is that case.'
                        : sprintf(
                            'This project\'s own extensions register %d tables. Name one to count its rows, which '
                            . 'is what says whether it is still edited in the record list.',
                            count($countable),
                        ),
                    '',
                    ...array_map(
                        static fn(array $entry): string => sprintf('- %s (%s)', $entry['table'], $entry['extension']),
                        $countable,
                    ),
                ]),
                $echo + [
                    'matchCount' => count($countable),
                    'answeredBy' => 'installation',
                    'rows' => null,
                    'pages' => [],
                    'countable' => $countable,
                    'readWith' => self::READ_WITH,
                ],
            );
        }

        if (!in_array($table, array_column($countable, 'table'), true)) {
            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        '"%s" is not counted here. This tool answers for the tables this project\'s own extensions '
                        . 'register, and a row of any other table is the installation\'s own — the backend and '
                        . 'vendor/bin/typo3 are where those are read, with the permissions that belong to them.',
                        $table,
                    ),
                    $countable === []
                        ? 'No extension of this project registers a table of its own.'
                        : 'What it does count: ' . implode(', ', array_column($countable, 'table')) . '.',
                ]),
                $echo + [
                    'matchCount' => 0,
                    'answeredBy' => 'installation',
                    'rows' => null,
                    'pages' => [],
                    'countable' => $countable,
                    'readWith' => self::READ_WITH,
                ],
            );
        }

        $counted = Typo3Runtime::recordCount($table);
        if (!is_array($counted) || isset($counted['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not count ' . $table . ': '
                . (is_array($counted) ? (string) $counted['unavailable'] : Typo3Runtime::reason()),
                $echo,
            );
        }

        $pages = self::pages($counted['groups']);
        $rows = [
            'total' => array_sum(array_column($pages, 'total')),
            'live' => array_sum(array_column($pages, 'live')),
            'hidden' => array_sum(array_column($pages, 'hidden')),
            'deleted' => array_sum(array_column($pages, 'deleted')),
        ];

        return ToolResult::create(
            implode("\n", [
                sprintf(
                    '%s holds %d rows: %d live, %d hidden, %d deleted%s.',
                    $table,
                    $rows['total'],
                    $rows['live'],
                    $rows['hidden'],
                    $rows['deleted'],
                    $counted['deleteField'] === '' ? ' (the table declares no delete field)' : '',
                ),
                self::verdict($rows['total'], $pages),
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
            ]),
            $echo + [
                'matchCount' => $rows['total'],
                'answeredBy' => 'installation',
                'rows' => $rows,
                'pages' => $pages,
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
     * title names no extension belongs to the installation and is not counted,
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
     * The groupings the probe returned, folded to one entry per page.
     *
     * The probe groups by every flag the table declares, so one page arrives as
     * up to four rows. A deleted row that is also hidden is counted as deleted:
     * a caller asking what is still there wants one number per row, and deleted
     * is the state that answers it.
     *
     * @param array<int, array<string, mixed>> $groups
     * @return array<int, array{pid: int, total: int, live: int, hidden: int, deleted: int}>
     */
    private static function pages(array $groups): array
    {
        $pages = [];
        foreach ($groups as $group) {
            $pid = (int) ($group['pid'] ?? 0);
            $rows = (int) ($group['rows'] ?? 0);
            $pages[$pid] ??= ['pid' => $pid, 'total' => 0, 'live' => 0, 'hidden' => 0, 'deleted' => 0];
            $pages[$pid]['total'] += $rows;
            $state = ($group['deleted'] ?? false) === true
                ? 'deleted'
                : ((($group['hidden'] ?? false) === true) ? 'hidden' : 'live');
            $pages[$pid][$state] += $rows;
        }
        usort($pages, static fn(array $one, array $other): int => [$other['total'], $one['pid']] <=> [$one['total'], $other['pid']]);

        return $pages;
    }

    /**
     * The sentence the count exists for, and the one the sighted session did
     * not have.
     *
     * A number on its own was in that session's hands twice and changed
     * nothing, so the answer says what the number means for where the records
     * are edited. The threshold is the record list's own page size of 20: one
     * page of it is a table nobody has to leave the list for, and a storage
     * folder that runs to dozens of pages is the case the sighting was.
     *
     * @param array<int, array{pid: int, total: int, live: int, hidden: int, deleted: int}> $pages
     */
    private static function verdict(int $total, array $pages): string
    {
        $fullest = $pages[0] ?? null;
        if ($fullest === null || $total === 0) {
            return 'Nothing is stored in it yet, so where it is edited is still open.';
        }
        if ($fullest['total'] <= 20) {
            return sprintf(
                'The fullest page holds %d, which is one page of the record list. Nothing here asks for an editing '
                . 'surface of its own.',
                $fullest['total'],
            );
        }

        return sprintf(
            'The fullest page holds %d, which is %d pages of the record list at 20 rows each. An editor maintaining '
            . 'that through the generic list has no filtering and no search over it, so this is where a backend '
            . 'module of its own is the question — typo3_backend_module_lookup reports what this installation '
            . 'already registers.',
            $fullest['total'],
            (int) ceil($fullest['total'] / 20),
        );
    }
}
