<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Knowledge\Versions;
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
 * the one changelog manual docs.typo3.org publishes, and since `D-ANS-165` so
 * does everything else it renders. This holds the join: which side each entry
 * came from, that neither shadows the other, and that the answer names an
 * unreachable docs.typo3.org rather than reads it as silence.
 *
 * Nothing here reaches docs.typo3.org. The seam is `CoreChangelog::useReader()` and
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
     * docs.typo3.org renders the changelog after every merge, so a version it lists
     * comes from it whatever the installation ships for that version. The
     * entry says where it came from and links by URL — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aVersionDocsTypo3OrgListsComesFromItWhateverTheInstallationShips(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $this->manualPublishing([
            '13.4/Deprecation-1-SomethingOld' => 'Deprecation: #1 - Something old',
            '15.0/Deprecation-110148-ExperimentalBackendViewHelpers' => 'Deprecation: #110148 - Experimental backend ViewHelpers',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something old']);

        self::assertSame(1, $result->data['matchCount']);
        $entry = $result->data['entries'][0];
        self::assertSame('13.4', $entry['version']);
        self::assertSame('manual', $entry['publishedIn'], 'docs.typo3.org is ahead of the package that ships the same version');
        self::assertStringStartsWith('https://docs.typo3.org/', $entry['file']);
        self::assertSame($entry['file'], $entry['url']);
        self::assertSame(['15.0', '13.4'], $result->data['versionsFromTheManual'], 'what docs.typo3.org lists inside the filters');
        self::assertSame(['13.4'], $result->data['versions'], 'what the installation ships is still said');
    }

    /**
     * A version docs.typo3.org does not list is the installation's. That is every
     * version below what the knowledge covers, which a package ships down to
     * 7.0 — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aVersionDocsTypo3OrgDoesNotListComesFromTheInstallation(): void
    {
        Instance::discoverFrom($this->installationAt('11.5', [
            '11.5/Deprecation-2-SomethingOlder' => 'Deprecation: #2 - Something older',
        ]));
        $this->manualPublishing([
            '15.0/Deprecation-3-SomethingNew' => 'Deprecation: #3 - Something new',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something older']);

        self::assertSame(1, $result->data['matchCount']);
        $entry = $result->data['entries'][0];
        self::assertSame('installation', $entry['publishedIn']);
        self::assertSame('EXT:core/Documentation/Changelog/11.5/Deprecation-2-SomethingOlder.rst', $entry['file']);
        self::assertStringStartsWith('https://docs.typo3.org/', $entry['url'], 'docs.typo3.org address is on every entry');
    }

    /**
     * A server that did not answer is a gap in this answer rather than in the
     * changelog. The installation answers with what it ships, and the answer
     * says that the versions above it are unread rather than absent —
     * `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function docsTypo3OrgNotAnsweringLeavesTheInstallationToAnswer(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        CoreChangelog::useReader(static fn(string $url): ?string => null);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something old']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame('installation', $result->data['entries'][0]['publishedIn']);
        self::assertStringContainsString('docs.typo3.org did not answer', $result->text);
        self::assertArrayNotHasKey('versionsFromTheManual', $result->data);
    }

    /**
     * docs.typo3.org is asked once per process where it does not answer. A session
     * that starts offline pays the connect timeout once rather than once a
     * question — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function docsTypo3OrgThatDidNotAnswerIsNotAskedAgainInThisProcess(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $asked = 0;
        CoreChangelog::useReader(function (string $url) use (&$asked): ?string {
            $asked++;

            return null;
        });

        Registry::call('typo3_changelog_lookup', ['query' => 'something old']);
        Registry::call('typo3_changelog_lookup', ['query' => 'something old']);

        self::assertSame(1, $asked);
    }

    /**
     * docs.typo3.org lists a major on its own, so one can be missing while the
     * others came in. The installation answers for it where it ships it, and
     * the answer names the major — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aMajorDocsTypo3OrgDidNotAnswerForComesFromTheInstallationWhereItShipsIt(): void
    {
        Instance::discoverFrom($this->installationAt('14.0', [
            '14.0/Feature-4-SomethingNew' => 'Feature: #4 - Something new',
        ]));
        $this->manualPublishing([
            '15.0/Feature-5-SomethingNewer' => 'Feature: #5 - Something newer',
        ], silent: [14]);

        $result = Registry::call('typo3_changelog_lookup', ['type' => 'feature']);

        self::assertSame(
            ['15.0' => 'manual', '14.0' => 'installation'],
            array_column($result->data['entries'], 'publishedIn', 'version'),
        );
        self::assertSame(['15.0'], $result->data['versionsFromTheManual']);
        self::assertStringContainsString('did not answer for 14', $result->text);
    }

    /**
     * Without an installation docs.typo3.org answers alone, and the answer says that
     * nothing comes from disk. That is the session before `composer install`,
     * which `D-ANS-105` could only tell what would make it answerable —
     * `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function withoutAnInstallationDocsTypo3OrgAnswersAlone(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());
        $this->manualPublishing([
            '15.0/Deprecation-3-SomethingNew' => 'Deprecation: #3 - Something new',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something new']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame('manual', $result->data['entries'][0]['publishedIn']);
        self::assertSame([], $result->data['versions']);
        self::assertStringContainsString('No TYPO3 installation was found', $result->text);
    }

    /**
     * Without an installation and without docs.typo3.org there is nothing to
     * answer from, and the answer is the unsupported one — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function withoutAnInstallationAndWithoutDocsTypo3OrgTheQuestionIsUnsupported(): void
    {
        Instance::discoverFrom(sys_get_temp_dir());
        CoreChangelog::useReader(static fn(string $url): ?string => null);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'something new']);

        self::assertArrayHasKey('unsupported', $result->data);
        self::assertStringContainsString('docs.typo3.org did not answer', $result->data['unsupported']['reason']);
    }

    /**
     * The listings read are one per covered major, whatever the installation
     * ships. docs.typo3.org lists them nowhere for less than its whole table of
     * contents — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function oneListingPerCoveredMajorIsRead(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $asked = [];
        CoreChangelog::useReader(function (string $url) use (&$asked): string {
            $asked[] = $url;

            return (string) json_encode(['major' => 0, 'entries' => []]);
        });

        Registry::call('typo3_changelog_lookup', ['query' => 'anything']);

        $expected = [];
        foreach (Versions::majors() as $major) {
            $expected[] = 'https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog-' . $major . '.json';
        }
        self::assertSame($expected, $asked);
    }

    /**
     * A manual entry brings its tags in the listing, so the tag filter reads
     * no entry it drops. The one it keeps is read for what the answer shows
     * of it — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aTagFilterReadsNoManualEntryItDrops(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $asked = $this->manualPublishing([
            '15.0/Deprecation-5-InFluid' => 'Deprecation: #5 - Something in Fluid',
            '15.0/Deprecation-6-InCore' => 'Deprecation: #6 - Something in core',
        ], tags: [
            '15.0/Deprecation-5-InFluid' => ['ext:fluid'],
            '15.0/Deprecation-6-InCore' => ['ext:core'],
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation', 'tag' => 'ext:fluid']);

        self::assertSame(['5'], array_column($result->data['entries'], 'issue'));
        self::assertSame(['PHP-API', 'ext:core', 'ext:fluid'], $result->data['tags'], 'the tags offered are read off the listing, beside the installed entry\'s');
        $pages = array_values(array_filter($asked(), static fn(string $url): bool => str_ends_with($url, '.md')));
        self::assertSame(
            ['https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/15.0/Deprecation-5-InFluid.md'],
            $pages,
            'the dropped entry was never read',
        );
    }

    /**
     * A shown entry is read as the Markdown docs.typo3.org rendered, which is the
     * page with every include resolved, and not as the RST it was built from.
     * The migration is its own section there, and the removal is stated in
     * the text — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aShownEntryIsReadAsTheMarkdownDocsTypo3OrgRenders(): void
    {
        Instance::discoverFrom($this->installationAt('13.4'));
        $this->manualPublishing([
            '15.0/Deprecation-5-InFluid' => 'Deprecation: #5 - Something in Fluid',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'in fluid']);

        $entry = $result->data['entries'][0];
        self::assertSame('16.0', $entry['removal']);
        self::assertSame("Use [the other one](https://docs.typo3.org/other) instead.\n\n```php\n\$other->run();\n```", $entry['migration']);
    }

    /**
     * The listing carries the stated title, and an installed entry's
     * title is a file read away. Under the field a search reads, a manual entry
     * would answer in the pass that reads file names alone. The installed one
     * it should have found is still there by name only — `D-ANS-165`.
     */
    #[Decision('D-ANS-165')]
    #[Test]
    public function aManualTitleDoesNotShadowTheInstalledEntryAQueryIsAbout(): void
    {
        Instance::discoverFrom($this->installationAt('11.5', [
            '11.5/Breaking-2-RenamedSomething' => 'Breaking: #2 - The frobnicator was removed',
        ]));
        $this->manualPublishing([
            '15.0/Feature-3-SomethingElse' => 'Feature: #3 - A frobnicator for the backend',
        ]);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'frobnicator']);

        // Neither has the word in its name, so the pass that reads titles
        // reaches both. That pass reads both sides or it reads neither. Newest
        // first, which is the order every answer here is in.
        self::assertSame(
            ['15.0', '11.5'],
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
     * What docs.typo3.org serves: one listing per covered major that names those
     * pages, and the Markdown of each page beside it.
     *
     * A covered major the fixture publishes nothing for answers with an empty
     * listing, and one named silent answers the way a server without a listing
     * for it does, with a page that is none. The closure returned says what
     * was asked.
     *
     * @param array<string, string> $pages page name to stated title
     * @param array<string, array<int, string>> $tags page name to the index tags it carries
     * @param array<int, int> $silent the majors docs.typo3.org does not answer for
     * @return \Closure(): array<int, string>
     */
    private function manualPublishing(array $pages, array $tags = [], array $silent = []): \Closure
    {
        $listings = [];
        $rendered = [];
        foreach ($pages as $name => $title) {
            [$version] = explode('/', $name, 2);
            $carried = $tags[$name] ?? ['Fluid', 'FullyScanned'];
            // "15.0/Deprecation-5-InFluid" spells its type and issue.
            [$type, $issue] = explode('-', explode('/', $name, 2)[1], 3);
            $listings[(int) $version][] = [
                'path' => 'Changelog/' . $name,
                'title' => $title,
                'type' => strtolower($type),
                'issue' => (int) $issue,
                'typo3-version' => $version,
                'tags' => $carried,
            ];
            $rendered['Changelog/' . $name . '.md'] = implode("\n", [
                '---',
                'title: ' . json_encode($title),
                'typo3-version: "' . $version . '"',
                'type: "' . strtolower($type) . '"',
                'tags: ' . json_encode($carried),
                '---',
                '',
                '# ' . $title . ' {#anchor}',
                '',
                '## Description {#description}',
                '',
                'What changed. It will be removed in v16.0.',
                '',
                '## Migration {#migration}',
                '',
                'Use [the other one](https://docs.typo3.org/other) instead.',
                '',
                '```php',
                '$other->run();',
                '```',
                '',
            ]);
        }

        $asked = [];
        CoreChangelog::useReader(static function (string $url) use ($listings, $rendered, $silent, &$asked): ?string {
            $asked[] = $url;
            if (preg_match('/Changelog-(\\d+)\\.json$/', $url, $major) === 1) {
                return in_array((int) $major[1], $silent, true)
                    ? '<html>Not Found</html>'
                    : (string) json_encode(['major' => (int) $major[1], 'entries' => $listings[(int) $major[1]] ?? []]);
            }
            foreach ($rendered as $path => $markdown) {
                if (str_ends_with($url, '/' . $path)) {
                    return $markdown;
                }
            }

            return null;
        });

        return static function () use (&$asked): array {
            return $asked;
        };
    }
}
