<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use Mcp\Capability\Discovery\SchemaValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\DevCompanion\Manual\Documentation;
use TYPO3\DevCompanion\Manual\Manuals;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\DocumentationLookup;
use TYPO3\DevCompanion\Tool\Registry;

#[Requirement('R-DOC-001')]
final class DocumentationTest extends TestCase
{
    protected function tearDown(): void
    {
        Manuals::useReader(null);
    }

    #[Test]
    public function itSearchesTheRequestedVersionAndKeepsProvenanceOnEveryResult(): void
    {
        $requested = [];
        $index = $this->inventory([
            'ApiOverview/Seo/PageTitleApi.html' => 'Page title API',
            'ApiOverview/Events/Index.html' => 'Events and hooks',
        ]);
        $documentation = new Documentation(static function (string $url) use (&$requested, $index): string {
            $requested[] = $url;
            if (str_ends_with($url, 'objects.inv')) {
                return $index;
            }
            if (str_ends_with($url, 'PageTitleApi.html')) {
                return '<html><article role="main"><p>Page title providers implement the provider interface.</p></article></html>';
            }

            return '<html><article role="main"><p>PSR-14 events extend TYPO3 without replacing the implementation.</p></article></html>';
        });

        $answer = $documentation->lookup(['page title event', 'page title provider'], '13.4', 2);

        self::assertSame('search', $answer['mode']);
        self::assertSame('answered', $answer['status']);
        self::assertNotEmpty($answer['results']);
        self::assertSame('Page title API', $answer['results'][0]['title']);
        self::assertSame('13.4', $answer['results'][0]['documentVersion']);
        self::assertSame('typo3/reference-coreapi', $answer['results'][0]['document']);
        self::assertStringStartsWith(
            'https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/',
            $answer['results'][0]['url'],
        );
        self::assertNotSame('', $answer['results'][0]['excerpt']);
        self::assertSame('', $answer['results'][0]['content']);
        self::assertSame([], array_filter($requested, static fn(string $url): bool => !str_contains($url, '/13.4/')));
    }

    #[Test]
    public function itReadsACanonicalSearchResultAsStructuredText(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/DocHeaderComponent.html';
        $documentation = new Documentation(static fn(string $requested): ?string => $requested === $url
            ? <<<'HTML'
                <html><body><nav>Not page content</nav><article role="main">
                <h1>DocHeaderComponent</h1>
                <p>Use the document header for module buttons.</p>
                <h2>Shortcut context</h2>
                <pre><code>$docHeader->setShortcutContext('records', 'Records');</code></pre>
                <ul><li>The route and arguments describe the current module.</li></ul>
                </article></body></html>
                HTML
            : null);

        $answer = $documentation->page($url, '14.3');

        self::assertSame('page', $answer['mode']);
        self::assertSame('answered', $answer['status']);
        self::assertSame($url, $answer['results'][0]['url']);
        self::assertSame('DocHeaderComponent', $answer['results'][0]['title']);
        self::assertStringContainsString('# DocHeaderComponent', $answer['results'][0]['content']);
        self::assertStringContainsString('setShortcutContext', $answer['results'][0]['content']);
        self::assertStringNotContainsString('Not page content', $answer['results'][0]['content']);
    }

