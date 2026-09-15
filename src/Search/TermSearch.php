<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Search;

/**
 * Scores free-text queries against a corpus of field-addressed documents.
 *
 * Two corpora take this search, the prose sections and the hints. They used to
 * score differently, which is why the same question reached one of them and not
 * the other. What they share is the whole method. A term is worth what it
 * separates one document from the rest. It matches at a word boundary rather
 * than as a substring, and where in the document it appears decides how much it
 * counts. What they do not share is the field layout, so that is the parameter.
 *
 * A document is `['field name' => 'text', ...]`; the caller says what each
 * field is worth.
 */
final class TermSearch
{
    /**
     * Words that carry no topic signal.
     *
     * The two-letter ones are here because MIN_LENGTH is two: until it was, the
     * floor did this list's work for every word that short. "if" is not one of
     * them on purpose, and it is the reason the floor moved. It names a
     * ViewHelper and a TypoScript function, so it is the word a caller who asks
     * about `f:if` has left. A word in this list is still a term where a query
     * writes it behind a namespace prefix — `D-ANS-047`.
     */
    private const STOPWORDS = [
        'am', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'can', 'do',
        'does', 'for', 'from', 'go', 'he', 'how', 'in', 'is', 'it', 'its',
        'me', 'my', 'no', 'not', 'of', 'on', 'or', 'so', 'the', 'their',
        'them', 'then', 'there', 'these', 'this', 'to', 'up', 'us', 'was',
        'we', 'what', 'when', 'where', 'which', 'why', 'will', 'with', 'you',
        'your', 'typo3', 'core',
    ];

    /**
     * Longest term still matched as a whole word rather than as a prefix.
     *
     * A prefix match is how a stem finds every form of its word — "label" finds
     * "labels". At three characters there is no word form left to find. The
     * prefix matches whatever happens to start with those letters, so the
     * tolerance turns into noise. Measured over the hint corpus: "fal", the
     * File Abstraction Layer, prefix-matched seven hints through "fallback" and
     * "false" and the right one once. And a term weighs by how few documents
     * carry it. So an accident that lands in exactly one document becomes the
     * strongest term in the query. "ist", which occurs in no hint as a word at
     * all, outweighed everything in "die Beschriftung im Frontend ist falsch"
     * and decided the answer.
     */
    private const PREFIX_FROM_LENGTH = 4;

    /**
     * Shortest word of a query the search reads at all.
     *
     * It is two rather than three because of the line above. A word this short
     * matches as a whole word, so none of what makes a short prefix noisy
     * applies to it. "if" is the case that showed the floor stood against the
     * wrong risk. `Global/If.html` of the ViewHelper reference has the title
     * "if". No query that names `f:if` reached it while the floor dropped every
     * word under three characters.
     *
     * One character is where it stops. A single letter is a whole word in the
     * corpus as readily as in the query. The `f` of `f:if` matches the `f` of
     * every other ViewHelper written out. So it separates nothing, and whatever
     * happens to spell it out carries it.
     */
    private const MIN_LENGTH = 2;

    /**
     * The meaningful terms of a query, reduced to a stem so that word forms of
     * the same word are one term. "deprecate", "deprecated" and "deprecations"
     * all become "deprec" and match the "Deprecations" section.
     *
     * @return array<int, string>
     */
    public static function terms(string $query): array
    {
        return array_values(array_unique(array_map(self::stem(...), self::meaningful($query))));
    }

    /**
     * The caller's own word behind each term, so an answer that hands a query
     * back is one they recognise. "commit message line" rather than "commit
     * messag line". The stem re-queries to the same term, so the answer still
     * names exactly what the search measured.
     *
     * @return array<string, string> The first word of the query that produced each term.
     */
    public static function words(string $query): array
    {
        $words = [];
        foreach (self::meaningful($query) as $word) {
            $words[self::stem($word)] ??= $word;
        }

        return $words;
    }

