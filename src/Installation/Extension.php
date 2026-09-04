<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Installation;

use Symfony\Component\Finder\Finder;

/**
 * What one installed extension registers, read from its own files.
 *
 * typo3_project_describe names the extensions and where they are. A maintenance
 * question is almost never about that — it is about what is inside one of them:
 * which tables its TCA defines and which it extends, which backend modules and
 * icons it brings, which site sets it ships, what it hangs into the container.
 * Most of that is declarative and sits in files with fixed names, so it is
 * readable without a console and without a database, the same way the sites are.
 * What is not declarative is asked of the installation instead: `Typo3Runtime`
 * boots it in a subprocess and reads the registries, because a table added by a
 * PHP call, a content element whose identifier came out of a variable and an
 * icon list built in a `foreach` exist in no file a reader could follow. Those
 * three are attributed back to this extension by the `EXT:<key>/` reference the
 * entry itself carries; where the boot did not happen, the files answer and the
 * answer says so.
 *
 * Nothing is included or executed here. The declaration files are tokenised for
 * their keys (see PhpArray) and the YAML is parsed; where the extension's own
 * code runs, it runs in TYPO3's process, not in this one.
 */
final class Extension
{
    /**
     * The files core reads from a site set directory by exact name.
     *
     * `config.yaml` is not among them: it is what makes the directory a set,
     * so it is always there and saying so tells a reader nothing. The rest are
     * optional and each one is a registration — `YamlSetDefinitionProvider`
     * reads the first four, and the last four are the defaults
     * `typoscript`, `pagets` and `labels` fall back to when the set declares
     * none of its own.
     */
    private const SET_FILES = [
        'settings.definitions.yaml', 'settings.yaml', 'route-enhancers.yaml', 'labels.xlf',
        'page.tsconfig', 'constants.typoscript', 'setup.typoscript', 'include_static_file.txt',
    ];

    /** Files an extension is recognised by, each one a registration point. */
    private const ROOT_FILES = [
        'ext_localconf.php', 'ext_tables.php', 'ext_tables.sql', 'ext_emconf.php',
        'Configuration/page.tsconfig', 'Configuration/user.tsconfig',
        'Configuration/RequestMiddlewares.php', 'Configuration/Services.yaml',
        'Configuration/JavaScriptModules.php', 'Configuration/Fluid/Namespaces.php',
        'Initialisation/data.t3d', 'Initialisation/data.xml',
    ];

    /**
     * @return array{
     *     key: string,
     *     path: string,
     *     origin: string,
     *     composerName: ?string,
     *     description: ?string,
     *     requires: array<int, array{package: string, constraint: string}>,
     *     tcaTables: array<int, string>,
     *     tcaOverrides: array<int, string>,
     *     contentElements: array<int, array{identifier: string, kind: string, templateName: ?string, source: ?string, pluginSettings: ?string, flexForm: ?string}>,
     *     renderedContentTypes: array<int, array{identifier: string, templateName: ?string, source: string, registeredBy: ?string}>,
     *     pluginFrame: ?string,
     *     unlistedFlexForms: array<int, array{identifier: string, flexForm: string}>,
     *     backendModules: array<int, string>,
     *     backendRoutes: array<int, string>,
     *     icons: array<int, string>,
     *     siteSets: array<int, array{name: string, path: string, files: array<int, string>}>,
     *     formConfigurations: array<int, array{path: string, name: ?string, registeredBy: string, storagePaths: array<int, string>, formDefinitions: array<int, string>}>,
     *     unlistedFlexForms: array<int, array{identifier: string, flexForm: string}>,
     *     middlewares: array<int, string>,
     *     serviceTags: array<int, string>,
     *     fluidRoots: array<int, string>,
     *     fluidNamespaces: array<int, string>,
     *     typoScript: array<int, string>,
     *     classes: array{directories: array<int, array{name: string, files: int}>, looseFiles: int, total: int},
     *     files: array<int, string>,
     *     deprecatedFiles: array<int, array{file: string, changelog: string, predicate: string, cost: string}>,
     *     notReadStatically: array<int, string>,
     *     artifacts: array{
     *         manual: ?string,
     *         readme: ?string,
     *         tests: array<int, string>,
     *         languageFiles: array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>
     *     }
     * }|null
     */
    public static function describe(string $key): ?array
    {
        $path = Instance::packages()[$key] ?? null;
        if ($path === null) {
            return null;
        }

        $manifest = Data::json($path . '/composer.json');
        $requires = [];
        foreach ($manifest['require'] ?? [] as $package => $constraint) {
            $requires[] = ['package' => (string) $package, 'constraint' => (string) $constraint];
        }

        $overrides = self::overrides($path);
        $files = self::files($path);
        $typoScript = self::typoScriptValues($path);
        $elements = self::contentElements(
            self::cTypes($key, $overrides['contentElements']),
            $typoScript,
            $overrides['plugins'],
            $overrides['flexForms'],
        );

        return [
            'key' => $key,
            'path' => $path,
            'origin' => self::origin($key, $path),
            'composerName' => isset($manifest['name']) ? (string) $manifest['name'] : null,
            'description' => isset($manifest['description']) ? (string) $manifest['description'] : null,
            'requires' => $requires,
            // A file below Configuration/TCA/ is named after the table it
            // defines. A file below Overrides/ is not: extensions number them
            // to fix their load order, so which table it extends is read from
            // what the file does — see overrides().
            'tcaTables' => self::tcaTables($key, $path),
            'tcaOverrides' => $overrides['tables'],
            'contentElements' => $elements,
            'renderedContentTypes' => self::renderedContentTypes(
                $typoScript,
                array_column($elements, 'identifier'),
            ),
            'pluginFrame' => self::pluginFrame($path),
            'unlistedFlexForms' => self::unlistedFlexForms($overrides['flexForms'], $elements),
            'backendModules' => PhpArray::keys($path . '/Configuration/Backend/Modules.php'),
            'backendRoutes' => array_merge(
                PhpArray::keys($path . '/Configuration/Backend/Routes.php'),
                PhpArray::keys($path . '/Configuration/Backend/AjaxRoutes.php'),
            ),
            'icons' => self::icons($key, $path),
            'siteSets' => self::siteSets($path),
            'formConfigurations' => self::formConfigurations($key, $path, $typoScript),
            // The outer keys are the request scopes; the identifiers a caller
            // orders its own middleware against are one level below them.
            'middlewares' => PhpArray::keys($path . '/Configuration/RequestMiddlewares.php', 2),
            'serviceTags' => self::serviceTags($path),
            'fluidRoots' => self::fluidRoots($path),
            'fluidNamespaces' => array_keys(FluidNamespaces::declaredBy($path)),
            'typoScript' => self::baseNames($path . '/Configuration/TypoScript', '*.typoscript', ''),
            'classes' => self::classes($path),
            'files' => $files,
            'deprecatedFiles' => self::deprecatedFiles($path, $manifest, $files),
            'notReadStatically' => self::notReadStatically($path),
            'artifacts' => self::artifacts($path),
        ];
    }

