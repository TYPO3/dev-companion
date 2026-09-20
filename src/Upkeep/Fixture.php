<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;

/**
 * A TYPO3 installation this repository writes, whose console answers.
 *
 * Nine tools answer `installation` where TYPO3 booted and said so and
 * `packages` where the files had to stand in. A core checkout can only ever
 * produce the second, so the first shape used to come from whichever real site
 * the machine had, `D-DOC-006`. Nothing is fake on this side of the process
 * boundary: `Typo3Cli` resolves this project, starts it and reads what it
 * printed.
 *
 * Written rather than committed, like `.checkouts/`, and shaped by `ToolCalls`
 * rather than after a real site. Everything in it says whose it is, because a
 * recorded answer must not carry an entry a reader could take for TYPO3's own.
 */
final class Fixture
{
    /** The extension the fixture's own registrations belong to. */
    public const EXTENSION = 'acme_events';

    /** Where it stands: below the checkout, ignored by git, rewritten whole. */
    public static function directory(): string
    {
        return Paths::root() . '/.fixtures';
    }

    public static function root(): string
    {
        return self::directory() . '/installation';
    }

    /**
     * The version it states it is.
     *
     * Read off `knowledge/versions.json` rather than written down, for the
     * reason `Environments::branch()` reads it. An installation recorded
     * against a version this server no longer answers for measures the wrong
     * thing.
     */
    public static function typo3Version(): string
    {
        return Environments::branch() . '.0';
    }

    /** Writes it whole and hands back the root it went to. */
    public static function write(): string
    {
        $root = self::root();
        self::clear($root);
        foreach (self::files() as $path => $contents) {
            self::put($root . '/' . $path, $contents);
        }
        self::bootsInto(
            $root,
            self::ICONS,
            self::TABLES,
            self::CONTENT_ELEMENTS,
            modules: self::MODULES,
            labels: self::LABELS,
            tca: self::FLEX_TCA,
            flexForm: self::FLEX_FORM,
            // The version this installation states it is, which is the stable
            // line — so its FlexFormTools has the signature that line's has.
            flexFormTakesTheSchema: true,
        );

        return $root;
    }

