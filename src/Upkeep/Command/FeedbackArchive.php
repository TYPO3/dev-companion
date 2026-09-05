<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Feedback\Channel;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Closing a feedback, which is moving it.
 *
 * In the same commit as the improvement it asked for, so that commit is both
 * what answers the feedback and what says so. Several feedback closed by one
 * change are named in one call, because they are one commit.
 */
#[AsCommand(
    name: 'feedback:archive',
    description: 'move the feedback a change worked off into feedback/archive/',
)]
final class FeedbackArchive
{
    /**
     * @param array<int, string> $feedback
     */
    public function __invoke(
        OutputInterface $output,
        #[Argument('the feedback this change worked off')]
        array $feedback,
    ): int {
        foreach ($feedback as $one) {
            try {
                Voice::ok($output, Channel::archive($one));
            } catch (\InvalidArgumentException|\RuntimeException $exception) {
                Voice::problem($output, $exception->getMessage());

                return 1;
            }
        }

        return 0;
    }
}