    /**
     * The same verdict for an extension nobody asked about by key.
     *
     * `typo3_project_describe` volunteers it for the extensions inside the
     * repository, so a session that never calls `typo3_extension_describe` is
     * still told which of the files it ships core has stopped reading —
     * `D-ANS-009`. Reading the manifest and the file listing again is what a
     * path alone buys; everything else `describe()` computes stays uncomputed.
     *
     * @return array<int, array{file: string, changelog: string, predicate: string, cost: string}>
     */
    public static function deprecatedFilesOf(string $path): array
    {
        return self::deprecatedFiles($path, Data::json($path . '/composer.json'), self::files($path));
    }

    /**
     * What the extension ships beside its registrations — and what it does not.
     *
     * Everything above this answers what the extension registers, which a
     * caller can only ever find more of by reading further. These four are the
     * ones whose *absence* is the answer, and absence has no file to stumble
     * over: a review that lists the tree cannot see a manual nobody wrote. Each
     * key is therefore always present, null or empty where the artifact is not
     * there, so a caller can tell "looked for and missing" from "not looked
     * for".
     *
     * The source language is a fact about the file and is reported as one. What
     * it ought to be is a convention and stays in the knowledge base.
     *
     * @return array{manual: ?string, readme: ?string, tests: array<int, string>, languageFiles: array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>}
     */
    private static function artifacts(string $path): array
    {
        $manual = self::firstFile($path, ['Documentation/Index.rst', 'Documentation/index.rst']);
        if ($manual === null && is_dir($path . '/Documentation')) {
            // A manual whose entry point is missing is not the same as no
            // manual, and the difference is the finding.
            $manual = 'Documentation/';
        }

        $tests = [];
        if (is_dir($path . '/Tests')) {
            foreach (Finder::create()->directories()->in($path . '/Tests')->depth(0)->sortByName() as $directory) {
                $tests[] = $directory->getFilename();
            }
        }

        return [
            'manual' => $manual,
            'readme' => self::firstFile($path, ['README.rst', 'README.md', 'readme.md', 'README.txt']),
            'tests' => $tests,
            'languageFiles' => self::languageFiles($path),
        ];
    }

    /**
     * The XLF files it ships, each with the language its own header declares.
     *
     * A locale-prefixed file is the translation of the one beside it — de.foo.xlf
     * belongs to foo.xlf — so it is listed there rather than as a file of its
     * own, which is also the shape that makes a missing translation visible.
     *
     * @return array<int, array{path: string, sourceLanguage: ?string, translations: array<int, string>}>
     */
    private static function languageFiles(string $path): array
    {
        $directory = $path . '/Resources/Private/Language';
        if (!is_dir($directory)) {
            return [];
        }

        $sources = [];
        $translations = [];
        // The directory itself and one level below it: a language file sits
        // beside its translations, or in a subdirectory with them.
        foreach (Finder::create()->files()->in($directory)->depth('< 2')->name('*.xlf')->sortByName() as $file) {
            $relative = substr($file->getPathname(), strlen($path) + 1);
            if (preg_match('/^([a-z]{2}(?:_[A-Z]{2})?)\.(.+\.xlf)$/', $file->getFilename(), $matches) === 1) {
                $translations[dirname($relative) . '/' . $matches[2]][] = $matches[1];
                continue;
            }
            $sources[$relative] = self::sourceLanguage($file->getPathname());
        }

        $files = [];
        foreach ($sources as $relative => $language) {
            $locales = $translations[$relative] ?? [];
            sort($locales);
            $files[] = ['path' => $relative, 'sourceLanguage' => $language, 'translations' => $locales];
        }

        return $files;
    }

    /** The source-language of an XLF, from its <file> element and nothing else. */
    private static function sourceLanguage(string $file): ?string
    {
        $handle = @fopen($file, 'rb');
        if ($handle === false) {
            return null;
        }
        $head = (string) fread($handle, 4096);
        fclose($handle);

        return preg_match('/<file\b[^>]*\bsource-language="([^"]*)"/', $head, $matches) === 1
            ? $matches[1]
            : null;
    }

    /**
     * Whether this is TYPO3's own, the project's, a dependency it pulled in, or
     * a package its test setup ships — the same four the project scope draws,
     * so the two answers agree.
     */
    private static function origin(string $key, string $path): string
    {
        if (Instance::isSystemExtension($key) === true) {
            return 'system';
        }

        return Project::origin($path);
    }

    /**
     * ExtensionManagementUtility methods whose first argument is the table.
     *
     * Deliberately only these: addStaticFile() and addPiFlexFormValue() take a
     * first argument of exactly the same shape that is not a table, and a list
     * of tables with an extension key in it is worse than a shorter list.
     *
     * @var array<int, string>
     */
    private const TABLE_FIRST_METHODS = [
        'addToAllTCAtypes', 'addTCAcolumns', 'addFieldsToPalette',
        'addTcaSelectItem', 'addTcaSelectItemGroup', 'allowTableOnStandardPages',
    ];

    /**
     * What the override files do: the tables they extend, and the content
     * elements they add.
     *
     * The file name is no answer here — `102_tt_content.php` and
     * `600_ext_container.php` are both ordinary — because the number is what
     * fixes the order the overrides load in. What the file touches is either
     * $GLOBALS['TCA']['<table>'] or the first argument of one of the
     * ExtensionManagementUtility calls above, and both survive tokenising.
     *
     * @return array{tables: array<int, string>, contentElements: array<int, string>, plugins: array<int, string>, flexForms: array<string, string>}
     */
    private static function overrides(string $path): array
    {
        $directory = $path . '/Configuration/TCA/Overrides';
        if (!is_dir($directory)) {
            return ['tables' => [], 'contentElements' => [], 'plugins' => [], 'flexForms' => []];
        }

        $tables = [];
        $elements = [];
        $plugins = [];
        $flexForms = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name('*.php')->sortByName() as $file) {
            $found = self::declarationsIn((string) file_get_contents($file->getPathname()));
            $flexForms += $found['flexForms'];
            if ($found['tables'] === []) {
                // Nothing recognisable: the conventional file name is the best
                // that is left, and only where it looks like a table at all.
                $name = $file->getBasename('.php');
                $found['tables'] = preg_match('/^[a-z][a-z0-9_]*$/', $name) === 1 ? [$name] : [];
            }
            foreach ($found['tables'] as $table) {
                $tables[$table] = true;
            }
            foreach ($found['contentElements'] as $element) {
                $elements[$element] = true;
            }
            foreach ($found['plugins'] as $plugin) {
                $elements[$plugin] = true;
                $plugins[$plugin] = true;
            }
        }

        $names = array_keys($tables);
        sort($names);
        $identifiers = array_keys($elements);
        sort($identifiers);
        $signatures = array_keys($plugins);
        sort($signatures);
        ksort($flexForms);

