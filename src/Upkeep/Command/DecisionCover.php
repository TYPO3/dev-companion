<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Entry;
use TYPO3\DevCompanion\Upkeep\Sources;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Writes each entry's `coveredBy` from the `#[Decision]` attributes the tests
 * carry.
 *
 * The coupling has to be readable from both ends: a session standing in a red
 * test needs the entry that rested on it, and a reader of the entry needs the
 * test that would catch its **Wrong if**. Written in both places by hand, the
 * two drifted — 405 tests were named by an entry and said nothing about it
 * (`D-DOC-043`). One is generated from the other now, so drifting is not a
 * state the two can be in.
 */
#[AsCommand(
    name: 'decisions:cover',
    description: 'write coveredBy in each entry from the #[Decision] attributes the tests carry',
)]
final class DecisionCover
{
    public function __invoke(OutputInterface $output): int
    {
        $held = Sources::held('Decision');
        $written = 0;
        foreach (Decisions::files() as $path) {
            $contents = (string) file_get_contents($path);
            $covered = Entry::withNames($contents, 'coveredBy', $held[Decisions::read($path)['id']] ?? []);
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
