<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What may go into one `type=flex` column, resolved by the installation that
 * would resolve it.
 *
 * The two calls `TcaFlexPrepare` makes and nothing else. So what comes back is
 * what the backend form builds rather than what the file the TCA points at
 * says. Why that is a different answer, and why the record comes from values
 * the caller passes rather than from a load: `D-ANS-095`.
 */
final class FlexFormLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_flexform_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'Resolve one TCA field of type=flex to the data structure the installation uses. That is the identifier TYPO3 produces for it, that identifier decoded, and every sheet and field of the structure with label, type and items. This is what the backend form builds, not what the referenced FlexForm file says. The installation resolves it through its own FlexFormTools. So a data structure a listener replaced and a sheet in a file of its own are in the answer. So are the default sDEF sheet a structure without sheets gets, and the TCA migration and preparation each field goes through. Which structure applies can depend on the record, so pass the values that decide it in record. That is CType for a content element or a plugin, and list_type beside it on TYPO3 12 and 13. Nothing loads a row; the tool emulates the record from exactly those values. Where the resolution throws, that is the answer. An empty ds, a column that is not type=flex, and a record type with no structure are that case. The exception comes back with the keys and the record fields that would have resolved. For the columns the table itself gets, ask typo3_schema_lookup; for what a content element registers, typo3_extension_describe.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'table' => ['type' => 'string', 'minLength' => 1, 'description' => 'The table the column is on, for example "tt_content".'],
                'field' => ['type' => 'string', 'minLength' => 1, 'description' => 'The type=flex column to resolve, for example "pi_flexform".'],
                'record' => [
                    'type' => 'object',
                    'additionalProperties' => ['type' => 'string'],
                    'description' => 'Column values the emulated record carries, as column => value. Pass only what decides which data structure applies. That is "CType" for a content element, and "list_type" beside it for a plugin on TYPO3 12 and 13. Omit it for a column that declares one structure and no record type.',
                ],
            ],
            'required' => ['table', 'field'],
        ];
    }

    public static function outputSchema(): array
    {
        $field = [
            'field' => Schema::string('The name the value sits under, which is what a Fluid template and a settings array read it by.'),
            'label' => Schema::string('As the structure declares it, an LLL: reference included.'),
            'description' => Schema::string(),
            'type' => Schema::string('The TCA type of this field, or "section" for a repeatable section.'),
            'renderType' => Schema::string('Empty where the type has no render type.'),
            'required' => ['type' => 'boolean'],
            'default' => ['description' => 'The default the field declares, null where it declares none or where it is not scalar.'],
            'items' => Schema::listOf(Schema::object([
                'value' => Schema::string(),
                'label' => Schema::string(),
            ], ['value', 'label']), 'The selectable items, where the field has any.'),
        ];

        return Schema::installationAnswer([
            'table' => Schema::string('The table asked about.'),
            'field' => Schema::string('The column asked about.'),
            'resolved' => ['type' => 'boolean', 'description' => 'Whether the installation resolved the column to a data structure. False means the answer is the failure or the declaration beside it, never that the column has none.'],
            'identifier' => Schema::string('The data structure identifier as TYPO3 produced it: the JSON string that resolves to this structure without the record again. Empty where nothing resolved.'),
            'decoded' => ['type' => ['object', 'null'], 'description' => 'The same identifier as an object. The default carries type, tableName, fieldName and dataStructureKey; a listener may return another shape entirely.'],
            'sheets' => Schema::listOf(Schema::object([
                'sheet' => Schema::string('The sheet key the values sit under. A structure that declares no sheets gets sDEF here, which the parse adds.'),
                'title' => Schema::string(),
                'description' => Schema::string(),
                'fields' => Schema::listOf(Schema::object($field + [
                    'containers' => Schema::listOf(Schema::object([
                        'container' => Schema::string('The container type key, which is what a section entry stores its type as.'),
                        'title' => Schema::string(),
                        'fields' => Schema::listOf(Schema::object($field, ['field', 'type'])),
                    ], ['container', 'fields']), 'The container types of a section, empty for every other field.'),
                ], ['field', 'type', 'containers'])),
            ], ['sheet', 'title', 'fields']), 'Every sheet of the parsed structure, in the order it declares them.'),
            'failure' => Schema::string('The exception the resolution threw, with its class and code. Empty where it did not throw. It is an answer rather than a breakage. An empty ds, a column that is not type=flex and a record type with no structure all report themselves this way.'),
            'declaration' => Schema::object([
                'type' => Schema::string('The TCA type of the column, empty where the table has no such column.'),
                'recordTypeField' => Schema::string('The column TYPO3 reads the record type from, empty where the table has no record types.'),
                'keys' => Schema::listOf(Schema::string(), 'The data structure keys this column declares. Where the TCA holds an array of structures they are its keys. Where it holds one they are "default" plus every record type that overrides it.'),
                'pointerFields' => Schema::listOf(Schema::string(), 'The columns ds_pointerField names, which is what the keys above resolve by. Empty on TYPO3 14 and up, where columnsOverrides replaced the mechanism.'),
                'flexFields' => Schema::listOf(Schema::string(), 'Every type=flex column this table has, which is what to ask for instead where the named one is not one.'),
            ], ['type', 'recordTypeField', 'keys', 'pointerFields', 'flexFields'], 'What the TCA declares about this column, which is what you retry a call that resolved nothing with.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['table', 'field', 'resolved', 'identifier', 'sheets', 'failure', 'declaration', 'answeredBy'], ['table', 'field']);
    }

    public static function answer(array $args): ToolResult
    {
        $table = trim((string) ($args['table'] ?? ''));
        $field = trim((string) ($args['field'] ?? ''));
        $echo = ['table' => $table, 'field' => $field];

        if ($table === '' || $field === '') {
            return Unsupported::because('a table and a field are both needed to resolve a flex column', $echo);
        }
        if (!Instance::isAvailable()) {
            return Unsupported::because(
                'no TYPO3 installation was found from the directory this server was started in',
                $echo,
            );
        }

        $record = [];
        foreach (is_array($args['record'] ?? null) ? $args['record'] : [] as $column => $value) {
            if (is_scalar($value)) {
                $record[(string) $column] = (string) $value;
            }
        }

        $read = Typo3Runtime::flexForm($table, $field, $record);
        if ($read === null) {
            return Unsupported::because(Typo3Runtime::reason(), $echo);
        }
        if (isset($read['unavailable'])) {
            return Unsupported::because(
                'the installation booted and could not be asked about this column: ' . (string) $read['unavailable'],
                $echo,
            );
        }

        $declaration = [
            'type' => (string) ($read['type'] ?? ''),
            'recordTypeField' => (string) ($read['recordTypeField'] ?? ''),
            'keys' => array_map('strval', (array) ($read['keys'] ?? [])),
            'pointerFields' => array_map('strval', (array) ($read['pointerFields'] ?? [])),
            'flexFields' => array_map('strval', (array) ($read['flexFields'] ?? [])),
        ];
        /** @var array<int, array<string, mixed>> $sheets */
        $sheets = is_array($read['sheets'] ?? null) ? array_values($read['sheets']) : [];
        $failure = (string) ($read['failure'] ?? '');
        $identifier = (string) ($read['identifier'] ?? '');

        $resolved = $failure === '' && $identifier !== '';
        $data = $echo + [
            'resolved' => $resolved,
            'identifier' => $identifier,
            'decoded' => is_array($read['decoded'] ?? null) ? $read['decoded'] : null,
            'sheets' => $sheets,
            'failure' => $failure,
            'declaration' => $declaration,
            'answeredBy' => 'installation',
        ];

        if (!$resolved) {
            return ToolResult::create(
                self::unresolved($table, $field, $failure, ($read['tableFound'] ?? false) === true, $declaration),
                $data,
            );
        }

        return ToolResult::create(self::structure($table, $field, $identifier, $sheets, $record), $data);
    }

    /**
     * The structure as the installation resolved it.
     *
     * @param array<int, array<string, mixed>> $sheets
     * @param array<string, string>            $record
     */
    private static function structure(
        string $table,
        string $field,
        string $identifier,
        array $sheets,
        array $record,
    ): string {
        $lines = [
            sprintf(
                '%s.%s resolves to this data structure in this installation%s.',
                $table,
                $field,
                $record === [] ? '' : ', for a record with ' . self::values($record),
            ),
            '',
            'Identifier: ' . $identifier,
        ];

        foreach ($sheets as $sheet) {
            $title = (string) ($sheet['title'] ?? '');
            $lines[] = '';
            $lines[] = sprintf(
                'Sheet %s%s',
                (string) ($sheet['sheet'] ?? ''),
                $title === '' ? '' : ' — ' . $title,
            );
            foreach (is_array($sheet['fields'] ?? null) ? $sheet['fields'] : [] as $entry) {
                $lines = [...$lines, ...self::field($entry, '  ')];
            }
        }

        $lines[] = '';
        $lines[] = 'This went through the installation\'s own FlexFormTools, so it is what the backend form builds '
            . 'rather than what the referenced file says: a listener may have replaced it, a sheet in a file of its '
            . 'own is resolved, and every field is migrated and prepared. The record was emulated from the values '
            . 'above and no row was read. What is listed per field is what writing or reading the FlexForm needs; '
            . 'the rest of each field\'s prepared TCA is not carried.';

        return implode("\n", $lines);
    }

    /**
     * One field, and the containers of a section under it.
     *
     * @param array<string, mixed> $entry
     * @return array<int, string>
     */
    private static function field(array $entry, string $indent): array
    {
        $label = (string) ($entry['label'] ?? '');
        $items = is_array($entry['items'] ?? null) ? $entry['items'] : [];
        $lines = [sprintf(
            '%s- %s (%s)%s%s%s',
            $indent,
            (string) ($entry['field'] ?? ''),
            (string) ($entry['type'] ?? ''),
            $label === '' ? '' : ' ' . $label,
            ($entry['required'] ?? false) === true ? ' — required' : '',
            $items === [] ? '' : ' — items: ' . implode(', ', array_map(
                static fn(array $item): string => (string) $item['value'],
                $items,
            )),
        )];

        foreach (is_array($entry['containers'] ?? null) ? $entry['containers'] : [] as $container) {
            $lines[] = sprintf('%s  container %s', $indent, (string) ($container['container'] ?? ''));
            foreach (is_array($container['fields'] ?? null) ? $container['fields'] : [] as $inside) {
                $lines = [...$lines, ...self::field($inside, $indent . '    ')];
            }
        }

        return $lines;
    }

    /**
     * Nothing resolved, and what the caller does about it.
     *
     * The three cases the todo behind `D-ANS-095` names are all the same shape.
     * The installation reported with a throw, and the exception plus what the
     * TCA declares is more than a retry could work out for itself.
     *
     * @param array{type: string, recordTypeField: string, keys: array<int, string>, pointerFields: array<int, string>, flexFields: array<int, string>} $declaration
     */
    private static function unresolved(
        string $table,
        string $field,
        string $failure,
        bool $tableFound,
        array $declaration,
    ): string {
        if (!$tableFound) {
            return sprintf(
                '"%s" is not a table this installation has TCA for, so it has no flex column to resolve. A table an '
                . 'extension declares without TCA is entirely its own.',
                $table,
            );
        }

        $lines = [$declaration['type'] === 'flex'
            ? sprintf('%s.%s is a flex column and this installation resolved no structure for it.', $table, $field)
            : sprintf(
                '%s.%s is %s in this installation, not type=flex, so there is no data structure behind it.',
                $table,
                $field,
                $declaration['type'] === '' ? 'not a column' : 'type=' . $declaration['type'],
            )];

        if ($failure !== '') {
            $lines[] = '';
            $lines[] = 'The resolution said: ' . $failure;
        }

        if ($declaration['keys'] !== []) {
            $lines[] = '';
            $lines[] = sprintf(
                'The column declares structures under: %s.%s',
                implode(', ', $declaration['keys']),
                $declaration['pointerFields'] === []
                    ? ($declaration['recordTypeField'] === ''
                        ? ''
                        : ' Which one applies is decided by the record type in ' . $declaration['recordTypeField']
                            . ', so pass that column in record.')
                    : ' Which one applies is looked up by ' . implode(' and ', $declaration['pointerFields'])
                        . ', so pass those columns in record.',
            );
        }

        if ($declaration['flexFields'] !== []) {
            $lines[] = '';
            $lines[] = 'The flex columns of ' . $table . ': ' . implode(', ', $declaration['flexFields']) . '.';
        }

        return implode("\n", $lines);
    }

    /** @param array<string, string> $record */
    private static function values(array $record): string
    {
        return implode(', ', array_map(
            static fn(string $column, string $value): string => $column . '=' . $value,
            array_keys($record),
            $record,
        ));
    }
}
