<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Search;

/**
 * The most of a query a corpus still carries, as a query a caller can ask.
 *
 * What a caller needs when the intersection is empty and the per-term counts
 * cannot say it: which words have to go (`D-ANS-016`). It is one pass rather
 * than a subset lattice. A subset reaches an item exactly when that item
 * carries every word of it. So the largest subsets that reach anything are the
 * largest sets of words a single item carries. The matcher is the caller's,
 * because the answer is only true of the corpus behind it. A subset offered on
 * the wrong one names items the re-query does not return.
 */
final class Subsets
{
    /**
     * Every largest subset that reaches an item rather than the best of them.
     * On `form set yaml registration deprecated` there are two, and they reach
     * one entry each. The one a tie-break picks first is `Unify form setup YAML
     * loading` rather than the deprecation the caller looked for.
     *
     * @param array<int, string> $texts One searchable text per item of the corpus.
     * @param array<int, string> $terms
     * @param callable(string, string): bool $carries Whether a text carries a term.
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReaching(array $texts, array $terms, callable $carries): array
    {
        if (count($terms) < 2) {
            return [];
        }

        $reached = [];
        $largest = 0;
        foreach ($texts as $text) {
            $carried = array_values(array_filter(
                $terms,
                static fn(string $term): bool => $carries($text, $term),
            ));
            // A subset, so a query that hits is not answered with itself, and a
            // single word is what the per-term counts already say.
            if (count($carried) < 2 || count($carried) === count($terms)) {
                continue;
            }
            $largest = max($largest, count($carried));
            $key = implode(' ', $carried);
            $reached[$key] = ($reached[$key] ?? 0) + 1;
        }

        $subsets = [];
        foreach ($reached as $key => $matchCount) {
            $carried = explode(' ', (string) $key);
            if (count($carried) === $largest) {
                $subsets[] = ['terms' => $carried, 'matchCount' => $matchCount];
            }
        }
        usort($subsets, static fn(array $a, array $b): int => $a['matchCount'] <=> $b['matchCount']);

        return $subsets;
    }
}
