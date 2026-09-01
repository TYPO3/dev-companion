<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * The columns TYPO3 adds to a table by itself, which an ext_tables.sql may
 * leave out.
 *
 * The core derives a table's technical columns from its TCA, and a declaration
 * that repeats them is the thing a review cannot check without asking the
 * installation. Beside them stands what the database actually has, where a
 * table is named and a schema is there to read — the difference between the two
 * is the finding, and neither side alone carries it (`D-DIS-022`). The derived
 * side answers with no schema at all, which is the state this tool is asked in
 * while the file that creates one is being written.
 */
final class SchemaLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_schema_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'List the columns TYPO3 derives for a table from its TCA — uid, pid, the timestamps, the delete and disable fields, the language and versioning columns, and one column per TCA field — each with the Doctrine type it gets, whether it is NOT NULL, and the default the core gives it. That is the DDL side of a TCA configuration: what column this field produces, whether it can hold SQL NULL, and what it stores when nothing is written. Those are also exactly the columns an ext_tables.sql does not have to declare, so this is what a redundant declaration is checked against. It asks the booted installation about a table that is in it, so it answers nothing about a table that exists only inside a functional test, and nothing about a TCA type in the abstract. It describes what TYPO3 would create, never what the database currently has, and it says so rather than answering empty when it cannot boot. It is about the shape of the table and not about what is in it: how many rows one of this project\'s own tables holds and what they are is typo3_record_lookup. A type=flex column is one column here and a data structure elsewhere: what this installation resolves it to, sheet by sheet, is typo3_flexform_lookup.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'description' => 'The table to list the derived columns of, for example "tt_content". Omit to list every table TYPO3 derives columns for, with how many each gets.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'table' => Schema::nullableString('The table asked about. Null where none was named and the answer is the list of them.'),
            'matchCount' => Schema::integer('Columns for a named table, tables for a call that named none. Zero means the name is not a TCA table in this installation, never that TYPO3 derives nothing.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'columns' => Schema::listOf(Schema::object([
                'name' => Schema::string(),
                'type' => Schema::string('The Doctrine type the core declares it as: integer, string, text, datetime, json, blob.'),
                'notnull' => ['type' => 'boolean'],
                'default' => ['description' => 'The default the core gives it, null where it declares none.'],
                'length' => ['type' => ['integer', 'null'], 'description' => 'Length where the type carries one.'],
            ], ['name', 'type', 'notnull']), 'Empty where no table was named.'),
            'tables' => Schema::listOf(Schema::object([
                'table' => Schema::string(),
                'columnCount' => Schema::integer(),
                'relationTable' => ['type' => 'boolean', 'description' => 'True where TYPO3 creates the table itself for an MM relation. No ext_tables.sql declares one at all.'],
            ], ['table', 'columnCount', 'relationTable']), 'Every table TYPO3 derives columns for. Returned on a call that named none, and on one whose name is not among them.'),
            'actual' => ['type' => ['object', 'null']] + Schema::object([
                'present' => ['type' => 'boolean', 'description' => 'Whether the database has the table at all.'],
                'columns' => Schema::listOf(Schema::object([
                    'name' => Schema::string(),
                    'type' => Schema::string('The Doctrine type the column has, read from the connection.'),
                    'notnull' => ['type' => 'boolean'],
                    'default' => ['description' => 'The default the column carries, null where it has none.'],
                    'length' => ['type' => ['integer', 'null'], 'description' => 'Length where the type carries one.'],
                ], ['name', 'type', 'notnull'])),
                'indexes' => Schema::listOf(Schema::object([
                    'name' => Schema::string(),
                    'columns' => Schema::listOf(Schema::string()),
                    'unique' => ['type' => 'boolean'],
                    'primary' => ['type' => 'boolean'],
                ], ['name', 'columns', 'unique', 'primary'])),
            ], ['present', 'columns', 'indexes'], 'What the database has for the named table, read from the connection that table maps to. Null where no table was named, or where the schema could not be read — a project that is down, or an installation whose tables were never created.'),
            'updates' => ['type' => ['array', 'null']] + Schema::listOf(Schema::object([
                'connection' => Schema::string('The TYPO3 database connection the change is on.'),
                'change' => Schema::string('The change type in TYPO3\'s own vocabulary — create_table, add, change, change_currentValue, drop, drop_table, change_table — which is also the argument `typo3 database:updateschema` takes.'),
                'tables' => Schema::listOf(Schema::string(), 'The tables that change names.'),
            ], ['connection', 'change', 'tables']), 'What TYPO3 would change to make the database match the schema its active extensions and its TCA declare. Empty where the two match, and null where no schema could be read. Where a table was named, only that table\'s changes are here.'),
        ], ['table', 'matchCount', 'answeredBy', 'columns', 'tables', 'actual', 'updates'], ['table']);
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

        // Two ways not to have an answer, and the caller acts on them
        // differently: no reading at all is the boot — a container that stayed
        // failsafe, a project that is down — while a reading carrying
        // `unavailable` is the enrichment itself, which is the database.
        $derived = Typo3Runtime::topic('derivedColumns');
        if (!is_array($derived)) {
            return Unsupported::because(Typo3Runtime::reason(), $echo);
        }
        if (isset($derived['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not derive its columns: ' . (string) $derived['unavailable'],
                $echo,
            );
        }

        /** @var array<string, array{columns: array<int, array<string, mixed>>, relationTable: bool}> $tables */
        $tables = is_array($derived['tables'] ?? null) ? $derived['tables'] : [];
        $index = array_map(static fn(string $name): array => [
            'table' => $name,
            'columnCount' => count($tables[$name]['columns']),
            'relationTable' => (bool) $tables[$name]['relationTable'],
        ], array_keys($tables));

        if ($table === '') {
            return ToolResult::create(
                implode("\n", [
                    sprintf('TYPO3 derives columns for %d tables in this installation.', count($index)),
                    'Name one to see its columns. What is listed for it is what an ext_tables.sql may leave out.',
                    '',
                    ...array_map(
                        static fn(array $entry): string => sprintf(
                            '- %s: %d columns%s',
                            $entry['table'],
                            $entry['columnCount'],
                            $entry['relationTable'] ? ' (created for an MM relation; declare nothing for it)' : '',
                        ),
                        $index,
                    ),
                ]),
                $echo + [
                    'matchCount' => count($index),
                    'answeredBy' => 'installation',
                    'columns' => [],
                    'tables' => $index,
                    // No table, no connection: the live side costs one and a
                    // caller listing tables has not asked for it — `D-DIS-022`.
                    'actual' => null,
                    'updates' => null,
                ],
            );
        }

        $actual = self::actual($table);

        if (!isset($tables[$table])) {
            return ToolResult::create(
                implode("\n", [
                    sprintf(
                        '"%s" is not a table this installation has TCA for, so TYPO3 derives no columns for it. A '
                        . 'table an extension declares without TCA is entirely its own.',
                        $table,
                    ),
                    self::liveSentence($table, $actual, false),
                    '',
                    'The tables it does derive for: ' . implode(', ', array_column($index, 'table')) . '.',
                ]),
                $echo + [
                    'matchCount' => 0,
                    'answeredBy' => 'installation',
                    'columns' => [],
                    'tables' => $index,
                    'actual' => $actual === null ? null : $actual['schema'],
                    'updates' => $actual === null ? null : $actual['updates'],
                ],
            );
        }

        $columns = $tables[$table]['columns'];

        return ToolResult::create(
            implode("\n", [
                sprintf(
                    'TYPO3 derives %d columns for %s from its TCA%s. An ext_tables.sql that declares one of them '
                    . 'again is declaring what the core already creates.',
                    count($columns),
                    $table,
                    $tables[$table]['relationTable']
                        ? ', and creates the table itself for an MM relation'
                        : '',
                ),
                self::liveSentence($table, $actual, true),
                '',
                ...array_map(static fn(array $column): string => sprintf(
                    '- %s %s%s%s',
                    $column['name'],
                    $column['type'],
                    ($column['notnull'] ?? false) === true ? ' NOT NULL' : '',
                    array_key_exists('default', $column) && $column['default'] !== null
                        ? ' DEFAULT ' . var_export($column['default'], true)
                        : '',
                ), $columns),
            ]),
            $echo + [
                'matchCount' => count($columns),
                'answeredBy' => 'installation',
                'columns' => $columns,
                'tables' => [],
                'actual' => $actual === null ? null : $actual['schema'],
                'updates' => $actual === null ? null : $actual['updates'],
            ],
        );
    }

    /**
     * What the database has for a table and what TYPO3 would change about it,
     * or null where no schema could be read.
     *
     * Both come from one reading, because both need the connection the derived
     * side does not — `D-DIS-022`.
     *
     * @return array{schema: array{present: bool, columns: array<int, array<string, mixed>>, indexes: array<int, array<string, mixed>>}, updates: array<int, array{connection: string, change: string, tables: array<int, string>}>}|null
     */
    private static function actual(string $table): ?array
    {
        $read = Typo3Runtime::liveSchema($table);
        if (!is_array($read) || isset($read['unavailable'])) {
            return null;
        }

        return [
            'schema' => [
                'present' => (bool) ($read['present'] ?? false),
                'columns' => is_array($read['columns'] ?? null) ? $read['columns'] : [],
                'indexes' => is_array($read['indexes'] ?? null) ? $read['indexes'] : [],
            ],
            'updates' => $read['suggestions'],
        ];
    }

    /**
     * The one line the live side adds to the text, which says which of three
     * states the database is in rather than repeating the columns.
     *
     * @param array{schema: array{present: bool, columns: array<int, array<string, mixed>>, indexes: array<int, array<string, mixed>>}, updates: array<int, array{connection: string, change: string, tables: array<int, string>}>}|null $actual
     */
    private static function liveSentence(string $table, ?array $actual, bool $derived): string
    {
        if ($actual === null) {
            return 'The database was not readable from here, so what follows is the derived side alone.';
        }
        if ($actual['schema']['present'] !== true) {
            return $derived
                ? sprintf(
                    'The database has no %s. On an installation whose schema was never applied that is every table.',
                    $table,
                )
                : sprintf('The database has no %s either, so no part of this installation knows the name.', $table);
        }

        $types = [];
        foreach ($actual['updates'] as $change) {
            if (in_array($table, $change['tables'], true)) {
                $types[] = $change['change'];
            }
        }
        $types = array_values(array_unique($types));

        return $types === []
            ? sprintf(
                'The database has %s with %d columns and %d indexes, and it matches what this installation declares.',
                $table,
                count($actual['schema']['columns']),
                count($actual['schema']['indexes']),
            )
            : sprintf(
                'The database has %s with %d columns and %d indexes, and TYPO3 would %s it — that is what '
                . '`vendor/bin/typo3 database:updateschema` acts on, and those words are its own argument.',
                $table,
                count($actual['schema']['columns']),
                count($actual['schema']['indexes']),
                implode(', ', $types),
            );
    }
}