    /**
     * The TCA reference states the machine-readable half of every property as a
     * definition list, and only the terms were emitted.
     *
     * `feedback/2026-08-07-132457` read the `type=datetime` page for the
     * default of `nullable` per `dbType`, and got `**Type**`, `**Default**`,
     * `**Path**` and `**Scope**` each named and each empty — which reads as the
     * property having no documented default rather than as this reader having
     * dropped it. The value it needed was one of those cells, and it read
     * `DateTimeFieldType` in the checkout instead.
     */
    #[Test]
    public function itCarriesTheValuesOfAPropertyDefinitionList(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/ColumnsConfig/Type/Datetime/Index.html';
        $documentation = new Documentation(static fn(string $requested): ?string => $requested === $url
            ? <<<'HTML'
                <html><body><article role="main">
                <h1>Datetime</h1>
                <dl>
                  <dt>nullable</dt>
                  <dd>
                    <dl class="field-list">
                      <dt>Type</dt><dd>bool</dd>
                      <dt>Default</dt><dd>false</dd>
                      <dt>Scope</dt><dd>Proc.</dd>
                    </dl>
                    <p>If nothing is entered into the field, then it will be saved as NULL.</p>
                  </dd>
                </dl>
                </article></body></html>
                HTML
            : null);

        $content = $documentation->page($url, '14.3')['results'][0]['content'];

        self::assertStringContainsString('**Type**: bool', $content);
        self::assertStringContainsString('**Default**: false', $content);
        self::assertStringContainsString('**Scope**: Proc.', $content);
        // The property itself keeps its own line: its definition is the list
        // and the prose below it, which is not a value to join to a term.
        self::assertStringContainsString("**nullable**\n", $content);
        self::assertStringContainsString('saved as NULL', $content);
        // And the wrapper is not printed a second time as one long run.
        self::assertSame(1, substr_count($content, 'saved as NULL'));
        self::assertStringNotContainsString('**nullable**: ', $content);
    }

