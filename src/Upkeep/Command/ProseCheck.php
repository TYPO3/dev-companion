<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Prose;
use TYPO3\DevCompanion\Upkeep\Voice;
use TYPO3\DevCompanion\Upkeep\Wrap;

/**
 * What the prose rule in AGENTS.md costs when nothing reads it.
 *
 * Every other rule that file states is held by something, and "one point per
 * sentence" was held by whoever reread the paragraph. This counts. It fails on
 * one thing only — the bold sentence a requirement or a decision opens with,
 * because that one has a job the rest of the file does not — and reports the
 * rest, over the three corpora `D-DOC-035` names.
 */
#[AsCommand(
    name: 'prose:check',
    description: 'the sentences over ' . Prose::MEASURE . ' words, what the comments cost, and the leads that may not be',
)]
final class ProseCheck
{
    /** How many files the report names before it stops naming them. */
    private const NAMED = 10;

    /**
     * The measure over the whole corpus, worst file first.
     *
     * A long sentence in the body is reported and nothing else: it can be the
     * right sentence, and a rewrite made to satisfy a counter produces two
     * short ones saying what one said. What the number is for is the file that
     * has twenty of them, which is a file nobody has reread since it was
     * written.
     */
    public function __invoke(OutputInterface $output): int
    {
        $measured = array_map(Prose::measure(...), Prose::documents());
        usort($measured, static fn(array $a, array $b): int => count($b['over']) <=> count($a['over']));

        $sentences = array_sum(array_column($measured, 'sentences'));
        $over = array_sum(array_map(static fn(array $file): int => count($file['over']), $measured));

        Voice::heading($output, sprintf(
            '%d of %d sentences run past %d words, in %d files',
            $over,
            $sentences,
            Prose::MEASURE,
            count(array_filter($measured, static fn(array $file): bool => $file['over'] !== [])),
        ));

        foreach (array_slice(array_filter($measured, static fn(array $file): bool => $file['over'] !== []), 0, self::NAMED) as $file) {
            Voice::row($output, sprintf('%s  %s %s', Voice::key((string) count($file['over']), 3), $file['file'], Voice::dim(sprintf('(longest %d)', $file['over'][0]['words']))));
        }

        // The other half, and the one nothing counted: what a client is handed
        // at connect. It is reported beside the corpus rather than folded into
        // it, because the number a reader wants here is the weight — a caller
        // pays for all of it before it has asked anything — and not which of
        // 578 markdown files is worst.
        $payload = Prose::payloadOverTheMeasure();
        Voice::heading($output, sprintf(
            'A client is handed %d characters of prose at connect, and %d of those sentences run past %d words',
            Prose::payloadWeight(),
            count($payload),
            Prose::MEASURE,
        ));
        foreach (array_slice($payload, 0, self::NAMED) as $entry) {
            Voice::row($output, sprintf('%s  %s', Voice::key((string) $entry['words'], 3), $entry['where']));
        }

        // The third corpus, and the one the sentence measure cannot see: a
        // comment that names its decision and retells it anyway is within the
        // measure on every sentence and is the duplicate the rule forbids.
        $weight = Prose::commentWeight();
        $retold = Prose::retellings();
        Voice::heading($output, sprintf(
            '%d of %d lines of PHP are comment, and %d comments name an entry and write past %d lines of prose',
            $weight['comment'],
            $weight['lines'],
            count($retold),
            Prose::RETOLD,
        ));
        foreach (array_slice($retold, 0, self::NAMED) as $comment) {
            Voice::row($output, sprintf(
                '%s  %s:%d %s',
                Voice::key((string) $comment['prose'], 3),
                $comment['file'],
                $comment['line'],
                Voice::dim('(' . implode(', ', $comment['names']) . ')'),
            ));
        }

        // The fourth corpus, and the one a formatter owns half of. Padding is
        // mechanical and `bin/cli prose:format` does it; what is reported is
        // the width that leaves, because a cell nobody can shorten is a list
        // rather than a table and only a reader can say which — `D-DOC-001`.
        $tables = Prose::tables();
        $wide = array_values(array_filter(
            $tables,
            static fn(array $table): bool => $table['cell'] > Wrap::COLUMN,
        ));
        Voice::heading($output, sprintf(
            '%d of %d tables hold a cell no line of %d columns fits',
            count($wide),
            count($tables),
            Wrap::COLUMN,
        ));
        foreach (array_slice($wide, 0, self::NAMED) as $table) {
            Voice::row($output, sprintf(
                '%s  %s:%d %s',
                Voice::key((string) $table['cell'], 3),
                $table['file'],
                $table['line'],
                Voice::dim(sprintf('(%d rows, %d wide)', $table['rows'], $table['width'])),
            ));
        }

        // The other half of the same rule, and the half nothing measured: a
        // title is the name an entry is read by, not its statement said again
        // — `D-DOC-046`.
        $titles = Prose::titles();
        $joined = array_values(array_filter($titles, static fn(array $title): bool => $title['joined']));
        Voice::heading($output, sprintf(
            '%d titles carry more than one thing, %d of them joining two claims outright',
            count($titles),
            count($joined),
        ));
        foreach (array_slice([...$joined, ...array_filter($titles, static fn(array $title): bool => !$title['joined'])], 0, self::NAMED) as $title) {
            Voice::row($output, sprintf('%s  %s %s', Voice::key((string) $title['words'], 3), Voice::key($title['id'], 11), $title['title']));
        }

        $names = Prose::names();
        $joined = array_values(array_filter($names, static fn(array $name): bool => $name['joined'] !== ''));
        Voice::heading($output, sprintf(
            '%d test names run past %d words, %d of them joining two claims outright',
            count($names),
            Prose::NAME_WORDS,
            count($joined),
        ));
        foreach (array_slice($names, 0, self::NAMED) as $name) {
            Voice::row($output, sprintf('%s  %s %s', Voice::key((string) $name['words'], 3), Voice::key($name['file'], 30), $name['name']));
        }

        $leads = Prose::leadsOverTheMeasure();
        $output->writeln('');
        $verdict = Voice::verdict(
            $output,
            count($leads),
            'Every requirement and decision opens within the measure.',
            sprintf('%d open with a sentence a reader cannot stop after:', count($leads)),
        );
        foreach ($leads as $lead) {
            Voice::row($output, sprintf('%s %3d words  %s…', Voice::key($lead['id'], 10), $lead['words'], substr($lead['text'], 0, 60)));
        }

        return $verdict;
    }
}
