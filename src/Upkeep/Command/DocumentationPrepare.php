<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Site;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Writes the copy a renderer publishes, and nothing else.
 *
 * This is the half of a render that belongs to this repository: something over a
 * third of the links below `documentation/` point at a decision, a requirement
 * or a class that no visitor of the site has, and the copy is where each of them
 * becomes the file on GitHub. It also publishes a directory's `readme.md` as the
 * `index.md` a generator serves as the directory itself. It needs no renderer,
 * no theme and no network — `D-DOC-028` is why the two halves are two commands.
 */
#[AsCommand(
    name: 'documentation:prepare',
    description: 'write the copy a renderer publishes, with every link that leaves the tree rewritten',
)]
final class DocumentationPrepare
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('where the site is built; `documentation/guides.xml` renders the default one')]
        string $into = Site::ROOT,
    ): int {
        $built = Site::build($into . '/source');
        foreach ($built['removed'] as $removed) {
            Voice::row($output, sprintf('removed %s, which documentation/ no longer has', $removed));
        }
        Voice::ok($output, sprintf('%s — %d files, %s', $into . '/source', count($built['written']), Site::repository()));

        return 0;
    }
}
