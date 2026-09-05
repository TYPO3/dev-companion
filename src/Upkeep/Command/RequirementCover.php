<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Requirements;
use TYPO3\DevCompanion\Upkeep\Sources;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Writes each entry's `heldBy` from the `#[Requirement]` attributes the tests
 * carry.
 *
 * The same one-way generation `decisions:cover` does, for the corpus that says
 * what must be true — `D-DOC-049`. What stays in the `## Held by` section is
 * what is not a test: a `bin/cli` command, a clause saying what one of them
 * holds, a half nothing guards.
 */
#[AsCommand(
    name: 'requirements:cover',
    description: 'write heldBy in each entry from the #[Requirement] attributes the tests carry',
)]
final class RequirementCover
{
    public function __invoke(OutputInterface $output): int
    {
        $held = Sources::held('Requirement');
        $written = 0;
        foreach (Requirements::files() as $path) {
            $contents = (string) file_get_contents($path);
            $covered = Entry::withNames($contents, 'heldBy', $held[Requirements::read($path)['id']] ?? []);
            if ($covered === $contents) {
                continue;
            }
            file_put_contents($path, $covered);
            Voice::row($output, basename(dirname($path)) . '/' . basename($path));
            $written++;
        }

        Voice::ok($output, sprintf(
            '%d entries rewritten, %d held by %d tests in all',
            $written,
            count($held),
            array_sum(array_map(count(...), $held)),
        ));

        return 0;
    }
}
