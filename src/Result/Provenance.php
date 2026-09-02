<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Catalog\Meta as CatalogMeta;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * What a component answer says about where it came from.
 *
 * The component lookup and the catalog scope answer the same question from two
 * sides — one hands over entries, the other reports what the catalog is worth —
 * so the provenance, the version range per entry and what a target version
 * withheld are written once for both.
 */
final class Provenance
{
    /** Which of the two sources supplied the class and custom-property contract. */
    public static function sourceNote(bool $installed): string
    {
        if (!$installed) {
            return CatalogMeta::line();
        }

        return sprintf(
            'Component contract: installed TYPO3 %s packages. Names, summaries, keywords, and fallback markup '
                . 'come from the curated catalog; classes and custom properties come from '
                . 'EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces '
                . 'the fallback markup where available.',
            Instance::typo3Version(),
        );
    }

    /**
     * The evidence version travels inside the component, not only in the block
     * after it: clients often render one record without its surrounding answer.
     *
     * @param array<string, mixed> $component
     * @return array{
     *     classes: array<int, string>, sourceFiles: array<int, string>,
     *     markupSource: string, contractVersion: string, describesVersion: string
     * }
     */
    public static function sourceRecord(array $component): array
    {
        $snapshot = CatalogMeta::read()['source']['version'];
        $markupSource = $component['markupSource'] ?? 'catalog';
        $contractVersion = $component['contractVersion'] ?? $snapshot;

        return [
            'classes' => $component['classes'] ?? [],
            'sourceFiles' => $component['sourceFiles'] ?? [],
            'markupSource' => $markupSource,
            'contractVersion' => $contractVersion,
            'describesVersion' => $markupSource === 'installation' ? $contractVersion : $snapshot,
        ];
    }

    /**
     * The majors a catalog entry was verified on, as data beside the label.
     *
     * @param array<string, mixed> $component
     * @return array{since: ?int, until: ?int, verifiedOn: string}
     */
    public static function verifiedRecord(array $component): array
    {
        return [
            'since' => $component['since'],
            'until' => $component['until'],
            'verifiedOn' => Versions::label($component['since'], $component['until']),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $withheld
     * @return array<int, array<string, mixed>>
     */
    public static function withheldRecords(array $withheld): array
    {
        return array_map(static fn(array $c): array => [
            'name' => $c['name'],
            'title' => $c['title'],
            'sassPaths' => $c['sassPaths'],
            'demoPath' => $c['demoPath'],
        ] + self::verifiedRecord($c), $withheld);
    }

    /**
     * What the stated version cost the answer, and what to check instead.
     *
     * Dropping the entry silently would be the one thing worse than handing it
     * over: the caller then reads "this component does not exist" into an
     * answer that means "the catalog has it and was never verified where you
     * are". So it is named, with the branch and the sources to verify against.
     *
     * @param array<int, array<string, mixed>> $withheld
     */
    public static function withheldNote(array $withheld, ?int $target): string
    {
        if ($withheld === [] || $target === null) {
            return '';
        }

        if (($withheld[0]['_installed'] ?? false) === true) {
            $lines = [sprintf(
                'Not found in the installed TYPO3 v%d backend component contract:',
                $target,
            )];
            foreach ($withheld as $component) {
                $lines[] = sprintf('- %s (%s)', $component['name'], $component['title']);
            }
            $lines[] = 'Their root class or custom element was absent from the installed backend CSS and JavaScript.';

            return implode("\n", $lines);
        }

        $branch = Versions::branch($target);
        $lines = [sprintf(
            'Withheld for TYPO3 v%d — in this catalog, and never verified there, so the classes and custom '
            . 'properties they describe may not exist on that version:',
            $target,
        )];
        foreach ($withheld as $component) {
            $lines[] = sprintf(
                '- %s (%s) — verified on %s; verify against %s',
                $component['name'],
                $component['title'],
                Versions::label($component['since'], $component['until']),
                $component['sassPaths'] === []
                    ? ($component['demoPath'] ?? 'the core checkout')
                    : implode(', ', $component['sassPaths']),
            );
        }
        $lines[] = sprintf(
            'Check those paths against %s before using any of them — a path that is not there is the answer too.',
            $branch === null ? 'a core checkout of that version' : 'the core repository\'s ' . $branch . ' branch',
        );

        return implode("\n", $lines);
    }

    /**
     * The provenance every catalog answer carries, so a client can tell a miss
     * on an old snapshot from a miss on the branch it works on.
     *
     * @return array<string, string>
     */
    public static function catalogRecord(bool $componentsDerived = false): array
    {
        $meta = CatalogMeta::read();

        return [
            'repository' => $meta['source']['repository'],
            'branch' => $meta['source']['branch'],
            'version' => $meta['source']['version'],
            'commit' => $meta['source']['commit'],
            'verifiedAt' => $meta['verifiedAt'],
            // The one thing a miss sent the caller to typo3_snapshot_scope for.
            // It travels with the pin instead, so an answer that says "not in
            // this snapshot" says how to check the snapshot in the same breath.
            'verifyCommand' => $meta['verifyCommand'],
            // Both numbers were known and never contrasted. They travel
            // together now, in every answer that carries the pin at all.
            'installedVersion' => Instance::typo3Version(),
            'skew' => $componentsDerived || CatalogMeta::skew() === '' ? null : CatalogMeta::skew(),
        ];
    }
}
