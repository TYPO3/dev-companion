<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Upkeep\ToolSurface;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Writes the tool reference back from the registry.
 *
 * What this replaces is the surface being written twice: the classes declare
 * what a caller can see of a tool, and the only place it was readable without
 * calling the server was a list of names in the readme. The fields a tool
 * answers with were written down nowhere at all.
 *
 * What a tool answered is not rewritten. It is carried over from the page as it
 * stands, because `tools:record` is the only thing that can produce it.
 */
#[AsCommand(
    name: 'tools:index',
    description: 'rewrite the tool reference and answer-source page from the registry',
)]
final class ToolIndex
{
    public function __invoke(OutputInterface $output): int
    {
        $pages = ToolSurface::pages();
        if (!is_dir(ToolSurface::directory())) {
            mkdir(ToolSurface::directory(), 0777, true);
        }
        foreach ($pages as $file => $contents) {
            file_put_contents($file, $contents);
        }

        foreach (ToolSurface::written() as $written) {
            if (!isset($pages[$written->getPathname()])) {
                unlink($written->getPathname());
                Voice::row($output, sprintf('removed %s, which the registry no longer offers', $written->getFilename()));
            }
        }

        Voice::ok($output, sprintf(
            '%s — %d pages',
            substr(ToolSurface::index(), strlen(Paths::root()) + 1),
            count($pages) - count(ToolSurface::standingPages()),
        ));

        return 0;
    }
}
