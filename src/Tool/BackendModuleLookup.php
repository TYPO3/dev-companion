<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Installation\Icons;
use TYPO3\DevCompanion\Installation\Typo3Runtime;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unsupported;

/**
 * The backend modules the installation has registered.
 *
 * Read from the booted container rather than from `debug:backend:modules`,
 * whose CSV carries neither the navigation component a module resolves to nor
 * any route beyond the module's own path — and which the two maintained LTS
 * lines do not have at all, the command being TYPO3 v14 and up. `D-ANS-077`.
 */
final class BackendModuleLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_backend_module_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Installation];
    }

    public static function description(): string
    {
        return 'List the backend modules registered in the TYPO3 installation you are working in, with the extension that declares each one, its place in the module tree, its labels, its access level, the route each one answers on and every sub-route it registers. It carries the navigation component as the module tree resolves it, which is the value a Configuration/Backend/Modules.php cannot give you: it is inherited from the parent module, so reading the registration files says a module is not page-tree navigated when it is. A project extension\'s modules are in it, because the installation is booted and asked rather than a snapshot read.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Module identifier, label, route, navigation component, or extension name to filter by. Omit to list every module.'],
                'file' => ['type' => 'string', 'description' => 'A Configuration/Backend/Modules.php to check instead of listing the registry: which parent and which iconIdentifier it names that this installation does not have. Answered without a cache flush and without the file being saved into an installation, which is what the registry needs before it can say anything. The file is read as text and nothing in it is executed, so a value a variable or a constant computes is reported as unresolvable rather than followed. Not with query.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::installationAnswer([
            'query' => Schema::string(),
            'matchCount' => Schema::integer(),
            'answeredBy' => Schema::answeredBy(self::answersFrom()),
            'checked' => Schema::listOf(Schema::object([
                'identifier' => Schema::string('The module the file declares, as its key spells it.'),
                'parent' => Schema::string('The parent it names. Empty where it names none, which makes it a first-level module and registers no route unless it also declares standalone.'),
                'parentRegistered' => ['type' => ['boolean', 'null'], 'description' => 'Whether this installation has a module under that identifier. Null where the entry names no parent, and null where the value was not a plain string in the file.'],
                'iconIdentifier' => Schema::string('The icon it names. Empty where it names none.'),
                'iconRegistered' => ['type' => ['boolean', 'null'], 'description' => 'Whether that identifier is registered here. Null where the entry names no icon, and null where the value was not a plain string.'],
                'labels' => Schema::string('The translation domain or LLL reference it names, as written. Not resolved: a domain resolves once the module is registered, which is the state this call exists to precede.'),
            ], ['identifier', 'parent', 'parentRegistered', 'iconIdentifier', 'iconRegistered', 'labels']), 'One entry per module the named file declares, in the order it declares them. Empty where no file was named.'),
            'modules' => Schema::listOf(Schema::object([
                'identifier' => Schema::string(),
                'parents' => Schema::listOf(Schema::string(), 'The modules it sits under, outermost first.'),
                'extension' => Schema::string('The package that declares it.'),
                'labels' => Schema::string('Its label, with the translation domain reference behind it.'),
                'path' => Schema::string('The backend route it answers on.'),
                'position' => Schema::string('Its declared before/after position, if any.'),
                'navigationComponent' => Schema::string('The navigation component as resolved, inheritance included — "@typo3/backend/tree/page-tree-element" is the page tree. Empty where the module has none. The value differs between TYPO3 versions, which is why it is read from the installation.'),
                'access' => Schema::string('Who may call it: "user", "admin", "systemMaintainer".'),
                'routes' => Schema::listOf(Schema::object([
                    'name' => Schema::string('The name the registration gives it; "_default" is what the module opens with.'),
                    'identifier' => Schema::string('The route identifier it is registered under: the module identifier for "_default", "<module>.<name>" for every other one.'),
                    'path' => Schema::string(),
                    'target' => Schema::string('Controller::method it dispatches to.'),
                ], ['name', 'identifier', 'path', 'target']), 'Every route the module registers. Empty for a first-level module that is not standalone, which registers none.'),
            ], ['identifier', 'parents', 'extension', 'path', 'navigationComponent', 'routes'])),
        ], ['query', 'matchCount', 'answeredBy', 'modules', 'checked'], ['query']);
    }

    public static function answer(array $args): ToolResult
    {
        $query = mb_strtolower(trim((string) ($args['query'] ?? '')));
        $file = trim((string) ($args['file'] ?? ''));

        $topic = Typo3Runtime::topic('modules');
        if (!is_array($topic) || !is_array($topic['modules'] ?? null)) {
            $reason = Typo3Runtime::reason();
            if ($reason === '') {
                // The boot came up and this one topic did not, which the probe
                // says why of. Every other topic of the same reading answered.
                $reason = is_array($topic) && is_string($topic['unavailable'] ?? null)
                    ? 'the installation booted and its module registry could not be read: ' . $topic['unavailable']
                    : 'the installation booted and answered nothing about its backend modules';
            }

            return Unsupported::because($reason, ['query' => $query]);
        }

        if ($file !== '') {
            return self::check($file, $topic['modules']);
        }

        $modules = [];
        foreach ($topic['modules'] as $module) {
            if (!is_array($module)) {
                continue;
            }
            $module = self::shape($module);
            $haystack = mb_strtolower(implode(' ', array_merge(
                $module['parents'],
                array_column($module['routes'], 'identifier'),
                [
                    $module['identifier'],
                    $module['extension'],
                    $module['labels'],
                    $module['path'],
                    $module['navigationComponent'],
                ],
            )));
            if ($query !== '' && !str_contains($haystack, $query)) {
                continue;
            }
            $modules[] = $module;
        }

        if ($modules === []) {
            return ToolResult::create(
                sprintf('No backend module in this installation matches "%s".', $query),
                ['query' => $query, 'matchCount' => 0, 'modules' => [], 'checked' => [], 'answeredBy' => 'installation'],
            );
        }

        $lines = [sprintf('%d backend module(s)%s:', count($modules), $query === '' ? '' : ' matching "' . $query . '"')];
        foreach ($modules as $module) {
            $lines[] = '- ' . implode(' > ', array_merge($module['parents'], [$module['identifier']]));
            $lines[] = '  ' . $module['path'] . '  (' . $module['extension'] . ')';
            if ($module['labels'] !== '') {
                $lines[] = '  ' . $module['labels'];
            }
            if ($module['navigationComponent'] !== '') {
                $lines[] = '  navigation: ' . $module['navigationComponent'];
            }
            foreach ($module['routes'] as $route) {
                if ($route['name'] === '_default') {
                    continue;
                }
                $lines[] = '  route ' . $route['identifier'] . '  ' . $route['path'];
            }
        }
        $lines[] = '';
        $lines[] = 'A module is declared in its extension\'s Configuration/Backend/Modules.php; the label in '
            . 'brackets is a translation domain reference. The navigation component is the resolved one: a module '
            . 'inherits its parent\'s, so the registration file of a page-tree navigated module often names none.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => $query,
            'matchCount' => count($modules),
            'modules' => $modules,
            'checked' => [],
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * What a registration file names that this installation does not have.
     *
     * Five registration mistakes in one session were each caught by an
     * installation that had already been rebuilt, and the cycle was: edit the
     * file, flush the cache, ask the registry — `D-FBK-055`. This is the half
     * that needs neither, and it is the half whose three fields fail when a
     * user opens the module and never when the file is read.
     *
     * The file is read as text. Executing it would run the caller's own PHP in
     * this process, which nothing here does, so a value a constant or a
     * variable computes is reported as unread rather than followed.
     *
     * @param array<int, mixed> $registered
     */
    private static function check(string $file, array $registered): ToolResult
    {
        if (!is_file($file)) {
            return ToolResult::create(
                sprintf('No file at %s. Name the Configuration/Backend/Modules.php to check.', $file),
                ['query' => '', 'matchCount' => 0, 'modules' => [], 'checked' => [], 'answeredBy' => 'installation'],
            );
        }

        $identifiers = [];
        foreach ($registered as $module) {
            if (is_array($module) && is_string($module['identifier'] ?? null)) {
                $identifiers[$module['identifier']] = true;
            }
        }

        $checked = [];
        foreach (self::entries((string) file_get_contents($file)) as $identifier => $entry) {
            $parent = $entry['parent'] ?? null;
            $icon = $entry['iconIdentifier'] ?? null;
            $checked[] = [
                'identifier' => (string) $identifier,
                'parent' => (string) ($parent ?? ''),
                'parentRegistered' => $parent === null ? null : isset($identifiers[$parent]),
                'iconIdentifier' => (string) ($icon ?? ''),
                'iconRegistered' => $icon === null ? null : Icons::find($icon) !== null,
                'labels' => (string) ($entry['labels'] ?? ''),
            ];
        }

        $lines = [sprintf('%d module(s) declared in %s:', count($checked), $file)];
        foreach ($checked as $entry) {
            $lines[] = '- ' . $entry['identifier'];
            $lines[] = $entry['parentRegistered'] === null
                ? '  no parent — a first-level module registers no route unless it declares standalone'
                : sprintf(
                    '  parent %s — %s',
                    $entry['parent'],
                    $entry['parentRegistered'] ? 'registered here' : 'NOT registered here',
                );
            if ($entry['iconRegistered'] !== null) {
                $lines[] = sprintf(
                    '  icon %s — %s',
                    $entry['iconIdentifier'],
                    $entry['iconRegistered'] ? 'registered here' : 'NOT registered here',
                );
            }
            if ($entry['labels'] !== '') {
                $lines[] = '  labels ' . $entry['labels'] . ' — read, not resolved';
            }
        }
        $lines[] = '';
        $lines[] = 'Read as text: nothing in the file was executed, so a value a constant or a variable computes is '
            . 'not seen. A labels domain resolves once the module is registered, which is the state this call '
            . 'precedes, so it is reported as written.';

        return ToolResult::create(implode("\n", $lines), [
            'query' => '',
            'matchCount' => count($checked),
            'modules' => [],
            'checked' => $checked,
            'answeredBy' => 'installation',
        ]);
    }

    /**
     * The entries of a declarative registration file, by identifier.
     *
     * A top-level key opens at one indent and its fields sit at two, which is
     * what every one of these files is written in and what the parser here
     * stands on. A file written otherwise reads as no entries, which is an
     * answer rather than a wrong one.
     *
     * @return array<string, array<string, string>>
     */
    private static function entries(string $contents): array
    {
        preg_match_all(
            "/^    '([A-Za-z0-9_]+)' => \\[\r?\n(.*?)^    \\],/ms",
            $contents,
            $blocks,
            PREG_SET_ORDER,
        );

        $entries = [];
        foreach ($blocks as $block) {
            $fields = [];
            foreach (['parent', 'iconIdentifier', 'labels'] as $field) {
                if (preg_match("/'" . $field . "' => '([^']*)'/", $block[2], $value) === 1) {
                    $fields[$field] = $value[1];
                }
            }
            $entries[$block[1]] = $fields;
        }

        return $entries;
    }

    /**
     * One module of the topic, in the shape the schema declares.
     *
     * @param array<mixed> $module
     * @return array{identifier: string, parents: array<int, string>, extension: string, labels: string, path: string, position: string, navigationComponent: string, access: string, routes: array<int, array{name: string, identifier: string, path: string, target: string}>}
     */
    private static function shape(array $module): array
    {
        $routes = [];
        foreach (is_array($module['routes'] ?? null) ? $module['routes'] : [] as $route) {
            if (!is_array($route)) {
                continue;
            }
            $routes[] = [
                'name' => (string) ($route['name'] ?? ''),
                'identifier' => (string) ($route['identifier'] ?? ''),
                'path' => (string) ($route['path'] ?? ''),
                'target' => (string) ($route['target'] ?? ''),
            ];
        }

        return [
            'identifier' => (string) ($module['identifier'] ?? ''),
            'parents' => array_values(array_map(
                'strval',
                is_array($module['parents'] ?? null) ? $module['parents'] : [],
            )),
            'extension' => (string) ($module['extension'] ?? ''),
            'labels' => (string) ($module['labels'] ?? ''),
            'path' => (string) ($module['path'] ?? ''),
            'position' => (string) ($module['position'] ?? ''),
            'navigationComponent' => (string) ($module['navigationComponent'] ?? ''),
            'access' => (string) ($module['access'] ?? ''),
            'routes' => $routes,
        ];
    }
}
