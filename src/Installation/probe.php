<?php

declare(strict_types=1);

/**
 * What the running installation says about itself.
 *
 * The server never includes this file. It comes in as text and goes to the
 * installation's own interpreter as a subprocess, through DDEV where the
 * project runs there. So everything below executes on the other side of a
 * process boundary, with the installation's autoloader, its PHP version and its
 * extensions. A fatal error here is an exit code rather than a dead MCP
 * session.
 *
 * Two properties carry weight and both look like omissions:
 *
 * - No `declare(strict_types=1)`. The body travels through `php -r`, which
 *   wraps it, and a declare is only legal as the very first statement of a
 *   script. Typo3Runtime strips the open tag for the same reason.
 * - The autoloader path is relative and goes into the literal below before
 *   delivery. The two sides of DDEV do not share absolute paths. The subprocess
 *   starts with the installation root as its working directory, and inside the
 *   container that same root is /var/www/html.
 *
 * What a caller asked for goes in the same way, as one array. The topics that
 * read it are the ones no other read wants. TYPO3_CONF_VARS is around 50 kB of
 * JSON before an extension has added to it. A flex field costs a resolution
 * nobody who asked about an icon has a use for.
 *
 * It prints one JSON object on stdout and nothing else. TYPO3's own output
 * buffer goes first, because an extension that echoes during boot would
 * otherwise sit in front of the payload.
 */
$answer = ['state' => 'unreachable', 'reason' => '', 'topics' => []];

