<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\OpenFeedback;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The pile as a pile, for whoever wants to read it rather than work it.
 *
 * What is to be done about any of it is on the board, one card per open
 * feedback. This is the other question: what has arrived, and which of it
 * somebody has taken on. Grouped by the checkout it was written in, because a
 * gap reported once and the same gap reported by thirty sessions out of one
 * directory are judged differently — `D-FBK-025`.
 */
#[AsCommand(
    name: 'feedback:list',
    description: 'every open feedback, grouped by the checkout it came from',
)]
final class FeedbackList
{
    /** Where a feedback with no directory is grouped, for a session that left none. */
    private const NO_DIRECTORY = 'no directory recorded';

    public function __invoke(OutputInterface $output): int
    {
        $open = OpenFeedback::all();
        if ($open === []) {
            Voice::ok($output, 'No open feedback.');

            return 0;
        }

        $groups = [];
        foreach ($open as $feedback) {
            $groups[$feedback['directory'] === '' ? self::NO_DIRECTORY : $feedback['directory']][] = $feedback;
        }
        // The biggest corpus first: what several sessions reported from one place
        // is what a judging run is looking for, and it is the group that is
        // easiest to miss one card at a time.
        uasort($groups, static fn(array $a, array $b): int => count($b) <=> count($a));

        $unjudged = count(array_filter($open, static fn(array $feedback): bool => !$feedback['judged']));
        Voice::note($output, sprintf('%d open, %d with no todo naming them, in %d directories.', count($open), $unjudged, count($groups)));

        foreach ($groups as $directory => $feedback) {
            Voice::heading($output, sprintf('%s — %d, newest first', $directory, count($feedback)));
            foreach (array_reverse($feedback) as $entry) {
                Voice::row($output, sprintf(
                    '%s  %s%s',
                    $entry['file'],
                    Voice::dim($entry['model']),
                    $entry['judged'] ? '' : ' — no todo names it',
                ));
            }
        }

        return 0;
    }
}