    #[Test]
    public function itRefusesAPageOutsideTheSelectedManualVersion(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Documentation(static fn(string $url): ?string => null))->page(
            'https://docs.typo3.org/m/typo3/reference-coreapi/13.4/en-us/ApiOverview/Backend/Index.html',
            '14.3',
        );
    }

    #[Test]
    public function aTcaQuestionIsAnsweredFromTheTcaReference(): void
    {
        // TYPO3 Explained documents everything around TCA and not TCA itself,
        // so this used to come back as the events that carry "inline" and
        // "localization" in their class names.
        $answer = (new Documentation($this->manuals()))->lookup(
            ['TCA inline foreign_field foreign_sortby localization children'],
            '14.3',
            3,
        );

        self::assertSame('IRRE / inline', $answer['results'][0]['title']);
        self::assertSame('typo3/reference-tca', $answer['results'][0]['document']);
    }

    /**
     * A page titled after its subject and a page whose title is a long event
     * class name carry the word equally well, and the class name carries five
     * other words besides. While no title in the corpus was long enough to be
     * diluted the two were worth the same, and the tie went to whichever manual
     * was indexed first (`D-ANS-029`) — `D-ANS-032`.
     */
    #[Decision('D-ANS-032')]
    #[Test]
    public function aPageTitledAfterItsSubjectOutranksALongerTitle(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['inline'], '14.3', 2);

        self::assertSame('IRRE / inline', $answer['results'][0]['title']);
        self::assertSame('ModifyInlineElementControlsEvent', $answer['results'][1]['title']);
    }

    /**
     * The manuals of the core are published under `/m/` and this one is not, so
     * a search that built every base the same way reached three books, none of
     * which documents a ViewHelper, and the question was answered from
     * whichever of them carried the word (`D-ANS-023`). A base that is wrong is
     * silent — the index does not answer and the book is simply absent — so
     * what is held is that its pages are reached and reached at their own base
     * — `D-ANS-026`.
     */
    #[Requirement('R-DOC-003')]
    #[Decision('D-ANS-026')]
    #[Test]
    public function aViewHelperQuestionReachesTheManualOutsideTheCollection(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(
            ['f:if f:then f:else condition ViewHelper'],
            '14.3',
            4,
        );

        $reference = array_values(array_filter(
            $answer['results'],
            static fn(array $result): bool => $result['document'] === 'typo3/view-helper-reference',
        ));
        self::assertNotSame([], $reference);
        self::assertSame('Fluid ViewHelper Reference', $reference[0]['documentTitle']);
        self::assertStringStartsWith(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/',
            $reference[0]['url'],
        );
    }

    /**
     * And the page of the ViewHelper that gave its name to the question.
     *
     * That book titles a page after the tag, so `Global/If.html` is called "if"
     * and the only word of `f:if` that can reach it is two characters long.
     * Every one of those was dropped before it was searched for, which is what
     * left the query answered by `Global/Else.html` — the right family and the
     * wrong page (`D-ANS-023`) — `D-ANS-028`.
     */
    #[Decision('D-ANS-028')]
    #[Test]
    public function aViewHelperNamedAfterAKeywordIsReachedByItsOwnName(): void
    {
        $documentation = new Documentation($this->manuals());

        self::assertSame(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html',
            $documentation->lookup(['f:if'], '14.3', 1)['results'][0]['url'],
        );
    }

    /**
     * And the book it belongs to, which is what neither the tokenizer nor the
     * dilution reference reaches. Three pages of the corpus are titled `if`, so
     * all three are undiluted, all three matched the title, and no field weight
     * separates identical titles (`D-ANS-032`). The query says which book in
     * the `f:` it is written in (`D-ANS-036`).
     */
    #[Requirement('R-DOC-003')]
    #[Decision('D-ANS-036')]
    #[Test]
    public function aQueryWrittenInFluidTagsIsAnsweredFromTheFluidBook(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['f:if'], '14.3', 3);

        self::assertSame(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html',
            $answer['results'][0]['url'],
        );
        self::assertSame([], array_filter(
            $answer['results'],
            static fn(array $result): bool => $result['document'] !== 'typo3/view-helper-reference',
        ));
    }

    /**
     * A tag named after a word the stopword list holds reaches its page too.
     * `f:then` is one term or none, and the list is what it is because "then"
     * says nothing in a sentence — which is not what it does behind a namespace
     * prefix (`D-ANS-047`).
     */
    #[Requirement('R-DOC-003')]
    #[Decision('D-ANS-047')]
    #[Test]
    public function aTagNamedAfterAStopwordIsReachedByItsOwnName(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['f:then'], '14.3', 1);

        self::assertSame('answered', $answer['status']);
        self::assertSame(
            'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/Then.html',
            $answer['results'][0]['url'],
        );
    }

    /**
     * A book that did not answer routes nothing. The route is in front of the
     * scoring, so a root that is down would otherwise leave such a query with
     * no candidates and report "no match" for a reason the caller cannot see —
     * `D-ANS-036`.
     */
    #[Decision('D-ANS-036')]
    #[Test]
    public function aQueryIsRoutedToABookOnlyWhileThatBookAnswers(): void
    {
        $manuals = $this->manuals();
        $answer = (new Documentation(static fn(string $url): ?string => str_contains($url, 'view-helper-reference')
            ? null
            : $manuals($url)))->lookup(['f:if'], '14.3', 3);

        self::assertSame('answered', $answer['status']);
        self::assertSame('typo3/reference-typoscript', $answer['results'][0]['document']);
    }

    /**
 * And the URL it hands back is one it takes back, on the same version —
 * `D-ANS-023`, `D-ANS-026`.
 */
    #[Requirement('R-DOC-003')]
    #[Decision('D-ANS-026')]
    #[Test]
    public function aPageOfThatManualIsReadBackAtItsOwnBase(): void
    {
        $url = 'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html';
        $documentation = new Documentation(static fn(string $requested): ?string => $requested === $url
            ? '<html><article role="main"><h1>If ViewHelper &lt;f:if&gt;</h1>'
                . '<p>This ViewHelper implements an if/else condition.</p></article></html>'
            : null);

        $answer = $documentation->page($url, '14.3');

        self::assertSame('answered', $answer['status']);
        self::assertSame('typo3/view-helper-reference', $answer['results'][0]['document']);
        self::assertSame('Fluid ViewHelper Reference', $answer['results'][0]['documentTitle']);
    }

    /**
     * The property name a caller holds, which no table of contents carries.
     *
     * A session needed one sentence about `columnsOverrides` and spent three
     * calls on it: the manual documenting that property is a handful of large
     * pages, and the property is a section of one of them. The writer registers
     * every such section, and this is what reads them — `D-ANS-144`.
     */
    #[Decision('D-ANS-144')]
    #[Test]
    public function aDeclaredPropertyIsReachedByItsOwnName(): void
    {
        $documentation = new Documentation($this->manuals());

        $answer = $documentation->lookup(['columnsOverrides types record type field configuration'], '14.3', 3);

        self::assertSame('columnsOverrides', $answer['results'][0]['title']);
        self::assertStringEndsWith(
            'Types/Index.html#confval-types-columnsoverrides',
            $answer['results'][0]['url'],
        );
        // The section's own prose. The page opens on record types and says
        // nothing about any one of the properties it documents.
        self::assertStringContainsString(
            'does not take columnsOverrides into account',
            $answer['results'][0]['excerpt'],
        );

        // A property named like an English word is a word of the sentence
        // wherever it stands in one, and is offered for nothing but itself.
        $prose = $documentation->lookup(['the label of a record type in the backend'], '14.3', 3);
        self::assertNotContains('label', array_column($prose['results'], 'title'));

        $alone = $documentation->lookup(['showitem'], '14.3', 2);
        self::assertSame('showitem', $alone['results'][0]['title']);
    }

    #[Decision('D-ANS-065')]
    #[Test]
    public function anApiIdentifierReachesThePageThatIsNotNamedAfterIt(): void
    {
        // Nothing in a table of contents is called AssetCollector or
        // FunctionalTestCase; the pages that answer them are titled after their
        // subject, which is assets and functional testing — `D-ANS-065`.
        $documentation = new Documentation($this->manuals());

        self::assertContains(
            'Assets (CSS, JavaScript, Media)',
            array_column($documentation->lookup(['Fluid AssetCollector css javascript ViewHelper'], '14.3', 3)['results'], 'title'),
        );
        self::assertContains(
            'Functional testing with the TYPO3 testing framework',
            array_column($documentation->lookup(['FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14'], '14.3', 3)['results'], 'title'),
        );
    }

    /**
     * The three queries of `D-ANS-021` came back `answered` with six results
     * each, and nothing in them showed that the word naming the subject had
     * reached none of the pages returned.
     */
    #[Decision('D-ANS-021')]
    #[Requirement('R-DOC-002')]
    #[Test]
    public function aResultNamesTheWordsOfTheQueryItWasMatchedOn(): void
    {
        $index = $this->inventory([
            'ExtensionArchitecture/HowTo/Localization/Fluid.html' => 'Multi-language Fluid templates',
            'ApiOverview/Database/DatabaseRecords/RecordObjects.html' => 'Record objects',
        ]);
        $documentation = new Documentation(static function (string $url) use ($index): ?string {
            if (!str_contains($url, 'typo3/reference-coreapi')) {
                return null;
            }

            return str_ends_with($url, 'objects.inv')
                ? $index
                : '<html><article role="main"><p>What this page says.</p></article></html>';
        });

        $answer = $documentation->lookup(['Record API Fluid template access record.header'], '14.3', 2);

        $matched = [];
        foreach ($answer['results'] as $result) {
            $matched[$result['title']] = array_column($result['matched'], 'field', 'term');
        }
        // The page the session was after carries the subject and nothing else;
        // the one that outranks it carries everything except the subject.
        self::assertSame(['record' => 'title', 'api' => 'path'], $matched['Record objects']);
        self::assertSame(['fluid' => 'title', 'templa' => 'title'], $matched['Multi-language Fluid templates']);
    }

    /** A page was not searched for, so it was matched on nothing. */
    #[Requirement('R-DOC-002')]
    #[Test]
    public function aPageReadBackCarriesNoMatch(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Assets/Index.html';
        $documentation = new Documentation(
            static fn(string $requested): string => '<html><article role="main"><p>What this page says.</p></article></html>',
        );

        self::assertSame([], $documentation->page($url, '14.3')['results'][0]['matched']);
    }

    /**
     * And it covers no query either, which is null rather than zero: nothing
     * was asked, so there is no share to report (`D-ANS-051`).
     */
    #[Decision('D-ANS-051')]
    #[Test]
    public function aPageReadBackCoversNoQuery(): void
    {
        $url = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Assets/Index.html';
        $documentation = new Documentation(
            static fn(string $requested): string => '<html><article role="main"><p>What this page says.</p></article></html>',
        );

        self::assertNull($documentation->page($url, '14.3')['results'][0]['coverage']);
    }

    /**
     * How much of the question a result carries, on the result. The page whose
     * title is the query covers all of it; the ones that carry one word of a
     * five-word question say so in a number rather than in a rank
     * (`D-ANS-051`).
     */
    #[Decision('D-ANS-051')]
    #[Test]
    public function everySearchResultSaysHowMuchOfTheQueryItCovers(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['inline'], '14.3', 2);

        self::assertSame(1.0, $answer['results'][0]['coverage']);
        self::assertLessThan(1.0, $answer['results'][1]['coverage']);
        self::assertGreaterThan(0.0, $answer['results'][1]['coverage']);
    }

    /**
     * The coverage reported is the one of the question the page is kept for,
     * like the match beside it — not of whichever query was passed last. Both
     * pages here are returned for the query that names them — `D-ANS-051`.
     */
    #[Decision('D-ANS-046')]
    #[Decision('D-ANS-051')]
    #[Test]
    public function aResultCoversTheQueryItIsKeptFor(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['inline', 'tcaDescription'], '14.3', 4);

        $coverage = array_column($answer['results'], 'coverage', 'title');
        self::assertSame(1.0, $coverage['IRRE / inline']);
        self::assertSame(1.0, $coverage['tcaDescription']);
    }

    /**
     * A thin answer is labelled and not emptied. The floor the rule search
     * drops a section below has no value here that both empties the six
     * collisions `feedback/2026-08-03-164734` reported and returns the page
     * that answers a three-word question, so this page keeps answering while
     * the number says how little of the question it carries (`D-ANS-051`).
     */
    #[Decision('D-ANS-051')]
    #[Test]
    public function aResultCoveringLessThanHalfTheQueryIsStillReturned(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(
            ['TCA inline foreign_field foreign_sortby localization children'],
            '14.3',
            3,
        );

        self::assertSame('answered', $answer['status']);
        self::assertSame('IRRE / inline', $answer['results'][0]['title']);
        self::assertLessThan(0.5, $answer['results'][0]['coverage']);
    }

    /**
     * And the caller is told in the text, because a share in a payload is not a
     * warning: the answer this feedback reported as the expensive kind of wrong
     * one was six results in the shape a good answer has — `D-ANS-051`.
     */
    #[Decision('D-ANS-046')]
    #[Decision('D-ANS-051')]
    #[Test]
    public function theAnswerSaysWhereNothingCoversHalfTheQuery(): void
    {
        Manuals::useReader($this->manuals());

        $text = Registry::call('typo3_documentation_lookup', [
            'queries' => ['TCA inline foreign_field foreign_sortby localization children'],
            'targetVersion' => '14.3',
            'limit' => 3,
        ])->text;

        self::assertStringContainsString('Nothing found covers half of a query asked', $text);
        self::assertStringContainsString('ask again with the subject alone', $text);
        self::assertMatchesRegularExpression('/Matched on: .+ — covers \d+% of the query\./', $text);
    }

    /**
 * And says nothing of the kind where a page does cover the question —
 * `D-ANS-051`.
 */
    #[Decision('D-ANS-051')]
    #[Test]
    public function anAnswerThatCoversTheQuestionCarriesNoSuchSentence(): void
    {
        Manuals::useReader($this->manuals());

        $text = Registry::call('typo3_documentation_lookup', [
            'queries' => ['inline'],
            'targetVersion' => '14.3',
            'limit' => 2,
        ])->text;

        self::assertStringNotContainsString('Nothing found covers half', $text);
        self::assertStringContainsString('covers 100% of the query.', $text);
    }

    #[Test]
    public function anAnsweredIndexWithNoMatchIsNotAnUnavailableService(): void
    {
        $index = $this->inventory(['Introduction.html' => 'Introduction']);
        $documentation = new Documentation(static fn(string $url): ?string => str_ends_with($url, 'objects.inv')
            ? $index
            : null);

        $answer = $documentation->lookup(['quantum pineapple'], '13.4');

        self::assertSame('empty', $answer['status']);
        self::assertSame([], $answer['results']);
        self::assertNull($answer['unavailable']);
    }

    /**
     * The tables of contents as they are published, cut down to the pages this
     * is about: the ones that answer, and the ones that used to be answered
     * instead because they carry one of the words.
     */
    /**
     * What the TCA reference declares as configuration values, which is where
     * its property names live: its table of contents carries none of them.
     *
     * @var array<string, array<string, string>>
     */
    private const PROPERTIES = [
        'typo3/reference-tca' => [
            'Types/Index.html#confval-types-columnsoverrides' => 'columnsOverrides',
            'Types/Index.html#confval-types-showitem' => 'showitem',
            // Named like an English word, which is what every prose question
            // carries by accident.
            'Types/Index.html#confval-types-label' => 'label',
        ],
    ];

    private function manuals(): \Closure
    {
        $manuals = [
            'typo3/reference-coreapi' => [
                'ApiOverview/Events/Events/Backend/ModifyInlineElementControlsEvent.html' => 'ModifyInlineElementControlsEvent',
                'ApiOverview/Events/Events/Backend/AfterPageColumnsSelectedForLocalizationEvent.html' => 'AfterPageColumnsSelectedForLocalizationEvent',
                'ApiOverview/Events/Events/Frontend/AfterStdWrapFunctionsExecutedEvent.html' => 'AfterStdWrapFunctionsExecutedEvent',
                'ApiOverview/Assets/Index.html' => 'Assets (CSS, JavaScript, Media)',
                'ApiOverview/Fluid/DevelopCustomViewhelper.html' => 'Developing a custom ViewHelper',
                'ApiOverview/ContentElements/AddingYourOwnContentElements.html' => 'Create a custom content element type (CType)',
                'Testing/FunctionalTesting/Index.html' => 'Functional testing with the TYPO3 testing framework',
            ],
            'typo3/reference-typoscript' => [
                'ContentObjects/Case/Index.html' => 'CASE',
                // The two pages `f:if` used to be answered with. That book
                // titles a function page after the function, so the corpus
                // holds three pages titled `if` and only the book tells them
                // apart.
                'Functions/If.html' => 'if',
                'Guide/TypoScriptFunctions/If/Index.html' => 'if',
            ],
            'typo3/reference-tca' => [
                'ColumnsConfig/Type/Inline/Index.html' => 'IRRE / inline',
                'ColumnsConfig/CommonProperties/FieldInformation/TcaDescription.html' => 'tcaDescription',
                'Types/Index.html' => 'Record types',
            ],
            'typo3/view-helper-reference' => [
                'Global/If.html' => 'If ViewHelper <f:if>',
                'Global/Then.html' => 'Then ViewHelper <f:then>',
                'Global/Else.html' => 'Else ViewHelper <f:else>',
                'Global/Translate.html' => 'Translate ViewHelper <f:translate>',
            ],
        ];

        $inventory = $this->inventory(...);

        return static function (string $url) use ($manuals, $inventory): ?string {
            foreach ($manuals as $manual => $pages) {
                if (!str_contains($url, $manual)) {
                    continue;
                }

                if (str_ends_with($url, 'objects.inv')) {
                    return $inventory($pages, self::PROPERTIES[$manual] ?? []);
                }

                // The one page of the corpus that documents its properties as
                // sections, which is the shape the TCA reference has.
                return str_contains($url, 'Types/Index.html')
                    ? '<html><article role="main"><p>Record types are what a table shows.</p>'
                        . '<section id="confval-types-columnsoverrides"><p>The DataHandler does not take'
                        . ' columnsOverrides into account.</p></section></article></html>'
                    : '<html><article role="main"><p>What this page says.</p></article></html>';
            }

            return null;
        };
    }

    /**
     * A Sphinx inventory carrying those pages, in the form docs.typo3.org
     * publishes one: four comment lines and the objects behind them, compressed
     * with zlib.
     *
     * @param array<string, string> $pages      the path of each page, and its title
     * @param array<string, string> $properties  the anchored uri of each declared property, and its name
     */
    private function inventory(array $pages, array $properties = []): string
    {
        $objects = '';
        foreach ($pages as $path => $title) {
            $objects .= sprintf("%s std:doc -1 %s %s\n", substr($path, 0, -strlen('.html')), $path, $title);
        }
        foreach ($properties as $uri => $name) {
            $objects .= sprintf("%s std:confval -1 %s %s\n", mb_strtolower($name), $uri, $name);
        }

        return "# Sphinx inventory version 2\n"
            . "# Project: TYPO3\n"
            . "# Version: 14.3\n"
            . "# The remainder of this file is compressed using zlib.\n"
            . (string) zlib_encode($objects, ZLIB_ENCODING_DEFLATE);
    }

    /**
     * The index is the inventory, and the title it carries is the one the
     * manual states rather than the one its navigation abbreviated to. Read
     * from the rendered root, this page was "Assets" and no question naming CSS
     * or JavaScript reached it (`D-ANS-065`).
     */
    #[Decision('D-ANS-065')]
    #[Test]
    public function aPageIsIndexedUnderTheTitleTheInventoryStates(): void
    {
        $answer = (new Documentation($this->manuals()))->lookup(['css javascript media'], '14.3', 1);

        self::assertSame('Assets (CSS, JavaScript, Media)', $answer['results'][0]['title']);
    }

    /**
     * What the inventory lists and the manual has no page for. Sphinx renders
     * the "content was removed" template as a document of its own, so it is in
     * every inventory and in no navigation tree, and its two words are ordinary
     * enough to be searched for — `D-ANS-065`.
     */
    #[Decision('D-ANS-065')]
    #[Test]
    public function theNotFoundPageIsNotOneOfTheAnswers(): void
    {
        $documentation = new Documentation($this->inventoryOf([
            'ApiOverview/Assets/Index.html' => 'Assets (CSS, JavaScript, Media)',
            '404.html' => 'Content was removed',
        ]));

        $answer = $documentation->lookup(['removed content'], '14.3', 3);

        self::assertSame([], array_filter(
            $answer['results'],
            static fn(array $result): bool => str_ends_with($result['url'], '404.html'),
        ));
    }

    /**
     * A page where the inventory was asked for is a host that did not answer,
     * not an index. That is what bot protection and a captive portal put a 200
     * in front of (`D-ANS-034`), and the whole corpus would otherwise be one
     * unparsed body away from an empty search that reads like a real one —
     * `D-ANS-065`.
     */
    #[Decision('D-ANS-065')]
    #[Test]
    public function aBodyThatIsNotAnInventoryIsNotAnIndex(): void
    {
        $documentation = new Documentation(static fn(string $url): string => '<html><body>Just a moment…</body></html>');

        $answer = $documentation->lookup(['assets'], '14.3');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('source-not-answering', $answer['unavailable']['cause']);
    }

    /**
     * A transport that answers every manual with those pages.
     *
     * @param array<string, string> $pages the path of each page, and its title
     */
    private function inventoryOf(array $pages): \Closure
    {
        $index = $this->inventory($pages);

        return static fn(string $url): string => str_ends_with($url, 'objects.inv')
            ? $index
            : '<html><article role="main"><p>What this page says.</p></article></html>';
    }

    #[Test]
    public function anUnreachableIndexIsDifferentFromNoMatch(): void
    {
        $documentation = new Documentation(static fn(string $url): ?string => null);

        $answer = $documentation->lookup(['page title'], '13.4');

        self::assertSame('unavailable', $answer['status']);
        self::assertSame([], $answer['results']);
        self::assertNotNull($answer['unavailable']);
        self::assertNotSame('', $answer['unavailable']['reason']);
        // Which of the two unavailable cases it is, because the remedies are
        // opposite: this one is answered by asking again (D-ANS-007).
        self::assertSame('source-not-answering', $answer['unavailable']['cause']);
    }

    /**
     * What the shared call table rests on. Two of its entries ask
     * docs.typo3.org for real so the recording has a filled answer to show, and
     * `ToolContractTest` drives the same entries — so a host that is down has
     * to come back as an answer rather than as a red build (`D-DOC-008`).
     *
     * The data half is what is held here, on both modes. The text half is the
     * one branch every unavailable answer shares, and the entry that asks for
     * TYPO3 999 already drives it without reaching anything.
     */
    #[Decision('D-DOC-008')]
    #[Test]
    public function aSourceThatDidNotAnswerIsStillAnAnswerToTheSchema(): void
    {
        $documentation = new Documentation(static fn(string $url): ?string => null);
        $schema = DocumentationLookup::outputSchema();

        $answers = [
            'search' => $documentation->lookup(['assets'], '14.3'),
            'page' => $documentation->page(
                'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Assets/Index.html',
                '14.3',
            ),
        ];

        foreach ($answers as $mode => $answer) {
            self::assertNotNull($answer['unavailable'], $mode . ' gave no reason');
            self::assertSame('source-not-answering', $answer['unavailable']['cause'], $mode);

            $errors = (new SchemaValidator())->validateAgainstJsonSchema(
                json_decode((string) json_encode($answer, JSON_THROW_ON_ERROR), true),
                $schema,
            );
            self::assertSame([], $errors, $mode . ' broke the output schema: ' . json_encode($errors));
        }
    }

    /**
     * The index is page titles and section paths, and a reporter writes the
     * identifier the stack trace gave them. A session settled Forge #81619 by
     * reducing `stdWrap_override` to the property `override` itself, and said
     * that the step was its own — the feedback of 2026-08-05.
     *
     * Every name offered is a substring of what was typed. Splitting humps as
     * well would answer `getByTag` with "tag", which is a suggestion nothing
     * supports made in the voice of a reading.
     */
    #[Test]
    public function aMissOnAnIdentifierNamesTheBareNamesInsideIt(): void
    {
        Manuals::useReader(fn(string $url): string => $this->inventory([]));

        $answer = Registry::call('typo3_documentation_lookup', [
            'queries' => ['stdWrap_override', 'ContentObjectRenderer::stdWrap_override', 'tt_content', 'getByTag()'],
            'targetVersion' => '14.3',
        ]);

        self::assertSame('empty', $answer->data['status']);
        self::assertSame(
            [
                ['query' => 'stdWrap_override', 'ask' => ['override']],
                [
                    'query' => 'ContentObjectRenderer::stdWrap_override',
                    'ask' => ['stdWrap_override', 'override'],
                ],
            ],
            $answer->data['insteadOf'],
            'a table name and a method with no property half are not reduced',
        );
        self::assertStringContainsString('instead of "stdWrap_override": override', $answer->text);
    }

    /** A query nothing reads as code gets no advice rather than a guess. */
    #[Test]
    public function aMissOnOrdinaryWordsOffersNothingInstead(): void
    {
        Manuals::useReader(fn(string $url): string => $this->inventory([]));

        $answer = Registry::call('typo3_documentation_lookup', [
            'queries' => ['backend layout'],
            'targetVersion' => '14.3',
        ]);

        self::assertSame('empty', $answer->data['status']);
        self::assertArrayNotHasKey('insteadOf', $answer->data);
    }

    /**
     * The other one, and the reason the field exists: a release outside the
     * covered versions is permanent, and nothing is fetched to find that out —
     * `D-ANS-007`.
     */
    #[Decision('D-ANS-007')]
    #[Test]
    public function aVersionOutsideTheCoveredOnesIsNotAskedFor(): void
    {
        $answer = Registry::call('typo3_documentation_lookup', [
            'queries' => ['assets'],
            'targetVersion' => '9.5',
        ])->data;

        self::assertSame('unavailable', $answer['status']);
        self::assertSame('version-not-covered', $answer['unavailable']['cause']);
        self::assertStringContainsString('outside the covered versions', $answer['unavailable']['reason']);
    }
}
