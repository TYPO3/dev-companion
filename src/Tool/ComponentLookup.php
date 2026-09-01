<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Catalog\ComponentClasses;
use TYPO3\DevCompanion\Knowledge\Catalog\Components;
use TYPO3\DevCompanion\Knowledge\Catalog\CustomElements;
use TYPO3\DevCompanion\Knowledge\Catalog\InstalledComponents;
use TYPO3\DevCompanion\Knowledge\Catalog\Meta as CatalogMeta;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Provenance;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * TYPO3 backend UI components: their markup, classes and custom properties.
 */
final class ComponentLookup extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_component_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages, Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Look up TYPO3 backend UI components by name or topic. The searchable index is a curated subset of what the core itself files as a component: the Sass partials under Build/Sources/Sass/component/ and the custom elements under element/. A miss therefore means uncurated rather than outside the subject — the module chrome and other layout classes are candidates as much as badges and cards. Where the target is the active installation, its backend CSS, JavaScript, and installed styleguide templates supply the component contract; the curated catalog supplies the searchable names and fallback markup. Without usable installed sources, the bundled version-bound snapshot answers. Returns markup, classes, custom properties, and every source used. Which of those answered, and which core revision the bundled one was taken from, is typo3_snapshot_scope. A class the query names outright is answered even where the entry it belongs to was withheld for the target version — as a name and the versions it holds on, never as markup.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Component name, class, or topic, for example badge, card, search box, or input-group. Omit to list the catalog.'],
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version the markup has to hold for, for example "13.4" or "14". Components not verified there are withheld, and a class the query names is still answered where the class list alone was verified there. Defaults to the version of the installation this server was started in; where there is none, the whole catalog is returned and every entry carries the versions it was verified on.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'query' => Schema::nullableString(),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the answer was composed for — stated by the caller, or read from the installation. Null means nothing was withheld and every entry carries the versions it was verified on.'],
            'matchCount' => Schema::integer('How many components hold on the target version. Ones withheld for it are in withheld, not here.'),
            'components' => Schema::listOf(Schema::object([
                'name' => Schema::string(),
                'title' => Schema::string(),
                'summary' => Schema::string(),
                'rootClass' => Schema::string(),
                'variants' => Schema::listOf(Schema::string()),
                'wrapping' => Schema::listOf(Schema::string(), 'Classes the core stylesheet writes on the element around this component on the target version, taken out of the three lists below because none of those names a wrapper. Attaching one to the component itself changes nothing and fails nowhere.'),
                'modifiers' => Schema::listOf(Schema::string()),
                'subComponents' => Schema::listOf(Schema::string()),
                'customProperties' => Schema::listOf(Schema::string()),
                'markup' => Schema::string('Canonical markup of the component.'),
                'examples' => Schema::listOf(Schema::string()),
                'sassPath' => Schema::nullableString('Primary Sass source in the core checkout; null for a web component that carries its own styles.'),
                'sassPaths' => Schema::listOf(Schema::string(), 'Every Sass source the component spans. A component can be split across several files.'),
                'demoPath' => Schema::nullableString('Styleguide demo in the core checkout, if there is one.'),
                'matchedIn' => Schema::listOf(Schema::string(), 'Where the query matched: name, keywords, sub-component classes, description.'),
                'classes' => Schema::listOf(Schema::string(), 'Every class of this component found in the installed backend CSS. Empty for a bundled fallback or a custom element without external CSS.'),
                'sourceFiles' => Schema::listOf(Schema::string(), 'Installed package files consulted for the component contract. Empty for the bundled fallback.'),
                'markupSource' => ['type' => 'string', 'enum' => ['installation', 'catalog'], 'description' => 'Whether markup came from an installed styleguide example or the bundled curated fallback.'],
                'contractVersion' => Schema::string('TYPO3 version whose classes and custom properties this entry describes.'),
                'describesVersion' => Schema::string('TYPO3 version whose markup this entry describes. It can differ from contractVersion when the installed styleguide has no matching example and bundled markup is the fallback.'),
            ] + Schema::verifiedOn(), ['name', 'title', 'rootClass', 'sassPath', 'demoPath', 'classes', 'sourceFiles', 'markupSource', 'contractVersion', 'describesVersion', 'verifiedOn'])),
            'withheld' => Schema::withheldComponents(),
            'coveredClasses' => Schema::listOf(Schema::object([
                'class' => Schema::string('A class the query named outright.'),
                'component' => Schema::string('The withheld entry it belongs to.'),
                'title' => Schema::string(),
                'position' => ['type' => ['string', 'null'], 'enum' => ['around', 'on', 'below', null], 'description' => 'Where the class sits relative to the component root on this version, read off the core stylesheet: around wraps it, on is the root element itself, below is an element inside it. Null where no selector places it, which is not a licence to put it anywhere.'],
                'stylesWithin' => Schema::listOf(Schema::string(), 'What the core styles inside this class on this version: what it may hold, never what it requires.'),
                'sassPaths' => Schema::listOf(Schema::string(), 'Where the core writes it.'),
            ] + Schema::verifiedOn(), ['class', 'component', 'title', 'position', 'stylesWithin', 'sassPaths', 'verifiedOn']), 'Classes the query named that were verified on the target version although their entry was not, each with where it sits. No markup and no custom properties, because those are what withheld the entry.'),
            'elements' => Schema::listOf(Schema::object([
                'tag' => Schema::string('The custom element the query named.'),
                'source' => Schema::string('The TypeScript file that declares it in the core.'),
            ] + Schema::verifiedOn(), ['tag', 'source', 'verifiedOn']), 'Custom elements the query named that a styleguide demo writes on the target version. An element carries its own position, so where one exists it is the way in and a class is the way round it. Only what a demo writes is offered: the core declares many more and the rest are the backend\'s own.'),
            'checklist' => Schema::object([
                'title' => Schema::string(),
                'intro' => Schema::string(),
                'items' => Schema::listOf(Schema::string()),
            ], ['title', 'items']),
            'componentSource' => ['type' => 'string', 'enum' => ['installation', 'catalog'], 'description' => 'installation when the class and custom-property contract was read from the active TYPO3 packages; catalog when the bundled snapshot answered.'],
            'catalog' => Schema::catalogProvenance(),
        ], ['matchCount', 'components', 'withheld', 'coveredClasses', 'elements', 'componentSource', 'catalog']);
    }

    /**
     * What a withheld entry still answers about a class the query named.
     *
     * Written as its own block rather than beside the component, and carrying no
     * markup, so a caller cannot read a covered class as a covered component —
     * `D-CAT-006`. It does carry where the class sits, because a name on its own
     * is what let `table-fit` be attached to the table it belongs around —
     * `D-CAT-008`.
     *
     * @param array<int, array{
     *     class: string, component: string, title: string, position: ?string,
     *     stylesWithin: list<string>, sassPaths: array<int, string>,
     *     since: ?int, until: ?int, verifiedOn: string
     * }> $covered
     */
    private static function coveredClassNote(array $covered, int $target): string
    {
        $lines = [sprintf('Still answered for TYPO3 v%d, one class at a time:', $target)];
        foreach ($covered as $class) {
            $lines[] = sprintf(
                '- `%s` — a class of the %s component (%s), %s, verified on %s%s.',
                $class['class'],
                $class['component'],
                $class['title'],
                self::placed($class['position'], $class['component']),
                $class['verifiedOn'] === '' ? 'every version this catalog covers' : $class['verifiedOn'],
                $class['sassPaths'] === [] ? '' : ', in ' . implode(', ', $class['sassPaths']),
            );
            if ($class['stylesWithin'] !== []) {
                $lines[] = sprintf(
                    '  The core styles %s inside it, which is what it may hold and not what it needs.',
                    implode(', ', array_map(static fn(string $n): string => '`' . $n . '`', $class['stylesWithin'])),
                );
            }
        }
        $lines[] = sprintf(
            'No markup is handed over. Each entry is withheld above, so what surrounds these classes was never '
                . 'verified on TYPO3 v%d, and where a class is not placed the stylesheet says nothing about where '
                . 'it goes rather than saying it goes anywhere.',
            $target,
        );

        return implode("\n", $lines);
    }

    /**
     * The elements the query named, said as the way in they are.
     *
     * An element carries its own position and its own contract, so a caller who
     * has one does not need a class placed for them — which is why this stands
     * above the classes in the answer rather than beside them (`D-CAT-009`).
     *
     * @param list<array<string, mixed>> $elements
     */
    private static function elementNote(array $elements): string
    {
        $lines = ['The backend declares a custom element for this, which is the way in:'];
        foreach ($elements as $element) {
            $lines[] = sprintf(
                '- `<%s>` — %s, declared in %s.',
                $element['tag'],
                $element['verifiedOn'] === '' ? 'on every version this catalog covers' : $element['verifiedOn'],
                $element['source'],
            );
        }
        $lines[] = 'An element cannot be attached to the wrong node, so prefer it over composing the markup '
            . 'from classes. Only elements a styleguide demo writes are offered here; the backend declares '
            . 'many more and those are its own.';

        return implode("\n", $lines);
    }

    /**
     * The component's classes the core's stylesheet writes above it, which is
     * the one grouping a curated list gets wrong in a way that ships: a wrapper
     * attached to the element it should wrap changes nothing and fails nowhere.
     *
     * @param array<string, mixed> $component
     * @return list<string>
     */
    private static function wrapping(array $component, ?int $target): array
    {
        $wrapping = [];
        foreach (['variants', 'modifiers', 'subComponents'] as $field) {
            foreach ($component[$field] ?? [] as $class) {
                if (is_string($class) && ComponentClasses::position($class, $target) === 'around') {
                    $wrapping[] = $class;
                }
            }
        }

        return $wrapping;
    }

    /**
     * Where the class sits, in the words the derivation reads off the core's own
     * selectors. A class the stylesheet never writes beside its component is
     * left unplaced rather than guessed at.
     */
    private static function placed(?string $position, string $component): string
    {
        return match ($position) {
            'around' => sprintf('written on the element wrapping the %s', $component),
            'on' => sprintf('written on the %s element itself', $component),
            'below' => sprintf('written on an element inside the %s', $component),
            default => 'placed by no selector the core writes, so where it goes is not answered here',
        };
    }

    public static function answer(array $args): ToolResult
    {
        $query = isset($args['query']) ? (string) $args['query'] : null;
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $installed = InstalledComponents::isAvailable($target);
        $split = Components::splitByTarget(Components::find($query, $target), $target);
        $components = $split['holds'];
        $withheld = $split['withheld'];
        $withheldNote = Provenance::withheldNote($withheld, $target);
        $sourceNote = Provenance::sourceNote($installed);
        $covered = Components::coveredClasses($withheld, $query, $target);
        $elements = CustomElements::named($query, $target);
        $elementNote = $elements === [] ? '' : self::elementNote($elements);
        $coveredNote = $covered === [] ? '' : self::coveredClassNote($covered, (int) $target);

        if ($components === []) {
            return ToolResult::create(
                sprintf(
                    "%sNo TYPO3 component%s matched \"%s\". Try a component name (badge, card), a class (input-group), or a topic (search box). %s\n%s%s%s",
                    $elementNote === '' ? '' : $elementNote . "\n\n",
                    $withheld === [] ? '' : sprintf(' verified on TYPO3 v%d', (int) $target),
                    (string) $query,
                    $installed
                        ? 'The installed packages were checked, but the searchable component index remains curated; inspect the installed backend CSS for an uncatalogued class.'
                        : Provenance::MISS_NOTE,
                    $withheldNote === '' ? '' : $withheldNote . "\n",
                    $coveredNote === '' ? '' : "\n" . $coveredNote . "\n\n",
                    $sourceNote,
                ),
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => 0,
                    'components' => [],
                    'withheld' => Provenance::withheldRecords($withheld),
                    'coveredClasses' => $covered,
                    'elements' => $elements,
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => Provenance::catalogRecord($installed),
                ],
            );
        }

        // A specific query returns the best matches; an empty query lists names.
        if ($query === null || trim($query) === '') {
            $names = implode("\n", array_map(
                static fn(array $c): string => '- ' . $c['name'] . ' — ' . $c['title'],
                $components
            ));

            return ToolResult::create(
                "TYPO3 backend component catalog:\n" . $names
                    . ($withheldNote === '' ? '' : "\n\n" . $withheldNote)
                    . "\n\n" . $sourceNote,
                [
                    'query' => $query,
                    'targetVersion' => $target,
                    'matchCount' => count($components),
                    // The listing is an overview, so the entries stay lean; query a
                    // component by name for its markup and class contract.
                    'components' => array_map(static fn(array $c): array => [
                        'name' => $c['name'],
                        'title' => $c['title'],
                        'summary' => $c['summary'],
                        'rootClass' => $c['rootClass'],
                        'sassPath' => $c['sassPath'],
                        'sassPaths' => $c['sassPaths'],
                        'demoPath' => $c['demoPath'],
                    ] + Provenance::sourceRecord($c) + Provenance::verifiedRecord($c), $components),
                    'withheld' => Provenance::withheldRecords($withheld),
                    'coveredClasses' => $covered,
                    'elements' => $elements,
                    'componentSource' => $installed ? 'installation' : 'catalog',
                    'catalog' => Provenance::catalogRecord($installed),
                ],
            );
        }

        // Only the best matches are described in full; the rest stay in the count.
        $described = array_slice($components, 0, 3);

        $blocks = array_map(static function (array $c) use ($target): string {
            $lines = ['## ' . $c['title'] . ' (`' . $c['rootClass'] . '`)'];
            if (($c['matchedIn'] ?? []) !== []) {
                $lines[] = 'Matched in: ' . implode(', ', $c['matchedIn']);
                // A component reached only through a sub-component class or a
                // word in its description is a neighbour of what was asked
                // for, not an answer to it.
                if (array_intersect(['name', 'keywords'], $c['matchedIn']) === []) {
                    $lines[] = 'Related, not the component you asked for: it matched through '
                        . implode(' and ', $c['matchedIn']) . ' only.';
                }
            }
            if ($c['summary'] !== '') {
                $lines[] = $c['summary'];
            }

            $lines[] = '';
            $lines[] = 'Markup:';
            $lines[] = '```html';
            $lines[] = $c['markup'];
            $lines[] = '```';

            $appendList = static function (string $label, array $items) use (&$lines): void {
                if ($items !== []) {
                    $lines[] = $label . ': ' . implode(', ', $items);
                }
            };
            // A class the stylesheet writes above the component is not a
            // modifier of it, whichever list it was curated into: `table-fit`
            // sat beside `table-striped` and is the element around the table —
            // `D-CAT-008`. The lists keep everything the derivation has no
            // opinion on, because a curated grouping is worth more than the
            // silence that would replace it.
            $wrapping = self::wrapping($c, $target);
            $appendList('Wrapping the component', $wrapping);
            $keep = static fn(array $items): array => array_values(array_diff($items, $wrapping));
            $appendList('Variants', $keep($c['variants']));
            $appendList('Modifiers', $keep($c['modifiers']));
            $appendList('Sub-components', $keep($c['subComponents']));
            $appendList('Custom properties', $c['customProperties']);
            if (($c['_installed'] ?? false) === true) {
                $cataloguedClasses = array_merge(
                    [$c['rootClass']],
                    $c['variants'],
                    $c['modifiers'],
                    $c['subComponents'],
                );
                $appendList('Other installed classes', array_values(array_diff($c['classes'], $cataloguedClasses)));
            }

            $lines[] = $c['sassPaths'] === []
                ? 'Sass source: none — this is a web component and carries its styles in its element source.'
                : (($c['_installed'] ?? false) ? 'Curated Sass source path' : 'Sass source')
                    . (count($c['sassPaths']) > 1 ? 's' : '') . ': ' . implode(', ', $c['sassPaths']);
            $lines[] = 'Styleguide demo: ' . ($c['demoPath'] ?? 'none (not a styleguide component)');
            if (($c['_installed'] ?? false) === true) {
                $lines[] = 'Installed sources: ' . implode(', ', $c['sourceFiles']);
                $lines[] = $c['markupSource'] === 'installation'
                    ? 'Markup source: installed styleguide template.'
                    : 'Markup source: bundled TYPO3 ' . CatalogMeta::read()['source']['version']
                        . ' fallback; no matching example was available in the installed packages.';
            }
            $label = Versions::label($c['since'], $c['until']);
            if ($label !== '') {
                // Beside the markup rather than in the block at the end: a
                // client that renders one component shows the classes without
                // any statement of where they exist.
                $lines[] = 'Verified on: ' . $label . '.';
            }

            if ($c['examples'] !== []) {
                $lines[] = '';
                $lines[] = 'Examples:';
                foreach ($c['examples'] as $example) {
                    $lines[] = '```html';
                    $lines[] = $example;
                    $lines[] = '```';
                }
            }

            return implode("\n", $lines);
        }, $described);

        if ($withheldNote !== '') {
            $blocks[] = $withheldNote;
        }
        if ($coveredNote !== '') {
            $blocks[] = $coveredNote;
        }

        $checklist = Components::checklist();
        $checklistLines = ['## ' . $checklist['title']];
        if ($checklist['intro'] !== '') {
            $checklistLines[] = $checklist['intro'];
        }
        foreach ($checklist['items'] as $item) {
            $checklistLines[] = '- [ ] ' . $item;
        }
        $blocks[] = implode("\n", $checklistLines);
        if ($target !== null) {
            $blocks[] = $installed
                ? sprintf(
                    'Answered from installed TYPO3 v%d package evidence; an indexed component absent there is withheld.',
                    $target,
                )
                : sprintf(
                    'Answered for TYPO3 v%d: every component above was verified there, and one that was not is '
                        . 'withheld instead. The snapshot below says which revision the catalog was read from, not '
                        . 'which versions an entry holds on — each entry states that itself.',
                    $target,
                );
        }
        if ($elementNote !== '') {
            $blocks[] = $elementNote;
        }
        $blocks[] = $sourceNote;

        return ToolResult::create(implode("\n\n", $blocks), [
            'query' => $query,
            'targetVersion' => $target,
            'matchCount' => count($components),
            'components' => array_map(static function (array $c) use ($target): array {
                $wrapping = self::wrapping($c, $target);
                $keep = static fn(array $items): array => array_values(array_diff($items, $wrapping));

                return [
                    'name' => $c['name'],
                    'title' => $c['title'],
                    'summary' => $c['summary'],
                    'rootClass' => $c['rootClass'],
                    'wrapping' => $wrapping,
                    'variants' => $keep($c['variants']),
                    'modifiers' => $keep($c['modifiers']),
                    'subComponents' => $keep($c['subComponents']),
                    'customProperties' => $c['customProperties'],
                    'markup' => $c['markup'],
                    'examples' => $c['examples'],
                    'sassPath' => $c['sassPath'],
                    'sassPaths' => $c['sassPaths'],
                    'demoPath' => $c['demoPath'],
                    'matchedIn' => $c['matchedIn'] ?? [],
                ] + Provenance::sourceRecord($c) + Provenance::verifiedRecord($c);
            }, $described),
            'withheld' => Provenance::withheldRecords($withheld),
            'coveredClasses' => $covered,
            'elements' => $elements,
            'checklist' => $checklist,
            'componentSource' => $installed ? 'installation' : 'catalog',
            'catalog' => Provenance::catalogRecord($installed),
        ]);
    }
}
