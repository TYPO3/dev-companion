<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

use TYPO3\DevCompanion\Knowledge\Catalog\References;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\Versions;

/**
 * Matched hints, as an answer.
 *
 * The hint lookup returns them as the answer and the task guide carries them
 * inside a larger one. So the range beside a statement, the notice above a
 * core-scoped hint and the worked example under it stand once.
 */
final class MatchedHints
{
    /**
     * Matched hints as data, without the internal match patterns.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array<string, mixed>>
     */
    public static function records(array $hints): array
    {
        return array_map(static fn(array $hint): array => [
            'id' => (string) $hint['id'],
            'title' => (string) $hint['title'],
            'category' => (string) $hint['category'],
            'scope' => ($hint['scope'] ?? null)?->value,
            'hints' => array_map(static fn(array $statement): array => [
                'text' => $statement['text'],
                'since' => $statement['since'],
                'until' => $statement['until'],
                'versions' => Versions::label($statement['since'], $statement['until']),
                'scope' => ($statement['scope'] ?? null)?->value,
            ], $hint['hints']),
        ], array_values($hints));
    }

    /**
     * One statement as a line, with the versions it holds for where that is not
     * all of them.
     *
     * The range renders beside the sentence rather than inside it. The sentence
     * is the same sentence on every version it holds for. A reader who filters
     * by version must not have to parse prose for it. Whose obligation it is
     * renders the same way, and only where it is not this caller's. Inside the
     * core everything listed applies, so the marker would be on every line and
     * say nothing.
     *
     * @param array{text: string, since: ?int, until: ?int, scope: ?Scope} $statement
     */
    public static function statement(array $statement, Scope $of = Scope::Uncertain): string
    {
        $labels = array_filter([
            Versions::label($statement['since'], $statement['until']),
            self::obligation($statement['scope'] ?? null, $of),
        ]);

        return $labels === [] ? $statement['text'] : $statement['text'] . ' [' . implode('; ', $labels) . ']';
    }

    /**
     * What one statement obliges, where that is not this caller.
     *
     * Two directions and one rule. A statement declares whose it is, and it
     * gets a label where the answer is for somebody else. Inside its own scope
     * the label would be on every line and say nothing. Where nothing placed
     * the work there is nobody to contrast it with.
     */
    private static function obligation(?Scope $declared, Scope $of): string
    {
        if ($declared === null || $of === Scope::Uncertain) {
            return '';
        }
        if ($declared === Scope::Core && $of->isOutsideTheCore()) {
            return 'binding for a core patch, a convention here';
        }
        if ($declared->isOutsideTheCore() && $of === Scope::Core) {
            return 'binding outside the core, context here';
        }

        return '';
    }

    /**
     * What a whole hint obliges, where that is not this caller.
     *
     * The backend's design system is the case this exists for. Every rule in it
     * is a condition of a core patch and none of it is a condition of anything
     * in a project. That does not make it useless there, because a project that
     * builds a backend module wants exactly those rules. So the answer keeps
     * them and says which of the two it hands over.
     *
     * @param array<string, mixed> $hint
     */
    public static function scopeNotice(array $hint, Scope $of): ?string
    {
        $declared = $hint['scope'] ?? null;
        if ($declared === null || $of === Scope::Uncertain) {
            return null;
        }
        if ($declared === Scope::Core && $of->isOutsideTheCore()) {
            return 'Binding for a patch to the TYPO3 core. Here they are conventions you may adopt — worth having '
                . 'where this repository builds the same thing, and no condition of anything in it.';
        }
        if ($declared->isOutsideTheCore() && $of === Scope::Core) {
            return 'Binding for work outside the TYPO3 core — a project repository or a distributed extension. '
                . 'In the core it is context for what such a repository has to do, and no condition of a patch.';
        }

        return null;
    }

    /**
     * The core's own worked example per hint id, as one line for the answer.
     *
     * A hint is a summary of something that exists in full and green. Its name
     * beside the summary is what makes "read it" available at the moment the
     * summary turns out thin. Not in a document read once.
     *
     * @return array<string, string>
     */
    public static function examples(?int $target): array
    {
        $lines = [];
        foreach (References::on($target) as $entry) {
            if ($entry['hint'] !== null) {
                $lines[$entry['hint']] = 'Worked example: ' . $entry['path']
                    . ' — typo3_reference_list for what it demonstrates and where an installation has it.';
            }
        }

        return $lines;
    }

    /**
     * The matched hints as text: one section per category, one block per hint.
     *
     * `$of` is the scope of the paths this block matched for. So a statement
     * that declares whose it is can get a label where the answer is for
     * somebody else.
     *
     * @param array<int, array<string, mixed>> $hints
     */
    public static function sections(array $hints, Scope $of, ?int $target): string
    {
        $examples = self::examples($target);
        $sectionTexts = [];
        foreach (Hints::groupByCategory($hints) as $section) {
            $hintTexts = [];
            foreach ($section['hints'] as $hint) {
                $block = ['## ' . $hint['title']];
                $notice = self::scopeNotice($hint, $of);
                if ($notice !== null) {
                    $block[] = $notice;
                }
                $block[] = 'Hints:';
                foreach ($hint['hints'] as $entry) {
                    $block[] = '- ' . self::statement($entry, $of);
                }
                if (isset($examples[$hint['id']])) {
                    $block[] = $examples[$hint['id']];
                }
                $hintTexts[] = implode("\n", $block);
            }
            $sectionTexts[] = '### ' . $section['category'] . "\n\n" . implode("\n\n", $hintTexts);
        }

        return implode("\n\n", $sectionTexts);
    }

    /**
     * What the groups of one call found, as the one answer the payload is.
     *
     * @param array<int, array{scope: Scope, paths: array<int, string>, result: array<string, mixed>}> $found
     * @return array{matchedHints: array<int, array<string, mixed>>, availableHints: array<int, array<string, mixed>>, availableHintsWithheld: int, domains: array<int, string>, withheldCategories: array<int, string>}
     */
    public static function merged(array $found): array
    {
        $matched = [];
        $available = [];
        $domains = [];
        $withheld = [];
        $withheldIndex = 0;
        foreach ($found as $group) {
            $withheldIndex = max($withheldIndex, (int) ($group['result']['availableHintsWithheld'] ?? 0));
            foreach ($group['result']['matchedHints'] as $hint) {
                $matched[$hint['id']] ??= $hint;
            }
            foreach ($group['result']['availableHints'] as $entry) {
                $available[$entry['id']] ??= $entry;
            }
            $domains = array_merge($domains, $group['result']['domains']);
            $withheld = array_merge($withheld, $group['result']['withheldCategories']);
        }

        return [
            'matchedHints' => array_values($matched),
            // Subtracted across the groups as well as inside each. A hint the
            // core path matched is in the answer, and the extension path's
            // index would otherwise offer it as something still to ask for.
            'availableHints' => array_values(array_diff_key($available, $matched)),
            // The largest of the groups rather than their sum. An id call holds
            // back the same neighbours in every group it runs in, so a sum
            // would count one list several times over.
            'availableHintsWithheld' => $withheldIndex,
            'domains' => array_values(array_unique($domains)),
            'withheldCategories' => array_values(array_unique($withheld)),
        ];
    }
}
