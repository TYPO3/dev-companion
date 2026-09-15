<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\DevCompanion\Manual\Manuals;
use TYPO3\DevCompanion\Manual\Permalink;
use TYPO3\DevCompanion\Tests\Support\Decision;
use TYPO3\DevCompanion\Tests\Support\Requirement;
use TYPO3\DevCompanion\Tool\Registry;
use TYPO3\DevCompanion\Upkeep\Cli;

#[Requirement('R-DOC-001')]
final class PermalinkTest extends TestCase
{
    protected function tearDown(): void
    {
        Manuals::useReader(null);
    }

    /**
     * The TCA reference at `main`, in the four objects the report's own
     * question turns on. The property it looked for, the label Sphinx generates
     * from that property's anchor, the section above it, and the page that
     * carries all three.
     *
     * @var array<int, array{0: string, 1: string, 2: string}>
     */
    private const TCA = [
        ['columns-onchange', 'std:confval', 'Columns/Index.html#confval-columns-onchange'],
        ['confval-columns-onchange', 'std:label', 'Columns/Index.html#confval-columns-onchange'],
        ['columns-properties', 'std:label', 'Columns/Index.html#columns-properties'],
        ['Columns/Index', 'std:doc', 'Columns/Index.html'],
    ];

    #[Decision('D-ANS-118')]
    #[Test]
    public function anIdentifierResolvesToThePageAndAnchorTheInventoryNames(): void
    {
        $answer = $this->lookup(['t3tca:columns-onchange'], []);

        self::assertSame('answered', $answer['status']);
        $resolved = $answer['identifiers'][0];
        self::assertTrue($resolved['resolved']);
        self::assertSame(
            'https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange',
            $resolved['url'],
        );
        self::assertSame('confval-columns-onchange', $resolved['anchor']);
        self::assertSame(['std:confval'], $resolved['roles']);
    }

    /**
     * `t3tca:columns-onchange` and `t3tca:confval-columns-onchange` both answer
     * 307 with the same anchor. Which one a patch should use is the review
     * question the session that reported it had to invent a rule for. The
     * inventory tells them apart. The property is the `std:confval` the manual
     * declares, the prefixed form is the label Sphinx generates from its anchor
     * — `D-ANS-118`.
     */
    #[Decision('D-ANS-118')]
    #[Test]
    public function bothSpellingsOfAConfigurationValueAnswerWithTheDeclaredOne(): void
    {
        $answer = $this->lookup(['t3tca:columns-onchange', 't3tca:confval-columns-onchange'], []);

        [$declared, $generated] = $answer['identifiers'];
        self::assertSame($declared['url'], $generated['url']);
        self::assertSame('columns-onchange', $declared['preferred']);
        self::assertSame('columns-onchange', $generated['preferred']);
        self::assertSame(
            [['name' => 'confval-columns-onchange', 'roles' => ['std:label']]],
            $declared['alsoKnownAs'],
        );
    }

    /**
     * The identifier space is the inventory minus its pages.
     *
     * `Documentation` reads `std:doc` and nothing else, because what it
     * searches is a table of contents. This reads everything else, because that
     * is what the permalink route accepts. `t3tca:Columns/Index` is a page of
     * the TCA reference and answers 404 on the host — `D-ANS-119`.
     */
    #[Decision('D-ANS-119')]
    #[Test]
    public function aPageOfAManualIsNotOneOfItsIdentifiers(): void
    {
        $answer = $this->lookup(['t3tca:Columns/Index'], []);

        self::assertSame('empty', $answer['status']);
        self::assertFalse($answer['identifiers'][0]['resolved']);
        self::assertStringContainsString('registers no "Columns/Index"', (string) $answer['identifiers'][0]['reason']);
    }

    /**
     * The shortcodes that reach a manual and the ones that reach none.
     *
     * A system extension goes by its Composer package in either form and needs
     * no entry in the list. The host publishes the manual of a package under
     * that package's own name. An extension outside the core gets no answer at
     * all. Its manual has versions per its own releases, so a TYPO3 version
     * would select the wrong branch of it — `D-ANS-120`.
     *
     * @return iterable<string, array{0: string, 1: ?string}>
     */
    public static function everyShapeOfShortcode(): iterable
    {
        yield 'a named manual' => ['t3tca', 'typo3/reference-tca'];
        yield 'a named manual in another collection' => ['t3viewhelper', 'typo3/view-helper-reference'];
        yield 'the changelog, which no package name reaches' => ['changelog', 'typo3/cms-core'];
        yield 'a system extension as its package' => ['typo3/cms-felogin', 'typo3/cms-felogin'];
        yield 'a system extension in the dashed spelling' => ['typo3-cms-felogin', 'typo3/cms-felogin'];
        yield 'one whose key carries dashes of its own' => ['typo3-cms-fluid-styled-content', 'typo3/cms-fluid-styled-content'];
        yield 'the theme the core ships' => ['typo3-theme-camino', 'typo3/theme-camino'];
        yield 'the same manual as the route reads it' => ['T3TCA', 'typo3/reference-tca'];
        yield 'somebody else\'s extension' => ['georgringer-news', null];
        yield 'a word that names no manual' => ['quantumflux', null];
    }

