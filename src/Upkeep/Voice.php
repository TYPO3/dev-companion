<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The one voice every command speaks in.
 *
 * A heading, a row under it, a verdict at the end, a problem where one is
 * found and a note that is context rather than answer: five shapes, and a
 * reader who has seen one command has seen them all. The colour is on the mark
 * and nowhere in the words, so a pipe and `--no-ansi` read the same sentence
 * — `D-DOC-067`.
 */
final class Voice
{
    private const OK = '<fg=green>✓</> ';
    private const WRONG = '<fg=red>✗</> ';

    /**
     * Every output a heading has been written to, so the second heading on one
     * is set off from what came before it and the first is not.
     *
     * @var ?\WeakMap<OutputInterface, true>
     */
    private static ?\WeakMap $headed = null;

    /** A section's name, and a blank line above it where it is not the first. */
    public static function heading(OutputInterface $output, string $text): void
    {
        self::$headed ??= new \WeakMap();
        if (isset(self::$headed[$output])) {
            $output->writeln('');
        }
        self::$headed[$output] = true;
        $output->writeln('<options=bold>' . OutputFormatter::escape($text) . '</>');
    }

    /**
     * One entry under a heading, at the indent every row stands at.
     *
     * Not escaped, because a row is assembled from `key()` and `dim()`, which
     * escape what they are given and hand back a tag.
     */
    public static function row(OutputInterface $output, string $text): void
    {
        $output->writeln('  ' . $text);
    }

    /** What went right, and the mark that says so before the sentence does. */
    public static function ok(OutputInterface $output, string $text): void
    {
        $output->writeln(self::OK . OutputFormatter::escape($text));
    }

    /**
     * What went wrong, on the error stream.
     *
     * A check prints its problems here and its verdict on stdout, so the count
     * survives a pipe that the problems are read out of — `Cli::errors()`.
     */
    public static function problem(OutputInterface $output, string $text): void
    {
        Cli::errors($output)->writeln(self::WRONG . OutputFormatter::escape($text));
    }

    /** What went wrong, where the caller keeps stdout for it: the verdict of a check that failed. */
    public static function wrong(OutputInterface $output, string $text): void
    {
        $output->writeln(self::WRONG . OutputFormatter::escape($text));
    }

    /**
     * How a check came out, as the exit code the caller returns.
     *
     * The sentence for the failing case is the one the count already sits in,
     * so a caller with one sentence for both leaves the second out.
     */
    public static function verdict(OutputInterface $output, int $problems, string $fine, ?string $wrong = null): int
    {
        if ($problems === 0) {
            self::ok($output, $fine);

            return 0;
        }
        self::wrong($output, $wrong ?? $fine);

        return 1;
    }

    /** Context rather than answer — where to read the result, what to run next — set apart from both. */
    public static function note(OutputInterface $output, string $text): void
    {
        $output->writeln('<fg=gray>' . OutputFormatter::escape($text) . '</>');
    }

    /**
     * An identifier where it leads a row — an entry id, a branch, a step —
     * padded to its column and coloured so a listing can be scanned by it.
     */
    public static function key(string $text, int $width = 0): string
    {
        return '<fg=cyan>' . OutputFormatter::escape(str_pad($text, $width)) . '</>';
    }

    /** What a row carries that a reader skims past — a path, a date, a clock time. */
    public static function dim(string $text): string
    {
        return '<fg=gray>' . OutputFormatter::escape($text) . '</>';
    }

    /**
     * A bar for the steps a command takes, on a terminal and nowhere else.
     *
     * A pipe, a log and `--no-ansi` get none of it: a bar that cannot redraw
     * itself is a line per step, and the rows the command prints are those
     * already. Zero steps is a bar with no end, for a loop whose length is
     * not known before it runs.
     */
    public static function progress(OutputInterface $output, int $steps = 0): ProgressBar
    {
        $bar = new ProgressBar($output->isDecorated() ? $output : new NullOutput(), $steps);
        $bar->setFormat($steps === 0
            ? '  <fg=cyan>%message%</> %bar% %current%'
            : '  <fg=cyan>%message%</> %bar% %current%/%max%');
        $bar->setBarCharacter('<fg=green>━</>');
        $bar->setEmptyBarCharacter('<fg=gray>━</>');
        $bar->setProgressCharacter('<fg=green>━</>');
        $bar->setBarWidth(24);
        $bar->setMessage('');

        return $bar;
    }

    /** A number and its noun, in the form the number takes: "1 problem", "3 problems". */
    public static function count(int $number, string $one, ?string $many = null): string
    {
        return $number . ' ' . ($number === 1 ? $one : ($many ?? $one . 's'));
    }
}
