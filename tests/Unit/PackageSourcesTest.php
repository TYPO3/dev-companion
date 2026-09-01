<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\FluidNamespaces;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Manual\CoreChangelog;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * What the installed packages can answer when the console cannot.
 *
 * Booting TYPO3 needs a migrated database, and a project spends a lot of its
 * life without one. Where the answer is a file in a package rather than the
 * container's assembled state, the question does not have to end there.
 */
final class PackageSourcesTest extends TestCase
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
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function fluidNamespacesAreReadFromThePackagesThatDeclareThem(): void
    {
        $root = $this->coreCheckout();
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        $this->namespaceFile($root . '/typo3/sysext/backend', ['f' => ['TYPO3\\CMS\\Backend\\ViewHelpers']]);
        Instance::discoverFrom($root);

        self::assertSame(
            ['core' => ['TYPO3\CMS\Core\ViewHelpers'], 'f' => ['TYPO3\CMS\Backend\ViewHelpers']],
            FluidNamespaces::all()
        );
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function withoutAConsoleTheDeclarationsAreTheAnswerAndSaySoAsOne(): void
    {
        $root = $this->coreCheckout('14.3.0');
        $this->namespaceFile($root . '/typo3/sysext/core', ['core' => ['TYPO3\\CMS\\Core\\ViewHelpers']]);
        Instance::discoverFrom($root);
        Typo3Cli::forget();

        $result = Registry::call('typo3_fluid_namespace_list', []);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('core', $result->data['namespaces'][0]['prefix']);
        self::assertStringContainsString('not what the container assembled', $result->text);
    }

    #[Test]
    public function aPackageThatDeclaresNothingContributesNothing(): void
    {
        Instance::discoverFrom($this->coreCheckout());

        self::assertSame([], FluidNamespaces::all());
    }

    #[Requirement('R-PRJ-003')]
    #[Test]
    public function theChangelogOfTheInstalledCoreIsSearchable(): void
    {
        // What a version deprecated is a list, not a convention, and every
        // installation has it on disk.
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-107208-FdebugrenderViewHelper', 'Deprecation: #107208 - <f:debug.render> ViewHelper', ['Fluid', 'NotScanned', 'ext:fluid']);
        $this->changelogEntry($root, '13.4', 'Breaking-101392-GetIdentifierRemoved', 'Breaking: #101392 - getIdentifier() removed', ['PHP-API', 'FullyScanned']);
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'viewhelper']);

        self::assertSame(1, $result->data['matchCount']);
        $entry = $result->data['entries'][0];
        self::assertSame('Deprecation', $entry['type']);
        self::assertSame('14.0', $entry['version']);
        self::assertSame('107208', $entry['issue']);
        self::assertSame('<f:debug.render> ViewHelper', $entry['title'], 'the type and the issue are fields of their own');
        self::assertSame(['Fluid', 'NotScanned', 'ext:fluid'], $entry['tags']);
        self::assertSame(['14.0', '13.4'], $result->data['versions'], 'newest first');
    }

    /**
     * What an upgrade audit decides on, from the entry that states it —
     * `D-ANS-020`. The wordings are the corpus's own and the clause wraps in
     * the file, so the whole text is matched rather than a line.
     */
    #[Decision('D-ANS-020')]
    #[Test]
    public function aDeprecationSaysWhichVersionItStopsWorkingIn(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry(
            $root,
            '14.2',
            'Deprecation-109412-FormYamlConfigurationRegistration',
            'Deprecation: #109412 - TypoScript-based form YAML registration',
            [],
            "The TypoScript-based paths will still be loaded during the deprecation period\nbut will be removed in TYPO3 v15.0.",
        );
        $this->changelogEntry(
            $root,
            '13.4',
            'Deprecation-105297-DeprecateTableoptionsConnectionConfiguration',
            'Deprecation: #105297 - Tableoptions connection configuration',
            [],
            'The keys are deprecated and will be removed with TYPO3 v15 (or later) as breaking change.',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation']);

        self::assertSame(['15.0', '15'], array_column($result->data['entries'], 'removal'));
        self::assertStringContainsString('(#109412) — removed in v15.0', $result->text);
        // The rule is the default and the entry is what carries the exception.
        // This one skips a major, and on `.checkouts/14.3` the core kept it.
        self::assertStringContainsString('(#105297) — removed in v15', $result->text);
    }

    /**
     * An entry that states no removal is the ordinary case — 31 of the 75
     * deprecations of 14 — and an empty field beside a populated one is read as
     * "no removal planned", which is what `D-ANS-009` was built against. So the
     * rule that covers the silence travels with the answer, as data and not
     * only as text: `R-ANS-002` — `D-ANS-020`.
     */
    #[Decision('D-ANS-020')]
    #[Test]
    public function whereTheEntryStatesNoRemovalTheRuleTravelsWithTheAnswer(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.1', 'Deprecation-108667-DeprecateCommandNameAlreadyInUseException', 'Deprecation: #108667 - Deprecate CommandNameAlreadyInUseException', []);
        $this->changelogEntry($root, '14.0', 'Feature-2-SomethingNew', 'Feature: #2 - Something new', []);
        Instance::discoverFrom($root);

        $deprecation = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation']);
        self::assertSame('', $deprecation->data['entries'][0]['removal']);
        self::assertStringContainsString('keeps working until the next major release', $deprecation->data['removalRule']);
        self::assertStringContainsString('not a promise that no removal is planned', $deprecation->text);

        // An answer carrying no deprecation has nothing for the rule to answer.
        self::assertArrayNotHasKey('removalRule', Registry::call('typo3_changelog_lookup', ['type' => 'feature'])->data);
    }

    /**
     * A removal clause in the prose is not this entry's removal by being there.
     * Three shapes are in `.checkouts/14.3`: a Feature announcing what replaces
     * a deprecated mechanism and stating that mechanism's removal, a 13.3
     * deprecation whose subject "will be removed with v5", which is Fluid
     * standalone, and an entry recounting what an earlier release already
     * removed before naming its own. What tells them apart is the type of the
     * entry, and that a removal is later than the version it was released in —
     * `D-ANS-020`.
     */
    #[Decision('D-ANS-020')]
    #[Test]
    public function aRemovalClauseThatIsNotThisEntrysIsNotReadAsOne(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry(
            $root,
            '14.2',
            'Feature-109412-FormYamlAutoDiscovery',
            'Feature: #109412 - Auto-discovery of form YAML configurations',
            [],
            "This registration mechanism has been deprecated and will be removed in\nTYPO3 v15.0.",
        );
        $this->changelogEntry(
            $root,
            '13.4',
            'Deprecation-1-Backreference',
            'Deprecation: #1 - Backreference',
            [],
            "The hook it replaced was removed in v12.4. The interface itself will be\nremoved in TYPO3 v15.0.",
        );
        $this->changelogEntry(
            $root,
            '13.3',
            'Deprecation-104223-FluidStandaloneMethods',
            'Deprecation: #104223 - Fluid standalone methods',
            [],
            'The methods log a deprecation level error with Fluid standalone v4, and will be removed with v5.',
        );
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', []);

        self::assertSame(['109412', '1', '104223'], array_column($result->data['entries'], 'issue'));
        self::assertSame(['', '15.0', ''], array_column($result->data['entries'], 'removal'));
    }

    /**
     * The shape two sessions reported from two checkouts, and the one a caller
     * actually types: the thing has a name with separators in it, and the
     * changelog file spells that name apart. Every one of these has an entry in
     * `.checkouts/14.3` and reached none of them — `D-ANS-006`.
     */
    #[Decision('D-ANS-006')]
    #[Test]
    public function anIdentifierReachesTheEntryTitledInWords(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.3', 'Deprecation-109438-ExtTablesPhpInExtensions', 'Deprecation: #109438 - ext_tables.php in extensions', []);
        $this->changelogEntry($root, '14.0', 'Deprecation-98453-SchedulerTaskRegistrationViaSCOPTIONS', 'Deprecation: #98453 - Scheduler task registration via SC_OPTIONS', []);
        $this->changelogEntry($root, '14.0', 'Breaking-107784-RemoveBackendLayoutDataProviderRegistration', 'Breaking: #107784 - Remove backend layout data provider registration', []);
        Instance::discoverFrom($root);

        $reaches = static function (string $query): array {
            return array_column(Registry::call('typo3_changelog_lookup', ['query' => $query])->data['entries'], 'issue');
        };

        self::assertSame(['109438'], $reaches('ext_tables.php'));
        self::assertSame(['98453'], $reaches('SC_OPTIONS'));
        self::assertSame(['107784'], $reaches('backend_layout'));
        // The words themselves still reach it, and every term still has to be
        // carried: nothing that matched before matches less.
        self::assertSame(['109438'], $reaches('ext tables extensions'));
        self::assertSame([], $reaches('ext_tables.php scheduler'));
    }

    /**
     * The sweep the deprecation feedback of 2026-07-31 asked for: a version's
     * deprecations are 75 entries and the words a reviewer guesses reach a
     * handful, so what bounds it is the tag rather than the query. The tags are
     * inside the files, so the filter reads what the version and type narrowed
     * to — 23 ms for one major's deprecations, measured.
     */
    #[Test]
    public function aSweepIsNarrowedByTheTagAnEntryCarries(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-1-Scanned', 'Deprecation: #1 - Scanned', ['PHP-API', 'FullyScanned', 'ext:core']);
        $this->changelogEntry($root, '14.0', 'Deprecation-2-Unscanned', 'Deprecation: #2 - Unscanned', ['TCA', 'NotScanned', 'ext:form']);
        Instance::discoverFrom($root);

        $scanned = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation', 'tag' => 'FullyScanned']);
        self::assertSame(['1'], array_column($scanned->data['entries'], 'issue'));

        // Case is the caller's business, not the corpus's.
        self::assertSame(1, Registry::call('typo3_changelog_lookup', ['tag' => 'ext:FORM'])->data['matchCount']);

        // A tag nothing carries is a miss that says which ones exist, because
        // the tag was never going to be guessed from the outside — and an
        // extension key of the caller's own is exactly that case: the corpus
        // names the system extension a change is in, never the package it
        // affects.
        $miss = Registry::call('typo3_changelog_lookup', ['tag' => 'bootstrap_package']);
        self::assertSame(0, $miss->data['matchCount']);
        self::assertSame(['NotScanned', 'PHP-API', 'TCA', 'ext:core', 'ext:form', 'FullyScanned'], array_values(array_intersect(
            ['NotScanned', 'PHP-API', 'TCA', 'ext:core', 'ext:form', 'FullyScanned'],
            $miss->data['tags'],
        )));
        self::assertStringContainsString('The tags those entries carry', $miss->text);
    }

    /**
     * What the second and third call of a sweep are read off rather than
     * guessed, which `feedback/2026-08-03-164818` asks be kept. The tag list is
     * held on a miss, where it is what to ask again with; on a hit it is in no
     * assertion and not among the keys `outputSchema()` requires, so dropping
     * it would break nothing and leave a caller bounding a sweep with the tags
     * it happened to see.
     */
    #[Test]
    public function theTagListTravelsWithAHit(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-1-Scanned', 'Deprecation: #1 - Scanned', ['PHP-API', 'FullyScanned', 'ext:core']);
        $this->changelogEntry($root, '14.0', 'Deprecation-2-Unscanned', 'Deprecation: #2 - Unscanned', ['TCA', 'NotScanned', 'ext:form']);
        Instance::discoverFrom($root);

        $hit = Registry::call('typo3_changelog_lookup', ['type' => 'deprecation', 'tag' => 'ext:core']);

        self::assertSame(['1'], array_column($hit->data['entries'], 'issue'));
        // Every tag the version and the type narrowed to, the one filtered by
        // among them — not the tags of the entries that came back, which is
        // the list a caller already has.
        self::assertSame(
            ['FullyScanned', 'NotScanned', 'PHP-API', 'TCA', 'ext:core', 'ext:form'],
            $hit->data['tags'],
        );
    }

    /**
     * The miss the feedback of 2026-07-31 arrived through: five words, each of
     * them reaching entries on its own, and nothing carrying all five. What
     * ends it is a query the caller can ask rather than five numbers, because
     * two words had to go before anything matched — `D-ANS-016`.
     */
    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-016')]
    #[Test]
    public function aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.2', 'Deprecation-109412-FormYamlConfigurationRegistration', 'Deprecation: #109412 - TypoScript-based form YAML registration', []);
        $this->changelogEntry($root, '14.2', 'Feature-109412-FormYamlAutoDiscovery', 'Feature: #109412 - Auto-discovery of form YAML configurations', []);
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'form set yaml registration deprecated']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertStringContainsString('No entry carries more than 3 of the 5 words', $result->text);
        self::assertStringContainsString('"form yaml registration" reaches 1 entry', $result->text);
        // On the subset, which can be asked for, rather than on the per-word
        // counts, which cannot: the smallest reach is the word to keep.
        self::assertSame(1, substr_count($result->text, 'ask again with the one that narrows best'));
    }

    /**
     * The same miss read by a client that renders `structuredContent` and drops
     * the text block, which is what `R-ANS-002` is written against. The session
     * `D-ANS-043` was decided on quoted `matchCount: 0` and the five fields
     * beside it, reported that nothing came back to re-ask with, and went to
     * `grep` — while the subset that returns the entry its review turned on was
     * in the text of that same answer. So the counts and the subsets travel as
     * data too, and which of the two count fields carries a number is what says
     * whether it was taken inside the narrowing or outside it.
     */
    #[Requirement('R-ANS-002')]
    #[Decision('D-ANS-043')]
    #[Test]
    public function theNarrowingAMissComputesIsAField(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.2', 'Deprecation-109412-FormYamlConfigurationRegistration', 'Deprecation: #109412 - TypoScript-based form YAML registration', []);
        $this->changelogEntry($root, '14.2', 'Feature-109412-FormYamlAutoDiscovery', 'Feature: #109412 - Auto-discovery of form YAML configurations', []);
        $this->changelogEntry($root, '13.4', 'Breaking-101392-GetIdentifierRemoved', 'Breaking: #101392 - getIdentifier() removed', []);
        Instance::discoverFrom($root);

        $query = ['query' => 'form set yaml registration deprecated'];
        $data = Registry::call('typo3_changelog_lookup', $query)->data;

        // Every word, the ones reaching nothing included: a zero is what the
        // text leaves out of its sentence and is the word to drop.
        self::assertSame([
            ['term' => 'form', 'matchCount' => 2],
            ['term' => 'set', 'matchCount' => 0],
            ['term' => 'yaml', 'matchCount' => 2],
            ['term' => 'registration', 'matchCount' => 1],
            ['term' => 'deprecated', 'matchCount' => 0],
        ], $data['termCounts']);
        self::assertSame([['terms' => ['form', 'yaml', 'registration'], 'matchCount' => 1]], $data['termSubsets']);
        self::assertArrayNotHasKey('termCountsWithoutTheNarrowing', $data, 'nothing was narrowed away');

        // Withheld beside a tag for the reason the text withholds it: the
        // subsets are counted off the entry names and a tag is inside the file,
        // so one offered here would promise entries this call does not return.
        // The counts are not a promise and stay — `D-ANS-016`.
        $tagged = Registry::call('typo3_changelog_lookup', $query + ['tag' => 'ext:core'])->data;
        self::assertArrayNotHasKey('termSubsets', $tagged);
        self::assertSame(['form', 'set', 'yaml', 'registration', 'deprecated'], array_column($tagged['termCounts'], 'term'));

        // Narrowed, the counts inside are zeros and what the words reach
        // outside is the second field: the filter is what emptied this.
        $narrowed = Registry::call('typo3_changelog_lookup', $query + ['version' => '13.4'])->data;
        self::assertSame([0, 0, 0, 0, 0], array_column($narrowed['termCounts'], 'matchCount'));
        self::assertSame([
            ['term' => 'form', 'matchCount' => 2],
            ['term' => 'yaml', 'matchCount' => 2],
            ['term' => 'registration', 'matchCount' => 1],
        ], $narrowed['termCountsWithoutTheNarrowing']);
        self::assertArrayNotHasKey('termSubsets', $narrowed, 'nothing inside the version reaches two words');
    }

    /**
     * What the miss owes once the query it offered comes back empty too: the
     * corpus. A changelog records change events, so a mechanism nobody changed
     * has no entry here — `D-ANS-010` — and `R-ANS-018` is that an answer
     * saying something is absent names the tool that has it. After the offer
     * and not in place of it: the reported miss did carry the entry, one subset
     * away, and a sentence naming the manual first would have routed that
     * session away from it — `D-ANS-043`.
     */
    #[Requirement('R-ANS-018')]
    #[Decision('D-ANS-043')]
    #[Test]
    public function aMissThatOffersARequeryNamesTheCorpusToAskNext(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.2', 'Deprecation-109412-FormYamlConfigurationRegistration', 'Deprecation: #109412 - TypoScript-based form YAML registration', ['ext:form']);
        $this->changelogEntry($root, '14.2', 'Feature-109412-FormYamlAutoDiscovery', 'Feature: #109412 - Auto-discovery of form YAML configurations', ['ext:form']);
        Instance::discoverFrom($root);

        $query = ['query' => 'form set yaml registration deprecated'];
        $text = Registry::call('typo3_changelog_lookup', $query)->text;

        self::assertStringContainsString('a changelog records change events', $text);
        self::assertStringContainsString('typo3_documentation_lookup with targetVersion', $text);
        self::assertLessThan(
            strpos($text, 'Where that comes back empty too'),
            strpos($text, 'No entry carries more than'),
        );

        // Nowhere else, because "that" is the offered query: a miss carrying
        // none has nothing for the sentence to follow.
        self::assertStringNotContainsString(
            'typo3_documentation_lookup',
            Registry::call('typo3_changelog_lookup', $query + ['tag' => 'ext:core'])->text,
        );
    }

    /**
     * The branch the session of 2026-08-24 landed in, which named no tool at
     * all: `Subsets` skips a carried set the size of the query, so a miss of
     * two words offers none whatever the changelog holds, and `R-ANS-018` was
     * held over the offering branch alone. Both corpora, because nothing in a
     * miss says which of the two shapes the question had and a caller with no
     * re-query left cannot recover from the wrong one — `D-ANS-110`.
     */
    #[Requirement('R-ANS-018')]
    #[Decision('D-ANS-110')]
    #[Test]
    public function aMissWithNoRequeryToOfferNamesBothCorporaThatAnswer(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.2', 'Breaking-1-VisibilityOfTheDispatcher', 'Breaking: #1 - Dispatcher visibility', []);
        $this->changelogEntry($root, '14.2', 'Feature-2-PublicRegistry', 'Feature: #2 - Public registry', []);
        Instance::discoverFrom($root);

        $query = ['query' => 'visibility public'];
        $text = Registry::call('typo3_changelog_lookup', $query)->text;

        self::assertStringNotContainsString('No entry carries more than', $text, 'two words leave no subset');
        self::assertStringContainsString('typo3_documentation_lookup with targetVersion', $text);
        self::assertStringContainsString('typo3_rule_lookup with documentId "core/contribution/changelog"', $text);

        // Withheld where the answer already names a way back into this corpus:
        // a version that emptied it, and a tag the subsets were withheld under.
        self::assertStringNotContainsString(
            'belongs to another corpus',
            Registry::call('typo3_changelog_lookup', $query + ['version' => '13.4'])->text,
        );
        self::assertStringNotContainsString(
            'belongs to another corpus',
            Registry::call('typo3_changelog_lookup', $query + ['tag' => 'ext:core'])->text,
        );
    }

    #[Requirement('R-ANS-006')]
    #[Test]
    public function whereNoTwoWordsMeetInOneEntryThePerWordReachIsWhatToAskWith(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Feature-1-SomethingAboutForms', 'Feature: #1 - Forms', []);
        $this->changelogEntry($root, '14.0', 'Breaking-2-YamlLoader', 'Breaking: #2 - Yaml', []);
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'forms yaml']);

        self::assertStringContainsString('On its own, "forms" reaches 1 entry', $result->text);
        self::assertStringContainsString('entry — ask again with the one that narrows best.', $result->text);
        self::assertStringNotContainsString('No entry carries more than', $result->text);
    }

    #[Test]
    public function aTagIsNotPromisedEntriesTheSameCallWouldNotReturn(): void
    {
        // The peel reads file names and a tag is inside the file, so a subset
        // counted without the tag would name entries this call does not return.
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-1-FormYamlRegistration', 'Deprecation: #1 - Form YAML registration', ['ext:form']);
        Instance::discoverFrom($root);

        $query = ['query' => 'form yaml registration deprecated'];

        self::assertStringContainsString(
            '"form yaml registration" reaches 1 entry',
            Registry::call('typo3_changelog_lookup', $query)->text,
        );
        self::assertStringNotContainsString(
            'No entry carries more than',
            Registry::call('typo3_changelog_lookup', $query + ['tag' => 'ext:core'])->text,
        );
    }

    /**
     * The miss the feedback of 2026-08-01 arrived through, and the sentence
     * that cost it its call: `version: "15"` narrows the changelog to 28
     * entries, "preview" is the one word of four that reaches anything inside
     * them, and the reach line said so without saying where it was counted. The
     * session read it as "the tool cannot reach that entry" and went to `grep`,
     * where all four words reach without the version. So the filter is what the
     * miss opens with, and the counts that stay are marked as taken inside it —
     * `D-ANS-016`.
     */
    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-016')]
    #[Test]
    public function aMissNarrowedByAVersionOpensWithTheVersionThatEmptiedIt(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '15.0', 'Feature-1-PreviewOfAPageInTheBackend', 'Feature: #1 - Preview of a page in the backend', []);
        $this->changelogEntry($root, '13.4', 'Feature-2-GifBuilderThumbnailPreview', 'Feature: #2 - GifBuilder thumbnail preview', []);
        $this->changelogEntry($root, '13.0', 'Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration', 'Breaking: #101955 - Removed public methods related to Image Generation', []);
        Instance::discoverFrom($root);

        $query = ['query' => 'GifBuilder placeholder preview thumbnail'];
        $narrowed = Registry::call('typo3_changelog_lookup', $query + ['version' => '15']);

        self::assertSame(0, $narrowed->data['matchCount']);
        self::assertStringContainsString(
            'Narrowed to version "15" — that filter is what emptied this, not the words: without it, '
            . '"gifbuilder" reaches 1 entry, "preview" reaches 2 entries, "thumbnail" reaches 1 entry. '
            . 'Ask again without it.',
            $narrowed->text,
        );
        // The count that stays says where it was taken, and the call to action
        // is dropping the version rather than asking again inside it.
        self::assertStringContainsString('Inside version "15", on its own, "preview" reaches 1 entry.', $narrowed->text);
        self::assertStringNotContainsString('ask again with the one that narrows best', $narrowed->text);
        self::assertLessThan(
            strpos($narrowed->text, 'Inside version "15"'),
            strpos($narrowed->text, 'Narrowed to version "15"'),
        );

        // The same query without the filter has no filter to name: nothing
        // carries all four words there either, and the words are the whole of
        // the reason.
        $unnarrowed = Registry::call('typo3_changelog_lookup', $query);
        self::assertStringNotContainsString('Narrowed to', $unnarrowed->text);
        self::assertStringContainsString('On its own, "gifbuilder" reaches 1 entry', $unnarrowed->text);
    }

    /**
     * A version that narrows nothing away is not what emptied the answer, and
     * the miss that says it is sends the caller to drop a filter that costs it
     * nothing. What the sentence turns on is a word reaching outside the
     * narrowing and nothing inside it, not that a narrowing was asked for —
     * `D-ANS-016`.
     */
    #[Decision('D-ANS-016')]
    #[Test]
    public function aFilterThatChangedNothingIsNotBlamedForTheMiss(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Feature-1-SomethingAboutForms', 'Feature: #1 - Forms', []);
        $this->changelogEntry($root, '14.0', 'Breaking-2-YamlLoader', 'Breaking: #2 - Yaml', []);
        Instance::discoverFrom($root);

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'forms yaml', 'version' => '14']);

        self::assertStringNotContainsString('Narrowed to', $result->text);
        self::assertStringContainsString(
            'Inside version "14", on its own, "forms" reaches 1 entry, "yaml" reaches 1 entry '
            . '— ask again with the one that narrows best.',
            $result->text,
        );
    }

    #[Requirement('R-PRJ-003')]
    #[Test]
    public function theChangelogIsNarrowedByTypeAndVersion(): void
    {
        $root = $this->composerProject();
        $this->changelogEntry($root, '14.0', 'Deprecation-1-SomethingOld', 'Deprecation: #1 - Something old', []);
        $this->changelogEntry($root, '14.0', 'Feature-2-SomethingNew', 'Feature: #2 - Something new', []);
        $this->changelogEntry($root, '13.4', 'Feature-3-SomethingOlder', 'Feature: #3 - Something older', []);
        Instance::discoverFrom($root);

        $byType = Registry::call('typo3_changelog_lookup', ['type' => 'feature']);
        self::assertSame(['2', '3'], array_column($byType->data['entries'], 'issue'));

        // A prefix, so "14" reaches 14.0 through 14.3.x.
        $byVersion = Registry::call('typo3_changelog_lookup', ['version' => '14']);
        self::assertSame(['1', '2'], array_column($byVersion->data['entries'], 'issue'));
    }

    #[Requirement('R-PRJ-003')]
    #[Test]
    public function anInstallationWithoutAChangelogSaysSo(): void
    {
        Instance::discoverFrom($this->composerProject());

        $result = Registry::call('typo3_changelog_lookup', ['query' => 'anything']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertArrayHasKey('unsupported', $result->data);
    }

    /** @param array<int, string> $tags */
    private function changelogEntry(string $root, string $version, string $name, string $title, array $tags, string $description = 'Description'): void
    {
        $path = $root . '/vendor/typo3/cms-core/Documentation/Changelog/' . $version;
        if (!is_dir($path)) {
            mkdir($path, 0o777, true);
        }
        file_put_contents($path . '/' . $name . '.rst', implode("\n", [
            '.. include:: /Includes.rst.txt',
            '',
            str_repeat('=', mb_strlen($title)),
            $title,
            str_repeat('=', mb_strlen($title)),
            '',
            $description,
            '',
            $tags === [] ? '' : '..  index:: ' . implode(', ', $tags),
        ]));
    }

    /** @param array<string, array<int, string>> $namespaces */
    private function namespaceFile(string $packagePath, array $namespaces): void
    {
        $path = $packagePath . '/Configuration/Fluid';
        mkdir($path, 0o777, true);

        $entries = '';
        foreach ($namespaces as $prefix => $phpNamespaces) {
            $entries .= sprintf("    '%s' => [\n", $prefix);
            foreach ($phpNamespaces as $phpNamespace) {
                $entries .= sprintf("        '%s',\n", str_replace('\\', '\\\\', $phpNamespace));
            }
            $entries .= "    ],\n";
        }

        file_put_contents($path . '/Namespaces.php', "<?php\n\nreturn [\n" . $entries . "];\n");
    }
}
