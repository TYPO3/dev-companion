<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Knowledge\CommitMessage;
use TYPO3\DevCompanion\Knowledge\Documents;
use TYPO3\DevCompanion\Knowledge\Domains;
use TYPO3\DevCompanion\Knowledge\Hints;
use TYPO3\DevCompanion\Knowledge\Scope;
use TYPO3\DevCompanion\Knowledge\TaskIntents;
use TYPO3\DevCompanion\Knowledge\TestSuiteHints;
use TYPO3\DevCompanion\Knowledge\Versions;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\HintLookup;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Tool\TaskGuide;
use TYPO3\DevCompanion\Upkeep\Scenarios;

final class HintsTest extends TestCase
{
    /**
     * Every statement of the named hints, as one text.
     *
     * A subject is several hints since `D-KNW-030`, so a test that asks whether
     * the corpus states something names the hints the split made. What a caller
     * gets is still one of them; that is what the reachability cases assert.
     */
    private static function statementsOf(string ...$ids): string
    {
        $texts = [];
        foreach ($ids as $id) {
            $hint = Hints::byId($id);
            self::assertNotNull($hint, $id . ' is not a hint');
            $texts[] = implode("\n", array_column($hint['hints'], 'text'));
        }

        return implode("\n", $texts);
    }

    #[Requirement('R-KNW-022')]
    #[Test]
    public function aPhpPathIsNeverAnsweredWithFrontendConventions(): void
    {
        $result = Hints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], '', 6);

        self::assertContains(Domains::PHP, $result['domains']);
        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertContains($hint['category'], ['PHP', 'General']);
        }
    }

    /**
     * The other direction, and `D-KNW-067`. The PHP domain carries the words of
     * somebody who asks for a test. So a query about a JavaScript one got
     * UnitTestCase, CSV fixtures and createStub as the larger half of it. The
     * path says which layer is meant.
     *
     * It is also the other half of `D-ANS-081`, where a curated phrase crosses
     * the domain gate. "unit test" stands curated on all three of these hints
     * and on the JavaScript one. So the layer the path names answers the query
     * itself and nothing crosses. A phrase let past on its own puts them back —
     * `D-ANS-084`.
     */
    #[Decision('D-KNW-067')]
    #[Requirement('R-ANS-031')]
    #[Decision('D-ANS-084')]
    #[Test]
    public function aTypeScriptTestPathIsNotAnsweredWithPhpunit(): void
    {
        $result = Hints::find(
            ['Build/Sources/TypeScript/backend/tests/layout-module/sticky-language-header-test.ts'],
            'JavaScript unit tests for a backend TypeScript module with state transitions',
            6,
        );

        self::assertNotContains(Domains::PHP, $result['domains']);
        $matched = array_column($result['matchedHints'], 'id');
        self::assertSame('javascript-unit-tests', $matched[0]);
        self::assertNotContains('unit-test-doubles', $matched);
        self::assertNotContains('core-tests', $matched);
        self::assertNotContains('project-extension-tests', $matched);
    }

    /**
     * The carve-out above reaches the paths and no further. A task that names
     * both layers asks for both, and the PHPUnit half of it is the answer to
     * the PHP path.
     */
    #[Test]
    public function aTaskCoveringBothLayersKeepsThePhpTestHints(): void
    {
        $result = Hints::find(
            [
                'Build/Sources/TypeScript/backend/tests/layout-module/sticky-language-header-test.ts',
                'typo3/sysext/backend/Tests/Unit/View/BackendLayoutViewTest.php',
            ],
            'unit tests for the module and the view it renders',
            8,
        );

        self::assertContains(Domains::PHP, $result['domains']);
        self::assertContains(Domains::TYPESCRIPT, $result['domains']);
    }

    /**
     * The two paths of `feedback/2026-08-24-100400`, which is the call that
     * session says it did not make because it did not expect an answer to
     * exist. `D-KNW-107` assumed the `appliesTo` is what decides whether the
     * hint arrives at all, so this asks with the paths alone. The hint has to
     * be reachable from either half of the module, and the PHP half is the one
     * `backend-typescript` never covered.
     */
    #[Decision('D-KNW-107')]
    #[Test]
    public function eitherHalfOfABackendModuleReachesWhichSideResolvesAResource(): void
    {
        $fromBoth = Hints::find(
            [
                'Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts',
                'typo3/sysext/form/Classes/Controller/FormManagerController.php',
            ],
            '',
            8,
        );
        $fromThePhp = Hints::find(['typo3/sysext/form/Classes/Controller/FormManagerController.php'], '', 8);

        self::assertContains('backend-client-server-boundary', array_column($fromBoth['matchedHints'], 'id'));
        self::assertContains('backend-client-server-boundary', array_column($fromThePhp['matchedHints'], 'id'));
    }

    /**
     * What the reading of `.checkouts/` settled, against the rule the feedback
     * proposed. The same subsystem contradicts "Backend TypeScript must not
     * hold an `EXT:` path": the form manager's select carries every
     * `newFormTemplates` path the server handed it. So the statement is the
     * distinction between a value carried and a value worked out.
     *
     * The core removed the one place it worked one out, which is why the last
     * statement has a bound. `form-manager/view-model.ts` sets the blank mode's
     * `templatePath` to a literal on 12.4 and 13.4. The wizard rewrite dropped
     * it rather than moved it into the module's payload.
     */
    #[Decision('D-KNW-107')]
    #[Test]
    public function whatTheClientMayCarryIsSaidApartFromWhatItMayDecide(): void
    {
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('backend-client-server-boundary', $major)['hints'], 'text'),
        );

        self::assertStringContainsString('Carrying a resource path is not the same as deciding one', $on(14));
        self::assertStringContainsString('the client carrying what the server gave it', $on(14));
        self::assertStringContainsString('1329233410', $on(14));

        self::assertStringContainsString('the only literal EXT: resource path', $on(12));
        self::assertStringContainsString('the only literal EXT: resource path', $on(13));
        self::assertStringNotContainsString('the only literal EXT: resource path', $on(14));
        self::assertStringContainsString('No backend TypeScript source resolves a resource by path', $on(14));
    }

    /**
     * `R-ANS-026`: a path says which subsystem the question is about.
     *
     * Two sessions passed the Extbase persistence paths and got FAL storages
     * back. A bare `appliesTo` word matches against the paths as a prefix, so
     * `storage` claimed the `Storage/` segment and `/Persistence/` the
     * directory above it. Both outranked everything on the keyword tier while
     * their own words answered nothing — `D-ANS-060`. The negative is what this
     * holds: which hint *should* answer here is a subject the corpus does not
     * carry.
     */
    #[Decision('D-ANS-060')]
    #[Requirement('R-ANS-026')]
    #[Test]
    public function anExtbasePersistencePathIsNotAnsweredWithAnotherSubsystem(): void
    {
        $result = Hints::find(
            [
                'typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php',
                'typo3/sysext/extbase/Classes/Persistence/Generic/Mapper/ColumnMap.php',
                'typo3/sysext/extbase/Classes/Persistence/Generic/Backend.php',
            ],
            'Extbase persistence, query parser, data mapper, column map, writing and reading records',
            8,
        );

        $matched = array_column($result['matchedHints'], 'id');
        self::assertNotContains('fal-storages-drivers', $matched);
        // The positive half, which waited for a hint about this subsystem to
        // exist at all. The two the report named cover the core QueryBuilder
        // and the model's table, and neither reaches the query parser.
        self::assertSame('extbase-persistence-internals', $matched[0]);
    }

    /**
     * A hint that answered nothing may not outrank one that did.
     *
     * `keywords` used to sort above `score` unconditionally, so a pattern match
     * was worth more than the hint's own words whatever they said.
     * `system-extension-boundaries` is what that cost. It claims
     * `typo3/sysext/`, which every core path in the world carries. It scored 0
     * against every query measured while it stood second on a FAL question.
     * That is the tier order `D-ANS-060` left open.
     */
    #[Test]
    public function aHintWhoseOwnWordsAnswerNothingRanksBelowOneThatDoes(): void
    {
        $result = Hints::find(
            ['typo3/sysext/core/Classes/Resource/Driver/LocalDriver.php'],
            'file storage driver',
            6,
        );

        $ids = array_column($result['matchedHints'], 'id');
        self::assertSame('fal-basics', $ids[0]);

        // The premise, so this fails as the ranking case rather than as a
        // corpus one if that hint ever starts answering.
        $boundaries = null;
        foreach ($result['matchedHints'] as $hint) {
            if ($hint['id'] === 'system-extension-boundaries') {
                $boundaries = $hint;
            }
        }
        if ($boundaries !== null) {
            self::assertSame(0, $boundaries['matchedOn']['score'], 'it now answers, so it is no longer this case');
            self::assertGreaterThan(
                array_search('fal-basics', $ids, true),
                array_search('system-extension-boundaries', $ids, true),
                'a hint scoring nothing is above one that answers',
            );
        }

        // Every hint that answers comes before every hint that does not.
        $answering = array_map(
            static fn(array $hint): bool => $hint['matchedOn']['score'] > 0,
            $result['matchedHints'],
        );
        $sorted = $answering;
        rsort($sorted);
        self::assertSame($sorted, $answering, 'the two halves are interleaved');
    }

    /**
     * The other half of the same change: what the pruned patterns were for.
     *
     * The DataHandler path reaches `datahandler-basics` without
     * `/Persistence/`, and a storage question reaches `fal-storages-drivers`
     * without the bare `storage`. Both had their measurement before the
     * patterns went, and this is what keeps it.
     */
    #[Decision('D-ANS-060')]
    #[Requirement('R-ANS-026')]
    #[Test]
    public function pruningThePathPatternsLeftBothSubjectsReachable(): void
    {
        $dataHandler = Hints::find(
            ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
            'write a record with DataHandler',
            6,
        );
        self::assertContains('datahandler-basics', array_column($dataHandler['matchedHints'], 'id'));

        $storages = Hints::find([], 'which storage does this file come from', 6);
        self::assertContains('fal-storages-drivers', array_column($storages['matchedHints'], 'id'));
    }

    #[Requirement('R-KNW-037')]
    #[Test]
    public function aDistributedExtensionIsNotAnsweredWithTheProjectLayout(): void
    {
        // The two hints describe different repositories, and the older one
        // describes the one with an installation in it. A review of a package
        // quoted it and moved the browser suite out of the only repository
        // there is.
        $result = Hints::find(
            ['composer.json', 'Tests/', 'Build/'],
            'reusable extension published for several TYPO3 versions: lock file, supported range, browser tests',
            6,
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('extension-repository-layout', $ids);
        self::assertLessThan(
            array_search('project-repository-layout', $ids, true) === false
                ? PHP_INT_MAX
                : (int) array_search('project-repository-layout', $ids, true),
            (int) array_search('extension-repository-layout', $ids, true),
            'the repository that is only the extension answers before the one that holds an installation',
        );
    }

    /**
     * `R-KNW-064`. Two of the three findings are traps rather than absences.
     * The installer accepts `app-dir` where it stands and ignores it where it
     * matters. The `cms-cli` failure blames a version line that looks wrong and
     * is not. A hint that named the keys and not their two failures would leave
     * a session exactly where the report found it — `D-KNW-053`.
     */
    #[Requirement('R-KNW-064')]
    #[Decision('D-KNW-053')]
    #[Test]
    public function installingTypo3BeneathTheExtensionNamesTheInertKey(): void
    {
        // The feedback's own query, which used to reach the manifest and the
        // project scripts, and the narrowed one, which reached nothing at all.
        $reported = Hints::find(
            [],
            'Making a TYPO3 extension\'s own composer.json install a full TYPO3 into .build/ so the extension '
            . 'can be run locally: which config/extra keys apply, and which packages may be required.',
            6,
        );
        self::assertContains('extension-repository-installation', array_column($reported['matchedHints'], 'id'));

        $narrowed = Hints::find(
            [],
            'TYPO3 extension composer root package app-dir web-dir typo3/cms-cli local installation',
            6,
        );
        self::assertSame('extension-repository-installation', $narrowed['matchedHints'][0]['id']);

        $text = self::statementsOf('extension-repository-installation');

        // The keys that move the installation, and the one the installer
        // accepts and then ignores. With the message, because that is what a
        // session sees.
        self::assertStringContainsString('config.vendor-dir and config.bin-dir', $text);
        self::assertStringContainsString('Changing app-dir is not supported any more', $text);
        self::assertStringContainsString('whether or not web-dir is set beside it', $text);
        self::assertStringContainsString('belong in .gitignore', $text);
        self::assertStringContainsString('must be a subdirectory of Composer root directory', $text);

        // The constraint belongs to the core on every covered major, so the
        // root package requiring the package itself is what cannot resolve.
        self::assertStringContainsString('not the root package\'s to require', $text);
        self::assertStringContainsString('pins a major of its own', $text);

        // And the placement, which the empty directory beside it reads against.
        self::assertStringContainsString('package path is the Composer root itself', $text);
        self::assertStringContainsString('empty where it exists at all', $text);
        self::assertStringContainsString('public-assets', $text);
    }

    /**
     * `R-KNW-074`, `D-KNW-093`. One assertion per hint the sweep of
     * `knowledge/hints/` reached: the six that prescribe a `typo3` command
     * whose success is unconditional. Two of them, `impexp-artifact` and
     * `extension-schema-sql`, came before the rule. This holds them so the form
     * stays one form rather than three.
     */
    #[Requirement('R-KNW-074')]
    #[Decision('D-KNW-093')]
    #[Test]
    public function aCommandThatAlwaysSucceedsCarriesItsDiscriminator(): void
    {
        // impexp:export answers [OK] whatever it left out of the artifact.
        $text = self::statementsOf('impexp-artifact');
        self::assertStringContainsString('answers [OK] whatever it left out', $text);
        self::assertStringContainsString('files_fal section beside header and records', $text);

        // extension:setup answers success having migrated from a cached TCA.
        $text = self::statementsOf('extension-schema-sql');
        self::assertStringContainsString('successfully set up', $text);
        self::assertStringContainsString('SHOW TABLES LIKE', $text);

        // language:update exits 0 with the failed download behind the progress
        // bar. `$status` starts at SUCCESS and only --fail-on-warnings moves
        // it, and the per-pack result prints under --no-progress alone.
        $text = self::statementsOf('site-label-language');
        self::assertStringContainsString('exits 0 whether or not a pack arrived', $text);
        self::assertStringContainsString('--fail-on-warnings', $text);
        self::assertStringContainsString('var/labels/<iso>/<extension key>/', $text);

        // backend:user:create returns SUCCESS whatever DataHandler reported:
        // createUser() never reads its errorLog.
        $text = self::statementsOf('installation-boot');
        self::assertStringContainsString('never reads that handler\'s errorLog', $text);
        self::assertStringContainsString('the be_users row, or a login', $text);

        // upgrade:run marks a wizard done where updateNecessary() said no, and
        // the run-all path swallows the note that says so. Both hints that
        // prescribe the command carry it, because neither points at the other.
        $text = self::statementsOf('upgrade-wizards');
        self::assertStringContainsString('also where updateNecessary() returned false', $text);
        self::assertStringContainsString('upgrade:list --all', $text);

        $text = self::statementsOf('installation-upgrade');
        self::assertStringContainsString('not evidence that anything migrated', $text);
        self::assertStringContainsString('upgrade:list --all', $text);
    }

    /**
     * `R-KNW-073`, `D-KNW-089`. The statement carries no version bound because
     * the mechanism reads the same on all four checkouts. The TCA cache
     * identifier and the unconditional success message are what they are on
     * 12.4, 13.4, 14.3 and main alike. What a stale entry hides differs by
     * major and the two statements beside this one already carry that.
     */
    #[Requirement('R-KNW-073')]
    #[Decision('D-KNW-089')]
    #[Test]
    public function theSchemaStepIsSaidToMigrateFromTheCachedTca(): void
    {
        // The feedback's own query and the symptom it arrived as. Neither
        // reached this hint before the statement existed. The first answered
        // with FormEngine and the upgrade order, the second with the upgrade
        // order and the shipped content.
        foreach ([
            'Add a new Configuration/TCA/tx_myext_thing.php to an installed extension, '
            . 'then run `typo3 extension:setup` without flushing caches',
            'table does not exist after extension:setup reported success',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('extension-schema-sql', $ids, $task);
        }

        $text = self::statementsOf('extension-schema-sql');

        // What the reader reads, and what does not invalidate it. The
        // precondition, without which the rule reads as a habit.
        self::assertStringContainsString('derives from the cached TCA', $text);
        self::assertStringContainsString('keyed on the TYPO3 version, the project path and the active package list', $text);
        self::assertStringContainsString('package rescan flushes nothing', $text);
        self::assertStringContainsString('A fresh installation has no entry at all', $text);

        // And the check, because the command answers the same either way.
        self::assertStringContainsString('successfully set up', $text);
        self::assertStringContainsString('cache:flush comes before it', $text);
        self::assertStringContainsString('SHOW TABLES LIKE', $text);
        self::assertStringContainsString('typo3_schema_lookup does not settle it', $text);

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString(
                'derives from the cached TCA',
                implode("\n", array_column((array) Hints::byId('extension-schema-sql', $major)['hints'], 'text')),
                'the mechanism holds on every covered major',
            );
        }
    }

    /**
     * `D-KNW-097`. The statement carries no version bound because the sort
     * reads the same on all four checkouts. `sortMatchedRoutes()` compares the
     * host score before the path on 12.4, 13.4, 14.3 and main alike. The only
     * difference between them is the `strpos` the tail tiebreak below it uses
     * on 12.4.
     */
    #[Decision('D-KNW-097')]
    #[Test]
    public function whichOfTwoCollidingSiteBasesWinsIsStated(): void
    {
        // The feedback's own task, and the symptom stripped of the project it
        // arrived from. Neither reached the mechanism before this hint: the
        // first answered with Extbase arguments and the second with the import
        // the reporting session then followed.
        foreach ([
            'Blog extension development installation was set up, backend works but frontend returns 404 page not found',
            'frontend returns 404 at the site root',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('site-base-collision', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('site-base-collision');

        // Which base wins, and the sort that decides it.
        self::assertStringContainsString('a base carrying a host beats a base that does not', $text);
        self::assertStringContainsString('getHostMatchScore()', $text);
        self::assertStringContainsString('the host is compared before the path ever is', $text);

        // And what the failure looks like, which is what makes it worth a
        // reach: the message reads like a slug with a typo.
        self::assertStringContainsString('The requested page does not exist', $text);
        self::assertStringContainsString('rootPageId above 0', $text);

        // The two neighbours it must not displace. A boot is not a collision,
        // and an import that rewrote a base is the other hint's subject.
        $boot = array_column(
            Hints::find([], 'boot the installation from a fresh clone, import the database and verify the site responds', 6)['matchedHints'],
            'id',
        );
        self::assertSame('installation-boot', $boot[0] ?? null);
        self::assertNotContains('site-base-collision', $boot);

        $imported = array_column(
            Hints::find([], 'distribution imported with extension:setup answers 404 at the project root', 6)['matchedHints'],
            'id',
        );
        self::assertLessThan(
            array_search('site-base-collision', $imported, true) ?: PHP_INT_MAX,
            array_search('initial-content-references', $imported, true),
            'a base an import rewrote is still the import hint\'s answer',
        );
    }

    /**
     * `D-KNW-100`. Nothing here has a bound. `AbstractProvider` is
     * byte-identical on 12.4, 13.4, 14.3 and main, and each of them builds the
     * `Resolver` inside `IncludeTreeConditionMatcherVisitor` and instantiates
     * every provider through `makeInstance()`. The line that consults the
     * container only for a class asked for without arguments reads the same on
     * all four.
     */
    #[Decision('D-KNW-100')]
    #[Test]
    public function whatAnExtensionMayBuildBehindAConditionProviderIsStated(): void
    {
        // The three queries `D-KNW-100` measured as reaching nothing.
        foreach ([
            'extension registers its own TypoScript condition ExpressionLanguage provider',
            'TypoScript condition provider ExpressionLanguage extension registers',
            'Configuration/ExpressionLanguage.php typoscript context provider',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('typoscript-condition-providers', $ids[0] ?? null, $task);
        }

        // And the files the reporting session had open, which carry no
        // TypoScript signal at all.
        $held = array_column(Hints::find(
            ['Configuration/ExpressionLanguage.php', 'Classes/ExpressionLanguage/ConditionProvider.php'],
            'the condition stopped matching',
            6,
        )['matchedHints'], 'id');
        self::assertContains('typoscript-condition-providers', $held);

        $text = self::statementsOf('typoscript-condition-providers');

        // The choice that decides every such fix: what each channel can see,
        // and that neither of them does both.
        self::assertStringContainsString('two channels and no third', $text);
        self::assertStringContainsString('That object sees no condition variable', $text);
        self::assertStringContainsString('an evaluator is handed $arguments', $text);
        self::assertStringContainsString('A function name cannot carry a dot', $text);
        self::assertStringContainsString('mutually exclusive', $text);
        self::assertStringContainsString('injected into the provider', $text);

        // The two the session behind the report read out of core rather than
        // looked up. What a provider with a dependency has to be, and why a
        // registration added to an installed extension stays invisible.
        self::assertStringContainsString('has to be a public service', $text);
        self::assertStringContainsString('#[Autoconfigure(public: true)]', $text);
        self::assertStringContainsString('system caches are flushed', $text);

        // The neighbour keeps the callers it already had and names this hint
        // rather than restating it — `D-KNW-100`.
        $container = array_column(
            Hints::find([], 'condition provider constructor injection makeInstance public service Autoconfigure', 6)['matchedHints'],
            'id',
        );
        self::assertContains('di-service-not-found', $container);
        self::assertStringContainsString(
            'typoscript-condition-providers',
            self::statementsOf('di-service-not-found'),
        );
    }

    /**
     * The fifth cause of a root that answers nothing, and the one nothing
     * pointed at: a site the core wrote itself.
     *
     * Its curation is on the provenance rather than on the 404. The symptom
     * words already answer `site-base-collision`, and a second hint that
     * claimed them would take the collision's own callers — `D-KNW-098`. So the
     * case asserts the two directions together. The query a session with such a
     * site would write reaches this hint. The plain root-404 query still
     * reaches the collision without this one beside it.
     */
    #[Decision('D-KNW-098')]
    #[Test]
    public function whereASiteNobodyWroteCameFromIsStated(): void
    {
        // The provenance query D-KNW-098 measured, which answered
        // site-label-language first, and the mechanism named outright, which
        // reached nothing at all.
        foreach ([
            'a site configuration nobody wrote exists with identifier autogenerated and the frontend answers 404 at the root',
            'site created automatically when a page is added at pid 0 is_siteroot CreateSiteConfiguration',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('autogenerated-site-configuration', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('autogenerated-site-configuration');

        // What marks the site, and what the hook did to the page under it.
        self::assertStringContainsString('autogenerated-<uid>-<md5 of that uid>', $text);
        self::assertStringContainsString('is_siteroot', $text);
        self::assertStringContainsString('updateSlugForPage()', $text);

        // Why a custom root page doktype got one at all.
        self::assertStringContainsString('DOKTYPE_DEFAULT', $text);
        self::assertStringContainsString('processDatamapClass', $text);

        // And the discriminator against the import, which is the neighbour the
        // reporting session followed instead.
        self::assertStringContainsString('returns early while DataHandler is importing', $text);

        // The symptom stays where it was. A plain root-404 is the collision's,
        // and a base an import rewrote is still the import hint's.
        $symptom = array_column(
            Hints::find([], 'frontend returns 404 at the site root', 6)['matchedHints'],
            'id',
        );
        self::assertSame('site-base-collision', $symptom[0] ?? null);
        self::assertNotContains('autogenerated-site-configuration', $symptom);

        $imported = array_column(
            Hints::find([], 'distribution imported with extension:setup answers 404 at the project root', 6)['matchedHints'],
            'id',
        );
        self::assertNotContains('autogenerated-site-configuration', $imported);
    }

    /**
     * The half of the symptom cluster that had no owner: the request reached a
     * site and the page under it did not come.
     *
     * The one place it stood was `datahandler-seeding`, whose `appliesTo` is
     * about a seed script. So a session on a failed request never reached it.
     * Its curation is on the page: the message with its exclamation mark, a
     * hidden or deleted root page, a tree that is hidden everywhere. The site
     * words are `site-base-collision`'s, and a second hint that claimed them
     * would take its callers (`D-KNW-098`).
     *
     * Read on `.checkouts/12.4`, `13.4`, `14.3` and `main`, which agree on the
     * messages and on the restrictions each query carries — `D-KNW-105`.
     */
    #[Decision('D-KNW-105')]
    #[Test]
    public function whatANotFoundMeansOnceASiteAnsweredIsReached(): void
    {
        // The message as a caller would paste it, the seed case the fact sat
        // in, and the two records that produce it.
        foreach ([
            'the frontend answers The requested page does not exist! and the page is in the page tree',
            'the seeded page tree answers 404 on every page',
            'the site root is 404 and its root page is deleted',
            'why does a hidden page answer 404 in the frontend',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('page-not-found-within-a-site', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('page-not-found-within-a-site');

        // What separates the four not-founds from each other, and all of them
        // from a request that reached no site.
        self::assertStringContainsString('The requested page does not exist!', $text);
        self::assertStringContainsString('No site configuration found.', $text);
        self::assertStringContainsString('ID was not an accessible page', $text);

        // Where that line is readable, which is the whole evidence a response
        // leaves: nothing throws and the log stays empty.
        self::assertStringContainsString('Reason: <the message>', $text);
        self::assertStringContainsString('errorHandling', $text);

        // Why the URL is right while the answer is a not-found, and the check a
        // session can run without a second installation.
        self::assertStringContainsString('deleted and workspace restrictions alone', $text);
        self::assertStringContainsString('getPage_noCheck()', $text);
        self::assertStringContainsString('private window', $text);

        // Hidden and deleted reach different distances, which is what says
        // whether one page or the whole tree is the subject.
        self::assertStringContainsString('takes nothing below it with it', $text);
        self::assertStringContainsString('SiteFinder::getSiteByPageId()', $text);

        // The symptom stays where it was: which of two sites answered is the
        // question before this one.
        $collision = array_column(
            Hints::find([], 'frontend returns 404 at the site root', 6)['matchedHints'],
            'id',
        );
        self::assertSame('site-base-collision', $collision[0] ?? null);

        // And the four neighbours name it back, so the caller who landed on one
        // of them is not left to guess this one's name.
        foreach ([
            'site-base-collision',
            'installation-boot',
            'installation-exception-output',
            'datahandler-seeding',
        ] as $neighbour) {
            self::assertStringContainsString(
                'page-not-found-within-a-site',
                self::statementsOf($neighbour),
                $neighbour . ' does not name the hint that reads the message it produces',
            );
        }
    }

    /**
     * The setup of the analysis is the extension author's question, and the
     * core is no answer to it. Its configuration sits in a mono repository and
     * half of what it declares is its own rule set. The paths are relative to a
     * tree an extension does not have. So the hint has to be reachable from the
     * words somebody at the setup would use, and a core test task must not
     * reach it. That caller has runTests.sh and a configuration already there.
     */
    #[Test]
    public function settingUpTheAnalysisReachesTheExtensionHint(): void
    {
        $setup = Hints::find([], 'how do I set up phpstan for my extension', 6);

        self::assertContains('extension-static-analysis', array_column($setup['matchedHints'], 'id'));

        $core = Hints::find(
            ['typo3/sysext/core/Classes/'],
            'write a functional test for a core patch',
            6,
        );

        self::assertNotContains('extension-static-analysis', array_column($core['matchedHints'], 'id'));
    }

    /**
     * `D-KNW-114`. The setup of the analysis and the fix for it are two
     * questions, and only the first had an answer. A core patch that PHPStan
     * rejected reached a hint about `tmpDir` and `bootstrapFiles`.
     *
     * The narrow form comes off `.checkouts/main` at `3cbdea24dd` and holds on
     * every covered major. `TypoScriptStringFactory`, `Bootstrap` and
     * `RedirectServiceTest` narrow `CacheManager::getCache()` with an inline
     * `@var`. The annotation stands throughout `Classes/` and `Tests/`, and
     * `assert($value instanceof …)` stands nowhere, so the idiom is the
     * codebase's rather than those files'.
     */
    #[Decision('D-KNW-114')]
    #[Test]
    public function aPhpstanFindingOnACorePatchReachesTheNarrowingItAsksFor(): void
    {
        // The words the reporting session had: the rejected call, the two types
        // the message named, and the fix it did not know was the fix.
        $ids = array_column(Hints::find(
            [],
            'phpstan argument.type CacheManager getCache returns FrontendInterface expects PhpFrontend',
            6,
        )['matchedHints'], 'id');

        self::assertSame('core-static-analysis', $ids[0] ?? null);

        $text = self::statementsOf('core-static-analysis');

        // What the fix is, and the site the session found by hand.
        self::assertStringContainsString('/** @var Type $variable */', $text);
        self::assertStringContainsString('TypoScriptStringFactory', $text);
        // And what it is not, which is the half a finding never states.
        self::assertStringContainsString('not with a cast and not with an `instanceof` check', $text);

        // The four rules the core writes itself, each named where a caller
        // would read its identifier off the failure.
        foreach ([
            'instanceof.unneededNotNullSubstitute',
            'unserialize.',
            'custom.namedArguments',
            'attribute.forbidden.classScope',
        ] as $identifier) {
            self::assertStringContainsString($identifier, $text);
        }

        // The baseline, which is where the session's second grep went.
        self::assertStringContainsString('phpstanGenerateBaseline', $text);
    }

    /**
     * The two rules the core had not written yet, and the file that carries the
     * baseline rule at all. `12.4` has no `AGENTS.md`, and its configuration
     * registers `UnneededInstanceOfRule` alone.
     */
    #[Decision('D-KNW-114')]
    #[Test]
    public function aRuleTheBranchDoesNotRegisterIsWithheldFromIt(): void
    {
        $on = static fn(int $major): string => implode("\n", array_column(
            (array) Hints::byId('core-static-analysis', $major)['hints'],
            'text',
        ));

        self::assertStringNotContainsString('unserialize.', $on(12));
        self::assertStringNotContainsString('phpstanGenerateBaseline', $on(12));
        self::assertStringContainsString('unserialize.', $on(13));
        self::assertStringNotContainsString('custom.namedArguments', $on(13));
        self::assertStringContainsString('custom.namedArguments', $on(14));

        // The narrowing is not one of them: it is what every covered major
        // writes, so a caller on the oldest one still gets the fix.
        self::assertStringContainsString('/** @var Type $variable */', $on(12));
    }

    /**
     * `D-KNW-055`, the half of the static-quality layer the corpus had.
     *
     * Read back out of an installed `typo3/coding-standards` v0.9.0, because
     * the package is in no checkout and in no environment here. Two of the
     * three things the report asked for do not hold. The excludes are directory
     * names that match at any depth rather than literal paths, and `.build` is
     * hidden. What is one is a build directory that is neither.
     */
    #[Decision('D-KNW-055')]
    #[Test]
    public function theFixerHalfOfTheStaticQualityLayerIsStatedAndReachable(): void
    {
        // The words a fixer task arrives with, none of which is the analyser's.
        foreach ([
            'coding standards php-cs-fixer setup for an extension',
            'php-cs-fixer',
            'code style fixer extension',
            'editorconfig',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('extension-coding-standards', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('extension-coding-standards');

        // What the setup command takes, which the report had from `--help`.
        self::assertStringContainsString('type argument is optional', $text);
        self::assertStringContainsString('extra.typo3/cms.extension-key', $text);
        self::assertStringContainsString('--rule-set defaults to both sets', $text);

        // What the shipped configuration already excludes, and the case the
        // template does need a correction for. That `.build/` is not that case
        // is why the report's correction fell out, and `D-KNW-055` states it
        // rather than this.
        self::assertStringContainsString('directory name matched at any depth and case-sensitively', $text);
        self::assertStringContainsString('neither hidden nor one of the excluded names', $text);

        // The verdict is the dry run's, because the fixing run has none.
        self::assertStringContainsString('exits 0 whether it changed anything or not', $text);
        self::assertStringContainsString('exits 8 where a file would change', $text);

        // And the neighbour, which is the only route the reporting session had.
        self::assertStringContainsString('extension-static-analysis', $text);
    }

    /**
     * The other half of the same gap. The guide answered the task with
     * `intents: []` and the core patch checklist, so nothing named the skill
     * that owns the work — `D-KNW-055`.
     */
    #[Decision('D-KNW-055')]
    #[Test]
    public function aCodeStyleFixerTaskIsRoutedToTheSkillThatOwnsIt(): void
    {
        $intents = TaskIntents::detect(
            'add a code style fixer (php-cs-fixer) to a standalone TYPO3 extension repository',
        );

        self::assertContains('coding-standards', array_column($intents, 'id'));
        self::assertSame(
            ['typo3-extension-testing'],
            TaskIntents::skills($intents, false, false),
        );

        // A word that names the subject without naming the work stays weak, so
        // the whole workflow is not loaded on it.
        $weak = TaskIntents::detect('reformat the generated output');
        self::assertSame(
            ['coding-standards' => 'weak'],
            array_column($weak, 'confidence', 'id'),
        );
        self::assertSame([], TaskIntents::skills($weak, false, false));
    }

    #[Test]
    public function aSassPathReachesTheCssHints(): void
    {
        $result = Hints::find(['Build/Sources/Sass/component/_badge.scss'], '', 6);

        self::assertContains(Domains::CSS, $result['domains']);
        self::assertNotSame([], $result['matchedHints']);
    }

    #[Test]
    public function aSassPathIsNeverAnsweredWithTypeScriptConventions(): void
    {
        $result = Hints::find(['Build/Sources/Sass/component/_card.scss'], 'card component styling', 8);

        self::assertNotContains(Domains::TYPESCRIPT, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotContains($hint['category'], [Hints::CATEGORY_TYPESCRIPT, 'JavaScript'], $hint['id']);
        }
    }

    #[Test]
    public function aTypeScriptPathIsNeverAnsweredWithCssConventions(): void
    {
        $result = Hints::find(
            ['Build/Sources/TypeScript/backend/form-editor/inspector-component.ts'],
            'field label override per record type',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Test]
    public function aFluidPathReachesTheFluidHintsAndNoOthers(): void
    {
        $result = Hints::find(
            [
                'typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html',
                'typo3/sysext/core/Classes/ViewHelpers/IconViewHelper.php',
            ],
            'Fluid template ViewHelper conventions escaping namespace',
            6
        );

        $categories = array_column($result['matchedHints'], 'category');
        self::assertContains('Fluid', $categories);
        self::assertNotContains(Hints::CATEGORY_TYPESCRIPT, $categories);
        self::assertNotContains(Hints::CATEGORY_CSS, $categories);
    }

    #[Decision('D-KNW-024')]
    #[Test]
    public function aQueryWrittenInFluidTagSyntaxReachesTheFluidHints(): void
    {
        // Somebody who reports what a template did writes the tag and not the
        // word. The query below is the one a session arrived with, and it named
        // no path, no file extension and never "Fluid". The domain fell back to
        // PHP and the whole category was gone before any score. So the words
        // the statement about the branch stood for could not find it —
        // `D-KNW-024`.
        $reached = Hints::find(
            [],
            'f:if with f:else but no explicit f:then swallows the inline then-branch / f:link.typolink output',
            6,
        );

        self::assertContains('fluid-conditions-and-arrays', array_column($reached['matchedHints'], 'id'));

        // The prefix is a token rather than a word, so it cannot land inside
        // one. A question about PHP stays PHP.
        $php = Hints::find([], 'inject a service into my DataHandler hook class', 6);
        self::assertNotContains('Fluid', array_column($php['matchedHints'], 'category'));
    }

    #[Decision('D-KNW-043')]
    #[Test]
    public function aFluidResourceUriTaskIsAnsweredWithWhoAppliesCacheBusting(): void
    {
        // The query is the one a bugfix session arrived with on 15.0.0-dev. It
        // got the ViewHelper class shape back. Correct, and silent about the
        // API the area now stands on, which is what the task was about.
        $query = 'Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource';
        $result = Hints::find([], $query, 6);
        self::assertSame('fluid-resource-uris', $result['matchedHints'][0]['id']);

        $onFifteen = self::statementsOf('fluid-resource-uris');
        self::assertStringContainsString('f:image and f:uri.image are not on it', $onFifteen);
        self::assertStringContainsString('belongs to the publisher rather than to the ViewHelper', $onFifteen);
        self::assertStringContainsString('useCacheBusting', $onFifteen);

        // The rule the same session got in the tracker, "you must not use
        // f:image for anything but FAL resources". The corpus used to repeat it
        // with the two image ViewHelpers flattened into one documented rule.
        // Only f:uri.image carries it, on every checkout, and f:image's own
        // example is an EXT: path the core's suite covers. `D-KNW-043`.
        self::assertStringContainsString('discouraged, not forbidden', $onFifteen);
        self::assertStringContainsString('f:uri.image\'s class documentation', $onFifteen);
        self::assertStringContainsString('SvgImageViewHelperTest', $onFifteen);
        self::assertStringContainsString('fallback storage', $onFifteen);
        self::assertStringNotContainsString('their own class documentation sends', $onFifteen);

        // Asked in the words of the rule rather than in the words of the API,
        // it used to reach nothing at all.
        $asQuoted = Hints::find([], 'must not use f:image for anything but FAL resources', 6);
        self::assertSame('fluid-resource-uris', $asQuoted['matchedHints'][0]['id']);

        // Read on both sides in .checkouts/. The SystemResource namespace, the
        // f:resource ViewHelper and File with PublicResourceInterface are on
        // 14.3 and on main alike, and on 13.4 there is none of it. The report
        // read the API as a 15 change because it came from 13.
        $guide = Registry::call('typo3_task_guide', ['task' => $query, 'targetVersion' => '13.4']);
        self::assertStringNotContainsString('System Resource API', $guide->text);
        self::assertStringContainsString('computes the URL itself through PathUtility', $guide->text);
    }

    /**
     * `R-KNW-063`, read out of an installed vendor tree because
     * `typo3fluid/fluid` is in no checkout. The whole file-name chain runs
     * inside one root path before the next one's turn — `D-KNW-052`.
     */
    #[Requirement('R-KNW-063')]
    #[Decision('D-KNW-052')]
    #[Test]
    public function theFileNameFallbackIsStatedAsOncePerRootPath(): void
    {
        // The path the audit had in hand, and then the question in the words of
        // the ask. That used to fall to the PHP domain before any score, so no
        // Fluid hint was ever a candidate.
        $audited = Hints::find(
            ['Resources/Private/Layouts/Login.html'],
            'fork of the core backend login layout',
            6,
        );
        self::assertSame('fluid-templates', $audited['matchedHints'][0]['id']);

        foreach ([
            'which template root path wins override order',
            'templateRootPaths order override core template',
            'does my extension Login.html still overload the core Login.fluid.html',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('fluid-templates', $ids, $task);
        }

        $text = self::statementsOf('fluid-templates');

        // The order, which is what decides between two files that both exist.
        self::assertStringContainsString('decided per root path', $text);
        self::assertStringContainsString('walks the root paths backwards', $text);
        self::assertStringContainsString('Foo.fluid.html, Foo.html, a bare Foo', $text);
        self::assertStringContainsString('first character uppercased', $text);

        // And which root path is the later one, which is the half no changelog
        // entry states and the reading had to settle.
        self::assertStringContainsString('sortArrayWithIntegerKeys', $text);
        self::assertStringContainsString('takes max+1 and does win', $text);
        self::assertStringContainsString('as soon as one key in it is a string', $text);

        // The three consequences of the same mechanism an audit needs, and
        // which the corpus had none of.
        self::assertStringContainsString('may not be used by an extension that still supports an older TYPO3 major', $text);
        self::assertStringContainsString('no longer has to begin with a capital', $text);
        self::assertStringContainsString('carries its own file extension leaves the chain', $text);
    }

    /**
     * The chain is the v14 half of that answer. Fluid 2.15.0 and 4.6.1, read in
     * `.environments/`, try the format and then the bare name and nothing else.
     * The action name gets its capital before the lookup rather than after it.
     * A caller on 13 told about `.fluid.html` would go looking for a file the
     * resolver there never asks for — `D-KNW-052`.
     */
    #[Requirement('R-KNW-063')]
    #[Decision('D-KNW-052')]
    #[Test]
    public function theFluidFileExtensionIsWithheldWhereItDoesNotResolve(): void
    {
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('fluid-templates', $major)['hints'], 'text'),
        );

        self::assertStringContainsString('Foo.fluid.html, Foo.html, a bare Foo', $on(14));

        foreach ([12, 13] as $major) {
            self::assertStringNotContainsString('Foo.fluid.html, Foo.html, a bare Foo', $on($major));
            self::assertStringContainsString('Foo.html and then a bare Foo', $on($major));
            self::assertStringContainsString('no uppercase fallback', $on($major));

            // The walk over the root paths themselves is the same on every
            // major, so that half carries no version boundary at all.
            self::assertStringContainsString('decided per root path', $on($major));
            self::assertStringContainsString('sortArrayWithIntegerKeys', $on($major));
        }
    }

    /**
     * `D-ANS-003`, read an eighth time. The index of installed ViewHelpers the
     * request wanted stays refused, and what was absent is where the two
     * argument lists live. The core half is one `typo3_documentation_lookup`
     * call into the ViewHelper Reference, the other half a class in the
     * caller's own tree.
     *
     * The mapping carries no version boundary. It is the same explode, ucfirst
     * and `ViewHelper` suffix in Fluid 2.15, 4.6.1 and 5.3.1, the three lines
     * 12.4, 13.4 and 14.3/main require. The core's own resolver subclass
     * overrides instantiation rather than the name on all four branches.
     */
    #[Test]
    public function whereAViewHelpersArgumentsComeFromIsStatedOnTheTemplateHint(): void
    {
        foreach ([
            'what arguments a viewhelper takes',
            'viewhelper argument list',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('fluid-templates', $ids, $task);
        }

        $text = self::statementsOf('fluid-templates');

        // The core half, which is a manual page rather than anything here.
        self::assertStringContainsString('Fluid ViewHelper Reference', $text);
        self::assertStringContainsString('typo3_documentation_lookup', $text);
        self::assertStringContainsString('Arguments section', $text);

        // The other half, written out far enough that a reader finds the class
        // rather than guesses at it. The prefix, the case, and which namespace
        // wins.
        self::assertStringContainsString('every dot-separated segment uppercased', $text);
        self::assertStringContainsString('My\\Package\\ViewHelpers\\Foo\\BarViewHelper', $text);
        self::assertStringContainsString('last one registered is tried first', $text);
        self::assertStringContainsString('registerArgument()', $text);

        foreach (Versions::majors() as $major) {
            $on = implode("\n", array_column((array) Hints::byId('fluid-templates', $major)['hints'], 'text'));

            self::assertStringContainsString('Fluid ViewHelper Reference', $on, (string) $major);
            self::assertStringContainsString('BarViewHelper', $on, (string) $major);
        }
    }

    /**
     * `D-KNW-075`. The reported query stood in the mechanism's words and
     * reached nothing. What a session holds is an error that names the
     * ViewHelper and not the method that produced the value, so the case
     * asserts both forms.
     *
     * The bound is the half that decision predicted wrong. Only the strict
     * processor raises the message for a false, and on 12 the compiled template
     * calls `renderStatic()` and drops the check with it. That is why the same
     * page throws once and then renders nothing.
     */
    #[Test]
    public function anObjectPathIsAnsweredWithTheGetterFirst(): void
    {
        foreach ([
            'Fluid object accessor resolution getter isser hasser method before public property',
            'f:for each argument is of type boolean error',
            'hasItems method shadows the public items property in a Fluid template',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('fluid-object-access', $ids, $task);
        }

        $text = self::statementsOf('fluid-object-access');

        // The order, and the two consequences that are what the statement is
        // for: the method nothing reaches, and the property it hides.
        self::assertStringContainsString('getFoo(), then isFoo(), then hasFoo(), and the public property foo last', $text);
        self::assertStringContainsString('never for the hasItems() somebody wrote', $text);
        self::assertStringContainsString('getHasMorePages()', $text);
        self::assertStringContainsString('makes {obj.items} the boolean', $text);
        self::assertStringContainsString('no has<Property>() or is<Property>()', $text);

        $on = static fn(int $major): string => implode("\n", array_column(
            (array) Hints::byId('fluid-object-access', $major)['hints'],
            'text',
        ));

        // The container branch is the one instance of the order the corpus
        // already stated, and it arrived with 13.
        self::assertStringContainsString('PSR-11 container is asked ahead', $on(13));
        self::assertStringNotContainsString('PSR-11', $on(12));

        foreach ([12, 13, 14] as $major) {
            self::assertStringContainsString('is of type "boolean"', $on($major), 'not stated for ' . $major);
        }
        self::assertStringContainsString('for a true and for a false alike', $on(14));
        self::assertStringContainsString('Where the boolean is false, nothing is raised at all', $on(12));
        self::assertStringNotContainsString('nothing is raised at all', $on(14));
        self::assertStringContainsString('skips validateArguments()', $on(12));
        self::assertStringNotContainsString('skips validateArguments()', $on(13));

        // The neighbour the same read corrected. The lenient check rejects a
        // clear mismatch as well, and what walks through it is every empty
        // value rather than every value.
        $viewHelpers = implode("\n", array_column(Hints::byId('fluid-viewhelpers', 13)['hints'], 'text'));
        self::assertStringContainsString('every empty value passes whatever the type says', $viewHelpers);
        self::assertStringNotContainsString('is passed to the ViewHelper unchanged', $viewHelpers);
    }

    /**
     * The shadow case is reachable from the class as much as from the template.
     *
     * `D-KNW-075`'s third **Wrong if**, measured rather than reported. The
     * session that writes `hasItems()` holds the PHP class, and the query it
     * holds is the ViewHelper's exception, which carries no Fluid word. A hint
     * tagged `fluid` alone was no candidate for it; on 2026-09-02 that call
     * reached nothing at all. So the tag was what filed the answer where a
     * reader meets the failure rather than where it comes from.
     */
    #[Decision('D-KNW-075')]
    #[Test]
    public function theShadowingIsReachedFromTheClassAndFromTheTemplate(): void
    {
        $message = 'The argument "each" was registered with type "array", but is of type "boolean"';

        foreach ([
            'packages/my_ext/Classes/Dto/Result.php',
            'packages/my_ext/Resources/Private/Templates/List.html',
        ] as $path) {
            $ids = array_column(Hints::find([$path], $message, 6)['matchedHints'], 'id');
            self::assertContains('fluid-object-access', $ids, $path);
        }
    }

    #[Test]
    public function aTypoScriptPathReachesTheTypoScriptHints(): void
    {
        // .typoscript and .tsconfig used to fall into the generic frontend
        // bucket, which answered a site set with the CSS browser baseline.
        $result = Hints::find(
            [
                'typo3/sysext/fluid_styled_content/Configuration/Sets/FluidStyledContent/setup.typoscript',
                'typo3/sysext/form/Configuration/page.tsconfig',
            ],
            '',
            6
        );

        self::assertContains(Domains::TYPOSCRIPT, $result['domains']);
        $categories = array_column($result['matchedHints'], 'category');
        self::assertContains('TypoScript', $categories);
        self::assertNotContains(Hints::CATEGORY_CSS, $categories);
    }

    /**
     * `D-KNW-101`. The session behind the report read the middleware order out
     * of three branches by hand. The migration the removal changelog
     * prescribes, read the page off the request, has nothing to read it from
     * where a condition runs.
     */
    #[Decision('D-KNW-101')]
    #[Test]
    public function aConditionIsAnsweredWithWhatItIsHanded(): void
    {
        $held = Hints::find([], 'typoscript condition variables page request', 6);
        self::assertSame('typoscript-conditions', $held['matchedHints'][0]['id']);

        $text = self::statementsOf('typoscript-conditions');

        // The variable set, and the three that go away again: a condition that
        // names pageId is a parse error rather than a wrong verdict.
        self::assertStringContainsString('`request`, `page`, `site`, `siteLanguage`, `context`, `tree`', $text);
        self::assertStringContainsString('unset again before the resolver is built', $text);

        // Why the prescribed migration does not work here: the wrapper has no
        // getAttribute() to read the page information attribute off.
        self::assertStringContainsString('has no getAttribute()', $text);

        // The symptom, which is what a caller arrives with — there is no error
        // to search for.
        self::assertStringContainsString('no error is raised and nothing is logged', $text);

        // And the way in that holds wherever this knowledge base reaches.
        self::assertStringContainsString('AfterPageAndLanguageIsResolvedEvent', $text);
    }

    /**
     * The half that decides whether a fix works. The globals a provider behind
     * a condition may read have values on the older majors and not on the newer
     * ones. So an unbound statement would be wrong on half the corpus either
     * way — `D-KNW-101`.
     */
    #[Decision('D-KNW-101')]
    #[Test]
    public function whichGlobalsAConditionCanReadIsBoundToItsMajor(): void
    {
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('typoscript-conditions', $major)['hints'], 'text'),
        );

        self::assertStringContainsString('reads either one and works', $on(12));
        self::assertStringContainsString('reads either one and works', $on(13));
        self::assertStringContainsString('`tsfe` is in the list', $on(13));

        self::assertStringContainsString('a provider reading it gets null', $on(14));
        self::assertStringNotContainsString('reads either one and works', $on(14));
        self::assertStringNotContainsString('`tsfe` is in the list', $on(14));

        // The event is the recommendation on every covered major, and what a
        // listener takes the record off changed with the controller.
        self::assertStringContainsString('$event->getController()->page', $on(12));
        self::assertStringContainsString('$event->getPageInformation()->getPageRecord()', $on(13));
        self::assertStringContainsString('$event->getPageInformation()->getPageRecord()', $on(14));

        // Why the obvious substitution fails, rather than only that it answers
        // null. `feedback/2026-08-19-094315` reports proposing exactly that
        // swap until this clause stopped it, and a summarising rewrite drops
        // the reason before it drops the verdict.
        self::assertStringContainsString('assigned by the frontend request handler', $on(14));
        self::assertStringContainsString('matched while that global is still unset', $on(14));
    }

    /**
     * The constant a caller has in front of them, beside the literal the
     * exception quotes.
     *
     * `feedback/2026-08-19-094315` read the constraint and saw
     * `ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT` throughout the extension
     * under audit. It says it would have reported that as a finding on the
     * statement alone. The core is where it settled the value instead. That is
     * the round trip a hint that states a constraint on a literal costs every
     * reader of code written with the constant.
     */
    #[Test]
    public function aPluginTypeArgumentIsAnsweredWithTheConstantThatSatisfiesIt(): void
    {
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('extbase-plugin-registration', $major)['hints'], 'text'),
        );

        // The constraint, and the constant that already meets it. Both come
        // from `.checkouts/`: the literal is the exception message of
        // ExtensionUtility::configurePlugin(), and PLUGIN_TYPE_CONTENT_ELEMENT
        // is 'CType' on every covered major.
        self::assertStringContainsString('omitted or given as "CType"', $on(14));
        self::assertStringContainsString('PLUGIN_TYPE_CONTENT_ELEMENT', $on(12));
        self::assertStringContainsString('PLUGIN_TYPE_CONTENT_ELEMENT', $on(14));

        // The other constant is not a wrong argument, it is an absent one, and
        // that is the half a dual-major package needs. The same line is valid
        // where the constant exists and fatal where it does not.
        self::assertStringContainsString('PLUGIN_TYPE_PLUGIN', $on(14));
        self::assertStringContainsString('undefined constant', $on(14));
        self::assertStringNotContainsString('PLUGIN_TYPE_PLUGIN', $on(13));
    }

    /**
     * The rule that decides which class holds a security verdict's read.
     *
     * `feedback/2026-08-19-094315` credits it with both directions of one
     * audit. The walk from ViewHelpers on to the template that emits, and the
     * refusal of a ViewHelper whose `escapeOutput=false` looked bad. It asks
     * that nobody shorten it, because the second direction lives in the clause
     * about what is only on the path. Nothing held either sentence.
     */
    #[Test]
    public function aSecurityFindingIsHeldToItsSinkAndToWhatIsOnlyOnThePath(): void
    {
        $text = self::statementsOf('security-sinks');

        // The claim a finding actually makes, and the two ways of discharging
        // it — read the sink, or say which class went unread.
        self::assertStringContainsString('a claim about its sink', $text);
        self::assertStringContainsString('report the finding as unverified', $text);

        // The half that drops a candidate rather than raises one. A component
        // on the path emits nothing, and an apparent opt-out on it is often
        // what stops a double encode of a protected value.
        self::assertStringContainsString('hands it on emits nothing', $text);
        self::assertStringContainsString('looks like an opt-out', $text);

        // And that the last hop is usually not in the code under review, which
        // is what makes the reading worth pricing at all.
        self::assertStringContainsString('inside an installed package', $text);
    }

    #[Requirement('R-SCO-004')]
    #[Decision('D-KNW-033')]
    #[Test]
    public function aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions(): void
    {
        // The theme is the frontend's and the CSS hints are the backend's own
        // conventions. So the whole category stays back on the domains the
        // paths name rather than on the words of the query — `D-KNW-033`.
        $result = Hints::find(
            ['Resources/Public/Scss/bootstrap.scss', 'Build/Sources/Sass/_variables.scss'],
            'Sass architecture, variables and build pipeline for a Bootstrap 5 based frontend theme',
            8
        );

        self::assertContains(Hints::CATEGORY_CSS, $result['withheldCategories']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Requirement('R-SCO-004')]
    #[Test]
    public function stylingABackendModuleStillReachesTheBackendCssHints(): void
    {
        $result = Hints::find(
            ['Resources/Public/Css/backend-icon-search.css'],
            'styling for the backend module of a site package',
            8
        );

        self::assertSame([], $result['withheldCategories']);
        self::assertContains(Hints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    /**
     * The three sentences `D-KNW-066` put into `css-browser-target` are what
     * two sessions report as having stopped them, and they rested on nobody
     * rewriting the hint. Each anticipates one bad argument rather than stating
     * the policy: the checkout as precedent, and `.browserslistrc` as a gate.
     * A rewrite that keeps the policy and drops the two refusals leaves a hint
     * that reads complete and stops neither session.
     */
    #[Decision('D-KNW-066')]
    #[Test]
    public function theBrowserTargetKeepsTheArgumentsItRefuses(): void
    {
        $text = self::statementsOf('css-browser-target');

        self::assertStringContainsString('every engine ships it — Blink, Gecko and WebKit', $text);
        self::assertStringContainsString(
            'Existing core usage of a modern CSS feature is not evidence that the feature is inside the baseline',
            $text,
        );
        self::assertStringContainsString('never rejects a feature, so it is not a gate', $text);
    }

    /**
     * The moment a session needs the browser target is the moment a feature is
     * about to go down on paper. At that moment the query names the feature
     * rather than the policy. Both features below were the candidate of a
     * reported session. Neither reached the hint until its own words stood in
     * `appliesTo` — `feedback/2026-08-10-182543`, judged into `D-KNW-066`.
     *
     * `css-container-queries` stands asserted beside it because it used to
     * carry a second, coarser copy of the policy, which is the one such a query
     * reached.
     */
    #[Decision('D-KNW-066')]
    #[Test]
    public function aQueryNamingAModernCssFeatureReachesTheBrowserTarget(): void
    {
        foreach ([
            'container query for a stuck element',
            'scroll-driven animation in backend Sass',
        ] as $query) {
            $ids = array_column(Hints::find([], $query, 6)['matchedHints'], 'id');

            self::assertContains('css-browser-target', $ids, $query . ' reaches ' . implode(', ', $ids));
        }

        self::assertStringNotContainsString('LTS browser baseline', self::statementsOf('css-container-queries'));
    }

    #[Requirement('R-SCO-004')]
    #[Test]
    public function aPhpClassNameThatCarriesTheWordScssIsStillPhp(): void
    {
        $result = Hints::find(
            ['Classes/ViewHelpers/Format/ScssViewHelper.php', 'Configuration/Services.yaml'],
            '',
            8
        );

        self::assertNotContains(Domains::CSS, $result['domains']);
        foreach ($result['matchedHints'] as $hint) {
            self::assertNotSame(Hints::CATEGORY_CSS, $hint['category'], $hint['id']);
        }
    }

    #[Requirement('R-KNW-002')]
    #[Test]
    public function aQueryAboutLanguageFilesReachesTheLanguageFilesHint(): void
    {
        $result = Hints::find([], 'Language files, XLF labels and how to reference them in TCA', 6);

        self::assertContains('language-files', array_column($result['matchedHints'], 'id'));
    }

    /**
     * A core path answered with a sitepackage hint is told whose work it is.
     *
     * Three core frontend `Classes/` paths drew `frontend-records` and
     * `page-variables-and-processors`. The answer said nothing about either as
     * somebody else's obligation, so the session read them as material for a
     * core patch. The assertion here is the notice and not the rank. Both sit
     * below the two hints that answer the query, and the notice is what the
     * answer still owed (`D-ANS-060`).
     */
    #[Test]
    public function aHintBindingOutsideTheCoreSaysSoOnACorePath(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'frontend link building and access restriction for menus',
            'targetVersion' => '15',
            'paths' => [
                'typo3/sysext/frontend/Classes/Typolink/PageLinkBuilder.php',
                'typo3/sysext/frontend/Classes/ContentObject/Menu/AbstractMenuContentObject.php',
                'typo3/sysext/core/Classes/Domain/Access/RecordAccessVoter.php',
            ],
        ]);

        $ids = array_column($result->data['hints'], 'id');
        // The hint that answers the question leads, which is what the corpus
        // gap cost the reporting session.
        self::assertSame('frontend-access-restriction', $ids[0]);

        $scopes = array_column($result->data['hints'], 'scope', 'id');
        self::assertSame('extension', $scopes['frontend-records'] ?? null);
        self::assertSame('extension', $scopes['page-variables-and-processors'] ?? null);
        self::assertStringContainsString('Binding for work outside the TYPO3 core', $result->text);
    }

    /**
     * Inherited frontend access restriction has three vocabularies in the
     * questions, and none of them is the others.
     *
     * The changelog writes the subject as "access restricted pages", the error
     * a visitor gets says "subsection", and the TCA column is
     * `extendToSubpages`. A caller who arrives with any of the three holds the
     * same question, and until 2026-08-08 the corpus answered none of them. Of
     * every hint it held, one carried `fe_group` and none carried
     * `extendToSubpages`, `groupAccessGranted` or
     * `accessGrantedForPageInRootLine`.
     */
    #[Test]
    #[DataProvider('accessRestrictionQueries')]
    public function everyWordingOfInheritedAccessRestrictionReachesTheSameHint(string $task): void
    {
        $result = Registry::call('typo3_hint_lookup', ['task' => $task, 'targetVersion' => '14']);

        self::assertContains('frontend-access-restriction', array_column($result->data['hints'], 'id'), $task);
        // The statement the whole subject turns on. One method walks the
        // rootline and the other does not, and which one a caller reaches
        // decides whether a restriction passes down.
        self::assertStringContainsString('accessGrantedForPageInRootLine()', $result->text);
        self::assertStringContainsString('Subsection was found and not accessible', $result->text);
    }

    /** @return array<string, array{0: string}> */
    public static function accessRestrictionQueries(): array
    {
        return [
            'the TCA column' => ['extendToSubpages does not work for links'],
            'the changelog wording' => ['access restricted pages are linked but not served'],
            'the error the visitor gets' => ['Subsection was found and not accessible'],
            'the field a site builder set' => ['fe_group on a parent page is not inherited by subpages'],
        ];
    }

    /**
     * `D-KNW-074`. The corpus answered which rows a query returns and stopped
     * there, and the layer after that one fails without a word. An enable field
     * `RecordFactory` moved into `SystemProperties` is absent from the row
     * rather than false, and an absent key reads as permitted.
     *
     * Read off `.checkouts/13.4`, `14.3` and `main`, where `SystemProperties`
     * and its `toArray()` are the same file on all three. So the key list and
     * the types are one statement rather than a table of versions —
     * `D-KNW-078`.
     */
    #[Decision('D-KNW-078')]
    #[Test]
    public function theShapeOfARecordSourcedRowNamesTheFieldsThatMoved(): void
    {
        // The feedback's own query, which reached three hints about other
        // layers and nothing about this one.
        $reported = Hints::find(
            [],
            'Record API SystemProperties hidden starttime endtime fe_group enable fields',
            6,
        );
        self::assertSame('record-system-properties', $reported['matchedHints'][0]['id']);

        $text = self::statementsOf('record-system-properties');

        // That the absence is by design, and what produced it.
        self::assertStringContainsString('unsets it from the properties', $text);

        // The key list and the types, which is what a caller gets wrong next.
        self::assertStringContainsString('isDeleted, isDisabled, isLockedForEditing', $text);
        self::assertStringContainsString('\DateTimeImmutable', $text);
        self::assertStringContainsString('LanguageInfo and VersionInfo objects', $text);

        // The finding itself. A hint listing the accessors without this
        // sentence stops nobody writing the bug.
        self::assertStringContainsString("\$row['hidden'] on such an array is absent, not false", $text);
        self::assertStringContainsString('empty reads as not disabled', $text);

        // The two ways out that are not one. The raw record, which has every
        // column the query selected. And the property access that answers on a
        // table without a typeField and throws on the next one.
        self::assertStringContainsString('getRawRecord()', $text);
        self::assertStringContainsString('only where the record has no record type', $text);

        // And the neighbour, which is where the reporting session landed.
        self::assertStringContainsString('record-system-properties', self::statementsOf('persistence-reading'));
    }

    /**
     * The subject does not exist on 12, which has no `Domain/Record.php` at
     * all, and the `pages` exception arrives with 14. On 13.4 `Page` is no
     * `Record` and `RecordFactory` builds none — `D-KNW-078`.
     */
    #[Decision('D-KNW-078')]
    #[Test]
    public function theRecordShapeIsWithheldFromTheBranchThatHasNoRecordApi(): void
    {
        self::assertNull(Hints::byId('record-system-properties', 12));

        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('record-system-properties', $major)['hints'], 'text'),
        );

        self::assertStringNotContainsString('Page::toArray(true)', $on(13));
        self::assertStringContainsString("\$row['_system']['isDisabled']", $on(13));
        self::assertStringContainsString('Page::toArray(true)', $on(14));
    }

    /**
     * `D-KNW-099`. The change reached no changelog entry, since it rode along
     * in `b0ee153010`, a commit about `f:render.text`. So the corpus is the
     * only source that can carry it. What a caller holds is the exception
     * class, its code and the one field the message names.
     *
     * Read off `.checkouts/13.4` and `14.3`: `lib.contentElement` gained
     * `dataProcessing.1770716912 = record-transformation` in
     * `fluid_styled_content`'s `Helper/ContentElement.typoscript`, which on 13.4
     * carries no `dataProcessing` block at all.
     */
    #[Decision('D-KNW-099')]
    #[Test]
    public function whatAPartialRowCostsIsReachedFromItsException(): void
    {
        // The query the session behind the report says it would have made, in
        // the words it had. The class, the code and the field the first message
        // named.
        $held = Hints::find([], 'IncompleteRecordException 1726046917 sys_language_uid lib.contentElement', 6);
        self::assertSame('content-element-record-row', $held['matchedHints'][0]['id']);

        $text = self::statementsOf('content-element-record-row');

        // The whole field list, which is the point of the statement. The three
        // guards throw one field at a time, so an answer that names fewer of
        // them buys the caller one more attempt each.
        self::assertStringContainsString('sys_language_uid and l18n_parent', $text);
        self::assertStringContainsString('t3ver_wsid, t3ver_oid, t3ver_state and t3ver_stage', $text);
        self::assertStringContainsString(
            'crdate, tstamp, starttime, endtime, deleted, hidden, editlock, sorting, fe_group and rowDescription',
            $text,
        );

        // The two failures before that exception comes at all: the table nobody
        // named on the f:cObject call, and the typeField.
        self::assertStringContainsString('Unable to create Record from non-TCA table', $text);
        self::assertStringContainsString('CType for tt_content', $text);

        // And the value shapes, which throw a TypeError instead and are what
        // the reporting session spent its second and third attempt on.
        self::assertStringContainsString('an integer passes where a numeric string fails', $text);
        self::assertStringContainsString("'0' passes where 0 fails", $text);
    }

    /**
     * `lib.contentElement` runs no data processor on the earlier branches,
     * where the same partial row renders. So the statement would be an account
     * of a failure the caller cannot have — `D-KNW-099`.
     */
    #[Decision('D-KNW-099')]
    #[Test]
    public function whatAPartialRowCostsIsWithheldWhereItRenders(): void
    {
        self::assertNull(Hints::byId('content-element-record-row', 13));
        self::assertNotNull(Hints::byId('content-element-record-row', 14));
    }

    /**
     * The two sinks arrive as different words, since one caller asks about the
     * output escape and the other about a query. What they need is the same
     * read. A hint reachable only through the words it stood for would leave
     * the second caller with the conventions of its surface and no method.
     */
    #[Requirement('R-SKL-004')]
    #[Test]
    #[DataProvider('securityQueries')]
    public function bothSidesOfAnInjectionQuestionReachTheSinkMethod(string $task): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('claim about its sink', $result->text);
        self::assertStringContainsString('does this hop emit the value, or hand it to something else', $result->text);
        self::assertStringContainsString('createNamedParameter()', $result->text);
        self::assertStringContainsString('returns nothing at all', $result->text);
    }

    /** @return array<string, array{0: string}> */
    public static function securityQueries(): array
    {
        return [
            'escaping' => ['review an extension for unescaped user input and raw output in templates'],
            'sql' => ['review an extension for sql injection in its repository query building'],
        ];
    }

    /**
     * The steady state alone left a caller with a German source file and two
     * remedies that both read as consistent with it. It picked the one that
     * changes nothing — D-KNW-011. So the hint names the correction too, and it
     * is an author procedure rather than a v14 mechanism. The unit shape is the
     * same on 12.4, 13.4 and 14.3, and so is the fallback that makes an
     * en.-prefixed file useless. So no answer this server gives may omit it —
     * `D-KNW-032`.
     */
    #[Requirement('R-KNW-033')]
    #[Decision('D-KNW-032')]
    #[Test]
    #[TestWith(['12'])]
    #[TestWith(['13'])]
    #[TestWith(['14'])]
    public function aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes(string $targetVersion): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'backend module registration controller and language files in a project sitepackage extension',
            'targetVersion' => $targetVersion,
        ]);

        self::assertStringContainsString('new labels in English in the source XLF', $result->text);
        self::assertStringContainsString('de.locallang.xlf', $result->text);
        self::assertStringContainsString('a defect to report', $result->text);

        self::assertStringContainsString('keeps its path and it keeps its unit ids', $result->text);
        self::assertStringContainsString('source-language="en" target-language="de"', $result->text);
        self::assertStringContainsString('as <source> and the wording it replaced as <target>', $result->text);
        self::assertStringContainsString('en.-prefixed file is never the correction', $result->text);
        self::assertStringContainsString('default is the fallback of every other locale', $result->text);
    }

    /**
     * The author procedure above says what a correct translation file declares,
     * and an audit reads a rule the other way round. The file in front of it
     * declares nothing, and the loader takes <source> without a word —
     * `D-KNW-050`. So the consequence stands as the defect, and the case asks
     * for it from both directions it arrives in. An audit of the file and an
     * upgrade that changed what the file does.
     */
    #[Requirement('R-KNW-061')]
    #[Decision('D-KNW-050')]
    #[Test]
    #[TestWith(['full conformance audit of an extension, Resources/Private/Language/de.locallang.xlf'])]
    #[TestWith(['upgrade an extension to a new TYPO3 major, German labels render in English'])]
    public function aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt(string $task): void
    {
        $result = Registry::call('typo3_hint_lookup', ['task' => $task, 'targetVersion' => '14']);

        self::assertStringContainsString('declares no target-language is read as a default-language template', $result->text);
        self::assertStringContainsString('discards the <target> wording', $result->text);
        self::assertStringContainsString('Nothing is raised, logged or deprecated', $result->text);
        self::assertStringContainsString('No schema check reports this', $result->text);
        self::assertStringContainsString('leaves target-language optional', $result->text);
    }

    /**
     * The requested language read the same file rather than an attribute of the
     * file, so it kept its translations. A caller told otherwise would go
     * looking for a defect the branch does not have — `D-KNW-050`.
     */
    #[Requirement('R-KNW-061')]
    #[Decision('D-KNW-050')]
    #[Test]
    #[TestWith(['12'])]
    #[TestWith(['13'])]
    public function whatAMissingTargetLanguageCostsIsWithheldWhereItIsFree(string $targetVersion): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'language-files',
            'targetVersion' => $targetVersion,
        ]);

        self::assertStringNotContainsString('default-language template', $result->text);
        self::assertStringNotContainsString('No schema check reports this', $result->text);
        self::assertStringContainsString('new labels in English in the source XLF', $result->text);
    }

    #[Requirement('R-KNW-036')]
    #[Test]
    public function labelReuseStaysAtTheUsageContext(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'language-files',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('translation resource it already uses', $result->text);
        self::assertStringContainsString('matching label elsewhere in the installation is not', $result->text);
        self::assertStringContainsString('actions.createRecord', $result->text);
        self::assertStringContainsString('context-free id such as new', $result->text);
    }

    /**
     * `R-KNW-069`. The corpus answered how to author a label and how the core's
     * generated JavaScript goes stale, and the label bundle is neither. It is
     * an HTTP response with a year on it, built from a cache that keys on
     * nothing about the file. Both steps are due and the build passes over both
     * — `D-KNW-076`'s read of `.checkouts/14.3` and `main` — `D-KNW-079`.
     */
    #[Requirement('R-KNW-069')]
    #[Decision('D-KNW-079')]
    #[Test]
    public function aNewBackendLabelIsToldWhatItCostsBeforeItResolves(): void
    {
        // The feedback's own query, which reached three hints about the layers
        // either side of this one.
        $reported = Hints::find(
            [],
            'JavaScript labels module cache flush after adding XLF trans-unit',
            6,
        );
        self::assertSame('javascript-labels', $reported['matchedHints'][0]['id']);

        // And the two phrasings the failure itself arrives in.
        foreach ([
            'Label is not defined at runtime after adding a new label',
            'my new label does not show up in the backend JavaScript module',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('javascript-labels', $ids[0], $task);
        }

        $text = self::statementsOf('javascript-labels');

        // The symptom, in the words of the search, and where the module that
        // throws it comes from.
        self::assertStringContainsString('Label is not defined: <key>', $text);
        self::assertStringContainsString('createLanguageDomainResponse()', $text);

        // The server half: why the flush is due at all, and which group.
        self::assertStringContainsString('no modification time, no content hash', $text);
        self::assertStringContainsString('cache:flush --group=system', $text);

        // The browser half, which the flush does not reach. Either statement
        // alone leaves the caller where the report found them.
        self::assertStringContainsString('hard reload', $text);
        self::assertStringContainsString('cacheBustInfix', $text);
        self::assertStringContainsString('reads as a flush that did not help', $text);

        // And that nothing before runtime says so.
        self::assertStringContainsString('generate-types:labels', $text);

        // The two hints the session behind the report got both point here.
        self::assertStringContainsString('javascript-labels', self::statementsOf('language-files'));
        self::assertStringContainsString('javascript-labels', self::statementsOf('backend-typescript'));
    }

    /**
     * The subject does not exist below 14: `JavaScriptLanguageDomainProvider`
     * is in neither LTS checkout, which `D-KNW-067` read from the test side.
     * The pointers go with it, because a neighbour naming a hint the caller
     * cannot ask for is worse than no neighbour — `D-KNW-079`.
     */
    #[Requirement('R-KNW-069')]
    #[Decision('D-KNW-079')]
    #[Test]
    public function theLabelModuleIsWithheldFromTheMajorsThatHaveNone(): void
    {
        foreach ([12, 13] as $major) {
            self::assertNull(Hints::byId('javascript-labels', $major), (string) $major);
            self::assertStringNotContainsString(
                'javascript-labels',
                implode("\n", array_column((array) Hints::byId('language-files', $major)['hints'], 'text')),
                (string) $major,
            );
            self::assertStringNotContainsString(
                '~labels/',
                implode("\n", array_column((array) Hints::byId('backend-typescript', $major)['hints'], 'text')),
                (string) $major,
            );
        }

        self::assertNotNull(Hints::byId('javascript-labels', 14));
    }

    /**
     * The two lint rules a core review may not report, and the file that
     * decides everything else. `feedback/2026-08-25-110726` had a cast to `any`
     * on record as a violation and dropped it on this block. It asked that the
     * block survive a trim — `D-FBK-018`. The rule names alone are a list of
     * rules; what stops the finding is the clause after them.
     */
    #[Test]
    public function theLintRulesThatAreSwitchedOffAreNamedAsNoFinding(): void
    {
        $text = self::statementsOf('backend-typescript');

        self::assertStringContainsString('@typescript-eslint/no-explicit-any', $text);
        self::assertStringContainsString('@typescript-eslint/no-inferrable-types', $text);
        self::assertStringContainsString('off on purpose, so neither is a finding', $text);
        self::assertStringContainsString(
            'imitating a neighbouring file turns a rule that is switched off into a diff nobody asked for',
            $text,
            'the move the review was about to make, which the rule names do not describe',
        );

        // The authority beside them, which is what lets a review call the rest
        // untidiness instead of arguing style.
        self::assertStringContainsString('Build/eslint.config.mjs', $text);
        self::assertStringContainsString('untidiness rather than a lint failure', $text);
    }

    /**
     * What an exact assertion is worth, and where it stops being one.
     *
     * `D-KNW-143`. The core's own sanitiser suite asserts with contains, so the
     * shape does not come off the neighbours. Icon markup carries the published
     * asset path, whose directory the publisher names by a package hash, so the
     * same argument does not reach it.
     */
    #[Decision('D-KNW-143')]
    #[Test]
    public function sanitisedOutputIsPinnedWholeAndAPublishedPathIsNot(): void
    {
        $text = self::statementsOf('core-tests');

        self::assertStringContainsString('assertSame against the full serialized string', $text);
        self::assertStringContainsString('SvgSanitizer suite asserts with contains', $text);
        self::assertStringContainsString('names that directory by a package hash', $text);
    }

    /**
     * The name of a test, and what its comment is not for.
     *
     * `D-KNW-142`. The corpus stated the attribute and the fixtures and said
     * nothing about the name or the docblock. So the frame this repository got
     * its correction on was a shape nothing here named. That is a test
     * introduced as the regression test for an issue number.
     */
    #[Decision('D-KNW-142')]
    #[Test]
    public function aTestIsNamedForWhatHoldsRatherThanForTheIssue(): void
    {
        $text = self::statementsOf('core-tests');

        self::assertStringContainsString('The name says what holds', $text);
        self::assertStringContainsString('Resolves: trailer and in the changelog file name', $text);
        self::assertStringContainsString('says nothing a reader needs years later', $text);
    }

    /**
     * The one statement here that is not what the core does.
     *
     * `empty()` stands 2614 times in the sysext classes and the core's own
     * analysis has no rule that could raise one. So a hint that says to write
     * something else is only usable where it says so — `D-KNW-140`. The case
     * asserts the substitution per type as well, because the call is one word
     * and its replacements are four different reads of the value.
     */
    #[Decision('D-KNW-140')]
    #[Test]
    public function theCheckThatSaysWhichValueWasMeantNamesWhatWillNotRaiseIt(): void
    {
        $text = self::statementsOf('php-value-checks');

        self::assertStringContainsString('tests falsiness rather than a value against the type', $text);
        self::assertStringContainsString("0, '0', '', null, false and [] all satisfy it", $text);

        // The four replacements, which are what a caller does with it.
        foreach (['($x ?? false)', '($x ?? null) !== null', '$x !== []', '$x !== \'\''] as $replacement) {
            self::assertStringContainsString($replacement, $text, $replacement . ' is not offered');
        }
        self::assertStringContainsString('never mechanical', $text);

        // And the half that keeps it from reading as something the checkout
        // enforces.
        self::assertStringContainsString('none of the rules it adds is about this call', $text);
        self::assertStringContainsString(
            'a finding to raise rather than a local style to keep',
            $text,
            'the tension with the code around it, which is where the report came from',
        );
    }

    /**
     * What to write instead, beside the rule that nothing pushes it.
     *
     * A session that copied a file nearby heard the two rules are off and
     * nothing more. So the corpus named the diff not to make and not the one to
     * make — `D-KNW-139`. The `@lit/task` half has its assertion where that
     * class already has an owner. Both forms of it stand in the same checkout,
     * so what the statement carries is which one pins the tuple.
     */
    #[Decision('D-KNW-139')]
    #[Test]
    public function whereAnAnnotationIsWrittenIsStatedBesideTheRulesThatAreOff(): void
    {
        $text = self::statementsOf('backend-typescript');

        self::assertStringContainsString('where inference cannot reach and nowhere else', $text);
        self::assertStringContainsString('takes its parameter bare', $text);
        self::assertStringContainsString('second source of truth', $text);
        // The instance that needs no reading of the type system, and the
        // procedure that is the actionable half of the rule.
        self::assertStringContainsString('switches the check off exactly where the query had narrowed it', $text);
        self::assertStringContainsString(
            'evidence about the annotation before it is a reason to put it back',
            $text,
            'the rule stops at the removal and says nothing about the error it raises',
        );
        self::assertStringContainsString('the alias stays and the annotation goes', $text);

        $task = self::statementsOf('backend-lit-task');

        self::assertStringContainsString('typed by args and not by itself', $task);
        self::assertStringContainsString('as const', $task);
        self::assertStringContainsString('Left unpinned the tuple widens', $task);
    }

    /**
     * `R-KNW-072`. The choice of interpreter comes before there is an
     * installation to ask, so the answer has to be readable without one. The
     * numbers a project ends up with are three different claims rather than
     * three forms of one.
     */
    #[Requirement('R-KNW-072')]
    #[Test]
    #[TestWith(['which PHP version do I put in the container for a new project'])]
    #[TestWith(['minimum PHP version'])]
    #[TestWith(['php_version'])]
    #[TestWith(['supported PHP versions'])]
    public function whichInterpreterAVersionNeedsIsAnsweredFirst(string $task): void
    {
        self::assertSame('php-versions', Hints::find([], $task, 6)['matchedHints'][0]['id']);

        $text = self::statementsOf('php-versions');

        // The three sources, each named as what it alone says.
        self::assertStringContainsString('typo3/cms-core declares in its own composer.json', $text);
        self::assertStringContainsString('pins its own dependency resolution to a version at the floor', $text);
        self::assertStringContainsString('The -p option of Build/Scripts/runTests.sh', $text);
        self::assertStringContainsString('not what the TYPO3 project supports an installation on', $text);

        // And the moment of the choice, which is the one the report came from:
        // nothing after it asks again.
        self::assertStringContainsString('a floor below what is ever executed is a claim no run tests', $text);
    }

    /**
     * Read in `.checkouts/` on 2026-08-18, fetched 2026-08-12.
     * `typo3/sysext/core/composer.json` for the constraint,
     * `config.platform.php` in the root manifest for what it resolves against,
     * and the `-p` option of `Build/Scripts/runTests.sh` for the suites. The
     * two LTS lines and the stable one share a constraint; `main` has already
     * left every interpreter a released line runs on — `D-KNW-091`.
     */
    #[Requirement('R-KNW-072')]
    #[Decision('D-KNW-091')]
    #[Test]
    public function eachCoveredLineCarriesItsOwnFloorAndTestedRange(): void
    {
        $on = static fn(int $major): string => implode("\n", array_column(
            (array) Hints::byId('php-versions', $major)['hints'],
            'text',
        ));

        self::assertStringContainsString('PHP ^8.1', $on(12));
        self::assertStringContainsString('PHP 8.1 through PHP 8.5', $on(12));

        foreach ([13, 14] as $major) {
            self::assertStringContainsString('PHP ^8.2', $on($major));
            self::assertStringContainsString('PHP 8.2 through PHP 8.6', $on($major));
            self::assertStringNotContainsString('PHP ^8.1', $on($major));
        }

        // The development line has already left every interpreter a released
        // one runs on. That is the statement an extension author needs and the
        // one a range cannot imply.
        self::assertStringContainsString('PHP ^8.5', $on(15));
        self::assertStringNotContainsString('PHP ^8.2', $on(15));
        self::assertStringNotContainsString('8.4', $on(15));

        // The choosing rule and the three sources hold on every line, so they
        // are one statement each rather than a table.
        foreach ([12, 13, 14, 15] as $major) {
            self::assertStringContainsString('claim no run tests', $on($major), (string) $major);
        }
    }

    #[Requirement('R-KNW-030')]
    #[Test]
    public function aGermanSiteTaskReachesItsLabelLanguageSetup(): void
    {
        $query = 'Set up a German-language site whose core and form labels still render in English';
        $result = Hints::find([], $query, 6);

        self::assertContains('site-label-language', array_column($result['matchedHints'], 'id'));

        $guide = Registry::call('typo3_task_guide', ['task' => $query]);
        self::assertContains('site-label-language', array_column($guide->data['hints'], 'id'));
        self::assertStringContainsString('typo3Language: de', $guide->text);
        self::assertStringContainsString('language:update de', $guide->text);
        self::assertStringContainsString('renderingOptions.submitButtonLabel', $guide->text);
    }

    #[Requirement('R-KNW-034')]
    #[Test]
    public function aSettingIsPlacedByTheReachOfItsValue(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'where a backend module stores a configurable storage pid in a sitepackage',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString('Configuration Belongs to Its Reach', $result->text);
        self::assertStringContainsString('Configuration/Sets/<Set>/settings.definitions.yaml', $result->text);
        self::assertStringContainsString('Extension configuration is installation-wide', $result->text);
        self::assertStringContainsString("scheduler task's own parameters", $result->text);
    }

    #[Requirement('R-KNW-035')]
    #[Test]
    public function aBackendModuleNamesItsShortcutApiAndPostRedirect(): void
    {
        $query = 'doc header buttons and the redirect after a POST in a backend module';
        $onThirteen = Registry::call('typo3_hint_lookup', [
            'task' => $query,
            'targetVersion' => '13',
        ])->text;
        self::assertStringContainsString('makeShortcutButton()', $onThirteen);
        self::assertStringNotContainsString('setShortcutContext(', $onThirteen);
        self::assertStringContainsString('RedirectResponse with HTTP 303 status', $onThirteen);

        $onFourteen = Registry::call('typo3_hint_lookup', [
            'task' => $query,
            'targetVersion' => '14',
        ])->text;
        self::assertStringContainsString('setShortcutContext(', $onFourteen);
        self::assertStringNotContainsString('makeShortcutButton()', $onFourteen);
        self::assertStringContainsString('RedirectResponse with HTTP 303 status', $onFourteen);
    }

    /**
     * The two statements a module needs after its registration and before it
     * has a doc header. What its controller answers with, and what makes the
     * state it keeps per user survive the request.
     *
     * Both come off `.checkouts/12.4` through `.checkouts/main`. There
     * `AboutController` builds its response the one way. Every controller that
     * sets a `ModuleData` property pushes it back itself, since `set()` writes
     * to the object. Neither has a bound: the shape is the same on all four.
     */
    #[Test]
    public function aBackendModuleNamesHowItAnswersAndHowItsStateIsPersisted(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'build a backend module controller that renders a listing and persists the filter the user set',
            'targetVersion' => '14',
        ])->text;

        self::assertStringContainsString('moduleTemplateFactory->create($request)', $result);
        self::assertStringContainsString('renderResponse(', $result);
        self::assertStringContainsString('pushModuleData(', $result);

        foreach ([12, 13, 14] as $major) {
            $statements = implode("\n", array_column(Hints::byId('backend-modules', $major)['hints'], 'text'));
            self::assertStringContainsString('pushModuleData(', $statements, 'not stated for ' . $major);
        }
    }

    /**
     * `D-KNW-070` drew the boundary at what the core's own classes do with a
     * route and then enumerated one identifier shape short. The corpus said a
     * single sentence about `AjaxRoutes.php`, which paired it with `Routes.php`
     * as the same declarative style. That is the general sentence from before
     * the exception existed, and the one that misleads a reader.
     *
     * Read on 12.4, 13.4, 14.3 and main, so nothing has a bound. Every
     * `AjaxRoutes.php` entry registers with the prefix, and
     * `PageRenderer::addAjaxUrlsToInlineSettings()` strips it off again.
     */
    #[Test]
    public function anAjaxRouteIsCarriedThroughAllThreeOfItsSpellings(): void
    {
        // The reporting session's own task, which reached the hint that says one
        // sentence about the file before the one that owns the mechanism.
        $ids = array_column(Hints::find(
            [],
            'AJAX route registration AjaxRoutes UriBuilder buildUriFromRoute backend',
            6,
        )['matchedHints'], 'id');
        self::assertSame('backend-routing-internals', $ids[0]);

        $text = self::statementsOf('backend-routing-internals');

        // One route through all three, which is what makes it a rule rather
        // than three facts.
        self::assertStringContainsString('The key in AjaxRoutes.php is page_tree_data', $text);
        self::assertStringContainsString("buildUriFromRoute('ajax_page_tree_data')", $text);
        self::assertStringContainsString('TYPO3.settings.ajaxUrls.page_tree_data', $text);
        self::assertStringContainsString('fails at runtime when it is written wrong', $text);

        // And the file the reader assumes behaves alike, on both sides.
        self::assertStringContainsString('Routes.php has no such asymmetry', $text);
        self::assertStringContainsString('They are not registered alike', self::statementsOf('backend-modules'));
    }

    #[Requirement('R-KNW-034')]
    #[Test]
    public function siteScopedConfigurationIsOfferedOnlyWhereSiteSettingsExist(): void
    {
        $onTwelve = implode("\n", array_column(
            Hints::byId('configuration-reach', 12)['hints'],
            'text',
        ));
        self::assertStringNotContainsString('settings.definitions.yaml', $onTwelve);
        self::assertStringContainsString('installation-wide', $onTwelve);

        $onThirteen = implode("\n", array_column(
            Hints::byId('configuration-reach', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('settings.definitions.yaml', $onThirteen);
    }

    #[Requirement('R-KNW-030')]
    #[Test]
    public function languagePackActivationUsesTheConfigurationOfTheTargetBranch(): void
    {
        $onThirteen = implode("\n", array_column(
            Hints::byId('site-label-language', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('EXTCONF', $onThirteen);
        self::assertStringNotContainsString('LANG/availableLocales', $onThirteen);

        $onFourteen = implode("\n", array_column(
            Hints::byId('site-label-language', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('LANG/availableLocales', $onFourteen);
        self::assertStringNotContainsString('EXTCONF', $onFourteen);
    }

    /**
     * The sweep the three constants in `D-ANS-002` came off, as far as it
     * stands on record. Eighteen queries with a known right answer, of which
     * fourteen survive. It is here rather than in a session's scrollback. A
     * constant measured against a set nobody kept has no second measurement
     * except against a set somebody rebuilds. Twelve of the fourteen are in
     * this provider. The other two have their assertion where their reason
     * stands, the German one nowhere, because `R-AUD-006` settled that queries
     * to this server are English. A null answer carries the same weight as a
     * hit.
     *
     * @return array<string, array{0: string, 1: ?string}>
     */
    public static function theSweep(): array
    {
        return [
            // The two that named the case: the hint says "the failure is a
            // service-not-found at request time" and names
            // PageTitleProviderManager, and neither phrasing reached it.
            'a service the container did not build' => [
                'my extension service is not found at runtime',
                'di-service-not-found',
            ],
            'the symptom that service produces' => [
                'page title provider does not work',
                'di-service-not-found',
            ],
            'a word nobody indexed' => ['file upload storage configuration', 'fal-writing'],
            'a backend form' => ['validate a form field in the backend', 'tca-formengine'],
            'where something goes' => ['where do I put my backend layouts', 'sitepackage-backend-layouts'],
            'a stale answer' => ['caching does not invalidate', 'caching'],
            'a menu that is wrong' => ['menu does not show all pages', 'page-variables-and-processors'],
            'a label in the wrong language' => [
                'the frontend shows the wrong language label',
                'language-files',
            ],
            'what the caller can see' => ['dark mode colors in my backend module', 'css-light-dark-mode'],
            'the curated vocabulary' => ['event listener', 'events-extension-points'],
            'a question about something else' => ['how do I write a good sonnet', null],
            'a question about somewhere else' => ['what is the weather in Düsseldorf', null],
        ];
    }

    /**
     * The symptom a caller arrives with is not the vocabulary a hint stood
     * under in the index. For a long time the search read only the vocabulary.
     * A hint's own two hundred words were invisible to the matcher. So the
     * subject was reachable through the handful of keywords whoever wrote it
     * happened to think of. Most of these reached nothing at all before the
     * hint text joined the score. A hint that says the thing in so many words
     * answers each — `D-ANS-081`, `D-ANS-084`, `D-ANS-025`, `D-KNW-024`.
     */
    #[Requirement('R-ANS-031')]
    #[Requirement('R-KNW-021')]
    #[Decision('D-ANS-025')]
    #[Decision('D-ANS-084')]
    #[Decision('D-KNW-024')]
    #[Test]
    #[DataProvider('theSweep')]
    public function theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay(
        string $query,
        ?string $expected,
    ): void {
        $result = Hints::find([], $query, 6);
        $ids = array_column($result['matchedHints'], 'id');

        if ($expected === null) {
            self::assertSame([], $ids, $query);
            self::assertNotSame([], $result['availableHints'], 'a miss says what there would have been to find');

            return;
        }

        self::assertContains($expected, $ids, $query);
    }

    /**
     * What the sweep cannot say on its own. The corpus may outgrow the length
     * its matcher had its measurement against, and no single query fails when
     * it does.
     *
     * Measured again on 2026-09-02 at a mean of 302 words over 164 hints, up
     * from 266. Recall over the sweep is whole from 20 to 500, and at 505 «how
     * do I write a good sonnet» gets an answer. The range at 266 was 120 to
     * 320, so it grew at both ends while the corpus grew. The reference stays
     * where it is with more room on either side than it had at the choice. So
     * the watch is on when to measure again rather than on a failure that walks
     * closer — `D-ANS-138`.
     */
    #[Test]
    public function theCorpusHasNotOutgrownTheLengthItsMatcherWasMeasuredAgainst(): void
    {
        $lengths = array_values(Hints::bodyWords());
        $mean = (int) round(array_sum($lengths) / count($lengths));

        self::assertLessThanOrEqual(
            Hints::MAX_MEAN_BODY_WORDS,
            $mean,
            'the mean hint body is ' . $mean . ' words: re-run the sweep and pick UNDILUTED_WORDS again, '
            . 'rather than raising the ceiling',
        );
    }

    #[Requirement('R-KNW-022')]
    #[Decision('D-KNW-032')]
    #[Decision('D-KNW-038')]
    #[Test]
    public function everyHintIsReachedByItsOwnTitle(): void
    {
        // The weakest question a lookup can take, and eight of the nineteen
        // Backend CSS hints failed it. "Color and Surface Tokens" returned
        // nothing, because none of those words is a CSS signal. The domain fell
        // back to PHP, and the hint's own category was then never a candidate.
        // Scoring had nothing to do with it — the hint never reached the
        // matcher. This holds the gate rather than the ranking, so it is about
        // candidacy: the hint has to be somewhere in the answer — `D-KNW-032`,
        // `D-KNW-038`.
        foreach (Hints::load() as $hint) {
            $reached = array_column(Hints::find([], $hint['title'], 6)['matchedHints'], 'id');

            self::assertContains($hint['id'], $reached, $hint['title'] . ' does not reach ' . $hint['id']);
        }
    }

    #[Requirement('R-KNW-022')]
    #[Test]
    public function whatACallerCanSeeReachesTheHintAboutIt(): void
    {
        // A caller names the symptom in the words of what is in front of them,
        // a colour, a dark mode, a shadow. Not in the vocabulary of the
        // subsystem that produces it. The dark-mode half is a sweep query and
        // has its assertion there. This is the other half, which the sweep
        // records as a miss and which is not one to fix here. "my button looks
        // wrong" is the thirteenth of the eighteen and carries no CSS signal at
        // all. So the domain falls back to PHP and no CSS hint is ever a
        // candidate. A component by name is typo3_component_lookup's question
        // anyway, with the markup as the answer rather than a convention.
        $seen = Hints::find([], 'dark mode colors in my backend module', 6);
        self::assertContains('css-light-dark-mode', array_column($seen['matchedHints'], 'id'));

        $named = Hints::find([], 'my button looks wrong', 6);
        self::assertNotContains(
            Hints::CATEGORY_CSS,
            array_column($named['matchedHints'], 'category'),
            'a component by its name is the component lookup\'s to answer',
        );
    }

    #[Decision('D-KNW-008')]
    #[Test]
    public function theTestApiAProjectWritesItsTestsWithReachesTheProjectHint(): void
    {
        // The API is the same one the core tests use, so the two test hints
        // both answer here. But a test in a package of this repository runs in
        // a harness the core hint knows nothing about. That hint is the one
        // that has to come first — `D-KNW-008`.
        $result = Hints::find(
            ['packages/my_sitepackage/Tests/Functional/HeroCarouselTest.php'],
            'FunctionalTestCase executeFrontendSubRequest CSV fixture for my content element',
            6,
        );

        $ids = array_column($result['matchedHints'], 'id');
        self::assertContains('project-extension-tests', $ids);
        self::assertLessThan(
            array_search('core-tests', $ids, true) ?: PHP_INT_MAX,
            array_search('project-extension-tests', $ids, true),
        );
    }

    #[Requirement('R-ANS-008b')]
    #[Test]
    public function aShortTermIsNotMatchedAsThePrefixOfALongerWord(): void
    {
        // Prefix matching is how a stem finds every form of its word. At three
        // characters there is no form left to find, and what it finds instead
        // is whatever starts with those letters. "fal" reached seven hints
        // through "fallback" and "false". It is worse than ordinary noise
        // because a term weighs by how few documents carry it. An accident that
        // lands in exactly one document becomes the strongest term in the query
        // and decides the answer.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );

        self::assertSame(['fal-storages-drivers', 'fal-basics'], $reached('fal storage driver'));

        // The same rule on the curated vocabulary, which is the stronger path:
        // an appliesTo hit passes whatever the coverage. "fal" is one of
        // fal-basics's patterns and a plain substring used to find it.
        self::assertSame([], array_filter(
            $reached('the label is falsch'),
            static fn(string $id): bool => str_starts_with($id, 'fal-'),
        ));

        // And the tolerance itself is intact from four characters up.
        self::assertContains('events-extension-points', $reached('hooks'));
    }

    #[Decision('D-ANS-022')]
    #[Decision('D-ANS-050')]
    #[Test]
    public function aCompoundIsFoundWhicheverWayTheCallerJoinsIt(): void
    {
        // Everything a query passes through stands in spaced compounds: the
        // domain keywords, the hint patterns, the markers. One hyphen used to
        // miss all of them at once. The first query is the sentence a session
        // wrote its own task down in, and it reached nothing. The domain fell
        // back to PHP. The search read the `content element` pattern verbatim
        // in a query that writes it with a hyphen — `D-ANS-050`.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );

        self::assertContains(
            'content-elements',
            $reached('show assigned related groups in a backend content-element preview template'),
        );

        // The domain gate is the half that fails first and without a word. So
        // its assertion stands where the hint's own category has to earn its
        // place. "dark mode" is a CSS signal and "dark-mode" was none. That
        // left every Backend CSS hint out of the candidates rather than out of
        // the rank.
        self::assertContains('css-light-dark-mode', $reached('dark-mode'));
        self::assertContains('fluid-viewhelpers', $reached('view-helper'));

        // And the separator inside one word stays alone, which is what
        // D-ANS-006 is about. These are identifiers, not compounds a caller
        // joined up, and each is still one token.
        foreach (['tt_content', 'list_type'] as $identifier) {
            self::assertContains('content-elements', $reached($identifier), $identifier);
        }
        self::assertContains('content-element-preview', $reached('mod.web_layout'));
    }

    /**
     * The gate that closed on length rather than on relevance.
     *
     * `MIN_COVERAGE` is a share of the query, and the dilution weight damped it
     * by the length of the body that carried the term. So past `200 * e` words
     * a hint was no candidate for any one-term query it had no curation for.
     * Not ranked lower: dropped, with nothing to say so.
     *
     * Exactly one hint in the corpus stated each of these four, in neither its
     * title nor its `appliesTo`, and each reached nothing at all. `showitem` is
     * a second hint's keyword now, and what this holds stays the same. The
     * query reaches the long body, not that it is the only one — `D-ANS-025`.
     */
    #[Decision('D-ANS-025')]
    #[Test]
    #[TestWith(['showitem', 'content-elements'])]
    #[TestWith(['allowProperties', 'extbase-arguments'])]
    #[TestWith(['sys_registry', 'initial-content-import-once'])]
    #[TestWith(['PidInList', 'frontend-records'])]
    #[TestWith(['withQueryParameters', 'extension-test-frontend-request'])]
    public function aTermOnlyOneHintStatesReachesItHoweverLongThatHintIs(
        string $query,
        string $expected,
    ): void {
        self::assertContains(
            $expected,
            array_column(Hints::find([], $query, 6)['matchedHints'], 'id'),
            $query,
        );
    }

    /**
     * And the half the dilution weight is still for. A long enough text covers
     * part of a query the corpus has no answer to. «write» and «good» are in
     * half the hints there are. What keeps that from an answer is the share the
     * floor asks for. Nothing carries "sonnet", so the cover is never whole and
     * the exception above never applies.
     *
     * The negative controls of the sweep assert the outcome; this asserts the
     * reason. That is the part a change to the floor would take away and leave
     * them where they are — `D-ANS-025`.
     */
    #[Decision('D-ANS-025')]
    #[Test]
    public function aHintThatCarriesPartOfAQueryStillDoesNotAnswerIt(): void
    {
        // Both terms belong to this one, and it is long enough for the damp to
        // reach it. That is what makes it the case worth an assertion. It stood
        // as "the longest hint there is" until the split by subject made the
        // longest one that carries neither term.
        $diluted = 'extension-test-site';
        self::assertGreaterThan(
            Hints::UNDILUTED_WORDS,
            Hints::bodyWords()[$diluted],
            'a hint the matcher does not damp would prove nothing here',
        );

        $whole = array_column(
            Hints::find([], 'diffed truncation', 6)['matchedHints'],
            'id',
        );
        self::assertContains($diluted, $whole, 'both terms are its own');

        $part = array_column(
            Hints::find([], 'diffed sonnet', 6)['matchedHints'],
            'id',
        );
        self::assertNotContains($diluted, $part, 'half a query is a mention, whatever the other half was');
    }

    /**
     * A symptom names the layer the failure showed in, and the hint that
     * explains it lives in another one — `D-ANS-081`. The words of this query
     * are Fluid and TypoScript, `datahandler-placement` is PHP, and "reverse
     * order" is what its curator wrote down for exactly this sentence.
     *
     * What keeps the cross to that case is that no selected hint claims the
     * phrase. Where one does, the layers the query named can answer it
     * themselves, and aTypeScriptTestPathIsNotAnsweredWithPhpunit holds that
     * half — `D-ANS-084`.
     */
    #[Requirement('R-ANS-031')]
    #[Decision('D-ANS-084')]
    #[Test]
    public function aSymptomReachesTheHintThatExplainsItFromAnotherDomain(): void
    {
        $result = Hints::find([], 'the content elements render in reverse order', 6);

        self::assertContains('datahandler-placement', array_column($result['matchedHints'], 'id'));
        self::assertNotContains(
            Domains::PHP,
            $result['domains'],
            'the query says nothing about the layer the hint is in, which is the point',
        );
    }

    /**
     * The phrase a caller quotes is an error message, and a message carries
     * punctuation. Curated as `is of type "boolean"`, it read as no phrase at
     * all while the shape test asked for letters and spaces. So the one hint
     * that explains the symptom stayed behind the gate for the caller in the
     * class that raised it — `D-KNW-145`.
     */
    #[Requirement('R-ANS-031')]
    #[Decision('D-KNW-145')]
    #[Test]
    public function aQuotedErrorMessageCrossesTheGateTheWayAPlainPhraseDoes(): void
    {
        $result = Hints::find(
            ['Classes/Domain/Model/Event.php'],
            'my template broke: is of type "boolean"',
            6,
        );

        self::assertContains('fluid-object-access', array_column($result['matchedHints'], 'id'));
        self::assertNotContains(
            Domains::FLUID,
            $result['domains'],
            'the query names the class the value came from, which is what puts the hint outside',
        );
    }

    #[Requirement('R-KNW-021')]
    #[Decision('D-ANS-084')]
    #[Test]
    public function theCuratedVocabularyStillDecidesWhereItWasWritten(): void
    {
        // Scoring the text is additive: where somebody anticipated a phrasing,
        // that hint is still what comes back first. Otherwise every hint that
        // mentions a subject in passing would compete with the one about it —
        // `D-ANS-081`, `D-ANS-084`.
        $result = Hints::find([], 'event listener', 6);

        self::assertSame('events-extension-points', $result['matchedHints'][0]['id']);
    }

    #[Requirement('R-ANS-006')]
    #[Test]
    public function aHintCanBeAskedForByItsIdInsteadOfGuessedAt(): void
    {
        $result = Hints::find([], '', 6, 'language-files');

        self::assertSame(['language-files'], array_column($result['matchedHints'], 'id'));
        self::assertSame([], $result['domains'], 'nothing was inferred, so nothing is claimed');

        // An id is often the first id a caller learned, and the subject it
        // belongs to is larger than it. The neighbours are the hints in its own
        // domains, and it is not among them: the answer carries it in full.
        $available = array_column($result['availableHints'], 'id');
        self::assertNotSame([], $available, 'a hit names what stands beside it');
        self::assertNotContains('language-files', $available);
        $asked = Hints::byId('language-files');
        self::assertNotNull($asked);
        foreach ($available as $id) {
            $neighbour = Hints::byId($id);
            self::assertNotNull($neighbour, $id . ' is not a hint');
            self::assertNotSame(
                [],
                array_intersect($neighbour['domains'], $asked['domains']),
                $id . ' is not in a domain the hint that was asked for is in',
            );
        }
    }

    /**
     * The ids an answer offers are the ids that caller can ask for.
     *
     * `record-routing` holds from 14, so on 12 it stays back. The index came
     * without the target, so the same answer said there is no such hint and
     * then listed it. Seven hints are in that position on 12 and three on 13,
     * and none on the two majors above them. That is why it turned without a
     * word — `D-ANS-075`.
     */
    #[Decision('D-ANS-075')]
    #[Test]
    public function theIdsOfferedAreTheOnesThatMajorHas(): void
    {
        $result = Hints::find([], '', 6, 'record-routing', [12]);

        self::assertSame([], $result['matchedHints']);
        self::assertNotContains('record-routing', array_column($result['availableHints'], 'id'));
        self::assertContains('record-routing', array_column(Hints::find([], '', 6, 'record-routing', [14])['matchedHints'], 'id'));
    }

    #[Requirement('R-ANS-006')]
    #[Test]
    public function anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo(): void
    {
        $result = Hints::find([], '', 6, 'language-file');

        self::assertSame([], $result['matchedHints']);
        self::assertContains('language-files', array_column($result['availableHints'], 'id'));
    }

    /**
     * The index an id call carried was two thirds of its answer. The head of it
     * was where the ids that session went around already stood.
     *
     * A miss is the other half of the same field and keeps everything. An id
     * that matched nothing has to say what there would have been to find
     * (`R-ANS-006`), and the bound below is not about that list.
     */
    #[Test]
    public function anIdThatMatchedWithholdsItsNeighboursUntilTheyAreAskedFor(): void
    {
        $withheld = Registry::call('typo3_hint_lookup', ['id' => 'events-extension-points', 'targetVersion' => '14.3']);
        self::assertSame([], $withheld->data['availableHints']);
        self::assertGreaterThan(0, $withheld->data['availableHintsWithheld'], 'nothing was withheld to count');
        // R-ANS-030: a caller cannot ask for a list it was never told about.
        self::assertStringContainsString('availableHints true', $withheld->text);

        $asked = Registry::call('typo3_hint_lookup', [
            'id' => 'events-extension-points',
            'targetVersion' => '14.3',
            'availableHints' => true,
        ]);
        self::assertSame(0, $asked->data['availableHintsWithheld']);
        self::assertCount($withheld->data['availableHintsWithheld'], $asked->data['availableHints']);
        self::assertLessThan(
            strlen($asked->text),
            strlen($withheld->text),
            'withholding the index did not make the answer smaller',
        );

        $miss = Registry::call('typo3_hint_lookup', ['id' => 'there-is-no-such-hint']);
        self::assertSame([], $miss->data['hints']);
        self::assertNotSame([], $miss->data['availableHints'], 'a miss stopped saying what there was to find');
        self::assertSame(0, $miss->data['availableHintsWithheld']);
    }

    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-075')]
    #[Test]
    public function aMissNamesWhatThereWouldHaveBeenToFind(): void
    {
        // The index comes off the candidates the score has just ranked. So a
        // hit offers what it did not return and a miss offers the closest thing
        // there is — `D-ANS-075`.
        $hit = Hints::find(['typo3/sysext/core/Classes/DataHandling/DataHandler.php'], 'DataHandler', 6);
        $matched = array_column($hit['matchedHints'], 'id');
        $available = array_column($hit['availableHints'], 'id');
        self::assertNotSame([], $available, 'the index is what an answer is corrected with, hit or miss');
        self::assertSame([], array_intersect($matched, $available), 'what the answer carries in full is not offered again');

        $miss = Hints::find([], 'how do I write a good sonnet', 6);
        self::assertSame([], $miss['matchedHints']);
        self::assertContains('dependency-injection', array_column($miss['availableHints'], 'id'));
        foreach ($miss['availableHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
        }
    }

    /**
     * The near-miss is the answer the index is worth most on, and it was the
     * one answer without it (`D-KNW-055`).
     *
     * The whole domain index rather than the categories the matched hints are
     * in, because a cut to those drops hints and none of the length. A near
     * miss is at most as long as the miss beside it — `D-ANS-075`.
     */
    #[Decision('D-ANS-075')]
    #[Test]
    public function anAnswerStillNamesTheIdsItDidNotReturn(): void
    {
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'coding standards php-cs-fixer setup for an extension',
            'paths' => ['composer.json', 'Classes/', 'ext_localconf.php'],
            'targetVersion' => '14.3',
        ]);

        $matched = array_column($result->data['hints'], 'id');
        $available = array_column($result->data['availableHints'], 'id');

        self::assertNotSame([], $matched, 'this query is a near miss, and a miss would prove nothing here');
        self::assertContains(
            'extension-static-analysis',
            $available,
            'the neighbouring subject the report had no route to',
        );
        self::assertSame([], array_intersect($matched, $available));
        // Named in the copy as well as in the payload. The id is what a second
        // call takes, and a client that reads only the text has it too.
        self::assertStringContainsString('extension-static-analysis', $result->text);
    }

    /**
     * The index the near-miss answer carries is only worth its lines if the
     * near miss is at the top of it.
     *
     * The file order showed a session `javascript-unit-tests` at position 43 of
     * 46 while the matcher had just ranked it seventh of the 13 it admitted.
     * The session spent six calls on a rebuild of that hint's subject out of
     * the filesystem instead. What the limit cut is what the query came closest
     * to and did not get, so it is what the index opens with — `D-ANS-075`.
     */
    #[Decision('D-ANS-075')]
    #[Test]
    public function theIndexNamesWhatTheLimitCutBeforeWhatTheFloorRefused(): void
    {
        $paths = [
            'Build/Sources/Sass/component/_module.scss',
            'typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html',
            'typo3/sysext/backend/Resources/Public/Css/backend.css',
            'Build/Sources/TypeScript/viewpage/main.ts',
        ];
        $task = 'backend docheader sticky CSS, Sass build artifacts, Fluid partial markup';

        $cut = array_slice(array_column(Hints::find($paths, $task, 100, null, [15])['matchedHints'], 'id'), 6);
        $available = array_column(Hints::find($paths, $task, 6, null, [15])['availableHints'], 'id');

        self::assertSame('javascript-unit-tests', $available[0], 'the hint the limit cut by one place');
        self::assertSame(
            $cut,
            array_slice($available, 0, count($cut)),
            'everything the matcher admitted and the limit cut, in the order it ranked them',
        );

        // The other id the same report names is the other case, and the split
        // between them is most of the finding. The floor refused this one, so
        // it stands among the rest and no order would have raised it.
        self::assertGreaterThan(
            0,
            (int) array_search('css-tokens-specificity', $available, true),
        );
    }

    /**
     * An answer that says a category is the wrong advice for this task used to
     * list 19 of its hints by id underneath the sentence.
     *
     * The index was a read of the domains while the matcher had already refused
     * those candidates, so the two halves of one answer disagreed. Built from
     * the candidates, the index cannot offer what the same call withheld —
     * `D-ANS-075`.
     */
    #[Decision('D-ANS-075')]
    #[Test]
    public function theIndexIsNotOfferingWhatTheSameAnswerWithheld(): void
    {
        $result = Hints::find(
            ['packages/sitepackage/Resources/Private/Templates/Page/Default.html'],
            'style the website frontend theme with our own CSS',
            6,
        );

        self::assertSame([Hints::CATEGORY_CSS], $result['withheldCategories']);
        self::assertNotContains(
            Hints::CATEGORY_CSS,
            array_column($result['availableHints'], 'category'),
        );
    }

    /**
     * Whether a removal is breaking is a question the corpus answers, and it
     * answers that the annotation does not settle it.
     *
     * A patch review reported a read of the class and its history by hand. It
     * had to establish that `GifBuilder` is public API while the removed member
     * is `@internal`. It asked for a lookup that reports the marker. The core
     * has filed removals of `@internal` members both ways, `Breaking-110319`
     * because non-Composer bootstraps called them, `Important-108796` because
     * nothing did. So a tool that answered the marker would answer beside the
     * question. What was missing is the rule — `D-FBK-038`.
     */
    #[Decision('D-FBK-038')]
    #[Test]
    public function whetherARemovalIsBreakingIsAnsweredWithoutTheMarker(): void
    {
        $reached = array_column(
            Hints::find([], 'is removing this internal method breaking', 6)['matchedHints'],
            'id',
        );
        self::assertContains('deprecated-apis', $reached);

        $written = json_encode(Hints::byId('deprecated-apis'), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('@internal', $written);
        self::assertStringContainsString('never the answer on its own', $written);
        self::assertStringContainsString('whether anything outside the core calls it', $written);

        // And the brief says it before it says how to write the entry, because
        // the entry is what a wrong answer produces. Asked as the caller's own
        // question rather than as a change type. Breaking is a property of a
        // change and none of the types a caller may state (`D-GUI-027`).
        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Is removing this internal method from a public core class breaking',
        ])->text;
        self::assertStringContainsString('Settle first whether the change is breaking at all', $brief);
    }

    /**
     * What a deprecation carrying the docblock alone raises, which is nothing.
     *
     * A conformance audit went to the installed core for the severity of
     * `InfoboxViewHelper::STATE_ERROR` — `feedback/2026-08-03-164805`. The
     * corpus stated the marking as a pair and had no word for the half of it
     * the constant carries. Read in `.checkouts/14.3`: that file has no
     * `trigger_error` at all, and none can go in. A read of a class constant
     * runs nothing in the class that declares it. The other half is what keeps
     * the read off a method. The trigger for one can sit in the caller, or in
     * the magic accessor of a compatibility trait. So a file without one
     * settles nothing there.
     */
    #[Test]
    public function whatADeprecationCarryingTheDocblockAloneRaisesIsStated(): void
    {
        $reached = array_column(
            Hints::find([], 'does a deprecated class constant raise a deprecation', 6)['matchedHints'],
            'id',
        );
        self::assertContains('deprecated-apis', $reached);

        $written = json_encode(Hints::byId('deprecated-apis'), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('no trigger_error can be attached to it anywhere', $written);
        self::assertStringContainsString('fatal error in the major that removes it', $written);
        self::assertStringContainsString('ClassConstantMatcher', $written);

        // The guard that keeps that reading off a method, and the measurement
        // it rests on: the attribute the core marks nothing with.
        self::assertStringContainsString('does not have to sit in the declaring body', $written);
        self::assertStringContainsString('#[\\\\Deprecated] attribute', $written);

        // The branch is not something the server is blind to where an
        // installation answers, which is where the changelog comes from.
        self::assertStringNotContainsString('does not know your branch', $written);
        self::assertStringContainsString('typo3_project_describe', $written);
    }

    /**
     * R-KNW-066. The third breaking move, which the corpus stated for neither
     * of the two it names.
     *
     * A core patch drafted an optional third parameter onto the public
     * `GifBuilder::start()` of a class that is not final. Every check the
     * project has was green on it. What raised it was the
     * `breaking-not-assessed` line of `typo3_commit_message_guide`, after the
     * diff and the entry existed. The path is not what carries it. Keyed on
     * core `Classes/` the hint outranked `fal-basics` on a FAL question and
     * pushed it out of a brief.
     */
    #[Requirement('R-KNW-066')]
    #[Test]
    public function wideningAPublicSignatureIsAnsweredAsTheBreakingMoveItIs(): void
    {
        foreach ([
            'can I add an optional parameter to this public method',
            'is adding a parameter breaking',
            'does changing a method signature break subclasses',
        ] as $task) {
            $reached = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('public-api-surface', $reached, $task);
        }

        $written = json_encode(Hints::byId('public-api-surface'), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('an optional one included', $written);
        self::assertStringContainsString('override point', $written);
        // The half that decides the target branch, which is why it cannot wait
        // for commit-message time.
        self::assertStringContainsString('cannot carry the signature change at all', $written);
        // And the two shapes that avoid it, one of which is not the cheap way
        // out it looks like.
        self::assertStringContainsString('Add rather than widen', $written);
        self::assertStringContainsString('final first is no cheaper', $written);

        // The rules behind it, which carry the entries the hint may not date.
        $bodies = implode("\n", array_column(Documents::search('breaking change'), 'body'));
        self::assertStringContainsString('An added parameter is one', $bodies);
        self::assertStringContainsString('Important-107342', $bodies);
        self::assertStringContainsString('FullyScanned', $bodies, 'the section it stands beside was cut to fit it');

        // And the check that reached the reporting session last, now naming
        // the move it was silent about.
        $check = CommitMessage::create([
            'keyword' => 'BUGFIX',
            'summary' => 'Do a thing',
            'issue' => '1',
            'releases' => ['main'],
            'workflow' => CommitMessage::WORKFLOW_CORE,
        ])['checks'];
        $message = implode("\n", array_column($check, 'message'));
        self::assertStringContainsString('whose signature it narrows or widens', $message);
        self::assertStringContainsString('whether or not the parameter is optional', $message);
    }

    /**
     * D-KNW-072. The move that is not a move, which every statement above misses.
     *
     * A review had to say whether a patch that changes rendered markup was
     * breaking. Every statement the corpus carried keyed on a member removed,
     * narrowed or widened. The question outranks the signature one on its own
     * words rather than by construction, so the measurement is here.
     * `public-api-surface` shares its `appliesTo` phrases and would otherwise
     * answer the wrong half — `D-KNW-073`.
     */
    #[Decision('D-KNW-073')]
    #[Test]
    public function aChangedRenderingIsAnsweredAsTheBreakingMoveWithNoMember(): void
    {
        foreach ([
            'is a change to the rendered frontend HTML of existing content classified Breaking',
            'changed default TypoScript output breaking change',
            'is this breaking, the patch changes lib.parseFunc_RTE so a caption gets a p tag',
        ] as $task) {
            $reached = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('breaking-without-a-moved-member', $reached, $task);
            if (in_array('public-api-surface', $reached, true)) {
                self::assertLessThan(
                    array_search('public-api-surface', $reached, true),
                    array_search('breaking-without-a-moved-member', $reached, true),
                    $task . ' — the signature hint answers a question that was not asked',
                );
            }
        }

        $written = json_encode(Hints::byId('breaking-without-a-moved-member'), JSON_THROW_ON_ERROR);
        // The word the four-type definition dropped, which is what carries it.
        self::assertStringContainsString('break or affect third-party code', $written);
        // The boundary the sweep of `.checkouts/` established, both halves: what
        // Important is for, and what decides between the two where it is not.
        self::assertStringContainsString('leaves existing output alone', $written);
        self::assertStringContainsString('the target branch decides', $written);
        self::assertStringContainsString('rare exemption', $written);

        // And the entry point the reporting session did reach, which stated the
        // definition one word narrower than its source.
        $bodies = implode("\n", array_column(Documents::search('breaking change changelog'), 'body'));
        self::assertStringContainsString('may break or affect third-party code', $bodies);
        self::assertStringContainsString('breaking-without-a-moved-member', $bodies);
    }

    /**
     * D-KNW-073. The reader who has already ruled the word Breaking out.
     *
     * The rule landed and did not take. Its pointer sat under `Breaking` in a
     * section whose lead exempted a `BUGFIX` by its keyword. So a review of a
     * fix that dropped a read of a configured option read past both and judged
     * the obligation alone — `feedback/2026-08-24-100635`. This holds the entry
     * point for the question of whether an entry is due at all, beside the one
     * for which type it is.
     */
    #[Decision('D-KNW-073')]
    #[Test]
    public function theQuestionWhetherAnEntryIsOwedReachesTheTestThatDecidesIt(): void
    {
        foreach ([
            'does this bugfix owe a changelog entry',
            'removed configuration option is that breaking',
        ] as $task) {
            $reached = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('breaking-without-a-moved-member', $reached, $task);
        }

        // The section the report's own rule lookup landed on. It now says what
        // a fix has to change nothing of to be the casual one. The bullet above
        // it carries the hint, so the reader who fails a test has the route one
        // line up rather than in another document.
        $bodies = (string) preg_replace(
            '/\s+/',
            ' ',
            implode("\n", array_column(Documents::search('changelog entry review readiness'), 'body')),
        );
        self::assertStringContainsString(
            'changes nothing an installation renders, is configured by, or has documented',
            $bodies,
        );
        self::assertStringContainsString('breaking-without-a-moved-member', $bodies);
    }

    #[Requirement('R-KNW-001')]
    #[Test]
    public function aPathAloneReachesTheHintForTheSubsystemItIsIn(): void
    {
        // Both were subsystems with no hint at all, and an extension
        // maintenance task got generic TCA and Fluid advice for them.
        $reached = static fn(string $path): array => array_column(
            Hints::find([$path], '', 6)['matchedHints'],
            'id'
        );

        self::assertContains('upgrade-wizards', $reached('Classes/Updates/AccordionElementUpdate.php'));
        self::assertContains('frontend-dataprocessors', $reached('Classes/DataProcessing/CsvFileProcessor.php'));
    }

    #[Requirement('R-KNW-003')]
    #[Test]
    public function theFrontendRenderingPathIsAnswered(): void
    {
        // Every Fluid hint was about backend modules. The mechanism every site
        // stands on, how the render finds a page template and how it reaches
        // its content, stood nowhere at all.
        $result = Hints::find([], 'Fluid templates frontend', 6);

        self::assertContains('frontend-page-rendering', array_column($result['matchedHints'], 'id'));
    }

    /**
     * `D-KNW-124`. The call that missed had a path scope. Probed with the two
     * classes the session read, the first hit was
     * `system-extension-boundaries`, whose `appliesTo` is every core file there
     * is. So the case asserts the paths beside the three questions. A hint the
     * words reach and the path does not is the same miss again.
     */
    #[Decision('D-KNW-124')]
    #[Test]
    public function theStateAFrontendRequestLeavesInTheRendererIsAnswered(): void
    {
        $reached = static fn(array $paths, string $task): array => array_column(
            Hints::find($paths, $task, 6)['matchedHints'],
            'id',
        );

        self::assertSame('frontend-render-pipeline-state', $reached(
            [
                'typo3/sysext/core/Classes/Page/PageRenderer.php',
                'typo3/sysext/frontend/Classes/Http/RequestHandler.php',
            ],
            '',
        )[0]);

        foreach ([
            'when is PageRenderer bodyContent populated and cleared during a frontend request',
            'which PageRenderer properties does reset() not reset',
            'does config.disableAllHeaderCode skip PageRenderer head assembly',
        ] as $question) {
            self::assertContains('frontend-render-pipeline-state', $reached([], $question), $question);
        }
    }

    /**
     * `D-KNW-124`. What the renderer does with the body it just rendered is the
     * half that moved. It empties where the state serialises on 14 and up,
     * stays on 13, and never clears at all on 12. The phases around it did not
     * move, so they have no bound. A hint that is a table of versions is what
     * this entry named as its own failure.
     */
    #[Decision('D-KNW-124')]
    #[Test]
    public function whatBecomesOfTheRenderedBodyIsStatedPerMajor(): void
    {
        $texts = static fn(int $major): string => implode("\n", array_column(
            Hints::byId('frontend-render-pipeline-state', $major)['hints'],
            'text',
        ));

        foreach ([12, 13] as $major) {
            self::assertStringContainsString('leaves `bodyContent` standing', $texts($major));
            self::assertStringNotContainsString('empties `bodyContent`', $texts($major));
        }
        foreach ([14, 15] as $major) {
            self::assertStringContainsString('empties `bodyContent`', $texts($major));
            self::assertStringNotContainsString('leaves `bodyContent` standing', $texts($major));
        }

        // `reset()` clears the body everywhere it has one, and on 12 it does
        // not touch the property at all. So the render after it starts from
        // what the one before left, which is the case the enumeration exists
        // for.
        self::assertStringContainsString('only ever overwritten by `setBodyContent()`', $texts(12));
        self::assertStringNotContainsString('only ever overwritten', $texts(13));

        // The asymmetry itself holds on every covered line and says so by
        // carrying no binding.
        foreach ([12, 13, 14, 15] as $major) {
            self::assertStringContainsString('It does not clear `cssLibs`', $texts($major));
            self::assertStringContainsString('the meta tag registry is a service of its own', $texts($major));
        }
    }

    /**
     * `D-KNW-124`. The same silence twice: a session wrote a functional test
     * against a fixture that cannot fail on what the renderer assembles, and
     * nothing said so. The trap is the shared base rather than the fixture
     * layered over it, so the statement names the file that carries the flag.
     */
    #[Decision('D-KNW-124')]
    #[Test]
    public function theFixtureThatCanAssertNoHeadOutputIsNamed(): void
    {
        $statements = self::statementsOf('core-tests');

        self::assertStringContainsString('EXT:core/Tests/Functional/Fixtures/Frontend/JsonRenderer.typoscript', $statements);
        self::assertStringContainsString('config.disableAllHeaderCode = 1', $statements);
        self::assertStringContainsString('frontend-render-pipeline-state', $statements);
    }

    /**
     * `D-KNW-147`. The list of what a set directory may hold read as if a key
     * outside it had no effect. A session that tried one took every page of
     * every site on that set down with an HTTP 500.
     * `YamlSetDefinitionProvider::createDefinition()` spreads the parsed file
     * into a readonly constructor, so the message names the properties rather
     * than the mistake.
     */
    #[Decision('D-KNW-147')]
    #[Test]
    public function aKeyTheSetDefinitionHasNoParameterForIsSaidToBeFatal(): void
    {
        $texts = static fn(int $major): string => implode("\n", array_column(
            Hints::byId('site-sets', $major)['hints'],
            'text',
        ));

        foreach ([13, 14, 15] as $major) {
            self::assertStringContainsString('Invalid properties', $texts($major));
            self::assertStringContainsString('answers HTTP 500', $texts($major));
            // The site keeps its own routing entry, which is the question the
            // same session was asking when it tried the key.
            self::assertStringContainsString('StaticRouteResolver', $texts($major));
        }

        // The file a set may hold arrived with the route keys it declares. So
        // the list has its bound where it changed rather than a statement for
        // all three.
        self::assertStringNotContainsString('route-enhancers.yaml', $texts(13));
        self::assertStringContainsString('route-enhancers.yaml', $texts(14));

        // And what makes that file usable. The enhancers of the dependencies
        // merge rather than replace, so a set completes another set's enhancer,
        // and the dependency is what holds the fragment together.
        self::assertStringContainsString('replaceAndAppendScalarValuesRecursive', $texts(14));
        self::assertStringContainsString('Enhancer type cannot be empty', $texts(14));
        self::assertStringNotContainsString('replaceAndAppendScalarValuesRecursive', $texts(13));
    }

    /**
     * What a record type's schema holds, which core TCA hides.
     *
     * A core bugfix turned on the difference between the table schema and the
     * sub-schema. It would have dropped every relation field an installation
     * keeps out of its forms. Every core suite was green, because core TCA
     * shows its own (`D-KNW-148`).
     */
    #[Decision('D-KNW-148')]
    #[Test]
    public function whatASubSchemaHoldsIsStatedAgainstTheTableSchema(): void
    {
        foreach ([13, 14, 15] as $major) {
            $text = implode("\n", array_column(Hints::byId('tca-sub-schema', $major)['hints'], 'text'));

            self::assertStringContainsString('showitem names and nothing else', $text);
            self::assertStringContainsString('findRelevantFieldsForSubSchema', $text);
            // The silent half, which is why the statement is due at all.
            self::assertStringContainsString('passes every core suite', $text);
            // What a type may change, and what it may not.
            self::assertStringContainsString('array_replace_recursive', $text);
            self::assertStringContainsString('relation map is built once for the table', $text);
            // And the table whose type lives in another table's row.
            self::assertStringContainsString('isPointerToForeignFieldInForeignSchema', $text);
        }

        // The hint about using the API at all names it and says what it
        // prevents, because that is the one a schema question reaches first.
        self::assertStringContainsString(
            'tca-sub-schema',
            implode("\n", array_column(Hints::byId('tca-schema-api', 14)['hints'], 'text')),
        );
    }

    /**
     * The sections a changelog type owes, and the shape its entries have.
     *
     * A session that wrote an `Important` entry found this corpus, the shipped
     * template and the tree with three different things to say. The tree is
     * what a reviewer compares against (`D-KNW-149`).
     */
    #[Decision('D-KNW-149')]
    #[Test]
    public function whatAChangelogTypeOwesIsSaidApartFromWhatItsEntriesCarry(): void
    {
        $hint = implode("\n", array_column(Hints::byId('documentation-changelog', 14)['hints'], 'text'));
        $document = Documents::read('core/contribution/changelog');

        foreach (['hint' => $hint, 'document' => $document] as $where => $text) {
            // The obligation, which is what it always said.
            self::assertStringContainsString('Description section', $text, $where);
            // And the read it stood for.
            self::assertStringContainsString('owes no Impact', $text, $where);
            self::assertStringContainsString('plenty of them carry one', $text, $where);
            // The template that describes the tree least well, named as such.
            self::assertStringContainsString('template is the one that disagrees with the tree', $text, $where);
            self::assertStringContainsString('neighbouring entry', $text, $where);
        }
    }

    /**
     * What answers a not-found, beside the hint that reads what one was.
     *
     * `page-not-found-within-a-site` named the site's `errorHandling` as the
     * thing that changes the outcome and said nothing about it, so a repair
     * came from memory (`D-KNW-150`).
     */
    #[Decision('D-KNW-150')]
    #[Test]
    public function whatASiteAnswersAnErrorWithIsStatedBesideWhatANotFoundWas(): void
    {
        foreach ([12, 13, 14, 15] as $major) {
            $text = implode("\n", array_column(Hints::byId('site-error-handling', $major)['hints'], 'text'));

            self::assertStringContainsString('errorCode', $text);
            self::assertStringContainsString('errorContentSource', $text);
            // The fallback nothing reports, and the status the body does not
            // change.
            self::assertStringContainsString('errorCode 0 is the fallback', $text);
            self::assertStringContainsString('the answer stays HTTP 404', $text);
            // The word that keeps a page out of the menu, beside the one that
            // takes it out of delivery.
            self::assertStringContainsString('nav_hide', $text);
        }

        // The fourth handler and the Extbase route arrived together, and 12.4
        // has neither.
        self::assertStringNotContainsString(
            'LoginRedirect',
            implode("\n", array_column(Hints::byId('site-error-handling', 12)['hints'], 'text')),
        );
        self::assertStringContainsString(
            'showPageNotFoundIfTargetNotFoundException',
            implode("\n", array_column(Hints::byId('site-error-handling', 13)['hints'], 'text')),
        );

        // And the diagnostic hint sends the reader there.
        self::assertStringContainsString(
            'site-error-handling',
            implode("\n", array_column(Hints::byId('page-not-found-within-a-site', 14)['hints'], 'text')),
        );
    }

    /**
     * What a records sitemap advertises, and what nothing checks.
     *
     * An audit crawled 3101 advertised addresses and found the page behind them
     * rendering no record at all, with every response HTTP 200 (`D-KNW-151`).
     */
    #[Decision('D-KNW-151')]
    #[Test]
    public function whatARecordsSitemapAdvertisesIsStatedAgainstWhatThePageRenders(): void
    {
        foreach ([12, 13, 14, 15] as $major) {
            $text = implode("\n", array_column(Hints::byId('record-xml-sitemap', $major)['hints'], 'text'));

            self::assertStringContainsString('url.pageId', $text);
            self::assertStringContainsString('additionalWhere', $text);
            // The silence that makes it a defect nobody sees.
            self::assertStringContainsString('never asks about', $text);
            // And what the query does apply, which both reports had backwards.
            self::assertStringContainsString('frontend group restriction', $text);
            self::assertStringContainsString('rather than replacing it', $text);
        }

        // What brings the sitemap at all, and the routing that arrived with the
        // set a major later.
        $thirteen = implode("\n", array_column(Hints::byId('record-xml-sitemap', 13)['hints'], 'text'));
        $fourteen = implode("\n", array_column(Hints::byId('record-xml-sitemap', 14)['hints'], 'text'));
        self::assertStringContainsString('typo3/seo-sitemap', $thirteen);
        self::assertStringNotContainsString('PageType enhancer', $thirteen);
        self::assertStringContainsString('PageType enhancer', $fourteen);
        // The null coalescing that makes a setting of zero a page id.
        self::assertStringContainsString('?? the requested page', $fourteen);

        // Reaching the record and advertising it are two configurations, and
        // each names the other.
        self::assertStringContainsString(
            'record-xml-sitemap',
            implode("\n", array_column(Hints::byId('record-routing', 14)['hints'], 'text')),
        );
    }

    /**
     * The grammar of a new group layout, and the item that disappears.
     *
     * A session that regrouped its own table into palettes and tabs got the
     * hint about how to extend a core palette instead. It wrote the strings
     * from memory and invented the test that proves them (`D-KNW-152`).
     */
    #[Decision('D-KNW-152')]
    #[Test]
    public function theGrammarOfAShowitemItemIsStatedWithWhatItSkips(): void
    {
        foreach ([12, 13, 14, 15] as $major) {
            $text = implode("\n", array_column(Hints::byId('tca-showitem', $major)['hints'], 'text'));

            self::assertStringContainsString('three segments separated by semicolons', $text);
            // Where the label of a palette lives, which is the empty middle
            // segment's whole reason.
            self::assertStringContainsString('palettes.<name>.label', $text);
            self::assertStringContainsString('A --div-- has no label', $text);
            // The silent skip, and the guard for it.
            self::assertStringContainsString('nothing is logged', $text);
            self::assertStringContainsString('every item of every type against the columns', $text);
        }

        // Writing your own form and extending somebody else's name each other.
        self::assertStringContainsString(
            'tca-showitem',
            implode("\n", array_column(Hints::byId('tca-core-palette', 14)['hints'], 'text')),
        );
    }

    /**
     * The environment answering before TYPO3 does.
     *
     * A correct static route for robots.txt answered HTTP 404 from DDEV's own
     * nginx, with nothing in any TYPO3 log. The same route works on the Apache
     * it deploys to (`D-KNW-153`).
     */
    #[Decision('D-KNW-153')]
    #[Test]
    public function whatTheLocalWebserverAnswersFirstIsStatedWhereTheOwnershipIs(): void
    {
        $text = implode("\n", array_column(Hints::byId('project-configuration-files', 14)['hints'], 'text'));

        self::assertStringContainsString('location = /robots.txt', $text);
        // Why the same route works on the deploy target.
        self::assertStringContainsString('public/.htaccess', $text);
        // And the fix that does not fight DDEV for a generated file.
        self::assertStringContainsString('webserver_type: apache-fpm', $text);

        // Named where a reader meets the failure.
        self::assertStringContainsString(
            'project-configuration-files',
            implode("\n", array_column(Hints::byId('site-sets', 14)['hints'], 'text')),
        );
    }

    /**
     * `D-KNW-087`. The hint said an area the layout never declared "renders
     * empty with no error". A session that skipped it got HTTP 500 on every
     * page instead. `ContentAreaViewHelper::render()` throws for anything that
     * is not a `ContentArea`, and `StandardVariableProvider::getByPath()` hands
     * it null for an identifier `ContentAreaCollection::has()` does not know.
     * The empty render is the other path, a column the layout does declare with
     * no records in it. So the case asserts the two apart rather than together.
     */
    #[Decision('D-KNW-087')]
    #[Test]
    public function anUndeclaredContentAreaIsSaidToThrow(): void
    {
        $texts = static fn(int $major): string => implode("\n", array_column(
            Hints::byId('page-content-areas', $major)['hints'],
            'text',
        ));

        foreach ([14, 15] as $major) {
            self::assertStringContainsString('exception 1770212183', $texts($major));
            self::assertStringContainsString('HTTP 500 on every page', $texts($major));
            self::assertStringContainsString('renders empty and reports nothing', $texts($major));
            self::assertStringNotContainsString('renders empty with no error', $texts($major));
        }

        // What an undeclared column costs is not the same failure on both, and
        // the caller is on one of them. The generated identifier is a
        // deprecation on 14 and a refusal at layout resolution on 15.
        self::assertStringContainsString('raises a deprecation', $texts(14));
        self::assertStringNotContainsString('exception 1780173420', $texts(14));
        self::assertStringContainsString('exception 1780173420', $texts(15));
        self::assertStringNotContainsString('raises a deprecation', $texts(15));

        // The hint the session reached three calls after the failure. It has to
        // answer the task the caller is on and the trace they arrive with.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );
        self::assertContains('page-content-areas', $reached('rendering a content area a backend layout never declared'));
        self::assertContains('page-content-areas', $reached('f:render.contentArea throws exception 1770212183'));
    }

    /**
     * `D-KNW-092`. A session met HTTP 500 four times and fetched the rendered
     * page each time. It wrote three extractions to get one line of message out
     * of it. Nothing named the file the same exception is in. The trap is the
     * statement rather than an aside beside it. The failure that session hit is
     * the first entry of `IGNORED_EXCEPTION_CODES`. So a caller sent to the log
     * for it finds nothing and has to hear which half they are on.
     */
    #[Decision('D-KNW-092')]
    #[Test]
    public function aFailingInstallationIsSaidWhatItWritesDownAndWhatItOnlyShows(): void
    {
        $statements = self::statementsOf('installation-exception-output');

        self::assertStringContainsString('var/log/typo3_*.log', $statements);
        self::assertStringContainsString('IGNORED_EXCEPTION_CODES', $statements);
        self::assertStringContainsString('1396795884', $statements);
        self::assertStringContainsString('SYS/displayErrors', $statements);

        // The session's own two queries, neither of which reached anything.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );
        self::assertContains('installation-exception-output', $reached('site returns HTTP 500 where is the exception logged'));
        self::assertContains('installation-exception-output', $reached('the site answers HTTP 500 and I cannot read the error'));

        // A session meets a 500 in whatever layer it works in, which is the
        // cross `D-ANS-084` measured. The query names Fluid and the mechanism
        // is PHP.
        $crossed = Hints::find([], 'my Fluid template now returns HTTP 500 and the page shows no message', 6);
        self::assertContains('installation-exception-output', array_column($crossed['matchedHints'], 'id'));
        self::assertNotContains(Domains::PHP, $crossed['domains']);

        // `D-KNW-054`'s subject is the boot and this one is the symptom. The
        // hint that owns the procedure still leads where the query is the task.
        $boot = $reached('boot the installation from a fresh clone');
        self::assertSame('installation-boot', $boot[0]);
    }

    /**
     * `R-KNW-059`. The session this comes from ran `rm` on a cache directory
     * after a template edit. That is the one cache in the list that was already
     * correct. A compiled template keys on the file's modification time, so the
     * edit rewrote it. What kept answering with the old page was the page
     * cache, in another group and in the database — `D-KNW-027`.
     */
    #[Requirement('R-KNW-059')]
    #[Decision('D-KNW-027')]
    #[Test]
    public function aChangeIsToldWhichCacheGroupHoldsItsOldOutput(): void
    {
        // The feedback's own query, which reached nothing.
        $result = Hints::find([], 'clearing the fluid_template (and code) caches after template changes', 6);
        self::assertContains('page-cache-flushing', array_column($result['matchedHints'], 'id'));

        // It arrives from three kinds of change, and they are three domains.
        foreach ([
            'which cache do I flush after a TypoScript change',
            'clear the caches after a TCA change',
            'my Fluid template change does not show on the page',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('page-cache-flushing', $ids, $task);
        }

        $text = self::statementsOf('page-cache-flushing');

        // The half that says no command is due, and the half that says which
        // one is. Either alone leaves the caller where the feedback found them.
        self::assertStringContainsString('modification time', $text);
        self::assertStringContainsString('var/cache/code/fluid_template', $text);
        self::assertStringContainsString('--group=pages', $text);
        self::assertStringContainsString('--group=system', $text);
        self::assertStringContainsString('Typo3DatabaseBackend', $text);

        // `cache:flushtags` is the core's own command on 14 and on neither of
        // the majors below it.
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('page-cache-flushing', $major)['hints'], 'text'),
        );
        self::assertStringContainsString('cache:flushtags', $on(14));
        self::assertStringNotContainsString('cache:flushtags', $on(13));
        self::assertStringNotContainsString('cache:flushtags', $on(12));
    }

    /**
     * The nearest thing the corpus had was `caching`, whose statements are
     * about the `cacheConfigurations` entry and which frontend a payload wants.
     * The flush of a cache and the declaration of one come in the same words
     * and are not the same question (`D-KNW-027`).
     */
    #[Requirement('R-KNW-059')]
    #[Decision('D-KNW-027')]
    #[Test]
    public function clearingACacheAndDeclaringOneAreDifferentQuestions(): void
    {
        $clearing = array_column(Hints::find([], 'how do I clear the TYPO3 caches', 6)['matchedHints'], 'id');
        self::assertSame('page-cache-flushing', $clearing[0]);

        foreach ([
            'declare a new cache for my extension',
            'inject a cache into a service instead of asking CacheManager',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('caching', $ids[0], $task);
        }
    }

    /**
     * A session read `SYS/caching/cacheConfigurations` and took it for a
     * registration file with a resolved registry behind it, which `D-ANS-003`'s
     * seventh read found there is not. The array is the whole of what
     * CacheManager holds, and what it hides is the defaults that fill the keys
     * an entry omits.
     */
    #[Test]
    public function theCacheRegistryIsTheConfiguredArrayAndTheDefaultsThatFillIt(): void
    {
        foreach ([
            'which caches does this installation actually have',
            'what frontend class does a cache entry run with',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertContains('caching', $ids, $task);
        }

        $text = self::statementsOf('caching');

        // The identifier the array does not carry, which is what makes the
        // array complete rather than nearly so.
        self::assertStringContainsString("'di'", $text);

        // Every default, because an entry that omits a key runs on it, and the
        // group is the one that decides when the flush comes.
        self::assertStringContainsString('VariableFrontend', $text);
        self::assertStringContainsString('Typo3DatabaseBackend', $text);
        self::assertStringContainsString("group 'all'", $text);
    }

    #[Requirement('R-KNW-011')]
    #[Decision('D-KNW-038')]
    #[Test]
    public function anExtbasePluginHasAHintOfItsOwn(): void
    {
        // There was none at all: the task returned datahandler-persistence,
        // which is about DataHandler, and asking by id returned the index —
        // `D-KNW-038`.
        $result = Hints::find(
            [],
            'Extbase plugin in a project extension: domain model, repository, controller, plugin registration, '
            . 'persistence mapping to a custom table, pagination and search',
            6
        );
        self::assertContains('extbase', array_column($result['matchedHints'], 'id'));

        // The failures are the half that cost the session, and each of them
        // answers with a wrong page rather than with an error.
        $text = implode("\n", array_column((array) Hints::byId('extbase-arguments')['hints'], 'text'));
        self::assertStringContainsString('cacheHash', $text);
        self::assertStringContainsString('allowProperties', $text);
    }

    /**
     * The extension a file sits in is not what the file is. A bare extension
     * key in `appliesTo` cannot tell the two apart — `D-KNW-038`. The paths and
     * the task are the ones a core bugfix arrived with. `ImageService` resolves
     * files and builds URLs, and the plugin brief was the largest block of the
     * answer it got.
     *
     * `hints:coverage` cannot see this. It counts what nothing reaches, and all
     * of it reaches a hint that answers everything.
     */
    #[Decision('D-KNW-038')]
    #[Test]
    public function aFileBelowAnExtensionIsAnsweredByItsRole(): void
    {
        $result = Hints::find(
            [
                'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
                'typo3/sysext/extbase/Classes/Service/ImageService.php',
            ],
            'Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource',
            6,
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('fluid-viewhelpers', $ids, 'what the task is about');
        self::assertSame(
            [],
            array_values(array_filter($ids, static fn(string $id): bool => str_starts_with($id, 'extbase'))),
            'nothing in the Extbase family bears on a file-resolution helper',
        );

        // And the same word asked as a question still reaches the family.
        self::assertContains(
            'extbase',
            array_column(Hints::find([], 'do I need extbase for a list of records', 6)['matchedHints'], 'id'),
        );
    }

    /**
     * The two sentences a core session credited with deciding its work, and the
     * one it had to read neighbouring files for. It asked that the first two
     * survive any trim of the block. An assertion is the only form this
     * repository has for a keep-request — `D-FBK-018`.
     */
    #[Test]
    public function aViewHelperPatchIsToldWhichTestItOwesAndWhichChangelogType(): void
    {
        $viewHelpers = self::statementsOf('fluid-viewhelpers');

        self::assertStringContainsString('Tests/Functional/ViewHelpers/', $viewHelpers);
        self::assertStringContainsString(
            'a ViewHelper needs a rendering context',
            $viewHelpers,
            'the reason, which is what makes the test layer obviously right',
        );
        self::assertStringContainsString('Documentation/Changelog/', $viewHelpers);
        self::assertStringContainsString(
            'documentation-changelog',
            $viewHelpers,
            'where the type of the entry is decided',
        );

        // The obligation stands in the Fluid block and its discharge in the
        // docs one, which a task about a ViewHelper bug never asks for. The
        // changelog intent matches the task text, and that text is about the
        // bug. The pointer is what crosses it, so the sentence it leads to has
        // to decide the type rather than only name the four.
        $changelog = self::statementsOf('documentation-changelog');
        self::assertStringContainsString('may require manual action', $changelog);
        self::assertStringContainsString('marked for a planned removal', $changelog);
        self::assertStringContainsString(
            'the directory of the minor version',
            $changelog,
            'where the file goes',
        );
    }

    #[Requirement('R-KNW-009')]
    #[Test]
    public function registeringSomethingSoTheCoreFindsItIsCovered(): void
    {
        // "How do I register X so the core actually finds it" fell between the
        // component catalog and the subsystem conventions. A read of the core
        // sources by hand answered it.
        $element = Hints::find([], 'register a new content element with its own CType', 6);
        self::assertContains('content-elements', array_column($element['matchedHints'], 'id'));

        $di = Hints::byId('di-service-not-found');
        self::assertNotNull($di);
        self::assertStringContainsString(
            'public: true',
            implode("\n", array_column($di['hints'], 'text')),
            'a provider the container resolves by class name is not found unless it is public'
        );
    }

    #[Requirement('R-KNW-008')]
    #[Test]
    public function aProductSectionInASitepackageIsAnsweredWithHowItIsBuilt(): void
    {
        // The task that produced nothing usable: a list, a detail view and a
        // teaser element for records of an own table. It got two hints about
        // backend forms and shipped content, and the mechanism the whole task
        // consists of stood nowhere.
        $result = Hints::find(
            [],
            'Add a product list and product detail rendering plus a product teaser element to a sitepackage '
            . 'extension: custom database table, TCA, frontend content elements, routing for the detail view',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');

        self::assertContains('frontend-records', $ids);
        self::assertContains('sitepackage-layout', $ids);
        self::assertContains('record-routing', $ids, 'the detail view has to be addressable');
        self::assertNotContains(Hints::CATEGORY_CSS, array_column($result['matchedHints'], 'category'));
    }

    #[Requirement('R-KNW-025')]
    #[Test]
    public function siteLocalSettingsSourcesAreAnsweredWithTheirPrecedence(): void
    {
        $result = Hints::find(
            [
                'config/sites/main/config.yaml',
                'config/sites/main/settings.yaml',
                'Configuration/Sets/Printworks/settings.definitions.yaml',
            ],
            'Site settings: settings.yaml of a site versus the inline settings key in config.yaml, and settings shipped by a site set',
            6
        );
        self::assertContains('site-sets', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('site-sets', 'site-set-settings');
        self::assertStringContainsString('alternatives, not layers', $text);
        self::assertStringContainsString('does not merge', $text);
        self::assertStringContainsString('backend settings editor', $text);
    }

    /**
     * A site's own `config.yaml` is YAML, and YAML detects as PHP. So a
     * question about which key in it becomes which accessor is a PHP query
     * however much it is about sets. That is why the case asks `site-sets` from
     * both domains. Under TypoScript alone it was no candidate for the question
     * the session behind the report had. The three hints it did get back are
     * the ones `D-KNW-115` measured.
     */
    #[Decision('D-KNW-115')]
    #[Test]
    #[DataProvider('siteConfigurationKeyQueries')]
    public function theKeyASiteNamesItsSetsUnderIsAnsweredBySiteSets(string $task): void
    {
        $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');

        self::assertSame('site-sets', $ids[0] ?? null, $task . ' reaches ' . implode(', ', $ids));

        $text = self::statementsOf('site-sets');
        self::assertStringContainsString('naming it under dependencies', $text);
        self::assertStringContainsString('the accessor is Site::getSets()', $text);
        self::assertStringContainsString('becomes Site::getSettings()', $text);
        self::assertStringContainsString('renders without a sys_template record', $text);
    }

    /** @return array<string, array{0: string}> */
    public static function siteConfigurationKeyQueries(): array
    {
        return [
            'the question the reporting session had' => [
                'What in config/sites/<id>/config.yaml makes Site::isTypoScriptRoot() return true, '
                . 'and which YAML key becomes Site::getSets()?',
            ],
            'the key and the accessor' => ['site config.yaml dependencies key sets Site::getSets'],
            'the word the caller starts from' => ['which yaml key in a site configuration names its site sets'],
        ];
    }

    /**
     * The word the two subjects share, kept from carrying one into the other.
     *
     * `dependencies` is a bare word in a question about a `composer.json`, and
     * its curation onto `site-sets` put that hint first on this query. The hint
     * that answers it had no curated phrase to match at all. What reaches
     * `site-sets` is the key in its two spelled-out forms.
     */
    #[Decision('D-KNW-115')]
    #[Test]
    public function aComposerDependencyQuestionIsAnsweredByTheRepositoryHint(): void
    {
        $ids = array_column(
            Hints::find([], 'what composer dependencies does my extension declare', 6)['matchedHints'],
            'id',
        );

        self::assertSame('extension-repository-dependencies', $ids[0] ?? null, implode(', ', $ids));
    }

    #[Requirement('R-KNW-032')]
    #[Test]
    public function projectSystemConfigurationStatesItsOwnershipBoundary(): void
    {
        $result = Hints::find(
            ['config/system/additional.php', 'config/system/.gitignore'],
            'Who owns additional.php in a TYPO3 project that uses DDEV?',
            6,
        );
        self::assertSame('project-configuration-files', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('project-repository-layout', 'project-configuration-files');
        self::assertStringContainsString('settings.php is the configuration array written by TYPO3', $text);
        self::assertStringContainsString('additional.php is optional PHP loaded afterwards', $text);
        self::assertStringContainsString('Remove that marker to take the file over', $text);
        self::assertStringContainsString('config/system/.gitignore', $text);
        self::assertStringContainsString('verify additional.php is still tracked', $text);
        self::assertStringContainsString('local-development environment, not the production configuration source', $text);
        self::assertStringContainsString('IS_DDEV_PROJECT', $text);
        self::assertStringContainsString('never commit production secrets', $text);
    }

    /**
     * The corpus named the database settings alone, which made the two ways out
     * of the generated file look interchangeable. A session took the file over
     * and wrote that half back. The installation answered every request with
     * the trusted hosts exception nobody had named — `R-KNW-060`. The database-
     * less half is the same omission read the other way, because DDEV's
     * generator writes the DB block unconditionally — `D-KNW-049`.
     */
    #[Requirement('R-KNW-060')]
    #[Decision('D-KNW-049')]
    #[Test]
    public function theDdevSettingsAnswerNamesEverySectionItGenerates(): void
    {
        $result = Hints::find(
            [],
            'DDEV writes driver mysqli and host db into additional.php although the project runs on SQLite '
            . 'with omit_containers: [db]',
            6,
        );
        self::assertSame('project-configuration-files', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('project-configuration-files');
        self::assertStringContainsString('DB for its own database container', $text);
        self::assertStringContainsString('GFX for the ImageMagick in that container', $text);
        self::assertStringContainsString('MAIL for its mail catcher', $text);
        self::assertStringContainsString('SYS with trustedHostsPattern, devIPmask and displayErrors', $text);
        self::assertStringContainsString('supplying the three non-database sections', $text);
        self::assertStringContainsString('drops the trusted hosts pattern', $text);

        // What the generator cannot configure, and the one route left where it
        // cannot: the DB block merges over what settings.php carries.
        self::assertStringContainsString('no variant that reads the driver', $text);
        self::assertStringContainsString('omit_containers: [db] cannot leave the file generated', $text);
        self::assertStringContainsString('drop the DB section, keep GFX, MAIL and SYS', $text);
    }

    /**
     * `R-KNW-072`. The corpus said every start rewrites the generated file,
     * which is what a clone does not get. DDEV picks the settings paths from an
     * installed TYPO3. So the start before `composer install` writes nothing,
     * and the site answers 1396795884 while the console reports success.
     *
     * Two places state what DDEV does with that file, so this holds both. The
     * hint that owns it and the checklist a boot brief carries — `D-KNW-085`.
     */
    #[Requirement('R-KNW-071')]
    #[Decision('D-KNW-085')]
    #[Test]
    public function theDdevSettingsAnswerSaysWhenThatFileIsWritten(): void
    {
        $text = self::statementsOf('project-configuration-files');

        // What decides it, and what the session sees instead of the file.
        self::assertStringContainsString('Typo3Version.php', $text);
        self::assertStringContainsString('writes nothing there', $text);
        self::assertStringContainsString('leaves no additional.php', $text);
        self::assertStringContainsString('exception 1396795884', $text);

        // Neither way out is the restart alone: the hooks run after the
        // detection, so the file arrives at the next start.
        self::assertStringContainsString('before the post-start hooks', $text);
        self::assertStringContainsString('starts it again afterwards', $text);

        $checklist = implode("\n", Registry::call('typo3_task_guide', [
            'task' => 'Bring the demo installation this repository declares up on this machine',
            'changeType' => 'operations',
        ])->data['checklist']);

        self::assertStringNotContainsString('rewritten on every start', $checklist);
        self::assertStringContainsString('every start that finds an installed TYPO3', $checklist);
        self::assertStringContainsString('precedes composer install', $checklist);
        self::assertStringContainsString('the detection runs before the hooks', $checklist);
    }

    /**
     * `R-KNW-076`. The restart and the committed file stood as a pair of
     * equals, "starts it again afterwards; a project-owned additional.php is
     * the other way out". A session in an extension repository read the second
     * as an alternative of equal weight. It committed `config/system/` and got
     * a correction from the user it worked for. `D-KNW-085` had already decided
     * the ordering; what did not land was the wording that carries it.
     *
     * This holds both surfaces, for the reason `D-KNW-085` states: a repair of
     * one leaves the other with the pair flat.
     */
    #[Requirement('R-KNW-076')]
    #[Decision('D-KNW-085')]
    #[Test]
    public function theRoutesOutOfAGeneratedAdditionalPhpAreOrdered(): void
    {
        $text = self::statementsOf('project-configuration-files');

        // The restart is the route, and it is over rather than repeated.
        self::assertStringContainsString('that second start ends the ordering for good', $text);

        // The committed file is the second choice, and the hint says what it
        // costs.
        self::assertStringContainsString('the other way out rather than the first', $text);
        self::assertStringContainsString('puts installation state under version control', $text);

        // Which repository may pay that cost, and which one may not.
        self::assertStringContainsString('deploys from the checkout', $text);
        self::assertStringContainsString('config/ is ignored whole', $text);
        self::assertStringContainsString('extension-repository-installation', $text);

        $checklist = implode("\n", Registry::call('typo3_task_guide', [
            'task' => 'Set up a DDEV development installation for a TYPO3 extension so its site set '
                . 'can be verified in a real frontend',
            'changeType' => 'operations',
            'paths' => ['.ddev/config.yaml', 'composer.json', 'Configuration/Sets/Example/config.yaml'],
        ])->data['checklist']);

        self::assertStringContainsString('the other way out rather than the first', $checklist);
        self::assertStringContainsString(
            'typo3_hint_lookup with id=extension-repository-installation',
            $checklist,
        );
    }

    /**
     * `D-FBK-018`. What a boot brief gets credit for is not the verdict but the
     * file, command or number the caller can check the verdict against. Each of
     * the three was in no assertion. So a summary could have taken the
     * decidable half away while the conclusion beside it stayed and read as the
     * same.
     */
    #[Test]
    public function aBootBriefCarriesTheTestThatDecidesABranch(): void
    {
        $checklist = implode("\n", Registry::call('typo3_task_guide', [
            'task' => 'Boot the local DDEV development installation for the blog extension repository: '
                . 'composer install, TYPO3 setup, site and demo content, so the backend and frontend answer',
            'changeType' => 'operations',
            'paths' => ['.ddev/config.yaml', 'composer.json', 'blog'],
        ])->data['checklist']);

        // What DDEV reads to decide the file belongs there, which is what makes
        // the missing additional.php a write order rather than a TYPO3 fault.
        self::assertStringContainsString(
            'vendor/typo3/cms-core/Classes/Information/Typo3Version.php',
            $checklist,
        );

        // The file whose presence decides whether --create-site has an effect.
        self::assertStringContainsString('required package ships Initialisation/data.xml', $checklist);

        // And what the generated file supplies, which is what a hand-written
        // database connection into it costs.
        self::assertStringContainsString('Leave config/system/additional.php to DDEV', $checklist);
    }

    /**
     * The setup command prints neither the user it created nor the password it
     * got. It sets `BE/installToolPassword` to the same value without an option
     * of its own. So a boot that reports only the backend user hands over half
     * of what the installation now holds. `feedback/2026-08-18-070515`, whose
     * session says it would not have named the install tool half on its own.
     */
    #[Test]
    public function theAdminPasswordIsAnsweredWithHowItIsRecovered(): void
    {
        $checklist = implode("\n", Registry::call('typo3_task_guide', [
            'task' => 'Install TYPO3 unattended and report the backend user to whoever asked for the installation',
            'changeType' => 'operations',
        ])->data['checklist']);

        self::assertStringContainsString('State the admin username and the password in your reply', $checklist);
        self::assertStringContainsString('the URL of the backend they belong to', $checklist);
        self::assertStringContainsString('Generate that password rather than inventing a quiet default', $checklist);
        self::assertStringContainsString('sets BE/installToolPassword in config/system/settings.php', $checklist);
        self::assertStringContainsString('backend:resetpassword', $checklist);
    }

    /**
     * `R-KNW-065`. The reported brief carried four PHP hints and none of them
     * was about a boot of anything, because the corpus said nothing about it.
     * The change type `operations` moved the checklist and could not move this.
     * The four cards `D-SKL-012` put first landed the install rather than the
     * boot. What the task asks for is the second run of each step. A database
     * from elsewhere, a user that is already taken, a base that names another
     * host. Every one of those fails without a word — `D-KNW-054`.
     */
    #[Requirement('R-KNW-065')]
    #[Decision('D-KNW-054')]
    #[Test]
    public function bootingADeclaredInstallationIsAnsweredBeforeThePhpFallback(): void
    {
        $reported = Hints::find(
            [],
            'Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, '
            . 'start the local environment, import the demo database and fileadmin, build frontend assets, '
            . 'create a backend user, verify the site responds',
            6,
        );
        self::assertSame('installation-boot', $reported['matchedHints'][0]['id']);

        $text = self::statementsOf('installation-boot');

        // Not the setup command, which is what a boot query used to get once it
        // reached the corpus at all.
        self::assertStringContainsString('refuses an existing config/system/settings.php', $text);
        self::assertStringContainsString('typo3_project_describe reports the DDEV hooks', $text);

        // What closes the gap between an imported database and the code in
        // front of it, and what it deliberately leaves standing.
        self::assertStringContainsString('add, change, create_table and change_table', $text);
        self::assertStringContainsString('Nothing is dropped and nothing is renamed', $text);
        self::assertStringContainsString('database:updateschema is not the core\'s command', $text);
        self::assertStringContainsString('hash, pages and rootline caches', $text);

        // The two answers of the user step that only a script sees.
        self::assertStringContainsString('asked even under --no-interaction', $text);
        self::assertStringContainsString('1670797516', $text);

        // Why the booted site answers nothing on the host it is reachable at.
        self::assertStringContainsString('host, scheme and port on the route as requirements', $text);
        self::assertStringContainsString('1396795884', $text);

        // And where the files should be, which the database and not the
        // repository says.
        self::assertStringContainsString('sys_file_storage carries basePath and pathType', $text);
        self::assertStringContainsString('cleanup:localprocessedfiles', $text);
    }

    #[Requirement('R-KNW-056')]
    #[Decision('D-KNW-045')]
    #[Test]
    public function whereAOneOffScriptMayNotGoNamesTheDocumentRoot(): void
    {
        // The corpus placed such a script, in Build/ and not var/ because git
        // ignores var/, and named no place the server serves. A session on the
        // Record class wrote check_record.php into the root of a DDEV project
        // and ran it there. This query reached nothing at all — `D-KNW-045`.
        $result = Hints::find(
            [],
            'writing and executing a PHP script in the live webroot to introspect core classes',
            6,
        );
        self::assertSame('project-build-and-scripts', $result['matchedHints'][0]['id']);

        $placement = Hints::find([], 'where do I put a one-off script', 6);
        self::assertContains('project-build-and-scripts', array_column($placement['matchedHints'], 'id'));

        // Named by what configures it rather than by a path, because the path
        // is a setting. typo3/cms-composer-installers defaults web-dir to
        // public and the root composer.json is what moves it.
        $text = self::statementsOf('project-build-and-scripts');
        self::assertStringContainsString('extra.typo3/cms.web-dir', $text);
        self::assertStringContainsString('public/ where that key is absent', $text);
        self::assertStringContainsString('/var/www/html', $text);
        // Both reasons. The served one is what only the document root has. The
        // one about survival also covers the project root above it, which is
        // where the reported file went.
        self::assertStringContainsString('reachable over HTTP', $text);
        self::assertStringContainsString('after the run that wrote it ends', $text);
        self::assertStringContainsString('it goes into Build/, or it is not written at all', $text);
    }

    #[Requirement('R-KNW-040')]
    #[Test]
    public function whichEnvironmentVariablesTheCoreReadsItselfIsAnswered(): void
    {
        // The other half of the sentence above. "Read deployment values and
        // secrets from environment variables" tells a project to wire something
        // up and says nothing about what the core already does. The corpus said
        // only that half. A session that asked whether the core reads
        // TYPO3_ENCRYPTION_KEY on its own got nothing back and answered itself
        // from memory. It happened to be right. The three the core does read
        // are the ones a wrong answer is expensive about. A project that
        // assumes more of them ships a deployment whose secrets never apply.
        $result = Hints::find(
            [],
            'Does TYPO3 read TYPO3_ENCRYPTION_KEY or TYPO3_DB_HOST from the environment by itself?',
            6,
        );
        self::assertSame('environment-variables', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('environment-variables', 'environment-placeholders', 'environment-runtime-readers');
        self::assertStringContainsString('SystemEnvironmentBuilder is the only thing that reads them', $text);
        self::assertStringContainsString('TYPO3_CONTEXT', $text);
        self::assertStringContainsString('TYPO3_PATH_ROOT', $text);
        self::assertStringContainsString('TYPO3_PATH_APP', $text);
        self::assertStringContainsString('REDIRECT_ prefix', $text);
        self::assertStringContainsString('HTTP_TYPO3_CONTEXT', $text);
        self::assertStringContainsString('resolved by the core\'s YamlFileLoader', $text);
        self::assertStringContainsString('does not reach config/system/settings.php or additional.php', $text);
        self::assertStringContainsString('the project\'s own getenv() in config/system/additional.php', $text);
        self::assertStringContainsString('ships no .env reader', $text);
        // The variable the reporting session found documented but unread: it
        // exists, the core does take it, and only while `typo3 setup` runs.
        self::assertStringContainsString('write settings.php once', $text);
    }

    #[Requirement('R-KNW-040')]
    #[Test]
    public function settingTheEncryptionKeyFromAnExtensionIsBoundToWhereItBreaks(): void
    {
        // Read on both sides in .checkouts/: up to 14.3 Bootstrap::init() calls
        // checkEncryptionKey() after ExtLocalconfFactory->load(), on main
        // before it. So the same ext_localconf.php assignment is merely bad
        // practice on one branch and a boot failure on the next. A hint that
        // stated either one flat would be wrong for half the callers.
        $onFourteen = implode("\n", array_column(
            Hints::byId('environment-runtime-readers', 14)['hints'],
            'text',
        ));
        self::assertStringNotContainsString('ext_localconf.php', $onFourteen);

        $onFifteen = implode("\n", array_column(
            Hints::byId('environment-variables', 15)['hints'],
            'text',
        ));
        self::assertStringContainsString('ext_localconf.php', $onFifteen);
        self::assertStringContainsString('TYPO3 Encryption is empty', $onFifteen);
    }

    #[Requirement('R-KNW-026')]
    #[Test]
    public function routedArgumentsAreAnsweredWithTheirCacheHashBoundary(): void
    {
        $result = Hints::find(
            ['Configuration/Sets/Printworks/route-enhancers.yaml'],
            'Route enhancer aspects and the cache hash: when does a mapped route argument still need cHash in the URL',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');
        self::assertSame('record-routing', $ids[0]);

        $text = self::statementsOf('frontend-records', 'record-routing', 'record-page-title');
        self::assertStringContainsString('PersistedAliasMapper and StaticValueMapper', $text);
        self::assertStringContainsString('needs no cHash', $text);
        self::assertStringContainsString('dynamicArguments', $text);
    }

    #[Requirement('R-KNW-031')]
    #[Test]
    public function persistedAliasesStateBothDirections(): void
    {
        $query = 'What does PersistedAliasMapper map, which value belongs in the query argument, and why is there no cHash?';
        $result = Hints::find(
            ['Configuration/Sets/Printworks/route-enhancers.yaml'],
            $query,
            6,
        );
        self::assertSame('record-routing', $result['matchedHints'][0]['id']);

        $guide = Registry::call('typo3_task_guide', [
            'task' => $query,
            'targetVersion' => '14',
        ]);
        $text = $guide->text;
        self::assertStringContainsString('record uid as the query argument', $text);
        self::assertStringContainsString('routeFieldName in the path', $text);
        self::assertStringContainsString('uniqueInSite', $text);
        self::assertStringContainsString('rejects the enhanced route before rendering', $text);
        self::assertStringContainsString('needs no cHash', $text);
    }

    #[Requirement('R-KNW-027')]
    #[Test]
    public function theFormFrameworkIsCoveredAsAWholeSubsystem(): void
    {
        $result = Hints::find(
            [],
            'EXT:form form definition YAML, form setup in sitepackage, prefill form fields',
            6
        );
        self::assertSame('form-framework', $result['matchedHints'][0]['id']);

        $hint = Hints::byId('form-framework');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('Configuration/Form/<SetName>/config.yaml', $text);
        self::assertStringContainsString('.form.yaml', $text);
        self::assertStringContainsString('_originalIdentifier', $text);
        self::assertStringContainsString('AfterCurrentPageIsResolvedEvent', $text);
        self::assertStringContainsString('submitted value still wins', $text);
    }

    #[Requirement('R-KNW-028')]
    #[Test]
    public function survivingHooksAreNamedByTheirSubsystemAndIntent(): void
    {
        $form = Hints::byId('form-framework');
        self::assertNotNull($form);
        $formText = implode("\n", array_column($form['hints'], 'text'));
        self::assertStringContainsString('ext/form/afterFormStateInitialized', $formText);
        self::assertStringContainsString('ext/form/buildFormDefinitionValidationConfiguration', $formText);

        $events = Hints::byId('events-extension-points');
        self::assertNotNull($events);
        $eventText = implode("\n", array_column($events['hints'], 'text'));
        self::assertStringContainsString('subsystem hint with the intent', $eventText);
        self::assertStringContainsString('form-framework', $eventText);
    }

    #[Decision('D-KNW-051')]
    #[Test]
    public function thePublicAssetAnswerSeparatesTheSupportedRoute(): void
    {
        // The query an audit of a v14 extension arrived with. What came back
        // named the factory and the publisher, and named
        // getPublicResourceWebPath() as "what computes such a URL here" with no
        // word of its deprecation. That statement had a bound up to 14. The
        // method the audited code called was in neither sentence. So the
        // session read PathUtility itself and stopped at the signature, one
        // line above the @internal. `D-KNW-051`.
        $result = Registry::call('typo3_hint_lookup', [
            'task' => 'Backend JavaScript ES modules, import maps and public assets shipped by an '
                . 'extension for the TYPO3 backend',
            'paths' => [
                'Configuration/JavaScriptModules.php',
                'Resources/Public/JavaScript/',
                'Resources/Public/Css/',
            ],
            'targetVersion' => '14.3',
        ]);

        self::assertStringContainsString('getSystemResourceUri', $result->text);
        self::assertStringContainsString('before this major reaches LTS', $result->text);
        self::assertStringContainsString('E_USER_DEPRECATED', $result->text);
        self::assertStringNotContainsString(
            'getPublicResourceWebPath() is what computes such a URL here',
            $result->text,
        );

        // The three classes the route comes from, against the migration example
        // that imports them from a namespace none of them is in.
        self::assertStringContainsString('TYPO3\CMS\Core\SystemResource', $result->text);

        // Asked in the words of the API rather than in the words of the
        // subject — R-KNW-002.
        $asAsked = Hints::find([], 'PathUtility::getSystemResourceUri for an EXT: image path', 6);
        self::assertSame('public-assets', $asAsked['matchedHints'][0]['id']);

        // On 13 the deprecation had not happened and neither had the API.
        $onThirteen = Registry::call('typo3_hint_lookup', [
            'task' => 'public assets shipped by an extension',
            'paths' => ['Resources/Public/Css/'],
            'targetVersion' => '13.4',
        ]);
        self::assertStringContainsString(
            'getPublicResourceWebPath() is what computes such a URL here',
            $onThirteen->text,
        );
        self::assertStringNotContainsString('getSystemResourceUri', $onThirteen->text);
    }

    #[Requirement('R-KNW-029')]
    #[Test]
    public function coreOnlyDocumentationAndBuildHintsHaveProjectTwins(): void
    {
        $documentation = Hints::byId('extension-documentation');
        self::assertNotNull($documentation);
        $documentationText = implode("\n", array_column($documentation['hints'], 'text'));
        self::assertStringContainsString('guides.xml', $documentationText);
        self::assertStringContainsString('semantic version', $documentationText);
        self::assertStringContainsString('documentation-changelog', $documentationText);

        $assets = Hints::byId('extension-asset-build');
        self::assertNotNull($assets);
        $assetText = implode("\n", array_column($assets['hints'], 'text'));
        self::assertStringContainsString('does not attach', $assetText);
        self::assertStringContainsString('public-assets', $assetText);
        self::assertStringContainsString('extension-declarative-files', $assetText);

        $docsQuery = Hints::find(
            [],
            'guides.xml and Documentation/Index.rst for my extension documentation and release notes',
            6
        );
        self::assertSame('extension-documentation', $docsQuery['matchedHints'][0]['id']);
        $assetQuery = Hints::find(
            [],
            'build scss and typescript frontend assets in a sitepackage extension',
            6
        );
        self::assertContains('extension-asset-build', array_column($assetQuery['matchedHints'], 'id'));
    }

    #[Requirement('R-KNW-007')]
    #[Decision('D-KNW-030')]
    #[Test]
    public function theSeedingAdviceCarriesTheStepsItAsksFor(): void
    {
        // "Seed with DataHandler, then export" named the way in and stopped
        // where the work starts. Getting a DataHandler at all is a hand-written
        // boot, and each of its steps is a trap.
        //
        // The two halves are two hints since D-KNW-030: how records come into
        // being is DataHandler's subject, and shipping the export is the
        // package's. What this holds is that neither half lost its steps.
        $seeding = Hints::byId('datahandler-seeding');
        self::assertNotNull($seeding);
        $steps = implode("\n", array_column($seeding['hints'], 'text'));

        self::assertStringContainsString('Bootstrap::init', $steps);
        self::assertStringContainsString('initializeBackendUser', $steps);

        self::assertStringContainsString('--table', self::statementsOf('impexp-artifact'));
    }

    /**
     * The datamap statement said how to write a scalar field and stopped. So a
     * session that seeded an element with inline children had nothing at the
     * first relation it reached. It wrote the child's pointer column by hand
     * and read the parent's int column as one that rejects a comma list. That
     * column is a counter DataHandler maintains, and the same holds on every
     * covered major, so the statement carries no range — `D-KNW-030`.
     */
    #[Requirement('R-KNW-043')]
    #[Decision('D-KNW-030')]
    #[Test]
    public function aRelationInADatamapSaysWhatTheParentColumnEndsUpHolding(): void
    {
        $statements = static fn(?int $major): string => implode("\n", array_column(
            Hints::byId('datahandler-relations', $major)['hints'] ?? [],
            'text',
        ));
        $text = $statements(null);

        self::assertStringContainsString('comma-separated list of the related uids', $text);
        self::assertStringContainsString('foreign_field', $text, 'what moves the relation onto the child');
        self::assertStringContainsString('foreign_table_field', $text);
        self::assertStringContainsString('foreign_match_fields', $text);
        self::assertStringContainsString(
            'holds the number of children rather than the list',
            $text,
            'the counter that reads as a column rejecting a comma list',
        );

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString(
                'holds the number of children rather than the list',
                $statements($major),
                'the mechanism is the same on every covered major',
            );
        }

        $reached = Hints::find(
            [],
            'IRRE inline child records written through DataHandler datamap parent field',
            6,
        );
        self::assertSame('datahandler-relations', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * A session that named its children `NEW_card1` lost every relation and got
     * a clean run for it — `R-KNW-070`. The split is the one place a NEW value
     * reads as two fields, and it is the same block on every covered major. So
     * the statement carries no range — `D-KNW-084`.
     */
    #[Requirement('R-KNW-070')]
    #[Decision('D-KNW-084')]
    #[Test]
    public function aRelationValueSaysWhichPlaceholderSpellingSurvivesIt(): void
    {
        $statements = static fn(?int $major): string => implode("\n", array_column(
            Hints::byId('datahandler-relations', $major)['hints'] ?? [],
            'text',
        ));
        $text = $statements(null);

        self::assertStringContainsString('may carry no underscore', $text);
        self::assertStringContainsString('<table>_<uid> notation', $text, 'why the split happens');
        self::assertStringContainsString('getUniqueId', $text, 'the id that always conforms');
        self::assertStringContainsString(
            'positioning pid and an MM parent id take an underscore',
            $text,
            'the constraint is on the relation value, not on the placeholder',
        );

        // The symptom, which is the only thing the reporting session had: no
        // error, and rows that read as a plausible half-success.
        self::assertStringContainsString('the run finishes clean', $text);
        self::assertStringContainsString("parent's counter stays 0", $text);
        self::assertStringContainsString('uid_foreign never set', $text);

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString('may carry no underscore', $statements($major));
        }

        // The session had the symptom and not the rule, so those are the words
        // it asks in.
        $reached = Hints::find(
            [],
            'inline children created but the relation is empty, parent counter 0, uid_foreign 0, nothing logged',
            6,
        );
        self::assertSame('datahandler-relations', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * The choice of pid is the first question and the corpus answered only the
     * second one. So a session that seeded a table of its own guessed at both
     * the page and the storage folder's role — `R-KNW-058`. What the doktype
     * allows is the same on every covered major. Only the place of the
     * declaration moved, which is the one statement with a range — `D-KNW-023`.
     */
    #[Requirement('R-KNW-058')]
    #[Decision('D-KNW-023')]
    #[Test]
    public function thePlacementAnswerSaysWhichPageMayHoldTheRecord(): void
    {
        $statements = static fn(?int $major): string => implode("\n", array_column(
            Hints::byId('datahandler-placement', $major)['hints'] ?? [],
            'text',
        ));
        $text = $statements(null);

        self::assertStringContainsString('doktype 254', $text, 'the folder that allows every table');
        self::assertStringContainsString('ignorePageTypeRestriction', $text, 'how a table joins the four');
        self::assertStringContainsString('ctrl.rootLevel', $text, 'what decides a pid of 0');
        self::assertStringContainsString(
            'writes a log entry and carries on',
            $text,
            'the refusal that raises nothing',
        );
        self::assertStringContainsString('admin does not get past', $text);

        foreach (Versions::majors() as $major) {
            self::assertStringContainsString('doktype 254', $statements($major));
            self::assertStringContainsString(
                'Which tables a page type allows is declared in',
                $statements($major),
                'every major says where the list is declared on it',
            );
        }

        self::assertStringContainsString('allowedRecordTypes', $statements(14));
        self::assertStringContainsString('PageDoktypeRegistry itself', $statements(13));

        $reached = Hints::find([], 'which page may hold a record of my own table, storage folder or standard page', 6);
        self::assertSame('datahandler-placement', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    #[Requirement('R-KNW-018')]
    #[Test]
    public function shippedContentIsAnsweredPastThePointWhereTheFileExists(): void
    {
        // The corpus covered the mechanism and not the lifecycle. The file came
        // out three times and never went in. The installation it came from had
        // already run it, and nothing said where else it could be. What gets a
        // new id and what ships as a stale integer was absent for the same
        // reason. It is only visible on the way back in.
        $text = self::statementsOf('initial-content-import-once', 'initial-content-references', 'impexp-artifact');

        // The key is the operative half of the registry entry; the namespace
        // alone re-triggers nothing.
        self::assertStringContainsString('Initialisation/dataImported', $text);
        self::assertStringContainsString('importData()', $text, 'where the artifact can be verified at all');
        self::assertStringContainsString('ReferenceIndex::getRelations()', $text, 'what decides whether a uid survives');
        self::assertStringContainsString('--save-files-outside-export-file', $text);

        // A different mechanism gives the site configuration its new ids than
        // the records, and it touches only one field of it. A reader who
        // carries the relation rule over to config.yaml gets the opposite of
        // the answer. So what the import leaves alone is the half worth a hold.
        self::assertStringContainsString(
            'the root page id to the page that was actually imported, and nothing else',
            $text,
            'what the site configuration import does not remap'
        );
        self::assertStringContainsString('t3://page?uid=', $text, 'the reference that ships stale');
    }

    #[Requirement('R-KNW-005')]
    #[Requirement('R-KNW-013')]
    #[Test]
    public function aNavigationIsAnsweredWhereMenusAreActuallyConfigured(): void
    {
        // excludeDoktypes replaces the default list instead of extending it,
        // which puts every storage folder into the menu. The hint that says so
        // has to be reachable from the word of the question.
        $result = Hints::find([], 'main navigation of the site, menu levels and which pages it shows', 6);

        self::assertContains('page-variables-and-processors', array_column($result['matchedHints'], 'id'));
    }

    #[Requirement('R-KNW-013')]
    #[Test]
    public function aMenuQuestionThatReadsAsFrontendWorkStillReachesTheMenuTrap(): void
    {
        // The statement was there and a second report called it absent. It sat
        // in the PHP category, and a question in the words of sitepackage work
        // has that whole category withheld from it. Where a statement lives
        // decides who can see it.
        $result = Hints::find(
            [],
            'the main navigation of my sitepackage shows storage folders, menu dataProcessing excludeDoktypes',
            6
        );
        $ids = array_column($result['matchedHints'], 'id');
        self::assertContains('page-variables-and-processors', $ids);

        $text = implode("\n", array_column((array) Hints::byId('page-variables-and-processors')['hints'], 'text'));
        self::assertStringContainsString('excludeDoktypes', $text);
    }

    #[Requirement('R-KNW-006')]
    #[Test]
    public function aSitepackageIsAnsweredWithTheLayoutTheCoreItselfShips(): void
    {
        // A session invented a layout for a sitepackage and lost it to a
        // rejection afterwards. The core ships a theme extension that
        // establishes one and nothing here pointed at it.
        $result = Hints::find([], 'directory structure of a sitepackage extension', 6);
        self::assertContains('sitepackage-layout', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('sitepackage-layout', 'sitepackage-templates', 'sitepackage-backend-layouts', 'sitepackage-typoscript-reference');
        self::assertStringContainsString('theme_camino', $text);
        self::assertStringContainsString('Content/Default', $text, 'the layout name collision is the load-bearing half');
    }

    #[Requirement('R-AUD-005')]
    #[Decision('D-KNW-007')]
    #[Test]
    public function whatOnlyBindsACorePatchSaysSoOutsideTheCore(): void
    {
        // The backend's design system is a condition of a core patch and of
        // nothing in a project. That does not make it useless there, so it
        // carries a mark rather than a drop. Inside the core the marker would
        // be on every line and say nothing — `D-KNW-007`.
        $project = Registry::call('typo3_hint_lookup', [
            'id' => 'css-class-naming',
            'paths' => ['packages/my_sitepackage/Classes/Controller/ProductController.php'],
        ]);
        self::assertSame(
            [Scope::Extension],
            array_values(array_unique(array_column($project->data['scopes'], 'scope'))),
        );
        self::assertSame(Scope::Core->value, $project->data['hints'][0]['scope']);
        self::assertStringContainsString('Binding for a patch to the TYPO3 core', $project->text);
        self::assertStringContainsString('conventions you may adopt', $project->text);

        $core = Registry::call('typo3_hint_lookup', [
            'id' => 'css-class-naming',
            'paths' => ['Build/Sources/Sass/component/_card.scss'],
        ]);
        self::assertStringNotContainsString('Binding for a patch', $core->text);
    }

    /**
     * The same rule the other way round, which the corpus could not say until
     * `D-KNW-007`. A hint whose whole subject is a repository outside the core
     * is context there rather than a condition. Inside its own scope the notice
     * would be on every answer and say nothing.
     */
    #[Decision('D-KNW-007')]
    #[Test]
    public function whatOnlyBindsOutsideTheCoreSaysSoInsideIt(): void
    {
        $inTheCore = Registry::call('typo3_hint_lookup', [
            'id' => 'project-repository-layout',
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
        ]);

        self::assertSame(Scope::Project->value, $inTheCore->data['hints'][0]['scope']);
        self::assertStringContainsString('Binding for work outside the TYPO3 core', $inTheCore->text);

        $inAProject = Registry::call('typo3_hint_lookup', [
            'id' => 'project-repository-layout',
            'paths' => ['packages/my_sitepackage/composer.json'],
        ]);
        self::assertStringNotContainsString('Binding for work outside', $inAProject->text);

        // The pair the corpus draws this line with: two hints, one subject
        // each, and now each saying whose it is rather than only its title.
        $tests = array_column(Hints::load(), 'scope', 'id');
        self::assertSame(Scope::Core, $tests['core-tests']);
        self::assertSame(Scope::Extension, $tests['project-extension-tests']);
    }

    /**
     * Three hints read as one audience by their titles and are not, which the
     * checkouts settled rather than intuition. `theme_camino` is a sitepackage
     * in the core repository and ships `Initialisation/data.xml`, so the layout
     * of one and its seed are obligations the core has too. A site set is how
     * any extension ships TypoScript — `fluid_styled_content` is one. A
     * declaration would tell a contributor working on those that their own
     * subject is somebody else's.
     *
     * What would change it is a release: the theme out of the core, which its
     * own first statement says is on the way.
     */
    #[Test]
    #[DataProvider('theHintsTheCoreIsObligedByToo')]
    public function aHintTheCoreIsAlsoObligedByDeclaresNoAudience(string $id): void
    {
        $scopes = array_column(Hints::load(), 'scope', 'id');

        self::assertNull($scopes[$id], $id . ' declares an audience the core is obliged by too');
    }

    /** @return array<string, array{0: string}> */
    public static function theHintsTheCoreIsObligedByToo(): array
    {
        return [
            'the sitepackage layout' => ['sitepackage-layout'],
            'the initial content a sitepackage ships' => ['sitepackage-initial-content'],
            'site sets' => ['site-sets'],
        ];
    }

    /**
     * The upstream XML's own header says to copy the bootstrap along with it,
     * which is boilerplate maintenance advice rather than a requirement. The
     * file holds a Testbase, ORIGINAL_ROOT and two directories, and nothing an
     * extension configures. A copy is a file nobody updates afterwards.
     */
    #[Requirement('R-KNW-047')]
    #[Test]
    public function theBootstrapIsReferenced(): void
    {
        $text = self::statementsOf('project-extension-tests');

        self::assertStringContainsString('Copy those two to Build/', $text, 'the XML is copied');
        self::assertStringContainsString('vendor/typo3/testing-framework', $text, 'the bootstrap is referenced');
        self::assertStringNotContainsString('Copy all four', $text);
    }

    /**
     * The corpus said every test class gets a database of its own and never
     * what becomes of it. So a session could not tell a leftover from the
     * database the site runs on and started to count records by hand
     * (`D-KNW-022`).
     *
     * The case asserts both cases that carry no such name, because either one
     * left out makes the per-class suffix too strong as the mark.
     * `$initializeDatabase = false` returns before `setUpTestDatabase()`, and
     * under `pdo_sqlite` the per-class database is a file below
     * `functional-sqlite-dbs/` with no `_ft` name anywhere. All three queries
     * match a curated pattern (`D-ANS-115`).
     */
    #[Decision('D-ANS-115')]
    #[Requirement('R-KNW-053')]
    #[Test]
    public function thePerClassDatabaseAnswerSaysWhatSurvivesTheRun(): void
    {
        $reaches = static fn(string $task): array => array_column(
            Hints::find([], $task, 6)['matchedHints'],
            'id',
        );

        self::assertContains('project-extension-tests', $reaches('live database versus test database'));
        self::assertContains('project-extension-tests', $reaches('what happens to the test databases after the run'));
        self::assertContains('project-extension-tests', $reaches('clean up functional test databases'));

        $text = self::statementsOf('project-extension-tests');

        // What survives, and what reclaims it.
        self::assertStringContainsString('Nothing drops one after the run ends', $text);
        self::assertStringContainsString('Testbase::setUpTestDatabase()', $text, 'where the only drop is');
        self::assertStringContainsString('tearDownAfterClass()', $text, 'what does not exist');
        self::assertStringContainsString('only when that same class runs again', $text);

        // Why the set has a bound, which is what makes a drop of them all safe.
        self::assertStringContainsString('substr(sha1(<test class>), 0, 7)', $text);
        self::assertStringContainsString('costs the next run nothing', $text);

        // What tells a leftover from the live database, and the two cases that
        // carry no such name.
        self::assertStringContainsString('_ft<7 hex>', $text);
        self::assertStringContainsString('nothing a functional test writes reaches the configured database', $text);
        self::assertStringContainsString('$initializeDatabase = false', $text);
        self::assertStringContainsString('functional-sqlite-dbs/test_<7 hex>.sqlite', $text);
    }

    /**
     * A declared suite that will not start gets its answer before the harness
     * does.
     *
     * The entry opened on how to write one, so a caller whose harness exists
     * read past three statements that were not theirs. That is the risk
     * `D-ANS-092` wrote down and the reason `typo3_project_describe` may now
     * hand this id over. That entry also carries how the measurement of the two
     * DDEV facts below went, and against which release.
     */
    #[Decision('D-ANS-092')]
    #[Test]
    public function aSuiteThatWillNotStartIsAnsweredBeforeTheHarnessIs(): void
    {
        $reaches = static fn(string $task): array => array_column(
            Hints::find([], $task, 6)['matchedHints'],
            'id',
        );

        self::assertSame('project-extension-tests', $reaches('run the functional suite from a git worktree')[0] ?? '');
        self::assertSame(
            'project-extension-tests',
            $reaches('composer test:php:functional fails with database credentials')[0] ?? '',
        );

        $text = self::statementsOf('project-extension-tests');

        self::assertStringContainsString('.ddev/config.yaml is tracked', $text);
        self::assertStringContainsString('in running state already exists', $text);
        self::assertStringContainsString('raw.dbinfo.published_port', $text);
        self::assertStringContainsString('typo3DatabasePort', $text);
        self::assertStringContainsString('This version of PHPUnit requires PHP', $text);
        self::assertStringContainsString('composer/platform_check.php', $text);

        // The other two the docblock names. The message an absent credential
        // stops with names no variable, so the five are the answer. The
        // per-class database is what the account has to have permission to
        // create.
        foreach (['Driver', 'Host', 'Name', 'Username', 'Password'] as $credential) {
            self::assertStringContainsString('typo3Database' . $credential, $text);
        }
        self::assertStringContainsString('root rather than the user the site itself runs as', $text);
    }

    /**
     * The chain a patch review read seven core classes for, because nothing
     * below `knowledge/` said how a file becomes a processed one (`D-KNW-028`).
     * What decides it is the order the registry asks in and the first `yes`.
     * That is why a registration after the processor that already claims a case
     * changes nothing.
     */
    #[Requirement('R-KNW-048')]
    #[Test]
    public function whichProcessorClaimsAFileIsAnswered(): void
    {
        $reached = array_column(
            Hints::find([], 'which processor makes the thumbnail', 6)['matchedHints'],
            'id',
        );
        self::assertContains('fal-processing', $reached);

        $text = self::statementsOf('fal-processing');

        self::assertStringContainsString('stops at the first that says yes', $text, 'what decides it');
        self::assertStringContainsString('never after it', $text, 'the mistake the order makes possible');
        self::assertStringContainsString('fileNeedsProcessing()', $text, 'why a processor may not run at all');
        self::assertStringContainsString('1560876294', $text, 'what an unclaimed task throws');
        self::assertStringContainsString('typo3_configuration_lookup', $text, 'where the real list is');
    }

    /**
     * The half `D-KNW-028` left unsaid, which a session then filled in from the
     * one call path it had read. That an image process needs a FAL object
     * (`D-KNW-042`). Both entry points it named as the way past FAL resolve
     * their string through `ResourceFactory` first. So the correction is part
     * of the statement rather than a footnote to it.
     */
    #[Requirement('R-KNW-054')]
    #[Test]
    public function whereFalStopsInTheImagePipelineIsAnswered(): void
    {
        $reached = array_column(
            Hints::find([], 'does image processing require a FAL object', 6)['matchedHints'],
            'id',
        );
        self::assertContains('fal-processing', $reached);

        $text = self::statementsOf('fal-processing');

        self::assertStringContainsString('getForLocalProcessing(false)', $text, 'where FAL stops');
        self::assertStringContainsString('takes a path string', $text, 'what runs below it');
        self::assertStringContainsString('GraphicalFunctions', $text, 'the layer under the task');
        self::assertStringContainsString('ImageInfo', $text, 'dimensions without a FAL record');
        self::assertStringContainsString('not a way past FAL', $text, 'what getImgResource does with a path');
    }

    /**
     * Two FAL traps that only show up as "nothing happened". The same call
     * carries a different default on the folder and on the storage. A
     * reference's own fields are the ones an editor filled in.
     */
    #[Test]
    public function theFileAnswersNameWhatFailsSilently(): void
    {
        $writing = implode("\n", array_column(Hints::byId('fal-writing')['hints'], 'text'));

        self::assertStringContainsString('ExistingTargetFileNameException', $writing);
        self::assertStringContainsString('ResourceStorage::addFile() renames', $writing);

        $reading = implode("\n", array_column(Hints::byId('fal-reading')['hints'], 'text'));

        self::assertStringContainsString('getOriginalFile()', $reading);
        self::assertStringContainsString('alternative text', $reading, 'what the reference record carries');
    }

    /**
     * The gap the umbrella hid. `datahandler-persistence` carried
     * `querybuilder`, `restriction`, `enablecolumns`, `hidden record` and
     * `deleted record` in its vocabulary and not one statement about a record
     * read. The whole corpus held one sentence naming any of the reading APIs,
     * and it was about a menu — `D-KNW-030`.
     */
    #[Requirement('R-KNW-045')]
    #[Decision('D-KNW-030')]
    #[Test]
    public function readingRecordsIsAnswered(): void
    {
        $reached = array_column(
            Hints::find([], 'why is a record missing from my query result', 6)['matchedHints'],
            'id',
        );
        self::assertContains('persistence-reading', $reached);

        $text = implode("\n", array_column(Hints::byId('persistence-reading')['hints'], 'text'));

        // What applies without a request, and what is a step after the query
        // rather than a condition in it. Both read as a missing record.
        self::assertStringContainsString('DefaultRestrictionContainer', $text);
        self::assertStringContainsString('removeAll()', $text);
        self::assertStringContainsString('versionOL', $text);
        self::assertStringContainsString('getLanguageOverlay', $text);
    }

    /**
     * impexp is how a site or a page tree comes back. The export is the
     * artifact a distribution ships and imports again, not the leftover of a
     * seed run. The corpus said "seed with DataHandler, then export", which
     * reads as the other way round — `D-KNW-030`.
     */
    #[Requirement('R-KNW-046')]
    #[Decision('D-KNW-030')]
    #[Test]
    public function theSeedingAnswerNamesImpexpAsTheWayATreeIsEstablishedAgain(): void
    {
        $text = implode("\n", array_column(Hints::byId('datahandler-seeding')['hints'], 'text'));

        self::assertStringContainsString('impexp:import', $text);
        self::assertStringContainsString('exists nowhere yet', $text, 'what a seeding script is for');
    }

    /**
     * The corpus said only the root page id gets a new value on import. That
     * holds for a configuration shipped in `Initialisation/Site/` and not for
     * one inside the export file. `Import::processSiteConfigurations()`
     * overwrites `base` as well, so a seeded distribution answers 404 at the
     * project root — `D-KNW-048`.
     */
    #[Requirement('R-KNW-062')]
    #[Test]
    public function theImportAnswerSaysWhatItRewritesInASiteConfiguration(): void
    {
        $reached = array_column(
            Hints::find([], 'imported site base url 404 root', 6)['matchedHints'],
            'id',
        );
        self::assertContains('initial-content-references', $reached);

        $text = implode("\n", array_column(Hints::byId('initial-content-references')['hints'], 'text'));

        self::assertStringContainsString('overwrites base with /<identifier>/', $text);
        self::assertStringContainsString('config/sites/<identifier>/config.yaml', $text, 'where it is corrected');
        // The two conditions under which the method does not run, both of which
        // read like the cause of a base nobody shipped.
        self::assertStringContainsString('not an admin', $text);
        self::assertStringContainsString('ImportSiteConfigurationsOnPackageInitialization', $text);

        // The two statements that say only rootPageId gets a new value now name
        // the route they hold for.
        self::assertStringContainsString('The Initialisation/Site/ route remaps the root page id', $text);

        $routing = implode("\n", array_column(Hints::byId('record-routing')['hints'], 'text'));

        self::assertStringContainsString('shipped in Initialisation/Site/ only rootPageId is remapped', $routing);
    }

    /**
     * The tag is the selector, so a domain nobody selects by is a hint nobody
     * reaches. It fails without a word, because an unknown tag reads exactly
     * like a narrow one. `Domains::hintDomains()` is the whole vocabulary there
     * is — `D-KNW-029`, `D-KNW-034`.
     */
    #[Decision('D-KNW-029')]
    #[Decision('D-KNW-034')]
    #[Test]
    public function everyHintIsTaggedWithADomainSomeQuerySelects(): void
    {
        $selectable = Domains::hintDomains([
            Domains::PHP,
            Domains::TYPOSCRIPT,
            Domains::FLUID,
            Domains::TYPESCRIPT,
            Domains::CSS,
            Domains::XLIFF,
            Domains::DOCS,
        ]);

        foreach (Hints::load() as $hint) {
            self::assertNotEmpty($hint['domains'], $hint['id'] . ' names no domain');
            foreach ($hint['domains'] as $domain) {
                self::assertContains(
                    $domain,
                    $selectable,
                    $hint['id'] . ' is tagged ' . $domain . ', which no query selects',
                );
            }
        }
    }

    /**
     * The bucket, as a number rather than as a plan.
     *
     * `any` is still a tag a hint may carry, since `D-KNW-029` kept it on
     * purpose. Nothing carries it since `D-KNW-033` named the domains each of
     * the 38 General hints really comes from. This fails on the first new one,
     * which is the point. An `any` hint is reachable from every task there is.
     * So it needs an argument in a decision rather than a reach for it when a
     * query misses.
     */
    #[Decision('D-KNW-033')]
    #[Test]
    public function nothingIsTaggedAnyWithoutSayingWhy(): void
    {
        $tagged = array_values(array_filter(
            Hints::load(),
            static fn(array $hint): bool => in_array(Domains::ANY, $hint['domains'], true),
        ));

        self::assertSame(
            [],
            array_column($tagged, 'id'),
            'a hint every query selects is a decision, not a tag chosen while writing it',
        );
    }

    /**
     * What the file a hint sits in may mean: nothing.
     *
     * It used to be the domain. That is why a hint of two of them had to sit
     * under `general`, the one domain every query selects. The tag carries it
     * now, so the file is free to be the subject. This fails the moment
     * somebody derives the domain from the file name again — `D-KNW-029`,
     * `D-KNW-034`.
     */
    #[Decision('D-KNW-029')]
    #[Decision('D-KNW-034')]
    #[Test]
    public function theFileAHintSitsInDoesNotDecideWhatSelectsIt(): void
    {
        $filed = [];
        $files = Finder::create()->files()->in(Paths::knowledge() . '/hints')->depth(0)->name('*.json');
        foreach ($files as $file) {
            foreach (json_decode((string) file_get_contents($file->getPathname()), true) as $entry) {
                $filed[$entry['id']] = $file->getBasename('.json');
            }
        }

        $differ = array_values(array_filter(
            Hints::load(),
            static fn(array $hint): bool => $hint['domains'] !== [$filed[$hint['id']]],
        ));

        // The `general.json` entries are the proof today. Their file says
        // `general` and their tag says `any`. So a matcher that read the file
        // name would select them by a domain that is not in the vocabulary at
        // all. Once the corpus sits by subject the same test passes for every
        // entry in it.
        self::assertNotSame([], $differ, 'every hint would answer the same if the file name were still the domain');
    }

    #[Requirement('R-AUD-005')]
    #[Test]
    public function oneCoreObligationInATransferableHintIsMarkedOnItsOwn(): void
    {
        // The ViewHelper conventions hold wherever somebody writes TYPO3; the
        // changelog file under typo3/sysext/ is the one sentence in them that
        // does not. Splitting the hint to say so would duplicate the six
        // statements around it, so the statement carries it — the same place
        // since/until sits.
        $result = Registry::call('typo3_hint_lookup', [
            'id' => 'fluid-viewhelpers',
            'paths' => ['packages/my_sitepackage/Classes/ViewHelpers/GreetingViewHelper.php'],
        ]);

        self::assertNull($result->data['hints'][0]['scope'], 'the hint as a whole transfers');
        $bound = array_values(array_filter(
            $result->data['hints'][0]['hints'],
            static fn(array $statement): bool => $statement['scope'] !== null,
        ));
        self::assertCount(1, $bound);
        self::assertStringContainsString('changelog entry', $bound[0]['text']);
        self::assertStringContainsString('binding for a core patch', $result->text);
    }

    #[Requirement('R-KNW-020')]
    #[Test]
    public function whereSomethingGoesInTheRepositoryIsAnsweredToo(): void
    {
        // The extension got an answer and the repository around it did not. So
        // a project invented the location of its phpunit configurations, its
        // browser suite and its scripts. The load-bearing rule is which of the
        // two units a thing belongs to.
        $result = Hints::find([], 'how do I structure the repository around my sitepackage', 6);
        self::assertContains('project-repository-layout', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('project-repository-layout', 'project-configuration-files', 'project-build-and-scripts');
        self::assertStringContainsString('config/sites/', $text);
        self::assertStringContainsString('composer.json and package.json', $text, 'nothing else says how a project is run');
    }

    /**
     * `D-KNW-088`. The hint deferred to the ignore file and named no path. So a
     * caller who wrote that file before the install, which is when it has to
     * exist, could read the answer off nothing.
     *
     * Read off `.checkouts/` for the half TYPO3 owns, where
     * `Install\FolderStructure\DefaultFactory` places the directories the same
     * way on all four branches. The rest is `typo3/cms-base-distribution` and
     * `typo3/cms-composer-installers`, which are in no checkout: read out of an
     * installed distribution per major.
     */
    #[Decision('D-KNW-088')]
    #[Test]
    public function whatAComposerInstallationGeneratesIsNamed(): void
    {
        // The feedback's own query, which reached the hint that defers, and the
        // three the generated files were unreachable under.
        foreach ([
            'what to put in .gitignore for a TYPO3 project',
            'htaccess generated by the installer',
            'typo3temp',
            'which files does a TYPO3 composer install generate that must not be committed',
        ] as $task) {
            $ids = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
            self::assertSame('project-build-and-scripts', $ids[0] ?? null, $task);
        }

        $text = self::statementsOf('project-build-and-scripts');

        // The two halves of the set, by what writes each: Composer, and the
        // installation under setup.
        self::assertStringContainsString('_assets/<hash>/, with vendor/ beside them', $text);
        self::assertStringContainsString('web.config under IIS', $text);
        self::assertStringContainsString('fileadmin/_processed_/ arrives with the first thumbnail', $text);

        // The rule the enumeration is for, which is what does not go stale.
        self::assertStringContainsString('/public/* with !/public/.htaccess', $text);
        self::assertStringContainsString('Denying and naming back survives what enumerating does not', $text);
        self::assertStringContainsString('creates the file where none exists', $text);
    }

    /**
     * The document root is the half that moved. The backend and install entry
     * points exist on both LTS branches and on neither newer one.
     * `DefaultSystemResourcePublisher`, which is what publishes
     * `_assets_install/`, exists in neither LTS checkout — `D-KNW-088`.
     */
    #[Decision('D-KNW-088')]
    #[Test]
    public function theGeneratedDocumentRootIsStatedPerMajorOnBothHints(): void
    {
        $on = static fn(string $id, int $major): string => implode("\n", array_column(
            (array) Hints::byId($id, $major)['hints'],
            'text',
        ));

        foreach ([12, 13] as $major) {
            self::assertStringContainsString('typo3/index.php and typo3/install.php', $on('project-build-and-scripts', $major));
            self::assertStringNotContainsString('_assets_install', $on('project-build-and-scripts', $major));
            self::assertStringNotContainsString('_assets_install', $on('public-assets', $major));
        }

        foreach ([14, 15] as $major) {
            self::assertStringNotContainsString('typo3/install.php', $on('project-build-and-scripts', $major));
            self::assertStringContainsString('_assets_install/ sits beside _assets/', $on('project-build-and-scripts', $major));
            self::assertStringContainsString('failsafe container the install tool boots', $on('public-assets', $major));
        }
    }

    #[Requirement('R-KNW-017')]
    #[Test]
    public function whereBackendLayoutsGoIsAnsweredWithTheConditionItDependsOn(): void
    {
        // The extension-level directory stood as the rule, read off a
        // distributable theme. In a sitepackage with one set and no
        // Configuration/page.tsconfig it is an indirection with no effect. The
        // set is the only path into any backend.
        $text = self::statementsOf('sitepackage-layout', 'sitepackage-templates', 'sitepackage-backend-layouts', 'sitepackage-typoscript-reference');
        self::assertStringContainsString('Configuration/Sets/<Set>/BackendLayouts/', $text);
        self::assertStringContainsString('Configuration/page.tsconfig', $text, 'the condition is what makes it transfer');
    }

    #[Decision('D-KNW-083')]
    #[Test]
    public function theSharedRootCollisionIsStatedForPartialsBesideLayouts(): void
    {
        // `D-KNW-083`. The layout half stood on its own. So a reader who
        // subdivided Layouts/ on it put a Header/Header beside the core's and
        // broke every element on the page.
        $text = self::statementsOf('sitepackage-templates');
        self::assertStringContainsString('Partials/Header/Header', $text);
        self::assertStringContainsString('Partials/Header/All', $text, 'what renders it is why the failure is not local');
    }

    #[Decision('D-KNW-082')]
    #[Test]
    public function theCTypeTemplateDerivationIsAttributedToItsTheme(): void
    {
        // `D-KNW-082`. The derivation stood as a property of lib.contentElement
        // and belongs to theme_camino. So a project that trusted it would have
        // named no template at all.
        $on = static fn(int $major): string => implode(
            "\n",
            array_column((array) Hints::byId('sitepackage-templates', $major)['hints'], 'text'),
        );

        $derivation = array_values(array_filter(
            (array) Hints::byId('sitepackage-templates', 14)['hints'],
            static fn(array $hint): bool => str_contains($hint['text'], 'uppercamelcase'),
        ));
        if ($derivation === []) {
            self::fail('the statement about how a template name is derived is gone');
        }
        self::assertCount(1, $derivation);
        self::assertStringContainsString('theme_camino', $derivation[0]['text'], 'the owner is what the statement was missing');

        foreach ([12, 13, 14] as $major) {
            self::assertStringContainsString('templateName = Text', $on($major), 'what fluid_styled_content does holds on all of them');
        }

        // theme_camino is a v14 package, so the convention it configures is
        // withheld from the branches that cannot have it.
        foreach ([12, 13] as $major) {
            self::assertStringNotContainsString('uppercamelcase', $on($major));
        }
    }

    #[Requirement('R-KNW-005')]
    #[Test]
    public function theTemplateTrapsThatFailWithoutAnErrorAreNamed(): void
    {
        // Both produce a wrong page rather than a failure. A variable assigned
        // outside a section of a template that declares a layout never runs. An
        // HTML comment renders into the response with its expressions resolved.
        // Neither reaches a log, so neither is searchable.
        $text = self::statementsOf('fluid-templates', 'fluid-backend-view', 'fluid-layouts-sections', 'fluid-conditions-and-arrays');

        self::assertStringContainsString('<f:section>', $text);
        self::assertStringContainsString('<f:comment>', $text);
    }

    /**
     * The trap stood from the mechanism's side alone. So it was reachable by «a
     * viewhelper call outside a section is never executed» and by nothing a
     * caller says at the debug. `D-ANS-081` measured the miss on the query
     * below, and the session that reported it read core source instead.
     *
     * The words are a statement rather than curated vocabulary, and that has a
     * measurement too. A phrase in `appliesTo` crosses the domain gate on every
     * query that writes it (`D-ANS-084`). That put this hint above
     * `extension-asset-build` on a query about a sass build.
     */
    #[Requirement('R-KNW-005')]
    #[Test]
    public function anAssetThatNeverReachesThePageIsAnsweredByItsLayout(): void
    {
        $text = self::statementsOf('fluid-layouts-sections');
        self::assertStringContainsString('<f:asset.css>', $text);

        $symptom = Hints::find([], 'f:asset.css does not appear in the rendered page', 6);
        self::assertContains('fluid-layouts-sections', array_column($symptom['matchedHints'], 'id'));

        $howTo = Hints::find([], 'how do I add a stylesheet with f:asset.css', 6);
        self::assertNotSame(
            'fluid-layouts-sections',
            array_column($howTo['matchedHints'], 'id')[0] ?? null,
        );
    }

    #[Requirement('R-KNW-016')]
    #[Test]
    public function theTestKindThatNeedsABrowserIsCovered(): void
    {
        // Asking for browser tests returned the id index and a knowledge section
        // about site sets. The core works the conventions out in
        // Build/tests/playwright/, and nothing here pointed at them.
        $result = Hints::find(
            [],
            'acceptance and end-to-end browser tests for a TYPO3 site with Playwright',
            6
        );
        self::assertContains('browser-tests', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('browser-tests', 'browser-test-accessibility', 'browser-tests-outside-core');
        // The accessibility half is the one that finds defects no PHP test can,
        // and the rendering test is what gets mistaken for a frontend test.
        self::assertStringContainsString('@axe-core/playwright', $text);
        self::assertStringContainsString('executeFrontendSubRequest', $text);
    }

    /**
     * The layer had coverage and no reach. Only words that already name the
     * answer found `browser-tests`. The caller who needs it most is the one who
     * has not yet decided that a browser comes into it (`D-KNW-017`).
     *
     * The cross is a statement in the two hints those questions do reach rather
     * than terms on `browser-tests` itself, and that has a measurement. For
     * half of these queries the domain gate drops the hint before any term gets
     * a score. The one term that did carry the other half put a test hint into
     * "the backend preview of my content element is empty" as well.
     */
    #[Requirement('R-ANS-019')]
    #[Test]
    public function aRenderedVerificationQuestionReachesTheLayerThatVerifiesIt(): void
    {
        foreach ([
            'verifying the rendered testimonials frontend and the backend page module preview',
            'verify that the content element renders correctly on the live site',
            'check the backend page module preview of a content element',
            'how do I verify rendered output of a content element',
        ] as $query) {
            $ids = array_column(Hints::find([], $query, 6)['matchedHints'], 'id');
            self::assertNotSame([], $ids, $query . ' reaches nothing at all');
            self::assertStringContainsString(
                'browser-tests',
                self::statementsOf(...$ids),
                sprintf('«%s» reaches %s, and none of them names the layer that verifies it', $query, implode(', ', $ids)),
            );
        }

        // The cost the crossing was chosen against: a question about the preview
        // itself is not a question about tests, and must not pay for one.
        self::assertNotContains(
            'browser-tests',
            array_column(Hints::find([], 'the backend preview of my content element is empty', 6)['matchedHints'], 'id'),
        );
    }

    /**
     * The same gap from the other side, and the one `bin/cli hints:coverage`
     * reports on: no scenario prompt at all reached `browser-tests`.
     *
     * The two prompts for this layer are the two that ask for the outcome. A
     * smoke test before every deployment, browser coverage after a regression
     * got through the PHP suite. Neither names Playwright. They come from the
     * contracts rather than a second copy here. So a prompt rewritten into the
     * vocabulary of the answer fails this instead of passes it without a word.
     */
    #[Requirement('R-ANS-019')]
    #[Test]
    public function theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome(): void
    {
        $contracts = Scenarios::contracts();
        foreach (['SITE-06', 'SKILL-06'] as $id) {
            self::assertArrayHasKey($id, $contracts);
            $ids = array_column(Hints::find([], $contracts[$id]['prompt'], 6)['matchedHints'], 'id');
            self::assertContains('browser-tests', $ids, $id . ' reaches ' . (implode(', ', $ids) ?: 'nothing'));
        }
    }

    /**
     * The corpus stated every fixture rule and never the premise under them. So
     * a session read `importCSVDataSet()` as a convention it could also satisfy
     * another way. It fetched records nobody primed, and took the empty table
     * for a broken query. «empty database per test run» reached neither testing
     * hint.
     *
     * The premise holds across every covered line and comes from a read rather
     * than from memory — `.checkouts/testing-framework` at `8.3.3`, `9.6.1` and
     * `main`. `FunctionalTestCase::setUp()` builds the instance for the first
     * test of a class and `Testbase::createDatabaseStructure()` installs the
     * `CREATE TABLE` statements alone, no rows. Every test after it goes
     * through `initializeTestDatabaseAndTruncateTables()`, which truncates
     * every table the schema manager lists. For sqlite it copies back the
     * `.empty.sqlite` file taken right after the schema install.
     * `setUpBackendUser()` is the consequence in one method: it throws rather
     * than creating the user.
     *
     * The case asserts both boundaries because either one left out makes the
     * sentence too strong. `$initializeDatabase = false` skips creation and
     * truncation alike, and `withDatabaseSnapshot()` restores what the first
     * test of a class created for all the ones after it.
     */
    #[Requirement('R-KNW-044')]
    #[Test]
    public function theFixtureRuleIsStatedWithTheEmptyDatabaseUnderIt(): void
    {
        self::assertContains(
            'core-tests',
            array_column(Hints::find([], 'empty database per test run', 6)['matchedHints'], 'id'),
            'the premise is not reachable in the words the failing session asked it in',
        );

        $text = implode("\n", array_column((array) Hints::byId('core-tests')['hints'], 'text'));
        self::assertStringContainsString('empty database', $text);
        self::assertStringContainsString('importCSVDataSet', $text, 'the premise stands beside the rule it explains');
        self::assertStringContainsString('$initializeDatabase = false', $text);
        self::assertStringContainsString('withDatabaseSnapshot()', $text);
    }

    /**
     * `R-KNW-055`. The question is "I am changing rendered output, what asserts
     * it", and before this statement existed the corpus answered a different one.
     *
     * The case asserts the two halves apart because either one alone leaves the
     * search wrong. A search around the value reaches the fixtures that hold
     * the expectations, and a search for the value itself reaches almost none
     * of them. The ratio holds on every covered line, so the statement carries
     * no version range — `D-KNW-044`.
     */
    #[Requirement('R-KNW-055')]
    #[Decision('D-KNW-044')]
    #[Test]
    public function aRenderedOutputChangeIsToldWhereTheExpectationsHide(): void
    {
        self::assertContains(
            'core-tests',
            array_column(Hints::find([], 'I am changing rendered output, what asserts it', 6)['matchedHints'], 'id'),
            'the question is not reachable in the words the reporting session asked it in',
        );

        $text = implode("\n", array_column((array) Hints::byId('core-tests')['hints'], 'text'));

        // Where they hide: not in the file the caller would think to search.
        self::assertStringContainsString('typo3/sysext/*/Tests/', $text);
        self::assertStringContainsString('*Test.php', $text);
        self::assertStringContainsString('.message', $text);
        self::assertStringContainsString('contentMatchRegExp', $text);

        // And what to search for, which is never the value that changed.
        self::assertStringContainsString('around the value rather than the value itself', $text);
        self::assertStringContainsString('quoted-printable', $text);
        self::assertStringContainsString('{$fileMtimeActions}', $text);
    }

    /**
     * The words that reached the layout instead, measured at the settlement of
     * `D-KNW-008`. Two hints carry a sitepackage in their name and none the
     * test setup. So "site package" was the pair of terms that separated and
     * "tests" separated nothing. That is `R-ANS-007` at work as designed on a
     * corpus where the word that names the subject is the weaker signal. The
     * vocabulary is what moved, not the corpus. `add tests` had its measurement
     * with these three forms and stayed out. It puts the project hint ahead of
     * `core-tests` for a question about the DataHandler — `D-KNW-032`,
     * `D-KNW-009`, `D-KNW-013`.
     */
    #[Decision('D-KNW-009')]
    #[Decision('D-KNW-013')]
    #[Decision('D-KNW-032')]
    #[Test]
    public function settingTestsUpInAPackageReachesTheHintAboutThat(): void
    {
        $reaches = static fn(string $task): array => array_column(
            Hints::find(['packages/my_sitepackage/Classes/Foo.php'], $task, 5)['matchedHints'],
            'id',
        );

        self::assertSame('project-extension-tests', $reaches('Set up tests for our site package extension')[0] ?? '');

        // The neighbours of the measurement, which any change here has to keep:
        // each reaches the cell it is about.
        self::assertContains('project-extension-tests', $reaches('how do I test my extension'));
        self::assertContains('project-extension-tests', $reaches('add functional tests to the extension'));
        self::assertContains('extension-static-analysis', $reaches('set up phpstan for our extension'));
        self::assertContains('browser-tests', $reaches('browser tests for the site package'));

        // Without a path, where the domain rather than the rank used to decide.
        // "site package" is a Fluid and TypoScript keyword and nothing in the
        // sentence was a PHP one, so php.json fell out before any score
        // (D-KNW-009).
        self::assertSame(
            'project-extension-tests',
            array_column(
                Hints::find([], 'Set up tests for our site package extension', 5)['matchedHints'],
                'id',
            )[0] ?? '',
        );
        self::assertSame(
            'project-extension-tests',
            array_column(
                Hints::find([], "Review our site package's test coverage", 5)['matchedHints'],
                'id',
            )[0] ?? '',
            'SKILL-01 asks in the words the vocabulary was missing',
        );

        // And the core side, which is what the fourth phrasing would have cost.
        $core = array_column(
            Hints::find(
                ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
                'add tests for the DataHandler change',
                5,
            )['matchedHints'],
            'id',
        );
        self::assertContains('core-tests', $core);
        self::assertNotContains('project-extension-tests', $core, 'a core testing question reached the project hint');
    }

    /**
     * The sixth form `D-KNW-009`'s first **Wrong if** asked about, and it came
     * out of this repository's own text. The conformance checklist wrote its
     * quality surface down as the bare `tests` that entry had rejected. So an
     * audit in the checklist's own words reached no PHP hint at all and no rule
     * about the supported range either. `D-KNW-013` is what that cost. This
     * holds both ends because either alone is inert. The words come out of the
     * checklist rather than a quote. So a session that writes the bare word
     * back fails here rather than in an audit six weeks later.
     */
    #[Decision('D-KNW-013')]
    #[Test]
    public function anAuditAskingAboutTestsReachesTheRuleAboutTheSupportedRange(): void
    {
        $checklist = (string) file_get_contents(
            Paths::root() . '/skills/typo3-extension-health/references/checklist.md',
        );
        if (preg_match('/^- Quality: (.+?)\.$/ms', $checklist, $surface) !== 1) {
            self::fail('the checklist no longer writes a quality surface down');
        }

        $reaches = static fn(string $task): array => array_column(
            Hints::find([], $task, 6)['matchedHints'],
            'id',
        );

        self::assertContains(
            'extension-repository-layout',
            $reaches('audit the quality surface of an extension: ' . $surface[1]),
            'the audit asks in the checklist\'s own words',
        );
        self::assertContains(
            'extension-repository-layout',
            $reaches('does the test suite cover every supported TYPO3 version'),
            'and a caller asks for the same rule without naming a repository at all',
        );

        // Through the tool the skill calls, where the answer used to be the
        // layout of a repository with an installation in it. That is not even
        // the unit under audit.
        $audit = Registry::call('typo3_hint_lookup', [
            'task' => 'Quality: ' . $surface[1],
            'paths' => ['composer.json', 'Tests/', 'Build/'],
            'targetVersion' => '14',
        ]);
        $ids = array_column($audit->data['hints'], 'id');
        self::assertContains('extension-repository-layout', $ids);
        self::assertLessThan(
            array_search('project-repository-layout', $ids, true) === false
                ? PHP_INT_MAX
                : (int) array_search('project-repository-layout', $ids, true),
            (int) array_search('extension-repository-layout', $ids, true),
            'the repository that is only the extension answers before the one that holds an installation',
        );

        // The neighbour the wider vocabulary had its measurement against: a
        // test question with no version in it still leads with the harness
        // hint.
        self::assertSame('project-extension-tests', $reaches('how do I test my extension')[0] ?? '');
    }

    #[Requirement('R-KNW-015')]
    #[Decision('D-KNW-008')]
    #[Test]
    public function aProjectExtensionIsToldHowToGetASuiteAtAll(): void
    {
        // core-tests describes how to write a test inside the mono repository,
        // where the harness already exists. In a project everything between
        // "composer require" and the first green run is the work, and none of
        // it stood anywhere — `D-KNW-008`.
        $result = Hints::find(
            [],
            'Add automated tests for a project sitepackage extension: unit and functional tests for an Extbase '
            . 'model, repository and controller, plus frontend tests for the rendered pages',
            6
        );
        self::assertContains('project-extension-tests', array_column($result['matchedHints'], 'id'));

        $text = self::statementsOf('project-extension-tests', 'extension-test-extensions', 'extension-test-site');
        // Each of these is a failure whose message does not name its cause.
        self::assertStringContainsString('typo3DatabaseUsername', $text);
        self::assertStringContainsString('$testExtensionsToLoad', $text);
        self::assertStringContainsString('SiteBasedTestTrait', $text);
        self::assertStringContainsString('setUpFrontendRootPage', $text);
    }

    /**
     * `feedback/2026-08-24-140340` credits these four sentences with a suite
     * that was green on its first run, and none of them had an assertion. The
     * trait the package does not ship, the call that replaces it, the flush the
     * class needs. And the symptom that makes a green `--filter` run evidence
     * of nothing.
     */
    #[Test]
    public function aTestSiteIsWrittenWithWhatMakesTheClassDeterministic(): void
    {
        $text = self::statementsOf('extension-test-site');

        self::assertStringContainsString('SiteBasedTestTrait is not available', $text);
        self::assertStringContainsString('export-ignored', $text, 'why the trait cannot be reached');
        self::assertStringContainsString('$this->get(SiteWriter::class)->write(', $text, 'what replaces it');

        // The state that outlives the truncation, and the run that hides it.
        self::assertStringContainsString('file backends', $text);
        self::assertStringContainsString('CacheManager::flushCaches()', $text);
        self::assertStringContainsString('run alone with --filter', $text);
    }

    #[Requirement('R-KNW-014')]
    #[Test]
    public function theFileAnExtensionNoLongerNeedsIsCoveredWhereItsFilesAre(): void
    {
        // "Which files does an extension need" listed every file that is still
        // current and left out the one that costs something. ext_emconf.php,
        // absent from the list, reads as "not relevant" rather than as "declare
        // this in composer.json instead".
        $hint = Hints::byId('extension-manifest');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));
        self::assertStringContainsString('ext_emconf.php', $text);

        $current = implode("\n", array_column((array) Hints::byId('extension-manifest', 14)['hints'], 'text'));
        self::assertStringContainsString('providesPackages', $current);
        self::assertStringContainsString('extra.typo3/cms.version', $current);

        // Which half is the predicate, and where it surfaces. A declaration of
        // one field of the two still reads the file. A suite that fails on a
        // deprecation is what a session meets that read first.
        self::assertStringContainsString('Declaring one of the two and not the other', $current);
        self::assertStringContainsString('failOnDeprecation', $current);
    }

    /**
     * A review of `bootstrap_package` found two contentRenderingTemplates
     * entries that named a directory the move to site sets had removed. It had
     * to read SysTemplateTreeBuilder and TreeFromLineStreamBuilder out of its
     * own vendor tree to settle that they were inert. Nothing here answered it.
     * The hint the question lands on said nothing about the key, and the key is
     * not deprecated, so no scanner names it either.
     *
     * What a reader needs is both halves. What a matched entry does, so a live
     * one stays, and what makes one unmatchable, so a dead one does not read as
     * a defect.
     */
    #[Test]
    public function aRegistrationThatCanNoLongerBeMatchedIsToldApartFromALiveOne(): void
    {
        $result = Hints::find(
            ['ext_localconf.php'],
            'is this contentRenderingTemplates registration still consumed, it names a directory that is gone',
            6,
        );
        self::assertSame('content-rendering-templates', $result['matchedHints'][0]['id']);

        $text = self::statementsOf('content-rendering-templates', 'site-set-migration');
        // What a matched entry does, and the two identifier shapes that match.
        self::assertStringContainsString("['defaultContentRendering']", $text);
        self::assertStringContainsString('configurePlugin()', $text);
        self::assertStringContainsString('fluidstyledcontent/Configuration/TypoScript/', $text);
        self::assertStringContainsString('ext_typoscript_setup.typoscript', $text);
        // Where it comes from, so a later reader can check the claim against a
        // checkout again. The two IncludeTree classes hold on every branch; the
        // resolver they replaced is a statement of its own rather than prose
        // about a version.
        self::assertStringContainsString('SysTemplateTreeBuilder::addStaticMagicFromGlobals()', $text);
        self::assertStringContainsString('TreeFromLineStreamBuilder', $text);
        $onFourteen = implode("\n", array_column((array) Hints::byId('content-rendering-templates', 14)['hints'], 'text'));
        self::assertStringNotContainsString('TemplateService', $onFourteen);
        $onTwelve = implode("\n", array_column((array) Hints::byId('content-rendering-templates', 12)['hints'], 'text'));
        self::assertStringContainsString('TemplateService::prependStaticExtra()', $onTwelve);
        self::assertStringContainsString('TypoScriptParser', $onTwelve);
        // And why nothing flags a dead one.
        self::assertStringContainsString('addStaticFile()', $text);
        self::assertStringContainsString('cleanup rather than a defect', $text);
        self::assertStringContainsString('not deprecated', $text);

        // The migration these entries remain from says so where it happens,
        // because that is the change that strands them.
        $sets = implode("\n", array_column((array) Hints::byId('site-set-migration', 14)['hints'], 'text'));
        self::assertStringContainsString('contentRenderingTemplates', $sets);
        self::assertStringContainsString('content-rendering-templates', $sets);

        // Sets are what a caller on the older branch has no migration into, so
        // the pointer is not offered there.
        $setsOnTwelve = implode("\n", array_column((array) Hints::byId('site-set-migration', 12)['hints'], 'text'));
        self::assertStringNotContainsString('contentRenderingTemplates', $setsOnTwelve);
    }

    #[Requirement('R-KNW-010')]
    #[Test]
    public function theIconHintSaysWhichHalfOfTypo3ItIsAbout(): void
    {
        // Every API the hint names is a backend one. A reader who writes a page
        // template does not infer the boundary from that list; they read it as
        // how to render an icon. The lookup says so on every answer; the hint
        // describing the same registry has to say it too.
        $hint = Hints::byId('icon-usage');
        self::assertNotNull($hint);
        $text = implode("\n", array_column($hint['hints'], 'text'));

        self::assertStringContainsString('backend', mb_strtolower($text));
        self::assertStringContainsString('frontend template', $text);
        self::assertStringContainsString('SVG', $text);
    }

    #[Test]
    public function aBackendModuleQueryIsStillAnsweredAboutBackendModules(): void
    {
        // A name for the frontend reaches the frontend hint, not a name for a
        // template: "backend module template" is a PHP question.
        $result = Hints::find([], 'backend module template', 6);

        self::assertContains('backend-modules', array_column($result['matchedHints'], 'id'));
    }

    #[Requirement('R-KNW-039')]
    #[Test]
    public function aBackendModuleInASitepackageDoesNotBecomeFrontendWork(): void
    {
        $task = 'Add a backend module to the project site package for reviewing imported product records, '
            . 'with a refresh action, status badges, icons and translated labels';
        $guide = Registry::call('typo3_task_guide', ['task' => $task]);
        $hintIds = array_column($guide->data['hints'], 'id');
        $tools = array_column($guide->data['nextTools'], 'tool');

        self::assertContains(Domains::PHP, $guide->data['domains']);
        self::assertNotContains(Domains::FLUID, $guide->data['domains']);
        self::assertNotContains(Domains::TYPOSCRIPT, $guide->data['domains']);
        self::assertSame('backend-modules', $hintIds[0]);
        self::assertNotContains('frontend-records', $hintIds);
        self::assertNotContains('sitepackage-layout', $hintIds);
        self::assertNotContains('sitepackage-initial-content', $hintIds);
        self::assertContains('typo3_component_lookup', $tools);
        self::assertContains('typo3_backend_module_lookup', $tools);
        self::assertContains('typo3_icon_lookup', $tools);
        self::assertContains('typo3_label_lookup', $tools);
    }

    /**
     * `D-KNW-001`'s **Wrong if**, run against the server on 2026-08-02. Two of
     * five backend-only task texts that name a content element came back with
     * the sitepackage layout.
     *
     * The exclusion that answers it existed already, and only the words
     * "backend module" reached it, while nothing about it was about modules.
     * The two large hints displace what the task named whenever the task is
     * backend-only. A sitepackage layout stands in the words of the backend an
     * editor works it from.
     */
    #[Test]
    public function aBackendTaskIsNotAnsweredWithTheSitepackageLayout(): void
    {
        foreach ([
            'Add a TCA field to the content element in the backend',
            'The backend preview of the content element is broken in the page module',
            'Fix the icon shown for our accordion content element in the backend new content element wizard',
            // SITE-08's prompt.
            'The accordion content element we already have needs one more field in the backend form, and the '
                . 'wrong icon shows for it in the new content element wizard. Nothing about the rendering changes.',
        ] as $task) {
            $guide = Registry::call('typo3_task_guide', ['task' => $task]);
            $hintIds = array_column($guide->data['hints'], 'id');

            self::assertNotContains('sitepackage-layout', $hintIds, $task);
            self::assertContains('content-elements', $hintIds, $task);
        }
    }

    /**
     * The half of `SITE-08` the exclusion above does not reach. The brief still
     * called such a task Fluid and TypoScript work, because "content element"
     * is a keyword of both. It stays one. Take it out and «Add a hero carousel
     * content element whose slides editors can create, order, translate and
     * hide» loses both domains too. A render question no longer reaches
     * `frontend-page-rendering`. What it stops doing is adding them where the
     * task names only the backend (`D-KNW-006`).
     */
    #[Decision('D-KNW-006')]
    #[Test]
    public function aBackendTaskIsNotCalledFluidAndTypoScriptWork(): void
    {
        foreach ([
            'Add a TCA field to the content element in the backend',
            'The backend preview of the content element is broken in the page module',
            // SITE-08's prompt.
            'The accordion content element we already have needs one more field in the backend form, and the '
                . 'wrong icon shows for it in the new content element wizard. Nothing about the rendering changes.',
        ] as $task) {
            $domains = Registry::call('typo3_task_guide', ['task' => $task])->data['domains'];

            self::assertContains(Domains::PHP, $domains, $task);
            self::assertNotContains(Domains::FLUID, $domains, $task);
            self::assertNotContains(Domains::TYPOSCRIPT, $domains, $task);
        }

        foreach ([
            // SITE-05's prompt, which names both halves.
            'Editors need a "team members" content element: a list of people picked from a folder, rendered as '
                . 'cards. Build it in our site package — the element, its backend form, and its frontend output.',
            // SKILL-04's, which names neither and is the reason the keyword stays.
            'Add a hero carousel content element whose slides editors can create, order, translate and hide '
                . 'directly inside the element. Keep its implementation maintainable and test the behavior that '
                . 'matters.',
        ] as $task) {
            $domains = Registry::call('typo3_task_guide', ['task' => $task])->data['domains'];

            self::assertContains(Domains::FLUID, $domains, $task);
            self::assertContains(Domains::TYPOSCRIPT, $domains, $task);
        }
    }

    /**
     * The other side of it, and why the gate reads the frontend markers rather
     * than the negation of namesTheFrontend(). There the backend markers win.
     * So a task that names both halves would count as backend-only and lose the
     * layout hint that is half of its answer. This is `SITE-05`'s prompt.
     */
    #[Test]
    public function aContentElementBuiltInASitepackageKeepsItsLayout(): void
    {
        $result = Hints::find([], 'Editors need a "team members" content element: a list of people '
            . 'picked from a folder, rendered as cards. Build it in our site package — the element, its backend '
            . 'form, and its frontend output.', 6);

        self::assertContains('sitepackage-layout', array_column($result['matchedHints'], 'id'));
    }

    /**
     * The order of the label declarations, which is what a reader meets first.
     * "General" is not among them any more. Every hint names the domains it
     * comes from since `D-KNW-033`. So nothing sits under the label that meant
     * "belongs to more than one and had nowhere to say so".
     */
    #[Decision('D-KNW-033')]
    #[Test]
    public function hintsAreGroupedByDomainWithPhpFirst(): void
    {
        $hints = Hints::load();
        $categories = array_column(Hints::groupByCategory($hints), 'category');

        self::assertSame('PHP', $categories[0]);
        self::assertNotContains('General', $categories);
        self::assertContains('Labels', $categories);
    }

    #[Test]
    public function everyHintCarriesItsSectionAndAtLeastOneHint(): void
    {
        foreach (Hints::load() as $hint) {
            self::assertNotSame('', $hint['id']);
            self::assertNotSame([], $hint['hints'], $hint['id'] . ' has no hints');
            self::assertNotSame('', $hint['category']);
        }
    }

    #[Decision('D-ANS-009')]
    #[Decision('D-KNW-091')]
    #[Test]
    public function noHintStatesSomethingThatOnlyHoldsOnOneBranch(): void
    {
        // The server does not know the caller's branch, so a hint has to hold
        // on every one of them. A version number, a concrete changelog file, or
        // a count taken from a single checkout is a snapshot. It reads as a
        // fact long after it was no longer one. Where the answer really is
        // branch-specific, the hint says how to look it up in the checkout —
        // `D-KNW-091`, `D-ANS-009`.
        $snapshots = [
            'a version number' => '/\bv\d+\b|\b\d+\.\d+\b|\bsince \d/i',
            'a concrete changelog file' => '/\b(Breaking|Deprecation|Feature|Important|Task)-\d+/i',
            'a count taken from a checkout' => '/\b\d{2,}\b/',
        ];

        foreach (Hints::load() as $hint) {
            // A PSR number names an interface, an XLIFF number names a file
            // format, an HTTP number names a response status. A TYPO3 exception
            // code names one throw site for good, assigned once and never
            // reused, so it says nothing about a branch. A doktype value is the
            // same. It is the number in the pages row and in the page tree, and
            // 254 has been the folder for longer than any covered branch. So is
            // an exit status, which is what the shell got and is a POSIX range
            // no TYPO3 release moves. None of them dates the statement against
            // a TYPO3 branch, which is the only thing this is looking for. Each
            // is worth its place because it is the symptom a caller arrives
            // with. So each stands with its word in front, "HTTP 404" and
            // "doktype 254" rather than bare. That is what makes them exempt
            // here without a count that happens to be three digits long. A PHP
            // version is the payload rather than the date. What this looks for
            // is a statement tied to one TYPO3 branch, and which interpreter a
            // branch requires is the question. `since` and `until` carry it
            // like any other bound statement, and every checkout can answer it
            // again. It stands with its word in front on both ends of a range,
            // "PHP 8.2 through PHP 8.6". That is what keeps `^13.4` and a bare
            // 8.2 out — `D-KNW-089`. A page type is a doktype's counterpart on
            // the other axis. A number the core registers once and a caller
            // greps for, which is why it stands with its word in front like the
            // rest. The seo extension's sitemap type is the one the corpus
            // names. The zero-date literals are the same argument with quotes
            // doing the work the word does above. `'0000-00-00 00:00:00'` is
            // what a non-nullable native datetime column stores instead of
            // NULL. MySQL has written it that way for longer than any covered
            // branch. It is the string a caller greps for after a row turned up
            // that their query could not see. Only these three, and only quoted
            // — an unquoted run of zeroes is not one of them.
            $text = (string) preg_replace(
                [
                    '/\bPSR-\d+/i', '/\bXLIFF \d+\.\d+/i', '/\bHTTP \d{3}\b/i',
                    '/\bexception \d{10}\b/i', '/\bdoktype \d{1,3}\b/i', '/\bpage type \d{4,}\b/i',
                    '/\bexit (status|code) (above )?\d{1,3}\b/i',
                    '/\bPHP \^?\d+\.\d+(\.\d+)?/i',
                    "/'0000-00-00 00:00:00'|'0000-00-00'|'00:00:00'/",
                ],
                ['PSR', 'XLIFF', 'HTTP', 'exception', 'doktype', 'page type', 'exit status', 'PHP', 'zero-date'],
                $hint['title'] . "\n" . implode("\n", array_column($hint['hints'], 'text'))
            );

            foreach ($snapshots as $what => $pattern) {
                self::assertDoesNotMatchRegularExpression(
                    $pattern,
                    $text,
                    $hint['id'] . ' states ' . $what . ', which only holds on the branch it was written from'
                );
            }
        }
    }

    /**
     * A suite is a runTests.sh target, and which ones that script offers
     * changes between majors. A command the caller's checkout does not have is
     * not a weaker answer than none. It sends them to debug their checkout for
     * something this server invented for another branch.
     *
     * Verified against this repository's own checkouts. No suite that matches
     * xlf or xliff exists in Build/Scripts/runTests.sh on 12.4 or 13.4 under
     * any name. 14.3 and main have checkIntegrityXliff and normalizeXliff —
     * `D-KNW-031`.
     */
    #[Requirement('R-KNW-024')]
    #[Decision('D-KNW-031')]
    #[Test]
    public function theSuiteListItselfIsFilteredByTheBranchItIsAskedFor(): void
    {
        $suites = static fn(?string $version): array => array_column(
            Registry::call('typo3_test_run_guide', ['query' => 'xliff labels', 'targetVersion' => $version])->data['suites'],
            'suite',
        );

        self::assertNotContains('checkIntegrityXliff', $suites('13.4'));
        self::assertContains('checkIntegrityXliff', $suites('14'));
    }

    #[Test]
    public function aSuiteIsFoundByItsName(): void
    {
        $hints = TestSuiteHints::find('phpstan');

        self::assertSame('phpstan', $hints[0]['suite']);
        self::assertStringContainsString('runTests.sh', $hints[0]['command']);
    }

    #[Test]
    public function aPhpTaskIsNeverRecommendedASassBuild(): void
    {
        $suites = array_column(TestSuiteHints::find('unit tests', [Domains::PHP]), 'suite');

        self::assertContains('unit', $suites);
        self::assertNotContains('lintScss', $suites);
        self::assertNotContains('build-css', $suites);
    }

    #[Test]
    public function aPathNamedInTheQueryNarrowsTheSuitesAsAnExplicitPathWould(): void
    {
        $suites = array_column(Registry::call('typo3_test_run_guide', [
            'query' => 'Only a Sass change in Build/Sources/Sass/component/_card.scss; recommend the narrow '
                . 'iteration check and the review-ready checks, without unrelated PHP or TypeScript suites',
        ])->data['suites'], 'suite');

        // Nothing said which version this is for. So the frontend build stands
        // under both names it has across the covered range, each with its own
        // range beside it. What the case is about is the narrowing: a Sass
        // path, and no PHP or TypeScript suite in the answer. e2e-prepare is in
        // it because a Sass change is one of the changes somebody has to look
        // at. It installs the backend that demos the components and leaves it
        // up. That is what the browser target costs a session that verifies in
        // one engine (`D-KNW-066`, `D-KNW-068`).
        self::assertSame(['build', 'build-css', 'buildCss', 'e2e-prepare', 'lintScss'], $suites);
    }

    /**
     * The other half of that filter, on the call behind `D-ANS-074`: paths in
     * css and fluid, and five domains none of them reached.
     *
     * `lintTypescript` and `unitJavascript` are among the withheld ones, which
     * is what a session left this server for `runTests.sh -h` and a grep to
     * find. The assertion counts against the suites that branch offers rather
     * than against a number here, because a suite added to
     * knowledge/test-suite-hints.json moves it.
     */
    #[Requirement('R-ANS-030')]
    #[Test]
    public function aNarrowedSuiteListNamesTheDomainsItWithheldAndCountsThem(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => [
                'Build/Sources/Sass/component/_card.scss',
                'typo3/sysext/backend/Resources/Private/Templates/DocHeader.fluid.html',
                'typo3/sysext/backend/Resources/Public/Css/backend.css',
            ],
            'targetVersion' => '15.0',
        ]);

        self::assertSame([Domains::CSS, Domains::FLUID], $answer->data['domains']);
        self::assertSame(
            [Domains::PHP, Domains::TYPOSCRIPT, Domains::XLIFF, Domains::DOCS, Domains::TYPESCRIPT],
            $answer->data['withheld']['domains'],
        );
        self::assertSame(
            count(TestSuiteHints::availableOn(15)) - count($answer->data['suites']),
            $answer->data['withheld']['suites'],
        );
        self::assertStringContainsString(
            'No given path reached php, typoscript, xliff, docs and typescript, which leaves '
            . $answer->data['withheld']['suites'] . ' suites out. A path landing in one of those domains means '
            . 'calling again.',
            $answer->text,
        );
    }

    /**
     * No filter applied, so nothing stayed back. The line belongs where a path
     * set actually left something out, and reads as noise anywhere else
     * (`D-ANS-074`).
     */
    #[Test]
    public function aCallThatNarrowedNothingWithholdsNothing(): void
    {
        $answer = Registry::call('typo3_test_run_guide', ['targetVersion' => '15.0']);

        self::assertSame(['domains' => [], 'suites' => 0], $answer->data['withheld']);
        self::assertStringNotContainsString('No given path reached', $answer->text);
    }

    /**
     * The call behind `D-GUI-022`: two of its four paths select no suite at
     * all, and the session concluded that by hand.
     *
     * The XML reference sits in the core and reaches no domain; the git hook is
     * a path nothing placed and reaches none either. The answer names both,
     * because a list of thirteen suites that simply does not mention a file
     * reads as coverage of it.
     */
    #[Requirement('R-ANS-036')]
    #[Test]
    public function aPathNoSuiteCoversIsNamedInTheAnswer(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => [
                'Build/git-hooks/commit-msg',
                'typo3/sysext/backend/Resources/Private/tsref.xml',
                'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
                'typo3/sysext/install/Resources/Private/Templates/Upgrade/ExtensionScanner.fluid.html',
            ],
            'targetVersion' => '15.0',
        ]);

        self::assertSame([Domains::PHP, Domains::FLUID], $answer->data['domains']);
        self::assertSame(
            ['Build/git-hooks/commit-msg', 'typo3/sysext/backend/Resources/Private/tsref.xml'],
            $answer->data['uncoveredPaths'],
        );
        self::assertStringContainsString(
            'No runTests.sh suite covers Build/git-hooks/commit-msg and '
            . 'typo3/sysext/backend/Resources/Private/tsref.xml. A suite is reached through the domain of a path '
            . 'and those reach none, so nothing below runs over them.',
            $answer->text,
        );
    }

    /**
     * Every path reached a domain, so there is nothing to name and the sentence
     * would be noise. That is the same bound
     * `aCallThatNarrowedNothingWithholdsNothing` puts on the withheld line.
     */
    #[Requirement('R-ANS-036')]
    #[Test]
    public function aCallWhoseEveryPathReachesASuiteNamesNoUncoveredPath(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
            'targetVersion' => '15.0',
        ]);

        self::assertSame([], $answer->data['uncoveredPaths']);
        self::assertStringNotContainsString('No runTests.sh suite covers', $answer->text);
    }

    #[Test]
    public function aNegatedDomainInTheQueryIsNotReadAsASignal(): void
    {
        // "without unrelated PHP or TypeScript suites" names both domains it
        // rules out, which is why only paths narrow the answer.
        self::assertSame([], Domains::fromPaths(['without unrelated PHP or TypeScript suites']));
        self::assertSame([Domains::CSS], Domains::fromPaths(['Build/Sources/Sass/component/_card.scss']));
    }

    #[Test]
    public function aQueryThatMatchesNoSuiteNameStillAnswersWithinItsDomain(): void
    {
        $suites = TestSuiteHints::find('recommend the review-ready checks', [Domains::CSS]);

        self::assertNotSame([], $suites, 'a narrowed list beats an empty answer');
        foreach ($suites as $suite) {
            self::assertContains(Domains::CSS, $suite['domains']);
        }
    }

    #[Test]
    public function withoutAQueryEverySuiteIsListedForBrowsing(): void
    {
        self::assertSame(count(TestSuiteHints::load()), count(TestSuiteHints::find(null)));
    }

    #[Test]
    public function theInvocationNotesApplyToEverySuite(): void
    {
        $invocation = TestSuiteHints::invocation();

        self::assertNotSame([], $invocation['notes']);
        self::assertNotSame([], $invocation['options']);
        self::assertNotSame([], $invocation['examples']);
    }

    /**
     * Every suite carries what running it does to the checkout, and the four
     * readings the mark has to tell apart.
     *
     * `build` is the one the review in `feedback/2026-08-24-100604` got first
     * with nothing to say it regenerates the committed JavaScript.
     * `lintTypescript` reads the same files and rewrites none, and `unit` is
     * the test suite `R-PRJ-007` already calls unknown.
     */
    #[Requirement('R-ANS-034')]
    #[Decision('D-ANS-099')]
    #[Test]
    public function everySuiteSaysWhatRunningItDoesToTheCheckout(): void
    {
        $runs = array_column(TestSuiteHints::load(), 'runs', 'suite');

        self::assertSame(
            [],
            array_diff(array_unique($runs), ['check', 'change', 'git', 'unknown']),
            'a suite is marked with something typo3_project_describe does not say either',
        );
        self::assertSame('change', $runs['build'], 'the suite that rewrites the committed JavaScript');
        self::assertSame('check', $runs['lintTypescript'], 'the suite that reads it and rewrites nothing');
        self::assertSame('unknown', $runs['unit'], 'a test suite runs the core\'s own code');
    }

    /**
     * The suite that stages the work tree stays on offer rather than back. The
     * mark is beside the command in the text as well — `D-ANS-099`.
     *
     * The paths are the ones the feedback called with. It found
     * `checkGruntClean` by reading Build/Scripts/runTests.sh, and its `git add
     * *` would have staged an untracked file sitting at the repository root.
     */
    #[Requirement('R-ANS-034')]
    #[Decision('D-ANS-099')]
    #[Test]
    public function aTypeScriptChangeIsOfferedTheSuiteThatStagesTheWorkingTree(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => [
                'Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts',
                'typo3/sysext/form/Resources/Public/JavaScript/backend/form-wizard/steps/settings-step.js',
            ],
            'targetVersion' => 'main',
        ]);

        self::assertSame('git', array_column($answer->data['suites'], 'runs', 'suite')['checkGruntClean'] ?? null);
        self::assertStringContainsString(
            "`CI=true ./Build/Scripts/runTests.sh -s checkGruntClean`\n"
            . 'Running it: git — it runs git over the working tree',
            $answer->text,
        );
        self::assertStringContainsString(
            "`CI=true ./Build/Scripts/runTests.sh -s build`\nRunning it: change",
            $answer->text,
        );
    }

    /**
     * What that suite stages, said out loud rather than left to the mark.
     * `feedback/2026-08-25-110726` was about to run it in the user's own
     * checkout and calls this the one concrete disaster the server prevented.
     * It asked that the sentences stay — `D-FBK-018`. The mark and its one-line
     * gloss have their hold above. A trim that keeps them and drops these
     * leaves a caller warned with no idea what of theirs is at stake.
     */
    #[Test]
    public function theStagingSuiteSaysWhichFilesItStages(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => [
                'Build/Sources/TypeScript/form/backend/form-editor/view-model.ts',
                'typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js',
            ],
            'targetVersion' => '15',
        ]);

        self::assertStringContainsString('runs `git add *` over the whole working tree', $answer->text);
        self::assertStringContainsString(
            'untracked ones included',
            $answer->text,
            'the half that reaches a file git is not tracking yet',
        );
        self::assertStringContainsString(
            'Run it in a checkout whose index you can throw away, and not in one holding work of your own',
            $answer->text,
            'the instruction the mark alone leaves the caller to derive',
        );
    }

    /**
     * A suite marked `git` names the way to its own answer that leaves the
     * checkout alone. The caller warned off meets it in the same answer —
     * `D-ANS-113`.
     *
     * Three sessions in two days read `checkGruntClean`'s warning, did not run
     * the suite, and each invented the procedure the entry withheld.
     */
    #[Decision('D-ANS-113')]
    #[Test]
    public function everySuiteThatRunsGitNamesTheDocumentAnsweringItsQuestion(): void
    {
        $documents = array_column(Documents::documents(), 'id');
        $marked = array_filter(
            TestSuiteHints::load(),
            static fn(array $suite): bool => $suite['runs'] === 'git',
        );

        self::assertNotSame([], $marked, 'no suite carries the mark this holds for');
        foreach ($marked as $suite) {
            preg_match('/documentId="([^"]+)"/', $suite['whenToUse'], $named);
            self::assertContains(
                $named[1] ?? '',
                $documents,
                $suite['suite'] . ' warns the caller off and names no document that answers it',
            );
        }

        $answer = Registry::call('typo3_test_run_guide', [
            'paths' => ['Build/Sources/TypeScript/form/backend/form-editor/view-model.ts'],
            'targetVersion' => 'main',
        ]);

        self::assertStringContainsString('core/contribution/committed-build-output', $answer->text);
    }

    /**
     * How to confirm that a suite exists on a branch, and the glob that makes
     * the obvious grep answer nothing.
     *
     * The session in `feedback/2026-08-24-100604` grepped `^    build)`, found
     * nothing, and came one step from filing a correct answer as wrong. The
     * note also names the two git-running suites this list leaves out, which
     * `-h` does hand over — `D-ANS-099`.
     */
    #[Decision('D-ANS-099')]
    #[Test]
    public function theNoteOnConfirmingASuiteSaysWhyGreppingTheCaseLabelMisses(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString('is how a suite is confirmed to exist on the branch', $notes);
        self::assertStringContainsString('`build*)`', $notes);
        self::assertStringContainsString('`buildJavascript)`', $notes);
        self::assertStringContainsString('checkIsoDatabase', $notes);
        self::assertStringContainsString('checkCharsets', $notes);
    }

    /**
     * What a caller does about `unknown`, which the mark itself only states.
     *
     * `feedback/2026-08-24-183711` ran `-s unit` over a checkout holding four
     * untracked test files and found them gone afterwards. Which suite is open
     * and the note claims none. Every `rm -rf` in Build/Scripts/runTests.sh
     * sits in a `clean*` case on all four covered majors. What remains is the
     * act — `D-ANS-099`.
     *
     * It stands apart from the other notes and leads with the copy. A second
     * session ran nineteen suites over four untracked files and read the note
     * only afterwards. A commit is not open to a session under the core's own
     * rules — `D-ANS-145`.
     */
    #[Decision('D-ANS-099')]
    #[Decision('D-ANS-145')]
    #[Test]
    public function theNoteOnATestSuiteSaysToSecureUntrackedWorkFirst(): void
    {
        $note = TestSuiteHints::invocation()['beforeYouRun'];

        self::assertStringStartsWith('Copy untracked work aside', $note);
        self::assertStringContainsString('`runs: unknown`', $note);
        self::assertStringContainsString('-s cleanTests', $note, 'the suites that do remove files');
        self::assertStringNotContainsString(
            'runs: unknown',
            implode("\n", TestSuiteHints::invocation()['notes']),
            'the warning is said once',
        );
    }

    /**
     * The caveat travels with the command rather than with the tool a session
     * may never call.
     *
     * `typo3_task_guide` is step 3 of the order every task starts in and it
     * hands over runnable suite commands; `typo3_test_run_guide` is optional.
     * A session ran nineteen suites off the first answer and met the warning
     * only in the second, near the end — `D-ANS-145`.
     */
    #[Decision('D-ANS-145')]
    #[Test]
    public function theBriefThatHandsOverASuiteCommandCarriesWhatARunCanTake(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Fix impexp export ignoring relations declared in columnsOverrides',
            'changeType' => 'bugfix',
            'paths' => ['typo3/sysext/impexp/Classes/Export.php'],
            'targetVersion' => '15',
        ]);

        self::assertNotSame([], $result->data['testSuites']);
        self::assertSame(TestSuiteHints::invocation()['beforeYouRun'], $result->data['beforeYouRun']);
        self::assertStringContainsString('Before running one: Copy untracked work aside', $result->text);

        // And the answer claims nothing where it named no suite.
        $none = Registry::call('typo3_task_guide', [
            'task' => 'Reword the readme of this sitepackage',
            'changeType' => 'documentation',
            'paths' => ['packages/sitepackage/README.md'],
        ]);
        self::assertSame([], $none->data['testSuites']);
        self::assertSame('', $none->data['beforeYouRun']);
    }

    /**
     * Both halves of what a checkout without vendor/ can run.
     *
     * The half that fails was already there; the half that works is what the
     * review assembled by hand. A bare worktree for the suites that touch
     * neither vendor/ nor bin/, so the tree under review stays as it is
     * (`D-ANS-099`).
     */
    #[Decision('D-ANS-099')]
    #[Test]
    public function thePreconditionSaysWhatABareWorktreeRunsWithoutASetup(): void
    {
        $preconditions = implode("\n", TestSuiteHints::invocation()['preconditions']);

        self::assertStringContainsString('-s composerInstall', $preconditions, 'the half a PHP suite fails on');
        self::assertStringContainsString('The node suites need none of that', $preconditions);
        self::assertStringContainsString('bare git worktree', $preconditions);
    }

    /**
     * The three things that make a suite command runnable unattended, on the
     * patch a session reported them from.
     *
     * `feedback/2026-08-01-121852` reviewed the AssetCollector deprecation and
     * reported the answer to these paths as the one that "ran clean first try".
     * It named CI=true, the `--` passthrough and `-n` for cgl.
     * theSuiteListItselfIsFilteredByTheBranchItIsAskedFor and
     * aPathNamedInTheQueryNarrowsTheSuitesAsAnExplicitPathWould already held
     * the filter, and nothing held the invocation itself. `targeted` could have
     * gone from every entry in knowledge/test-suite-hints.json and no test
     * would have noticed. `versions:check` verifies the `-s <suite>` of a
     * command and not its options.
     *
     * All three come from `.checkouts/main/Build/Scripts/runTests.sh`. Line 6
     * branches on `CI`, and `shift $((OPTIND - 1))` hands what follows `--` to
     * the tool. `-n` sets CGLCHECK_DRY_RUN, which the cgl suite turns into
     * `--dry-run --diff`.
     */
    #[Test]
    public function theTargetedInvocationKeepsWhatMakesItRunnable(): void
    {
        $answer = Registry::call('typo3_test_run_guide', [
            'query' => 'functional unit cgl phpstan',
            'paths' => [
                'typo3/sysext/core/Classes/Page/AssetCollector.php',
                'typo3/sysext/core/Tests/Unit/Page/AssetCollectorTest.php',
                'typo3/sysext/frontend/Tests/Functional/ContentObject/ImageContentObjectTest.php',
            ],
            'targetVersion' => 'main',
        ]);

        $targeted = [];
        foreach ($answer->data['suites'] as $suite) {
            self::assertStringStartsWith('CI=true ', $suite['command'], $suite['suite'] . ' is handed over without CI=true');
            if ($suite['targeted'] !== null) {
                self::assertStringStartsWith('CI=true ', $suite['targeted']);
                $targeted[$suite['suite']] = $suite['targeted'];
            }
        }

        self::assertSame('CI=true ./Build/Scripts/runTests.sh -s cgl -n', $targeted['cgl'] ?? null);
        self::assertStringContainsString(' -- ', $targeted['unit'] ?? '');
        self::assertStringContainsString(' -- ', $targeted['functional'] ?? '');
        self::assertStringContainsString('Targeted run while iterating:', $answer->text);

        // And the note that says why the passthrough is there at all, which is
        // what tells a caller the form generalises past the examples.
        self::assertStringContainsString(
            'Everything after `--` is handed to the underlying tool unchanged',
            implode("\n", TestSuiteHints::invocation()['notes'])
        );
    }

    /**
     * The mechanism behind the failure the separator note above never
     * describes. An option after a path goes to phpunit rather than to the
     * script — `D-KNW-112`.
     */
    #[Decision('D-KNW-112')]
    #[Test]
    public function theInvocationNoteSaysWhereTheOptionParsingStops(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString('stops reading its own options at the first word that is not one', $notes);
        self::assertStringContainsString('shift $((OPTIND - 1))', $notes);
        self::assertStringContainsString('Test file "--filter" not found', $notes, 'the line the failing run prints');
        // The mechanism above says what goes wrong; this says where to put the
        // words, and it is the half a caller acts on. A session wrote roughly
        // fifteen targeted invocations off it and hit the line above none of
        // the times — `feedback/2026-08-24-225153`.
        self::assertStringContainsString(
            'Every option the script owns stands before the `--`, and the path stands after it',
            $notes,
            'the mechanism is stated and the order to write is left to be worked out',
        );
    }

    /**
     * The other cause of that same line, and the one an `ls` would have caught.
     * A path that is not there ends the run before a test — `D-KNW-117`.
     */
    #[Decision('D-KNW-117')]
    #[Test]
    public function theInvocationNoteSaysWhatOneMissingPathCostsARun(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString('resolves every entry before it builds a suite', $notes, 'the mechanism');
        self::assertStringContainsString('the paths beside it never run', $notes, 'that one entry ends all of it');
        self::assertStringContainsString(
            'costs a whole container start rather than a failing test',
            $notes,
            'what the caller pays'
        );
    }

    /**
     * `R-KNW-055`, the other half. `TestSuiteHints::invocation()` goes out with
     * every `typo3_test_run_guide` answer. So the iterate-narrowly note reaches
     * the one caller it is wrong for whether or not they asked about tests. It
     * is right for every other change, which is why what lands beside it is the
     * exception rather than a rewrite.
     *
     * The case asserts both notes together because the exception is only
     * readable next to the rule. On its own it says run the whole suite, and
     * what makes that cheap is the search before the run. So the note points at
     * the statement that says where to aim it — `D-KNW-044`.
     */
    #[Requirement('R-KNW-055')]
    #[Decision('D-KNW-044')]
    #[Test]
    public function theIterateNarrowlyNoteCarriesTheOneChangeItIsWrongFor(): void
    {
        $notes = implode("\n", TestSuiteHints::invocation()['notes']);

        self::assertStringContainsString(
            'run a single test file or a single test method instead of a whole suite',
            $notes,
            'the rule the exception is an exception to',
        );
        self::assertStringContainsString('alters rendered output', $notes);
        self::assertStringContainsString('core-tests', $notes, 'the exception says where the expectations hide');
        self::assertStringContainsString('rather than widening the path set', $notes);
    }

    #[Test]
    public function aDeprecationTaskIsRecognizedAsOne(): void
    {
        $intents = TaskIntents::detect('Deprecate GeneralUtility::getUrl()');

        self::assertContains('deprecation', array_column($intents, 'id'));
    }

    /**
     * A deprecation is a change type, and the rules it owes arrive with it.
     *
     * The review session behind `feedback/2026-08-01-122113` classified the
     * patch rather than described it. The one core patch shape with a fixed
     * rule set was the one the enum had no value for. So it verified every rule
     * with a grep for the `setCorrelationId` precedent instead. The rules
     * asserted here are that precedent, read in `.checkouts/main`.
     */
    #[Test]
    public function aDeprecationIsAChangeTypeThatCarriesWhatOneOwes(): void
    {
        self::assertContains(
            'deprecation',
            TaskGuide::inputSchema()['properties']['changeType']['enum'],
        );

        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Review the patch that deprecates the AssetCollector media handling',
            'changeType' => 'deprecation',
        ]);

        self::assertContains('deprecation', array_column($brief->data['intents'], 'id'));
        foreach ([
            '@deprecated since TYPO3 v',
            'E_USER_DEPRECATED',
            'Deprecation-<issue>-<UpperCamelCaseDescription>.rst',
            '_deprecation-<issue>-<unix timestamp>:',
            'NotScanned',
            'typo3/sysext/install/Configuration/ExtensionScanner/Php/',
        ] as $owed) {
            self::assertStringContainsString($owed, $brief->text);
        }
        self::assertContains(
            'CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst',
            $brief->data['checks'],
        );

        // Once, not twice: the change type routes into the intent that already
        // carries the rules, rather than holding a second copy of them.
        self::assertSame(
            array_values(array_unique($brief->data['checklist'])),
            $brief->data['checklist'],
        );
    }

    /**
     * A task that changes nothing is not answered with the steps a patch owes.
     *
     * R-GUI-006, and the call is the one the requirement ran again with. A
     * conformance review of a site package, classified as nothing because the
     * enum had no value for it. The three items the assertion keeps out are
     * what came back for it. A focused patch, test coverage, and a commit
     * message for a session that commits nothing — `D-GUI-009`, `D-GUI-006`.
     */
    #[Requirement('R-GUI-006')]
    #[Decision('D-GUI-006')]
    #[Decision('D-GUI-009')]
    #[Test]
    public function aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist(): void
    {
        self::assertContains('audit', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $described = Registry::call('typo3_task_guide', [
            'task' => 'review the TYPO3 project and site package',
        ]);
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'Is this sitepackage written the way TYPO3 14 expects',
            'changeType' => 'audit',
        ]);

        foreach ([$described, $stated] as $brief) {
            self::assertContains('audit', array_column($brief->data['intents'], 'id'));

            $checklist = implode("\n", $brief->data['checklist']);
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            self::assertNotContains(
                'typo3_commit_message_guide',
                array_column($brief->data['nextTools'], 'tool'),
                'the follow-up calls name the step the checklist dropped',
            );

            // What it does owe instead: a finding is a piece of read code, and
            // a check nobody ran proves nothing about what it covers.
            self::assertStringContainsString('rule or documentation it contradicts', $checklist);
            self::assertStringContainsString('gap in the check layer', $checklist);
        }

        // The caller's own classification keeps the skeleton. The same task
        // text, stated as a deprecation, is author work seen from the
        // reviewer's side and keeps every step that patch owes. What the words
        // recognized goes on the end rather than out. The other caller, a
        // reviewer who names the type of the patch under review, looks the same
        // as this one (`D-GUI-009`).
        $authoring = Registry::call('typo3_task_guide', [
            'task' => 'Review the patch that deprecates the AssetCollector media handling',
            'changeType' => 'deprecation',
        ]);

        self::assertContains('Keep the patch focused on the stated task.', $authoring->data['checklist']);
        self::assertContains('audit', array_column($authoring->data['intents'], 'id'));
        $both = implode("\n", $authoring->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $both);
        self::assertStringContainsString('typo3_commit_message_guide', $both);
    }

    /**
     * A brief for work that produces a change names the deprecation sweep.
     *
     * `D-GUI-013`. The obligation is step 5 of `skills/base.md`, stated in the
     * paragraph after the exemption that removes the step. The session of
     * `feedback/2026-08-18-074327` had taken that exemption on a 404 before the
     * task became three commits on `Classes/ViewHelpers`, `Classes/Service` and
     * `Classes/Controller`. So the brief carries the obligation and its axes,
     * and the arm that changes nothing names it among what a re-run would owe.
     */
    #[Decision('D-GUI-013')]
    #[Test]
    public function aBriefForAChangeNamesTheDeprecationSweepItOwes(): void
    {
        // That session's own paths, and the change type it would have passed
        // once the task turned into a patch.
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'fix the 404 the blog extension returns in the frontend after setup',
            'paths' => [
                'packages/blog/Classes/ViewHelpers/CommentsViewHelper.php',
                'packages/blog/Classes/Controller/BlogController.php',
            ],
            'changeType' => 'bugfix',
            'targetVersion' => '14',
        ]);

        $checklist = implode("\n", $patch->data['checklist']);
        // The axes rather than step 5's reasoning: the type, the omitted query,
        // the major and the tag.
        self::assertStringContainsString('Sweep the deprecations before writing', $checklist);
        self::assertStringContainsString('type "deprecation" and the query omitted, at TYPO3 v14', $checklist);
        self::assertStringContainsString('One call per tag', $checklist);
        self::assertStringContainsString('Sweep the deprecations before writing', $patch->text);

        // Every path in the report was an extension's, and the sweep addresses
        // the package rather than the core repository. So it survives the
        // filter that drops what only the core has.
        self::assertStringContainsString(Scope::OUTSIDE_CORE_NOTICE, $patch->text);

        // The other arm, and the moment the step went. A brief for work that
        // changes nothing leaves the sweep out. It says so beside the diff, the
        // coverage and the commit message it already named.
        $operations = Registry::call('typo3_task_guide', [
            'task' => 'run the blog setup and get the frontend answering',
            'changeType' => 'operations',
        ]);

        self::assertStringNotContainsString(
            'Sweep the deprecations',
            implode("\n", $operations->data['checklist']),
        );
        self::assertStringContainsString(
            'what a patch owes — the deprecation sweep, the focused diff',
            $operations->text,
        );
        self::assertStringContainsString('Pass changeType where the task does change something', $operations->text);
    }

    /**
     * A checklist item that summarizes a rule says it does not decide the rest.
     *
     * `D-GUI-025`. Two sessions acted on an item and never opened the page
     * behind it. One read the changelog item as the word that an `@internal`
     * bugfix owes no entry. The other read the sweep as unconditional and
     * dropped it on a three-statement diff. So the item carries the clause and
     * the page carries the edge, which is what the two halves below read.
     */
    #[Decision('D-GUI-025')]
    #[Test]
    public function aChecklistItemThatSummarizesARuleSaysItDoesNotDecideTheRest(): void
    {
        $entry = Registry::call('typo3_task_guide', [
            'task' => 'write the changelog entry this bugfix owes',
            'changeType' => 'bugfix',
            'targetVersion' => '14',
        ]);

        $checklist = implode("\n", $entry->data['checklist']);
        // The clause and the id that decides it, in the item that was misread.
        self::assertStringContainsString(
            'which fix is casual is settled by typo3_rule_lookup with '
                . 'documentId=core/contribution/changelog rather than by this item',
            $checklist,
        );
        // The sweep names no page, so it carries its condition instead — and
        // the reading the second session took is what it refuses.
        self::assertStringContainsString('Only a change touching no TYPO3 API skips it', $checklist);
        self::assertStringContainsString('how small the diff is decides nothing', $checklist);

        // The other half: the page the clause names decides the case that
        // session decided for itself. Step 5's own edge has its test where the
        // rest of that step is —
        // SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched.
        $page = Registry::call('typo3_rule_lookup', ['documentId' => 'core/contribution/changelog']);

        self::assertStringContainsString('`@internal` on the changed member does not exempt it', $page->text);
        // And the edge the sweep over the rest of the items reached: an item
        // that names one directory, where the page names two.
        self::assertStringContainsString('Add the entry to both `.x` directories', $page->text);

        // The rest of the sweep, in the two intents that summarize the same
        // page. The directory a backport's file goes into is the judgement
        // neither item decides.
        $breaking = Registry::call('typo3_task_guide', [
            'task' => 'remove the deprecated renderer method and file the breaking change',
            'changeType' => 'feature',
            'targetVersion' => '14',
        ]);

        self::assertStringContainsString(
            "Which directory a backport's file goes into is settled by typo3_rule_lookup with "
                . 'documentId=core/contribution/changelog rather than by this item',
            implode("\n", $breaking->data['checklist']),
        );
    }

    /**
     * A brief names the task skill that owns the work.
     *
     * `D-SKL-013`. `skills/base.md` and the `instructions` every client
     * receives at initialize both say this call returns the workflow a task
     * belongs to. `TaskGuide` named no skill at all. It stands alone, which is
     * the half `D-ANS-050` closed. An assertion that the right name is among
     * them holds just as well when a whole unrelated workflow loads first —
     * `D-SKL-039`.
     */
    #[Decision('D-ANS-050')]
    #[Decision('D-SKL-013')]
    #[Decision('D-SKL-039')]
    #[Test]
    public function aBriefNamesTheSkillThatOwnsTheWork(): void
    {
        // That session's own task, re-run.
        $element = Registry::call('typo3_task_guide', [
            'task' => 'build a testimonials content element with a custom backend preview',
            'changeType' => 'feature',
            'targetVersion' => '14',
        ]);

        self::assertSame(['typo3-content-element-development'], $element->data['skills']);
        self::assertStringContainsString('typo3-content-element-development', $element->text);
        self::assertStringNotContainsString('typo3-extension-testing', $element->text);
        self::assertNotContains('tests', array_column($element->data['intents'], 'id'));
        // Above the payload, because a caller in the wrong workflow is in it
        // for the whole answer.
        self::assertLessThan(
            (int) strpos($element->text, 'Hints:'),
            (int) strpos($element->text, 'Owned by:'),
        );

        // The other half of the same question. A session that arrived through a
        // skill hears which one owns the task. The two sides of an audit are
        // two skills whose own descriptions hand each other away.
        $package = Registry::call('typo3_task_guide', [
            'task' => 'TYPO3 extension conformance audit of the site package',
            'paths' => ['packages/printworks_sitepackage'],
        ]);
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'review the patch on this core branch',
            'paths' => ['typo3/sysext/frontend/Classes/ContentObject/ContentObjectRenderer.php'],
        ]);

        self::assertSame(['typo3-extension-health'], $package->data['skills']);
        self::assertSame(['typo3-core-patch-review'], $patch->data['skills']);

        // A weak match routes nothing. The word named the subject and not the
        // work. A whole workflow loaded on one of those is the wrong answer
        // rather than a partly wrong one.
        $weak = Registry::call('typo3_task_guide', [
            'task' => 'restyle the slider on the homepage',
            'paths' => ['packages/printworks_sitepackage'],
        ]);

        self::assertContains('content-element', array_column($weak->data['intents'], 'id'));
        self::assertSame([], $weak->data['skills']);
        self::assertStringNotContainsString('Owned by:', $weak->text);
    }

    /**
     * And it carries its hints anyway (`D-GUI-016`).
     *
     * The one measured run where a brief named a skill worked from the hints
     * and loaded nothing. That reads as the block in place of the route. What
     * the block stands in for is step 4 of `skills/base.md`, which what the
     * brief says about it discharges. So a hold on a name this server cannot
     * verify takes the hints off every session that has no such skill
     * installed.
     */
    #[Decision('D-GUI-016')]
    #[Test]
    public function theSkillABriefNamesTakesNoHintOutOfIt(): void
    {
        $paths = [
            'packages/sitepackage/Configuration/TCA/Overrides/tt_content.php',
            'packages/sitepackage/Resources/Private/Templates/ContentElements/Teaser.html',
        ];
        $task = 'Register a new content element with a custom backend preview and a Fluid template';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'targetVersion' => '14',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '14',
            'paths' => $paths,
            'limit' => HintLookup::MAX_HINTS,
        ]);

        self::assertSame(['typo3-content-element-development'], $brief->data['skills']);
        // The two halves are the lookup's own answer, whole: the name subtracts
        // nothing from the block and nothing from the pointer to the rest.
        self::assertSame(
            array_column($lookup->data['hints'], 'id'),
            array_merge(
                array_column($brief->data['hints'], 'id'),
                array_column($brief->data['omittedHints'], 'id'),
            ),
        );
        // What step 4 of the skill needs, in the sentence that step reads.
        self::assertStringContainsString(
            sprintf(TaskGuide::HINTS_TRUNCATED, TaskGuide::HINTS_PER_GROUP),
            $brief->text,
        );
    }

    /**
     * A bugfix brief names the call that settles the release branches.
     *
     * The item asked whether the defect also affects maintained older release
     * branches, and the session behind the report held one. "The checklist item
     * pointed at work I had no way to do". It answered the question from
     * `typo3_commit_message_guide` instead and says that was the better answer
     * (`D-GUI-023`).
     */
    #[Decision('D-GUI-023')]
    #[Test]
    public function theBugfixChecklistNamesTheCallThatSettlesTheReleaseBranches(): void
    {
        $checklist = implode("\n", Registry::call('typo3_task_guide', [
            'task' => 'fix the workspaces language filter',
            'changeType' => 'bugfix',
            'paths' => ['typo3/sysext/workspaces/Classes/Service/WorkspaceService.php'],
        ])->data['checklist']);

        self::assertStringContainsString(
            'Settle which release branches the fix goes to with typo3_commit_message_guide',
            $checklist,
        );
        // And the half the tool cannot answer stays the caller's, or the
        // Releases line reads as settling which branches carry the defect.
        self::assertStringContainsString('Whether the defect is on them is your reading', $checklist);
    }

    /**
     * And the page that writes that kind of work up (`D-GUI-012`).
     *
     * The session behind the report learned the corpus exists from one place,
     * the `guides` key of `typo3_project_describe`, read at a 404 diagnosis. It
     * added functional tests three turns later without
     * `extension/testing/phpunit` (`feedback/2026-08-18-074226`). Measured
     * before the change, that brief named `core/contribution/rules`: the page
     * that judges a core patch, handed to a package.
     */
    #[Requirement('R-GUI-013')]
    #[Decision('D-GUI-012')]
    #[Decision('D-GUI-024')]
    #[Test]
    public function aBriefNamesTheGuideTheWorkIsWrittenUpIn(): void
    {
        $tests = Registry::call('typo3_task_guide', [
            'task' => 'add unit and functional tests for a ViewHelper in the blog extension',
            'paths' => ['packages/blog/Classes/ViewHelpers/GravatarViewHelper.php'],
            'changeType' => 'test',
        ]);

        self::assertSame(
            // The prose page rides on every writing brief rather than on an
            // intent — `D-DOC-068`.
            ['extension/testing/phpunit', 'any/writing/the-prose-a-patch-carries'],
            array_column($tests->data['guides'], 'id'),
        );
        // As the call rather than as the typo3://guides address, because the
        // client that reported this rendered no resource list at all.
        self::assertStringContainsString('extension/testing/phpunit', $tests->text);
        self::assertStringContainsString('typo3_rule_lookup call with that documentId', $tests->text);
        self::assertLessThan(
            (int) strpos($tests->text, 'Hints:'),
            (int) strpos($tests->text, TaskGuide::GUIDES_OWNING),
        );

        // And what the caller has to do for the page to be the one to read, in
        // both halves. The page's own declaration rather than a second sentence
        // about it, so the two cannot drift apart. Six sessions read an id and
        // a title from four surfaces and opened none of them.
        $declared = array_column(Documents::documents(), 'whenToUse', 'id');
        self::assertNotSame('', $declared['extension/testing/phpunit']);
        self::assertSame($declared['extension/testing/phpunit'], $tests->data['guides'][0]['when']);
        self::assertStringContainsString($tests->data['guides'][0]['when'], $tests->text);

        // The core side of the same work names none. What a core patch owes is
        // the three contribution documents, which the rule sections in the same
        // answer already name and quote.
        $core = Registry::call('typo3_task_guide', [
            'task' => 'add unit tests for the DataHandler hook',
            'paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php'],
            'changeType' => 'test',
        ]);

        self::assertSame(
            // Bar the page every writing brief carries — `D-DOC-068`.
            ['any/writing/the-prose-a-patch-carries'],
            array_column($core->data['guides'], 'id'),
        );
        self::assertStringContainsString(TaskGuide::GUIDES_OWNING, $core->text);
        self::assertStringNotContainsString('extension/testing/phpunit', $core->text);

        // A brief that changes nothing names only the pages of work that
        // changes nothing either. That is the rule `D-SKL-039` established for
        // the skill on the line above. The words of the change under review are
        // the words of its author.
        $review = Registry::call('typo3_task_guide', [
            'task' => 'review the patch that adds playwright tests',
            'changeType' => 'audit',
        ]);

        // The prose page is not among them: a review writes no file.
        self::assertSame([], $review->data['guides']);

        // And the obligation that had no page beside it. The patch-review
        // intent states that a finding needs a judgement on every declared
        // major and owned no guide. So the brief created the duty and named
        // nothing that discharges it. A session derived the procedure by hand
        // with the page listed to it minutes earlier by typo3_project_describe
        // (`D-GUI-024`).
        $incoming = Registry::call('typo3_task_guide', [
            'task' => 'Review an incoming pull request that changes an f:if condition in a Fluid partial',
            'paths' => ['Resources/Private/Partials/ContentElements/Table/Columns.html'],
            'changeType' => 'audit',
        ]);

        self::assertSame(
            ['extension/compatibility/a-declared-major-that-is-not-installed'],
            array_column($incoming->data['guides'], 'id'),
        );
        self::assertStringContainsString(
            'every major the package\'s Composer constraint declares',
            implode("\n", $incoming->data['checklist']),
        );
    }

    /**
     * And the acts at which the same question is worth a second ask
     * (`D-SKL-062`).
     *
     * The session behind the report opened with a German symptom report about
     * two TypoScript conditions. It went on to three kinds of work its first
     * line never named (`feedback/2026-08-18-081159`). The brief for each of
     * those sub-steps already named the skill and the guide that own it, so
     * what was absent is the moment.
     *
     * This is the half that reaches a session which called once. The other half
     * reaches one that called nothing and is a clause of `instructions.start`,
     * held by `ScopeTest::theSecondCallIsAskedAgainAtTheCallersOwnActs`.
     */
    #[Requirement('R-GUI-014')]
    #[Decision('D-SKL-062')]
    #[Test]
    public function aBriefNamesTheActsTheWorkflowQuestionIsAskedAgainAt(): void
    {
        $bugfix = Registry::call('typo3_task_guide', [
            'task' => 'Fix the TypoScript conditions of the blog extension broken on TYPO3 v14',
            'paths' => ['packages/blog/Classes/ExpressionLanguage/BlogVariableProvider.php'],
            'changeType' => 'bugfix',
        ]);

        $when = array_column($bugfix->data['nextTools'], 'when', 'tool');
        self::assertArrayHasKey('typo3_task_guide', $when);
        foreach ([
            'test directory',
            'a check the repository declares',
            'branch or commit',
            'code or documentation the package ships',
        ] as $act) {
            self::assertStringContainsString($act, $when['typo3_task_guide']);
        }

        // The session that reported it was in its own repository, and the acts
        // are the caller's own rather than the core's. So the entry is not one
        // of the steps a brief outside the core drops.
        self::assertTrue(Scope::from($bugfix->data['scope'])->isOutsideTheCore());
        self::assertStringContainsString('typo3_task_guide — again, where the work enters', $bugfix->text);

        // And a brief for work that changes nothing keeps it: a review asked to
        // make the change is the subject the opening did not name.
        $review = Registry::call('typo3_task_guide', [
            'task' => 'Review the blog extension for TYPO3 v14 readiness',
            'changeType' => 'audit',
        ]);
        self::assertContains('typo3_task_guide', array_column($review->data['nextTools'], 'tool'));
    }

    /**
     * Looking at a change is a kind of work of its own (`D-GUI-014`).
     *
     * Measured before the change. "prove a rendering change in the browser
     * after fixing a frontend crash" matched no intent at all. That is the task
     * `feedback/2026-08-18-074226` finished without the page for it. So the
     * brief named no guide and no skill.
     */
    #[Decision('D-GUI-014')]
    #[Test]
    public function aBriefRecognizesLookingAtAChangeInABrowser(): void
    {
        $reported = Registry::call('typo3_task_guide', [
            'task' => 'prove a rendering change in the browser after fixing a frontend crash',
            'paths' => ['packages/blog/Resources/Private/Templates/Post/List.html'],
        ]);

        self::assertContains('browser-check', array_column($reported->data['intents'], 'id'));
        self::assertSame(
            ['any/testing/browser-check', 'any/writing/the-prose-a-patch-carries'],
            array_column($reported->data['guides'], 'id'),
        );
        // The page holds on both sides, so the core side names the same one.
        // The session that wanted to see a backend CSS patch was in a core
        // checkout. It told its reader five times that it could not
        // (`feedback/2026-08-10-182417`).
        $core = Registry::call('typo3_task_guide', [
            'task' => 'prove a rendering change in the browser after fixing a frontend crash',
            'paths' => ['typo3/sysext/frontend/Classes/ContentObject/ContentObjectRenderer.php'],
        ]);

        self::assertSame(
            ['any/testing/browser-check', 'any/writing/the-prose-a-patch-carries'],
            array_column($core->data['guides'], 'id'),
        );

        // And a review is where that session was, so the intent changes nothing
        // and keeps the page in a brief that changes nothing either.
        $review = Registry::call('typo3_task_guide', [
            'task' => 'review the backend css patch for sticky positioning, which I cannot judge visually',
            'paths' => ['Build/Sources/Sass/component/module.scss'],
            'changeType' => 'audit',
        ]);

        self::assertSame(['any/testing/browser-check'], array_column($review->data['guides'], 'id'));

        // What it is not is the suite intent widened. A look is the step before
        // a spec. The workflow that writes one is a whole test layer the
        // session that only wants to see the change never asked for.
        self::assertSame([], $reported->data['skills']);
        $suite = Registry::call('typo3_task_guide', [
            'task' => 'write playwright tests for the editor journey',
            'paths' => ['packages/site/Tests/e2e/editor.spec.ts'],
        ]);

        self::assertNotContains('browser-check', array_column($suite->data['intents'], 'id'));
    }

    /**
     * The needles are the act in the words sessions write it in (`D-GUI-014`).
     *
     * That entry assumed a session which is only looking says so in its five
     * original needles, and recorded the assumption as unmeasured. Measured on
     * 2026-08-24 it does not hold. The words of the page's own title, "in a
     * real browser", missed "in a browser", because a needle matches as one
     * word-bounded phrase. The intent reached none of the 49 scenario prompts.
     *
     * Each case below is a phrasing a filed session used —
     * `feedback/2026-08-24-140239` for the first three.
     */
    #[Decision('D-GUI-014')]
    #[Test]
    #[DataProvider('lookingAtAChangeIsAskedForInTheseWords')]
    public function aBriefRecognizesLookingHoweverTheSessionPhrasedIt(string $task): void
    {
        $brief = Registry::call('typo3_task_guide', ['task' => $task]);

        self::assertContains('browser-check', array_column($brief->data['intents'], 'id'));
        self::assertContains('any/testing/browser-check', array_column($brief->data['guides'], 'id'));
    }

    /** @return array<string, array{string}> */
    public static function lookingAtAChangeIsAskedForInTheseWords(): array
    {
        return [
            'the title of the page itself' => ['verify the extension rendering in a real browser'],
            'verification named for the frontend' => ['we need the tests and also the frontend verification'],
            'the rendering named as correct' => ['check that the extension renders correctly on the frontend'],
            'looking named as looking' => ['look at the rendered page and confirm the banner appears'],
            'the site opened' => ['open the site and confirm the consent banner shows up'],
        ];
    }

    /**
     * The widened needles stay the act rather than the subject (`D-GUI-014`).
     *
     * Its first **Wrong if** is a brief that names the page to a session that
     * writes a suite. Its second is one that needs the probe instead. Both stay
     * held with the vocabulary above in the corpus.
     */
    #[Decision('D-GUI-014')]
    #[Test]
    #[DataProvider('theseAreOtherWorkThanLooking')]
    public function aWidenedNeedleReachesNeitherTheSuiteNorTheProbe(string $task): void
    {
        $brief = Registry::call('typo3_task_guide', ['task' => $task]);

        self::assertNotContains('browser-check', array_column($brief->data['intents'], 'id'));
    }

    /** @return array<string, array{string}> */
    public static function theseAreOtherWorkThanLooking(): array
    {
        return [
            'a playwright suite' => ['write playwright specs for the editor journey'],
            'end-to-end coverage' => ['add e2e coverage for the login form'],
            'browser coverage for a page' => ['add browser coverage for the important page and its form'],
            'a visual regression suite' => ['set up visual regression tests'],
            'the rendering probe' => ['prove what lib.parseFunc_RTE actually renders with a throwaway functional test'],
        ];
    }

    /**
     * A review request that names a change routes the review (`D-SKL-039`).
     *
     * Run on 2026-08-14, this named `typo3-core-patch-development`. "breaking"
     * is the intent's own needle and the words of the patch under review are
     * the words of its author. None of the review shapes `audit` carried is one
     * a request that names its change by number arrives in. It is `D-SKL-013`'s
     * second **Wrong if** by a second route.
     */
    #[Decision('D-SKL-039')]
    #[Test]
    public function aReviewOfAChangeRoutesTheReview(): void
    {
        $review = Registry::call('typo3_task_guide', [
            'task' => 'review core patch 95169 and say whether it is breaking',
        ]);

        self::assertSame(['typo3-core-patch-review'], $review->data['skills']);
        // The intent stays recognized and its checklist stays in the brief.
        // What it knows is what the caller asked, and its first item is the
        // answer. The session enters a workflow and reads a statement, which is
        // why only the route stays back.
        self::assertContains('breaking', array_column($review->data['intents'], 'id'));
        self::assertStringContainsString(
            'Settle first whether the change is breaking at all',
            implode("\n", $review->data['checklist']),
        );

        // The same words, from the side that writes the change, route what they
        // always did.
        $authoring = Registry::call('typo3_task_guide', [
            'task' => 'remove the public method and make it a breaking change',
            'paths' => ['typo3/sysext/core/Classes/Utility/GeneralUtility.php'],
        ]);

        self::assertSame(['typo3-core-patch-development'], $authoring->data['skills']);

        // And the review that fetches what it reviews keeps both: neither
        // workflow writes a change of its own.
        $fetched = Registry::call('typo3_task_guide', [
            'task' => 'review core patch 95169 and check whether the patch still applies',
        ]);

        self::assertSame(
            ['typo3-core-patch-checkout', 'typo3-core-patch-review'],
            $fetched->data['skills'],
        );
    }

    /**
     * A backend-module task reaches the skill that owns it (R-SKL-001).
     *
     * The words of `SITE-07` matched `backend-ui` and nothing else, and that
     * intent names no skill. The brief answered with the markup a module
     * writes. The workflow that decides where the module sits, who may open it
     * and how to prove it never loaded. Inside the core the same task is a
     * patch and owes what a patch owes, which is the split `tests` and `audit`
     * already make.
     */
    #[Test]
    public function aBackendModuleTaskReachesTheSkillThatOwnsIt(): void
    {
        $package = Registry::call('typo3_task_guide', [
            'task' => 'add a backend module to our site package that lists imported records',
            'paths' => ['packages/printworks_sitepackage'],
        ]);
        $core = Registry::call('typo3_task_guide', [
            'task' => 'fix the doc header button in the list module',
            'paths' => ['typo3/sysext/backend/Classes/Controller/RecordListController.php'],
        ]);

        self::assertSame(['typo3-backend-module-development'], $package->data['skills']);
        self::assertSame(['typo3-core-patch-development'], $core->data['skills']);
    }

    /**
     * A triage gets a triage's answer, not a diff review's.
     *
     * `D-GUI-011`. The session behind the report picked `audit` because it is
     * the value documented as one that writes no file, "the closest of the
     * available values". It got the removals to enumerate, the extension
     * scanner matcher and `checkRst` over a core diff, for work that produces
     * no diff. It used none of them and says so.
     */
    #[Decision('D-GUI-011')]
    #[Test]
    public function aTriageIsAnsweredWithWhatDecidingAReportNeeds(): void
    {
        self::assertContains('triage', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $described = Registry::call('typo3_task_guide', [
            'task' => 'Triage an old open core bug report: establish whether it still reproduces against this checkout',
        ]);
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'Say whether Forge 15984 is still a thing on this checkout',
            'changeType' => 'triage',
        ]);

        foreach ([$described, $stated] as $brief) {
            self::assertSame('strong', array_column($brief->data['intents'], 'confidence', 'id')['triage'] ?? null);
            self::assertContains('typo3-core-issue-triage', $brief->data['skills']);

            $checklist = implode("\n", $brief->data['checklist']);
            // A triage writes no patch, so none of what one owes.
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            // Nor what a review of a diff owes, which is the reported half: a
            // triage removes nothing and renames nothing.
            self::assertStringNotContainsString('enumerate what it removes or renames', $checklist);
            self::assertStringNotContainsString('ExtensionScanner', $checklist);
            self::assertStringNotContainsString('Report what the review did not reach', $checklist);

            // What it owes instead: the report read against the branch under
            // judgement, and a verdict that says what would change it.
            self::assertStringContainsString('Read the comments before the description', $checklist);
            self::assertStringContainsString('A verdict names what would change it', $checklist);
            self::assertStringContainsString('Report what the triage did not reach', $checklist);
        }
    }

    /**
     * A request for a cause gets what the search for one needs, not the brief
     * for a patch.
     *
     * `D-SKL-065`, and the three calls are the ones it measured on 2026-08-19.
     * The content element handed over the workflow for a new one, the backend
     * module the one for a build. The page that answered with an error matched
     * nothing at all and came back with keep the change focused, add coverage,
     * draft the commit message. `SKILL-15` is the case.
     */
    #[Decision('D-SKL-065')]
    #[Test]
    public function aRequestForACauseIsAnsweredWithWhatFindingOneNeeds(): void
    {
        self::assertContains('diagnosis', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $element = Registry::call('typo3_task_guide', [
            'task' => 'A content element renders the wrong markup on the page, find the cause',
        ]);
        $module = Registry::call('typo3_task_guide', [
            'task' => 'The backend module shows a blank page since the upgrade, find out why and report the '
                . 'file and the cause, change nothing',
        ]);
        $page = Registry::call('typo3_task_guide', [
            'task' => 'The frontend page answers with an error and I need to find which file causes it, '
                . 'without changing anything',
        ]);
        // And the caller who classifies rather than describes, which is the
        // trap `D-GUI-011` recorded. Before this value existed, `audit` was the
        // one documented as one that writes no file.
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'One page on our site answers with an error and the rest of it is fine',
            'changeType' => 'diagnosis',
        ]);

        foreach ([$element, $module, $page, $stated] as $brief) {
            self::assertSame(
                'strong',
                array_column($brief->data['intents'], 'confidence', 'id')['diagnosis'] ?? null,
            );

            $checklist = implode("\n", $brief->data['checklist']);
            // None of what a patch owes, which is what all three came back
            // with. The element and the module through the workflow that writes
            // one, the page through the skeleton nothing had placed.
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('Sweep the deprecations', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            self::assertNotContains(
                'typo3_commit_message_guide',
                array_column($brief->data['nextTools'], 'tool'),
            );
            // Nor the other three arms', each of which owes something a
            // diagnosis does not.
            self::assertStringNotContainsString('Report what the review did not reach', $checklist);
            self::assertStringNotContainsString('Report what the triage did not reach', $checklist);
            self::assertStringNotContainsString('the URL the installation answers on', $checklist);

            // What it owes instead: the reading each half of the answer came
            // from, the log before the page, and the stop before the fix.
            self::assertStringContainsString('Name the file and the reason together', $checklist);
            self::assertStringContainsString('Stop at the finding', $checklist);
            self::assertStringContainsString('Report what the diagnosis did not reach', $checklist);
            self::assertStringContainsString('installation-exception-output', $checklist);
            self::assertStringContainsString('An empty log is a finding rather than a missing file', $checklist);
        }

        // The brief names the workflow that owns the installation half, and
        // takes no route for the caller. The same needles reach a cause in the
        // package's own code, which has no workflow before somebody agrees the
        // fix.
        self::assertStringContainsString(
            'typo3-development-installation',
            implode("\n", $page->data['checklist']),
        );
        self::assertSame([], $page->data['skills']);

        // The element is the case the todo names: recognized beside
        // `content-element`, it still loses the workflow for adding one.
        self::assertContains('content-element', array_column($element->data['intents'], 'id'));
        self::assertSame([], $element->data['skills']);
        // What that intent knows still arrives — only the route is withheld
        // (`D-SKL-039`).
        self::assertStringContainsString(
            'Describe the editor workflow before naming a single field',
            implode("\n", $element->data['checklist']),
        );

        // A stated change type that does change something keeps the skeleton.
        // So "find out why it broke and fix it" gets the answer for the patch
        // it is (`D-GUI-009`).
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'Find out why the frontend throws and fix it',
            'changeType' => 'bugfix',
        ]);

        self::assertContains('Keep the patch focused on the stated task.', $patch->data['checklist']);
        self::assertStringContainsString(
            'An empty log is a finding rather than a missing file',
            implode("\n", $patch->data['checklist']),
        );
    }

    /**
     * Work that operates an installation gets a boot's answer, not a patch's
     * and not a review's.
     *
     * D-GUI-008, and the first call is `feedback/2026-08-03-154508`'s own. A
     * boot of a Composer project from a fresh clone had no change type of its
     * own, so `unknown` handed it the patch skeleton. The one intent it reached
     * was `installation-setup`, matched on `install dependencies`, which is
     * Composer's install and not TYPO3's — `D-GUI-009`.
     */
    #[Requirement('R-GUI-006')]
    #[Decision('D-GUI-008')]
    #[Decision('D-GUI-009')]
    #[Test]
    public function workThatOperatesAnInstallationIsAnsweredWithABootBrief(): void
    {
        self::assertContains('operations', TaskGuide::inputSchema()['properties']['changeType']['enum']);

        $confidences = static fn(ToolResult $brief): array => array_column(
            $brief->data['intents'],
            'confidence',
            'id',
        );
        $described = Registry::call('typo3_task_guide', [
            'task' => 'Boot up a TYPO3 project locally for the first time from a fresh clone: install '
                . 'dependencies, start the local environment, import the demo database and fileadmin, build '
                . 'frontend assets, create a backend user, verify the site responds',
        ]);
        $stated = Registry::call('typo3_task_guide', [
            'task' => 'Bring the demo installation this repository declares up on this machine',
            'changeType' => 'operations',
        ]);

        foreach ([$described, $stated] as $brief) {
            self::assertSame('strong', $confidences($brief)['installation-operations'] ?? null);

            $checklist = implode("\n", $brief->data['checklist']);
            self::assertStringNotContainsString('Keep the patch focused', $checklist);
            self::assertStringNotContainsString('test coverage', $checklist);
            self::assertStringNotContainsString('typo3_commit_message_guide', $checklist);
            self::assertNotContains(
                'typo3_commit_message_guide',
                array_column($brief->data['nextTools'], 'tool'),
                'the follow-up calls name the step the checklist dropped',
            );

            // Nor the review's, which is the second half of the fork. A boot
            // reports what it produced, and a surface it did not reach is not a
            // finding it holds back.
            self::assertStringNotContainsString('Report what the review did not reach', $checklist);
            self::assertStringContainsString('the URL the installation answers on', $checklist);

            // What it owes instead: the environment this repository declares,
            // and the import that seeds it.
            self::assertStringContainsString('.ddev/config.yaml', $checklist);
            self::assertStringContainsString('typo3 extension:setup', $checklist);
        }

        // The value is half of it; the needle is the other half. The reported
        // call no longer reaches the intent whose five items are all properties
        // of the setup command.
        self::assertArrayNotHasKey('installation-setup', $confidences($described));

        // And a task that really does create an installation still does —
        // `feedback/2026-08-03-162826`, which D-GUI-008 names as the measure.
        $installing = Registry::call('typo3_task_guide', [
            'task' => 'install TYPO3 14.3.5 unattended from a shell script so that "ddev start" sets the '
                . 'instance up on its own',
        ]);

        self::assertSame('strong', $confidences($installing)['installation-setup'] ?? null);
        self::assertSame('weak', $confidences($installing)['installation-operations'] ?? null);
        self::assertContains('Keep the patch focused on the stated task.', $installing->data['checklist']);

        // A stated change type that does change something keeps the skeleton.
        // So the patch steps stay for work that boots something and fixes it —
        // D-GUI-008's own **Wrong if**. What the words recognized goes below
        // them rather than out (`D-GUI-009`).
        $patch = Registry::call('typo3_task_guide', [
            'task' => 'fix the deploy hook so the import runs when booting the installation',
            'changeType' => 'bugfix',
        ]);

        self::assertSame('strong', $confidences($patch)['installation-operations'] ?? null);
        self::assertContains('Keep the patch focused on the stated task.', $patch->data['checklist']);
        self::assertStringNotContainsString(
            'the URL the installation answers on',
            implode("\n", $patch->data['checklist']),
        );
    }

    /**
     * A task about an installation that already exists gets the setup items
     * under their condition, never as instructions.
     *
     * `feedback/2026-08-18-074305` is the keep-request. Six of the brief's
     * items were "correctly labelled, so the guard worked", on a session that
     * repaired an installation somebody else had set up. What it read is
     * `installation-setup` with a weak match through `development
     * installation`, which `D-SKL-051` decided and nothing held for this
     * intent. A needle moved into `match` would state "state the admin username
     * and the password in your reply" as a step of a repair.
     */
    #[Test]
    public function theSetupItemsArriveUnderTheirGuard(): void
    {
        $brief = Registry::call('typo3_task_guide', [
            'task' => 'Blog extension development installation was set up, backend works but frontend '
                . 'returns 404 page not found',
            'changeType' => 'operations',
            'targetVersion' => '14.3',
            'paths' => ['config/sites/blog/config.yaml', 'config/sites/main/config.yaml', '.ddev/config.yaml'],
        ]);

        $confidences = array_column($brief->data['intents'], 'confidence', 'id');
        self::assertSame('strong', $confidences['installation-operations'] ?? null);
        self::assertSame('weak', $confidences['installation-setup'] ?? null);

        $setup = array_values(array_filter(
            TaskIntents::load(),
            static fn(array $intent): bool => $intent['id'] === 'installation-setup',
        ))[0];
        $guard = ucfirst($setup['condition']) . ': ';

        $guarded = array_values(array_filter(
            $brief->data['checklist'],
            static fn(string $entry): bool => str_starts_with($entry, $guard),
        ));
        self::assertCount(
            count($setup['checklist']),
            $guarded,
            'the items of a weakly matched intent are stated as fact or dropped, rather than guarded',
        );
        self::assertStringContainsString('the admin username and the password in your reply', implode("\n", $guarded));

        // The guard is what lets a session skip them, so it is the prefix and
        // not a sentence somewhere above them.
        foreach ($brief->data['checklist'] as $entry) {
            if (str_starts_with($entry, $guard)) {
                continue;
            }
            self::assertStringNotContainsString('admin username', $entry);
        }

        // What the condition costs stays a prefix: the skill still arrives,
        // from the intent that matched strongly.
        self::assertStringContainsString('Possibly also: Setting an installation up, ' . $setup['condition'], $brief->text);
        self::assertContains('typo3-development-installation', $brief->data['skills']);
    }

    /**
     * A review brief names what the change removes, whatever the task says.
     *
     * `R-GUI-010`, on a core patch review that under-stated the removal of an
     * `@internal` method until the user pushed back. `D-GUI-004` read why
     * nothing could have named it. The `breaking` intent matches on words a
     * review's task text does not have. What a diff takes away is what the
     * review is about to find out. So the surface stands as a statement rather
     * than a match.
     */
    #[Decision('D-GUI-004')]
    #[Requirement('R-GUI-010')]
    #[Test]
    public function aReviewBriefNamesWhatTheChangeRemoves(): void
    {
        $review = Registry::call('typo3_task_guide', [
            'task' => 'review the core patch replacing GD-based error thumbnails with a static SVG placeholder',
        ]);

        self::assertContains('audit', array_column($review->data['intents'], 'id'));
        self::assertNotContains('breaking', array_column($review->data['intents'], 'id'));

        $checklist = implode("\n", $review->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $checklist);
        foreach ([
            'typo3/sysext/install/Configuration/ExtensionScanner/Php/',
            'Breaking or Deprecation changelog file',
            '[!!!]',
            'CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst',
        ] as $owed) {
            self::assertStringContainsString($owed, $checklist);
        }

        // The rule the surface states is the core's own, read in
        // `.checkouts/main` for `D-ANS-035`, and not the feedback's.
        // `@internal` waives no marker, because `@internal` does not decide
        // whether the removal is breaking at all.
        self::assertStringContainsString('whether anything outside the core calls the member', $checklist);

        // Outside the core the enumeration is still owed and the core's own
        // obligations are not: a sitepackage has no changelog and no scanner.
        $extension = Registry::call('typo3_task_guide', [
            'task' => 'Is this sitepackage written the way TYPO3 14 expects',
            'changeType' => 'audit',
        ]);

        $outside = implode("\n", $extension->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $outside);
        self::assertStringNotContainsString('ExtensionScanner', $outside);
        self::assertStringNotContainsString('runTests.sh', $outside);
    }

    /**
     * The call the feedback reported, answered from the side that still had the
     * failure.
     *
     * It states the type of the patch under review rather than of its own work.
     * So the audit intent fell out, and the surface `R-GUI-010` states never
     * reached it (`D-GUI-009`).
     */
    #[Decision('D-GUI-009')]
    #[Test]
    public function aReviewThatStatesThePatchTypeNamesWhatItRemoves(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'review the core patch replacing GD-based error thumbnails with a static SVG placeholder',
            'changeType' => 'cleanup',
        ]);

        $checklist = implode("\n", $result->data['checklist']);
        self::assertStringContainsString('enumerate what it removes or renames', $checklist);
        self::assertStringContainsString('typo3/sysext/install/Configuration/ExtensionScanner/Php/', $checklist);

        // The stated type keeps its skeleton and its own item, so the caller
        // who was authoring after all loses nothing to the review half.
        self::assertContains('Keep the patch focused on the stated task.', $result->data['checklist']);
        self::assertStringContainsString('Keep the cleanup mechanical', $checklist);

        // Reading a patch is not putting one up. The Gerrit steps reached this
        // brief through "review" as a needle of the submission intent. That is
        // the workflow for the push and the patch set.
        self::assertNotContains('submission', array_column($result->data['intents'], 'id'));
        self::assertStringNotContainsString('refs/for/', $checklist);
    }

    /**
     * The route from a stated change type to the intent that owns it. That is a
     * map rather than the type on the end of the words the matcher reads.
     *
     * On the end it made every intent with the type as a needle a strong match.
     * `cleanup` is the one where that is a different piece of work. The enum
     * value means a mechanical patch and the intent means a whole repository
     * put right (`D-GUI-027`).
     */
    #[Decision('D-GUI-027')]
    #[DataProvider('statedChangeTypes')]
    #[Test]
    public function aStatedChangeTypeNamesTheIntentThatOwnsIt(string $changeType, ?string $intent): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Do the thing the task describes',
            'changeType' => $changeType,
        ]);

        $ids = array_column($result->data['intents'], 'id');
        if ($intent === null) {
            self::assertSame([], $ids);

            return;
        }
        self::assertSame([$intent], $ids);
    }

    /** @return array<string, array{string, ?string}> */
    public static function statedChangeTypes(): array
    {
        return [
            'audit' => ['audit', 'audit'],
            'triage' => ['triage', 'triage'],
            'operations' => ['operations', 'installation-operations'],
            'diagnosis' => ['diagnosis', 'diagnosis'],
            'deprecation' => ['deprecation', 'deprecation'],
            'documentation' => ['documentation', 'documentation'],
            'test' => ['test', 'tests'],
            // The three that name a kind of change and no kind of work. What
            // the task is about is the sentence, and the type contributes its
            // own checklist arm instead.
            'bugfix' => ['bugfix', null],
            'feature' => ['feature', null],
            'cleanup' => ['cleanup', null],
        ];
    }

    /**
     * A task that includes a site set, which is not a task that defines one of
     * its settings.
     *
     * "site set" was a needle that confirms the intent. So a sitemap task that
     * named a set it wanted in spent four checklist items on the definition of
     * a setting it never touched. That went over the condition that intent
     * states for itself (`D-GUI-027`).
     */
    #[Decision('D-GUI-027')]
    #[Test]
    public function namingASiteSetIsNotNamingASettingItDefines(): void
    {
        $including = Registry::call('typo3_task_guide', [
            'task' => 'Add an XML sitemap to a TYPO3 site: include the typo3/seo-sitemap site set and add a '
                . 'robots.txt route to the site configuration',
            'changeType' => 'feature',
            'targetVersion' => '14',
            'paths' => ['config/sites/main/config.yaml'],
        ]);

        $intents = array_column($including->data['intents'], 'confidence', 'id');
        self::assertSame('weak', $intents['site-setting'] ?? null);
        foreach ($including->data['checklist'] as $item) {
            if (str_contains($item, 'Decide who the value is for')) {
                self::assertStringStartsWith('Only if the task adds or changes a setting', $item);
            }
        }

        // The words that do name the work still confirm it.
        $defining = Registry::call('typo3_task_guide', [
            'task' => 'Add a setting to the site set a sitepackage ships',
            'changeType' => 'feature',
        ]);
        self::assertSame(
            'strong',
            array_column($defining->data['intents'], 'confidence', 'id')['site-setting'] ?? null,
        );
    }

    /**
     * The call the feedback reported: one named file and a concrete edit,
     * classified as a cleanup.
     *
     * Six items about auditing a repository arrived as recognized work beside
     * the four that answered the task, over the condition the intent states for
     * itself. The words still reach it weakly — "better" is one of its own —
     * and that is the shape the caller can skip (`D-GUI-027`).
     */
    #[Decision('D-GUI-027')]
    #[Test]
    public function aCleanupOfOneNamedFileLeavesTheRepositoryItemsConditional(): void
    {
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Group the fields of this TCA file better',
            'changeType' => 'cleanup',
            'paths' => ['packages/animals/Configuration/TCA/tx_animals_domain_model_animal.php'],
            'targetVersion' => '14',
        ]);

        $intents = array_column($result->data['intents'], 'confidence', 'id');
        self::assertSame('strong', $intents['tca-field']);
        self::assertSame('weak', $intents['cleanup']);

        // The workflow for auditing a repository is not loaded on a one-file
        // edit, and the audit items say what they hold under.
        self::assertNotContains('typo3-extension-health', $result->data['skills']);
        foreach ($result->data['checklist'] as $item) {
            if (str_contains($item, 'Run the audit before writing the list')) {
                self::assertStringStartsWith('Only if the task asks for the repository', $item);
            }
        }

        // What the stated type does contribute is its own arm.
        self::assertStringContainsString('Keep the cleanup mechanical', implode("\n", $result->data['checklist']));
    }

    #[Test]
    public function aWordThatOnlyNamesTheSubjectMatchesConditionally(): void
    {
        // "field label" in a FormEngine task is not an XLF change, but the word
        // alone looks exactly like one.
        $intents = TaskIntents::detect(
            'Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields'
        );

        self::assertSame(['labels'], array_column($intents, 'id'));
        self::assertSame('weak', $intents[0]['confidence']);
        self::assertSame([], TaskIntents::confirmed($intents));
        self::assertSame([], TaskIntents::rules(TaskIntents::confirmed($intents)));
    }

    #[Test]
    public function anXlfSignalMatchesTheLabelIntentOutright(): void
    {
        $intents = TaskIntents::confirmed(TaskIntents::detect('Add one label to locallang_layout.xlf'));

        self::assertSame(['labels'], array_column($intents, 'id'));

        // What the intent is worth is the rules the caller ends up with, not
        // which corpus they came from. The XLIFF lifecycle used to be a prose
        // section this intent queried by name. It is a bound statement in
        // language-files now, and the guide reaches it by match instead.
        $guide = Registry::call('typo3_task_guide', ['task' => 'Add one label to locallang_layout.xlf']);

        self::assertContains('language-files', array_column($guide->data['hints'], 'id'));
        self::assertStringContainsString('x-unused-since', $guide->text);
    }

    /**
     * The suites a domain always runs stand in the answer even where nothing in
     * the task names a suite. This sentence is about TSconfig field labels. The
     * intent matcher finds the XLIFF checks in it, and the suite matcher finds
     * the label integrity ones. Neither reaches the functional suite the change
     * can fail on. It came off the hints until `D-KNW-031`, where twenty-eight
     * of them repeated it as their own `checks` list.
     */
    #[Decision('D-KNW-031')]
    #[Test]
    public function theBaseSuitesOfADomainAreStatedWhateverTheTaskNames(): void
    {
        $checks = Registry::call('typo3_task_guide', [
            'task' => 'Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields',
            'paths' => ['typo3/sysext/backend/Classes/Form'],
            'changeType' => 'bugfix',
        ])->data['checks'];

        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s functional', $checks);
        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s unit', $checks);

        // And they are the domain's, not one list for everybody: a Sass change
        // gets the frontend build and no PHP suite.
        $sass = Registry::call('typo3_task_guide', [
            'task' => 'Restyle the card component in the backend',
            'paths' => ['Build/Sources/Sass/component/_card.scss'],
        ])->data['checks'];

        self::assertContains('CI=true ./Build/Scripts/runTests.sh -s lintScss', $sass);
        self::assertNotContains('CI=true ./Build/Scripts/runTests.sh -s functional', $sass);
    }

    #[Test]
    public function aRecognizedIntentPullsTheRulesThatApply(): void
    {
        $rules = TaskIntents::rules(TaskIntents::detect('deprecate a method'));

        self::assertNotSame([], $rules);
        foreach ($rules as $rule) {
            self::assertNotSame('', $rule['heading']);
        }
    }

    #[Requirement('R-ANS-017')]
    #[Decision('D-ANS-035')]
    #[Test]
    public function aRemovalIsToldWhatTheScannerMatcherRequires(): void
    {
        // R-ANS-017. "Consider an extension scanner matcher" was the whole of
        // what a removal heard. The reviewer of one asked the rules and got an
        // answer without it at all — `D-ANS-035`.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Remove public method GifBuilder::getTemporaryImageWithText()',
        ]);

        $checklist = implode("\n", $result->data['checklist']);

        self::assertStringContainsString('Configuration/ExtensionScanner/Php/', $checklist);
        self::assertStringContainsString('FullyScanned', $checklist);
    }

    /**
     * R-GUI-008. The session that reported this had every fact it needed and
     * still read a report about f:image as an API question. Is the value passed
     * of the type the argument accepts. The product asks what the editor and
     * the visitor get once the image changes (`feedback/2026-08-02-145043`) —
     * `D-GUI-005`.
     */
    #[Requirement('R-GUI-008')]
    #[Decision('D-GUI-005')]
    #[Test]
    public function everyBriefOpensOnThePremiseADefectIsJudgedBy(): void
    {
        foreach ([
            [
                'task' => 'Fix f:image failing on a src that carries a cache busting query string',
                'paths' => ['typo3/sysext/fluid'],
                'changeType' => 'bugfix',
            ],
            // Outside the core as well. An editor and a visitor are the same
            // two people there. The premise names no core-only artifact that
            // the filter would take out with the changelog.
            [
                'task' => 'Add a testimonials content element',
                'paths' => ['packages/my_sitepackage/Classes/Controller/TestimonialController.php'],
                'changeType' => 'feature',
            ],
            // The arm that changes nothing carries it too, and it is the case
            // behind R-GUI-008. The session that read a report as an API
            // question assessed one and patched nothing.
            [
                'task' => 'Is this sitepackage written the way TYPO3 14 expects',
                'changeType' => 'audit',
            ],
        ] as $call) {
            $brief = Registry::call('typo3_task_guide', $call);

            self::assertSame(TaskGuide::PRODUCT_PREMISE, $brief->data['checklist'][0], $call['task']);
            self::assertStringContainsString(TaskGuide::PRODUCT_PREMISE, $brief->text, $call['task']);
        }
    }

    /**
     * R-GUI-009. The fifth recorded `REVIEW-03` run quoted `fluid-viewhelpers`
     * and `core-tests` as `typo3_hint_lookup` and never called it. The brief
     * had carried them, correctly and with no word about whose they were. The
     * report sent its reader to a tool that had not answered
     * (`scenarios/runs/REVIEW-03.json`, `D-SKL-009`) — `D-GUI-007`.
     */
    #[Requirement('R-GUI-009')]
    #[Decision('D-GUI-007')]
    #[Test]
    public function theHintsABriefCarriesNameTheLookupTheyCameFrom(): void
    {
        // The paths of that run, which is the call that showed the defect.
        $paths = [
            'typo3/sysext/core/Classes/Resource/ResourceFactory.php',
            'typo3/sysext/extbase/Classes/Service/ImageService.php',
            'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
            'typo3/sysext/backend/Classes/ViewHelpers/ThumbnailViewHelper.php',
            'typo3/sysext/core/Tests/Functional/Resource/ResourceFactoryTest.php',
        ];
        $task = 'Review a core patch that moves file source resolution from Extbase ImageService into '
            . 'ResourceFactory and adds a new public method';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15.0',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '15.0',
            'paths' => $paths,
            'limit' => 10,
        ]);

        self::assertNotSame([], $brief->data['hints']);
        self::assertStringContainsString(TaskGuide::HINTS_SOURCE, $brief->text);
        // These paths are the truncating case, so the brief says it stopped
        // short. Where it does not, it says the other thing —
        // aBriefThatCarriedEverythingDoesNotSendTheCallerBackForMore.
        self::assertStringContainsString(
            sprintf(TaskGuide::HINTS_TRUNCATED, TaskGuide::HINTS_PER_GROUP),
            $brief->text,
        );

        // Quoted whole rather than in summary, which is what makes the citation
        // right. A reader who follows it to typo3_hint_lookup finds the same
        // statements, not a longer version of them.
        $owned = [];
        foreach ($lookup->data['hints'] as $hint) {
            $owned[$hint['id']] = $hint;
        }
        foreach ($brief->data['hints'] as $hint) {
            self::assertArrayHasKey($hint['id'], $owned, $hint['id'] . ' is not one of the lookup\'s');
            self::assertSame($owned[$hint['id']], $hint, $hint['id'] . ' is not what the lookup answers');
        }

        // And a selection of them, which is the other half of what the sentence
        // states: the brief stops where the lookup goes on.
        self::assertLessThanOrEqual(TaskGuide::HINTS_PER_GROUP, count($brief->data['hints']));
        self::assertGreaterThan(count($brief->data['hints']), count($lookup->data['hints']));
    }

    /**
     * `R-GUI-009`, second half. The sentence is what the brief did, not a
     * standing disclaimer.
     *
     * A session read a filled `hints` array as the prescribed per-subsystem
     * call already answered, and it was not wrong about the content. The brief
     * carried every hint those paths reach and `omittedHints` was correctly
     * empty. What the payload did was claim otherwise in prose beside that
     * empty list — the failure `R-GUI-012` names from the other direction.
     */
    #[Requirement('R-GUI-009')]
    #[Test]
    public function aBriefThatCarriedEverythingDoesNotSendTheCallerBackForMore(): void
    {
        $paths = [
            'typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php',
            'typo3/sysext/extbase/Classes/Persistence/Generic/Query.php',
            'typo3/sysext/extbase/Classes/Persistence/QueryInterface.php',
        ];
        $task = 'Verify a bug report claiming an Extbase repository query cannot filter for IS NULL '
            . 'on a nullable date field';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '15',
            'paths' => $paths,
            'limit' => 20,
        ]);

        // Which of the two sentences is due is a property of this call and not
        // of the corpus, and the corpus moves. These paths were the complete
        // case until `extbase-persistence-internals` and `tca-datetime-storage`
        // arrived for them, and then they were the cut one. So this holds the
        // rule, that the sentence says what the brief did, rather than which of
        // the two this call happens to be.
        $carried = array_column($brief->data['hints'], 'id');
        $held = array_column($lookup->data['hints'], 'id');
        self::assertSame($held, array_merge($carried, array_column($brief->data['omittedHints'], 'id')));

        self::assertStringContainsString(TaskGuide::HINTS_SOURCE, $brief->text);
        if ($brief->data['omittedHints'] === []) {
            self::assertSame($held, $carried);
            self::assertStringContainsString(TaskGuide::HINTS_COMPLETE, $brief->text);
            self::assertStringNotContainsString(
                sprintf(TaskGuide::HINTS_TRUNCATED, TaskGuide::HINTS_PER_GROUP),
                $brief->text,
            );

            return;
        }

        self::assertStringContainsString(
            sprintf(TaskGuide::HINTS_TRUNCATED, TaskGuide::HINTS_PER_GROUP),
            $brief->text,
        );
        self::assertStringNotContainsString(TaskGuide::HINTS_COMPLETE, $brief->text);
    }

    /**
     * The complete branch, held on a call that has one hint to carry.
     *
     * The case above follows the corpus wherever it goes, so it stops
     * exercising `HINTS_COMPLETE` the moment a subject grows a second hint.
     * This one asks for a subject narrow enough that it cannot.
     */
    #[Test]
    public function aBriefWithNothingLeftOverSaysThatInstead(): void
    {
        $paths = ['typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php'];
        $task = 'Extbase query parser translating a comparison into SQL';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15',
            'paths' => $paths,
        ]);

        // Asserted rather than skipped over. A subject that grows past what a
        // brief carries is a real change to this case, and the next author
        // picks another call. A skip would report that as green.
        self::assertSame(
            [],
            $brief->data['omittedHints'],
            'this subject now holds more than a brief carries, so it no longer exercises HINTS_COMPLETE',
        );

        self::assertStringContainsString(TaskGuide::HINTS_SOURCE, $brief->text);
        self::assertStringContainsString(TaskGuide::HINTS_COMPLETE, $brief->text);
        self::assertStringNotContainsString(
            sprintf(TaskGuide::HINTS_TRUNCATED, TaskGuide::HINTS_PER_GROUP),
            $brief->text,
        );
    }

    /**
     * R-GUI-012. The same run read the four hints the brief carried and made no
     * separate call. It established `#[Autowire(lazy: true)]` for the patch's
     * new service dependency with a grep for three call sites in the checkout.
     * `dependency-injection` is the seventh hint the lookup holds for those
     * paths, and the brief had stated a count rather than the subjects
     * (`feedback/2026-08-03-144410`) — `D-ANS-050`.
     */
    #[Requirement('R-GUI-012')]
    #[Decision('D-ANS-050')]
    #[Decision('D-ANS-146')]
    #[Test]
    public function aBriefNamesTheHintsItLeftBehind(): void
    {
        $paths = [
            'typo3/sysext/core/Classes/Resource/ResourceFactory.php',
            'typo3/sysext/extbase/Classes/Service/ImageService.php',
            'typo3/sysext/fluid/Classes/ViewHelpers/ImageViewHelper.php',
            'typo3/sysext/backend/Classes/ViewHelpers/ThumbnailViewHelper.php',
            'typo3/sysext/core/Tests/Functional/Resource/ResourceFactoryTest.php',
        ];
        $task = 'Review a core patch that moves file source resolution from Extbase ImageService into '
            . 'ResourceFactory and adds a new public method';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15.0',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '15.0',
            'paths' => $paths,
            'limit' => HintLookup::MAX_HINTS,
        ]);

        $carried = array_column($brief->data['hints'], 'id');
        $left = array_column($brief->data['omittedHints'], 'id');

        // The measurement behind D-GUI-007: four carried, three left, and the
        // one the report went to the checkout for is among the three.
        self::assertSame(['fal-reading', 'fal-processing', 'dependency-injection'], $left);
        // What the pointer stands for is what the lookup would answer, so the
        // two halves are that answer and nothing else.
        self::assertSame(array_column($lookup->data['hints'], 'id'), array_merge($carried, $left));

        // Named rather than quoted. The ids are in the copy a reader looks at,
        // and each record is what the lookup takes back as an id.
        self::assertStringContainsString(sprintf(TaskGuide::HINTS_OMITTED, implode(', ', $left)), $brief->text);
        // And above the brief rather than under its hints. A session read the
        // four blocks, moved on to the code and made about a hundred further
        // calls without the id coming back (`D-ANS-146`).
        self::assertLessThan(
            (int) strpos($brief->text, 'Task: '),
            (int) strpos($brief->text, 'Owed after this brief'),
            'the hints a brief left are named below what it did carry',
        );
        foreach ($brief->data['omittedHints'] as $entry) {
            self::assertNotSame('', $entry['title']);
            self::assertNotSame('', $entry['category']);
            self::assertArrayNotHasKey('hints', $entry);
        }
    }

    /**
     * D-ANS-097 and R-ANS-033, the core direction: `feedback/2026-08-24-100427`
     * re-run. The brief spent two of its four slots on a build in somebody
     * else's repository. `extension-asset-build` and
     * `project-build-and-scripts` answer a query about a build in the words any
     * build has. The two core hints these paths name sat in `omittedHints`.
     */
    #[Requirement('R-ANS-033')]
    #[Decision('D-ANS-097')]
    #[Test]
    public function aCoreBriefSpendsItsSlotsOnTheCoreHints(): void
    {
        $paths = [
            'Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts',
            'typo3/sysext/form/Resources/Public/JavaScript/backend/form-wizard/steps/settings-step.js',
        ];
        $task = 'Review a bugfix patch for the TYPO3 form manager new-form wizard: the settings step must '
            . 'pick the template matching the Blank/Predefined mode chosen in step 1';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'audit',
            'targetVersion' => '15',
            'paths' => $paths,
        ]);

        self::assertSame(['core', 'core'], array_column($brief->data['scopes'], 'scope'));
        foreach ($brief->data['hints'] as $hint) {
            self::assertContains(
                $hint['scope'],
                [null, Scope::Core->value, Scope::Any->value],
                $hint['id'] . ' is declared for another repository and took a slot in a core brief',
            );
        }
        self::assertContains('backend-typescript', array_column($brief->data['hints'], 'id'));

        // Demoted rather than dropped: each is one call away by id, which is
        // the whole of what the tier may cost.
        $left = array_column($brief->data['omittedHints'], 'id');
        self::assertContains('extension-asset-build', $left);
        self::assertContains('project-build-and-scripts', $left);
    }

    /**
     * The mirror, measured the same day and worse. `core-tests` ranked first on
     * a functional-test task in a distributed extension, above
     * `project-extension-tests`, which is the hint that binds there
     * (`feedback/2026-08-24-140340`).
     */
    #[Requirement('R-ANS-033')]
    #[Decision('D-ANS-097')]
    #[Test]
    public function anExtensionBriefSpendsItsSlotsOnTheExtensionHints(): void
    {
        $paths = [
            'packages/my_extension/Classes/Domain/Repository/ThingRepository.php',
            'packages/my_extension/Tests/Functional/Domain/Repository/ThingRepositoryTest.php',
        ];
        $task = 'Add functional test coverage for the extension';

        $brief = Registry::call('typo3_task_guide', [
            'task' => $task,
            'changeType' => 'test',
            'targetVersion' => '14',
            'paths' => $paths,
        ]);
        $lookup = Registry::call('typo3_hint_lookup', [
            'task' => $task,
            'targetVersion' => '14',
            'paths' => $paths,
            'limit' => HintLookup::MAX_HINTS,
        ]);

        self::assertSame(['extension', 'extension'], array_column($brief->data['scopes'], 'scope'));
        $carried = array_column($brief->data['hints'], 'id');
        foreach ($brief->data['hints'] as $hint) {
            self::assertContains(
                $hint['scope'],
                [null, Scope::Any->value, Scope::Project->value, Scope::Extension->value],
                $hint['id'] . ' is declared for the core and took a slot in an extension brief',
            );
        }
        self::assertContains('project-extension-tests', $carried);

        $left = array_column($brief->data['omittedHints'], 'id');
        self::assertContains('core-tests', $left);

        // The tier moves a hint and leaves the answer whole. What the brief
        // carries and what it names between them are what the lookup holds for
        // these paths, in another order.
        $held = array_column($lookup->data['hints'], 'id');
        sort($held);
        $reachable = array_merge($carried, $left);
        sort($reachable);
        self::assertSame($held, $reachable);
    }

    #[Requirement('R-PRJ-004')]
    #[Decision('D-KNW-032')]
    #[Test]
    public function upgradingAnInstallationIsAnsweredAsAnOrderOfOperations(): void
    {
        // The question a site maintainer asks first, "what do I do, in which
        // order", used to get how to author a deprecation. That is the same
        // subject seen from the core's side and useless here — `D-KNW-032`.
        $result = Registry::call('typo3_task_guide', ['task' => 'upgrade this composer site project to TYPO3 v14']);

        self::assertContains('installation-upgrade', array_column($result->data['intents'], 'id'));

        $group = array_values(array_filter(
            $result->data['hints'],
            static fn(array $hint): bool => $hint['id'] === 'installation-upgrade',
        ));
        self::assertCount(1, $group, 'the order of operations is part of the answer');

        // The steps are only worth anything in this order: the schema before
        // the wizards that declare it as their prerequisite, the caches last.
        $statements = implode("\n", array_column($group[0]['hints'], 'text'));
        $order = array_map(
            static fn(string $command): int => (int) strpos($statements, $command),
            ['extension:setup', 'upgrade:run', 'cache:flush'],
        );
        self::assertNotContains(0, $order, 'every step of the sequence is named');
        self::assertLessThan($order[1], $order[0], 'the schema is applied before the wizards run');
        self::assertLessThan($order[2], $order[1], 'the caches are flushed after the wizards');
    }

    #[Decision('D-KNW-046')]
    #[Test]
    public function anUnattendedInstallIsAnsweredWithWhatTheCommandRefuses(): void
    {
        // The script came from the command's own --help, which names neither
        // the connection type the option takes nor the driver it persists. It
        // says nothing about a second run. Four failed runs — `D-KNW-046`.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'install TYPO3 unattended from a shell script so that "ddev start" sets the instance up on its own',
        ]);

        self::assertContains('installation-setup', array_column($result->data['intents'], 'id'));
        self::assertContains('installation-setup', array_column($result->data['hints'], 'id'));

        $statements = self::statementsOf('installation-setup');
        self::assertStringContainsString('pdo_sqlite', $statements, 'the driver name the option does not take');
        self::assertStringContainsString(
            'config/system/settings.php',
            $statements,
            'what an idempotent installer guards on, since the command guards on the schema',
        );

        // Which of the two refusals a run met, and what --force moves. A number
        // is what tells them apart in the output a script sees, and only the
        // settings half gets a rewrite. `feedback/2026-08-18-070515` reports
        // the three as what put its sequence in order before any of them fired.
        self::assertStringContainsString('exception 1669747685', $statements);
        self::assertStringContainsString('exception 1669747200', $statements);
        self::assertStringContainsString('--force overwrites the settings files and never the schema', $statements);

        // The site half is the one that moved. Before the distribution option
        // existed, --create-site wrote the site configuration whatever else the
        // installation had. Since it does, a required package that ships an
        // initialisation file silences both options.
        $onThirteen = implode("\n", array_column(Hints::byId('installation-setup', 13)['hints'], 'text'));
        self::assertStringContainsString('--create-site <url>', $onThirteen);
        self::assertStringNotContainsString('--distribution', $onThirteen);

        $onFourteen = implode("\n", array_column(Hints::byId('installation-setup', 14)['hints'], 'text'));
        self::assertStringContainsString('--distribution', $onFourteen);
        self::assertStringContainsString(
            'no way to be told its own URL',
            $onFourteen,
            'the consequence the two inert options have',
        );

        // Which package silences them is a test the caller runs rather than a
        // list it inherits, and `SetupService::getAvailableDistributions()` has
        // no second predicate. A package ships one of the two files or it is
        // not a distribution, in `.checkouts/14.3` as in `.checkouts/main`.
        self::assertStringContainsString('Initialisation/data.xml or Initialisation/data.t3d', $onFourteen);
        self::assertStringContainsString('is the whole test for being one', $onFourteen);
    }

    #[Decision('D-KNW-094')]
    #[Test]
    public function theLineThatCarriesAVariableIntoTheContainerIsAnswered(): void
    {
        // Two failed round trips on syntax rather than on TYPO3 —
        // `feedback/2026-08-18-070423` typed `-e`, then `--raw=false` with the
        // whole line in one string. Measured in an `E-SITE` since. The prefix
        // does survive a plain `ddev exec`, and `--raw` switches on its
        // presence rather than on its value — `D-KNW-094`.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'boot the local DDEV development installation of an extension repository, '
                . 'running typo3 setup unattended inside the web container',
        ]);
        self::assertContains('installation-setup', array_column($result->data['hints'], 'id'));

        $statements = self::statementsOf('installation-setup');
        self::assertStringContainsString('ddev exec TYPO3_DB_DRIVER=mysqli', $statements, 'the form that works');
        self::assertStringContainsString('There is no --env option', $statements, 'the flag that was reached for');
        self::assertStringContainsString('--raw, --raw=true and --raw=false behave alike', $statements);
        self::assertStringContainsString(
            "ddev exec bash -c '",
            $statements,
            'what carries a value the join would quote',
        );

        // The other family, in the file the caller is reading anyway, and the
        // hint that owns it.
        self::assertStringContainsString('typo3DatabaseHost', $statements);
        self::assertStringContainsString('project-extension-tests', $statements);
    }

    /**
     * Read in `.checkouts/` on 2026-08-24. `SetupService::createSite()` writes
     * `config/sites/<identifier>/setup.typoscript` and inserts no
     * `sys_template` on `14.3` and on `main`, and inserts the row and writes no
     * file on `12.4` and `13.4`. `SysTemplateTreeBuilder` decides which of the
     * two wins. The sets hang below the site's include node, and
     * `IncludeTreeTraverser` reads a node's children before the node's own
     * lines. Only a `SysTemplateInclude` with the clear flag resets the AST —
     * `D-KNW-116`.
     */
    #[Decision('D-KNW-116')]
    #[Test]
    public function whatCreateSiteLeavesRenderingThePageIsAnsweredPerMajor(): void
    {
        $on = static fn(int $major): string => implode("\n", array_column(
            (array) Hints::byId('installation-setup', $major)['hints'],
            'text',
        ));

        foreach ([14, 15] as $major) {
            self::assertStringContainsString('config/sites/<identifier>/setup.typoscript', $on($major));
            self::assertStringContainsString('applied after the sets it depends on', $on($major));
            self::assertStringNotContainsString('sys_template', $on($major));
        }

        foreach ([12, 13] as $major) {
            self::assertStringContainsString('root sys_template row', $on($major));
            self::assertStringNotContainsString('setup.typoscript', $on($major));
        }

        // The row is the harder half, and it is only a collision where sets
        // exist at all. That is one of the two majors that carry it.
        self::assertStringContainsString('resets the AST', $on(13));
        self::assertStringNotContainsString('resets the AST', $on(12));

        // What the caller pays for is that none of it fails. So the symptom
        // stands with the marker that tells an overridden set from one that
        // never loaded.
        self::assertStringContainsString('answers HTTP 200', $on(14));
        self::assertStringContainsString('page.5 = TEXT', $on(14));

        // A session whose set does not render arrives on the symptom words,
        // which the probe answers with the set hint rather than the install one.
        self::assertStringContainsString('installation-setup', self::statementsOf('site-sets'));
    }

    /**
     * Read in `.checkouts/` on 2026-08-25. `SetupService::createSite()` fills
     * the site's `dependencies` on `14.3` and `main` and writes the
     * `sys_template` row with the static includes on `12.4` and `13.4`.
     * `ImportSiteConfigurationsOnPackageInitialization` returns before it reads
     * `Initialisation/Site/` unless the content import left an `Import` behind,
     * which `12.4`'s `InstallUtility` does not require.
     * `PackageManager::getPackageKeyFromManifest()` falls back to the directory
     * name up to `13.4` and throws 1348146451 from `14.3` on — `D-KNW-118`.
     */
    #[Decision('D-KNW-118')]
    #[Test]
    public function whereADevelopmentInstallationGetsItsPageObjectIsStated(): void
    {
        $on = static fn(int $major): string => implode("\n", array_column(
            (array) Hints::byId('development-installation-page-object', $major)['hints'],
            'text',
        ));

        // Something is rendering that page already, so the first step is the
        // one `D-KNW-116` owns rather than a second object beside the first.
        self::assertStringContainsString('installation-setup', $on(14));

        // Where the site takes the replacement up is one key, and whether that
        // key already exists is what the majors differ in.
        foreach ([13, 14, 15] as $major) {
            self::assertStringContainsString('under dependencies', $on($major));
        }
        self::assertStringContainsString('typo3/fluid-styled-content-css', $on(14));
        self::assertStringNotContainsString('typo3/fluid-styled-content-css', $on(13));
        self::assertStringContainsString('no dependencies key at all', $on(13));

        // The route that looks like it saves that edit and imports nothing.
        self::assertStringContainsString('Initialisation/Site/', $on(13));
        self::assertStringNotContainsString('Initialisation/Site/', $on(12));

        // What names the extension that ships the demo set, which the report
        // this serves had taken for the directory's own work.
        self::assertStringContainsString('extra.typo3/cms.extension-key', $on(14));
        self::assertStringContainsString('1348146451', $on(14));
        self::assertStringNotContainsString('1348146451', $on(13));
        self::assertStringContainsString("directory's own name", $on(13));

        // Two packaging paths, and the attribute reaches one of them.
        self::assertStringContainsString('export-ignore', $on(14));
        self::assertStringContainsString('extension-ter-release', $on(14));

        // A session whose set renders nothing arrives on the set hint, and one
        // at the seeding step of the workflow arrives on the seeding hint.
        self::assertStringContainsString(
            'development-installation-page-object',
            self::statementsOf('site-sets', 'fresh-instance-seeding'),
        );
    }

    #[Requirement('R-ANS-011')]
    #[Test]
    public function aRepeatableContentElementIsRoutedThroughWhatItOwns(): void
    {
        // A session designed a hero carousel out of generic record references.
        // That is possible, and what an element ends up with when nobody asked
        // who creates, orders, translates and hides a slide. The decision has
        // to be in the answer before the registration is. The task that asks
        // for it does not have to say "content element" to get there.
        $result = Registry::call('typo3_task_guide', [
            'task' => 'Add a hero carousel content element whose slides editors can create, order, translate and hide inside the element',
            'paths' => ['packages/printworks_sitepackage/'],
        ]);

        self::assertContains('content-element', array_column($result->data['intents'], 'id'));

        $hint = array_values(array_filter(
            $result->data['hints'],
            static fn(array $entry): bool => $entry['id'] === 'content-elements',
        ));
        self::assertCount(1, $hint);
        $ownership = self::statementsOf('content-element-shape');
        self::assertStringContainsString('type=inline', $ownership);
        self::assertStringContainsString('reuse is a requirement somebody stated', $ownership);

        // The words a first question arrives with reach it too, and stay a
        // conditional match. Nothing in them says the work is a content
        // element.
        $vague = Registry::call('typo3_task_guide', ['task' => 'Add a Hero Carousel that rotates different elements']);
        self::assertNotSame(
            [],
            array_intersect(
                ['content-elements', 'content-element-shape'],
                array_column($vague->data['hints'], 'id'),
            ),
            'a carousel is the subject even where nothing in the wording says content element',
        );
    }

    /**
     * The reported miss is a testimonials element built on TCA and a
     * DatabaseQueryProcessor, in an extension whose other plugins are Extbase,
     * with Extbase never considered. The fork stands on the extbase and the
     * frontend-records hint, and both open on a word this task has no reason to
     * use. The case asserts that here, because it is what the checklist
     * delivers instead of. The words come off `.checkouts/14.3`.
     * `registerPlugin()` hands `addPlugin()` a `SelectItem` for the CType
     * column, the same column `addRecordType()` writes, and `configurePlugin()`
     * generates `tt_content.<signature> =< lib.contentElement` with `20 =
     * EXTBASEPLUGIN`. So the fork is not element or plugin — `D-ANS-039`.
     */
    #[Requirement('R-ANS-016')]
    #[Decision('D-ANS-039')]
    #[Test]
    public function aContentElementTaskIsOfferedTheExtbaseForkWithoutNamingIt(): void
    {
        $task = 'new content element for testimonials with a repeatable list of entries, TCA and Fluid rendering';

        $reached = array_column(Hints::find([], $task, 6)['matchedHints'], 'id');
        self::assertNotContains('extbase', $reached, 'the fork is filed under a branch this task did not name');
        self::assertNotContains('frontend-records', $reached);

        $result = Registry::call('typo3_task_guide', ['task' => $task]);
        self::assertContains('content-element', array_column($result->data['intents'], 'id'));

        $checklist = implode("\n", $result->data['checklist']);
        self::assertStringContainsString('needs Extbase', $checklist);
        self::assertStringContainsString('same CType selector', $checklist);
        self::assertStringContainsString('no model, no repository and no controller', $checklist);
        // The other half: the answer reports what the extension already does,
        // and that is evidence about the architecture, not only about where a
        // template is.
        self::assertStringContainsString('kind of element or plugin', $checklist);

        // A fork is only worth anything while the choice is still free, so it
        // arrives before the registration and the template do.
        self::assertLessThan(
            (int) strpos($checklist, 'Register this element in a file of its own'),
            (int) strpos($checklist, 'needs Extbase'),
        );

        // Where the rest of the fork is, in the checklist rather than in the
        // tool list. nextTools keeps one entry per tool, and this is not the
        // only intent a content-element task matches that names the lookup.
        self::assertStringContainsString('id=extbase', $checklist);
        self::assertStringContainsString('id=frontend-records', $checklist);

        $when = array_column($result->data['nextTools'], 'when', 'tool');
        self::assertStringContainsString('architecture this extension already has', $when['typo3_extension_describe'] ?? '');
    }

    /**
     * The corpus registered the preview template and stopped there. So a
     * session arrived at a template with one variable in it and no statement
     * about what that variable is. Both halves come off the checkouts.
     * FluidBasedContentPreviewRenderer assigns the row's columns and record on
     * 13.4 and record alone on 14.3. The TCA type of a field decides what a
     * field off it resolves to. That is why the hint names the types that come
     * back as records rather than a single rule for "a relation" — `D-KNW-020`.
     */
    #[Requirement('R-KNW-041')]
    #[Decision('D-KNW-020')]
    #[Test]
    public function aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo(): void
    {
        $onThirteen = implode("\n", array_column(
            Hints::byId('content-element-preview', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('{pi_flexform_transformed}', $onThirteen);
        self::assertStringNotContainsString('no longer variables of their own', $onThirteen);

        $onFourteen = implode("\n", array_column(
            Hints::byId('content-element-preview', 14)['hints'],
            'text',
        )) . "\n" . implode("\n", array_column(
            Hints::byId('preview-record-variable', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('handed one variable, record', $onFourteen);
        self::assertStringNotContainsString('{pi_flexform_transformed}', $onFourteen);

        // The mechanism the session behind the report could not find, and the
        // field it renders an empty spot over. What the record type's schema
        // does not declare is not on the record, and the path resolves to null.
        self::assertStringContainsString('PSR-11 container', $onFourteen);
        self::assertStringContainsString('has() and get()', $onFourteen);
        self::assertStringContainsString('{record.systemProperties.disabled}', $onFourteen);

        // What a relation comes back as, and what reads like one and is not.
        self::assertStringContainsString('lazy collection f:for iterates', $onFourteen);
        self::assertStringContainsString(
            'type=select with a relation, group, inline, category and file',
            $onFourteen,
        );
        self::assertStringContainsString('renderType is selectSingle', $onFourteen);

        // The subject is reachable from the question it was missing for. The
        // reporting session's own spelling, «content-element», reaches it as
        // little as before — that is D-ANS-022 and not this statement's to fix.
        $reached = Hints::find(
            [],
            'Record API field access in a backend content element preview Fluid template: '
            . 'what a relational select field resolves to',
            6,
        );
        self::assertSame('content-elements', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * The check of a typed f:argument runs while the template renders and
     * nothing before it. So a class name guessed wrong costs a failed page
     * rather than a failed build. That is why the corpus names the classes
     * instead of leaves them to an exception. Both halves come off the
     * checkouts. RecordFieldTransformer decides what a column becomes, and
     * StrictArgumentProcessor, which the fluid extension aliases the argument
     * processor to, accepts an interface name for it — `D-KNW-090`.
     */
    #[Decision('D-KNW-090')]
    #[Test]
    public function theRecordAndItsTransformedColumnsAreNamedAsPhpTypes(): void
    {
        $onFourteen = implode("\n", array_column(
            Hints::byId('preview-record-variable', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('TYPO3\CMS\Core\Domain\RecordInterface', $onFourteen);
        self::assertStringContainsString('TYPO3\CMS\Core\LinkHandling\TypolinkParameter', $onFourteen);
        self::assertStringContainsString('TYPO3\CMS\Core\Resource\FileReference', $onFourteen);
        self::assertStringContainsString('TYPO3\CMS\Core\Country\Country', $onFourteen);

        // The record is the same object on both sides, which is what lets one
        // statement answer a frontend partial and a backend preview at once.
        self::assertStringContainsString('createResolvedRecordFromDatabaseRow', $onFourteen);
        self::assertStringContainsString('as in a frontend partial', $onFourteen);

        // What the session behind the report declared, and why the refusal
        // came.
        self::assertStringContainsString('f:argument', $onFourteen);
        self::assertStringContainsString('array is refused', $onFourteen);

        // The ViewHelper is Fluid 5's and the country transformation is not on
        // the older branch, while the classes a column arrives as are.
        $onThirteen = implode("\n", array_column(
            Hints::byId('preview-record-variable', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('TYPO3\CMS\Core\LinkHandling\TypolinkParameter', $onThirteen);
        self::assertStringNotContainsString('f:argument', $onThirteen);
        self::assertStringNotContainsString('type=country', $onThirteen);

        // The subject is reachable from the exception the miss arrives as.
        $reached = Hints::find(
            [],
            'the argument record is registered with type array, but the provided value is of '
            . 'type TypolinkParameter',
            6,
        );
        self::assertSame('preview-record-variable', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * The reported miss is a template that repeats what is already on the page.
     * GridColumnItem::getPreview() renders the header before it dispatches the
     * event FluidBasedContentPreviewRenderer listens on, and that listener sets
     * the content alone. The case asserts the header parts by field, because a
     * session that hears only that "the header" exists repeats subheader or
     * date instead. Both majors draw the same four, so the statement carries no
     * version binding and has to reach a caller on either — `D-KNW-021`.
     */
    #[Requirement('R-KNW-042')]
    #[Decision('D-KNW-021')]
    #[Test]
    #[DataProvider('theMajorsThePreviewAnswerIsBoundFor')]
    public function aPreviewAnswerSaysWhatTheDefaultRendererAlreadyDraws(int $major): void
    {
        $texts = implode("\n", array_column(
            Hints::byId('content-element-preview', $major)['hints'],
            'text',
        ));

        self::assertStringContainsString('replaces the content half', $texts);
        self::assertStringContainsString('where header_layout hides the header', $texts);
        self::assertStringContainsString('the date field with its label', $texts);
        self::assertStringContainsString("record type's label field linked to the edit form", $texts);
        self::assertStringContainsString('and subheader', $texts);
        self::assertStringContainsString('space_before_class', $texts);

        // The words the reporting session arrived with. The probe D-KNW-015
        // recorded the gap on reached nothing at all. «backend preview» was in
        // none of the hint's own vocabulary until this statement landed.
        $reached = Hints::find(
            [],
            'backend preview element header already rendered by the default renderer',
            6,
        );
        self::assertSame('content-element-preview', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * Both majors draw the same four, so the statement carries no version
     * binding and has to reach a caller on either.
     *
     * @return array<string, array{0: int}>
     */
    public static function theMajorsThePreviewAnswerIsBoundFor(): array
    {
        return ['on 13' => [13], 'on 14' => [14]];
    }

    /**
     * Registration, what the template gets and what the header already draws
     * all stand in the hint. A preview that reads "Testimonials element"
     * satisfies every one of them. What the statement names comes off the ten
     * previews in theme_camino's ContentPreviews/ on 14.3 — `D-KNW-037` has the
     * read.
     *
     * The case asserts the field kinds one at a time, because "show a summary"
     * changes no line of the template somebody writes. It holds on every
     * covered major. What a preview owes the editor is not a property of a
     * major, and how to reach a field carries the bound instead.
     */
    #[Requirement('R-KNW-050')]
    #[Decision('D-KNW-037')]
    #[Test]
    public function aPreviewAnswerNamesTheFieldsThePreviewDrawsFrom(): void
    {
        foreach (Versions::majors() as $major) {
            $texts = implode("\n", array_column(
                Hints::byId('content-element-preview', $major)['hints'],
                'text',
            ));

            self::assertStringContainsString("the element's own payload", $texts);
            self::assertStringContainsString('without opening either', $texts);

            self::assertStringContainsString('the text columns stripped of their markup', $texts);
            self::assertStringContainsString('every file relation as a thumbnail', $texts);
            self::assertStringContainsString('a link field as its label', $texts);
            self::assertStringContainsString('a select as the translated label', $texts);
            self::assertStringContainsString('each child of an inline relation', $texts);

            self::assertStringContainsString('label naming the element', $texts);
        }

        $reached = Hints::find(
            [],
            'what a backend preview of a content element should show the editor',
            6,
        );
        self::assertSame('content-element-preview', array_column($reached['matchedHints'], 'id')[0] ?? '');
    }

    /**
     * The reported bug was a one-line TCA override that had worked for years.
     * An extension appended to a core palette with `.=`. On the next major the
     * field and its line break left the backend form with nothing in the log —
     * `D-KNW-103`. Both halves come off the checkouts. `addFieldsToPalette()`
     * is byte for byte the same on all four, and it is the half that survives a
     * new shape. So the statement of it carries no bound. What the new shape
     * did has a bound, because that is what a caller on an upgrade arrives with
     * — `D-KNW-104`.
     */
    #[Decision('D-KNW-104')]
    #[Test]
    public function addingToACorePaletteIsStatedAsTheCall(): void
    {
        foreach (Versions::majors() as $major) {
            $texts = implode("\n", array_column(
                Hints::byId('tca-core-palette', $major)['hints'],
                'text',
            ));

            // The rule, and the reason it is not a matter of taste.
            self::assertStringContainsString('addFieldsToPalette', $texts);
            self::assertStringContainsString("core's string and not the extension's", $texts);
            self::assertStringContainsString('shape core happened to write', $texts);

            // Why nothing reports the loss, which is what left the reporting
            // session with a form to read rather than an error to search for.
            self::assertStringContainsString('naming no column', $texts);
            self::assertStringContainsString('without logging', $texts);

            // What the call does that the concatenation does not, item by item.
            // A session that hears only "use the API" writes the second call
            // twice and the line break once.
            self::assertStringContainsString('trims the trailing comma', $texts);
            self::assertStringContainsString('a second call adds the field once', $texts);
            self::assertStringContainsString('--linebreak-- is exempt', $texts);
            self::assertStringContainsString('created rather than refused', $texts);
        }

        // The new shape is the bound half, and the version question keeps the
        // route D-KNW-103 measured rather than a repeat here.
        $onFourteen = implode("\n", array_column(
            Hints::byId('tca-core-palette', 14)['hints'],
            'text',
        ));
        self::assertStringContainsString('short form reference', $onFourteen);
        self::assertStringContainsString('typo3_changelog_lookup', $onFourteen);
        self::assertStringNotContainsString('works on this branch', $onFourteen);

        $onThirteen = implode("\n", array_column(
            Hints::byId('tca-core-palette', 13)['hints'],
            'text',
        ));
        self::assertStringContainsString('works on this branch', $onThirteen);
        self::assertStringNotContainsString('short form reference', $onThirteen);

        // The four probes D-KNW-103 recorded the gap on, of which the last
        // reached nothing at all, and the symptom the session actually had.
        $reached = static fn(string $query): array => array_column(
            Hints::find([], $query, 6)['matchedHints'],
            'id',
        );
        foreach ([
            'add a field to a core tt_content palette',
            'addFieldsToPalette tt_content frames',
            'appending to core TCA showitem string breaks',
            'extend core palette showitem with a linebreak',
        ] as $query) {
            self::assertContains('tca-core-palette', $reached($query), $query);
        }
        self::assertSame(
            'tca-core-palette',
            $reached('my field is missing from the backend form after a TCA override')[0] ?? '',
        );

        // And the boundary the statement had to land inside: the palette words
        // are a third TCA subject, not a claim on either neighbour.
        self::assertContains('content-elements', $reached('register a content element'));
        self::assertContains('tca-formengine', $reached('validate a form field in the backend'));
    }

    #[Test]
    public function withoutAnySignalTheDomainIsPhp(): void
    {
        self::assertSame([Domains::PHP], Domains::detect([], 'do something'));
    }

    #[Test]
    public function anXlfOnlyChangeStaysAnXlfChange(): void
    {
        // A label patch that touches no PHP must not pull the PHP suites in.
        // Unit and functional runs cost minutes and cannot fail on an XLF edit.
        $domains = Domains::detect(['typo3/sysext/backend/Resources/Private/Language/locallang.xlf'], '');

        self::assertSame([Domains::XLIFF], $domains);
        self::assertSame(
            ['checkIntegrityXliff', 'normalizeXliff'],
            array_column(TestSuiteHints::find(null, $domains), 'suite')
        );
    }

    #[Test]
    public function aFluidTemplateIsItsOwnDomain(): void
    {
        $domains = Domains::detect(['typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html'], '');

        self::assertContains(Domains::FLUID, $domains);
        self::assertNotContains(Domains::TYPESCRIPT, $domains);
        self::assertNotContains(Domains::CSS, $domains);
    }
}