    #[Decision('D-ANS-120')]
    #[Test]
    #[DataProvider('everyShapeOfShortcode')]
    public function aShortcodeReachesTheManualItNames(string $shortcode, ?string $document): void
    {
        self::assertSame($document, Manuals::byShortcode($shortcode)['document'] ?? null);
    }

    /**
     * A caller cannot tell a name that is not registered from a shortcode
     * nothing here places, and the two ask for opposite reactions. The first is
     * a wrong link and the second is a manual this server does not answer for.
     */
    #[Decision('D-ANS-120')]
    #[Test]
    public function anUnknownShortcodeSaysWhichOnesAreKnown(): void
    {
        $answer = $this->lookup(['quantumflux:start'], []);

        self::assertSame('unavailable', $answer['status'], 'no manual was read, so nothing was asked of the host');
        $miss = $answer['identifiers'][0];
        self::assertFalse($miss['resolved']);
        self::assertNull($miss['manual']);
        self::assertStringContainsString('t3tca', (string) $miss['reason']);
        self::assertStringContainsString('typo3/cms-felogin', (string) $miss['reason']);
    }

    /**
     * The host answers a branch it does not publish with a redirect to `main`
     * and a 200. So an identifier can read as resolved for a release whose
     * manual nothing reached. The inventory says which branch actually
     * answered, and the answer passes that on rather than the status —
     * `R-DOC-001`, `D-ANS-118`.
     */
    #[Requirement('R-DOC-001')]
    #[Decision('D-ANS-118')]
    #[Test]
    public function aManualServedFromAnotherBranchSaysWhichOneAnswered(): void
    {
        $answer = $this->lookup(['t3tca:columns-onchange'], [], 'main');

        $resolved = $answer['identifiers'][0];
        self::assertTrue($resolved['resolved']);
        self::assertSame('14.3', $resolved['branch'], 'the branch that was asked for');
        self::assertSame('main', $resolved['answeredBranch'], 'the branch the manual says it is');
        // The URL is the one that exists rather than the composed one, so a
        // caller who pastes it lands where the answer came from.
        self::assertStringContainsString('/reference-tca/main/en-us/', (string) $resolved['url']);
        self::assertStringContainsString(
            'this manual has no 14.3 branch',
            Registry::call('typo3_permalink_lookup', [
                'identifiers' => ['t3tca:columns-onchange'],
                'targetVersion' => '14.3',
            ])->text,
        );
    }

    /** An identifier may pin a branch of its own, which is the form the permalink route publishes. */
    #[Test]
    public function anIdentifierPinsItsOwnBranch(): void
    {
        $asked = [];
        $answer = (new Permalink($this->manual($asked)))->lookup(['t3tca:columns-onchange@13.4'], [], '14.3');

        self::assertSame('13.4', $answer['identifiers'][0]['branch']);
        self::assertSame([], array_filter($asked, static fn(string $url): bool => !str_contains($url, '/13.4/')));
    }

    /**
     * The same table read the other way, on the URL the session that reported
     * it had in hand. `Columns/Properties/OnChange.html` is a 404 today and
     * nothing on the page it left behind says where the subject went. So the
     * lookup matches the words of the URL against the names — `D-ANS-118`.
     */
    #[Decision('D-ANS-118')]
    #[Test]
    public function aUrlThatIsGoneIsAnsweredWithTheNamesCarryingItsWords(): void
    {
        $answer = $this->lookup([], [
            'https://docs.typo3.org/m/typo3/reference-tca/11.5/en-us/Columns/Properties/OnChange.html',
        ]);

        $reversed = $answer['urls'][0];
        self::assertSame('t3tca', $reversed['shortcode']);
        self::assertSame('11.5', $reversed['urlBranch'], 'the branch the link in the code points at');
        self::assertSame('14.3', $reversed['branch'], 'and the one it was answered for');
        self::assertSame([], $reversed['identifiers']);
        self::assertSame('columns-onchange', $reversed['nearest'][0]['name']);
    }