    /**
     * Writes an autoloader that boots into a container that answers every
     * topic.
     *
     * The probe is the real one and this is what it lands in. So everything it
     * reads has to be here in the shape it reads it in. A registry whose icons
     * carry `EXT:<key>/` sources and a TCA whose titles carry `LLL:EXT:<key>/`,
     * because that reference is the only attribution either of them has.
     *
     * The Doctrine and schema classes are the fourth topic. `DefaultTcaSchema`
     * is what says which columns TYPO3 derives for a table and which table
     * exists only because a relation asked for it. A container without it
     * answers that topic `unavailable`, which is a state, and not the one this
     * installation exists to show.
     *
     * @param array<string, string> $icons identifier => source, as the registry resolves it
     * @param array<string, string> $tables table => ctrl title
     * @param array<string, array{0: string, 1: string}> $contentElements CType => [label, icon]
     * @param array<string, array<string, array<string, array<int, string>>>> $formDataGroups group => provider => depends/before
     * @param array<string, array<string, mixed>> $modules identifier => registration, in the shape the registry hands one back
     * @param array<string, string> $labels reference => what the installation's language service resolves it to
     * @param array<string, mixed> $configuration merged into TYPO3_CONF_VARS, which is what the configuration topic is read out of
     * @param array<string, mixed> $tca merged over the TCA above, for the declarations a topic reads off a table
     * @param array{pointer?: string, structures?: array<string, mixed>, schemaTables?: array<int, string>} $flexForm what its FlexFormTools answers: the column of the record the key is taken from, one parsed structure per key, and which tables its schema factory has where that is not every table with TCA
     * @param bool $flexFormTakesTheSchema whether its FlexFormTools has the TYPO3 v14 signature, which wants a TcaSchema and throws without one
     */
    public static function bootsInto(
        string $root,
        array $icons = [],
        array $tables = [],
        array $contentElements = [],
        bool $failsafe = false,
        array $formDataGroups = [],
        array $modules = [],
        array $labels = [],
        array $configuration = [],
        array $tca = [],
        array $flexForm = [],
        bool $flexFormTakesTheSchema = false,
    ): void {
        $items = [];
        foreach ($contentElements as $value => [$label, $icon]) {
            $items[] = ['label' => $label, 'value' => $value, 'icon' => $icon];
        }

        $state = var_export([
            'icons' => $icons,
            'tables' => $tables,
            'items' => $items,
            'failsafe' => $failsafe,
            'formDataGroups' => $formDataGroups,
            'modules' => $modules,
            'labels' => $labels,
            'configuration' => $configuration,
            'tca' => $tca,
            'flexForm' => $flexForm,
        ], true);

        // TYPO3 v14 wants a TcaSchema handed to both calls and throws where it
        // is null; v12 and v13 have no such parameter and read the global TCA.
        // The probe reads which of the two it is off the signature, so the
        // fixture has to have one signature or the other rather than a flag.
        $schemaArgument = $flexFormTakesTheSchema ? ', $schema = null' : '';
        $withoutSchema = $flexFormTakesTheSchema
            ? 'if ($schema === null) { throw new \\RuntimeException('
                . '\'Can not resolve default data structure without TCA.\', 1753182123); }'
            : '';
        // Declared only beside that signature, so a probe that asks for a
        // schema where nobody wants one reaches a class this installation does
        // not have.
        $schemaFactory = $flexFormTakesTheSchema ? <<<PHP
            namespace TYPO3\\CMS\\Core\\Schema {
                class TcaSchema {
                    public function __construct(public string \$name) {}
                }
                class TcaSchemaFactory {
                    public function has(string \$name): bool {
                        \$declared = \$GLOBALS['FAKE']['flexForm']['schemaTables'] ?? null;
                        return \$declared === null ? isset(\$GLOBALS['TCA'][\$name]) : in_array(\$name, \$declared, true);
                    }
                    public function get(string \$name): TcaSchema { return new TcaSchema(\$name); }
                }
            }
            PHP : '';

        $major = (int) explode('.', self::typo3Version())[0];
        self::put($root . '/vendor/autoload.php', <<<PHP
            <?php
            namespace {
                \$GLOBALS['FAKE'] = {$state};
                \$GLOBALS['TCA'] = ['tt_content' => ['columns' => ['CType' => ['config' => [
                    'items' => \$GLOBALS['FAKE']['items'],
                ]]]]];
                foreach (\$GLOBALS['FAKE']['tables'] as \$table => \$title) {
                    \$GLOBALS['TCA'][\$table] = ['ctrl' => ['title' => \$title]];
                }
                \$GLOBALS['TCA'] = array_replace_recursive(\$GLOBALS['TCA'], \$GLOBALS['FAKE']['tca']);
                \$GLOBALS['TYPO3_CONF_VARS'] = \$GLOBALS['FAKE']['configuration'];
                \$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']
                    = \$GLOBALS['FAKE']['formDataGroups'];
            }
            namespace TYPO3\\CMS\\Core\\Information {
                // What the probe asks before it hands the schema class its
                // tables, since 12.4 reads them in another shape.
                class Typo3Version {
                    public function getMajorVersion(): int { return {$major}; }
                }
            }
            namespace TYPO3\\CMS\\Core\\Utility {
                // Traversal and nothing else. The core's own is what runs
                // against a real installation; a stand-in is honest here
                // because there is no TYPO3 behaviour in walking a path, which
                // is the difference from the ordering service above.
                class ArrayUtility {
                    public static function isValidPath(array \$array, string \$path, string \$delimiter = '/'): bool {
                        foreach (explode(\$delimiter, trim(\$path, \$delimiter)) as \$segment) {
                            if (!is_array(\$array) || !array_key_exists(\$segment, \$array)) {
                                return false;
                            }
                            \$array = \$array[\$segment];
                        }
                        return true;
                    }
                    public static function getValueByPath(array \$array, string \$path, string \$delimiter = '/') {
                        \$value = \$array;
                        foreach (explode(\$delimiter, trim(\$path, \$delimiter)) as \$segment) {
                            \$value = \$value[\$segment];
                        }
                        return \$value;
                    }
                }
            }
            namespace TYPO3\\CMS\\Core\\Service {
                // Reverses rather than resolves. What the probe has to be held
                // to is that it hands the registry to the core's service and
                // reports what comes back — an ordering of its own would be the
                // second implementation the probe exists to avoid, and a
                // passthrough here could not tell the two apart.
                class DependencyOrderingService {
                    public function orderByDependencies(array \$items, string \$before = 'before', string \$after = 'after'): array {
                        return array_reverse(\$items, true);
                    }
                }
            }
            namespace TYPO3\\CMS\\Core\\Core {
                class SystemEnvironmentBuilder {
                    public const REQUESTTYPE_CLI = 2;
                    public static function run(int \$level = 0, int \$type = 0): void {}
                }
                class Bootstrap {
                    public static function init(\$classLoader) {
                        return \$GLOBALS['FAKE']['failsafe']
                            ? new \\TYPO3\\CMS\\Core\\DependencyInjection\\FailsafeContainer()
                            : new \\Fake\\Container();
                    }
                }
            }
            namespace TYPO3\\CMS\\Core\\DependencyInjection {
                class FailsafeContainer {}
            }
            namespace TYPO3\\CMS\\Core\\Imaging {
                class IconRegistry {
                    public function getAllRegisteredIconIdentifiers(): array {
                        return array_keys(\$GLOBALS['FAKE']['icons']);
                    }
                    public function getIconConfigurationByIdentifier(string \$identifier): array {
                        return ['options' => ['source' => \$GLOBALS['FAKE']['icons'][\$identifier] ?? '']];
                    }
                    public function getDeprecatedIcons(): array { return []; }
                }
            }
            namespace TYPO3\\CMS\\Core\\Utility {
                class GeneralUtility {
                    public static function makeInstance(string \$class) { return new \$class(); }
                }
            }
            namespace Doctrine\\DBAL\\Types {
                class Type {
                    public function __construct(public string \$name = 'string') {}
                    public static function lookupName(Type \$type): string { return \$type->name; }
                }
            }
            namespace Doctrine\\DBAL\\Schema {
                class Column {
                    public function __construct(
                        private string \$name,
                        private string \$type,
                        private bool \$notnull,
                        private \$default,
                        private ?int \$length,
                    ) {}
                    public function getName(): string { return \$this->name; }
                    public function getType(): \\Doctrine\\DBAL\\Types\\Type {
                        return new \\Doctrine\\DBAL\\Types\\Type(\$this->type);
                    }
                    public function getNotnull(): bool { return \$this->notnull; }
                    public function getDefault() { return \$this->default; }
                    public function getLength(): ?int { return \$this->length; }
                }
                class Table {
                    private array \$columns = [];
                    public function __construct(private string \$name) {}
                    public function getName(): string { return \$this->name; }
                    public function addColumn(string \$name, string \$type, bool \$notnull, \$default, ?int \$length): void {
                        \$this->columns[] = new Column(\$name, \$type, \$notnull, \$default, \$length);
                    }
                    public function getColumns(): array { return \$this->columns; }
                }
            }
            namespace TYPO3\\CMS\\Core\\Database\\Schema {
                class DefaultTcaSchema {
                    // What core derives for every TCA table, and the relation
                    // table a relation asks for: the two shapes the topic exists
                    // to tell apart.
                    public function enrich(array \$tables): array {
                        foreach (\$tables as \$table) {
                            \$table->addColumn('uid', 'integer', true, null, null);
                            \$table->addColumn('pid', 'integer', true, 0, null);
                            \$table->addColumn('tstamp', 'integer', true, 0, null);
                            \$table->addColumn('deleted', 'smallint', true, 0, null);
                        }
                        \$relation = new \\Doctrine\\DBAL\\Schema\\Table('tx_acme_events_event_category_mm');
                        \$relation->addColumn('uid_local', 'integer', true, 0, null);
                        \$relation->addColumn('uid_foreign', 'integer', true, 0, null);
                        \$relation->addColumn('sorting', 'integer', true, 0, null);
                        \$tables['tx_acme_events_event_category_mm'] = \$relation;
                        return \$tables;
                    }
                }
            }
            namespace TYPO3\\CMS\\Core\\Localization {
                class LanguageService {
                    public function sL(string \$reference): string {
                        return \$GLOBALS['FAKE']['labels'][\$reference] ?? \$reference;
                    }
                }
                class LanguageServiceFactory {
                    public function create(string \$locale): LanguageService { return new LanguageService(); }
                }
            }
            namespace TYPO3\\CMS\\Backend\\Module {
                class Module {
                    public ?Module \$parentModule = null;
                    public function __construct(private string \$identifier, private array \$configuration) {}
                    public function getIdentifier(): string { return \$this->identifier; }
                    public function getParentModule(): ?Module { return \$this->parentModule; }
                    public function hasParentModule(): bool { return \$this->parentModule !== null; }
                    public function isStandalone(): bool { return (bool)(\$this->configuration['standalone'] ?? false); }
                    public function getPath(): string { return (string)(\$this->configuration['path'] ?? ''); }
                    public function getTitle(): string { return (string)(\$this->configuration['title'] ?? ''); }
                    public function getAccess(): string { return (string)(\$this->configuration['access'] ?? ''); }
                    public function getPosition(): array { return (array)(\$this->configuration['position'] ?? []); }
                    // Inherited from the parent, the way the core resolves it.
                    // What the probe is held to is reporting that value: a
                    // module declaring none is page-tree navigated here, and an
                    // inheritance written into the probe could not be told from
                    // one that reads the registry.
                    public function getNavigationComponent(): string {
                        \$own = (string)(\$this->configuration['navigationComponent'] ?? '');
                        if ((\$this->configuration['inherit'] ?? true) && \$this->parentModule !== null) {
                            return \$this->parentModule->getNavigationComponent() ?: \$own;
                        }
                        return \$own;
                    }
                    public function getDefaultRouteOptions(): array {
                        \$routes = (array)(\$this->configuration['routes'] ?? []);
                        if (\$routes === []) {
                            throw new \\RuntimeException('No default route for ' . \$this->identifier, 1674063354);
                        }
                        return \$routes;
                    }
                }
                class ModuleRegistry {
                    public function getModules(): array {
                        \$modules = [];
                        foreach (\$GLOBALS['FAKE']['modules'] as \$identifier => \$configuration) {
                            \$modules[\$identifier] = new Module(\$identifier, \$configuration);
                        }
                        foreach (\$GLOBALS['FAKE']['modules'] as \$identifier => \$configuration) {
                            \$parent = \$configuration['parent'] ?? '';
                            if (\$parent !== '' && isset(\$modules[\$parent])) {
                                \$modules[\$identifier]->parentModule = \$modules[\$parent];
                            }
                        }
                        return \$modules;
                    }
                }
            }
            {$schemaFactory}
            namespace TYPO3\\CMS\\Core\\Configuration\\FlexForm {
                // A spy with a canned answer, not a resolution. What the probe
                // has to be held to is that it hands this installation the
                // record it was given and reports what came back — TYPO3's own
                // resolution is hundreds of lines across four events, and a
                // second one written here is the thing the probe exists to
                // avoid. What it does do is take the key out of the row, which
                // is what shows the caller's values reaching the resolution.
                class FlexFormTools {
                    public function getDataStructureIdentifier(array \$fieldTca, string \$table, string \$field, array \$row{$schemaArgument}): string {
                        {$withoutSchema}
                        \$flex = \$GLOBALS['FAKE']['flexForm'];
                        \$pointer = (string)(\$flex['pointer'] ?? '');
                        \$key = \$pointer === '' ? 'default' : (string)(\$row[\$pointer] ?? 'default');
                        if (!isset(\$flex['structures'][\$key])) {
                            throw new \\RuntimeException(
                                'TCA misconfiguration in table "' . \$table . '" field "' . \$field . '" config section:'
                                . ' The field is either not configured as type="flex" or no valid data structure is defined.',
                                1732198004
                            );
                        }
                        return json_encode([
                            'type' => 'tca',
                            'tableName' => \$table,
                            'fieldName' => \$field,
                            'dataStructureKey' => \$key,
                        ]);
                    }
                    public function parseDataStructureByIdentifier(string \$identifier{$schemaArgument}): array {
                        \$key = (string)(json_decode(\$identifier, true)['dataStructureKey'] ?? '');
                        return \$GLOBALS['FAKE']['flexForm']['structures'][\$key] ?? [];
                    }
                }
            }
            namespace Fake {
                class Container {
                    public function get(string \$id) {
                        // The raw configuration every module was built from,
                        // which is where the package and the labels live.
                        if (\$id === 'backend.modules') {
                            return new \\ArrayObject(\$GLOBALS['FAKE']['modules']);
                        }
                        return new \$id();
                    }
                }
            }
            namespace {
                return new \\Fake\\Container();
            }
            PHP);
    }