try {
    // Typo3Runtime replaces it before delivery; a literal so the file stays
    // valid PHP a linter and a reader can take on its own.
    $autoload = 'vendor/autoload.php';
    // What the call asked for, in the same way. Empty unless a caller asked a
    // topic that takes an argument, and then the only reason that topic reads
    // at all.
    $parameters = [];
    $configurationPath = (string) ($parameters['configurationPath'] ?? '');
    $flexForm = is_array($parameters['flexForm'] ?? null) ? $parameters['flexForm'] : null;
    $liveSchema = is_array($parameters['liveSchema'] ?? null) ? $parameters['liveSchema'] : null;
    $records = is_array($parameters['records'] ?? null) ? $parameters['records'] : null;
    $services = is_array($parameters['services'] ?? null) ? $parameters['services'] : null;
    if (!is_file($autoload)) {
        $answer['reason'] = 'no autoloader at ' . $autoload . ' below ' . getcwd();
        throw new RuntimeException('', 1);
    }

    $classLoader = require $autoload;
    TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::run(
        0,
        TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_CLI
    );
    $container = TYPO3\CMS\Core\Core\Bootstrap::init($classLoader);

    // A system without essential configuration boots into a failsafe container.
    // Core packages only, no ext_localconf.php, no TCA. Its registries answer,
    // and what they answer is a subset that looks like the whole. A name for
    // that state is the entire point of the question.
    if ($container instanceof TYPO3\CMS\Core\DependencyInjection\FailsafeContainer) {
        $answer['state'] = 'failsafe';
        $answer['reason'] = 'the installation has no essential configuration yet, so TYPO3 booted failsafe '
            . 'with core packages only and no extension registrations';
        throw new RuntimeException('', 1);
    }

    $answer['state'] = 'full';

    // One path out of TYPO3_CONF_VARS as it stands after every extension has
    // had its say. `ArrayUtility` is the core's own read of such a path and is
    // what `configuration:show --type=active` traverses with. So a caller gets
    // the same value on a line that has that command and on the two that do
    // not. In a try of its own: a failure here is one topic.
    if ($configurationPath !== '') {
        try {
            $found = TYPO3\CMS\Core\Utility\ArrayUtility::isValidPath(
                $GLOBALS['TYPO3_CONF_VARS'],
                $configurationPath,
            );
            $answer['topics']['configuration'] = [
                'found' => $found,
                'value' => $found ? TYPO3\CMS\Core\Utility\ArrayUtility::getValueByPath(
                    $GLOBALS['TYPO3_CONF_VARS'],
                    $configurationPath,
                ) : null,
            ];
        } catch (Throwable $failure) {
            $answer['topics']['configuration'] = [
                'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
            ];
        }
    }

    $registry = $container->get(TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $icons = [];
    foreach ($registry->getAllRegisteredIconIdentifiers() as $identifier) {
        $identifier = (string) $identifier;
        $configuration = $registry->getIconConfigurationByIdentifier($identifier);
        $options = is_array($configuration['options'] ?? null) ? $configuration['options'] : [];
        // The source is what says which extension an identifier belongs to.
        // EXT:news/Resources/Public/Icons/… is the only attribution the
        // registry carries, and a bitmap or sprite icon names it differently.
        $source = $options['source'] ?? ($options['name'] ?? '');
        $icons[$identifier] = is_string($source) ? $source : '';
    }
    $answer['topics']['icons'] = $icons;

    // TCA as it is after every extension has had its say. That is where the
    // tables an extension adds through a PHP call and the content elements
    // registered from a variable exist at all. What TCA does not carry is which
    // extension an entry belongs to. An answer about one extension cannot use a
    // list that belongs to all of them. So each entry travels with what names
    // an extension in it. A label or a ctrl title is `LLL:EXT:<key>/…`, and an
    // item's icon resolves through the registry to `EXT:<key>/…`. Both come in
    // here and get their attribution on the other side. Where neither names
    // anything the entry is the installation's rather than a package's.
    $tca = is_array($GLOBALS['TCA'] ?? null) ? $GLOBALS['TCA'] : [];
    $tables = [];
    foreach ($tca as $table => $configuration) {
        $title = $configuration['ctrl']['title'] ?? '';
        $tables[(string) $table] = is_string($title) ? $title : '';
    }
    $answer['topics']['tables'] = $tables;

    $contentElements = [];
    foreach ($tca['tt_content']['columns']['CType']['config']['items'] ?? [] as $item) {
        if (!is_array($item)) {
            continue;
        }
        // Keyed since v12, positional before it, and both shapes are in the
        // wild because an extension serves the line it supports.
        $value = $item['value'] ?? ($item[1] ?? null);
        if (!is_string($value) || $value === '' || $value === '--div--') {
            continue;
        }
        $label = $item['label'] ?? ($item[0] ?? '');
        $icon = $item['icon'] ?? ($item[2] ?? '');
        $contentElements[$value] = [
            'label' => is_string($label) ? $label : '',
            'icon' => is_string($icon) ? $icon : '',
        ];
    }
    $answer['topics']['contentElements'] = $contentElements;

    // One type=flex column resolved the way FormEngine resolves it, which is
    // the two calls TcaFlexPrepare makes and nothing else. The identifier, and
    // the structure that identifier parses to. Everything between them is the
    // installation's: the events a package listens to, the file that holds a
    // sheet, the migration and the preparation. None of it is in the file the
    // TCA points at. The row is the caller's. FlexFormTools needs one to find
    // the key with, and nothing here loads one. Read only where a caller asked,
    // for the reason the configuration path is.
    if ($flexForm !== null) {
        $table = (string) ($flexForm['table'] ?? '');
        $field = (string) ($flexForm['field'] ?? '');
        $record = is_array($flexForm['record'] ?? null) ? $flexForm['record'] : [];
        $tableTca = is_array($tca[$table] ?? null) ? $tca[$table] : null;
        $columnTca = is_array($tableTca['columns'][$field] ?? null) ? $tableTca['columns'][$field] : null;
        $configuration = is_array($columnTca['config'] ?? null) ? $columnTca['config'] : [];
        $declared = $configuration['ds'] ?? null;

        $flexColumns = [];
        foreach ($tableTca['columns'] ?? [] as $column => $other) {
            if (($other['config']['type'] ?? '') === 'flex') {
                $flexColumns[] = (string) $column;
            }
        }

        // What a caller can put in the record to reach another structure, read
        // off the declaration in the shape this installation has it in. The
        // pointer fields key an array of structures; a single one has an
        // override per record type. Those are the two mechanisms the covered
        // majors differ by. The core's own resolution branches on the same
        // shape rather than on a version.
        $keys = [];
        $pointerFields = [];
        if (is_array($declared)) {
            $keys = array_map('strval', array_keys($declared));
            $pointerFields = array_values(array_filter(array_map(
                'trim',
                explode(',', (string) ($configuration['ds_pointerField'] ?? '')),
            )));
        } elseif (is_string($declared) && $declared !== '') {
            $keys = ['default'];
            foreach ($tableTca['types'] ?? [] as $type => $override) {
                if (is_string($override['columnsOverrides'][$field]['config']['ds'] ?? null)) {
                    $keys[] = (string) $type;
                }
            }
        }

        $topic = [
            'table' => $table,
            'field' => $field,
            'tableFound' => $tableTca !== null,
            'type' => (string) ($configuration['type'] ?? ''),
            'flexFields' => $flexColumns,
            'recordTypeField' => (string) ($tableTca['ctrl']['type'] ?? ''),
            'keys' => array_values(array_unique($keys)),
            'pointerFields' => $pointerFields,
            'identifier' => '',
            'decoded' => null,
            'sheets' => [],
            'failure' => '',
        ];

        // What a caller who writes or reads a FlexForm needs of an element,
        // rather than the prepared TCA of it. The same fields the backend form
        // labels an input with, and not the rest of what the preparation left
        // on it.
        $summarize = static function (array $elements) use (&$summarize): array {
            $fields = [];
            foreach ($elements as $name => $element) {
                if (!is_array($element)) {
                    continue;
                }
                $config = is_array($element['config'] ?? null) ? $element['config'] : [];
                $items = [];
                foreach ($config['items'] ?? [] as $item) {
                    if (is_array($item)) {
                        $items[] = [
                            'value' => (string) ($item['value'] ?? ($item[1] ?? '')),
                            'label' => (string) ($item['label'] ?? ($item[0] ?? '')),
                        ];
                    }
                }
                // A section holds container types and each of those holds
                // fields, which is the one nesting a data structure has.
                $section = ($element['section'] ?? '') === '1';
                $containers = [];
                $inContainers = $section && is_array($element['el'] ?? null) ? $element['el'] : [];
                foreach ($inContainers as $container => $inside) {
                    $containers[] = [
                        'container' => (string) $container,
                        'title' => (string) ($inside['title'] ?? ''),
                        'fields' => $summarize(is_array($inside['el'] ?? null) ? $inside['el'] : []),
                    ];
                }
                $default = $config['default'] ?? null;
                $fields[] = [
                    'field' => (string) $name,
                    // A field carries a label and a section carries a title,
                    // which is the same line to a reader of the answer.
                    'label' => (string) ($element['label'] ?? ($element['title'] ?? '')),
                    'description' => (string) ($element['description'] ?? ''),
                    'type' => $section ? 'section' : (string) ($config['type'] ?? ''),
                    'renderType' => (string) ($config['renderType'] ?? ''),
                    'required' => (bool) ($config['required'] ?? false),
                    'default' => is_scalar($default) ? $default : null,
                    'items' => $items,
                    'containers' => $containers,
                ];
            }

            return $fields;
        };

        // In a try of its own, and its failure is the answer rather than an
        // absent topic. An empty ds, a column that is not type=flex and a
        // record type nothing registers for all report with a throw. What they
        // throw is what the caller has to read.
        try {
            $tools = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class,
            );
            // The installation answers what its own signature is rather than
            // hears it from a version number. TYPO3 v14 resolves against a
            // TcaSchema the caller hands in and throws where it is null. v12
            // and v13 read the global TCA and have no such parameter.
            $wantsSchema = (new ReflectionMethod($tools, 'getDataStructureIdentifier'))->getNumberOfParameters() > 4;
            $schema = null;
            if ($wantsSchema) {
                $factory = $container->get(TYPO3\CMS\Core\Schema\TcaSchemaFactory::class);
                $schema = $factory->has($table) ? $factory->get($table) : null;
            }

            $identifier = $wantsSchema
                ? $tools->getDataStructureIdentifier($columnTca ?? [], $table, $field, $record, $schema)
                : $tools->getDataStructureIdentifier($columnTca ?? [], $table, $field, $record);
            $parsed = $wantsSchema
                ? $tools->parseDataStructureByIdentifier($identifier, $schema)
                : $tools->parseDataStructureByIdentifier($identifier);

            $topic['identifier'] = (string) $identifier;
            $topic['decoded'] = json_decode((string) $identifier, true);
            foreach ($parsed['sheets'] ?? [] as $sheet => $definition) {
                $root = is_array($definition['ROOT'] ?? null) ? $definition['ROOT'] : [];
                $topic['sheets'][] = [
                    'sheet' => (string) $sheet,
                    'title' => (string) ($root['sheetTitle'] ?? ''),
                    'description' => (string) ($root['sheetDescription'] ?? ''),
                    'fields' => $summarize(is_array($root['el'] ?? null) ? $root['el'] : []),
                ];
            }
        } catch (Throwable $failure) {
            $topic['failure'] = get_class($failure) . ' (' . $failure->getCode() . '): ' . $failure->getMessage();
        }

        $answer['topics']['flexForm'] = $topic;
    }

    // The columns TYPO3 adds to a table by itself, which is what an
    // ext_tables.sql may leave out. DefaultTcaSchema gets one empty table per
    // TCA table, it throws where one is absent. So everything it comes back
    // with derives rather than comes from a declaration. It reaches the
    // ConnectionPool for the platform of each table. The MySQL, MariaDB and
    // PostgreSQL drivers ask the server for that and the SQLite one does not
    // (D-DIS-012). In a try of its own. A failure here is one topic, and the
    // icons and the TCA above are already in.
    try {
        $tables = [];
        foreach (array_keys($tca) as $table) {
            $tables[(string) $table] = new Doctrine\DBAL\Schema\Table((string) $table);
        }
        // Built by hand, because the class is a private service. What its
        // constructor takes moved on main, where it stopped to default its
        // dependencies, and `makeInstance` builds it with none, `D-DIS-025`.
        // The container supplies each one the constructor declares.
        $dependencies = [];
        $constructor = (new ReflectionClass(TYPO3\CMS\Core\Database\Schema\DefaultTcaSchema::class))
            ->getConstructor();
        foreach ($constructor === null ? [] : $constructor->getParameters() as $dependency) {
            $type = $dependency->getType();
            $dependencies[] = $type instanceof ReflectionNamedType ? $container->get($type->getName()) : null;
        }
        $derived = [];
        $enriched = (new TYPO3\CMS\Core\Database\Schema\DefaultTcaSchema(...$dependencies))->enrich($tables);
        foreach ($enriched as $definition) {
            $table = $definition->getName();
            $columns = [];
            foreach ($definition->getColumns() as $column) {
                $default = $column->getDefault();
                $columns[] = [
                    'name' => $column->getName(),
                    'type' => Doctrine\DBAL\Types\Type::lookupName($column->getType()),
                    'notnull' => $column->getNotnull(),
                    'default' => is_scalar($default) || $default === null ? $default : (string) $default,
                    'length' => $column->getLength(),
                ];
            }
            // A table the enrichment created rather than enriched is an MM
            // table. It exists because a relation asked for it, and no
            // ext_tables.sql needs to declare it at all.
            $derived[(string) $table] = [
                'columns' => $columns,
                'relationTable' => !array_key_exists((string) $table, $tca),
            ];
        }
        $answer['topics']['derivedColumns'] = ['tables' => $derived];
    } catch (Throwable $failure) {
        $answer['topics']['derivedColumns'] = [
            'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
        ];
    }
    // On request rather than with everything else. It opens a connection and
    // lists a schema, which a caller who asked about an icon should not pay
    // for. The derived columns above say what TYPO3 would create; this says
    // what is there, and the difference is the finding, `D-DIS-022`.
    if ($liveSchema !== null) {
        try {
            $wanted = (string) ($liveSchema['table'] ?? '');
            $pool = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Database\ConnectionPool::class,
            );
            $connection = $pool->getConnectionForTable($wanted !== '' ? $wanted : 'pages');
            $manager = $connection->createSchemaManager();
            $names = $manager->listTableNames();
            sort($names, SORT_NATURAL);
            $topic = ['tables' => $names];
            if ($wanted !== '') {
                // A table the schema does not have is an answer rather than a
                // failure. An installation whose tables never came to be is the
                // case the derived side exists for.
                $topic['table'] = $wanted;
                $topic['present'] = in_array($wanted, $names, true);
                if ($topic['present']) {
                    $columns = [];
                    foreach ($manager->listTableColumns($wanted) as $column) {
                        $default = $column->getDefault();
                        $columns[] = [
                            'name' => $column->getName(),
                            'type' => Doctrine\DBAL\Types\Type::lookupName($column->getType()),
                            'notnull' => $column->getNotnull(),
                            'default' => is_scalar($default) || $default === null ? $default : (string) $default,
                            'length' => $column->getLength(),
                        ];
                    }
                    $indexes = [];
                    foreach ($manager->listTableIndexes($wanted) as $index) {
                        $indexes[] = [
                            'name' => $index->getName(),
                            'columns' => array_values($index->getColumns()),
                            'unique' => $index->isUnique(),
                            'primary' => $index->isPrimary(),
                        ];
                    }
                    $topic['columns'] = $columns;
                    $topic['indexes'] = $indexes;
                }
            }

            // TYPO3 answers what the two sides differ by rather than a
            // computation here. `SqlReader` assembles the effective schema,
            // every active extension's ext_tables.sql and what TCA generates.
            // The migrator diffs that against the connection, which is the read
            // the Install Tool and database:updateschema act on. Its change
            // types are that command's own argument. The statements themselves
            // drop out and only the tables they name stay. SQLite cannot alter
            // a column, so one extra column comes back as a four-kilobyte table
            // rebuild. A caller who wants the SQL has the command that prints
            // it.
            $reader = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Database\Schema\SqlReader::class,
            );
            $migrator = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Database\Schema\SchemaMigrator::class,
            );
            $statements = $reader->getCreateTableStatementArray($reader->getTablesDefinitionString());
            $declared = [];
            foreach ($statements as $statement) {
                if (preg_match('/CREATE TABLE\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i', (string) $statement, $found) === 1) {
                    $declared[] = $found[1];
                }
            }
            $known = array_values(array_unique(array_merge($names, $declared)));
            $suggestions = [];
            foreach ([false, true] as $remove) {
                foreach ($migrator->getUpdateSuggestions($statements, $remove) as $name => $types) {
                    foreach (is_array($types) ? $types : [] as $type => $entries) {
                        foreach (is_array($entries) ? $entries : [] as $entry) {
                            if (!is_string($entry) || $entry === '') {
                                continue;
                            }
                            // Only where a name stands in a table position. A
                            // column called `backend_layout` sits in the column
                            // list of a rebuilt `pages`. A match on the bare
                            // word reported the table of that name as drifted.
                            preg_match_all(
                                '/(?:TABLE|INTO)\s+[`"]?([a-zA-Z0-9_]+)[`"]?/i',
                                $entry,
                                $found,
                            );
                            $named = array_values(array_intersect(array_unique($found[1]), $known));
                            // A statement naming another table is another
                            // table's finding, and a caller who named one asked
                            // about that one.
                            if ($wanted !== '' && !in_array($wanted, $named, true)) {
                                continue;
                            }
                            // The tables and not how many statements name them.
                            // One changed column is one ALTER on MySQL and a
                            // whole table rebuild on SQLite, so a count says
                            // which platform is under the answer.
                            $key = $name . "\0" . $type;
                            $at = array_values(array_unique(array_merge($suggestions[$key]['tables'] ?? [], $named)));
                            sort($at);
                            $suggestions[$key] = [
                                'connection' => (string) $name,
                                'change' => (string) $type,
                                'tables' => $at,
                            ];
                        }
                    }
                }
            }
            // A list rather than a map keyed by connection. An empty map is
            // `[]` in JSON and a schema that says object refuses it, and a
            // client reads one shape either way.
            ksort($suggestions);
            $topic['statementCount'] = count($statements);
            $topic['suggestions'] = array_values($suggestions);
            $answer['topics']['liveSchema'] = $topic;
        } catch (Throwable $failure) {
            $answer['topics']['liveSchema'] = [
                'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
            ];
        }
    }

    // The queries this probe runs over rows, and the only ones it may run. How
    // many there are, grouped by the page they sit on and by the state the
    // enable fields put them in. And the rows themselves where the caller asked
    // for them, `D-AUD-017`. What a row carries stands here rather than with
    // the caller. The identifiers, the label the table names in its own ctrl,
    // the timestamps and the two flags. A column list the caller composes would
    // make every column of a countable table readable, which is the boundary
    // rather than a convenience. Every restriction goes on purpose. A deleted
    // or hidden row is what the caller asks about, and the default restrictions
    // would report it as absent rather than as deleted.
    if ($records !== null) {
        try {
            $wanted = (string) ($records['table'] ?? '');
            $where = is_array($records['where'] ?? null) ? $records['where'] : [];
            $limit = (int) ($records['limit'] ?? 0);
            $control = is_array($tca[$wanted]['ctrl'] ?? null) ? $tca[$wanted]['ctrl'] : [];
            $deleted = is_string($control['delete'] ?? null) ? $control['delete'] : '';
            $hidden = is_string($control['enablecolumns']['disabled'] ?? null)
                ? $control['enablecolumns']['disabled']
                : '';
            $label = is_string($control['label'] ?? null) ? $control['label'] : '';
            $changed = is_string($control['tstamp'] ?? null) ? $control['tstamp'] : '';
            $created = is_string($control['crdate'] ?? null) ? $control['crdate'] : '';

            $pool = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                TYPO3\CMS\Core\Database\ConnectionPool::class,
            );

            // One place builds the filter, so the count and the rows are two
            // answers about one set. Every value binds rather than goes into
            // the SQL, and every column name is one the caller heard this table
            // has.
            $constrain = static function (
                TYPO3\CMS\Core\Database\Query\QueryBuilder $builder
            ) use ($where): TYPO3\CMS\Core\Database\Query\QueryBuilder {
                foreach ($where as $column => $value) {
                    $builder->andWhere($builder->expr()->eq(
                        (string) $column,
                        $builder->createNamedParameter($value),
                    ));
                }

                return $builder;
            };

            // A column the caller groups by: one call answers a distribution
            // that was thirteen counted calls, one per value — `D-ANS-141`.
            $groupBy = is_string($records['groupBy'] ?? null) ? $records['groupBy'] : '';
            $default = $groupBy === ''
                ? null
                : ($tca[$wanted]['columns'][$groupBy]['config']['default'] ?? null);

            $counting = $constrain($pool->getQueryBuilderForTable($wanted));
            $counting->getRestrictions()->removeAll();
            $grouped = ['pid'];
            foreach ([$deleted, $hidden, $groupBy] as $flag) {
                if ($flag !== '' && !in_array($flag, $grouped, true)) {
                    $grouped[] = $flag;
                }
            }
            // The alias stands here rather than comes from the platform. The
            // key a bare COUNT(*) comes back under is the platform's, and the
            // three this server covers do not agree on it.
            $counting->selectLiteral($counting->expr()->count('*', 'rowCount'))->from($wanted);
            foreach ($grouped as $column) {
                $counting->addSelect($column);
                $counting->addGroupBy($column);
            }
            $groups = [];
            foreach ($counting->executeQuery()->fetchAllAssociative() as $row) {
                $group = [
                    'pid' => (int) ($row['pid'] ?? 0),
                    'deleted' => $deleted !== '' && (int) ($row[$deleted] ?? 0) !== 0,
                    'hidden' => $hidden !== '' && (int) ($row[$hidden] ?? 0) !== 0,
                    'rows' => (int) ($row['rowCount'] ?? 0),
                ];
                if ($groupBy !== '') {
                    $group['value'] = $row[$groupBy] ?? null;
                }
                $groups[] = $group;
            }

            // The rows that depart from the column's TCA default, which is what
            // turns a distribution into a decision. A value one row in a
            // hundred carries is invisible in the counts. It is the row that
            // breaks when the branch it needs goes, `D-AUD-018`.
            $departing = [];
            if ($groupBy !== '' && $default !== null) {
                $departingFrom = $constrain($pool->getQueryBuilderForTable($wanted));
                $departingFrom->getRestrictions()->removeAll();
                $departingFrom
                    ->select('uid', 'pid', $groupBy)
                    ->from($wanted)
                    ->andWhere($departingFrom->expr()->neq(
                        $groupBy,
                        $departingFrom->createNamedParameter($default),
                    ))
                    ->orderBy('uid', 'ASC')
                    ->setMaxResults(max(1, (int) ($records['departing'] ?? 20)));
                foreach ($departingFrom->executeQuery()->fetchAllAssociative() as $row) {
                    $departing[] = [
                        'uid' => (int) ($row['uid'] ?? 0),
                        'pid' => (int) ($row['pid'] ?? 0),
                        'value' => $row[$groupBy] ?? null,
                    ];
                }
            }

            $rows = [];
            if ($limit !== 0) {
                $selecting = $constrain($pool->getQueryBuilderForTable($wanted));
                $selecting->getRestrictions()->removeAll();
                $columns = ['uid', 'pid'];
                // The columns the caller named come off the same read. Each one
                // had a check against what TYPO3 derives for the table before
                // the call. That is what lets it go into the SQL as an
                // identifier, `D-AUD-019`.
                $wantedColumns = is_array($records['columns'] ?? null) ? array_values($records['columns']) : [];
                foreach ([$label, $changed, $created, $deleted, $hidden, ...$wantedColumns] as $column) {
                    if (is_string($column) && $column !== '' && !in_array($column, $columns, true)) {
                        $columns[] = $column;
                    }
                }
                $selecting->select(...$columns)->from($wanted)->orderBy('uid', 'ASC');
                if ($limit > 0) {
                    $selecting->setMaxResults($limit);
                }
                foreach ($selecting->executeQuery()->fetchAllAssociative() as $row) {
                    $values = [];
                    foreach ($wantedColumns as $column) {
                        if (is_string($column) && $column !== '') {
                            $values[] = ['column' => $column, 'value' => $row[$column] ?? null];
                        }
                    }
                    $rows[] = [
                        'uid' => (int) ($row['uid'] ?? 0),
                        'pid' => (int) ($row['pid'] ?? 0),
                        'label' => $label === '' ? '' : (string) ($row[$label] ?? ''),
                        'changed' => $changed === '' ? 0 : (int) ($row[$changed] ?? 0),
                        'created' => $created === '' ? 0 : (int) ($row[$created] ?? 0),
                        'deleted' => $deleted !== '' && (int) ($row[$deleted] ?? 0) !== 0,
                        'hidden' => $hidden !== '' && (int) ($row[$hidden] ?? 0) !== 0,
                        'values' => $values,
                    ];
                }
            }

            $answer['topics']['records'] = [
                'table' => $wanted,
                // Said rather than inferred from what came back. A table with
                // no delete field marks nothing as deleted, and that is not the
                // same answer as no deletion at all. The label field is here
                // for the same reason. An empty label on every row is a table
                // whose ctrl names none.
                'deleteField' => $deleted,
                'hiddenField' => $hidden,
                'labelField' => $label,
                'groups' => $groups,
                // Null is a column whose TCA declares no default, which is a
                // different answer from a default of zero. Nothing departs from
                // the first and every non-zero row departs from the second.
                'groupDefault' => $default,
                'departing' => $departing,
                'rows' => $rows,
            ];
        } catch (Throwable $failure) {
            $answer['topics']['records'] = [
                'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
            ];
        }
    }

    // The container the installation runs is a compiled one, and a compiled one
    // has forgotten every private service, which is nearly all of them. What is
    // readable is the builder before that, and it assembles through the core's
    // own ContainerBuilder rather than a repeat of what it does. The passes,
    // the load order and the synthetic early services are its, and a copy of
    // them here would drift with no failure. `buildContainer` has protected
    // visibility, so this reaches it by reflection and reports the topic
    // unavailable where that stops to work, `D-DIS-023`.
    if ($services !== null) {
        try {
            $packageManager = $container->get(TYPO3\CMS\Core\Package\PackageManager::class);
            $early = [];
            foreach ($container->getServiceIds() as $id) {
                if (str_starts_with((string) $id, '_early.')) {
                    $early[substr((string) $id, 7)] = $container->get($id);
                }
            }
            $coreBuilder = new TYPO3\CMS\Core\DependencyInjection\ContainerBuilder($early);
            $build = new ReflectionMethod($coreBuilder, 'buildContainer');
            $build->setAccessible(true);
            $registry = new TYPO3\CMS\Core\DependencyInjection\ServiceProviderRegistry($packageManager);
            $builder = null;
            try {
                $builder = $build->invoke($coreBuilder, $packageManager, $registry);
            } catch (Symfony\Component\DependencyInjection\Exception\ExceptionInterface $broken) {
                // A container that will not assemble is the finding rather than
                // the absence of one. The message names the service and the
                // argument, which is what the caller came for. It answers and
                // does not throw. One try wraps this whole file, so a throw
                // here would end the read and take every other topic with it.
                $answer['topics']['services'] = [
                    'definitionCount' => 0,
                    'aliasCount' => 0,
                    'compilationFailure' => get_class($broken) . ': ' . $broken->getMessage(),
                    'services' => [],
                ];
            }

            if ($builder !== null) {
                // `buildContainer` compiles before it returns, so what comes
                // back is the builder with autowiring resolved and the unused
                // private definitions already removed. That is the set the live
                // container has, which is the one a caller asks about.
                $definitions = $builder->getDefinitions();

                $wanted = strtolower((string) ($services['query'] ?? ''));
                $tag = (string) ($services['tag'] ?? '');
                $found = [];
                foreach ($definitions as $id => $definition) {
                    $class = (string) ($definition->getClass() ?? '');
                    $tags = array_keys($definition->getTags());
                    if ($tag !== '' && !in_array($tag, $tags, true)) {
                        continue;
                    }
                    if ($wanted !== ''
                        && !str_contains(strtolower((string) $id), $wanted)
                        && !str_contains(strtolower($class), $wanted)
                    ) {
                        continue;
                    }
                    $arguments = [];
                    foreach ($definition->getArguments() as $position => $argument) {
                        $arguments[] = [
                            'position' => is_int($position) ? $position : -1,
                            'resolves' => $argument instanceof Symfony\Component\DependencyInjection\Reference
                                ? (string) $argument
                                : (is_scalar($argument) ? 'value: ' . var_export($argument, true) : 'value'),
                        ];
                    }
                    $found[] = [
                        'id' => (string) $id,
                        'class' => $class,
                        'aliasFor' => '',
                        'public' => $definition->isPublic(),
                        'shared' => $definition->isShared(),
                        'autowired' => $definition->isAutowired(),
                        'abstract' => $definition->isAbstract(),
                        'synthetic' => $definition->isSynthetic(),
                        'tags' => $tags,
                        'arguments' => $arguments,
                    ];
                }
                // An interface usually reaches its implementation through an
                // alias. A lookup that reads definitions alone answers
                // "nothing" to the commonest question there is, `D-DIS-023`.
                $aliases = $builder->getAliases();
                foreach ($aliases as $id => $alias) {
                    $target = (string) $alias;
                    $seen = [];
                    while (isset($aliases[$target]) && !isset($seen[$target])) {
                        $seen[$target] = true;
                        $target = (string) $aliases[$target];
                    }
                    $class = isset($definitions[$target]) ? (string) ($definitions[$target]->getClass() ?? '') : '';
                    if ($tag !== '') {
                        continue;
                    }
                    if ($wanted !== ''
                        && !str_contains(strtolower((string) $id), $wanted)
                        && !str_contains(strtolower($class), $wanted)
                    ) {
                        continue;
                    }
                    $found[] = [
                        'id' => (string) $id,
                        'class' => $class,
                        'aliasFor' => $target,
                        'public' => $alias->isPublic(),
                        'shared' => false,
                        'autowired' => false,
                        'abstract' => false,
                        'synthetic' => false,
                        'tags' => [],
                        'arguments' => [],
                    ];
                }

                usort($found, static fn(array $a, array $b): int => strcmp($a['id'], $b['id']));
                $answer['topics']['services'] = [
                    'definitionCount' => count($definitions),
                    'aliasCount' => count($aliases),
                    'compilationFailure' => '',
                    'services' => $found,
                ];
            }
        } catch (Throwable $failure) {
            $answer['topics']['services'] = [
                'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
            ];
        }
    }

    // A form data group is a dependency graph and not a list. Every provider
    // declares `depends` and `before`, and what orders the run is what the core
    // resolves from those. The raw registry hands a reader the inputs and calls
    // it the answer. tcaDatabaseRecord has 61 providers, and the pair any one
    // question is about sits far apart in it with no edge between them. Ordered
    // by the core's own service, with the two keys
    // `Form\FormDataGroup\OrderedProviderList` passes it. A second
    // implementation on the other side would answer confidently and, the day
    // the resolution changes, differently. In a try of its own, for the reason
    // the enrichment above has one.
    try {
        $groups = [];
        $registry = $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'] ?? [];
        $ordering = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TYPO3\CMS\Core\Service\DependencyOrderingService::class,
        );
        foreach (is_array($registry) ? $registry : [] as $group => $providers) {
            if (!is_array($providers)) {
                continue;
            }
            $ordered = [];
            foreach ($ordering->orderByDependencies($providers, 'before', 'depends') as $provider => $declared) {
                $declared = is_array($declared) ? $declared : [];
                $ordered[] = [
                    'provider' => (string) $provider,
                    'depends' => array_values(array_map('strval', (array) ($declared['depends'] ?? []))),
                    'before' => array_values(array_map('strval', (array) ($declared['before'] ?? []))),
                ];
            }
            $groups[(string) $group] = $ordered;
        }
        $answer['topics']['formDataGroups'] = ['groups' => $groups];
    } catch (Throwable $failure) {
        $answer['topics']['formDataGroups'] = [
            'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
        ];
    }
    // The module tree as the registry resolved it. Two of the values exist
    // nowhere else. `navigationComponent` comes down from the parent module, so
    // a Modules.php says nothing about whether a module has page-tree
    // navigation. The routes beyond the module's own path assemble per module
    // rather than come from a declaration. A read of the files instead is not a
    // weaker answer here, it is a wrong one. EXT:backend's own Modules.php
    // references enum constants and no include outside a booted core works. The
    // package and the labels are not on the registry's API, so the raw
    // configuration comes in beside it. That is the same low-level access the
    // core's own debug:backend:modules takes, for the reason it states there.
    // In a try of its own, for the reason the enrichment above has one.
    try {
        $registry = $container->get(TYPO3\CMS\Backend\Module\ModuleRegistry::class);
        $declared = $container->get('backend.modules')->getArrayCopy();
        $language = $GLOBALS['LANG'] = $container->get(
            TYPO3\CMS\Core\Localization\LanguageServiceFactory::class,
        )->create('en');

        $modules = [];
        foreach ($registry->getModules() as $module) {
            $identifier = $module->getIdentifier();
            $configuration = is_array($declared[$identifier] ?? null) ? $declared[$identifier] : [];

            $parents = [];
            for ($above = $module->getParentModule(); $above !== null; $above = $above->getParentModule()) {
                array_unshift($parents, $above->getIdentifier());
            }

            // What ModuleRegistry::registerRoutesForModules() registers for
            // this module, worked out its way. A first-level module that is not
            // standalone gets none, `_default` goes under the module identifier
            // and every other route under `<module>.<name>` below the module's
            // path. A module with no routable default throws rather than
            // answers, and that is a module with no routes.
            $routes = [];
            if ($module->hasParentModule() || $module->isStandalone()) {
                try {
                    $declaredRoutes = $module->getDefaultRouteOptions();
                } catch (Throwable $unroutable) {
                    $declaredRoutes = [];
                }
                foreach ($declaredRoutes as $name => $options) {
                    $name = (string) $name;
                    $below = (string) (($options['path'] ?? false) ?: ('/' . $name));
                    $routes[] = [
                        'name' => $name,
                        'identifier' => $name === '_default' ? $identifier : $identifier . '.' . $name,
                        'path' => $name === '_default' ? $module->getPath() : $module->getPath() . $below,
                        // The options carry the module object itself, so the
                        // fields stand by name rather than go over whole.
                        'target' => is_string($options['target'] ?? null) ? $options['target'] : '',
                    ];
                }
            }

            $labels = $configuration['labels'] ?? '';
            if (is_array($labels)) {
                $labels = (string) ($labels['title'] ?? '');
            }
            $position = $module->getPosition();

            $modules[] = [
                'identifier' => $identifier,
                'parents' => $parents,
                'extension' => (string) ($configuration['packageName'] ?? ''),
                'labels' => trim($language->sL($module->getTitle()) . ' [' . $labels . ']'),
                'path' => $module->getPath(),
                'position' => $position === [] ? '' : (string) json_encode($position),
                'navigationComponent' => $module->getNavigationComponent(),
                'access' => $module->getAccess(),
                'routes' => $routes,
            ];
        }
        $answer['topics']['modules'] = ['modules' => $modules];
    } catch (Throwable $failure) {
        $answer['topics']['modules'] = [
            'unavailable' => get_class($failure) . ': ' . $failure->getMessage(),
        ];
    }
} catch (Throwable $failure) {
    if ($answer['reason'] === '') {
        $answer['state'] = 'unreachable';
        $answer['reason'] = get_class($failure) . ': ' . $failure->getMessage();
    }
}

while (ob_get_level() > 0) {
    ob_end_clean();
}
fwrite(STDOUT, (string) json_encode($answer));
