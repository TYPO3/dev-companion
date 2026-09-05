<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Upkeep\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Upkeep\Catalogs;
use TYPO3\DevCompanion\Upkeep\Checkouts;
use TYPO3\DevCompanion\Upkeep\RangeReport;
use TYPO3\DevCompanion\Upkeep\Voice;

/**
 * Whether the recorded system extensions are the ones the checkouts ship.
 *
 * The list is per major and moves with a release, so what it says has to be
 * re-read rather than trusted.
 */
#[AsCommand(
    name: 'system-extensions:check',
    description: 'which system extensions each covered major ships, against .checkouts/',
)]
final class SystemExtensionCheck
{
    public function __invoke(OutputInterface $output): int
    {
        $checkouts = Checkouts::directory();

        return self::verifySystemExtensions($output, $checkouts, Catalogs::read('system-extension/entries'));
    }

    /**
     * Re-reads which system extensions each covered version ships, and reports
     * every difference from what the catalog records.
     *
     * Nothing is judged here: the extension keys below typo3/sysext are the answer,
     * and the Composer package name is what that directory's own composer.json says.
     * A core release that adds or drops a system extension therefore invalidates the
     * catalog loudly rather than leaving it to be noticed by a caller.
     *
     * @param array<int, array<string, mixed>> $recorded
     */
    private static function verifySystemExtensions(OutputInterface $output, string $checkouts, array $recorded): int
    {
        Voice::heading($output, 'System extensions');
        $covered = Versions::covered();
        $shipped = [];
        foreach ($covered as $version) {
            $directory = $checkouts . '/' . $version['branch'] . '/typo3/sysext';
            if (!is_dir($directory)) {
                Voice::problem($output, sprintf('No checkout for TYPO3 v%d below %s — run bin/cli checkouts:update.', $version['major'], $checkouts));

                return 2;
            }
            foreach (Finder::create()->directories()->in($directory)->depth(0)->sortByName() as $extension) {
                $manifest = $extension->getPathname() . '/composer.json';
                if (!is_file($manifest)) {
                    continue;
                }
                $shipped[$extension->getFilename()][$version['major']] = (string) (json_decode((string) file_get_contents($manifest), true)['name'] ?? '');
            }
        }

        $problems = 0;
        $byKey = array_column($recorded, null, 'key');
        foreach ($shipped as $key => $packages) {
            $majors = array_keys($packages);
            $entry = $byKey[$key] ?? null;
            if ($entry === null) {
                Voice::problem($output, sprintf('%s: shipped on v%s, not in the catalog', $key, implode(', v', $majors)));
                ++$problems;
                continue;
            }
            $problems += RangeReport::of($output, $key, $entry, $majors, array_column($covered, 'major'));

            $package = end($packages);
            if (($entry['package'] ?? '') !== $package) {
                Voice::problem($output, sprintf('%s: records package %s, ships as %s', $key, (string) ($entry['package'] ?? ''), $package));
                ++$problems;
            }
        }
        foreach ($byKey as $key => $entry) {
            if (!isset($shipped[$key])) {
                Voice::problem($output, sprintf('%s: in the catalog, shipped by no covered version', $key));
                ++$problems;
            }
        }
        Voice::row($output, sprintf('%d system extensions against %s', count($shipped), implode(', ', array_column($covered, 'branch'))));

        return Voice::verdict($output, $problems, 'Every system extension is recorded as the checkouts ship it.', Voice::count($problems, 'system extension') . ' out of date.');
    }
}
