<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Writes the listing of every group, and of all of them, back into the readmes.
 *
 * What this replaces is one document of thirty entries that called itself
 * newest-first and was not: two had arrived at its foot, and the labels a
 * reader navigates by had drifted into thirteen spellings of four things.
 */
#[AsCommand(
    name: 'decisions:index',
    description: 'rewrite the listing at the foot of the readme and of each group readme',
)]
final class DecisionIndex
{
    /**
     * Where the generated listing begins, so everything above it survives a
     * regeneration. Three shapes are matched: the table these listings were
     * until D-DOC-001, the list they are now, and the group heading the root
     * readme carries above each run of it.
     *
     * That last one is why prose above a listing may not use a third-level
     * heading: it would be read as the start of the generated half and go with
     * the next regeneration.
     */
    private const LISTING_STARTS = '/(?:\| Decided\s|### |- \[`D-)[^\n]*(?:\n.*)?$/s';

    public function __invoke(OutputInterface $output): int
    {
        foreach (['', ...array_values(Decisions::GROUPS)] as $group) {
            $readme = Decisions::directory() . '/' . ($group === '' ? '' : $group . '/') . 'readme.md';
            $contents = (string) file_get_contents($readme);
            $head = (string) preg_replace(self::LISTING_STARTS, '', $contents);
            file_put_contents($readme, $head . Decisions::listing($group));
            Voice::row($output, substr($readme, strlen(Paths::root()) + 1));
        }

        return 0;
    }
}
