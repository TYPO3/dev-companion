<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Links;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The other half of `links:check`: the dead link this repository moved the file
 * out from under, repointed.
 *
 * One move here does that, and it is the one nothing can do in advance —
 * `feedback:archive` cannot see the decision another branch is writing to the
 * report in the same window, and whichever of the two merges second carries the
 * link. `todo:home` runs this on the rebased branch, and it is the same command
 * for a checkout that met it some other way — `D-DOC-064`.
 */
#[AsCommand(
    name: 'links:repair',
    description: 'repoint what names a feedback at the archive the feedback was answered into',
)]
final class LinkRepair
{
    public function __invoke(OutputInterface $output): int
    {
        $written = Links::repair();
        if ($written === []) {
            Voice::ok($output, 'No link names a feedback that has been answered since.');

            return 0;
        }

        foreach ($written as $link) {
            Voice::row($output, sprintf('%s: %s is now %s', $link['file'], $link['link'], $link['repair']));
        }
        Voice::ok($output, Voice::count(count($written), 'link') . ' repointed at the archive.');

        return 0;
    }
}
