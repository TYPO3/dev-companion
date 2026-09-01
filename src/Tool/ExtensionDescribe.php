<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Extension;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * What one extension registers, from its own files.
 */
final class ExtensionDescribe extends ReadOnlyTool
{
    /**
     * The fields the extension schema requires, empty. A miss answers with the
     * same shape as a hit, so a client never has to branch on which it got.
     *
     * answeredBy is not among them, because the two misses this fills are not
     * the same answer: an installation that has other extensions and not this
     * one has answered, and a directory with no installation has not. Sharing
     * the value made every miss report "nothing", which says the installation
     * could not be asked about an installation that just listed 27 packages.
     *
     * @var array<string, mixed>
     */
    private const MISS_FIELDS = [
        'path' => null,
        'origin' => null,
        'composerName' => null,
        'description' => null,
        'requires' => [],
        'tcaTables' => [],
        'tcaOverrides' => [],
        'contentElements' => [],
        'unlistedFlexForms' => [],
        'backendModules' => [],
        'backendRoutes' => [],
        'icons' => [],
        'siteSets' => [],
        'formConfigurations' => [],
        'middlewares' => [],
        'serviceTags' => [],
        'fluidRoots' => [],
        'fluidNamespaces' => [],
        'typoScript' => [],
        'classes' => ['directories' => [], 'looseFiles' => 0, 'total' => 0],
        'files' => [],
        'deprecatedFiles' => [],
        'notReadStatically' => [],
        'artifacts' => ['manual' => null, 'readme' => null, 'tests' => [], 'languageFiles' => []],
    ];

    /**
     * The call a listed binding raises, said once and rendered wherever one is
     * listed — `D-ANS-129`.
     */
    private const FLEX_FORM_CALL = 'typo3_flexform_lookup resolves one of those structures to the fields it holds '
        . 'and the names a template and a settings array read them by: the table, the type=flex column the binding '
        . 'sits on, and record={"CType": "<the identifier>"} for the element it applies to.';

