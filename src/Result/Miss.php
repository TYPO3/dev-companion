<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Result;

/**
 * What a lookup that found nothing hands back, where more than one of them says
 * it the same way.
 *
 * `R-ANS-006`: a miss says what there would have been to find, and a caller can
 * ask for what it names outright. A caller who learns to read one miss reads
 * the next one without a second lesson. So the sentence stands once and each
 * corpus says the name of one of its items.
 */
final class Miss
{
    /** Subsets named outright, before the rest come as counts instead. */
    private const SHOWN = 4;

    /**
     * What each word reaches on its own, as a clause.
     *
     * @param array<int, array{term: string, matchCount: int}> $counts
     * @param string $singular What one item of this corpus is called.
     * @param string $plural
     */
    public static function reaching(array $counts, string $singular, string $plural): string
    {
        return implode(', ', array_map(static fn(array $term): string => sprintf(
            '"%s" reaches %d %s',
            $term['term'],
            $term['matchCount'],
            $term['matchCount'] === 1 ? $singular : $plural,
        ), $counts));
    }

    /**
     * The largest part of the query something still carries, as the next call.
     *
     * @param array<int, array{terms: array<int, string>, matchCount: int}> $subsets Narrowest first.
     * @param int $askedFor How many words the query had.
     * @param string $singular What one item of this corpus is called.
     * @param string $plural
     * @param string $where Where the subsets were counted, where that is not the whole corpus.
     */
    public static function largestReaching(
        array $subsets,
        int $askedFor,
        string $singular,
        string $plural,
        string $where = '',
    ): string {
        if ($subsets === []) {
            return '';
        }

        $shown = array_slice($subsets, 0, self::SHOWN);

        return sprintf(
            'No %s %scarries more than %d of the %d words: %s%s — ask again with the one that narrows best.',
            $singular,
            $where === '' ? '' : $where . ' ',
            count($subsets[0]['terms']),
            $askedFor,
            implode(', ', array_map(static fn(array $subset): string => sprintf(
                '"%s" reaches %d %s',
                implode(' ', $subset['terms']),
                $subset['matchCount'],
                $subset['matchCount'] === 1 ? $singular : $plural,
            ), $shown)),
            count($subsets) > count($shown) ? sprintf(', and %d more', count($subsets) - count($shown)) : '',
        );
    }
}
