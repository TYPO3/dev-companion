<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolCalls;
use TYPO3\DevCompanion\Upkeep\ToolSurface;

/**
 * The tool reference against the registry it derives from.
 *
 * A generated page nothing reads back is a hand-written one with one generation
 * behind it. This is also where a tool on offer and described nowhere outward
 * fails, since the index below is the list. `readme.md` carried a hand-written
 * one until five tools had arrived since anybody checked, and a generator
 * replaced it.
 */
final class ToolSurfaceTest extends TestCase
{
    /**
     * The page derives from the declarations rather than stands beside them,
     * schemas included. So neither half can go stale against the other —
     * `D-DOC-016`, `D-DOC-011`. The initialize page derives from a session
     * against the built server, and this holds it the same way — `D-DOC-071`.
     */
    #[Decision('D-DOC-033')]
    #[Decision('D-DOC-011')]
    #[Decision('D-DOC-016')]
    #[Decision('D-DOC-071')]
    #[Test]
    public function everyPageIsWhatTheServerDeclares(): void
    {
        foreach (ToolSurface::pages() as $file => $contents) {
            $named = substr($file, strlen(Paths::root()) + 1);
            self::assertFileExists($file, $named . ' — run bin/cli tools:index');
            self::assertSame(
                $contents,
                (string) file_get_contents($file),
                $named . ' is not what the server declares — run bin/cli tools:index',
            );
        }
    }

    /**
     * What the comparison above cannot say on its own: that the directory is
     * the whole surface and nothing besides. A renderer that dropped every tool
     * would agree with a directory that had none. A tool that left the registry
     * leaves a page behind that reads like one it still offers — `D-DOC-029`,
     * `D-SCO-011`, `D-KNW-035`, `D-DOC-007`.
     */
    #[Decision('D-DOC-007')]
    #[Decision('D-DOC-029')]
    #[Decision('D-KNW-035')]
    #[Decision('D-SCO-011')]
    #[Test]
    public function theIndexReachesEveryToolAndTheDirectoryHoldsNoOther(): void
    {
        $pages = ToolSurface::pages();
        $index = (string) file_get_contents(ToolSurface::index());
        self::assertStringContainsString('.. grid:: wide', $index);

        foreach (array_column(Registry::definitions(), 'name') as $name) {
            self::assertArrayHasKey(ToolSurface::file($name), $pages, $name . ' is offered and rendered nowhere');
            self::assertStringContainsString('<' . $name . '>`', $index, $name . ' is on no line of the index');
            self::assertStringContainsString(
                '.. card:: :doc:`' . $name,
                $index,
                $name . ' has no catalog card',
            );
        }

        foreach (ToolSurface::written() as $written) {
            self::assertArrayHasKey(
                $written->getPathname(),
                $pages,
                $written->getFilename() . ' is a page for a tool the registry does not offer',
            );
        }
    }

    /**
     * Every page either carries what the tool answered or says why it has none.
     *
     * Neither happened for the two feedback tools. The head of the surface
     * promised a record for every tool, and the renderer emitted nothing where
     * there was none. So the promise was false in the one direction a reader
     * cannot check: an absent record and a forgotten one look the same —
     * `D-DOC-007`.
     */
    #[Decision('D-DOC-007')]
    #[Test]
    public function everyToolCarriesItsAnswerOrSaysWhyItHasNone(): void
    {
        foreach (array_column(Registry::definitions(), 'name') as $name) {
            $page = (string) preg_replace('/\s+/', ' ', (string) file_get_contents(ToolSurface::file($name)));

            if (!isset(ToolCalls::undriven()[$name])) {
                self::assertNotSame('', ToolAnswers::recordedIn(ToolSurface::file($name)), $name . ' answered nowhere');

                continue;
            }

            self::assertStringContainsString(
                (string) preg_replace('/\s+/', ' ', ToolCalls::undriven()[$name]),
                $page,
                $name . ' has no recorded answer and its page does not say why',
            );
        }
    }

    /**
     * The measurement reads the corpus that already stands, so a page whose
     * shape changes takes the counter with it rather than reports zero.
     *
     * The zero is the failure this holds against. The first parser read the
     * `Data:` label before it closed the text block above it. Every tool came
     * back with no text at all and a total that still looked plausible.
     */
    #[Test]
    public function whatAToolAnswersWithIsCountedInBothHalves(): void
    {
        $measured = ToolAnswers::measured();
        $answering = array_values(array_filter(
            $measured,
            static fn(array $tool): bool => $tool['calls'] > 0,
        ));

        self::assertNotSame([], $answering, 'no tool page carries a recorded answer');
        foreach ($answering as $tool) {
            self::assertGreaterThan(0, $tool['text'], $tool['tool'] . ' answers with no text');
            self::assertGreaterThan(0, $tool['data'], $tool['tool'] . ' answers with no data');
            self::assertSame($tool['text'] + $tool['data'], $tool['total'], $tool['tool'] . ' does not add up');
        }

        $totals = array_column($measured, 'total');
        $sorted = $totals;
        rsort($sorted);
        self::assertSame($sorted, $totals, 'the report does not open on what costs the most');
    }
}
