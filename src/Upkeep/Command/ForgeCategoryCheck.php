<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Knowledge\Catalog\SystemExtensions;
use TYPO3\DevCompanion\Upkeep\Cli;

/**
 * Whether the areas the system extension catalog names are areas the tracker
 * still has.
 *
 * The core administers that vocabulary and renames an area without telling
 * anybody, and a `forgeCategory` naming one that is gone resolves to nothing:
 * the caller is back at the empty answer the mapping exists to save them
 * (`D-ANS-142`). It reads forge.typo3.org, which is what makes it a command
 * rather than a test.
 */
#[AsCommand(
    name: 'forge-categories:check',
    description: 'the tracker area each system extension files its issues under, against what the project publishes',
)]
final class ForgeCategoryCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $areas = (new Forge())->categories();
        if ($areas === []) {
            Cli::errors($output)->writeln('forge.typo3.org answered no areas, so nothing could be read against them.');

            return 2;
        }

        $problems = 0;
        $mapped = 0;
        foreach (SystemExtensions::load() as $entry) {
            if ($entry['forgeCategory'] === '') {
                continue;
            }

            ++$mapped;
            $stands = isset($areas[$entry['forgeCategory']]);
            $output->writeln(sprintf(
                '  %-20s %-40s %s',
                $entry['key'],
                $entry['forgeCategory'],
                $stands ? 'stands' : 'no such area',
            ));
            if ($stands) {
                continue;
            }

            ++$problems;
            Cli::errors($output)->writeln(sprintf(
                '    %s is filed under "%s" here, and the project has no area of that name.',
                $entry['key'],
                $entry['forgeCategory'],
            ));
        }

        $output->writeln('');
        if ($problems === 0) {
            $output->writeln(sprintf(
                '%d mapped extension(s) against the %d areas the project publishes.',
                $mapped,
                count($areas),
            ));

            return 0;
        }

        $output->writeln(sprintf('%d mapped extension(s) name an area the tracker no longer has.', $problems));

        return 1;
    }
}
