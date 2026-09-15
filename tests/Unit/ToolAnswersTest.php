<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Process\CommandRunner;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolCalls;
use TYPO3\DevCompanion\Upkeep\ToolSurface;

/**
 * The record of what the tools answered, as far as a test can hold it here.
 *
 * Not that it is current. It is a run against an installation, and no test run
 * has one. Pages a command only some machines can produce may not turn the
 * suite red. This holds their shape. Every answer is JSON a reader can paste
 * anywhere, and no absolute path survives into a page every reader of this
 * package gets.
 */
final class ToolAnswersTest extends TestCase
{
    /**
     * The record is evidence a reader can paste anywhere rather than a
     * derivation. That is what the shape holds and all it can — `D-DOC-006`,
     * `D-DOC-007`.
     */
    #[Decision('D-DOC-006')]
    #[Decision('D-DOC-007')]
    #[Test]
    public function everyRecordedAnswerIsJson(): void
    {
        $found = 0;
        foreach (self::recorded() as $name => $section) {
            foreach (self::blocks($section) as [$language, $block]) {
                if ($language !== 'json') {
                    continue;
                }
                ++$found;
                self::assertNotNull(
                    json_decode($block, true),
                    'a recorded block is not JSON: ' . $name . ' — ' . substr($block, 0, 120),
                );
            }
        }

        self::assertGreaterThan(0, $found, 'the recording carries no data at all');
    }

    /**
     * Every call on a page carries its arguments and each answer it got.
     *
     * What this used to guard is gone with the markdown — `D-DOC-029`. Half of
     * these answers are documents themselves. In a fenced corpus an answer's
     * own end fence ended the block around it. A directive has no end marker
     * for an answer to imitate. The count stays, because it is what says a page
     * holds what it claims to. One set of arguments per call, and a text and a
     * data answer for each — `D-DOC-007`.
     */
    #[Decision('D-DOC-007')]
    #[Decision('D-DOC-029')]
    #[Test]
    public function everyCallOnAPageCarriesItsArgumentsAndItsAnswers(): void
    {
        foreach (self::recorded() as $name => $section) {
            $outside = self::outsideBlocks($section);
            $calls = preg_match_all('/^~{2,}$/m', $outside);
            $answers = preg_match_all('/^Data:$/m', $outside);

            self::assertGreaterThanOrEqual($calls, $answers, $name . ': answers per call');
            self::assertSame($calls + $answers * 2, count(self::blocks($section)), $name . ': blocks');
            self::assertSame(
                $calls,
                preg_match_all('/^Called with:$/m', $outside),
                $name . ': ' . $calls . ' calls, and this many "Called with:" left outside a block',
            );
            self::assertSame(
                $answers,
                preg_match_all('/^Text:$/m', $outside),
                $name . ': ' . $answers . ' answers, and this many "Text:" left outside a block',
            );
        }
    }

    /**
     * A page with two answers per call has to say which is which, or it is two
     * records a reader cannot tell apart. What tells them apart is the whole
     * reason the second one is there — `D-DOC-006`.
     */
    #[Decision('D-DOC-006')]
    #[Test]
    public function everyAnswerOnAPageOfTwoRecordingsSaysWhichItCameFrom(): void
    {
        foreach (self::recorded() as $name => $section) {
            $outside = self::outsideBlocks($section);
            $answers = preg_match_all('/^Data:$/m', $outside);
            if ($answers === preg_match_all('/^~{2,}$/m', $outside)) {
                continue;
            }

            self::assertSame(
                $answers,
                preg_match_all('/^From .+\n"{2,}$/m', $outside),
                $name . ': answers, and this many saying which recording they are of',
            );
        }
    }

    /**
     * The pages ship inside this package, so a path from the machine that
     * recorded them would be in every checkout of it. The substitutions are
     * `ToolAnswers`' own and the surface says they happened — `D-DOC-006`.
     */
    #[Decision('D-DOC-006')]
    #[Test]
    public function theRecordingCarriesNobodysDirectoryLayout(): void
    {
        $home = (string) getenv('HOME');

        foreach (ToolSurface::written() as $page) {
            self::assertStringNotContainsString(Paths::root(), $page->getContents(), $page->getFilename());
            if ($home !== '') {
                self::assertStringNotContainsString($home, $page->getContents(), $page->getFilename());
            }
        }
    }

    /**
     * The record is of the table the contract test drives, so a call added to
     * one is a call the other shows. It may be older than the table, which is
     * the whole point of a record rather than a check. So the assertion is that
     * every tool in the table has answered, not that the pages match call for
     * call — `D-KNW-035`, `D-DOC-006`, `D-DOC-007`.
     */
    #[Decision('D-DOC-006')]
    #[Decision('D-DOC-007')]
    #[Decision('D-KNW-035')]
    #[Test]
    public function everyToolTheTableDrivesHasARecordedAnswer(): void
    {
        $missing = [];
        foreach (array_unique(array_column(ToolCalls::all(), 0)) as $name) {
            if (ToolAnswers::recordedIn(ToolSurface::file($name)) === '') {
                $missing[] = $name;
            }
        }

        self::assertSame([], $missing, 'driven by the table and recorded nowhere — run bin/cli tools:record');
    }

