<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\Decisions;
use TYPO3\DevCompanion\Upkeep\Renumber;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Giving a decision another number, and handing over the references it cannot.
 *
 * What moves is the file the caller named, never the first one carrying the id:
 * both files carry it, and an id two files claim is refused with both named,
 * because the one that keeps its number is the one that merged first and
 * nothing here can read that. This settles the half a file can settle — the
 * entry, its name, and every reference whose link path says which entry is meant
 * — and prints the rest, which a person reads against `git diff main -- <file>`
 * (`D-DOC-015`). A listing that already carried the entry is put back in order,
 * and one a branch left alone is left alone.
 */
#[AsCommand(
    name: 'decisions:renumber',
    description: 'give a decision another number, moving what a link path settles and naming what it does not',
)]
final class DecisionRenumber
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('the decision to move, by id or by file')]
        string $decision,
        #[Argument('the number it takes, or nothing for the next one free in its group')]
        string $number = '',
    ): int {
        $root = Paths::root();

        $files = Renumber::files($root, $decision);
        if ($files === []) {
            Voice::problem($output, $decision . ' names neither an id nor a decision file');

            return 1;
        }
        if (count($files) > 1) {
            Voice::problem($output, $decision . ' is claimed by more than one file, and which of them moves is yours to say:');
            foreach ($files as $path) {
                Voice::row($output, self::relative($root, $path));
            }

            return 1;
        }

        $from = Decisions::read($files[0])['id'];

        try {
            $to = $number === '' ? Renumber::next($root, substr($from, 2, 3)) : $this->target($from, $number);
            $report = Renumber::decision($root, $files[0], $to);
        } catch (\InvalidArgumentException|\RuntimeException $exception) {
            Voice::problem($output, $exception->getMessage());

            return 1;
        }

        Voice::ok($output, sprintf('%s → %s', $report['from'], $report['to']));
        Voice::row($output, Voice::dim($report['file']));

        $output->writeln('');
        $output->writeln(sprintf(
            '%d mentions moved: the entry itself, and every reference whose own line names its file.',
            count($report['moved']),
        ));
        foreach ($this->byFile($report['moved']) as $file => $lines) {
            $output->writeln(sprintf('    %s (%d)', $file, count($lines)));
        }

        if ($report['branch'] !== []) {
            $output->writeln('');
            $output->writeln(sprintf(
                '%d more moved: a line this branch added means this branch\'s entry. Read them.',
                count($report['branch']),
            ));
            foreach ($this->byFile($report['branch']) as $file => $lines) {
                $output->writeln('');
                $output->writeln('    ' . $file);
                foreach ($lines as $line) {
                    $output->writeln(sprintf('    %5d  %s', $line['line'], $line['text']));
                }
            }
        }

        $output->writeln('');
        if ($report['named'] === []) {
            $output->writeln('Nothing else names ' . $report['from'] . '.');
        } else {
            $output->writeln(sprintf(
                '%d name %s, stand on `main` as well, and no file says which entry is meant. Read each against',
                count($report['named']),
                $report['from'],
            ));
            $output->writeln('`git diff main -- <file>`: a line this branch added means this branch\'s entry.');
            foreach ($this->byFile($report['named']) as $file => $lines) {
                $output->writeln('');
                $output->writeln('    ' . $file);
                foreach ($lines as $line) {
                    $output->writeln(sprintf('    %5d  %s', $line['line'], $line['text']));
                }
            }
        }

        if ($this->listed($report['moved'])) {
            (new DecisionIndex())(new NullOutput());
            $output->writeln('');
            $output->writeln('The listings are regenerated: the number is what a group sorts on.');
        }

        return 0;
    }

    private static function relative(string $root, string $path): string
    {
        return str_starts_with($path, $root . '/') ? substr($path, strlen($root) + 1) : $path;
    }

    /**
     * The number a caller named, as a whole id. A bare one takes the group of
     * the entry being moved; a written-out id is left as it stands, so naming
     * another group is refused rather than silently corrected.
     */
    private function target(string $from, string $number): string
    {
        if (preg_match('/^D-[A-Z]{3}-\d{3}[a-z]?$/', $number) === 1) {
            return $number;
        }
        if (preg_match('/^(\d{1,3})([a-z]?)$/', $number, $matches) !== 1) {
            throw new \InvalidArgumentException($number . ' is no decision number');
        }

        return substr($from, 0, 6) . str_pad($matches[1], 3, '0', STR_PAD_LEFT) . $matches[2];
    }

    /**
     * @param list<array{file: string, line: int, text: string}> $references
     * @return array<string, list<array{file: string, line: int, text: string}>>
     */
    private function byFile(array $references): array
    {
        $byFile = [];
        foreach ($references as $reference) {
            $byFile[$reference['file']][] = $reference;
        }

        return $byFile;
    }

    /** @param list<array{file: string, line: int, text: string}> $moved */
    private function listed(array $moved): bool
    {
        foreach ($moved as $reference) {
            if (preg_match('#^decisions/(?:[^/]+/)?readme\.md$#', $reference['file']) === 1) {
                return true;
            }
        }

        return false;
    }
}
