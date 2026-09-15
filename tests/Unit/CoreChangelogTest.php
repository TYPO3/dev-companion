<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * The versions above the installed major, which the installation cannot ship.
 *
 * A package carries every changelog down to 7.0 and nothing above its own, so
 * the entries an upgrade is *to* are exactly the absent ones. 469 of them over
 * six directories for a 13.4 installation, measured 2026-08-08. They come from
 * the one changelog manual docs.typo3.org publishes, and this holds the join.
 * Which side each entry came from, that neither shadows the other, and that the
 * answer names an unreachable host rather than reads it as silence.
 *
 * Nothing here reaches that host. The seam is `CoreChangelog::useReader()` and
 * every body below stands here — `R-COD-003`.
 */
final class CoreChangelogTest extends TestCase
{
    use TemporaryInstallation;

    #[After]
    public function forgetBothSides(): void
    {
        CoreChangelog::useReader(null);
        Instance::discoverFrom(null);
    }

    /**
     * What the installation ships stops at its own major. So docs.typo3.org
     * answers a version above it, and the entry says where it came from —
     * `D-ANS-067`.
     */
    #[Decision('D-ANS-067')]
    #[Test]
    public function anEntryAboveTheInstalledMajorComesFromTheManual(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $this->manualPublishing([
            '13.4/Deprecation-1-SomethingOld' => 'Deprecation: #1 - Something old',
            '15.0/Deprecation-110148-ExperimentalBackendViewHelpers' => 'Deprecation: #110148 - Experimental backend ViewHelpers',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'backend viewhelpers']);

        self::assertSame(1, $result->data['matchCount']);
        $entry = $result->data['entries'][0];
        self::assertSame('15.0', $entry['version']);
        self::assertSame('manual', $entry['publishedIn'], 'the installation ships nothing above 13.4');
        self::assertStringStartsWith('https://docs.typo3.org/', $entry['file'], 'an entry not on disk has no EXT: path');
        self::assertSame(['15.0'], $result->data['versionsFromTheManual']);
    }