    /**
     * Every answer on a page is one the tool could have given.
     *
     * A record was evidence and therefore had no hold on it. It said what came
     * back on a day, and a field that has since left the schema stays on show
     * to every reader. What the pages are for is the endpoint and the states it
     * answers in. A state shown by an answer the schema no longer allows shows
     * nothing.
     *
     * This is the one thing a check reaches without an installation. The answer
     * is on the page and the schema is in the class. So a test can hold the two
     * to each other wherever the suite runs — `D-DOC-012`.
     */
    #[Decision('D-DOC-012')]
    #[Test]
    public function everyAnswerOnAPageIsOneItsSchemaAllows(): void
    {
        $schemas = [];
        foreach (Registry::definitions() as $definition) {
            $schemas[$definition['name']] = $definition['outputSchema'] ?? [];
        }

        $validator = new SchemaValidator();
        $broken = [];
        foreach (self::recorded() as $name => $section) {
            foreach (self::answers($section) as $state => $answer) {
                $errors = $validator->validateAgainstJsonSchema(json_decode($answer), $schemas[$name]);
                if ($errors !== []) {
                    $broken[] = $name . ' — ' . $state . ': ' . implode(' ', array_column($errors, 'message'));
                }
            }
        }

        // All of them, because one at a time says a page is stale. The list
        // says how far the record as a whole has drifted from the classes.
        self::assertSame([], $broken, 'answers no schema allows');
    }

    /**
     * A record that does not say which day it is of looks like a current one.
     * The day is what the report that asks for a new record stands on —
     * `D-DOC-006`, `D-DOC-058`.
     */
    #[Decision('D-DOC-006')]
    #[Decision('D-DOC-058')]
    #[Test]
    public function everyRecordedPageSaysWhichDayItWasAnsweredOn(): void
    {
        // Every tool with a recorded answer and not a derived one. The derived
        // half carries the same heading and says `tools:check` holds it. A day
        // on one of those pages would ask for a record nothing records.
        $recorded = array_diff(
            array_column(Registry::definitions(), 'name'),
            ToolCalls::derived(),
            array_keys(ToolCalls::undriven()),
        );
        sort($recorded);

        self::assertSame($recorded, array_keys(ToolAnswers::recordedOn()));
        foreach (ToolAnswers::recordedOn() as $name => $day) {
            self::assertSame($day, date('Y-m-d', (int) strtotime($day)), $name . ' carries no day');
        }
    }

    /**
     * The day the sources moved is git's answer. A checkout that cannot take
     * the question reports nothing rather than a record that is current —
     * `D-DOC-058`.
     */
    #[Decision('D-DOC-058')]
    #[Test]
    public function theDayTheSourcesMovedIsWhatGitSaysOrNothing(): void
    {
        self::answering(0, "1787572800\n");
        self::assertSame('2026-08-24', ToolAnswers::sourcesMovedOn());

        // No git, or a checkout without history. Either way nothing here can
        // say a page is behind. A report of it anyway would ask for a record on
        // the strength of a question with no answer.
        self::answering(128, "fatal: not a git repository\n");
        self::assertNull(ToolAnswers::sourcesMovedOn());
    }

    /**
     * One instant is one day on both sides of the comparison, wherever the
     * machine that does it thinks it stands — `D-DOC-059`.
     */
    #[Decision('D-DOC-059')]
    #[Test]
    public function bothDaysTheReportComparesAreTheUtcOne(): void
    {
        // 22:16 UTC, which is 00:16 the next day in Berlin. Those are the hours
        // a record made in Europe read as a day behind a commit made a minute
        // before it.
        $instant = 1787782560;
        date_default_timezone_set('Europe/Berlin');
        self::answering(0, $instant . "\n");

        self::assertSame('2026-08-26', ToolAnswers::day($instant));
        self::assertSame(ToolAnswers::day($instant), ToolAnswers::sourcesMovedOn());
    }

    /** What this process had before a test moved it. */
    private static string $zone = '';

    #[Before]
    protected function rememberTheTimeZone(): void
    {
        self::$zone = date_default_timezone_get();
    }

    #[After]
    protected function putBackWhatTheTestChanged(): void
    {
        Checkouts::useRunner(null);
        date_default_timezone_set(self::$zone);
    }

