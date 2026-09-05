<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether every path the component catalog names still exists in one checkout.
 *
 * Two things can be wrong with an entry, and each has its own command: the
 * paths it names are gone from a checkout — this one — or its `since`/`until`
 * no longer say which versions it holds on, which is `components:check`. This one
 * reads a checkout of the caller's own rather than .checkouts/, because it
 * answers a question about that checkout.
 */
#[AsCommand(
    name: 'components:paths',
    description: 'the paths one entry names, against one core checkout of your own',
)]
final class ComponentPaths
{
    public function __invoke(
        OutputInterface $output,
        #[Argument('a TYPO3 core checkout to read the paths against')]
        string $checkout,
    ): int {
        $coreRoot = rtrim($checkout, '/');
        if (!is_dir($coreRoot . '/typo3/sysext/core')) {
            Voice::problem($output, sprintf('Not a TYPO3 core checkout: %s', $coreRoot));

            return 2;
        }

        $components = Catalogs::read('component/entries');
        Voice::heading($output, 'Components');
        $problems = 0;
        foreach ($components as $component) {
            $paths = $component['sassPaths'] ?? [];
            if (isset($component['sassPath'])) {
                $paths[] = $component['sassPath'];
            }
            if (isset($component['demoPath'])) {
                $paths[] = $component['demoPath'];
            }
            foreach (array_unique($paths) as $path) {
                if (is_string($path) && $path !== '' && !file_exists($coreRoot . '/' . $path)) {
                    Voice::problem($output, $component['name'] . ' names ' . $path . ', which is gone');
                    ++$problems;
                }
            }
        }
        Voice::row($output, sprintf('%d components', count($components)));

        if ($problems === 0) {
            [$exitCode, $said] = Checkouts::run(['git', '-C', $coreRoot, 'rev-parse', 'HEAD']);
            $revision = $exitCode === 0 ? trim($said) : '';
            Voice::ok($output, sprintf('No drift against %s%s', $coreRoot, $revision === '' ? '' : ' @ ' . substr($revision, 0, 12)));

            return 0;
        }

        Voice::wrong($output, Voice::count($problems, 'problem') . ' found.');

        return 1;
    }
}