    /**
     * Every file of it, by where it goes.
     *
     * One list rather than a method per package. What a reader of this class
     * needs first is what the installation is, and that is the list.
     *
     * @return array<string, string>
     */
    private static function files(): array
    {
        $branch = Environments::branch();

        return [
            'composer.json' => self::json([
                'name' => 'typo3-mcp/fixture-installation',
                'description' => 'Written by TYPO3\\DevCompanion\\Upkeep\\Fixture so this server has an installation to '
                    . 'answer from. It is nobody\'s site.',
                'type' => 'project',
                'require' => [
                    'php' => '^8.2',
                    'typo3/cms-core' => '^' . $branch,
                    'typo3/cms-backend' => '^' . $branch,
                ],
                'scripts' => [
                    'cgl' => 'php-cs-fixer fix',
                    'cgl:ci' => 'php-cs-fixer fix --dry-run --diff',
                    'test' => 'phpunit -c Build/phpunit.xml',
                ],
                'extra' => ['typo3/cms' => ['web-dir' => 'public']],
            ]),
            'config/sites/fixture/config.yaml' => self::site(),
            'vendor/bin/typo3' => self::console(),
            'vendor/composer/installed.json' => self::json(['packages' => [
                [
                    'name' => 'typo3/cms-core',
                    'type' => 'typo3-cms-framework',
                    'install-path' => '../typo3/cms-core',
                    'extra' => ['typo3/cms' => ['extension-key' => 'core']],
                ],
                [
                    'name' => 'typo3/cms-backend',
                    'type' => 'typo3-cms-framework',
                    'install-path' => '../typo3/cms-backend',
                    'extra' => ['typo3/cms' => ['extension-key' => 'backend']],
                ],
                [
                    'name' => 'acme/acme-events',
                    'type' => 'typo3-cms-extension',
                    'install-path' => '../../packages/' . self::EXTENSION,
                    'extra' => ['typo3/cms' => ['extension-key' => self::EXTENSION]],
                ],
            ]]),
            'vendor/typo3/cms-core/composer.json' => self::manifest(
                'typo3/cms-core',
                'core',
                'typo3-cms-framework',
                ['php' => '^8.2'],
            ),
            'vendor/typo3/cms-core/Classes/Information/Typo3Version.php' => sprintf(
                "<?php\n\nnamespace TYPO3\\CMS\\Core\\Information;\n\n"
                . "class Typo3Version\n{\n    protected const VERSION = '%s';\n}\n",
                self::typo3Version(),
            ),
            'vendor/typo3/cms-backend/composer.json' => self::manifest(
                'typo3/cms-backend',
                'backend',
                'typo3-cms-framework',
            ),
            'vendor/typo3/cms-backend/Configuration/Backend/Modules.php' => "<?php\n\nreturn [\n"
                . "    'web_list' => ['parent' => 'web', 'path' => '/module/web/list'],\n"
                . "    'acme_events' => ['parent' => 'web', 'path' => '/module/web/acme-events'],\n];\n",
            'vendor/typo3/cms-backend/Configuration/Backend/Routes.php' => "<?php\n\nreturn [\n"
                . "    'login' => ['path' => '/login'],\n    'main' => ['path' => '/main'],\n];\n",
            'vendor/typo3/cms-backend/Configuration/Icons.php' => "<?php\n\nreturn [\n"
                . "    'actions-open' => ['source' => 'EXT:backend/Resources/Public/Icons/actions-open.svg'],\n"
                . "    'actions-close' => ['source' => 'EXT:backend/Resources/Public/Icons/actions-close.svg'],\n];\n",
            'vendor/typo3/cms-backend/Configuration/Services.yaml' => "services:\n"
                . "  _defaults:\n    autowire: true\n    autoconfigure: true\n"
                . "  TYPO3\\CMS\\Backend\\Controller\\FixtureModuleController:\n"
                . "    tags:\n      - name: backend.controller\n",
            'vendor/typo3/cms-backend/Classes/Controller/FixtureModuleController.php' => "<?php\n\n"
                . "namespace TYPO3\\CMS\\Backend\\Controller;\n\nclass FixtureModuleController\n{\n}\n",
            'vendor/typo3/cms-backend/Resources/Private/Language/locallang.xlf' => self::labelFile([
                'labels.save' => 'Save',
                'labels.saveAndClose' => 'Save and close',
                'labels.close' => 'Close',
            ]),
            'vendor/typo3/cms-core/Resources/Private/Language/locallang_core.xlf' => self::labelFile([
                'labels.savedok' => 'Save document',
                'labels.cancel' => 'Cancel',
            ]),
            'packages/' . self::EXTENSION . '/composer.json' => self::manifest(
                'acme/acme-events',
                self::EXTENSION,
                'typo3-cms-extension',
            ),
            'packages/' . self::EXTENSION . '/ext_tables.php' => "<?php\n\n"
                . "// The file Deprecation-900001 is about. It is here so the entry has a subject.\n"
                . "defined('TYPO3') or die();\n",
            'packages/' . self::EXTENSION . '/Configuration/TCA/tx_acme_events_event.php' => "<?php\n\nreturn [\n"
                . "    'ctrl' => ['title' => 'LLL:EXT:" . self::EXTENSION
                . "/Resources/Private/Language/locallang_db.xlf:tx_acme_events_event'],\n];\n",
            'packages/' . self::EXTENSION . '/Configuration/Icons.php' => "<?php\n\nreturn [\n"
                . "    'acme-events-teaser' => ['source' => 'EXT:" . self::EXTENSION
                . "/Resources/Public/Icons/teaser.svg'],\n];\n",
            'packages/' . self::EXTENSION . '/Resources/Private/Language/locallang_db.xlf' => self::labelFile([
                'tx_acme_events_event' => 'Event',
                'tx_acme_events_event.title' => 'Title',
            ]),
            'packages/' . self::EXTENSION . '/Classes/Domain/Model/Event.php' => "<?php\n\n"
                . "namespace Acme\\AcmeEvents\\Domain\\Model;\n\nclass Event\n{\n}\n",
            ...self::changelog($branch),
        ];
    }

