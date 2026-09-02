<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\ChangelogLookup;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * Reaching a changelog entry by what a caller holds rather than by its title:
 * the identifier its body names, and the issue number it was filed under.
 *
 * The identifier corpus is three entries written in the two markups the core's
 * own changelog uses for one, because what that holds is a rule about markup —
 * `D-ANS-042`. The number is read off the file name, and `D-VER-009` is what a
 * sweep asks it for.
 */
final class ChangelogLookupTest extends TestCase
{
    use TemporaryInstallation;

    /**
     * Nothing here reaches docs.typo3.org. The changelog lookup reads the
     * versions above the installed major from the manual, and a unit test that
     * let it would be measuring the host — `R-COD-003`.
     */
    #[Before]
    public function sealTheManual(): void
    {
        CoreChangelog::useReader(static fn(string $url): ?string => null);
    }

    #[After]
    public function forgetTheInstance(): void
    {
        CoreChangelog::useReader(null);
        putenv(Instance::ROOT_VARIABLE);
        Instance::discoverFrom(null);
    }

    /**
     * A title says what stopped working; the migration says what to write.
     *
     * A sweep of 75 deprecations returned the two that stopped a session's
     * work and neither said what to write instead, so it read the two files
     * out of the installed package for their Migration sections and used both
     * heavily — `D-ANS-139`. The file was already open here.
     */
    #[Decision('D-ANS-139')]
    #[Test]
    public function oneEntryCarriesItsMigrationAndASweepCarriesNone(): void
    {
        Instance::discoverFrom($this->installationWithAMigrationSection());

        $one = Registry::call('typo3_changelog_lookup', ['query' => 'makeLinkButton']);
        self::assertSame(1, $one->data['matchCount']);
        self::assertStringContainsString('use the component factory instead', $one->data['entries'][0]['migration']);
        // The index directive closes the file rather than the section.
        self::assertStringNotContainsString('index::', $one->data['entries'][0]['migration']);
        self::assertStringContainsString('Migration', $one->text);

        $sweep = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation']);
        self::assertGreaterThan(1, $sweep->data['matchCount']);
        self::assertSame(
            [],
            array_values(array_filter(array_column($sweep->data['entries'], 'migration'))),
            'a sweep of seventy-five is a list of titles and not seventy-five migrations',
        );
        self::assertStringContainsString('ask again for the one entry by its issue number', $sweep->text);
    }

    #[Decision('D-ANS-042')]
    #[Test]
    public function aRemovedMethodReachesTheEntriesNamingItInTheirBody(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText']);

        // Breaking-101955 is titled about image generation and carries the
        // method in a list of what it removed; Deprecation-46770 is the older
        // markup, single backticks and no :php: role — `D-ANS-042`.
        self::assertSame(2, $result->data['matchCount']);
        self::assertStringContainsString('13.0/Breaking-101955-', (string) $result->data['entries'][0]['file']);
        self::assertStringContainsString('7.1/Deprecation-46770-', (string) $result->data['entries'][1]['file']);
        self::assertSame('body', $result->data['matchedIn']);
        self::assertStringContainsString('not the same as being about it', $result->text);
    }

