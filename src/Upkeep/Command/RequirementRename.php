<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Renumber;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Puts every requirement where its title says, and rewrites what named it.
 *
 * A wrong title gets a correction, and the file name is the title. So the move
 * is a consequence of the correction rather than a reason not to make it. This
 * command exists to make it cheap (`D-DOC-047`). Run over the whole corpus
 * rather than one entry, because a title changes where it stands and nobody
 * knows afterwards which one moved.
 */
#[AsCommand(
    name: 'requirements:rename',
    description: 'file every requirement under the name its title says, rewriting every path that names one',
)]
final class RequirementRename
{
    public function __invoke(OutputInterface $output): int
    {
        $moved = 0;
        $references = 0;
        foreach (Requirements::all() as $requirement) {
            $file = Requirements::directory() . '/' . $requirement['group'] . '/' . $requirement['file'];
            $refiled = Renumber::refile(Paths::root(), $file, $requirement['id'], $requirement['title']);
            if ($refiled['from'] === $refiled['to']) {
                continue;
            }

            ++$moved;
            $references += $refiled['references'];
            Voice::row($output, sprintf('%s → %s', $refiled['from'], $refiled['to']));
        }

        Voice::ok($output, sprintf('%s moved, %s rewritten.', Voice::count($moved, 'requirement'), Voice::count($references, 'reference')));

        return 0;
    }
}