    /**
     * The icons the registry answers with.
     *
     * `actions-open` is the one `ToolCalls` asks for by name. The rest are what
     * a list looks like around it, and the `EXT:` source each carries says
     * whose it is.
     *
     * @var array<string, string>
     */
    private const ICONS = [
        'actions-open' => 'EXT:backend/Resources/Public/Icons/actions-open.svg',
        'actions-close' => 'EXT:backend/Resources/Public/Icons/actions-close.svg',
        'actions-document-open' => 'EXT:backend/Resources/Public/Icons/actions-document-open.svg',
        'acme-events-teaser' => 'EXT:acme_events/Resources/Public/Icons/teaser.svg',
        'content-text' => 'EXT:core/Resources/Public/Icons/content-text.svg',
        'mimetypes-x-content-text' => 'EXT:core/Resources/Public/Icons/mimetypes-x-content-text.svg',
    ];

    /** @var array<string, string> */
    private const TABLES = [
        'pages' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages',
        'tt_content' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:tt_content',
        'tx_acme_events_event' => 'LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:tx_acme_events_event',
    ];

    /** @var array<string, array{0: string, 1: string}> */
    private const CONTENT_ELEMENTS = [
        'text' => ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:CType.text', 'content-text'],
        'acme_events_teaser' => [
            'LLL:EXT:acme_events/Resources/Private/Language/locallang_db.xlf:CType.teaser',
            'acme-events-teaser',
        ],
    ];

