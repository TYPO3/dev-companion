<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\RangeReport;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether every worked example is still where the catalog records it.
 *
 * A reference entry promises a shape rather than a path, so its range is
 * derived from the files that carry that shape and not from the directory
 * around them (`D-CAT-007`).
 */
#[AsCommand(
    name: 'references:check',
    description: 'the worked examples each reference entry names, against .checkouts/',
)]
final class ReferenceCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();

        return self::verifyReferences($output, $checkouts, Catalogs::read('reference/entries'));
    }

    /**
     * Re-reads which covered versions have each worked example, and reports every
     * entry whose recorded range no longer says so.
     *
     * A directory that moved leaves an answer pointing at nothing, and the caller
     * reads the miss as "I looked in the wrong place". Where the directory alone
     * would claim a major the shape is not on, the entry names the files that
     * carry it in `files` and a version has the example when it has all of them
     * — `D-CAT-007`.
     *
     * @param array<int, array<string, mixed>> $references
     */
    private static function verifyReferences(OutputInterface $output, string $checkouts, array $references): int
    {
        Voice::heading($output, 'Core references');
        $covered = Versions::covered();
        $problems = 0;
        foreach ($references as $entry) {
            $path = (string) $entry['path'];
            $majors = [];
            $inside = [];
            foreach ($covered as $version) {
                $branch = $checkouts . '/' . $version['branch'];
                if (!is_dir($branch)) {
                    Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                    return 2;
                }
                $missing = self::missingFrom($branch, $entry);
                if ($missing === null) {
                    $majors[] = $version['major'];
                    continue;
                }
                if ($missing !== $path) {
                    // The failure the range alone cannot name: the directory is
                    // where it was, and what the entry says is inside it is not.
                    $inside[] = sprintf('v%d has %s and not %s', $version['major'], $path, $missing);
                }
            }

            if ($majors === []) {
                Voice::problem($output, sprintf('%s: on no covered version', $path));
                self::writeAll($output, $inside);
                ++$problems;
                continue;
            }
            $drift = RangeReport::of($output, $path, $entry, $majors, array_column($covered, 'major'));
            if ($drift > 0) {
                self::writeAll($output, $inside);
            }
            $problems += $drift;
        }
        Voice::row($output, sprintf('%d references against %s', count($references), implode(', ', array_column($covered, 'branch'))));

        return Voice::verdict($output, $problems, 'Every worked example is where it is recorded.', Voice::count($problems, 'reference') . ' out of date.');
    }

    /**
     * The first thing a worked example names that this checkout does not have, or
     * null where it has all of them.
     *
     * The path comes first, so an entry that names no files reads exactly as it did
     * before. `files` is what the entry adds when its sentence promises a shape: the
     * two or three files that carry it, named absolutely from the checkout root
     * because half of what a suite is made of sits beside the directory rather than
     * inside it.
     *
     * @param array<string, mixed> $entry
     */
    private static function missingFrom(string $branch, array $entry): ?string
    {
        $named = array_merge([(string) $entry['path']], array_map(strval(...), (array) ($entry['files'] ?? [])));
        foreach ($named as $path) {
            if (!file_exists($branch . '/' . $path)) {
                return $path;
            }
        }

        return null;
    }

    /** @param array<int, string> $lines */
    private static function writeAll(OutputInterface $output, array $lines): void
    {
        foreach ($lines as $line) {
            Voice::problem($output, $line);
        }
    }
}
