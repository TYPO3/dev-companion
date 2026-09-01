<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Knowledge\Catalog\Components;
use TYPO3\DevCompanion\Knowledge\Catalog\InstalledComponents;
use TYPO3\DevCompanion\Knowledge\Catalog\Meta as CatalogMeta;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Result\Provenance;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;

/**
 * What the component catalog is worth: where the contracts come from, which
 * core revision the fallback was taken from, and what a miss means.
 */
final class SnapshotScope extends ReadOnlyTool
{
    public static function name(): string
    {
        return 'typo3_snapshot_scope';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Packages, Source::Knowledge];
    }

    public static function description(): string
    {
        return 'Report whether component contracts come from the active installation or the bundled fallback, which TYPO3 core revision the fallback catalogs were taken from, what they cover, and how to re-check them. Call this to judge whether a typo3_component_lookup miss is authoritative: even with installed sources, component names remain a curated index rather than every backend class.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'targetVersion' => ['type' => 'string', 'description' => 'The TYPO3 version to report the catalog\'s coverage for, for example "13.4" or "14". Defaults to the version of the installation this server was started in.'],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'catalog' => Schema::catalogProvenance(),
            'verifyCommand' => Schema::string(),
            'scope' => Schema::object([], [], 'One entry per catalog describing what it contains.'),
            'counts' => Schema::object([], [], 'One entry per catalog with its number of entries.'),
            'targetVersion' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major the coverage was reported for — stated by the caller, or read from the installation. Null means the whole catalog answers.'],
            'verifiedCount' => Schema::integer('How many components were verified on that version.'),
            'componentSource' => ['type' => 'string', 'enum' => ['installation', 'catalog']],
            'withheld' => Schema::withheldComponents(),
        ], ['catalog', 'verifyCommand', 'scope', 'counts', 'verifiedCount', 'componentSource', 'withheld']);
    }

    public static function answer(array $args): ToolResult
    {
        $meta = CatalogMeta::read();
        $target = Versions::target(isset($args['targetVersion']) ? (string) $args['targetVersion'] : null);
        $installed = InstalledComponents::isAvailable($target);
        $split = Components::splitByTarget(Components::find(null, $target), $target);

        $lines = [
            $installed ? 'Installed component contract' : 'Catalog validity',
            $target === null
                ? 'No target TYPO3 version was stated and none was found to read, so the whole catalog answers and '
                    . 'every entry carries the versions it was verified on. Pass targetVersion to have the ones that '
                    . 'were not verified there withheld.'
                : ($installed
                    ? sprintf(
                        'For TYPO3 v%d, %d of the %d curated component entries were found in the installed backend '
                            . 'CSS or JavaScript. Their class and custom-property contracts were read from those packages.',
                        $target,
                        count($split['holds']),
                        count($split['holds']) + count($split['withheld']),
                    )
                    : sprintf(
                        'For TYPO3 v%d, %d of %d components were verified; entries that were not are withheld.',
                        $target,
                        count($split['holds']),
                        count($split['holds']) + count($split['withheld']),
                    )),
            $installed
                ? 'The bundled catalog remains the curated search index and markup fallback; it does not override installed classes.'
                : 'Each component entry owns this validity range. It does not inherit the version of the source checkout below.',
            '',
            'Bundled fallback source checkout',
            '- Source: ' . $meta['source']['repository'],
            '- Checkout branch: ' . $meta['source']['branch'] . ' (TYPO3 ' . $meta['source']['version'] . ')',
            '- Commit: ' . $meta['source']['commit'],
            '- Verified: ' . $meta['verifiedAt'],
            '- Re-check with: `' . $meta['verifyCommand'] . '`',
            '',
            'Scope',
        ];
        foreach ($meta['scope'] as $catalog => $scope) {
            $lines[] = '- ' . $catalog . ': ' . $scope;
        }

        $lines[] = '';
        $lines[] = 'Counts';
        foreach ($meta['counts'] as $name => $count) {
            $lines[] = '- ' . $name . ': ' . $count;
        }

        $lines[] = '';
        $lines[] = $installed
            ? 'A lookup miss means the component is not in the curated search index. The installed backend CSS may '
                . 'still contain an uncatalogued class, so inspect it before concluding the class does not exist.'
            : 'A lookup that finds nothing means the entry is not in this snapshot. On a different core '
                . 'branch — a 13.4 backport, for example — verify against the checkout before concluding that a '
                . 'component or class does not exist.';

        $withheldNote = Provenance::withheldNote($split['withheld'], $target);
        if ($withheldNote !== '') {
            $lines[] = '';
            $lines[] = $withheldNote;
        }

        if (!$installed && CatalogMeta::skew() !== '') {
            $lines[] = '';
            $lines[] = CatalogMeta::skew();
        }

        return ToolResult::create(implode("\n", $lines), [
            'catalog' => Provenance::catalogRecord($installed),
            'verifyCommand' => $meta['verifyCommand'],
            'scope' => $meta['scope'],
            'counts' => $meta['counts'],
            'targetVersion' => $target,
            'verifiedCount' => count($split['holds']),
            'componentSource' => $installed ? 'installation' : 'catalog',
            'withheld' => Provenance::withheldRecords($split['withheld']),
        ]);
    }
}
