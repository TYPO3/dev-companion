<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\ToolAnswers;
use TYPO3\DevCompanion\Upkeep\ToolSurface;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether the tool reference still says what the registry declares, and how far
 * behind the recorded half of it is.
 *
 * A generated page nothing reads back is a hand-written one that was generated
 * once: a tool added, a description rewritten or a schema field gained leaves
 * it standing, and it goes on being read. `composer test` runs the same
 * comparison through ToolSurfaceTest; this is the readable half.
 */
#[AsCommand(
    name: 'tools:check',
    description: 'hold the tool reference to what the registry declares, and say how old its recorded half is',
)]
final class ToolCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $pages = ToolSurface::pages();

        $stale = [];
        foreach ($pages as $file => $contents) {
            if (!is_file($file) || (string) file_get_contents($file) !== $contents) {
                $stale[] = substr($file, strlen(Paths::root()) + 1);
            }
        }
        foreach (ToolSurface::written() as $written) {
            if (!isset($pages[$written->getPathname()])) {
                $stale[] = substr($written->getPathname(), strlen(Paths::root()) + 1);
            }
        }

        foreach ($stale as $file) {
            Voice::problem($output, $file . ' is not what the registry declares — run bin/cli tools:index');
        }
        $verdict = Voice::verdict($output, count($stale), sprintf('%d tools, %s', count(Registry::definitions()), Voice::count(count($stale), 'problem')));
        self::howOldTheRecordingIs($output);

        return $verdict;
    }

    /**
     * How far the recorded half of the surface is behind what it answers from.
     *
     * It reports and never fails, because a recording is evidence about a day
     * and a command only a machine with `.checkouts/` can re-run may not turn
     * anything red — `D-DOC-006`, whose second **Wrong if** is a recording
     * nobody re-runs and nothing that asks. This is the asking, and it is here
     * rather than in `unresolved:list` because the reader who can answer it is
     * the one already looking at this surface — `D-DOC-058`.
     */
    private static function howOldTheRecordingIs(OutputInterface $output): void
    {
        $moved = ToolAnswers::sourcesMovedOn();
        $recorded = ToolAnswers::recordedOn();
        if ($moved === null || $recorded === []) {
            return;
        }

        $behind = array_filter($recorded, static fn(string $day): bool => $day < $moved);
        if ($behind === []) {
            Voice::note($output, sprintf(
                '%d recorded pages, none of them older than knowledge/ and src/ on %s.',
                count($recorded),
                $moved,
            ));

            return;
        }

        Voice::note($output, sprintf(
            '%d of %d recorded pages are older than knowledge/ and src/, which last moved on %s.',
            count($behind),
            count($recorded),
            $moved,
        ));
        Voice::note($output, sprintf(
            'The oldest is from %s, and bin/cli tools:record answers them again.',
            min($behind),
        ));
    }
}
