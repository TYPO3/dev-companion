<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolSurface;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What one answer of each tool costs the caller who asked for it, and what
 * each tool's definition costs before any call.
 *
 * A session pays one context per call and what comes back is nearly free per
 * token. Until it is not, and nothing here said which tool was the one that
 * paid. Three sessions reported the same thing from three sides and none of
 * them was a number for the whole surface. So a trim started at whichever tool
 * somebody had noticed rather than at the top of a list.
 *
 * Both halves of an answer count, and both halves of a definition, because
 * which half a client hands the model is the client's — `D-EVI-011`.
 *
 * It reports and never fails. A long answer can be the right one, the guides
 * are long because a procedure is. A counter that failed on one would get a
 * split in two as its answer.
 */
#[AsCommand(
    name: 'tools:measure',
    description: 'what each tool definition and one recorded answer of each tool weigh, worst first',
)]
final class ToolMeasure
{
    public function __invoke(OutputInterface $output): int
    {
        self::definitions($output);
        self::answers($output);

        return 0;
    }

    private static function definitions(OutputInterface $output): void
    {
        $measured = ToolSurface::measured();
        $declared = array_sum(array_column($measured, 'declared'));
        $schema = array_sum(array_column($measured, 'outputSchema'));

        Voice::heading($output, 'Definitions, as tools/list carries them');
        foreach ($measured as $tool) {
            $output->writeln(sprintf(
                '%7s  %s %s declared, %s output schema',
                number_format($tool['declared'] + $tool['outputSchema']),
                Voice::key($tool['tool'], 34),
                number_format($tool['declared']),
                number_format($tool['outputSchema']),
            ));
        }

        $output->writeln('');
        Voice::ok($output, sprintf(
            '%s bytes over %s: %s declared, %s output schema (%d%%).',
            number_format($declared + $schema),
            Voice::count(count($measured), 'definition'),
            number_format($declared),
            number_format($schema),
            (int) round(100 * $schema / max(1, $declared + $schema)),
        ));
        Voice::note(
            $output,
            'Declared is the name, the description and the input schema, which is what a client hands the model. '
            . 'Neither client read on 2026-09-17 hands it the output schema — D-EVI-011.'
        );
    }

    private static function answers(OutputInterface $output): void
    {
        $measured = ToolAnswers::measured();
        $text = array_sum(array_column($measured, 'text'));
        $data = array_sum(array_column($measured, 'data'));

        Voice::heading($output, 'Answers, as recorded');
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
            . 'A tool answering several calls is the sum of them, not one answer. '
            . 'Claude Code hands the model the data half alone and opencode the text half — D-EVI-011. '
            . 'bin/cli tools:tokens measures one call in the client.'
        );
    }
}
