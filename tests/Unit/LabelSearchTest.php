<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Installation\Typo3Cli;
use TYPO3\DevCompanion\Search\LabelSearch;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tests\Support\TemporaryInstallation;
use TYPO3\DevCompanion\Tool\Registry;

/**
 * What a label query means, and what an empty answer to one means.
 *
 * Both were wrong in the same call: "save document" could never match, because
 * the console searched for that string rather than for those words, and the
 * empty result it printed came back as an unreachable installation.
 */
final class LabelSearchTest extends TestCase
{
    use TemporaryInstallation;

    /** The checkout the current test's console and label files sit in. */
    private string $installationRoot = '';

    #[After]
    public function forgetTheInstance(): void
    {
        putenv(Typo3Cli::CONSOLE_VARIABLE);
        Instance::discoverFrom(null);
        Typo3Cli::forget();
    }

    #[Test]
    public function aQueryOfSeveralWordsAsksForAllOfThemAtOnce(): void
    {
        // One console call, not one per word: a call boots TYPO3.
        self::assertSame('--regex=/(save|document)/i', LabelSearch::consoleOption(LabelSearch::terms('save document')));
    }

    #[Test]
    public function aWordThatLooksLikeAPatternIsStillAWord(): void
    {
        self::assertSame('--regex=/(labels\.title)/i', LabelSearch::consoleOption(LabelSearch::terms('labels.title')));
    }

    #[Requirement('R-ANS-004')]
    #[Test]
    public function aLabelAnswersOnlyWhenItCarriesEveryWord(): void
    {
        $labels = [
            ['key' => 'labels.save_document', 'source' => 'Save document'],
            ['key' => 'labels.save', 'source' => 'Save'],
            ['key' => 'labels.document', 'source' => 'Document'],
        ];

        $matching = LabelSearch::carryingEvery($labels, LabelSearch::terms('save document'));

        self::assertSame(['labels.save_document'], array_column($matching, 'key'));
    }

    #[Requirement('R-ANS-004')]
    #[Test]
    public function theWordsMayComeInAnyOrderAndAnyCase(): void
    {
        $labels = [['key' => 'labels.save_document', 'source' => 'Save document']];

        self::assertCount(1, LabelSearch::carryingEvery($labels, LabelSearch::terms('Document SAVE')));
    }

    #[Test]
    public function aWordInsideATransUnitIdCountsWithoutABoundary(): void
    {
        // An underscore is a word character, so anchoring the match would drop
        // exactly the ids a caller searches by.
        $labels = [['key' => 'labels.save_document', 'source' => 'Speichern']];

        self::assertCount(1, LabelSearch::carryingEvery($labels, LabelSearch::terms('document')));
    }

    #[Test]
    public function anEmptyResultSaysHowFarEachWordReachesOnItsOwn(): void
    {
        $labels = [
            ['key' => 'labels.save', 'source' => 'Save'],
            ['key' => 'labels.save_all', 'source' => 'Save all'],
            ['key' => 'labels.document', 'source' => 'Document'],
        ];

        self::assertSame(
            [['term' => 'save', 'matchCount' => 2], ['term' => 'document', 'matchCount' => 1]],
            LabelSearch::perTermCounts($labels, LabelSearch::terms('save document'))
        );
    }

