<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What one answer of each tool costs the caller who asked for it.
 *
 * A session is charged one context per call and what comes back is nearly free
 * per token — until it is not, and nothing here said which tool was the one
 * paying. Three sessions reported the same thing from three sides and none of
 * them was a number for the whole surface, so a trim started at whichever tool
 * somebody had noticed rather than at the top of a list.
 *
 * It reports and never fails. A long answer can be the right one — the guides
 * are long because a procedure is — and a counter that failed on one would be
 * answered by splitting it in two.
 */
#[AsCommand(
    name: 'tools:measure',
    description: 'what one recorded answer of each tool weighs, worst first',
)]
final class ToolMeasure
{
    public function __invoke(OutputInterface $output): int
    {
        $measured = ToolAnswers::measured();

        $text = array_sum(array_column($measured, 'text'));
        $data = array_sum(array_column($measured, 'data'));

        foreach ($measured as $tool) {
            if ($tool['calls'] === 0) {
                continue;
            }
            $output->writeln(sprintf(
                '%7s  %s %s text, %s data, over %s',
                number_format($tool['total']),
                Voice::key($tool['tool'], 34),
                number_format($tool['text']),
                number_format($tool['data']),
                Voice::count($tool['calls'], 'call'),
            ));
        }

        $output->writeln('');
        Voice::ok($output, sprintf(
            '%s bytes recorded across %d tools: %s text, %s data.',
            number_format($text + $data),
            count(array_filter($measured, static fn(array $tool): bool => $tool['calls'] > 0)),
            number_format($text),
            number_format($data),
        ));
        Voice::note(
            $output,
            'Recorded by bin/cli tools:record and bin/cli tools:index, so nothing was called to count it. '
            . 'A tool answering several calls is the sum of them, not one answer.'
        );

        return 0;
    }
}