    public static function name(): string
    {
        return 'typo3_extension_describe';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation, Source::Packages];
    }

    public static function description(): string
    {
        return 'Describe what one installed extension registers: the tables its TCA defines and the ones it extends, the content elements it adds to tt_content with the Fluid template each renders through and the FlexForm each binds, its backend modules and routes, its icons, its site sets with the files each carries, the form configurations it registers and the form definitions they store, its service tags, middlewares, which of the three Fluid root directories it ships and the namespaces it registers globally, and the shape of its Classes/ directory — and beside all that: its manual, its README, its test layers, and its XLF files with the source language each declares. Those four are answered when they are absent too, which a file listing cannot show. A content element that is an Extbase plugin is marked as one and points at plugin.tx_<identifier>: it renders through the dispatcher and has no templateName to be missing. Tables, content elements and icons come from the booted installation where there is one, attributed to this extension by the EXT: reference each entry carries, so a list built in a loop or a table added by a PHP call is in the answer; everything else is parsed from that extension\'s own files, never executed, so it answers on a fresh clone and for a third-party extension as well as for the project\'s own. answeredBy says which of the two answered, and names what packages leaves out. A file it ships that core has stopped reading, or is stopping, is reported with what it costs, because that predicate is the file rather than anything the extension calls and no changelog search over its code reaches it: ext_tables.php, ext_emconf.php beside a composer.json declaring neither providesPackages nor a version, ext_icon.* where no Resources/Public/Icons/Extension.* stands to be read first, and an ext_typoscript_*.txt with no .typoscript file of the same name beside it. That is those four and nothing else, so it is not an upgrade check. typo3_project_describe names the extensions this can be called for.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'extension' => ['type' => 'string', 'minLength' => 1, 'description' => 'The extension key, as typo3_project_describe reports it, for example "my_sitepackage" or "news".'],
            ],
            'required' => ['extension'],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'key' => Schema::string('The extension key that was asked for.'),
            'path' => Schema::nullableString('Absolute path of the extension. Null when the installation does not have it.'),
            'origin' => ['type' => ['string', 'null'], 'enum' => ['system', 'project', 'third-party', 'fixture', null], 'description' => 'system: TYPO3\'s own. project: inside the repository. third-party: installed as a dependency. fixture: below a Tests/ directory, so it belongs to the test setup.'],
            'composerName' => Schema::nullableString('The Composer package name it declares.'),
            'description' => Schema::nullableString('What its composer.json says it is.'),
            'requires' => Schema::listOf(Schema::object([
                'package' => Schema::string(),
                'constraint' => Schema::string(),
            ], ['package', 'constraint']), 'What it requires, which is where a version conflict during an upgrade comes from.'),
            'tcaTables' => Schema::listOf(Schema::string(), 'Tables its Configuration/TCA/ defines, by file name. typo3_schema_lookup takes one of these names and answers what columns the core derives for it.'),
            'tcaOverrides' => Schema::listOf(Schema::string(), 'Tables it extends below Configuration/TCA/Overrides/. typo3_schema_lookup answers these the same way.'),
            'contentElements' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('The CType value, read from an addTcaSelectItem(), addRecordType() or registerPlugin() call in one of those override files. An identifier assembled at runtime or taken from a constant is not among them.'),
                'kind' => ['type' => 'string', 'enum' => ['element', 'plugin'], 'description' => 'plugin: an Extbase plugin, registered by ExtensionUtility::registerPlugin(), which renders through the dispatcher rather than through a templateName of its own. element: everything else.'],
                'templateName' => Schema::nullableString('The Fluid template it renders through, from tt_content.<identifier>.templateName in this extension\'s TypoScript. Null where its TypoScript does not set one — another extension or the site configuration may. On a plugin, one set here replaces the Generic wrapper configurePlugin() generates instead of naming the plugin\'s template, and null is the normal case.'),
                'source' => Schema::nullableString('The TypoScript file that set it, relative to the extension.'),
                'pluginSettings' => Schema::nullableString('On a plugin: the TypoScript file of this extension that configures plugin.tx_<identifier>, which is where its templateRootPaths and settings are. Null where its TypoScript configures nothing there, and on anything that is not a plugin.'),
                'flexForm' => Schema::nullableString('The FlexForm data structure it binds, as the call declares it — a FILE:EXT: reference, or "inline" where the XML stands in the override file itself. Null where it binds none, which is a different element to review than one that does.'),
            ], ['identifier', 'kind', 'templateName', 'source', 'pluginSettings', 'flexForm']), 'The content elements it adds to tt_content, where each renders, and what it configures through.'),
            'unlistedFlexForms' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('The content type the binding names.'),
                'flexForm' => Schema::string('The data structure, as above.'),
            ], ['identifier', 'flexForm']), 'FlexForm bindings read from the override files whose content type none of the contentElements entries above carries. typo3_flexform_lookup resolves one to its fields. Usually empty. An entry here is a registration this answer read and could not attribute: the identifier is real and the binding is real, and whatever else registers that element was not established.'),
            'backendModules' => Schema::listOf(Schema::string(), 'Module identifiers from Configuration/Backend/Modules.php.'),
            'backendRoutes' => Schema::listOf(Schema::string(), 'Route names from Configuration/Backend/Routes.php and AjaxRoutes.php.'),
            'icons' => Schema::listOf(Schema::string(), 'Identifiers from Configuration/Icons.php. typo3_icon_lookup searches every package at once.'),
            'siteSets' => Schema::listOf(Schema::object([
                'name' => Schema::string('The composer-style set name a site depends on.'),
                'path' => Schema::string('Relative to the extension.'),
                'files' => Schema::listOf(Schema::string(), 'Which of the files core reads a set directory for are in it: settings.definitions.yaml, settings.yaml, route-enhancers.yaml, labels.xlf, page.tsconfig, constants.typoscript, setup.typoscript and include_static_file.txt. config.yaml is not among them, being what makes the directory a set. route-enhancers.yaml is read from v14.1; on v13 a set carrying one is loaded and that file ignored. The last four are the defaults a set gets where its config.yaml declares no typoscript, pagets or labels path of its own — one that declares them reads from there instead, and this list does not say so.'),
            ], ['name', 'path', 'files'])),
            'formConfigurations' => Schema::listOf(Schema::object([
                'path' => Schema::string('The YAML file, relative to the extension.'),
                'name' => Schema::nullableString('The set name its config.yaml declares, which is what disabledSets matches against. Null for a TypoScript-registered file, which has none.'),
                'registeredBy' => ['type' => 'string', 'enum' => ['set', 'typoscript'], 'description' => 'set: the directory convention Configuration/Form/<SetName>/config.yaml, collected from every active extension since v14.2 without being registered anywhere. typoscript: plugin.tx_form.settings.yamlConfigurations or the module. one beside it, which is the way before it, deprecated in v14.2 and removed in v15.0.'],
                'storagePaths' => Schema::listOf(Schema::string(), 'What it declares under persistenceManager.allowedExtensionPaths — where the form definitions it stores live. A storage configured as a file mount instead is a record and is in no answer read from files.'),
                'formDefinitions' => Schema::listOf(Schema::string(), 'The .form.yaml files below those of the storage paths that are inside this extension, relative to it.'),
            ], ['path', 'name', 'registeredBy', 'storagePaths', 'formDefinitions']), 'The form configurations it registers, both ways in. Empty where it registers none — an extension that ships a .form.yaml and registers no storage for it has a form nothing loads, which is what this list is read for.'),
            'middlewares' => Schema::listOf(Schema::string(), 'Middleware identifiers from Configuration/RequestMiddlewares.php, across the request scopes.'),
            'serviceTags' => Schema::listOf(Schema::string(), 'Tags its Services.yaml carries, such as data.processor, event.listener or console.command.'),
            'fluidRoots' => Schema::listOf(Schema::string(), 'Which of Resources/Private/Templates, Partials and Layouts exist.'),
            'fluidNamespaces' => Schema::listOf(Schema::string(), 'Prefixes it registers globally in Configuration/Fluid/Namespaces.php.'),
            'typoScript' => Schema::listOf(Schema::string(), 'Files below Configuration/TypoScript/.'),
            'classes' => Schema::object([
                'directories' => Schema::listOf(Schema::object([
                    'name' => Schema::string('The directory, directly below Classes/, for example EventListener or Utility.'),
                    'files' => Schema::integer('PHP files anywhere below it, its own subdirectories included.'),
                ], ['name', 'files']), 'Every directory directly below Classes/, whatever it is named. Nothing is filtered, so a directory here is not a registration of any kind — it is what the extension calls it.'),
                'looseFiles' => Schema::integer('PHP files lying directly in Classes/, under no directory of their own.'),
                'total' => Schema::integer('Every PHP file below Classes/, which is what `find Classes -name \'*.php\' | wc -l` gives. The rows above and looseFiles add up to it.'),
            ], ['directories', 'looseFiles', 'total'], 'The shape of its Classes/ directory, read off the file tree rather than off a registration.'),
            'files' => Schema::listOf(Schema::string(), 'Registration files it ships, from ext_localconf.php to Initialisation/data.t3d.'),
            'deprecatedFiles' => Schema::listOf(Schema::deprecatedFile(), 'The files this extension ships that core has stopped reading, or is stopping, each with what shipping it costs. Four predicates are checked: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif and the two ext_typoscript_*.txt. Each is read from the extension\'s own tree — the file, whatever core reads before it, and for ext_emconf.php what composer.json declares — which is where every one of these predicates lives, so no changelog sweep over what its code calls reaches any of them. An empty list says none of the four holds here, not that the extension is ready for the next major: nothing else here is checked for a deprecation, and typo3_changelog_lookup is what answers that question.'),
            'notReadStatically' => Schema::listOf(Schema::string(), 'Declaration files that are there but whose entries do not stand in their own text: each assembles its list while it runs, so what it registers is missing from the lists above rather than absent. The booted installation is what answers for them. An empty list says each declaration file that exists stood in its own text, not that everything the extension ships was read: ext_localconf.php and ext_tables.php register by running and are read by nothing here.'),
            'artifacts' => Schema::object([
                'manual' => Schema::nullableString('Its manual entry point, "Documentation/" where the directory exists without one, null where the extension ships no manual at all.'),
                'readme' => Schema::nullableString('The README it ships, null where there is none.'),
                'tests' => Schema::listOf(Schema::string(), 'The layers below Tests/, for example Unit and Functional. Empty where the extension ships no tests.'),
                'languageFiles' => Schema::listOf(Schema::object([
                    'path' => Schema::string('Relative to the extension.'),
                    'sourceLanguage' => Schema::nullableString('The source-language its own <file> element declares, null where it declares none. This is what the file says, not what it should say.'),
                    'translations' => Schema::listOf(Schema::string(), 'Locales of the prefixed files beside it, such as de for de.messages.xlf.'),
                ], ['path', 'sourceLanguage', 'translations'])),
            ], ['manual', 'readme', 'tests', 'languageFiles'], 'What it ships beside its registrations. Every key is present even when the artifact is not, because the absence of a manual, a test or a translation is the answer a file listing cannot give.'),
            'installed' => Schema::listOf(Schema::string(), 'On a miss: the extension keys this installation does have.'),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
        ], ['key', 'path', 'origin', 'tcaTables', 'tcaOverrides', 'contentElements', 'unlistedFlexForms', 'backendModules', 'icons', 'siteSets', 'formConfigurations', 'serviceTags', 'files', 'deprecatedFiles', 'notReadStatically', 'artifacts', 'answeredBy'], ['key']);
    }

    public static function answer(array $args): ToolResult
    {
        $key = trim((string) ($args['extension'] ?? ''));
        $extension = $key === '' ? null : Extension::describe($key);
        if ($extension === null) {
            return self::miss($key);
        }

        $lines = [sprintf(
            '%s (%s) — %s%s',
            $extension['key'],
            $extension['origin'],
            $extension['path'],
            $extension['description'] === null ? '' : "\n" . $extension['description'],
        )];

        $sections = [
            'TCA tables it defines' => $extension['tcaTables'],
            'TCA tables it extends' => $extension['tcaOverrides'],
            'Backend modules' => $extension['backendModules'],
            'Backend routes' => $extension['backendRoutes'],
            'Icons' => $extension['icons'],
            'Middlewares' => $extension['middlewares'],
            'Service tags' => $extension['serviceTags'],
            'Fluid namespaces declared globally' => $extension['fluidNamespaces'],
            'TypoScript files' => $extension['typoScript'],
            'Registration files' => $extension['files'],
        ];
        foreach ($sections as $heading => $entries) {
            if ($entries !== []) {
                $lines[] = '';
                $lines[] = $heading . ': ' . implode(', ', $entries);
            }
        }

        // The next call, named where the list that raises it stands. A session
        // holding this answer read a FlexForm with grep and diffed two tables'
        // columns through `SHOW COLUMNS`, having called neither tool all day —
        // `D-ANS-129`.
        if ($extension['tcaTables'] !== [] || $extension['tcaOverrides'] !== []) {
            $lines[] = 'typo3_schema_lookup with one of those table names answers what columns the core derives '
                . 'for it, with the type, the default and the length of each. That is the resolved schema rather '
                . 'than what the TCA file declares, and two tables generated from one TCA do not have to agree on '
                . 'the order they come back in.';
        }

        // Apart from the registrations above, because this one is three is_dir()
        // calls: an extension appending its layout root from an event listener
        // got the same line as one that declares it — D-ANS-045.
        if ($extension['fluidRoots'] !== []) {
            $lines[] = '';
            $lines[] = 'Fluid root directories it ships: ' . implode(', ', $extension['fluidRoots']);
            $lines[] = 'Each is a directory that is there rather than a root something declared. An Extbase '
                . 'controller of this extension falls back to these three; every other view is pointed at a root '
                . 'by TypoScript or by a call while the request runs, and neither of those is in this list.';
        }

        // Directly under the file listing, because that listing is where two of
        // the four are the finding: every predicate is a file the extension
        // ships, and none is reachable by a changelog sweep over what its code
        // calls — D-ANS-009. Nothing is rendered where there is none. "No
        // deprecated files" is one line away from being read as a compatibility
        // verdict, and this answer checks nothing else for one. Which
        // predicates were checked is the closing sentence rather than a line per
        // file that did not fire, for the same reason: a clean line under this
        // heading is that verdict at file granularity, and a reviewer confirmed
        // the absent sibling by hand for want of the sentence, not for want of
        // a bullet.
        if ($extension['deprecatedFiles'] !== []) {
            $lines[] = '';
            $lines[] = 'Files core has stopped reading, or is stopping:';
            foreach ($extension['deprecatedFiles'] as $deprecated) {
                $lines[] = sprintf(
                    '- %s (%s) — %s %s',
                    $deprecated['file'],
                    $deprecated['changelog'],
                    $deprecated['predicate'],
                    $deprecated['cost'],
                );
            }
            $lines[] = 'That is what shipping each file turns on, read from the files themselves. Four predicates '
                . 'are checked: ext_tables.php, ext_emconf.php, ext_icon.svg/.png/.gif, and ext_typoscript_setup.txt '
                . 'beside ext_typoscript_constants.txt. One of them missing from this block was looked at rather '
                . 'than skipped: the extension ships no such file, or what core reads first stands beside it — '
                . 'for ext_emconf.php a composer.json declaring the providesPackages and the version, for '
                . 'ext_icon.* a Resources/Public/Icons/Extension.*, for an ext_typoscript_*.txt the .typoscript '
                . 'file of the same name. It is not an upgrade check: nothing else above was looked at for a '
                . 'deprecation, and typo3_changelog_lookup is what answers that — #109438, #108345, #98093 and '
                . '#96518 whole, and everything they leave out.';
        }

        if ($extension['contentElements'] !== []) {
            $lines[] = '';
            $lines[] = 'Content elements it adds:';
            foreach ($extension['contentElements'] as $element) {
                $lines[] = '- ' . $element['identifier'] . ' — ' . self::renders($element)
                    . ($element['flexForm'] === null ? '' : ', FlexForm ' . $element['flexForm']);
            }
            $lines[] = 'The identifiers come from the addRecordType(), addTcaSelectItem() and registerPlugin() calls '
                . 'below Configuration/TCA/Overrides/ and the templates from tt_content.<identifier>.templateName in '
                . 'its TypoScript. A value the file assigns to a variable once is followed there; one a call puts '
                . 'together at runtime, takes from a constant, or reads from a variable that file assigns more than '
                . 'once, is in neither. An element named without a FlexForm binds none: the binding is read from the '
                . 'same files, from addPiFlexFormValue(), from the data structure argument addPlugin() and '
                . 'registerPlugin() take since v14.2, and from a columnsOverrides assignment on pi_flexform.';
            if (array_filter(array_column($extension['contentElements'], 'flexForm')) !== []) {
                $lines[] = self::FLEX_FORM_CALL;
            }
            if (in_array('plugin', array_column($extension['contentElements'], 'kind'), true)) {
                $lines[] = 'A plugin renders through the Extbase dispatcher: configurePlugin() generates '
                    . 'tt_content.<identifier> on lib.contentElement with templateName = Generic and an EXTBASEPLUGIN '
                    . 'below it, so no templateName here is no missing template. Its own templates come from '
                    . 'plugin.tx_<identifier>.view, and one set under tt_content replaces that Generic wrapper '
                    . 'instead. Before 14.0 configurePlugin() could register a plugin under list_type rather than as '
                    . 'its own CType, and that call is in ext_localconf.php, which nothing here reads.';
            }
        }

        if ($extension['unlistedFlexForms'] !== []) {
            $lines[] = '';
            $lines[] = 'FlexForms bound to a content type none of the above names:';
            foreach ($extension['unlistedFlexForms'] as $binding) {
                $lines[] = '- ' . $binding['identifier'] . ' — ' . $binding['flexForm'];
            }
            $lines[] = 'Each identifier is registered by a call this answer does not read, so the element is real '
                . 'and what else it registers was not established. It is not a stray file.';
            $lines[] = self::FLEX_FORM_CALL;
        }

        if ($extension['siteSets'] !== []) {
            $lines[] = '';
            $lines[] = 'Site sets:';
            foreach ($extension['siteSets'] as $set) {
                $lines[] = '- ' . $set['name'] . ' (' . $set['path'] . ') — '
                    . ($set['files'] === [] ? 'config.yaml alone' : 'beside config.yaml: ' . implode(', ', $set['files']));
            }
            $lines[] = 'Those are the file names core reads a set directory for, and a name it does not read is not '
                . 'listed. route-enhancers.yaml is read from v14.1, so a set carrying one on v13 has it ignored. '
                . 'A config.yaml that declares a typoscript, pagets or labels path of its own reads from there '
                . 'instead of from the last of these, which this list does not say.';
        }

        if ($extension['formConfigurations'] !== []) {
            $lines[] = '';
            $lines[] = 'Form configurations:';
            foreach ($extension['formConfigurations'] as $configuration) {
                $lines[] = sprintf(
                    '- %s (%s)%s%s',
                    $configuration['path'],
                    $configuration['registeredBy'] === 'set'
                        ? 'form set ' . ($configuration['name'] ?? 'without a name')
                        : 'registered by TypoScript, the way deprecated in v14.2',
                    $configuration['storagePaths'] === []
                        ? ' — declares no storage path'
                        : ' — stores in ' . implode(', ', $configuration['storagePaths']),
                    $configuration['formDefinitions'] === []
                        ? ''
                        : ': ' . implode(', ', $configuration['formDefinitions']),
                );
            }
            $lines[] = 'A form set is the directory below Configuration/Form/ that carries a config.yaml, collected '
                . 'from every active extension since v14.2 and registered nowhere. Definitions are the .form.yaml '
                . 'files below the storage paths that are inside this extension; a storage configured as a file '
                . 'mount holds records instead and is in no answer read from files.';
        }

        $classes = $extension['classes'];
        $rows = array_map(
            static fn(array $directory): string => $directory['name'] . ' (' . $directory['files'] . ')',
            $classes['directories'],
        );
        if ($classes['looseFiles'] > 0) {
            // Their own row rather than a directory's, because that is where
            // they are: core keeps four of them, SingletonInterface.php among
            // them, and a whitelist of directory names had them nowhere.
            $rows[] = $classes['looseFiles'] . ' directly in Classes/';
        }
        if ($rows !== []) {
            $lines[] = '';
            $lines[] = 'Classes: ' . implode(', ', $rows) . ' — ' . $classes['total']
                . ' PHP file' . ($classes['total'] === 1 ? '' : 's') . ' in total.';
            // Both numbers are said because both are checked: a caller who
            // counts one level of Classes/Updates/ gets a smaller number
            // (D-ANS-008), and one who runs find over Classes/ gets the total.
            $lines[] = 'Every directory below Classes/ is named here, and each count is every PHP file below that '
                . 'directory, its own subdirectories included. The total is what `find Classes -name \'*.php\' | wc -l` '
                . 'gives.';
        }

        if ($extension['requires'] !== []) {
            $lines[] = '';
            $lines[] = 'Requires: ' . implode(', ', array_map(
                static fn(array $requirement): string => $requirement['package'] . ' ' . $requirement['constraint'],
                $extension['requires'],
            ));
        }

        // Always rendered, present or not. Everything above is found by reading
        // further; these are the ones a caller finds by being told, because a
        // manual nobody wrote leaves no file to notice. All four of them: the
        // language files were the list below alone until D-FBK-018, so an
        // extension shipping no XLF said nothing about them here. A term in
        // this line is what makes saying it on every answer cost a word.
        $artifacts = $extension['artifacts'];
        $lines[] = '';
        $lines[] = 'Ships: ' . implode(', ', [
            'manual ' . ($artifacts['manual'] ?? 'none'),
            'readme ' . ($artifacts['readme'] ?? 'none'),
            'tests ' . ($artifacts['tests'] === [] ? 'none' : implode('+', $artifacts['tests'])),
            'language files ' . ($artifacts['languageFiles'] === [] ? 'none' : count($artifacts['languageFiles'])),
        ]);
        if ($artifacts['languageFiles'] !== []) {
            foreach ($artifacts['languageFiles'] as $file) {
                $lines[] = sprintf(
                    '- %s — source-language %s, %s',
                    $file['path'],
                    $file['sourceLanguage'] ?? 'not declared',
                    $file['translations'] === []
                        ? 'no translations beside it'
                        : 'translated into ' . implode(', ', $file['translations']),
                );
            }
            $lines[] = 'The source language is what each file declares, not what it should declare — '
                . 'typo3_hint_lookup owns that rule.';
        }

        // Each absence names the workflow that owns it, on the object the
        // caller is already looking at. A session read `manual: null`,
        // `readme: null` and `tests: []` twice, wrote three READMEs by hand and
        // shipped no test at all, holding the closing sentence of the skill it
        // was following, which names both of these — D-SKL-053. Only where the
        // artifact is absent, so an extension that ships them reads as it did
        // before. Only these two, because no field of this answer reports that
        // nobody audited the package, and an absence is what a name here hangs
        // on.
        $absent = [];
        if ($artifacts['manual'] === null) {
            $absent[] = 'manual';
        }
        if ($artifacts['readme'] === null) {
            $absent[] = 'README';
        }
        if ($absent !== []) {
            $lines[] = 'It ships no ' . implode(' and no ', $absent)
                . ': `typo3-extension-documentation` is the workflow that writes '
                . (count($absent) === 1 ? 'one' : 'them') . '.';
        }
        if ($artifacts['tests'] === []) {
            $lines[] = 'It ships no test: `typo3-extension-testing` is the workflow that sets the first one up.';
        }

        $lines[] = '';
        // The boundary of this answer, stated rather than implied — and it is
        // not the same boundary in both cases, which is why it is not one
        // sentence with a clause bolted on.
        $lines[] = Extension::answeredBy() === 'installation'
            ? 'The tables, content elements and icons are what the booted installation has, attributed to this '
                . 'extension by the EXT: reference each entry carries; everything else is read from its files. '
                . 'What a hook or an event listener changes at request time is in neither.'
            : 'Read from the files, so this is what the extension declares — not what it does at runtime. '
                . 'A table or an icon list built in a loop, and anything a hook or an event listener changes, '
                . 'are not in this list; the files that could hold them are named above. The installation itself '
                . 'was not asked: ' . Typo3Runtime::reason() . '.';

        // The two a caller reads as registrations and this answer never opens.
        // notReadStatically is about a declaration file that defeated the
        // parser, so its empty list is not the "nothing more to read" a session
        // took it for — D-ANS-003.
        $byRunning = array_values(array_intersect(['ext_localconf.php', 'ext_tables.php'], $extension['files']));
        if ($byRunning !== []) {
            $lines[] = implode(' and ', $byRunning) . (count($byRunning) === 1 ? ' is' : ' are')
                . ' named above and read by nothing here. Each registers by running, so a hook, an RTE preset '
                . 'or a global Fluid namespace it sets is in none of the lists above. The booted installation '
                . 'answers the tables, content elements and icons it adds, and none of the rest.';
        }

        if ($extension['notReadStatically'] !== []) {
            // Which files the degradation cost, beside the reason it names: an
            // omitted section is otherwise the same silence as a file that is
            // not there at all.
            $lines[] = 'Nothing could be read statically from ' . implode(', ', $extension['notReadStatically'])
                . ': each is there, and each assembles its list while it runs, so what it registers is missing '
                . 'from the lists above rather than absent.';
        }

        return ToolResult::create(implode("\n", $lines), $extension + ['answeredBy' => Extension::answeredBy()]);
    }

    /**
     * What one content element of the list renders through.
     *
     * A plugin and an element are two answers to that, and the plugin one used
     * to be given as the element one with the template missing (`D-ANS-015`).
     * What replaces the absence is where its configuration is, because that is
     * the file the caller was after.
     *
     * @param array{identifier: string, kind: string, templateName: ?string, source: ?string, pluginSettings: ?string} $element
     */
    private static function renders(array $element): string
    {
        if ($element['kind'] === 'plugin') {
            $settings = 'plugin.tx_' . $element['identifier'];

            return 'Extbase plugin, renders through the dispatcher; '
                . match (true) {
                    $element['templateName'] !== null => 'wrapped in ' . $element['templateName']
                        . ' (' . $element['source'] . ') instead of Generic',
                    $element['pluginSettings'] !== null => 'configured under ' . $settings
                        . ' in ' . $element['pluginSettings'],
                    default => 'this extension\'s TypoScript sets nothing under ' . $settings,
                };
        }

        return $element['templateName'] === null
            ? 'no templateName in this extension\'s TypoScript; another extension or the site may set it'
            : 'renders through ' . $element['templateName'] . ' (' . $element['source'] . ')';
    }

    /** The keys there are, so a miss is a question a caller can ask again. */
    private static function miss(string $key): ToolResult
    {
        $installed = array_keys(Instance::packages());
        if ($installed === []) {
            return Unsupported::because(
                'no TYPO3 installation was found, so there are no extensions to describe',
                ['key' => $key],
            );
        }

        return ToolResult::create(
            $key === ''
                ? 'Name the extension to describe. This installation has: ' . implode(', ', $installed) . '.'
                : sprintf(
                    'This installation has no extension "%s". It has: %s.',
                    $key,
                    implode(', ', $installed),
                ),
            // The package list is what answered, and it is read from the
            // metadata the installed packages ship rather than from the booted
            // container — which is what "packages" says.
            self::MISS_FIELDS + ['key' => $key, 'installed' => $installed, 'answeredBy' => 'packages'],
        );
    }
}