    #[Decision('D-ANS-042')]
    #[Test]
    public function aQueryTheNamesAnswerIsNotWidenedByTheBodies(): void
    {
        // Breaking-101955 names GraphicalFunctions 44 times in the corpus this
        // was measured against while being titled about image generation, which
        // is the answer this order exists to keep out: the names answer, and
        // the bodies are read only where they answered nothing — `D-ANS-042`.
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'GraphicalFunctions']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertStringContainsString('Deprecation-46770', (string) $result->data['entries'][0]['file']);
        self::assertSame('name', $result->data['matchedIn']);
    }

    #[Decision('D-ANS-042')]
    #[Test]
    public function aWordThatIsAlsoWrittenAsCodeIsNotAnIdentifier(): void
    {
        // `crop()` is a real method and the index does not carry it: an
        // identifier is a name written with a hump or an underscore, and
        // without that rule every entry writing `preview` or `file` would
        // answer the word — `D-ANS-042`.
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'crop']);

        self::assertSame(0, $result->data['matchCount']);
    }

    /**
     * A reviewer holds the identifier the diff removes, in whichever form the
     * code spelled it, and the entry's own body is where it is written —
     * `D-ANS-042`.
     */
    #[Decision('D-ANS-042')]
    #[Test]
    public function anIdentifierIsReachedInEverySpellingACallerHasIt(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        foreach ([
            'qualified by its class' => 'GraphicalFunctions::getTemporaryImageWithText()',
            'fully qualified' => '\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->getTemporaryImageWithText',
        ] as $spelling => $query) {
            $result = Registry::call('typo3_changelog_lookup', ['query' => $query]);

            self::assertSame(2, $result->data['matchCount'], $spelling);
        }
    }

    /**
     * The only entry that carries the word is in a system extension the query
     * never mentioned, and the answer says so.
     *
     * `extendToSubpages` is the TCA column and the natural word for inherited
     * frontend access restriction, and the changelog answers it with one 12.0
     * Breaking removing an Indexed Search option that happens to spell it. The
     * answer is arguably correct — that area was never reworked, and a
     * changelog records change events — but returned flat beside nothing it
     * reads as evidence about the area (`feedback/2026-08-07-233553`).
     */
    #[Test]
    public function anAnswerThatComesWholeFromOneSystemExtensionSaysSo(): void
    {
        Instance::discoverFrom($this->installationWithTheIndexedSearchEntry());

        $answered = Registry::call('typo3_changelog_lookup', ['query' => 'extendToSubpages']);

        self::assertSame(1, $answered->data['matchCount']);
        self::assertStringContainsString('Every one of these is in ext:indexed_search', $answered->text);
        self::assertStringContainsString('the place that happens to spell the word', $answered->text);

        // Not where the caller asked about that extension. Word by word,
        // because nobody types the key with its underscore.
        $asked = Registry::call('typo3_changelog_lookup', ['query' => 'indexed search']);
        self::assertSame(1, $asked->data['matchCount']);
        self::assertStringNotContainsString('Every one of these is in', $asked->text);
    }

    /**
     * And never for `ext:core`, which most of what the changelog records is in:
     * "every one of these is in ext:core" is a statement about the corpus
     * rather than about the query.
     */
    #[Test]
    public function theCoresOwnTagIsNotSomebodyElsesSubject(): void
    {
        Instance::discoverFrom($this->installationWithTheImageGenerationEntries());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'getTemporaryImageWithText']);

        self::assertStringNotContainsString('Every one of these is in', $result->text);
    }

    /**
     * The second question a dual-major sweep asks of every deprecation it got
     * back, and the call that answers it — `D-VER-009`.
     *
     * The number is a word of every file name filed under it, so the siblings
     * come back off the names and the version each carries is what says whether
     * the replacement is on the lower declared major.
     */
    #[Test]
    public function anIssueNumberReachesEveryEntryFiledUnderIt(): void
    {
        Instance::discoverFrom($this->installationWithTheEntriesOfTwoIssues());

        $result = Registry::call('typo3_changelog_lookup', ['query' => '108557']);

        self::assertSame(3, $result->data['matchCount']);
        self::assertSame(
            ['Deprecation', 'Feature', 'Important'],
            array_column($result->data['entries'], 'type'),
        );
        self::assertSame(['14.2', '14.2', '14.2'], array_column($result->data['entries'], 'version'));
        // Off the names, so nothing was opened to answer it.
        self::assertSame('name', $result->data['matchedIn']);
    }

    /**
     * The deprecations of one major come back in one call, carrying the tags a
     * sweep used to spend a call apiece on — `D-ANS-093`.
     *
     * `feedback/2026-08-19-094403` composed that sweep out of eleven tag calls
     * and reached 72 of the 75 deprecations of 14, at 1.7 times the payload of
     * the one call that lists them. So `limit` carries the largest set the
     * covered majors put under a version and a type, which is the 128
     * deprecations of 12 counted in `.checkouts/12.4` on 2026-08-21.
     */
    #[Decision('D-ANS-093')]
    #[Test]
    public function aMajorsDeprecationsComeBackInOneCall(): void
    {
        $maximum = ChangelogLookup::inputSchema()['properties']['limit']['maximum'];
        self::assertGreaterThanOrEqual(128, $maximum, 'the largest covered major does not fit in one answer');

        Instance::discoverFrom($this->installationWithSixtyDeprecationsOfOneMajor());

        $result = Registry::call('typo3_changelog_lookup', [
            'type' => 'deprecation',
            'version' => '14',
            'limit' => $maximum,
        ]);

        self::assertSame(60, $result->data['matchCount']);
        self::assertCount(60, $result->data['entries']);
        self::assertStringNotContainsString('showing the first', $result->text);

        // The surfaces a package ships pick its entries out of this answer,
        // which is what the tag calls were for.
        $tags = array_column($result->data['entries'], 'tags');
        self::assertCount(
            30,
            array_filter($tags, static fn(array $carried): bool => in_array('ext:form', $carried, true)),
        );
    }

    /**
     * Sixty deprecations across two minors of one major, which is over the fifty
     * a single answer used to carry.
     *
     * Half of them are tagged `ext:form` and half `ext:core`, because what the
     * answer has to carry per entry is the tag the sweep is read by.
     */
    private function installationWithSixtyDeprecationsOfOneMajor(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach (['14.0', '14.3'] as $minor => $version) {
            mkdir($changelog . '/' . $version, 0o777, true);
            for ($entry = 1; $entry <= 30; $entry++) {
                $issue = 108000 + $minor * 30 + $entry;
                file_put_contents(
                    sprintf('%s/%s/Deprecation-%d-DeprecatedApi%d.rst', $changelog, $version, $issue, $entry),
                    sprintf(
                        "Deprecation: #%d - Deprecated API %d\n\nDescription\n===========\n\n"
                        . "The API is deprecated and will be removed in TYPO3 v15.0.\n\n"
                        . ".. index:: PHP-API, FullyScanned, %s\n",
                        $issue,
                        $entry,
                        $entry % 2 === 0 ? 'ext:form' : 'ext:core',
                    ),
                );
            }
        }

        return $root;
    }

    /**
     * What the core filed under two issue numbers, each with the entry that
     * announced the replacement beside the deprecation.
     *
     * The five are read off `.checkouts/14.3` on 2026-08-21, as excerpts: the
     * three of #108557 in 14.2 and the two of #108524 in 14.1, which is what
     * keeps the count above a corpus that only holds one number.
     */
    private function installationWithTheEntriesOfTwoIssues(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach ([
            '14.2/Deprecation-108557-TCAOptionAllowedRecordTypesForPageTypes.rst' => 'Deprecation: #108557 - TCA '
                . "option allowedRecordTypes for page types\n\nDescription\n===========\n\n"
                . "The registry option is deprecated and will be removed in TYPO3 v15.0.\n",
            '14.2/Feature-108557-TCAOptionAllowedRecordTypesForPageTypes.rst' => 'Feature: #108557 - TCA option '
                . "allowedRecordTypes for page types\n\nDescription\n===========\n\n"
                . "Page types now declare the record types they allow in TCA.\n",
            '14.2/Important-108557-DropPageDoktypeRegistryOnlyAllowedTablesOption.rst' => 'Important: #108557 - Drop '
                . "PageDoktypeRegistry option allowedTables\n\nDescription\n===========\n\n"
                . "The option is gone from the registry.\n",
            '14.1/Deprecation-108524-FluidNamespacesInTYPO3_CONF_VARS.rst' => 'Deprecation: #108524 - Fluid '
                . "namespaces in TYPO3_CONF_VARS\n\nDescription\n===========\n\n"
                . "Registering namespaces in TYPO3_CONF_VARS is deprecated.\n",
            '14.1/Feature-108524-ConfigurationFileToRegisterGlobalFluidNamespaces.rst' => 'Feature: #108524 - '
                . "Configuration file to register global Fluid namespaces\n\nDescription\n===========\n\n"
                . "Configuration/Fluid/Namespaces.php registers them instead.\n",
        ] as $path => $contents) {
            if (!is_dir($changelog . '/' . dirname($path))) {
                mkdir($changelog . '/' . dirname($path), 0o777, true);
            }
            file_put_contents($changelog . '/' . $path, $contents);
        }

        return $root;
    }

    private function installationWithTheIndexedSearchEntry(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog/12.0';
        mkdir($changelog, 0o777, true);
        file_put_contents(
            $changelog . '/Breaking-97530-IndexedSearchOptionSearchSkipExtendToSubpagesCheckingRemoved.rst',
            "Breaking: #97530 - Indexed Search option searchSkipExtendToSubpagesChecking removed\n\n"
            . "Description\n===========\n\nThe option has been removed.\n\n"
            . ".. index:: TypoScript, NotScanned, ext:indexed_search\n",
        );

        return $root;
    }

    /**
     * A project whose core ships the three entries around one removed method.
     *
     * Each is an excerpt of the entry it is named after, kept in the markup
     * that entry writes: the `:php:` role of 13.0, the single backticks of 7.1,
     * and a feature that only mentions the class in passing.
     */
    /**
     * A project whose core ships the three entries around one removed method.
     *
     * Each is an excerpt of the entry it is named after, kept in the markup
     * that entry writes: the `:php:` role of 13.0, the single backticks of 7.1,
     * and a feature that only mentions the class in passing.
     */
    /**
     * Two deprecations, so a sweep is a sweep, and one with a Migration.
     */
    private function installationWithAMigrationSection(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach ([
            '14.0/Deprecation-107823-ButtonBarMakeMethods.rst' => "Deprecation: #107823 - ButtonBar make methods\n\n"
                . "Description\n===========\n\nThe :php:`makeLinkButton()` family is deprecated.\n\n"
                . "Impact\n======\n\nCalling one raises a deprecation.\n\n"
                . "Migration\n=========\n\nInject the factory and use the component factory instead of the "
                . "button bar's own makers.\n\n.. index:: Backend, PHP-API, ext:backend\n",
            '14.0/Deprecation-109519-BackendUtilityItemListLabelMethods.rst' => 'Deprecation: #109519 - '
                . "BackendUtility item list label methods\n\nDescription\n===========\n\n"
                . "The label helpers on :php:`BackendUtility` are deprecated.\n",
        ] as $path => $contents) {
            if (!is_dir($changelog . '/' . dirname($path))) {
                mkdir($changelog . '/' . dirname($path), 0o777, true);
            }
            file_put_contents($changelog . '/' . $path, $contents);
        }

        return $root;
    }



    private function installationWithTheImageGenerationEntries(): string
    {
        $root = $this->composerProject();
        $changelog = $root . '/vendor/typo3/cms-core/Documentation/Changelog';
        foreach ([
            '13.0/Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst' => 'Breaking: #101955 - '
                . "Removed public methods related to Image Generation\n\nDescription\n===========\n\n"
                . "The following public methods from :php:`GraphicalFunctions` have been removed:\n\n"
                . "- :php:`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->crop()`\n"
                . "- :php:`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions->getTemporaryImageWithText()`\n\n"
                . ".. index:: PHP-API, FullyScanned, ext:core\n",
            '7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions.rst' => 'Deprecation: #46770 - Deprecate '
                . "LocalImageProcessor::getTemporaryImageWithText\n\nDescription\n===========\n\n"
                . 'The public method `LocalImageProcessor::getTemporaryImageWithText()` has been marked as '
                . "deprecated, it is directly\nreplaced by "
                . "`\\TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions::getTemporaryImageWithText()`.\n",
            '13.0/Feature-102755-ImprovedGetImageResourceFunctionality.rst' => 'Feature: #102755 - Improved '
                . "getImageResource functionality\n\nDescription\n===========\n\n"
                . "The instruction is handed to :php:`GraphicalFunctions` unchanged.\n",
        ] as $path => $contents) {
            if (!is_dir($changelog . '/' . dirname($path))) {
                mkdir($changelog . '/' . dirname($path), 0o777, true);
            }
            file_put_contents($changelog . '/' . $path, $contents);
        }

        return $root;
    }
}