    /**
     * What the per-term counts cannot say: which words have to go. Two of these
     * five had to, and the smallest reach — `yaml`, carried by both entries the
     * query was after — is the one to keep rather than the one to drop —
     * `D-ANS-016`.
     */
    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-016')]
    #[Test]
    public function anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach(): void
    {
        $entries = [
            ['key' => 'Deprecation-109412-FormYamlConfigurationRegistration', 'source' => 'Form Yaml Configuration Registration'],
            ['key' => 'Feature-84203-UnifyFormSetupYAMLLoading', 'source' => 'Unify Form Setup YAML Loading'],
            ['key' => 'Breaking-101392-GetIdentifierRemoved', 'source' => 'Get Identifier Removed'],
        ];

        // Both of them, because the one a tie-break picks first here is the
        // YAML loading feature rather than the deprecation being looked for.
        self::assertSame(
            [
                ['terms' => ['form', 'yaml', 'registration'], 'matchCount' => 1],
                ['terms' => ['form', 'set', 'yaml'], 'matchCount' => 1],
            ],
            LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('form set yaml registration deprecated'))
        );
    }

    /**
     * Every largest reaching subset is offered and the narrower one is first,
     * because the caller reads the list from the top and the widest of them is
     * the one that answers least — `D-ANS-016`.
     */
    #[Decision('D-ANS-016')]
    #[Test]
    public function theSubsetThatNarrowsBestComesFirst(): void
    {
        $entries = [
            ['key' => 'Feature-1-BackendModuleModules', 'source' => 'Backend Module Modules'],
            ['key' => 'Feature-2-BackendModuleModulesAgain', 'source' => 'Backend Module Modules Again'],
            ['key' => 'Feature-3-BackendModuleRegistration', 'source' => 'Backend Module Registration'],
        ];

        self::assertSame(
            [
                ['terms' => ['backend', 'module', 'registration'], 'matchCount' => 1],
                ['terms' => ['backend', 'module', 'modules'], 'matchCount' => 2],
            ],
            LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('backend module registration modules'))
        );
    }

    #[Test]
    public function whereNoTwoWordsMeetInOneEntryThereIsNoSubsetToOffer(): void
    {
        // A single word is what the per-term counts already say, so this adds
        // nothing and says so rather than repeating them.
        $entries = [
            ['key' => 'Feature-1-Alpha', 'source' => 'Alpha'],
            ['key' => 'Feature-2-Beta', 'source' => 'Beta'],
        ];

        self::assertSame([], LabelSearch::largestReachingSubsets($entries, LabelSearch::terms('alpha beta')));
    }

    #[Requirement('R-ANS-005')]
    #[Test]
    public function aConsoleThatFoundNothingIsAnAnswer(): void
    {
        // The console prints "[WARNING] No language resource files found." and
        // exits successfully. Reading that as an unreachable installation sent
        // the caller to typo3_server_scope instead of to a narrower query.
        $this->consoleThatPrints("Labels in active extensions\n===\n\n [WARNING] No language resource files found.\n");

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('installation', $result->data['answeredBy']);
        self::assertSame(0, $result->data['matchCount']);
        self::assertStringNotContainsString('could not be asked', $result->text);
        self::assertStringContainsString('No label in', $result->text);
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function aConsoleThatExitsWellAndSaysNothingUsableEstablishesNothing(): void
    {
        // The payload shares stdout with the title the same command prints
        // ahead of it, and the decoder starts at the first brace or bracket.
        // What else can land on that stream is not established — the two
        // obvious candidates were checked and neither reaches it. That is the
        // point rather than a gap in it: exit 0 says nothing about the stream,
        // so the tool may not read it as an installation that answered. Here
        // the payload is intact and the decoder misses it, and read as "none"
        // that is the one wrong answer nothing distinguishes from a right one.
        $this->consoleThatPrints(
            "[note] something reached this stream ahead of the payload\n"
            . "\nLabels in active extensions\n===========================\n\n"
            . (string) json_encode(['items' => [[
                'resource' => 'EXT:core/Resources/Private/Language/locallang.xlf',
                'labels' => [['domain' => 'core.messages', 'reference' => 'labels.save_document', 'label' => 'Save document']],
            ]]], JSON_THROW_ON_ERROR)
        );
        $this->labelFile('Resources/Private/Language/locallang.xlf', ['labels.save_document' => 'Save document']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('packages', $result->data['answeredBy'], 'the console settled nothing, so the files answered');
        self::assertSame(1, $result->data['matchCount']);
        self::assertStringContainsString('the console settled nothing', $result->text);
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function aConsoleThatExitsWellAndSaysNothingIsUnanswered(): void
    {
        // Same failure with nothing behind it to fall back on: what must not
        // happen is a matchCount of 0 under answeredBy "installation", because
        // that is the shape of an installation that really has no such label.
        $this->consoleThatPrints('');

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('not answerable here', $result->text);
        self::assertStringContainsString(
            'exited successfully with neither a JSON payload nor the warning',
            $result->data['unsupported']['reason']
        );
    }

    #[Requirement('R-ANS-005')]
    #[Test]
    public function aConsoleThatCannotRunIsStillUnanswered(): void
    {
        $this->consoleThatFails('the database is not reachable');

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('not answerable here', $result->text);
    }

    #[Test]
    public function whatEachWordReachesIsInTheAnswer(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [[
            'resource' => 'EXT:backend/Resources/Private/Language/locallang.xlf',
            'labels' => [
                ['domain' => 'backend.messages', 'reference' => 'labels.save', 'label' => 'Save'],
                ['domain' => 'backend.messages', 'reference' => 'labels.document', 'label' => 'Document'],
            ],
        ]]], JSON_THROW_ON_ERROR));

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(
            [['term' => 'save', 'matchCount' => 1], ['term' => 'document', 'matchCount' => 1]],
            $result->data['terms']
        );
        self::assertStringContainsString('"save" reaches 1 label', $result->text);
    }

    /**
     * The guessed path of `feedback/2026-08-24-225129`: there is no Wizard.xlf,
     * and the file the session was after is Wizards/general.xlf. Every word
     * came back at 0, which is what a misspelled word comes back as, so the
     * session read "this resource holds no such label" and wrote one —
     * `D-ANS-016`.
     */
    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-016')]
    #[Test]
    public function aResourceHoldingNothingNamesTheResourcesThatDo(): void
    {
        $this->consoleThatPrints($this->wizardLabels());

        $result = Registry::call('typo3_label_lookup', [
            'query' => 'error title',
            'resource' => 'EXT:backend/Resources/Private/Language/Wizard.xlf',
        ]);

        self::assertSame(0, $result->data['matchCount']);
        self::assertSame(
            [['term' => 'error', 'matchCount' => 2], ['term' => 'title', 'matchCount' => 1]],
            $result->data['termCountsWithoutTheNarrowing'],
        );
        self::assertSame(
            ['EXT:backend/Resources/Private/Language/Wizards/general.xlf'],
            $result->data['resources'],
        );
        self::assertStringContainsString(
            'Narrowed to that resource — it is what emptied this, not the words: in extension "backend" without it, '
            . '"error" reaches 2 labels, "title" reaches 1 label. Ask again with extension "backend" and no resource.',
            $result->text,
        );
        self::assertStringContainsString(
            "The resources that do hold one:\n- EXT:backend/Resources/Private/Language/Wizards/general.xlf",
            $result->text,
        );
        self::assertStringContainsString('A path that exists nowhere answers exactly like', $result->text);
        // The sentence the session acted on. Every word reaching 0 inside a
        // resource that holds nothing is not a fact about the words.
        self::assertStringNotContainsString('ask again with the one that narrows best', $result->text);
    }

    /**
     * A resource that holds part of the query is a resource that exists, so the
     * listing that replaces a guessed path would be noise — and the word it
     * does not reach is still the filter's doing, which is the sentence that
     * stays — `D-ANS-016`.
     */
    #[Decision('D-ANS-016')]
    #[Test]
    public function aResourceHoldingPartOfTheQueryIsNotReplacedByAListing(): void
    {
        $this->consoleThatPrints($this->wizardLabels());

        $result = Registry::call('typo3_label_lookup', [
            'query' => 'error title',
            'resource' => 'EXT:backend/Resources/Private/Language/Wizards/localization.xlf',
        ]);

        self::assertArrayNotHasKey('resources', $result->data);
        self::assertSame(
            [['term' => 'error', 'matchCount' => 2], ['term' => 'title', 'matchCount' => 1]],
            $result->data['termCountsWithoutTheNarrowing'],
        );
        self::assertStringContainsString('Narrowed to that resource', $result->text);
        self::assertStringNotContainsString('The resources that do hold one', $result->text);
        self::assertStringContainsString(
            'Inside extension "backend" and resource "EXT:backend/Resources/Private/Language/Wizards/localization.xlf"'
            . ', on its own, "error" reaches 1 label.',
            $result->text,
        );
    }

    /**
     * A resource that narrowed nothing away is not what emptied the answer, and
     * a miss that says it is sends the caller to drop a filter that costs it
     * nothing — the same rule the changelog miss is held to by
     * `PackageSourcesTest::aFilterThatChangedNothingIsNotBlamedForTheMiss`.
     */
    #[Decision('D-ANS-016')]
    #[Test]
    public function aResourceThatChangedNothingIsNotBlamedForTheMiss(): void
    {
        $this->consoleThatPrints($this->wizardLabels());

        $result = Registry::call('typo3_label_lookup', [
            'query' => 'error frobnicate',
            'resource' => 'EXT:backend/Resources/Private/Language/Wizards/localization.xlf',
        ]);

        self::assertArrayNotHasKey('termCountsWithoutTheNarrowing', $result->data);
        self::assertStringNotContainsString('Narrowed to', $result->text);
        self::assertStringContainsString('ask again with the one that narrows best', $result->text);
    }

    /**
     * Where no resource holds one, the empty list is the answer rather than a
     * withheld field: the caller that reads it stops looking for a path and
     * writes the label — which is the move the reported miss made on a query
     * that did have one.
     */
    #[Requirement('R-ANS-006')]
    #[Decision('D-ANS-016')]
    #[Test]
    public function aQueryNoResourceHoldsWholeIsToldSo(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [[
            'resource' => 'EXT:backend/Resources/Private/Language/Wizards/localization.xlf',
            'labels' => [
                ['domain' => 'backend.wizards.localization', 'reference' => 'wizard.error', 'label' => 'Configuration failed'],
                ['domain' => 'backend.wizards.localization', 'reference' => 'wizard.headline', 'label' => 'Title'],
            ],
        ]]], JSON_THROW_ON_ERROR));

        $result = Registry::call('typo3_label_lookup', [
            'query' => 'error title',
            'resource' => 'EXT:backend/Resources/Private/Language/Wizard.xlf',
        ]);

        self::assertSame([], $result->data['resources']);
        self::assertStringContainsString('No other resource holds one either.', $result->text);
    }

    /**
     * Two resources of one extension: one carries both words of the query, the
     * other only the first.
     */
    private function wizardLabels(): string
    {
        return (string) json_encode(['items' => [
            [
                'resource' => 'EXT:backend/Resources/Private/Language/Wizards/general.xlf',
                'labels' => [
                    ['domain' => 'backend.wizards.general', 'reference' => 'wizard.step.error.title', 'label' => 'Error'],
                ],
            ],
            [
                'resource' => 'EXT:backend/Resources/Private/Language/Wizards/localization.xlf',
                'labels' => [
                    ['domain' => 'backend.wizards.localization', 'reference' => 'wizard.error', 'label' => 'Configuration failed'],
                ],
            ],
        ]], JSON_THROW_ON_ERROR);
    }

    #[Requirement('R-KNW-036')]
    #[Test]
    public function aResourceRestrictsReuseToTheUsageContext(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [
            [
                'resource' => 'EXT:backend/Resources/Private/Language/locallang.xlf',
                'labels' => [
                    ['domain' => 'backend.messages', 'reference' => 'action.new', 'label' => 'New'],
                ],
            ],
            [
                'resource' => 'EXT:sitepackage/Resources/Private/Language/Backend/Import.xlf',
                'labels' => [
                    ['domain' => 'sitepackage.backend.import', 'reference' => 'actions.createImport', 'label' => 'New import'],
                ],
            ],
        ]], JSON_THROW_ON_ERROR));

        $resource = 'EXT:sitepackage/Resources/Private/Language/Backend/Import.xlf';
        $result = Registry::call('typo3_label_lookup', [
            'query' => 'new',
            'resource' => $resource,
        ]);

        self::assertSame(1, $result->data['matchCount']);
        self::assertSame($resource, $result->data['resource']);
        self::assertSame('actions.createImport', $result->data['labels'][0]['key']);
        self::assertStringNotContainsString('backend.messages:action.new', $result->text);
        self::assertStringContainsString('Search restricted to the translation resource used', $result->text);
        self::assertStringContainsString($resource, $result->text);
    }

    /**
     * The backend ships two labels keyed `newPage`, and which of them a piece
     * of markup renders decides what an assertion on that markup has to expect:
     * `backend.pages_new:newPage` is "Page" and `backend.layout:newPage` is
     * "Create new page", in `.checkouts/14.3` as in `.checkouts/main`. Neither
     * the key nor the extension separates them. The resource does, and the
     * domain half of the ref is derived from it, so an answer that dropped the
     * file would still be right and no longer decidable —
     * `feedback/2026-08-13-214838`, which read the wrong guess off the key.
     */
    #[Test]
    public function twoLabelsOfOneKeyAreToldApartByTheResourceEachIsIn(): void
    {
        $this->consoleThatPrints((string) json_encode(['items' => [
            [
                'resource' => 'EXT:backend/Resources/Private/Language/locallang_pages_new.xlf',
                'labels' => [['domain' => 'backend.pages_new', 'reference' => 'newPage', 'label' => 'Page']],
            ],
            [
                'resource' => 'EXT:backend/Resources/Private/Language/locallang_layout.xlf',
                'labels' => [['domain' => 'backend.layout', 'reference' => 'newPage', 'label' => 'Create new page']],
            ],
        ]], JSON_THROW_ON_ERROR));

        $result = Registry::call('typo3_label_lookup', ['query' => 'newPage', 'extension' => 'backend']);

        self::assertSame(
            [
                ['backend.pages_new:newPage', 'Page', 'EXT:backend/Resources/Private/Language/locallang_pages_new.xlf'],
                ['backend.layout:newPage', 'Create new page', 'EXT:backend/Resources/Private/Language/locallang_layout.xlf'],
            ],
            array_map(
                static fn(array $label): array => [$label['ref'], $label['source'], $label['resource']],
                $result->data['labels'],
            ),
        );
        // The text is what a client rendering it instead of the data shows, and
        // `resource` is no required key of the record, so both halves are held
        // here rather than one of them by the schema.
        self::assertStringContainsString(
            "\n  EXT:backend/Resources/Private/Language/locallang_pages_new.xlf",
            $result->text,
        );
        self::assertStringContainsString(
            "\n  EXT:backend/Resources/Private/Language/locallang_layout.xlf",
            $result->text,
        );
    }

    /**
     * Both reports of German written into a source XLF came from a session
     * that called this tool and named labels nowhere else — `R-ANS-015`. So
     * the rule rides on the answer rather than on a query the caller would
     * have had to phrase around labels first.
     */
    #[Decision('D-ANS-024')]
    #[Requirement('R-ANS-015')]
    #[Test]
    #[DataProvider('whatTheConsoleAnswers')]
    public function aCallerAboutToWriteAUnitIsToldItsSourceLanguage(string $output): void
    {
        $this->consoleThatPrints($output);

        $result = Registry::call('typo3_label_lookup', ['query' => 'testimonial author']);

        self::assertStringContainsString('Write a new trans-unit in English in the unprefixed source file', $result->text);
        self::assertStringContainsString('de.locallang.xlf for locallang.xlf', $result->text);
        self::assertStringContainsString('is a defect to correct in place', $result->text);
        self::assertStringContainsString('an en.-prefixed file is not that correction', $result->text);
    }

    /**
     * The two branches a caller about to author a unit arrives on: nothing
     * matched, and a neighbouring label that may or may not be reusable.
     *
     * @return array<string, array{0: string}>
     */
    public static function whatTheConsoleAnswers(): array
    {
        return [
            'nothing matched' => ["Labels in active extensions\n===\n\n [WARNING] No language resource files found.\n"],
            'a neighbour in the same resource' => [(string) json_encode(['items' => [[
                'resource' => 'EXT:sitepackage/Resources/Private/Language/backend_fields.xlf',
                'labels' => [
                    ['domain' => 'sitepackage.backend_fields', 'reference' => 'testimonial.author', 'label' => 'Autor des Testimonials'],
                ],
            ]]], JSON_THROW_ON_ERROR)],
        ];
    }

    #[Requirement('R-ANS-038')]
    #[Test]
    public function aProjectSiteLabelFileIsReadBesideAnEmptyConsoleAnswer(): void
    {
        $this->consoleThatPrints("Labels in active extensions\n===\n\n [WARNING] No language resource files found.\n");
        $this->projectLabelFile('config/sites/main/labels.xlf', ['site.title' => 'Site title']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'site title']);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('LLL:config/sites/main/labels.xlf:site.title', $result->data['labels'][0]['ref']);
        self::assertSame('project-site', $result->data['resourceDiagnostics'][0]['location']);
        self::assertStringContainsString('does not register XLF files below config/sites automatically', $result->text);
    }

    #[Requirement('R-ANS-038')]
    #[Test]
    public function aNonStandardLabelFileIsWarned(): void
    {
        $this->consoleThatFails('The console cannot boot');
        $this->labelFile('Resources/Private/Language/BackendLabels.xlf', ['button.save' => 'Save changes']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'save changes']);

        self::assertFalse($result->data['resourceDiagnostics'][0]['conventionalName']);
        self::assertStringContainsString(
            'The conventional package language file name is locallang.xlf or locallang_<subject>.xlf.',
            $result->text,
        );
    }

    #[Decision('D-ANS-134')]
    #[Requirement('R-ANS-038')]
    #[Test]
    public function aStaticReferenceIsNamed(): void
    {
        $this->consoleThatFails('The console cannot boot');
        $this->labelFile('Resources/Private/Language/locallang_feature.xlf', ['feature.title' => 'Feature title']);
        $reference = $this->installationRoot . '/typo3/sysext/core/Configuration/TCA/Overrides/pages.php';
        mkdir(dirname($reference), 0o777, true);
        file_put_contents($reference, "<?php return ['label' => 'core.feature:feature.title'];");

        $result = Registry::call('typo3_label_lookup', ['query' => 'feature title']);

        self::assertTrue($result->data['resourceDiagnostics'][0]['referenced']);
        self::assertSame(
            ['EXT:core/Configuration/TCA/Overrides/pages.php'],
            $result->data['resourceDiagnostics'][0]['references'],
        );
        self::assertStringNotContainsString('No static reference', $result->text);
    }

    #[Decision('D-ANS-134')]
    #[Requirement('R-ANS-038')]
    #[Test]
    public function anUnreferencedResourceStaysVisible(): void
    {
        $this->consoleThatFails('The console cannot boot');
        $this->labelFile('Resources/Private/Language/locallang_orphan.xlf', ['orphan.title' => 'Orphan title']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'orphan title']);

        self::assertSame(1, $result->data['matchCount']);
        self::assertFalse($result->data['resourceDiagnostics'][0]['referenced']);
        self::assertStringContainsString('references assembled at runtime are outside this scan', $result->text);
    }

    #[Requirement('R-ANS-038')]
    #[Test]
    public function aSiteSetLabelsFileCarriesItsImplicitReference(): void
    {
        $this->consoleThatFails('The console cannot boot');
        $this->labelFile('Configuration/Sets/Feature/labels.xlf', ['feature.name' => 'Feature name']);
        $configuration = $this->installationRoot . '/typo3/sysext/core/Configuration/Sets/Feature/config.yaml';
        file_put_contents($configuration, "name: core/feature\n");

        $result = Registry::call('typo3_label_lookup', ['query' => 'feature name']);

        self::assertTrue($result->data['resourceDiagnostics'][0]['referenced']);
        self::assertSame(
            ['EXT:core/Configuration/Sets/Feature/config.yaml (implicit labels.xlf)'],
            $result->data['resourceDiagnostics'][0]['references'],
        );
        self::assertSame([], $result->data['resourceDiagnostics'][0]['warnings']);
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function aConsoleThatCannotBootIsAnsweredFromTheFilesItWouldHaveRead(): void
    {
        // An installed TYPO3 whose database has no schema yet: the console
        // fails on the first query, and the labels are sitting in the XLF files
        // of the same packages the icon lookup already reads.
        $this->consoleThatFails('An exception occurred while executing a query: '
            . "Table 'db.tx_scheduler_task' doesn't exist");
        $this->labelFile('Resources/Private/Language/locallang.xlf', ['labels.save' => 'Save document']);

        $result = Registry::call('typo3_label_lookup', ['query' => 'save document']);

        self::assertSame('packages', $result->data['answeredBy']);
        self::assertSame('core.messages:labels.save', $result->data['labels'][0]['ref']);
        // The file a hit is in travels on this path too, which is the half of
        // the answer the caller opens next.
        self::assertSame(
            'EXT:core/Resources/Private/Language/locallang.xlf',
            $result->data['labels'][0]['resource'],
        );
        self::assertStringContainsString('LANG/resourceOverrides', $result->text);
    }

    #[Requirement('R-ANS-008')]
    #[Test]
    public function aDatabaseWithoutASchemaIsNamed(): void
    {
        $this->consoleThatFails('An exception occurred while executing a query: '
            . "Table 'db.tx_scheduler_task' doesn't exist");

        // Nothing to fall back on here — this package ships no labels — so the
        // answer is unanswered, and says what to do about it.
        $result = Registry::call('typo3_label_lookup', ['query' => 'save']);

        self::assertArrayNotHasKey('answeredBy', $result->data);
        self::assertStringContainsString('no TYPO3 schema yet', $result->text);
        self::assertStringContainsString('no TYPO3 schema yet', $result->data['unsupported']['diagnosis']);
    }

    /** @param array<string, string> $units */
    private function labelFile(string $path, array $units): void
    {
        $this->projectLabelFile('typo3/sysext/core/' . $path, $units);
    }

    /** @param array<string, string> $units */
    private function projectLabelFile(string $path, array $units): void
    {
        $file = $this->installationRoot . '/' . $path;
        mkdir(dirname($file), 0o777, true);

        $body = '';
        foreach ($units as $id => $source) {
            $body .= sprintf('<trans-unit id="%s"><source>%s</source></trans-unit>', $id, $source);
        }
        file_put_contents($file, '<?xml version="1.0" encoding="UTF-8"?>'
            . '<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">'
            . '<file source-language="en" datatype="plaintext"><body>' . $body . '</body></file></xliff>');
    }

    /** A console that answers every call with $output and exits successfully. */
    private function consoleThatPrints(string $output): void
    {
        $this->console(sprintf('<?php echo %s;', var_export($output, true)));
    }

    /** A console that fails the way a broken installation does. */
    private function consoleThatFails(string $reason): void
    {
        $this->console(sprintf('<?php fwrite(STDERR, %s); exit(1);', var_export($reason, true)));
    }

    private function console(string $script): void
    {
        $root = $this->removeAfterwards(sys_get_temp_dir() . '/typo3-dev-companion-labels-' . bin2hex(random_bytes(6)));
        $this->installationRoot = $root;
        mkdir($root . '/typo3/sysext/core', 0o777, true);
        file_put_contents($root . '/composer.json', json_encode(
            ['name' => 'typo3/cms', 'type' => 'typo3-cms-core'],
            JSON_THROW_ON_ERROR
        ));
        file_put_contents($root . '/typo3/sysext/core/composer.json', json_encode([
            'name' => 'typo3/cms-core',
            'type' => 'typo3-cms-framework',
            'extra' => ['typo3/cms' => ['extension-key' => 'core']],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($root . '/console.php', $script);

        putenv(Typo3Cli::CONSOLE_VARIABLE . '=' . PHP_BINARY . ' ' . $root . '/console.php');
        Instance::discoverFrom($root);
        Typo3Cli::forget();
    }
}