    #[Decision('D-ANS-118')]
    #[Test]
    public function aUrlThatStillExistsIsAnsweredWithTheIdentifiersReachingIt(): void
    {
        $answer = $this->lookup([], [
            'https://docs.typo3.org/m/typo3/reference-tca/14.3/en-us/Columns/Index.html#confval-columns-onchange',
        ]);

        self::assertSame('answered', $answer['status']);
        self::assertSame(
            ['columns-onchange', 'confval-columns-onchange'],
            array_column($answer['urls'][0]['identifiers'], 'name'),
        );
    }

    #[Test]
    public function aUrlOfAnotherHostIsSaidToBeOne(): void
    {
        $answer = $this->lookup([], ['https://example.org/manual/page.html']);

        self::assertSame('unavailable', $answer['status']);
        self::assertNull($answer['urls'][0]['manual']);
        self::assertStringContainsString('Not a manual URL of this host', (string) $answer['urls'][0]['reason']);
    }

    #[Test]
    public function aHostThatDidNotAnswerIsNotAnIdentifierThatDoesNotResolve(): void
    {
        $answer = (new Permalink(static fn(string $url): ?string => null))
            ->lookup(['t3tca:columns-onchange'], [], '14.3');

        self::assertSame('unavailable', $answer['status']);
        self::assertNotNull($answer['unavailable']);
        self::assertSame('source-not-answering', $answer['unavailable']['cause']);
        self::assertFalse($answer['identifiers'][0]['resolved']);
        self::assertNull($answer['identifiers'][0]['answeredBranch']);
    }

    #[Test]
    public function aVersionOutsideTheCoveredOnesIsNotAsked(): void
    {
        $data = Registry::call('typo3_permalink_lookup', [
            'identifiers' => ['t3tca:columns-onchange'],
            'targetVersion' => '999',
        ])->data;

        self::assertSame('unavailable', $data['status']);
        self::assertSame('version-not-covered', $data['unavailable']['cause']);
        self::assertSame([], $data['identifiers']);
    }

    #[Test]
    public function aCallWithNothingToLookUpIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Registry::call('typo3_permalink_lookup', ['targetVersion' => '14.3']);
    }

    /**
     * The list lives here and nothing on the host publishes the set, so what
     * keeps it true is each manual's own claim. The theme writes the
     * `interlink-shortcode` of the manual's `guides.xml` into every page it
     * renders — `D-ANS-120`.
     */
    #[Decision('D-ANS-120')]
    #[Test]
    public function aManualThatRenamedItsShortcodeIsReported(): void
    {
        $renamed = 'https://docs.typo3.org/m/typo3/reference-tca/main/en-us/Index.html';
        Manuals::useReader(static fn(string $url): string => sprintf(
            '<html data-interlink-shortcode="%s"></html>',
            $url === $renamed ? 't3tcareference' : self::shortcodeOf($url),
        ));

        $buffer = new BufferedOutput();
        $exit = Cli::application()->doRun(new StringInput('manuals:check'), $buffer);

        self::assertSame(1, $exit);
        self::assertStringContainsString('addressed as t3tca here and declares t3tcareference', $buffer->fetch());
    }

    /** What the manual at one URL declares, which is what this list says it does. */
    private static function shortcodeOf(string $url): string
    {
        foreach (Manuals::all() as $manual) {
            if (str_contains($url, '/' . $manual['document'] . '/')) {
                return $manual['shortcode'];
            }
        }

        return '';
    }

    /**
     * @param list<string> $identifiers
     * @param list<string> $urls
     * @return array<string, mixed>
     */
    private function lookup(array $identifiers, array $urls, string $branch = '14.3'): array
    {
        $asked = [];
        Manuals::useReader($this->manual($asked, $branch));

        return (new Permalink())->lookup($identifiers, $urls, '14.3');
    }

    /**
     * The TCA reference and nothing else, as the host serves it.
     *
     * @param array<int, string> $asked
     * @return \Closure(string): ?string
     */
    private function manual(array &$asked, string $branch = '14.3'): \Closure
    {
        $objects = '';
        foreach (self::TCA as [$name, $role, $uri]) {
            $objects .= sprintf("%s %s -1 %s -\n", $name, $role, $uri);
        }
        $inventory = "# Sphinx inventory version 2\n"
            . "# Project: TCA Reference\n"
            . '# Version: ' . $branch . "\n"
            . "# The remainder of this file is compressed using zlib.\n"
            . (string) zlib_encode($objects, ZLIB_ENCODING_DEFLATE);

        return static function (string $url) use (&$asked, $inventory): ?string {
            $asked[] = $url;

            return str_contains($url, '/reference-tca/') ? $inventory : null;
        };
    }
}
