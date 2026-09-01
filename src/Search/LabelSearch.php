<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Search;

/**
 * Turns a label query into something the installation's console can answer.
 *
 * `language:domain:search --search=` is a literal substring match against one
 * label at a time, so a query of more than one word only ever matches where
 * that exact string occurs in one label — which is almost never, while the
 * tool asks for words. The words are therefore handed over as an alternation,
 * which brings back every label carrying at least one of them, and the
 * intersection is taken here: a label answers the query when it carries all of
 * them, in its text or in its trans-unit id, in any order.
 *
 * The alternation is one console call rather than one per word. A console call
 * boots TYPO3, and the union it returns is also what makes "save alone matches
 * 65 labels" answerable without asking again.
 */
final class LabelSearch
{
    /**
     * The words a query is made of.
     *
     * Case is not part of a word here: the trans-unit id of a label is
     * lowercase and its source text is not, and a caller typing "save" means
     * both.
     *
     * @return array<int, string>
     */
    public static function terms(string $query): array
    {
        $terms = preg_split('/\s+/', trim($query), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_map(
            static fn(string $term): string => mb_strtolower($term),
            $terms
        )));
    }

    /**
     * The terms as an answer names them back, so a miss says what was searched
     * for rather than repeating the query as one string.
     *
     * @param array<int, string> $terms
     */
    public static function quoted(array $terms): string
    {
        return implode(', ', array_map(static fn(string $term): string => '"' . $term . '"', $terms));
    }

    /**
     * The console option that returns every label carrying at least one term.
     *
     * Without terms this is the empty search the console reads as "everything",
     * because an empty alternation would be a regular expression that matches
     * every string by accident rather than by intent.
     *
     * @param array<int, string> $terms
     */
    public static function consoleOption(array $terms): string
    {
        if ($terms === []) {
            return '--search=';
        }

        $alternatives = implode('|', array_map(
            static fn(string $term): string => preg_quote($term, '/'),
            $terms
        ));

        return '--regex=/(' . $alternatives . ')/i';
    }

    /**
     * The items that carry every term, in any of the fields searched.
     *
     * No word boundary: a trans-unit id is `labels.save_document`, and an
     * underscore is a word character, so anchoring would drop exactly the ids a
     * caller searches by.
     *
     * An item may carry an `identifiers` field as well — the names its own text
     * writes, which a term reaches whole rather than as a substring. That is the
     * second way of carrying a term, and an item without the field has one.
     *
     * @param array<int, array<string, mixed>> $items
     * @param array<int, string> $terms
     * @return array<int, array<string, mixed>>
     */
    public static function carryingEvery(array $items, array $terms): array
    {
        if ($terms === []) {
            return array_values($items);
        }

        return array_values(array_filter($items, static function (array $item) use ($terms): bool {
            $haystack = self::haystack($item);
            $identifiers = mb_strtolower((string) ($item['identifiers'] ?? ''));
            foreach ($terms as $term) {
                if (!self::carries($haystack, $term) && !self::names($identifiers, $term)) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * The fields a search runs over, as one string.
     *
     * A label carries its trans-unit id and its source text. A changelog entry
     * carries its file name and gains the title inside the file where the names
     * alone reached nothing — `D-ANS-041`. What an entry's body names is not
     * here: an identifier is reached whole, by `names()`.
     *
     * @param array<string, mixed> $item
     */
    private static function haystack(array $item): string
    {
        return mb_strtolower(
            (string) ($item['key'] ?? '') . ' '
            . (string) ($item['source'] ?? '') . ' '
            . (string) ($item['title'] ?? ''),
        );
    }

    /**
     * Whether one of the identifiers an item names is the one the term asks for.
     *
     * Whole and never a substring: the index is what a body writes, and a term
     * contained in one of its names is how "image" would reach every entry
     * naming `imageCreateFromFile`. What is compared is the name the term ends
     * in, because a caller types the identifier at whichever specificity they
     * have it — bare, qualified by its class, or fully qualified — and the last
     * segment is the one the query is about.
     */
    private static function names(string $identifiers, string $term): bool
    {
        if ($identifiers === '') {
            return false;
        }

        $qualified = preg_replace('/\(.*$/', '', $term) ?? $term;
        $segments = preg_split('/\\\\|::|->/', $qualified, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return $segments !== [] && in_array(end($segments), explode(' ', $identifiers), true);
    }

    /**
     * Whether one haystack carries one term.
     *
     * Plain containment, and one addition for the term that spells an
     * identifier: `ext_tables.php`, `SC_OPTIONS`, `mod.web_layout`. Those are
     * written with their separators where they are used and taken apart where
     * they are titled — the changelog file name is `ExtTablesPhpInExtensions`
     * and its words are "ext tables php in extensions", so the caller's own
     * spelling of the thing reached neither. Compared without the separators,
     * the two are the same string.
     *
     * Only a term carrying one is compared that way, so nothing a query without
     * separators reaches changes, and nothing this ever matched stops matching.
     */
    private static function carries(string $haystack, string $term): bool
    {
        if (str_contains($haystack, $term)) {
            return true;
        }

        $identifier = self::withoutSeparators($term);

        return $identifier !== $term
            && $identifier !== ''
            && str_contains(self::withoutSeparators($haystack), $identifier);
    }

    /** The same string as one word: what separates an identifier is how it is written, not what it is. */
    private static function withoutSeparators(string $text): string
    {
        return (string) preg_replace('/[\s_.\-]+/u', '', $text);
    }

    /**
     * How many of the items each term reaches on its own.
     *
     * What a caller needs when the intersection is empty: the term that already
     * narrows enough is the one to ask with, and the term that reaches nothing
     * is the one that was misspelled or does not exist here.
     *
     * @param array<int, array<string, mixed>> $items
     * @param array<int, string> $terms
     * @return array<int, array{term: string, matchCount: int}>
     */
    public static function perTermCounts(array $items, array $terms): array
    {
        $counts = [];
        foreach ($terms as $term) {
            $counts[] = ['term' => $term, 'matchCount' => count(self::carryingEvery($items, [$term]))];
        }

        return $counts;
    }

    /**
     * The most of a query a label or a changelog entry still carries, as a
     * query that can be asked.
     *
     * The computation is `Subsets`; what is this corpus's own is the field pair
     * and the matching — the same `carries()` `carryingEvery()` and
     * `perTermCounts()` answer with, identifier spellings included (`D-ANS-016`).
     *
     * @param array<int, array<string, mixed>> $items
     * @param array<int, string> $terms
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReachingSubsets(array $items, array $terms): array
    {
        return Subsets::largestReaching(
            array_map(self::haystack(...), array_values($items)),
            $terms,
            self::carries(...),
        );
    }
}
