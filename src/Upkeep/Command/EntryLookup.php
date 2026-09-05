<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Entries;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What is written down about the code at a path, before it is changed.
 *
 * The attributes answer from the failing end: a red test names the entries that
 * rested on it. That is after the change. This is the call a session makes
 * before one — the entries naming the class it is about to edit, and the tests
 * that would print them.
 */
#[AsCommand(
    name: 'entries:lookup',
    description: 'the decisions and requirements written about the code at a path',
)]
final class EntryLookup
{
    /** How many entries a test class is listed with rather than counted. */
    private const LISTED = 8;

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'a file or a directory below src/ or tests/')]
        string $path = '',
    ): int {
        if ($path === '') {
            Voice::problem($output, 'Name a file or a directory: bin/cli entries:lookup src/Knowledge/Hints.php');

            return 1;
        }

        $classes = Entries::declaredBelow($path);
        if ($classes === []) {
            Voice::problem($output, $path . ' declares no class this repository reads.');

            return 1;
        }

        Voice::heading($output, sprintf('%s — %s', $path, implode(', ', $classes)));
        $entries = Entries::all();

        $naming = Entries::naming($classes);
        if ($naming === []) {
            Voice::note($output, 'Nothing is written about it. What that means is that nothing was, not that anything is settled.');
        } else {
            Voice::heading($output, sprintf('%d entries name it', count($naming)));
        }
        foreach ($naming as $id => $named) {
            $entry = $entries[$id];
            Voice::row($output, sprintf('%s %-11s %s', Voice::key($id, 11), $entry['status'], $entry['title']));
            Voice::row($output, sprintf('%-23s %s', '', Voice::dim($entry['file'] . ' — ' . implode(', ', $named))));
        }

        $tests = Entries::testsNaming($classes);
        if ($tests !== []) {
            Voice::heading($output, sprintf(
                '%d test classes name it, holding %d entries between them',
                count($tests),
                count(array_unique(array_merge(...array_values($tests)))),
            ));
            foreach ($tests as $test => $ids) {
                // The ids where a reader can hold them, the count where a
                // hundred of them would be the whole answer's length. What a
                // long one says is how much rides on the class, and the entries
                // themselves are what the failure prints.
                Voice::row($output, sprintf(
                    '%s %s',
                    Voice::key($test, 28),
                    count($ids) > self::LISTED ? count($ids) . ' entries' : implode(', ', $ids),
                ));
            }
        }

        return 0;
    }
}