    /**
     * The flex column the fixture's content element carries.
     *
     * Written in the shape the stable line uses, because that is the version
     * this installation states it is. One `ds` string on the column and a
     * record type that overrides it through `columnsOverrides`.
     * `Breaking-107047` removed the keyed `ds` array and `ds_pointerField` the
     * two LTS lines still resolve by. `bodytext` is here so a call about a
     * column that is not flex has one to name.
     *
     * @var array<string, mixed>
     */
    private const FLEX_TCA = [
        'tt_content' => [
            'ctrl' => ['type' => 'CType'],
            'columns' => [
                'bodytext' => [
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext',
                    'config' => ['type' => 'text'],
                ],
                'pi_flexform' => [
                    'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:pi_flexform',
                    'config' => [
                        'type' => 'flex',
                        'ds' => 'FILE:EXT:' . self::EXTENSION . '/Configuration/FlexForms/Default.xml',
                    ],
                ],
            ],
            'types' => [
                'acme_events_teaser' => [
                    'showitem' => 'CType, header, pi_flexform',
                    'columnsOverrides' => [
                        'pi_flexform' => ['config' => [
                            'ds' => 'FILE:EXT:' . self::EXTENSION . '/Configuration/FlexForms/Teaser.xml',
                        ]],
                    ],
                ],
            ],
        ],
    ];

