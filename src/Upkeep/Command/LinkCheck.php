<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Links;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether the paths this repository writes between its own files still resolve.
 *
 * This one fails rather than reports. A long sentence can be the right sentence
 * and `prose:check` says so; a link to a file that is not there is wrong in
 * every reading, and the reader who follows it is otherwise the check.
 */
#[AsCommand(
    name: 'links:check',
    description: 'every path this repository writes between its own files',
)]
final class LinkCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $dead = Links::dead();
        $unrendered = Links::unrendered();
        $repairable = 0;
        foreach ($dead as $link) {
            if ($link['repair'] === null) {
                Voice::problem($output, sprintf('%s:%d links to %s, which is not there', $link['file'], $link['line'], $link['link']));
                continue;
            }
            ++$repairable;
            Voice::problem($output, sprintf(
                '%s:%d links to %s, and that feedback was answered into %s',
                $link['file'],
                $link['line'],
                $link['link'],
                $link['repair'],
            ));
        }
        foreach ($unrendered as $link) {
            Voice::problem($output, sprintf('%s:%d writes %s in markdown, which this page renders as itself', $link['file'], $link['line'], $link['link']));
        }
        if ($repairable > 0) {
            Voice::note($output, sprintf('%d of them `bin/cli links:repair` repoints at the archive.', $repairable));
        }

        return Voice::verdict(
            $output,
            count($dead) + count($unrendered),
            'Every link resolves.',
            sprintf('%s, %d written in the wrong markup.', Voice::count(count($dead), 'dead link'), count($unrendered)),
        );
    }
}
