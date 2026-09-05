<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Renumber;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Puts every decision where its title says, and rewrites what named it.
 *
 * A title that is wrong is corrected, and the file name is the title — so the
 * move is a consequence of the correction rather than a reason not to make it,
 * which is what this command exists to make cheap (`D-DOC-047`). Run over the
 * whole corpus rather than one entry, because a title is edited where it stands
 * and nobody knows afterwards which one moved.
 */
#[AsCommand(
    name: 'decisions:rename',
    description: 'file every decision under the name its title says, rewriting every path that names one',
)]
final class DecisionRename
{
    public function __invoke(OutputInterface $output): int
    {
        $moved = 0;
        $references = 0;
        foreach (Decisions::all() as $decision) {
            $file = Decisions::directory() . '/' . $decision['group'] . '/' . $decision['file'];
            $refiled = Renumber::refile(Paths::root(), $file, $decision['id'], $decision['title']);
            if ($refiled['from'] === $refiled['to']) {
                continue;
            }

            ++$moved;
            $references += $refiled['references'];
            Voice::row($output, sprintf('%s → %s', $refiled['from'], $refiled['to']));
        }

        Voice::ok($output, sprintf('%s moved, %s rewritten.', Voice::count($moved, 'decision'), Voice::count($references, 'reference')));

        return 0;
    }
}
