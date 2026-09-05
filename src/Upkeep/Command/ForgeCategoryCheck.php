<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Knowledge\Catalog\SystemExtensions;
use TYPO3\DevCompanion\Upkeep\Voice;

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
            Voice::problem($output, 'forge.typo3.org answered no areas, so nothing could be read against them.');

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
            Voice::row($output, sprintf(
                '%s %-40s %s',
                Voice::key($entry['key'], 20),
                $entry['forgeCategory'],
                $stands ? 'stands' : 'no such area',
            ));
            if ($stands) {
                continue;
            }

            ++$problems;
            Voice::problem($output, sprintf(
                '%s is filed under "%s" here, and the project has no area of that name.',
                $entry['key'],
                $entry['forgeCategory'],
            ));
        }

        return Voice::verdict(
            $output,
            $problems,
            sprintf('%s against the %d areas the project publishes.', Voice::count($mapped, 'mapped extension'), count($areas)),
            Voice::count($problems, 'mapped extension') . ' name an area the tracker no longer has.',
        );
    }
}