        return ['tables' => $names, 'contentElements' => $identifiers, 'plugins' => $signatures, 'flexForms' => $flexForms];
    }

    /**
     * @return array{tables: array<int, string>, contentElements: array<int, string>, plugins: array<int, string>, flexForms: array<string, string>}
     */
    private static function declarationsIn(string $code): array
    {
        $tables = [];
        $elements = [];
        $plugins = [];
        $flexForms = [];
        $tokens = @token_get_all($code);
        $variables = self::stringVariables($tokens);
        $count = count($tokens);
        for ($index = 0; $index < $count; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token)) {
                continue;
            }

            if ($token[0] === T_VARIABLE && $token[1] === '$GLOBALS') {
                // Nine, because the longest subscript this reads is the one the
                // v14 deprecation of addPiFlexFormValue() points at, and the
                // value it assigns is the ninth literal before the semicolon.
                $keys = self::followingStrings($tokens, $index, 9);
                if (($keys[0] ?? '') === 'TCA' && isset($keys[1])) {
                    $tables[] = $keys[1];
                }
                $subscript = array_slice($keys, 0, 8);
                if (
                    count($subscript) === 8
                    && array_slice($subscript, 0, 3) === ['TCA', 'tt_content', 'types']
                    && array_slice($subscript, 4) === ['columnsOverrides', 'pi_flexform', 'config', 'ds']
                    && ($structure = self::dataStructure($keys[8] ?? null)) !== null
                ) {
                    $flexForms[$subscript[3]] = $structure;
                }
                continue;
            }

            if ($token[0] === T_STRING && $token[1] === 'addPiFlexFormValue') {
                // The way in until v14.3, where it is deprecated. Its first
                // argument was the plugin key and is now unused; the content
                // type it binds to is the third, and the second is the data
                // structure itself.
                $arguments = self::arguments($tokens, $index);
                $identifier = self::firstLiteral($arguments[2] ?? [], $variables);
                $structure = self::dataStructure(self::firstLiteral($arguments[1] ?? [], $variables));
                if ($identifier !== null && $structure !== null) {
                    $flexForms[$identifier] = $structure;
                }
                continue;
            }

            if ($token[0] === T_STRING && in_array($token[1], ['addPlugin', 'registerPlugin'], true)) {
                // One handler, because both calls answer two questions at once
                // and each of them used to have a reader of its own that ran
                // first and returned.
                //
                // The binding: both take the data structure as an argument
                // since v14.2, and it is where core binds its own from then on.
                // Which argument it is differs, and so does where the identifier
                // comes from — addPlugin() carries the select item,
                // registerPlugin() composes the signature out of its first two.
                //
                // The kind: an Extbase plugin is a content element registered by
                // a call of its own, and this is the only place it can stand.
                // The identifier is neither argument but the signature both
                // derive, which is what pluginSignature() already returns above.
                $arguments = self::arguments($tokens, $index);
                $isAddPlugin = $token[1] === 'addPlugin';
                $identifier = $isAddPlugin
                    ? self::selectItemValue($arguments[0] ?? [], $variables)
                    : self::pluginSignature($arguments, $variables);
                $structure = self::dataStructure(self::firstLiteral($arguments[$isAddPlugin ? 1 : 6] ?? [], $variables));
                if ($identifier !== null && $structure !== null) {
                    $flexForms[$identifier] = $structure;
                }

                if (!$isAddPlugin && $identifier !== null) {
                    $plugins[] = $identifier;
                }

                continue;
            }

            if ($token[0] === T_STRING && $token[1] === 'addRecordType') {
                $arguments = self::arguments($tokens, $index);
                // The one registration call that does not name its table first
                // and often does not name it at all: the table is the fifth
                // argument and defaults to tt_content, which is what makes this
                // the short way to register a content element on 13.4 and newer.
                // Read positionally — a call that passes `table:` by name keeps
                // the default here, the same way an identifier that is not a
                // literal is left out rather than guessed.
                $table = self::firstLiteral($arguments[4] ?? [], $variables) ?? 'tt_content';
                $tables[] = $table;
                if ($table === 'tt_content') {
                    $identifier = self::selectItemValue($arguments[0] ?? [], $variables);
                    if ($identifier !== null) {
                        $elements[] = $identifier;
                    }
                }
                continue;
            }

            if ($token[0] !== T_STRING || !in_array($token[1], self::TABLE_FIRST_METHODS, true)) {
                continue;
            }

            $arguments = self::arguments($tokens, $index);
            $table = self::firstLiteral($arguments[0] ?? [], $variables);
            if ($table === null) {
                continue;
            }
            $tables[] = $table;

            // A content element is an item of tt_content's CType. Every other
            // select item this call adds is a value in some other field, and
            // handing those over as content elements would be worse than the
            // pointer at tt_content the answer already carried.
            if (
                $token[1] === 'addTcaSelectItem'
                && $table === 'tt_content'
                && self::firstLiteral($arguments[1] ?? [], $variables) === 'CType'
            ) {
                $identifier = self::selectItemValue($arguments[2] ?? [], $variables);
                if ($identifier !== null) {
                    $elements[] = $identifier;
                }
            }
        }

        return [
            'tables' => array_values(array_filter(
                array_unique($tables),
                static fn(string $table): bool => preg_match('/^[a-z][a-z0-9_]*$/', $table) === 1,
            )),
            'contentElements' => array_values(array_unique($elements)),
            'plugins' => array_values(array_unique($plugins)),
            'flexForms' => $flexForms,
        ];
    }

    /**
     * A FlexForm data structure argument, as it is worth reporting.
     *
     * Every method that takes one documents the same two forms: a reference to
     * a file, or the XML itself. The reference is the answer — it names a file
     * a reviewer opens — and the XML is a document rather than a fact about the
     * registration, so it is reported as being there and not quoted.
     */
    private static function dataStructure(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (str_starts_with($value, 'FILE:')) {
            return $value;
        }

        return str_starts_with(ltrim($value), '<') ? 'inline' : null;
    }

    /**
     * The plugin signature registerPlugin() composes, from the two arguments it
     * composes it out of.
     *
     * `strtolower($extensionName) . '_' . strtolower($pluginName)`, on every
     * covered major, after the extension name has had its underscores taken
     * out — which is why `printworks_sitepackage` becomes
     * `printworkssitepackage_catalogue` rather than keeping its underscore.
     *
     * @param array<int, array<int, array{0: int, 1: string, 2: int}|string>> $arguments
     * @param array<string, string> $variables
     */
    private static function pluginSignature(array $arguments, array $variables = []): ?string
    {
        $extension = self::firstLiteral($arguments[0] ?? [], $variables);
        $plugin = self::firstLiteral($arguments[1] ?? [], $variables);
        if ($extension === null || $plugin === null) {
            return null;
        }

        return strtolower(str_replace('_', '', $extension)) . '_' . strtolower($plugin);
    }

    /**
     * The string variables a declaration file assigns to itself, once each.
     *
     * A registration file is read and never executed, so a value that arrives
     * through a variable used to be a value this parser could not see. Most of
     * them do not need executing: `$contentType = 'my_element';` at the top of a
     * TCA override, used further down, is a plain literal that took a detour,
     * and refusing it drops a whole content element from the answer.
     *
     * Two shapes are resolved — one assignment of a string literal, and one of
     * a registerPlugin() call, which returns a signature it composes out of its
     * own arguments. A variable assigned twice, or assigned anything else, is
     * dropped rather than resolved to its first value: what it holds at the call
     * depends on the order the file runs in, and that is the thing this parser
     * deliberately does not know.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<string, string>
     */
    private static function stringVariables(array $tokens): array
    {
        $values = [];
        $ambiguous = [];
        $count = count($tokens);
        for ($index = 0; $index < $count; ++$index) {
            $token = $tokens[$index];
            if (!is_array($token) || $token[0] !== T_VARIABLE || $token[1] === '$GLOBALS') {
                continue;
            }

            $name = $token[1];
            $assignment = self::nextSignificant($tokens, $index);
            if ($assignment === null || $tokens[$assignment] !== '=') {
                continue;
            }

            $value = self::nextSignificant($tokens, $assignment);
            $assigned = $value === null ? null : self::assignedValue($tokens, $value);
            if ($assigned === null || isset($values[$name])) {
                $ambiguous[$name] = true;
                continue;
            }

            $values[$name] = $assigned;
        }

        return array_diff_key($values, $ambiguous);
    }

    /**
     * What an assignment puts into a variable, where reading can say what it is.
     *
     * The second shape is the one core writes itself: `$contentTypeName =
     * ExtensionUtility::registerPlugin('Felogin', 'Login', …)`, used further
     * down as the content type a FlexForm binds to. The signature is composed
     * from arguments that stand in the file, so it is as readable as a literal
     * — and refusing it drops the binding of every plugin registered the way
     * core registers its own.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function assignedValue(array $tokens, int $value): ?string
    {
        $token = $tokens[$value];
        $end = self::nextSignificant($tokens, $value);
        if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING && $end !== null && $tokens[$end] === ';') {
            return trim($token[1], "'\"");
        }

        for ($next = $value; isset($tokens[$next]) && $tokens[$next] !== ';'; ++$next) {
            $current = $tokens[$next];
            if (is_array($current) && $current[0] === T_STRING && $current[1] === 'registerPlugin') {
                return self::pluginSignature(self::arguments($tokens, $next));
            }
        }

        return null;
    }

    /**
     * The index of the next token that is not whitespace or a comment.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     */
    private static function nextSignificant(array $tokens, int $index): ?int
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return $next;
        }

        return null;
    }

    /**
     * The identifier the item array of an addTcaSelectItem() call carries.
     *
     * Its shape changed inside the covered range: keyed by `value`, and
     * positional before that, where the value is the second entry after the
     * label. Both are read, because an extension is written for the line it
     * supports rather than for the newest one. An item whose value comes from a
     * constant, or from a variable this file assigns more than once, still has
     * no identifier that reading can establish.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $item
     * @param array<string, string> $variables
     */
    private static function selectItemValue(array $item, array $variables = []): ?string
    {
        $literals = [];
        $isKey = [];
        foreach ($item as $position => $token) {
            if (!is_array($token)) {
                continue;
            }
            if ($token[0] === T_VARIABLE) {
                if (!isset($variables[$token[1]])) {
                    continue;
                }
                $isKey[] = self::followedByArrow($item, $position);
                $literals[] = $variables[$token[1]];
                continue;
            }
            if ($token[0] !== T_CONSTANT_ENCAPSED_STRING) {
                continue;
            }
            $isKey[] = self::followedByArrow($item, $position);
            $literals[] = trim($token[1], "'\"");
        }

        $keyed = false;
        foreach ($literals as $position => $literal) {
            if ($isKey[$position] !== true) {
                continue;
            }
            $keyed = true;
            if ($literal === 'value' && ($isKey[$position + 1] ?? true) === false) {
                return $literals[$position + 1];
            }
        }

        // Positional: the label comes first and the value second.
        return $keyed ? null : ($literals[1] ?? null);
    }

    /** @param array<int, array{0: int, 1: string, 2: int}|string> $tokens */
    private static function followedByArrow(array $tokens, int $position): bool
    {
        for ($next = $position + 1; isset($tokens[$next]); ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && $token[0] === T_WHITESPACE) {
                continue;
            }

            return is_array($token) && $token[0] === T_DOUBLE_ARROW;
        }

        return false;
    }

    /**
     * The arguments of the call whose name is at $index, one token slice each.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<int, array<int, array{0: int, 1: string, 2: int}|string>>
     */
    private static function arguments(array $tokens, int $index): array
    {
        $count = count($tokens);
        for ($next = $index + 1; $next < $count && $tokens[$next] !== '('; ++$next) {
            // Whitespace and comments may stand between the two; anything else
            // means this name was not a call.
            if (!is_array($tokens[$next])) {
                return [];
            }
        }

        $arguments = [];
        $current = [];
        $depth = 0;
        for (; $next < $count; ++$next) {
            $token = $tokens[$next];
            if ($token === '(' || $token === '[') {
                if (++$depth === 1) {
                    continue;
                }
            } elseif ($token === ')' || $token === ']') {
                if (--$depth === 0) {
                    $arguments[] = $current;
                    break;
                }
            } elseif ($token === ',' && $depth === 1) {
                $arguments[] = $current;
                $current = [];
                continue;
            }
            $current[] = $token;
        }

        return $arguments;
    }

    /**
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @param array<string, string> $variables
     */
    private static function firstLiteral(array $tokens, array $variables = []): ?string
    {
        foreach ($tokens as $token) {
            if (!is_array($token)) {
                continue;
            }
            if ($token[0] === T_CONSTANT_ENCAPSED_STRING) {
                return trim($token[1], "'\"");
            }
            if ($token[0] === T_VARIABLE && isset($variables[$token[1]])) {
                return $variables[$token[1]];
            }
        }

        return null;
    }

    /**
     * The next $wanted string literals after $index, in order, stopping at the
     * end of the subscript they belong to.
     *
     * @param array<int, array{0: int, 1: string, 2: int}|string> $tokens
     * @return array<int, string>
     */
    private static function followingStrings(array $tokens, int $index, int $wanted): array
    {
        $found = [];
        $count = count($tokens);
        for ($next = $index + 1; $next < $count && count($found) < $wanted; ++$next) {
            $token = $tokens[$next];
            if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
                $found[] = trim($token[1], "'\"");
                continue;
            }
            if ($token === ';' || $token === ')') {
                break;
            }
        }

        return $found;
    }

    /**
     * The content types this extension renders and does not register.
     *
     * A sitepackage that takes the rendering frame over from
     * `fluid_styled_content` ends up owning `tt_content.shortcut` while
     * `EXT:frontend` still registers it, and the two questions — what does this
     * package render, what does it register — stop having one answer. Deleting
     * such a definition kills an element editors can still select, and nothing
     * else in the package points at it (`D-ANS-149`).
     *
     * The evidence is the assignment itself: a `tt_content.<identifier>` line
     * in this extension's own TypoScript whose identifier is none of the
     * elements it registers. Who does register it is the installation's to say,
     * read off the `EXT:` reference in the CType's label the way every other
     * attribution here is; null is a foreign identifier nothing could be asked
     * about, and on a booted installation it is a definition for an element
     * nothing registers at all.
     *
     * @param array<string, array{value: string, file: string}> $typoScript
     * @param array<int, string> $registered the identifiers this extension registers
     * @return array<int, array{identifier: string, templateName: ?string, source: string, registeredBy: ?string}>
     */
    private static function renderedContentTypes(array $typoScript, array $registered): array
    {
        $registry = Typo3Runtime::topic('contentElements');
        $found = [];
        foreach ($typoScript as $key => $set) {
            if (preg_match('/^tt_content\.([\w\-]+)(?:\.templateName)?$/', $key, $matches) !== 1) {
                continue;
            }
            $identifier = $matches[1];
            if (in_array($identifier, $registered, true) || isset($found[$identifier]['templateName'])) {
                continue;
            }
            $label = is_array($registry) && is_string($registry[$identifier]['label'] ?? null)
                ? $registry[$identifier]['label']
                : '';
            $found[$identifier] = [
                'identifier' => $identifier,
                'templateName' => str_ends_with($key, '.templateName') ? $set['value'] : null,
                'source' => $set['file'],
                'registeredBy' => $label === '' ? null : Typo3Runtime::extensionIn($label),
            ];
        }
        ksort($found);

        return array_values($found);
    }

    /**
     * The template every Extbase plugin on the site renders through, where this
     * extension ships one.
     *
     * `ExtensionUtility::configurePlugin()` writes
     * `tt_content.<signature> =< lib.contentElement` with
     * `templateName = Generic` for every plugin registered as a content type,
     * so a package that defines `lib.contentElement` itself owes a `Generic`
     * template beside it. Nothing in the package points at that file, which
     * makes it the one most likely to be deleted as unused, and deleting it
     * empties every plugin on the site — `EXT:form`'s content element included.
     *
     * Its own list rather than a row of the one above, because it is not a
     * content type: it is the frame all of them share.
     */
    private static function pluginFrame(string $path): ?string
    {
        $directory = $path . '/Resources/Private/Templates';
        if (!is_dir($directory)) {
            return null;
        }
        foreach (Finder::create()->files()->in($directory)->name('Generic.*html')->sortByName() as $file) {
            return substr($file->getPathname(), strlen($path) + 1);
        }

        return null;
    }

    /**
     * The content elements it adds, each with the template it renders through
     * and the FlexForm it binds.
     *
     * The template is this extension's own `templateName` under the identifier,
     * and where it says nothing the template stays unknown rather than being
     * derived from the identifier: a guessed file name sends the caller to a
     * file that is not there. The FlexForm is on the entry because an element
     * with one and an element without are different things to review. An Extbase
     * plugin has nothing to be unknown about — its rendering definition is
     * generated — so the kind is said instead and the answer points at
     * `plugin.tx_<identifier>` (`D-ANS-015`).
     *
     * @param array<int, string> $identifiers
     * @param array<string, array{value: string, file: string}> $typoScript
     * @param array<int, string> $plugins the signatures of the Extbase plugins among them
     * @param array<string, string> $flexForms
     * @return array<int, array{identifier: string, kind: string, templateName: ?string, source: ?string, pluginSettings: ?string, flexForm: ?string}>
     */
    private static function contentElements(array $identifiers, array $typoScript, array $plugins, array $flexForms): array
    {
        return array_map(static function (string $identifier) use ($typoScript, $plugins, $flexForms): array {
            $set = $typoScript['tt_content.' . $identifier . '.templateName'] ?? null;
            $isPlugin = in_array($identifier, $plugins, true);

            return [
                'identifier' => $identifier,
                'kind' => $isPlugin ? 'plugin' : 'element',
                'templateName' => $set['value'] ?? null,
                'source' => $set['file'] ?? null,
                'pluginSettings' => $isPlugin ? self::pluginSettings($typoScript, $identifier) : null,
                'flexForm' => $flexForms[$identifier] ?? null,
            ];
        }, $identifiers);
    }

    /**
     * The file where this extension's TypoScript configures a plugin.
     *
     * `plugin.tx_<signature>` is the path Extbase reads a plugin's own
     * configuration from, the signature being the same string as the CType, so
     * the question needs no second source. Any line below it counts — the
     * `view.templateRootPaths` a template is looked for in is one of several
     * ways to arrive there, and a reference to a `lib.` object is the shortest.
     *
     * @param array<string, array{value: string, file: string}> $typoScript
     */
    private static function pluginSettings(array $typoScript, string $identifier): ?string
    {
        $path = 'plugin.tx_' . $identifier;
        foreach ($typoScript as $key => $set) {
            if ($key === $path || str_starts_with($key, $path . '.')) {
                return $set['file'];
            }
        }

        return null;
    }

    /**
     * The FlexForm bindings that found no content element to sit on.
     *
     * Reading an override file for a binding and reading it for an identifier
     * are two different parsers, and the second does not recognise every call
     * the first does. Where they disagree the binding is reported here rather
     * than dropped — `R-ANS-012`.
     *
     * @param array<string, string> $flexForms
     * @param array<int, array{identifier: string, templateName: ?string, source: ?string, flexForm: ?string}> $elements
     * @return array<int, array{identifier: string, flexForm: string}>
     */
    private static function unlistedFlexForms(array $flexForms, array $elements): array
    {
        $listed = array_column($elements, 'identifier');

        $unlisted = [];
        foreach ($flexForms as $identifier => $structure) {
            if (!in_array($identifier, $listed, true)) {
                $unlisted[] = ['identifier' => $identifier, 'flexForm' => $structure];
            }
        }

        return $unlisted;
    }

    /**
     * Every value this extension's TypoScript sets, by its full path.
     *
     * Not a TypoScript parser: it tracks the nesting so that a value can be
     * addressed however it was written — `tt_content.x.templateName = T`, a
     * `tt_content.x { }` block, or a `tt_content { x { } }` one. A reference is
     * held as the path it copies from rather than followed. Conditions are
     * ignored rather than evaluated, so a value set only inside one reads as
     * though it were set outright; the file it came from travels with it, which
     * is where a caller checks that.
     *
     * @return array<string, array{value: string, file: string}>
     */
    private static function typoScriptValues(string $path): array
    {
        $values = [];
        foreach (self::typoScriptFiles($path) as $file) {
            $stack = [];
            $inMultiline = false;
            foreach (preg_split('/\R/', (string) file_get_contents($file)) ?: [] as $raw) {
                $line = trim($raw);
                if ($inMultiline) {
                    $inMultiline = $line !== ')';
                    continue;
                }
                if ($line === '' || $line[0] === '#' || $line[0] === '[' || str_starts_with($line, '//')) {
                    continue;
                }
                if ($line === '}') {
                    array_pop($stack);
                    continue;
                }
                if (preg_match('/^([\w.\-]+)\s*\{$/', $line, $matches) === 1) {
                    $stack[] = $matches[1];
                    continue;
                }
                // The reference operators along with the assignment, because
                // `plugin.tx_x < lib.y` is how a plugin usually arrives at its
                // configuration and a path that only ever stands on the left of
                // one is nowhere in a store of assignments alone.
                if (preg_match('/^([\w.\-]+)\s*(=<|<|=)\s*(.*)$/', $line, $matches) !== 1) {
                    continue;
                }
                if ($matches[3] === '(') {
                    $inMultiline = true;
                    continue;
                }
                $key = ($stack === [] ? '' : implode('.', $stack) . '.') . $matches[1];
                $values[$key] = [
                    'value' => ($matches[2] === '<' ? '< ' : '') . trim($matches[3]),
                    'file' => substr($file, strlen($path) + 1),
                ];
            }
        }

        return $values;
    }

    /**
     * The TypoScript files it ships, from both places it can put them: the
     * Configuration/TypoScript/ directory an extension is included from, and
     * the site sets a site depends on.
     *
     * @return array<int, string>
     */
    private static function typoScriptFiles(string $path): array
    {
        $directories = array_values(array_filter(
            [$path . '/Configuration/TypoScript', $path . '/Configuration/Sets'],
            is_dir(...),
        ));
        if ($directories === []) {
            return [];
        }

        $files = [];
        foreach (Finder::create()->files()->in($directories)->name('*.typoscript')->sortByName() as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * The site sets it ships, each with the files core reads it for.
     *
     * A set is a directory of files with fixed names, and which of them are
     * there is what the set carries: settings and their definitions, route
     * enhancers, labels, page TSconfig and TypoScript. Naming the directory
     * says where to look, which is the answer a caller already had.
     *
     * @return array<int, array{name: string, path: string, files: array<int, string>}>
     */
    private static function siteSets(string $path): array
    {
        $directory = $path . '/Configuration/Sets';
        if (!is_dir($directory)) {
            return [];
        }

        $sets = [];
        foreach (Finder::create()->files()->in($directory)->depth(1)->name('config.yaml')->sortByName() as $file) {
            $set = $file->getRelativePath();
            $sets[] = [
                'name' => (string) (Data::yaml($file->getPathname())['name'] ?? $set),
                'path' => 'Configuration/Sets/' . $set . '/',
                'files' => array_values(array_filter(
                    self::SET_FILES,
                    static fn(string $name): bool => is_file(dirname($file->getPathname()) . '/' . $name),
                )),
            ];
        }

        return $sets;
    }

    /**
     * The form configurations it registers, and the form definitions each one
     * stores.
     *
     * Two ways in, and an extension supporting two majors ships both. Since
     * v14.2 a directory below `Configuration/Form/` carrying a `config.yaml` is
     * a form set and is collected without being registered anywhere — the same
     * convention site sets already work by, and `FormYamlCollectorConfigurator`
     * is what walks it. Before that, and still read in v14.3, a YAML file is
     * registered by TypoScript under `plugin.tx_form.settings.yamlConfigurations`
     * or the `module.` one beside it, which is this extension's own TypoScript
     * and is already parsed.
     *
     * Either file declares where the form definitions live, in
     * `persistenceManager.allowedExtensionPaths`. The ones inside this extension
     * are read; a storage in a file mount is a record rather than a file and is
     * in no answer that reads files.
     *
     * @param array<string, array{value: string, file: string}> $typoScript
     * @return array<int, array{path: string, name: ?string, registeredBy: string, storagePaths: array<int, string>, formDefinitions: array<int, string>}>
     */
    private static function formConfigurations(string $key, string $path, array $typoScript): array
    {
        $configurations = [];
        $directory = $path . '/Configuration/Form';
        if (is_dir($directory)) {
            foreach (Finder::create()->files()->in($directory)->depth(1)->name('config.yaml')->sortByName() as $file) {
                $configuration = Data::yaml($file->getPathname());
                $name = $configuration['name'] ?? null;
                $configurations[substr($file->getPathname(), strlen($path) + 1)] = [
                    'name' => is_string($name) && $name !== '' ? $name : null,
                    'registeredBy' => 'set',
                    'storage' => $configuration,
                ];
            }
        }

        foreach ($typoScript as $setting => $entry) {
            if (preg_match('/^(?:plugin|module)\.tx_form\.settings\.yamlConfigurations\./', $setting) !== 1) {
                continue;
            }
            $relative = self::inThisExtension($key, $entry['value']);
            if ($relative === null || isset($configurations[$relative])) {
                continue;
            }
            $configurations[$relative] = [
                'name' => null,
                'registeredBy' => 'typoscript',
                'storage' => Data::yaml($path . '/' . $relative),
            ];
        }

        ksort($configurations);

        $answer = [];
        foreach ($configurations as $relative => $configuration) {
            $storagePaths = $configuration['storage']['persistenceManager']['allowedExtensionPaths'] ?? [];
            $storagePaths = array_values(array_map(strval(...), is_array($storagePaths) ? $storagePaths : []));
            $answer[] = [
                'path' => $relative,
                'name' => $configuration['name'],
                'registeredBy' => $configuration['registeredBy'],
                'storagePaths' => $storagePaths,
                'formDefinitions' => self::formDefinitions($key, $path, $storagePaths),
            ];
        }

        return $answer;
    }

    /**
     * The form definitions below the storage paths that are this extension's.
     *
     * @param array<int, string> $storagePaths
     * @return array<int, string>
     */
    private static function formDefinitions(string $key, string $path, array $storagePaths): array
    {
        $directories = [];
        foreach ($storagePaths as $storage) {
            $relative = self::inThisExtension($key, rtrim($storage, '/') . '/');
            if ($relative !== null && is_dir($path . '/' . $relative)) {
                $directories[] = $path . '/' . $relative;
            }
        }
        if ($directories === []) {
            return [];
        }

        $definitions = [];
        foreach (Finder::create()->files()->in($directories)->name('*.form.yaml')->sortByName() as $file) {
            $definitions[] = substr($file->getPathname(), strlen($path) + 1);
        }

        return $definitions;
    }

    /**
     * What an `EXT:` reference points at inside this extension, relative to it,
     * or null where it points somewhere else.
     */
    private static function inThisExtension(string $key, string $reference): ?string
    {
        $prefix = 'EXT:' . $key . '/';

        return str_starts_with($reference, $prefix) ? substr($reference, strlen($prefix)) : null;
    }

    /**
     * The tags this extension's services carry, deduplicated.
     *
     * A tag is where an extension hangs itself into a core mechanism —
     * data.processor, event.listener, console.command — so the list says what
     * kind of extension this is in one line, without naming every service.
     *
     * @return array<int, string>
     */
    private static function serviceTags(string $path): array
    {
        $services = Data::yaml($path . '/Configuration/Services.yaml')['services'] ?? null;
        if (!is_array($services)) {
            return [];
        }

        $tags = [];
        foreach ($services as $definition) {
            foreach ((is_array($definition) ? $definition['tags'] ?? [] : []) as $tag) {
                $name = is_array($tag) ? ($tag['name'] ?? null) : $tag;
                if (is_string($name) && $name !== '') {
                    $tags[$name] = true;
                }
            }
        }

        $names = array_keys($tags);
        sort($names);

        return $names;
    }

    /** @return array<int, string> */
    private static function fluidRoots(string $path): array
    {
        $roots = [];
        foreach (['Templates', 'Partials', 'Layouts'] as $kind) {
            if (is_dir($path . '/Resources/Private/' . $kind)) {
                $roots[] = 'Resources/Private/' . $kind . '/';
            }
        }

        return $roots;
    }

    /**
     * The shape of Classes/: every directory in it, and every PHP file under it.
     *
     * Every directory, because thirteen recognised names were a filter nobody
     * chose — for `core` that left 106 of 1508 files out of the answer
     * (`D-ANS-045`). The total is beside the breakdown because it is the number
     * a caller checks the section with.
     *
     * @return array{directories: array<int, array{name: string, files: int}>, looseFiles: int, total: int}
     */
    private static function classes(string $path): array
    {
        $directory = $path . '/Classes';
        if (!is_dir($directory)) {
            return ['directories' => [], 'looseFiles' => 0, 'total' => 0];
        }

        $directories = [];
        foreach (Finder::create()->directories()->in($directory)->depth(0)->sortByName() as $entry) {
            $directories[] = [
                'name' => $entry->getFilename(),
                'files' => self::countPhpFiles($entry->getPathname()),
            ];
        }

        return [
            'directories' => $directories,
            'looseFiles' => Finder::create()->files()->in($directory)->depth(0)->name('*.php')->count(),
            'total' => self::countPhpFiles($directory),
        ];
    }

    /**
     * The whole subtree, because a nested directory has no row of its own.
     *
     * `Classes/Updates/Criteria/` is below a directory that is listed rather
     * than beside it, so counting one level alone would leave its files out of
     * that row. What the number covers is stated where it is rendered and in
     * the schema, because a reader who counts one level gets a different one —
     * `D-ANS-008`.
     */
    private static function countPhpFiles(string $directory): int
    {
        return Finder::create()->files()->in($directory)->name('*.php')->count();
    }

    /** @return array<int, string> */
    private static function files(string $path): array
    {
        return array_values(array_filter(
            self::ROOT_FILES,
            static fn(string $file): bool => is_file($path . '/' . $file),
        ));
    }

    /**
     * The files it ships that core has stopped reading, or is stopping, and what
     * each costs.
     *
     * The predicate is the file being there, plus what stands beside it that
     * core reads first — `D-ANS-009`, which has the four and where each was
     * read. Two are registration files `files` names already; the rest are read
     * by nothing now, so they are a registration point nowhere and are checked
     * here alone. A cost is stated with the version it starts at rather than
     * filtered by the installation's, because an extension supporting two
     * majors is read from both. A framework package is exempt from all four.
     *
     * @param array<string, mixed> $manifest
     * @param array<int, string> $files
     * @return array<int, array{file: string, changelog: string, predicate: string, cost: string}>
     */
    private static function deprecatedFiles(string $path, array $manifest, array $files): array
    {
        if (($manifest['type'] ?? null) === 'typo3-cms-framework') {
            return [];
        }

        $extra = $manifest['extra']['typo3/cms'] ?? null;
        $extra = is_array($extra) ? $extra : [];
        // PackageManager::isComposerOnlyCapable(): providesPackages declared —
        // an empty object counts, and is what an extension shipping no Composer
        // packages of its own writes — and a version in either of the two
        // places. Declaring one of the two and not the other still reads the
        // file.
        $composerOnly = isset($extra['Package']['providesPackages'])
            && (($manifest['version'] ?? null) !== null || isset($extra['version']));

        $deprecated = [];
        if (in_array('ext_tables.php', $files, true)) {
            $deprecated[] = [
                'file' => 'ext_tables.php',
                'changelog' => '#109438',
                'predicate' => 'The file is there and this package is not a system extension.',
                'cost' => 'Deprecated in v14.3. Loading it raises an E_USER_DEPRECATED, on an uncached request '
                    . 'and while the compiled ext_tables cache entry is written; a request served from that cache '
                    . 'raises nothing, so a functional suite with failOnDeprecation is usually what surfaces it. '
                    . 'From v15.0 nothing reads the file, and a backend module, a route or a user setting '
                    . 'registered there is lost without a report.',
            ];
        }
        if (in_array('ext_emconf.php', $files, true) && !$composerOnly) {
            $deprecated[] = [
                'file' => 'ext_emconf.php',
                'changelog' => '#108345',
                'predicate' => 'The file is there and composer.json declares neither '
                    . 'extra.typo3/cms.Package.providesPackages nor a version, in extra.typo3/cms.version or in '
                    . 'the top-level version field.',
                'cost' => 'Deprecated in v14.2, where the package manifest is read. A Composer installation is '
                    . 'unaffected: building the package artifact skips the fallback before it is reached, so what '
                    . 'raises this is classic mode and the functional test instances built like it. From v15.0 '
                    . 'there is no fallback and the installation throws InvalidPackageManifestException naming '
                    . 'the two fields.',
            ];
        }

        // getExtensionIcon() takes the first of six locations that is there and
        // reads Resources/Public/Icons/Extension.* before the root file, so an
        // extension shipping both never reaches the deprecated one and pays
        // nothing for leaving it behind.
        $icon = self::firstFile($path, ['ext_icon.svg', 'ext_icon.png', 'ext_icon.gif']);
        $outranks = self::firstFile($path, [
            'Resources/Public/Icons/Extension.svg',
            'Resources/Public/Icons/Extension.png',
            'Resources/Public/Icons/Extension.gif',
        ]);
        if ($icon !== null && $outranks === null) {
            $deprecated[] = [
                'file' => $icon,
                'changelog' => '#98093',
                'predicate' => 'The file is there and the extension ships no Resources/Public/Icons/Extension.svg, '
                    . '.png or .gif, which core reads first.',
                'cost' => 'Deprecated in v12.4. ExtensionManagementUtility::getExtensionIcon() falls back to the '
                    . 'file and raises an E_USER_DEPRECATED wherever the backend draws the extension icon: the '
                    . 'extension manager list, the new record wizard, the language pack list. From v13.0 nothing '
                    . 'reads it — Package::getPackageIcon() looks below Resources/Public/Icons/ alone and returns '
                    . 'null — so the extension is drawn without an icon and nothing is logged.',
            ];
        }

        foreach (['setup', 'constants'] as $kind) {
            $file = 'ext_typoscript_' . $kind . '.txt';
            if (!is_file($path . '/' . $file) || is_file($path . '/ext_typoscript_' . $kind . '.typoscript')) {
                continue;
            }
            $deprecated[] = [
                'file' => $file,
                'changelog' => '#96518',
                'predicate' => 'The file is there and no ext_typoscript_' . $kind . '.typoscript stands beside it.',
                'cost' => 'Dropped in v12.0, so no version this server covers reads it. Core builds an extension\'s '
                    . 'static TypoScript from ext_typoscript_setup.typoscript and '
                    . 'ext_typoscript_constants.typoscript alone, so what stands in this file reaches no template '
                    . 'and nothing reports it missing. Renaming it to .typoscript is the whole migration, and core '
                    . 'has read that name since v8.',
            ];
        }

        return $deprecated;
    }

    /**
     * The first of these files that is there, named relative to the extension.
     *
     * @param array<int, string> $candidates in the order they are preferred
     */
    private static function firstFile(string $path, array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (is_file($path . '/' . $candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * The registration files whose entries do not stand in their own text.
     *
     * Not a statement about the file — its content is read in full — but about
     * what reading it yields: a list assembled in a `foreach` exists only once
     * the file has run, and an empty list is omitted, so such a file arrives as
     * the same silence as one that does not exist. `ext_localconf.php` and
     * `ext_tables.php` stay out, for the reason `D-ANS-003` records.
     *
     * @return array<int, string>
     */
    private static function notReadStatically(string $path): array
    {
        $files = [
            'Configuration/Backend/Modules.php',
            'Configuration/Backend/Routes.php',
            'Configuration/Backend/AjaxRoutes.php',
            'Configuration/RequestMiddlewares.php',
        ];
        // Where the container answered, the icons are its and this file is no
        // longer what the answer rests on. The four above have no such source:
        // no registry hands them over, so reading them is all there ever is.
        if (self::answeredBy() === 'packages') {
            array_unshift($files, 'Configuration/Icons.php');
        }

        return array_values(array_filter(
            $files,
            static fn(string $file): bool => PhpArray::assembledAtRuntime($path . '/' . $file),
        ));
    }

    /**
     * The tables this extension defines, as the installation has them.
     *
     * A file below Configuration/TCA/ is named after the table it defines, so
     * the files answer on their own. What they cannot show is a table an
     * extension adds through a PHP call, and what they cannot check is whether
     * a file that looks like a definition produced one — so where the
     * installation booted, a table is in the answer when TCA has it and either
     * this extension's file declares it or its ctrl title names this extension.
     *
     * @return array<int, string>
     */
    private static function tcaTables(string $key, string $path): array
    {
        $declared = self::baseNames($path . '/Configuration/TCA', '*.php');
        $runtime = Typo3Runtime::topic('tables');
        if (!is_array($runtime) || $runtime === []) {
            return $declared;
        }

        $tables = [];
        foreach ($runtime as $table => $title) {
            if (Typo3Runtime::extensionIn((string) $title) === $key) {
                $tables[] = (string) $table;
            }
        }
        foreach ($declared as $table) {
            if (isset($runtime[$table])) {
                $tables[] = $table;
            }
        }

        return self::sorted($tables);
    }

    /**
     * The content elements this extension adds, as the installation has them.
     *
     * Attribution is the whole difficulty: `tt_content.CType` is one list for
     * every extension at once. An item names its owner twice over — its label
     * is `LLL:EXT:<key>/…` and its icon resolves to a file below `EXT:<key>/` —
     * and where neither names this extension the item is somebody else's.
     *
     * @param array<int, string> $parsed the identifiers read from this extension's own files
     * @return array<int, string>
     */
    private static function cTypes(string $key, array $parsed): array
    {
        $runtime = Typo3Runtime::topic('contentElements');
        if (!is_array($runtime) || $runtime === []) {
            return $parsed;
        }

        $icons = Typo3Runtime::topic('icons');
        $identifiers = [];
        foreach ($runtime as $identifier => $item) {
            $label = is_array($item) ? (string) ($item['label'] ?? '') : '';
            $icon = is_array($item) ? (string) ($item['icon'] ?? '') : '';
            $source = is_array($icons) ? (string) ($icons[$icon] ?? '') : '';
            if (Typo3Runtime::extensionIn($label) === $key || Typo3Runtime::extensionIn($source) === $key) {
                $identifiers[] = (string) $identifier;
            }
        }
        // One this extension's files register and the installation does not have
        // was registered under a condition that did not apply here, and saying
        // it is registered would send a caller to a template nothing renders.
        foreach ($parsed as $identifier) {
            if (isset($runtime[$identifier])) {
                $identifiers[] = $identifier;
            }
        }

        return self::sorted($identifiers);
    }

    /**
     * The icons this extension registers, as the installation has them.
     *
     * @return array<int, string>
     */
    private static function icons(string $key, string $path): array
    {
        $registered = Typo3Runtime::topic('icons');
        if (!is_array($registered) || $registered === []) {
            return PhpArray::keys($path . '/Configuration/Icons.php');
        }

        $icons = [];
        foreach ($registered as $identifier => $source) {
            if (Typo3Runtime::extensionIn((string) $source) === $key) {
                $icons[] = (string) $identifier;
            }
        }

        return self::sorted($icons);
    }

    /**
     * Where this answer comes from, in the vocabulary every tool reports it in.
     *
     * `installation` once the container answered for the registries that have
     * no file behind them; `packages` for the reading that has to leave those
     * out. `Typo3Runtime::reason()` says why, and the answer carries it.
     */
    public static function answeredBy(): string
    {
        return is_array(Typo3Runtime::topic('icons')) ? 'installation' : 'packages';
    }

    /**
     * @param array<int, string> $values
     * @return array<int, string>
     */
    private static function sorted(array $values): array
    {
        $values = array_values(array_unique($values));
        sort($values);

        return $values;
    }

    /**
     * The file names in a directory, without their extension by default: a TCA
     * file is named after its table, and the table is what is wanted.
     *
     * @return array<int, string>
     */
    private static function baseNames(string $directory, string $pattern, string $suffix = '.php'): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $names = [];
        foreach (Finder::create()->files()->in($directory)->depth(0)->name($pattern)->sortByName() as $file) {
            $names[] = $suffix === '' ? $file->getFilename() : $file->getBasename($suffix);
        }

        return $names;
    }

}
