<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Search;

/**
 * Small text helpers shared by the matchers.
 */
final class Text
{
    /**
     * Endings a word may gain and still be the word a needle names.
     *
     * These are the forms of one word — its plural, its tense, the noun of the
     * act — and not its derivatives. The line is where the measurement put it
     * rather than where grammar would. Over the 169 prompts, contract cases and
     * hint titles this repository has to hand, five endings account for 89
     * matches past a needle's end. Those are `-s`, `-es`, `-e`, `-ed` and
     * `-ing`. Not one of them is a different word. `-able`, `-er` and `-ers`
     * account for four, of which three are "maintainable" and "maintainers"
     * read as maintenance work. `-ion` and `-ions` are in for `deprecat`, the
     * one needle in the whole curated vocabulary that is less than a word. Its
     * noun is the name of the intent it selects.
     *
     * @var array<int, string>
     */
    private const INFLECTIONS = ['s', 'es', 'e', 'd', 'ed', 'ing', 'ion', 'ions'];

    /**
     * Whether $haystack carries $needle as the word it is.
     *
     * A plain substring match is hard to police: "preview" contains "review",
     * "success" contains "css". The word boundary keeps the prefix match
     * ("label" finds "labels") without them. A hyphen joins two words as
     * readily as a space does (`D-ANS-022`). A separator inside one word
     * matches as written (`D-ANS-006`). The match ends where the needle's word
     * does, give or take an inflection (`D-ANS-050`), where a stem takes
     * startsWord().
     */
    public static function containsWord(string $haystack, string $needle): bool
    {
        return self::matches($haystack, $needle, self::ending($needle));
    }

    /**
     * Whether $haystack carries $needle at the start of a word.
     *
     * The same rule with its right side open, for a needle that is not a word.
     * `TermSearch::stem()` cuts a query word to six characters, so the search
     * reads "testimonials" as "testim" and "deprecated" as "deprec". A rule
     * that closed those on the right would search the corpus for a word nobody
     * writes. Which of the two a caller wants is a property of the needle it
     * holds, and only the caller knows it.
     */
    public static function startsWord(string $haystack, string $needle): bool
    {
        return self::matches($haystack, $needle, '');
    }

    private static function matches(string $haystack, string $needle, string $ending): bool
    {
        if ($needle === '') {
            return false;
        }

        $words = array_map(
            static fn(string $word): string => preg_quote($word, '/'),
            preg_split('/ +/', $needle) ?: [],
        );

        return preg_match('/\b' . implode('[ -]+', $words) . $ending . '/i', $haystack) === 1;
    }

    /**
     * What a match may run into past the needle, and where it has to stop.
     *
     * Only a needle that ends in a letter can run into the next word at all.
     * `f:` and `typo3/sysext/` already end where their word does, so their
     * right side stays as it was. A letter closes it rather than a word
     * character. A needle that runs into an underscore is inside one identifier
     * rather than in the next word. `sys_file` that reaches
     * `sys_file_reference` is `D-ANS-006`'s side of the same question.
     */
    private static function ending(string $needle): string
    {
        if (preg_match('/\p{L}$/u', $needle) !== 1) {
            return '';
        }

        return '(?:' . implode('|', self::INFLECTIONS) . ')?(?!\p{L})';
    }
}
