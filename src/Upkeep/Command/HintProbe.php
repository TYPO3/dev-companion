<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Search\TermSearch;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What one query reaches in the hint corpus, and why.
 *
 * `components:check` exists because a core update invalidates an entry
 * silently. A hint decays the same way and even more quietly: nothing about it
 * changes, the query nobody phrased right simply comes back empty, and the
 * caller reads that as "this server does not know" rather than as "I said it
 * differently". This is one of the two readings that make it loud.
 */
#[AsCommand(
    name: 'hints:probe',
    description: 'what that query reaches, in order, and which way in earned each hit',
)]
final class HintProbe
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the query to read the corpus back through')]
        string $query,
    ): int {
        $result = Hints::find([], $query, 10);

        Voice::row($output, sprintf('%s %s', Voice::key('Query', 8), $query));
        Voice::row($output, sprintf('%s %s', Voice::key('Domains', 8), implode(', ', $result['domains']) ?: '(none)'));
        if ($result['withheldCategories'] !== []) {
            Voice::row($output, sprintf('%s %s — the query reads as frontend work', Voice::key('Withheld', 8), implode(', ', $result['withheldCategories'])));
        }

        if ($result['matchedHints'] === []) {
            // Not a failure of this command, and not necessarily one of the
            // matcher: a miss is a legitimate answer, and what makes it one is that
            // the caller is told what there would have been to find.
            Voice::note($output, sprintf('Nothing matched. %d hints were candidates, and are returned as the index.', count($result['availableHints'])));

            return 0;
        }

        // What each word of the query is worth, before any hint is named. A
        // term is weighed by how few of the candidates carry it, so this is
        // where a common word shows as the cheap one — and where the statement
        // somebody has just written into the corpus shows what it cost every
        // query carrying that word.
        $weights = $result['matchedHints'][0]['matchedOn']['weights'];
        if ($weights !== []) {
            arsort($weights);
            $words = TermSearch::words($query);
            Voice::heading($output, 'Terms');
            foreach ($weights as $term => $weight) {
                Voice::row($output, sprintf('%s %.2f  %s', Voice::key($term, 12), $weight, $words[$term] ?? $term));
            }
        }
        Voice::heading($output, 'Hits');
        foreach ($result['matchedHints'] as $hint) {
            // Which way in earned it. A hit on the curated vocabulary means
            // somebody anticipated this phrasing; a hit on the text alone means
            // the hint answers a question nobody indexed it for, and that is the
            // one worth reading — it is either a good catch or a false one.
            $how = $hint['matchedOn']['keywords'] > 0
                ? sprintf('appliesTo(%d) + text(%d)', $hint['matchedOn']['keywords'], $hint['matchedOn']['score'])
                : sprintf('text only(%d)', $hint['matchedOn']['score']);

            Voice::row($output, sprintf('%s %-16s %s', Voice::key($hint['id'], 34), $hint['category'], $how));
            Voice::row($output, '    ' . Voice::dim(self::admission($hint['matchedOn'])));
        }

        return 0;
    }

    /**
     * Which of the three ways in admitted this hit, and how much room it left.
     *
     * The third is the fragile one: a coverage share is measured against
     * weights the whole corpus decides, so an unrelated statement using one of
     * these words moves it. The number is printed rather than a verdict on it,
     * because a reader seeing 0.502 against 0.500 knows to look at what they
     * have just written instead of at the assertion that failed — `D-ANS-115`.
     *
     * @param array{keywords: int, score: int, coverage: float, terms: array<string, string>, weights: array<string, float>} $matchedOn
     */
    private static function admission(array $matchedOn): string
    {
        if ($matchedOn['keywords'] > 0) {
            return sprintf(
                'admitted by the curated vocabulary; coverage %.3f is not asked',
                $matchedOn['coverage'],
            );
        }

        if (count($matchedOn['terms']) === count($matchedOn['weights'])) {
            return 'admitted by carrying every term of the query';
        }

        return sprintf(
            'admitted by coverage %.3f against a floor of %.3f, missing %s',
            $matchedOn['coverage'],
            Hints::MIN_COVERAGE,
            implode(', ', array_keys(array_diff_key($matchedOn['weights'], $matchedOn['terms']))),
        );
    }
}