    /**
     * What its FlexFormTools answers: the column that carries the key in the
     * record, and one parsed structure per key.
     *
     * Parsed rather than declared. This stands where the installation does, and
     * what an installation hands back is what its own migration, preparation
     * and listeners have already been through.
     *
     * @var array{pointer: string, structures: array<string, mixed>}
     */
    private const FLEX_FORM = [
        'pointer' => 'CType',
        'structures' => [
            'acme_events_teaser' => ['sheets' => ['sDEF' => ['ROOT' => [
                'sheetTitle' => 'LLL:EXT:' . self::EXTENSION
                    . '/Resources/Private/Language/locallang_db.xlf:flexform.teaser',
                'type' => 'array',
                'el' => [
                    'settings.headline' => [
                        'label' => 'LLL:EXT:' . self::EXTENSION
                            . '/Resources/Private/Language/locallang_db.xlf:flexform.headline',
                        'config' => ['type' => 'input', 'required' => true],
                    ],
                    'settings.layout' => [
                        'label' => 'LLL:EXT:' . self::EXTENSION
                            . '/Resources/Private/Language/locallang_db.xlf:flexform.layout',
                        'config' => [
                            'type' => 'select',
                            'renderType' => 'selectSingle',
                            'default' => 'wide',
                            'items' => [
                                ['label' => 'Wide', 'value' => 'wide'],
                                ['label' => 'Narrow', 'value' => 'narrow'],
                            ],
                        ],
                    ],
                    'settings.slides' => [
                        'type' => 'array',
                        'section' => '1',
                        'title' => 'LLL:EXT:' . self::EXTENSION
                            . '/Resources/Private/Language/locallang_db.xlf:flexform.slides',
                        'el' => [
                            'slide' => [
                                'type' => 'array',
                                'title' => 'LLL:EXT:' . self::EXTENSION
                                    . '/Resources/Private/Language/locallang_db.xlf:flexform.slide',
                                'el' => [
                                    'settings.slide.title' => [
                                        'label' => 'LLL:EXT:' . self::EXTENSION
                                            . '/Resources/Private/Language/locallang_db.xlf:flexform.slide.title',
                                        'config' => ['type' => 'input'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]]]],
        ],
    ];

    /**
     * The module tree the registry answers with.
     *
     * `web` is what a first-level module looks like: it declares the navigation
     * component and registers no route of its own. `web_list` declares neither
     * and inherits the page tree, which is the value no registration file
     * carries and the one this fixture exists to show. `acme_events` is the
     * project extension's, with a sub-route beside its default.
     *
     * @var array<string, array<string, mixed>>
     */
    private const MODULES = [
        'web' => [
            'packageName' => 'backend',
            'labels' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf',
            'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.web',
            'path' => '/module/web',
            'navigationComponent' => '@typo3/backend/tree/page-tree-element',
        ],
        'web_list' => [
            'parent' => 'web',
            'packageName' => 'backend',
            'labels' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf',
            'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.web_list',
            'path' => '/module/web/list',
            'position' => ['after' => 'web_layout'],
            'access' => 'user',
            'routes' => ['_default' => ['target' => 'TYPO3\\CMS\\Backend\\Controller\\RecordListController::mainAction']],
        ],
        'acme_events' => [
            'parent' => 'web',
            'packageName' => 'acme_events',
            'labels' => 'LLL:EXT:acme_events/Resources/Private/Language/locallang.xlf',
            'title' => 'LLL:EXT:acme_events/Resources/Private/Language/locallang.xlf:module.events',
            'path' => '/module/web/acme-events',
            'position' => ['after' => 'web_list'],
            'access' => 'user',
            'routes' => [
                '_default' => ['target' => 'TYPO3\\CMS\\Backend\\Controller\\FixtureModuleController::listAction'],
                'detail' => ['target' => 'TYPO3\\CMS\\Backend\\Controller\\FixtureModuleController::detailAction'],
            ],
        ],
        'site' => [
            'packageName' => 'backend',
            'labels' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf',
            'title' => 'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.site',
            'path' => '/module/site',
        ],
    ];

    /**
     * What the installation's language service resolves a reference to.
     *
     * @var array<string, string>
     */
    private const LABELS = [
        'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.web' => 'Web',
        'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.web_list' => 'Records',
        'LLL:EXT:acme_events/Resources/Private/Language/locallang.xlf:module.events' => 'Events',
        'LLL:EXT:backend/Resources/Private/Language/locallang.xlf:module.site' => 'Site Management',
    ];

    /**
     * The changelog the installed core ships.
     *
     * Three entries, and each is one of the states `ToolCalls` asks for. The
     * query that names a file, the sweep for a deprecation with a scanner tag,
     * and the deprecation that carries none. Every issue number is far outside
     * the range Forge has ever issued and every title names this fixture. So no
     * entry here can pass as something TYPO3 deprecated.
     *
     * @return array<string, string>
     */
    private static function changelog(string $branch): array
    {
        $directory = 'vendor/typo3/cms-core/Documentation/Changelog/' . $branch . '/';

        return [
            $directory . 'Deprecation-900001-ExtTablesPhpInTheFixtureExtension.rst' => self::entry(
                'Deprecation: #900001 - ext_tables.php in the fixture extension',
                'PHP-API, FullyScanned, ext:' . self::EXTENSION,
                'The fixture extension registers through :file:`ext_tables.php`, which is read on every request '
                . "and will be removed in TYPO3 v15.0.\nMove the registration to :file:`Configuration/TCA/` instead.",
            ),
            $directory . 'Deprecation-900002-TheFixtureLegacyLabelFile.rst' => self::entry(
                'Deprecation: #900002 - The fixture legacy label file',
                'PHP-API, NotScanned, ext:' . self::EXTENSION,
                'The fixture extension ships one label file per table. Nothing scans for it, which is what makes '
                . 'this entry the other half of a sweep by tag.',
            ),
            $directory . 'Feature-900003-ATeaserContentElementInTheFixture.rst' => self::entry(
                'Feature: #900003 - A teaser content element in the fixture',
                'TCA, ext:' . self::EXTENSION,
                'The fixture extension registers one content element, so the installation has a CType that is not '
                . 'the core\'s.',
            ),
        ];
    }

    /** One changelog entry, in the shape `Changelog` reads them in. */
    private static function entry(string $heading, string $tags, string $description): string
    {
        return sprintf(
            ".. include:: /Includes.rst.txt\n\n%s\n%s\n%s\n\nDescription\n===========\n\n%s\n\nImpact\n"
            . "======\n\nNothing outside this fixture.\n\n.. index:: %s\n",
            str_repeat('=', strlen($heading)),
            $heading,
            str_repeat('=', strlen($heading)),
            $description,
            $tags,
        );
    }

    /**
     * The console, which answers the four commands the installation-backed
     * tools run and refuses everything else the way a console does.
     *
     * It is a program rather than a record. `language:domain:search` applies
     * the regex it receives. So the query that asks for nothing gets the
     * warning the real console prints, and the tool takes the "none" branch it
     * exists for. A script that printed the same payload whatever the question
     * would record one answer twice.
     */
    private static function console(): string
    {
        $labels = var_export([
            'EXT:backend/Resources/Private/Language/locallang.xlf' => [
                'domain' => 'backend.locallang',
                'labels' => ['labels.save' => 'Save', 'labels.saveAndClose' => 'Save and close', 'labels.close' => 'Close'],
            ],
            'EXT:core/Resources/Private/Language/locallang_core.xlf' => [
                'domain' => 'core.locallang_core',
                'labels' => ['labels.savedok' => 'Save document', 'labels.cancel' => 'Cancel'],
            ],
            'EXT:acme_events/Resources/Private/Language/locallang_db.xlf' => [
                'domain' => 'acme_events.locallang_db',
                'labels' => ['tx_acme_events_event' => 'Event', 'tx_acme_events_event.title' => 'Title'],
            ],
        ], true);
        $namespaces = var_export([
            'core' => ['TYPO3\\CMS\\Core\\ViewHelpers'],
            'f' => ['TYPO3Fluid\\Fluid\\ViewHelpers', 'TYPO3\\CMS\\Fluid\\ViewHelpers'],
        ], true);
        $configuration = var_export([
            'SYS/fluid' => [
                'namespaces' => [
                    'f' => ['TYPO3Fluid\\Fluid\\ViewHelpers', 'TYPO3\\CMS\\Fluid\\ViewHelpers'],
                ],
                'interceptors' => [],
            ],
        ], true);

        return <<<PHP
            #!/usr/bin/env php
            <?php

            // The console of the fixture installation. It is started by
            // Typo3Cli as a subprocess, exactly as a real one is.

            \$arguments = array_slice(\$argv, 1);
            \$command = \$arguments[0] ?? '';
            \$option = static function (string \$name) use (\$arguments): ?string {
                foreach (\$arguments as \$argument) {
                    if (str_starts_with(\$argument, '--' . \$name . '=')) {
                        return substr(\$argument, strlen(\$name) + 3);
                    }
                }
                return null;
            };

            if (\$command === 'language:domain:search') {
                \$resources = {$labels};
                \$regex = \$option('regex');
                \$extension = \$option('extension');
                \$items = [];
                foreach (\$resources as \$resource => \$declared) {
                    if (\$extension !== null && \$extension !== '' && !str_starts_with(\$resource, 'EXT:' . \$extension . '/')) {
                        continue;
                    }
                    \$matched = [];
                    foreach (\$declared['labels'] as \$reference => \$label) {
                        if (\$regex !== null && \$regex !== ''
                            && preg_match(\$regex, \$reference) !== 1 && preg_match(\$regex, \$label) !== 1) {
                            continue;
                        }
                        \$matched[] = ['domain' => \$declared['domain'], 'reference' => \$reference, 'label' => \$label];
                    }
                    if (\$matched !== []) {
                        \$items[] = ['resource' => \$resource, 'domain' => \$declared['domain'], 'labels' => \$matched];
                    }
                }
                if (\$items === []) {
                    // What the console prints instead of a payload, and it
                    // exits successfully doing it: an installation that
                    // answered "none" rather than one that could not be asked.
                    fwrite(STDOUT, "No language resource files found\\n");
                    exit(0);
                }
                fwrite(STDOUT, json_encode(['items' => \$items], JSON_UNESCAPED_SLASHES));
                exit(0);
            }

            if (\$command === 'fluid:namespaces') {
                fwrite(STDOUT, json_encode({$namespaces}, JSON_UNESCAPED_SLASHES));
                exit(0);
            }

            if (\$command === 'configuration:show') {
                \$known = {$configuration};
                \$path = trim(\$arguments[1] ?? '', '/');
                if (!array_key_exists(\$path, \$known)) {
                    fwrite(STDERR, 'No configuration found for path "' . \$path . '"' . "\\n");
                    exit(1);
                }
                fwrite(STDOUT, json_encode(\$known[\$path], JSON_UNESCAPED_SLASHES));
                exit(0);
            }

            fwrite(STDERR, 'Command "' . \$command . '" is not defined.' . "\\n");
            exit(1);
            PHP;
    }

    /** A site the project configures, in the shape `Project` reads one in. */
    private static function site(): string
    {
        return "rootPageId: 1\n"
            . "base: 'https://fixture.example.org/'\n"
            . "dependencies:\n"
            . "  - typo3/fluid-styled-content\n"
            . "  - acme/acme-events\n"
            . "languages:\n"
            . "  -\n    title: English\n    languageId: 0\n    locale: en_US.UTF-8\n    base: /\n";
    }

    /** @param array<string, string> $labels */
    private static function labelFile(array $labels): string
    {
        $units = '';
        foreach ($labels as $key => $source) {
            $units .= sprintf(
                "      <trans-unit id=\"%s\">\n        <source>%s</source>\n      </trans-unit>\n",
                $key,
                $source,
            );
        }

        return "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\" ?>\n<xliff version=\"1.0\">\n"
            . "  <file source-language=\"en\" datatype=\"plaintext\" original=\"messages\">\n    <body>\n"
            . $units
            . "    </body>\n  </file>\n</xliff>\n";
    }

    /**
     * One installed package's manifest, with what it requires where something
     * here reads that.
     *
     * `typo3_project_describe` reports the installed core's PHP floor out of
     * this file. So the core package states the one TYPO3 14.3 really declares:
     * `^8.2`, read in `.checkouts/14.3` on 2026-08-04. It stands here rather
     * than comes from the branch beside it, because the floor does not follow
     * the major. 12.4 requires `^8.1`, 13.4 and 14.3 both `^8.2`, main `^8.5`.
     *
     * @param array<string, string> $require
     */
    private static function manifest(string $name, string $key, string $type, array $require = []): string
    {
        return self::json([
            'name' => $name,
            'type' => $type,
            'description' => sprintf('The fixture installation\'s %s package.', $key),
            ...($require === [] ? [] : ['require' => $require]),
            'extra' => ['typo3/cms' => ['extension-key' => $key]],
        ]);
    }

    /** @param array<string, mixed> $value */
    private static function json(array $value): string
    {
        return (string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function put(string $path, string $contents): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0o777, true);
        }
        file_put_contents($path, $contents);
    }

    /**
     * Takes the last one away before it writes this one.
     *
     * A file the shape no longer has would otherwise stay. A renamed extension
     * then answers under both keys, and the record shows an installation
     * nothing here describes.
     */
    private static function clear(string $root): void
    {
        if (!is_dir($root)) {
            return;
        }

        // Read whole before any removal: the walk is lazy, and a directory
        // taken away under it is one the iterator then descends into.
        $entries = iterator_to_array(Finder::create()->in($root)->sortByName()->reverseSorting(), false);
        foreach ($entries as $entry) {
            $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
        }
        rmdir($root);
    }
}