    /**
     * The words of a query the search reads at all, as the caller wrote them.
     *
     * A word written behind a namespace prefix is never a stopword, because it
     * is not prose there. The `or` of `f:or` is the name of a ViewHelper and
     * the `core` of `EXT:core` is an extension key. `f:or` and `f:then` had no
     * term left at all — `D-ANS-047`. MIN_LENGTH drops the prefix itself, which
     * is every namespace the corpora carry.
     *
     * @return array<int, string>
     */
    private static function meaningful(string $query): array
    {
        $query = mb_strtolower(trim($query));
        $words = [];
        foreach (preg_split('/[^\p{L}\p{N}_.-]+/u', $query, -1, PREG_SPLIT_OFFSET_CAPTURE) ?: [] as [$word, $offset]) {
            // The colon has to touch both sides, which is what separates a
            // qualified name from the colon of a sentence.
            $qualified = $offset > 0 && $query[$offset - 1] === ':';
            $word = trim($word, '.-');
            if ($word === '' || strlen($word) < self::MIN_LENGTH) {
                continue;
            }
            if (!$qualified && in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            $words[] = $word;
        }

        return $words;
    }

    /**
     * How much each term separates one document of the corpus from the rest.
     *
     * "content", "structure" and "element" are in half the knowledge base and
     * say almost nothing about which document answers the question; "tsconfig"
     * says nearly everything. An equal weight is what let the backend's Sass
     * class names answer a query about site sets. That was at a confident three
     * quarters of the query terms.
     *
     * @param array<int, string> $terms
     * @param array<int, array<string, string>> $documents
     * @return array<string, float>
     */
    public static function weights(array $terms, array $documents): array
    {
        $total = count($documents);
        $weights = [];
        foreach ($terms as $term) {
            $carrying = 0;
            foreach ($documents as $document) {
                foreach ($document as $text) {
                    if (self::carries($text, $term)) {
                        ++$carrying;
                        break;
                    }
                }
            }
            if ($total === 0) {
                $weights[$term] = 0.0;
                continue;
            }

            // A term nothing carries counts as if the square root of the corpus
            // held it. That is halfway between the rarest term there is and no
            // term at all. Nothing can cover it, so what it does is lower the
            // coverage of everything else, and the fraction decides which way
            // that cuts. A weight of nothing let "how do I write a good sonnet"
            // decay into a query about "write" and "good". Something always
            // answers that. A full weight let one unknown word sink a query the
            // corpus does answer. Nobody wrote "upload", and the storage hint
            // was no longer the answer to "file upload storage configuration".
            $weights[$term] = $carrying === 0
                ? log($total) / 2
                : log($total / $carrying);
        }

        return $weights;
    }

    /**
     * Returns [score, weight covered, the field that carried each term]. A term
     * counts by what it says, by the weight of the field that carries it, and
     * against how much other text stands around it. The strongest field wins,
     * so a term in both the title and the body counts once.
     *
     * That strongest field is the third number's whole content. A caller that
     * learns what matched a result learns which words of the query reached the
     * document and where. The terms it does not carry are the ones absent from
     * it. To work that out anywhere else means to decide the same tie a second
     * time.
     *
     * The covered weight is the second number because it answers a different
     * question than the score. The score ranks, and the coverage says whether
     * the document is about the query at all. Which field carried the term is a
     * rank signal and on purpose not an aboutness one. A rare word anywhere in
     * a document is evidence that it is about it. A demotion of that to a
     * quarter of a title hit dropped "my extension service is not found at
     * runtime" below the floor. Its own hint said exactly that.
     *
     * @param array<string, string> $document
     * @param array<string, float> $weights
     * @param array<string, int> $fieldWeights
     * @param int $undilutedWords Field length that still counts for what the
     *                            term says; see the callers for why a corpus
     *                            has the value it has.
     * @return array{0: int, 1: float, 2: array<string, string>}
     */
    public static function score(array $document, array $weights, array $fieldWeights, int $undilutedWords): array
    {
        $score = 0.0;
        $covered = 0.0;
        $matched = [];
        foreach ($weights as $term => $weight) {
            $best = 0.0;
            $dilution = 1.0;
            $strongest = null;
            foreach ($fieldWeights as $field => $fieldWeight) {
                $text = $document[$field] ?? '';
                if (!self::carries($text, (string) $term)) {
                    continue;
                }
                $diluted = self::dilution($text, $undilutedWords);
                if ($fieldWeight / $diluted >= $best) {
                    $best = $fieldWeight / $diluted;
                    $dilution = $diluted;
                    $strongest = $field;
                }
            }
            if ($best <= 0.0 || $strongest === null) {
                continue;
            }
            $covered += $weight / $dilution;
            $score += $weight * $best;
            $matched[(string) $term] = $strongest;
        }

        return [(int) round($score * 10), $covered, $matched];
    }

    /**
     * Whether the text carries the term: as a word prefix from
     * PREFIX_FROM_LENGTH characters up, as a whole word below it.
     *
     * A term is what stem() left of a query word, so the prefix is the whole
     * point of it. The search reads "deprecated" as "deprec" and reaches the
     * "Deprecations" section because the match runs past its own end. That is
     * why this is `Text::startsWord()` and carriesWord() beside it is not.
     */
    public static function carries(string $text, string $term): bool
    {
        if (strlen($term) >= self::PREFIX_FROM_LENGTH) {
            return Text::startsWord($text, $term);
        }

        return preg_match('/\b' . preg_quote($term, '/') . '\b/i', $text) === 1;
    }

    /**
     * The same question asked of a curated pattern, which is a word.
     *
     * The vocabulary needs the length floor for the same reason a query does:
     * `fal`, the File Abstraction Layer, prefix-matched seven hints through
     * "fallback" and "false". It needs the other half of the rule for the
     * opposite reason. Nobody truncated these, so a pattern that runs past its
     * own end matches a word it only starts. `D-ANS-050`.
     */
    public static function carriesWord(string $text, string $word): bool
    {
        if (strlen($word) >= self::PREFIX_FROM_LENGTH) {
            return Text::containsWord($text, $word);
        }

        return preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text) === 1;
    }

    /**
     * How much longer than the corpus's ordinary field this one is, on a log
     * scale; never below 1, so a short field is not sharpened.
     *
     * A term says the same thing wherever it appears. A find among a thousand
     * other words is weaker evidence than a find among fifty, because a long
     * enough text contains anything.
     */
    private static function dilution(string $text, int $undilutedWords): float
    {
        $words = max(1, str_word_count($text));

        return $words <= $undilutedWords ? 1.0 : log($words / $undilutedWords) + 1.0;
    }

    /**
     * Cuts a plural suffix and shortens long words, so the stem that remains is
     * a substring of every form of that word. Words are only shortened while at
     * least four characters remain, so short words like "css" stay intact.
     */
    private static function stem(string $word): string
    {
        if (strlen($word) >= 6 && str_ends_with($word, 'ies')) {
            $word = substr($word, 0, -3);
        } elseif (strlen($word) >= 6 && str_ends_with($word, 'es')) {
            $word = substr($word, 0, -2);
        } elseif (strlen($word) >= 5 && str_ends_with($word, 's')) {
            $word = substr($word, 0, -1);
        }

        return strlen($word) > 6 ? substr($word, 0, 6) : $word;
    }
}