    private static function answering(int $exitCode, string $output): void
    {
        $git = self::createStub(CommandRunner::class);
        $git->method('run')->willReturn(
            ['ok' => $exitCode === 0, 'exitCode' => $exitCode, 'output' => $output, 'error' => ''],
        );
        Checkouts::useRunner($git);
    }

    /**
     * The data half of every answer in a section, by the state it is under.
     *
     * A state answered from two working directories carries two, and both are
     * the same tool's. The key keeps them apart by the heading each sits under.
     *
     * @return array<string, string>
     */
    private static function answers(string $section): array
    {
        $blocks = self::blocks($section);
        $answers = [];
        $state = '';
        $from = '';
        $index = 0;

        // A heading is a line and the rule under it. So what says which of the
        // two this is stands below rather than in front — `D-DOC-029`.
        $lines = explode("\n", self::outsideBlocks($section));
        foreach ($lines as $at => $line) {
            $under = $lines[$at + 1] ?? '';
            if (preg_match('/^~{2,}$/', $under) === 1) {
                $state = $line;
                $from = '';
            }
            if (preg_match('/^"{2,}$/', $under) === 1 && str_starts_with($line, 'From ')) {
                $from = ', ' . substr($line, 5);
            }
            if ($line === 'Called with:') {
                ++$index;
            }
            if ($line === 'Data:') {
                $answers[$state . $from] = $blocks[$index + 1][1] ?? '';
                $index += 2;
            }
        }

        return $answers;
    }

    /**
     * Every tool the table leaves out says why it is out.
     *
     * This used to name the two in the assertion itself, which held the list
     * and nothing else. A third tool that dropped out failed here, and the
     * cheapest way to make it pass again was to add its name. The list is
     * `ToolCalls::undriven()` now, so a green run here means a written reason.
     * The reason is what the tool's own page then states where a reader meets
     * the absence — `D-DOC-007`.
     */
    #[Decision('D-DOC-007')]
    #[Test]
    public function everyToolTheTableLeavesOutSaysWhy(): void
    {
        $driven = array_unique(array_column(ToolCalls::all(), 0));
        $offered = array_column(Registry::definitions(), 'name');

        self::assertSame(
            array_values(array_diff($offered, $driven)),
            array_keys(ToolCalls::undriven()),
            'a tool joined or left the table without its reason being written down',
        );
        foreach (ToolCalls::undriven() as $name => $why) {
            self::assertNotSame('', trim($why), $name . ' is left out of the table and says nothing about why');
        }
    }

    /**
     * The recorded half of every page that has one, by the tool it is of.
     *
     * @return array<string, string>
     */
    private static function recorded(): array
    {
        $sections = [];
        foreach (array_column(Registry::definitions(), 'name') as $name) {
            $section = ToolAnswers::recordedIn(ToolSurface::file($name));
            if ($section !== '') {
                $sections[$name] = $section;
            }
        }

        return $sections;
    }

    /**
     * The code blocks of a section, by the rule a renderer reads them with. A
     * directive owns every line indented past it, and a line at its own column
     * or left of it is where it ends.
     *
     * @return list<array{0: string, 1: string}>
     */
    private static function blocks(string $page): array
    {
        $blocks = [];
        foreach (self::split($page) as [$language, $content]) {
            if ($language !== null) {
                $blocks[] = [$language, implode("\n", $content)];
            }
        }

        return $blocks;
    }

    /**
     * The section with its blocks taken out, so what remains is its own text.
     */
    private static function outsideBlocks(string $page): string
    {
        $outside = [];
        foreach (self::split($page) as [$language, $content]) {
            if ($language === null) {
                $outside = [...$outside, ...$content];
            }
        }

        return implode("\n", $outside);
    }

    /**
     * The page as its blocks and the text between them, in order.
     *
     * Both readers above want the same split and disagreed about it when they
     * each did their own. The markdown these replaced had two copies of one
     * fence rule.
     *
     * @return list<array{0: string|null, 1: list<string>}>
     */
    private static function split(string $page): array
    {
        $parts = [];
        $language = null;
        $at = 0;
        $content = [];

        foreach (explode("\n", $page) as $line) {
            $indent = strlen($line) - strlen(ltrim($line));

            if ($language !== null) {
                if (trim($line) === '' || $indent > $at) {
                    $content[] = trim($line) === '' ? '' : substr($line, $at + 4);

                    continue;
                }
                // A block keeps no blank line it ended on.
                while ($content !== [] && end($content) === '') {
                    array_pop($content);
                }
                $parts[] = [$language, $content];
                $language = null;
                $content = [];
            }

            if (preg_match('/^(\s*)\.\. code-block::\s*(\S*)\s*$/', $line, $matched) === 1) {
                $parts[] = [null, $content];
                $language = $matched[2];
                $at = strlen($matched[1]);
                $content = [];

                continue;
            }

            $content[] = $line;
        }

        $parts[] = [$language, $content];

        return $parts;
    }
}
