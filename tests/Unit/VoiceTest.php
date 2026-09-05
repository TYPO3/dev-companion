<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * The shapes every command prints in, held where the words are not: the mark,
 * the colour, the blank line and the stream are what make two commands read as
 * one product — `D-DOC-067`.
 */
final class VoiceTest extends TestCase
{
    /** The first heading opens the output and the second is set off from what came before it. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aHeadingAfterAnotherIsSetOffByABlankLine(): void
    {
        $output = new BufferedOutput();

        Voice::heading($output, 'First');
        Voice::row($output, 'one');
        Voice::heading($output, 'Second');

        self::assertSame("First\n  one\n\nSecond\n", $output->fetch());
    }

    /** The exit code is the verdict, and the failing sentence defaults to the fine one. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aVerdictIsTheExitCodeWithItsMarkBeforeTheSentence(): void
    {
        $output = new BufferedOutput();

        self::assertSame(0, Voice::verdict($output, 0, 'Every link resolves.'));
        self::assertSame(1, Voice::verdict($output, 2, '2 problems'));
        self::assertSame(1, Voice::verdict($output, 1, 'fine', 'wrong'));

        self::assertSame("✓ Every link resolves.\n✗ 2 problems\n✗ wrong\n", $output->fetch());
    }

    /** The colour is on the mark alone, so the sentence a pipe reads is the sentence a terminal reads. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function theColourIsOnTheMarkAndNotInTheWords(): void
    {
        $decorated = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $plain = new BufferedOutput();

        Voice::ok($decorated, 'held');
        Voice::ok($plain, 'held');

        self::assertSame("\033[32m✓\033[39m held\n", $decorated->fetch());
        self::assertSame("✓ held\n", $plain->fetch());
    }

    /** A problem goes where `Cli::errors()` sends it, so the verdict on stdout survives a pipe. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aProblemIsWrittenToTheErrorStream(): void
    {
        $output = new ConsoleOutput();
        $errors = new BufferedOutput();
        $output->setErrorOutput($errors);

        Voice::problem($output, 'D-X-001 has no title');

        self::assertSame("✗ D-X-001 has no title\n", $errors->fetch());
    }

    /** What a caller writes is printed as written, a tag-shaped placeholder included. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aPlaceholderInAngleBracketsIsPrintedAsWritten(): void
    {
        $output = new BufferedOutput();

        Voice::note($output, 'bin/cli todo:home <id>');

        self::assertSame("bin/cli todo:home <id>\n", $output->fetch());
    }

    /** A key is padded before it is coloured, so the column it heads still lines up. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aKeyIsPaddedToItsColumnBeforeItIsColoured(): void
    {
        $decorated = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $plain = new BufferedOutput();

        Voice::row($decorated, Voice::key('12.4', 6) . ' ' . Voice::dim('2026-09-05'));
        Voice::row($plain, Voice::key('12.4', 6) . ' ' . Voice::dim('2026-09-05'));

        self::assertSame("  \033[36m12.4  \033[39m \033[90m2026-09-05\033[39m\n", $decorated->fetch());
        self::assertSame("  12.4   2026-09-05\n", $plain->fetch());
    }

    /** A bar is drawn on a terminal and leaves no trace in a pipe, where a log would be a line per redraw. */
    #[Decision('D-DOC-067')]
    #[Test]
    public function aProgressBarIsDrawnOnATerminalAndNotInAPipe(): void
    {
        $terminal = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        $pipe = new BufferedOutput();

        foreach ([$terminal, $pipe] as $output) {
            $bar = Voice::progress($output, 3);
            $bar->setMessage('render');
            $bar->start();
            $bar->advance();
            $bar->finish();
        }

        self::assertStringContainsString('render', $terminal->fetch());
        self::assertSame('', $pipe->fetch());
    }

    /** One of a thing is named in the singular, and the plural is the regular one unless named. */
    #[Test]
    public function aCountTakesTheFormItsNumberDoes(): void
    {
        self::assertSame('1 problem', Voice::count(1, 'problem'));
        self::assertSame('3 problems', Voice::count(3, 'problem'));
        self::assertSame('0 problems', Voice::count(0, 'problem'));
        self::assertSame('2 branches', Voice::count(2, 'branch', 'branches'));
    }
}
