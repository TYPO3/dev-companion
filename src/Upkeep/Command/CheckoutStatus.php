<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\PinnedPackage;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * What is below .checkouts/, and how old it is.
 *
 * The packages the core pins are reported beside the core branches, because a
 * statement about one of them is verified against a tag of that package rather
 * than against a core branch (D-KNW-106).
 */
#[AsCommand(
    name: 'checkouts:status',
    description: 'what exists, at which revision',
)]
final class CheckoutStatus
{
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();
        Voice::heading($output, sprintf('Core checkouts below %s', $checkouts));
        foreach (Versions::covered() as $version) {
            $path = $checkouts . '/' . $version['branch'];
            Voice::row($output, sprintf(
                '%s %s',
                Voice::key($version['branch'], 6),
                is_dir($path . '/typo3/sysext/core') ? Checkouts::revision($path) : 'missing — run bin/cli checkouts:update',
            ));
        }

        foreach (PinnedPackage::all() as $package) {
            Voice::heading($output, sprintf('%s, one release line per pin', $package->package));
            foreach ($package->pairing($checkouts) as $pair) {
                Voice::row($output, sprintf(
                    '%s %-9s %s',
                    Voice::key($pair['branch'], 6),
                    $pair['constraint'] === '' ? 'no pin' : $pair['constraint'],
                    // A worktree that is not there answers nothing, which is
                    // what says it is missing: the source directory a package
                    // keeps its classes in is its own and says nothing here.
                    Checkouts::revision($pair['path']) ?: 'missing — run bin/cli checkouts:update',
                ));
            }
        }

        return 0;
    }
}
