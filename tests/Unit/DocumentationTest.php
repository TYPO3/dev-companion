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
     * The host publishes every page as Markdown beside its HTML, at the same
     * URL with `.md` for `.html`. It is the whole page after the build, with
     * the title in its front matter, and a seventh of the HTML on the wire. So
     * a page is read as that, and the HTML is not asked for — `D-ANS-157`.
     */
    #[Decision('D-ANS-157')]
    #[Test]
    public function aPageIsReadAsTheMarkdownTheHostPublishesBesideIt(): void
    {
        $url = 'https://docs.typo3.org/other/typo3/view-helper-reference/14.3/en-us/Global/If.html';
        $requested = [];
        $documentation = new Documentation(static function (string $requested_) use (&$requested, $url): string {
            $requested[] = $requested_;

            return $requested_ === substr($url, 0, -strlen('.html')) . '.md'
                ? <<<'MARKDOWN'
                    ---
                    title: "If ViewHelper <f:if>"
                    manual: "Fluid ViewHelper Reference"
                    version: "14.3"
                    ---

                    # If ViewHelper `<f:if>`

                    This ViewHelper implements an if/else condition.

                    ```
                    <f:if condition="{foo}">shown</f:if>
                    ```
                    MARKDOWN
                : '<html><article role="main"><h1>Not the page</h1></article></html>';
        });

        $answer = $documentation->page($url, '14.3');

        self::assertSame('answered', $answer['status']);
        self::assertSame('If ViewHelper <f:if>', $answer['results'][0]['title']);
        self::assertSame($url, $answer['results'][0]['url']);
        self::assertStringStartsWith('# If ViewHelper', $answer['results'][0]['content']);
        self::assertStringContainsString('<f:if condition="{foo}">shown</f:if>', $answer['results'][0]['content']);
        self::assertStringNotContainsString('manual: "Fluid', $answer['results'][0]['content']);
        self::assertSame([substr($url, 0, -strlen('.html')) . '.md'], $requested);
    }

    /**
     * A manual has Markdown once it is rendered again since the format
     * arrived, per manual and per version. So a page that answers as HTML
     * alone says the manual has none, and the next page of it is not asked for
     * Markdown first — `D-ANS-157`.
     */
    #[Decision('D-ANS-157')]
    #[Test]
    public function aManualWithoutMarkdownIsReadFromItsHtmlAndAskedOnce(): void
    {
        $base = 'https://docs.typo3.org/m/typo3/reference-coreapi/12.4/en-us/';
        $requested = [];
        $documentation = new Documentation(static function (string $url) use (&$requested): ?string {
            $requested[] = $url;

            return str_ends_with($url, '.html')
                ? '<html><article role="main"><h1>Assets</h1><p>The AssetCollector.</p></article></html>'
                : null;
        });

        $first = $documentation->page($base . 'ApiOverview/Assets/Index.html', '12.4');
        $second = $documentation->page($base . 'ApiOverview/Events/Index.html', '12.4');

        self::assertSame('Assets', $first['results'][0]['title']);
        self::assertStringContainsString('The AssetCollector.', $second['results'][0]['content']);
        self::assertSame([
            $base . 'ApiOverview/Assets/Index.md',
            $base . 'ApiOverview/Assets/Index.html',
            $base . 'ApiOverview/Events/Index.html',
        ], $requested);
    }

    /**
     * A search reads each result page for its lead, and it reads the Markdown
     * where the host has it. The lead is the first paragraphs, and none of the
     * front matter, the heading and the section list every page opens with —
     * `D-ANS-157`.
     */
    #[Decision('D-ANS-157')]
    #[Test]
    public function aSearchExcerptIsTheLeadOfTheMarkdownPage(): void
    {
        $index = $this->inventory([
            'Testing/FunctionalTesting/Index.html' => 'Functional testing',
            'ApiOverview/Events/Index.html' => 'Events and hooks',
        ]);
        $documentation = new Documentation(static fn(string $url): ?string => match (true) {
            str_ends_with($url, 'objects.inv') => $index,
            str_ends_with($url, '.md') => <<<'MARKDOWN'
                ---
                title: "Functional testing"
                ---

                # Functional testing

                **Sections on this page**

                -   [Simple example](https://docs.typo3.org/permalink/t3coreapi:simple-example@14.3)

                ## Simple example

                TYPO3 Core contains more than 2600 functional tests.

                ```php
                final class GeneratorTest extends FunctionalTestCase

                {
                }
                ```

                > [!NOTE]
                > A quote is not the lead.

                Do not hesitate looking around.
                MARKDOWN,
            default => null,
        });

        $answer = $documentation->lookup(['functional testing'], '14.3', 1);

        self::assertSame(
            'TYPO3 Core contains more than 2600 functional tests. Do not hesitate looking around.',
            $answer['results'][0]['excerpt'],
        );
    }

    /**
     * The TCA reference states the machine-readable half of every property as a
     * definition list, and the reader emitted only the terms.
     *
     * `feedback/2026-08-07-132457` read the `type=datetime` page for the
     * default of `nullable` per `dbType`, and got `**Type**`, `**Default**`,
     * `**Path**` and `**Scope**` each named and each empty. That reads as a
     * property with no documented default rather than as a value this reader
     * dropped. The value it needed was one of those cells, and it read
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
        // The property itself keeps its own line. Its definition is the list
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
        // TYPO3 Explained documents everything around TCA and not TCA itself.
        // So this used to come back as the events that carry "inline" and
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
     * A page named after its subject and a page whose title is a long event
     * class name carry the word equally well. The class name carries five other
     * words besides. While no title in the corpus was long enough to weigh
     * less, the two were worth the same. The tie went to whichever manual came
     * first in the index (`D-ANS-029`) — `D-ANS-032`.
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
     * The host publishes the manuals of the core under `/m/` and this one
     * elsewhere. So a search that built every base the same way reached three
     * books, none of which documents a ViewHelper. Whichever of them carried
     * the word answered the question (`D-ANS-023`). A base that is wrong is
     * silent: the index does not answer and the book is absent. So this holds
     * that the search reaches its pages, and reaches them at their own base —
     * `D-ANS-026`.
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
     * That book names a page after the tag. So `Global/If.html` has the title
     * "if", and the only word of `f:if` that can reach it is two characters
     * long. The floor dropped every one of those before the search, which is
     * what left `Global/Else.html` as the answer. The right family and the
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
     * dilution reference reaches. Three pages of the corpus have the title
     * `if`. So all three weigh full, all three matched the title, and no field
     * weight separates identical titles (`D-ANS-032`). The query says which
     * book in the `f:` it carries (`D-ANS-036`).
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
     * says nothing in a sentence. That is not what it does behind a namespace
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
     * score. So a root that is down would otherwise leave such a query with no
     * candidates. It would report "no match" for a reason the caller cannot see
     * — `D-ANS-036`.
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
     * calls on it. The manual that documents that property is a handful of
     * large pages, and the property is a section of one of them. The writer
     * registers every such section, and this is what reads them — `D-ANS-144`.
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
        // wherever it stands in one, and answers for nothing but itself.
        $prose = $documentation->lookup(['the label of a record type in the backend'], '14.3', 3);
        self::assertNotContains('label', array_column($prose['results'], 'title'));

        $alone = $documentation->lookup(['showitem'], '14.3', 2);
        self::assertSame('showitem', $alone['results'][0]['title']);
    }

    /**
     * TYPO3 Explained declares the classes, the methods and the console
     * commands it documents, with the anchor of the section that does. So a
     * query that names one reaches that section the way it reaches a property.
     * A method is reached as `Class::method` and never by its own name alone,
     * which 48 event pages share — `D-ANS-158`.
     */
    #[Decision('D-ANS-158')]
    #[Test]
    public function aDeclaredClassMethodOrCommandIsReachedByItsOwnName(): void
    {
        $base = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/';
        $index = $this->inventory(
            [
                'ApiOverview/Assets/Index.html' => 'Assets (CSS, JavaScript, media)',
                'ApiOverview/CommandControllers/ListCommands.html' => 'List of commands',
                'ApiOverview/Events/Events/Backend/AfterPageTreeItemsPreparedEvent.html' => 'AfterPageTreeItemsPreparedEvent',
            ],
            [],
            [
                ['php:class', 'ApiOverview/Assets/Index.html#typo3-cms-core-page-assetcollector', '\\TYPO3\\CMS\\Core\\Page\\AssetCollector'],
                ['php:method', 'ApiOverview/Assets/Index.html#typo3-cms-core-page-assetcollector-addjavascript', '\\TYPO3\\CMS\\Core\\Page\\AssetCollector::addJavaScript'],
                ['php:method', 'ApiOverview/Events/Events/Backend/AfterPageTreeItemsPreparedEvent.html#getrequest', '\\TYPO3\\Backend\\Event\\AfterPageTreeItemsPreparedEvent::getRequest'],
                ['std:console:command', 'ApiOverview/CommandControllers/ListCommands.html#console-command-cache-flushtags', 'vendor/bin/typo3 cache:flushtags'],
            ],
        );
        $documentation = new Documentation(static fn(string $url): string => str_ends_with($url, 'objects.inv')
            ? $index
            : '<html><article role="main"><p>What this page says.</p></article></html>');

        $class = $documentation->lookup(['AssetCollector'], '14.3', 1)['results'][0];
        self::assertSame('\\TYPO3\\CMS\\Core\\Page\\AssetCollector', $class['title']);
        self::assertSame($base . 'ApiOverview/Assets/Index.html#typo3-cms-core-page-assetcollector', $class['url']);

        $qualified = $documentation->lookup(['\\TYPO3\\CMS\\Core\\Page\\AssetCollector'], '14.3', 1)['results'][0];
        self::assertSame($class['url'], $qualified['url']);

        $method = $documentation->lookup(['AssetCollector::addJavaScript'], '14.3', 1)['results'][0];
        self::assertSame($base . 'ApiOverview/Assets/Index.html#typo3-cms-core-page-assetcollector-addjavascript', $method['url']);

        $alone = $documentation->lookup(['getRequest'], '14.3', 3);
        self::assertNotContains('getrequest', array_map(
            static fn(array $result): string => (string) parse_url($result['url'], PHP_URL_FRAGMENT),
            $alone['results'],
        ));

        $command = $documentation->lookup(['run vendor/bin/typo3 cache:flushtags after a deploy'], '14.3', 1)['results'][0];
        self::assertSame('vendor/bin/typo3 cache:flushtags', $command['title']);
        self::assertSame($base . 'ApiOverview/CommandControllers/ListCommands.html#console-command-cache-flushtags', $command['url']);
    }

    /**
     * The inventory lists every heading of a page with its anchor. A page is
     * worth its title and its best heading, so the question a title does not
     * carry reaches the page whose section does. The answer names that
     * section, sends the caller to its anchor, and says the words matched
     * there — `D-ANS-159`, `R-DOC-002`.
     */
    #[Decision('D-ANS-159')]
    #[Requirement('R-DOC-002')]
    #[Test]
    public function aQuestionTheTitleDoesNotCarryReachesThePageWhoseHeadingDoes(): void
    {
        $base = 'https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/';
        $index = $this->inventory(
            [
                'Administration/Installation/SystemRequirements/Index.html' => 'System requirements for running TYPO3',
                'Administration/Deployment/Configuration/Index.html' => 'Configuration per environment',
                'ApiOverview/Events/Index.html' => 'Events and hooks',
            ],
            [],
            [
                ['std:title', 'Administration/Installation/SystemRequirements/Index.html#htaccess', 'Apache .htaccess configuration file'],
                ['std:title', 'Administration/Installation/SystemRequirements/Index.html#php', 'PHP'],
                // The page's own title again, which Sphinx lists as a section too.
                ['std:title', 'Administration/Deployment/Configuration/Index.html#configuration', 'Configuration per environment'],
            ],
        );
        $documentation = new Documentation(static fn(string $url): ?string => match (true) {
            str_ends_with($url, 'objects.inv') => str_contains($url, 'reference-coreapi') ? $index : null,
            default => '<html><article role="main"><p>The page opens on requirements.</p>'
                . '<section id="apache"><a id="htaccess"></a><h4>Apache</h4><p>This file configures the rewrite rules.</p></section></article></html>',
        });

        $answer = $documentation->lookup(['apache htaccess configuration'], '14.3', 2);

        $first = $answer['results'][0];
        self::assertSame('System requirements for running TYPO3', $first['title']);
        self::assertSame('Apache .htaccess configuration file', $first['section']);
        self::assertSame($base . 'Administration/Installation/SystemRequirements/Index.html#htaccess', $first['url']);
        self::assertContains(['term' => 'apache', 'field' => 'section'], $first['matched']);
        // The excerpt is the section's own prose, read from around its anchor.
        self::assertStringContainsString('rewrite rules', $first['excerpt']);
        self::assertStringNotContainsString('opens on requirements', $first['excerpt']);

        // A page whose title answers the question is sent to as a page: its
        // section is its title, and the URL carries no anchor.
        $second = $answer['results'][1];
        self::assertSame('Configuration per environment', $second['title']);
        self::assertSame('Configuration per environment', $second['section']);
        self::assertSame($base . 'Administration/Deployment/Configuration/Index.html', $second['url']);
    }

    /**
     * A heading that repeats the title's word says nothing the title did not.
     * So the page with the repeating heading is not the better answer, which
     * is what kept `f:if` on the If ViewHelper rather than on the one whose
     * section is titled "then / else" — `D-ANS-159`.
     */
    #[Decision('D-ANS-159')]
    #[Test]
    public function aHeadingThatRepeatsTheTitleAddsNothing(): void
    {
        $index = $this->inventory(
            [
                'Global/If.html' => 'If ViewHelper <f:if>',
                'Global/Security/IfAuthenticated.html' => 'IfAuthenticated ViewHelper <f:security.ifAuthenticated>',
                'Global/Then.html' => 'Then ViewHelper <f:then>',
            ],
            [],
            [
                ['std:title', 'Global/Security/IfAuthenticated.html#if-then', 'if with then'],
                ['std:title', 'Global/Security/IfAuthenticated.html#if-else', 'if with else'],
            ],
        );
        $documentation = new Documentation(static fn(string $url): string => str_ends_with($url, 'objects.inv')
            ? $index
            : '<html><article role="main"><p>What this page says.</p></article></html>');

        $answer = $documentation->lookup(['f:if'], '14.3', 2);

        self::assertSame('If ViewHelper <f:if>', $answer['results'][0]['title']);
        self::assertSame('IfAuthenticated ViewHelper <f:security.ifAuthenticated>', $answer['results'][1]['section']);
        self::assertNotContains('section', array_column($answer['results'][1]['matched'], 'field'));
    }

    #[Decision('D-ANS-065')]
    #[Test]
    public function anApiIdentifierReachesThePageThatIsNotNamedAfterIt(): void
    {
        // Nothing in a table of contents has the name AssetCollector or
        // FunctionalTestCase. The pages that answer them carry the name of
        // their subject, which is assets and functional tests — `D-ANS-065`.
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
     * each. Nothing in them showed that the word that names the subject had
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

    /** Nothing searched for a page, so nothing matched it. */
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
     * And it covers no query either, which is null rather than zero. The caller
     * asked nothing, so there is no share to report (`D-ANS-051`).
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
     * title is the query covers all of it. The ones that carry one word of a
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
     * The reported coverage is the one of the question the page stays for, like
     * the match beside it. Not of whichever query came last. Both pages here
     * come back for the query that names them — `D-ANS-051`.
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
     * A thin answer gets a label and not an empty list. The floor the rule
     * search drops a section below has no value here that does both. One that
     * empties the six collisions `feedback/2026-08-03-164734` reported and
     * returns the page that answers a three-word question. So this page still
     * answers while the number says how little of the question it carries
     * (`D-ANS-051`).
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
     * And the text tells the caller, because a share in a payload is not a
     * warning. The answer this feedback reported as the expensive kind of wrong
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
     * The tables of contents as the host publishes them, cut down to the pages
     * this is about. The ones that answer, and the ones that used to answer
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
                // The two pages `f:if` used to get as its answer. That book
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
     * A Sphinx inventory with those pages, in the form docs.typo3.org publishes
     * one: four comment lines and the objects behind them, compressed with
     * zlib.
     *
     * @param array<string, string> $pages      the path of each page, and its title
     * @param array<string, string> $properties  the anchored uri of each declared property, and its name
     * @param list<array{string, string, string}> $declared  the role, the anchored uri and the display name of anything else the manual declares
     */
    private function inventory(array $pages, array $properties = [], array $declared = []): string
    {
        $objects = '';
        foreach ($pages as $path => $title) {
            $objects .= sprintf("%s std:doc -1 %s %s\n", substr($path, 0, -strlen('.html')), $path, $title);
        }
        foreach ($properties as $uri => $name) {
            $objects .= sprintf("%s std:confval -1 %s %s\n", mb_strtolower($name), $uri, $name);
        }
        foreach ($declared as [$role, $uri, $display]) {
            $objects .= sprintf("%s %s -1 %s %s\n", (string) strtok($uri, '#'), $role, $uri, $display);
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
     * from the rendered root, this page was "Assets" and no question that named
     * CSS or JavaScript reached it (`D-ANS-065`).
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
     * the template for removed content as a document of its own. So it is in
     * every inventory and in no navigation tree, and its two words are ordinary
     * enough for a search — `D-ANS-065`.
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
     * A page where the inventory should be is a host that did not answer, not
     * an index. That is what bot protection and a captive portal put a 200 in
     * front of (`D-ANS-034`). The whole corpus would otherwise be one unparsed
     * body away from an empty search that reads like a real one — `D-ANS-065`.
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
        // opposite: a second call answers this one (D-ANS-007).
        self::assertSame('source-not-answering', $answer['unavailable']['cause']);
    }

    /**
     * What the shared call table rests on. Two of its entries ask
     * docs.typo3.org for real so the record has a filled answer to show, and
     * `ToolContractTest` drives the same entries. So a host that is down has to
     * come back as an answer rather than as a red build (`D-DOC-008`).
     *
     * This holds the data half, on both modes. The text half is the one branch
     * every unavailable answer shares, and the entry that asks for TYPO3 999
     * already drives it and reaches nothing.
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
     * identifier the stack trace gave them. A session settled Forge #81619 with
     * a cut of `stdWrap_override` down to the property `override` itself. It
     * said that the step was its own — the feedback of 2026-08-05.
     *
     * Every name on offer is a substring of what the caller typed. A split on
     * humps as well would answer `getByTag` with "tag", which is a suggestion
     * nothing supports in the voice of a read.
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
     * The other one, and the reason the field exists. A release outside the
     * covered versions is permanent, and the answer needs no fetch to find that
     * out — `D-ANS-007`.
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
