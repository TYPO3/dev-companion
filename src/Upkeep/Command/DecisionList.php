<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What was decided, newest first.
 *
 * One decision is one file below decisions/, an id decides the directory and
 * the file name, and the order is generated from the dates rather than
 * maintained by where a paragraph was pasted.
 */
#[AsCommand(
    name: 'decisions:list',
    description: 'every decision newest first, or one group of them',
)]
final class DecisionList
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('one group of them rather than all')]
        string $group = '',
    ): int {
        if ($group !== '' && !in_array($group, Decisions::GROUPS, true)) {
            Voice::problem($output, 'No such group: ' . $group . '
Groups: ' . implode(', ', Decisions::GROUPS));

            return 2;
        }

        foreach (Decisions::group($group) as $decision) {
            $output->writeln(sprintf(
                '%s  %s %-12s %-10s %s',
                Voice::dim($decision['date']),
                Voice::key($decision['id'], 10),
                $decision['group'],
                $decision['status'],
                $decision['title'],
            ));
        }

        return 0;
    }
}