    /**
     * A version the installation does ship is that installation's, whatever the
     * manual says about it. The two are never both in one answer. An entry on
     * disk is the code that runs, and the host publishes what the release
     * branch carries today — `D-ANS-067`.
     */
    #[Decision('D-ANS-067')]
    #[Test]
    public function aVersionTheInstallationShipsIsNeverTakenFromTheManual(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $this->manualPublishing([
            '13.4/Deprecation-1-SomethingOld' => 'Deprecation: #1 - Something old',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something old']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame('installation', $result->data['entries'][0]['publishedIn']);
    }

    /**
     * Naming a version the installation ships is the one call that must stay
     * local. It is the ordinary question, what did the release I am on change.
     * It would otherwise pay a round trip for entries the version filter
     * already excluded. On a machine with no network that is a connect timeout
     * — `D-ANS-067`.
     */
    #[Decision('D-ANS-067')]
    #[Test]
    public function askingForAnInstalledVersionReachesNoHostAtAll(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $asked = [];
        CoreChangelog::useReader(function (string $url) use (&$asked): ?string {
            $asked[] = $url;

            return null;
        });

        $result = Registry::call('typo3_changelog_lookup', ['version' => '13.4']);

        self::assertSame([], $asked, 'the answer is complete on disk');
        self::assertArrayNotHasKey('versionsFromTheManual', $result->data, 'nothing was read, so nothing is claimed');
        self::assertStringNotContainsString('docs.typo3.org did not answer', $result->text, 'nothing was asked, so nothing failed');
    }

    /**
     * A host that did not answer is a gap in this answer rather than in the
     * changelog. The difference is what a caller on an upgrade has to know.
     * Silence read as "there is nothing above your major" is the wrong answer
     * to the one question this exists for — `D-ANS-067`.
     */
    #[Decision('D-ANS-067')]
    #[Test]
    public function aHostThatDoesNotAnswerIsSaid(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        CoreChangelog::useReader(static fn(string $url): ?string => null);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something old']);

        self::assertStringContainsString('docs.typo3.org did not answer', $result->text);
        self::assertArrayNotHasKey('versionsFromTheManual', $result->data);
    }

    /**
     * The inventory line carries the stated title, and an installed entry's
     * title is a file read away. Under the field a search reads, a manual entry
     * would answer in the pass that reads file names alone. The installed one
     * it should have found is still there by name only — `D-ANS-067`.
     */
    #[Decision('D-ANS-067')]
    #[Test]
    public function aManualTitleDoesNotShadowTheInstalledEntryAQueryIsAbout(): void
    {
        Instance::discoverFrom($this->installationAt('13.4', [
            '13.4/Breaking-2-RenamedSomething' => 'Breaking: #2 - The frobnicator was removed',
        ]));
        $this->manualPublishing([
            '15.0/Feature-3-SomethingElse' => 'Feature: #3 - A frobnicator for the backend',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'frobnicator']);

        // Neither has the word in its name, so the pass that reads titles
        // reaches both. That pass reads both sides or it reads neither. Newest
        // first, which is the order every answer here is in.
        self::assertSame(
            ['15.0', '13.4'],
            array_column($result->data['entries'], 'version'),
            'the installed entry is not lost to the manual answering one pass earlier',
        );
    }

    /**
     * A composer project that ships one changelog directory per version named,
     * with an entry in each.
     *
     * @param array<string, string> $entries page path to stated title
     */
    private function installationAt(string $major, array $entries = ['13.4/Deprecation-1-SomethingOld' => 'Deprecation: #1 - Something old']): string
    {
        $root = $this->composerProject();
        foreach ($entries as $page => $title) {
            [$version, $name] = explode('/', $page, 2);
            $directory = $root . '/vendor/typo3/cms-core/Documentation/Changelog/' . $version;
            if (!is_dir($directory)) {
                mkdir($directory, 0o777, true);
            }
            file_put_contents($directory . '/' . $name . '.rst', implode("\n", [
                '.. include:: /Includes.rst.txt',
                '',
                str_repeat('=', mb_strlen($title)),
                $title,
                str_repeat('=', mb_strlen($title)),
                '',
                'Description',
                '',
                '..  index:: PHP-API',
                '',
            ]));
        }

        self::assertStringContainsString($major, implode(' ', array_keys($entries)), 'the fixture is at the major it claims');

        return $root;
    }

    /**
     * What the host serves: a Sphinx inventory that names those pages, and the
     * RST of each under `_sources`.
     *
     * The inventory stands here rather than comes from a fetch, in the format
     * the writer produces — four comment lines and then the objects,
     * compressed.
     *
     * @param array<string, string> $pages page name to stated title
     */
    private function manualPublishing(array $pages): void
    {
        $lines = [];
        $sources = [];
        foreach ($pages as $name => $title) {
            $lines[] = sprintf('Changelog/%s std:doc -1 Changelog/%s.html %s', $name, $name, $title);
            $sources['Changelog/' . $name . '.rst.txt'] = implode("\n", [
                str_repeat('=', mb_strlen($title)),
                $title,
                str_repeat('=', mb_strlen($title)),
                '',
                'Description',
                '',
                '..  index:: Fluid, FullyScanned',
                '',
            ]);
        }

        $inventory = "# Sphinx inventory version 2\n# Project: cms-core\n# Version: main\n"
            . "# The remainder of this file is compressed using zlib.\n"
            . (string) gzcompress(implode("\n", $lines));

        CoreChangelog::useReader(static function (string $url) use ($inventory, $sources): ?string {
            if (str_ends_with($url, 'objects.inv')) {
                return $inventory;
            }
            foreach ($sources as $path => $rst) {
                if (str_ends_with($url, '_sources/' . $path)) {
                    return $rst;
                }
            }

            return null;
        });
    }
}
