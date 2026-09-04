<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Manual;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Search\TermSearch;
use TYPO3\DevCompanion\Search\Text;

/**
 * Searches and reads the official, versioned TYPO3 manuals.
 *
 * docs.typo3.org publishes the Sphinx inventory of every manual beside it. That
 * is the public index used here; /search/ is deliberately not called because
 * robots.txt excludes it, and there is no search index to read instead. The
 * selected result pages are then read for a short excerpt. A canonical result
 * URL can then be handed back to read that page as text, without re-deriving
 * its API from an installed source tree.
 */
final class Documentation
{
    /**
     * The page of a manual that is not one, and the one page an inventory lists
     * that a caller must never be sent to.
     */
    private const NOT_A_PAGE = '404.html';

    /**
     * What an inventory lists that this searches: the pages, which is what a
     * table of contents is made of.
     *
     * The other roles are the addressable objects inside those pages, they are
     * what a permalink identifier names, and `Manual\Permalink` reads them
     * (`R-DOC-001`, `D-ANS-119`).
     */
    private const PAGE = 'std:doc';

    /**
     * The other role this searches: a configuration value the manual declares,
     * with the anchor of the section documenting it.
     *
     * The TCA reference is a handful of large pages carrying hundreds of
     * properties as sections, so nothing in its table of contents is called
     * `columnsOverrides` and a query naming that property reached six pages
     * that never mention it — `D-ANS-144`. `Manual\Permalink` reads the same
     * objects for the other question asked of them.
     */
    private const PROPERTY = 'std:confval';

    /**
     * What a declared property has to look like before a query word may reach
     * it: an inner capital, an underscore, or a dot.
     *
     * A property named like an English word is one every prose question carries
     * by accident — `template`, `title`, `default` — and admitting those put
     * three of the seven ranked questions of `D-ANS-032` behind sections nobody
     * asked for. Written the way code is, it is the subject rather than a word
     * of the sentence.
     */
    private const IDENTIFIER = '/\p{Ll}\p{Lu}|[_.]/u';

    /**
     * What a page is searched by. The title is what it is called; the path is
     * the section it sits in, which is the other half of what a table of
     * contents knows — "Assets" says little, `ApiOverview/Assets/Index.html`
     * says where it belongs. The manual is the third thing a caller names
     * without meaning to name a page: a question about TCA belongs in the TCA
     * reference before it belongs in any page of another manual that carries
     * the word.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['title' => 4, 'path' => 2, 'manual' => 2];

    /**
     * The ordinary field of this corpus, which is what a longer one is measured
     * against. A title runs to about four words over the pages the four manuals
     * index and a path to seven, so a path is diluted against a title by design.
     *
     * It was 12, longer than any title the rendered navigation carried, so no
     * title was ever diluted and the field length did nothing. Not below 3
     * either: `Fluid ViewHelper Reference` is three words and the other three
     * books are two, so a smaller reference weighs the books by the length of
     * their names — `D-ANS-065`.
     */
    private const UNDILUTED_WORDS = 3;

    /** What this reader prints as a block of its own, which is also what makes a `dd` more than a value. */
    private const BLOCKS = './/h1|.//h2|.//h3|.//h4|.//h5|.//h6|.//p|.//pre|.//li|.//dt|.//dd';

    private readonly Fetch $reader;

    private readonly Inventory $inventory;

    /** @param (\Closure(string): ?string)|null $fetch */
    public function __construct(?\Closure $fetch = null)
    {
        $this->reader = new Fetch($fetch ?? Manuals::reader());
        $this->inventory = new Inventory($this->reader);
    }

    /**
     * @param list<string> $queries
     * @return array{
     *   mode: 'search'|'page',
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{
     *     title: string,
     *     url: string,
     *     document: string,
     *     documentTitle: string,
     *     documentVersion: string,
     *     section: string,
     *     excerpt: string,
     *     content: string,
     *     coverage: float|null,
     *     matched: list<array{term: string, field: string}>
     *   }>,
     *   unavailable: array{cause: string, reason: string}|null
     * }
     */
    public function lookup(array $queries, string $targetVersion, int $limit = 6): array
    {
        $queries = array_values(array_filter(array_map(trim(...), $queries), static fn(string $query): bool => $query !== ''));
        $pages = [];
        $indexed = [];

        $named = self::identifiers($queries);

        foreach (Manuals::searched() as $document => $manual) {
            $base = self::base($document, $targetVersion);
            $index = $this->index($base);
            if ($index === null) {
                continue;
            }
            $indexed[$document] = true;

            foreach ([...$index, ...$this->properties($base, $named)] as $page) {
                $pages[$document . '|' . $page['url']] = [
                    'score' => 0,
                    'coverage' => 0.0,
                    'matched' => [],
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'document' => $document,
                    'documentTitle' => $manual['title'],
                    'searchable' => [
                        'title' => self::split($page['title']),
                        'path' => self::split($page['path']),
                        'manual' => $manual['title'],
                    ],
                ];
            }
        }

        if ($indexed === []) {
            return $this->answer('search', 'unavailable', $queries, $targetVersion, [], [
                'cause' => 'source-not-answering',
                'reason' => 'The versioned TYPO3 documentation indexes could not be reached.',
            ]);
        }

        // Every manual is weighed against every other manual's pages, because
        // what makes a term worth something is how few of all the pages there
        // are carry it.
        $searchable = array_column($pages, 'searchable');
        foreach ($queries as $query) {
            $book = self::book($query, $indexed);
            $weights = TermSearch::weights(TermSearch::terms(self::split($query)), $searchable);
            $askedFor = array_sum($weights);
            $scores = [];
            $covered = [];
            $matched = [];
            foreach ($pages as $key => $page) {
                if ($book !== null && $page['document'] !== $book) {
                    continue;
                }
                [$scores[$key], $covered[$key], $matched[$key]] = TermSearch::score(
                    $page['searchable'],
                    $weights,
                    self::FIELD_WEIGHTS,
                    self::UNDILUTED_WORDS,
                );
            }

            // Each query is its own question and its scores are its own scale —
            // one made of common words scores everything higher than one made of
            // rare ones. So a page is worth how well it answers a question
            // rather than what that question's words happen to be worth, and it
            // keeps the best question it answers rather than the sum of the ones
            // it brushes past. Two questions in one call otherwise return one
            // question's pages twice over.
            $best = max([0, ...$scores]);
            foreach ($scores as $key => $score) {
                $relative = $best === 0 ? 0 : (int) round($score / $best * 1000);
                if ($relative <= $pages[$key]['score']) {
                    continue;
                }
                // The match reported is the one of the question the page is
                // kept for, so it is the words of that query rather than of
                // whichever one was passed last. The coverage is that query's
                // too: the share of its weight this page carries, which the
                // score has always returned and this lookup discarded in the
                // destructuring. It labels rather than filters — `D-ANS-051`.
                $pages[$key]['score'] = $relative;
                $pages[$key]['matched'] = $matched[$key];
                $pages[$key]['coverage'] = $askedFor > 0.0 ? $covered[$key] / $askedFor : 0.0;
            }
        }

        $candidates = array_filter($pages, static fn(array $page): bool => $page['score'] > 0);
        uasort($candidates, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
        $results = [];
        foreach (array_slice($candidates, 0, $limit) as $candidate) {
            $page = $this->get($candidate['url']);
            $results[] = [
                'title' => $candidate['title'],
                'url' => $candidate['url'],
                'document' => $candidate['document'],
                'documentTitle' => $candidate['documentTitle'],
                'documentVersion' => $targetVersion,
                'section' => $candidate['title'],
                'excerpt' => $page === null ? '' : $this->excerpt($page, (string) parse_url($candidate['url'], PHP_URL_FRAGMENT)),
                'content' => '',
                'coverage' => round($candidate['coverage'], 3),
                'matched' => self::matched($candidate['matched']),
            ];
        }

        return $this->answer('search', $results === [] ? 'empty' : 'answered', $queries, $targetVersion, $results, null);
    }

    /**
     * Read one canonical URL returned by lookup(), on the same version.
     *
     * @return array{
     *   mode: 'search'|'page',
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{
     *     title: string,
     *     url: string,
     *     document: string,
     *     documentTitle: string,
     *     documentVersion: string,
     *     section: string,
     *     excerpt: string,
     *     content: string,
     *     coverage: float|null,
     *     matched: list<array{term: string, field: string}>
     *   }>,
     *   unavailable: array{cause: string, reason: string}|null
     * }
     */
    public function page(string $url, string $targetVersion): array
    {
        $owner = null;
        foreach (Manuals::searched() as $document => $manual) {
            $base = self::base($document, $targetVersion);
            if (str_starts_with($url, $base) && str_ends_with(explode('#', $url, 2)[0], '.html')) {
                $owner = ['document' => $document, 'title' => $manual['title']];
                break;
            }
        }
        if ($owner === null) {
            throw new \InvalidArgumentException(
                'page must be a canonical result URL for targetVersion from typo3_documentation_lookup',
            );
        }

        $html = $this->get($url);
        if ($html === null) {
            return $this->answer('page', 'unavailable', [], $targetVersion, [], [
                'cause' => 'source-not-answering',
                'reason' => 'The selected TYPO3 documentation page could not be reached.',
            ]);
        }

        $content = $this->content($html);
        $title = $this->title($html);
        if ($content === '') {
            return $this->answer('page', 'empty', [], $targetVersion, [], null);
        }

        return $this->answer('page', 'answered', [], $targetVersion, [[
            'title' => $title,
            'url' => $url,
            'document' => $owner['document'],
            'documentTitle' => $owner['title'],
            'documentVersion' => $targetVersion,
            'section' => $title,
            'excerpt' => substr($content, 0, 700),
            'content' => $content,
            // Nothing was asked, so there is no query to cover — the null
            // beside the empty match, rather than a zero that says this page
            // answers nothing.
            'coverage' => null,
            'matched' => [],
        ]], null);
    }

    /** Where one manual is published, at one version. */
    private static function base(string $document, string $targetVersion): string
    {
        return Manuals::base(Manuals::searched()[$document]['collection'], $document, $targetVersion);
    }

    /**
     * The properties this manual declares that a question named, with the
     * anchor of the section documenting each.
     *
     * @param list<string> $named the lowercased names a question may reach one by
     * @return list<array{title: string, path: string, url: string}>
     */
    private function properties(string $base, array $named): array
    {
        $inventory = $named === [] ? null : $this->inventory->of($base);
        if ($inventory === null) {
            return [];
        }

        $properties = [];
        $seen = [];
        foreach ($inventory['objects'] as $object) {
            if ($object['role'] !== self::PROPERTY) {
                continue;
            }
            // `-` is what the writer puts where the display name is the
            // object's own name, which is how a property with no label of its
            // own arrives.
            $title = in_array($object['display'], ['-', ''], true) ? $object['name'] : $object['display'];
            $url = $base . $object['uri'];
            if (!in_array(mb_strtolower($title), $named, true) || isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            // Placed under the path of the page it is a section of, so it sits
            // in its chapter the way a page does and the anchor's own slug adds
            // no words to match against.
            $properties[] = ['title' => $title, 'path' => (string) strtok($object['uri'], '#'), 'url' => $url];
        }

        return $properties;
    }

    /**
     * The names a declared property may be offered for, lowercased.
     *
     * Two ways in, and both keep the sections out of a question asked in prose.
     * A word written the way code is names its subject wherever it stands, and
     * a question that is one word is asking about that word whatever it is
     * called — which is the only way `showitem` or `label` is reachable, since
     * either of them inside a sentence is a word of the sentence.
     *
     * @param list<string> $queries
     * @return list<string>
     */
    private static function identifiers(array $queries): array
    {
        $identifiers = [];
        foreach ($queries as $query) {
            if (preg_match('/\s/u', $query) !== 1) {
                $identifiers[mb_strtolower($query)] = true;
            }
            foreach (preg_split('/[^\p{L}\p{N}_.]+/u', $query) ?: [] as $word) {
                if ($word !== '' && preg_match(self::IDENTIFIER, $word) === 1) {
                    $identifiers[mb_strtolower($word)] = true;
                }
            }
        }

        return array_keys($identifiers);
    }

    /**
     * The pages of one manual, each with the title it was published under.
     *
     * Null is a manual that did not answer and has not answered before, which is
     * `Inventory`'s whole error vocabulary; an empty list is a book that
     * answered and lists no page.
     *
     * @return list<array{title: string, path: string, url: string}>|null
     */
    private function index(string $base): ?array
    {
        $inventory = $this->inventory->of($base);
        if ($inventory === null) {
            return null;
        }

        $pages = [];
        $seen = [];
        foreach ($inventory['objects'] as $object) {
            if ($object['role'] !== self::PAGE) {
                continue;
            }
            // `<Unknown>` is what the writer puts where a page has no title of
            // its own — three pages of the ViewHelper reference at 14.3 — and
            // the document name is what the navigation showed for them.
            $title = $object['display'] === '<Unknown>' ? $object['name'] : $object['display'];
            $url = $base . $object['uri'];
            if ($object['uri'] === self::NOT_A_PAGE || $title === '' || isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            $pages[] = ['title' => $title, 'path' => $object['uri'], 'url' => $url];
        }

        return $pages;
    }

    /**
     * The one manual a query names by the namespace prefix it is written in.
     *
     * `f:` is the Fluid namespace prefix, which a session reporting what a
     * template did writes instead of the word Fluid — a domain keyword for the
     * hints since `D-KNW-024` and read by nothing here. It selects the book
     * rather than weighing it, which is the difference `D-ANS-036` measured.
     * Only a book that answered, so a root that is down leaves the query the
     * whole index rather than no candidates at all.
     *
     * @param array<string, true> $indexed
     */
    private static function book(string $query, array $indexed): ?string
    {
        // Anchored at a word boundary, so `conf:` and `if:` are not the prefix.
        $book = Text::containsWord($query, 'f:') ? 'typo3/view-helper-reference' : null;

        return $book !== null && isset($indexed[$book]) ? $book : null;
    }

    /**
     * The same text with the compound names in it taken apart.
     *
     * Both sides need it, and for the same reason. What is searched is a table
     * of contents — page titles and paths — and no page is titled after the
     * class it documents, while a caller arrives with the words that are in the
     * code: `AssetCollector`, `FunctionalTestCase`, `executeFrontendSubRequest`.
     * Split, those reach the pages that are actually called "Assets" and
     * "Functional tests", and no list of the identifiers there are has to be
     * kept. The candidate side is split for the mirror image of it: a term is
     * matched at a word boundary, and `AfterPageColumnsSelectedForLocalizationEvent`
     * has one word in it until it is taken apart.
     */
    private static function split(string $text): string
    {
        return (string) preg_replace(
            '/(?<=\p{Ll})(?=\p{Lu})|(?<=\p{Lu})(?=\p{Lu}\p{Ll})/u',
            ' ',
            $text,
        );
    }

    /**
     * What a page was matched on, in the order the query's words were read: the
     * stem each was reduced to, and the field of the table of contents that
     * carried it. A word of the query that is not here reached the page
     * nowhere, which is what tells an aimed answer from a confident one
     * (`R-DOC-002`).
     *
     * @param array<string, string> $matched
     * @return list<array{term: string, field: string}>
     */
    private static function matched(array $matched): array
    {
        $terms = [];
        foreach ($matched as $term => $field) {
            $terms[] = ['term' => $term, 'field' => $field];
        }

        return $terms;
    }

    /**
     * The opening prose of what the URL names.
     *
     * An anchor names a section rather than the page, and the page's own
     * introduction says nothing about it: every property of the TCA reference's
     * Types page would come back with the same two sentences about record
     * types. Where the anchor names nothing on the page, the article answers as
     * before.
     */
    private function excerpt(string $html, string $anchor = ''): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $within = '//article[@role="main"]';
        if (preg_match('/^[\w.:-]+$/u', $anchor) === 1
            && self::first($xpath, sprintf('//*[@id="%s"]', $anchor)) !== null
        ) {
            $within = sprintf('//*[@id="%s"]', $anchor);
        }

        $parts = [];
        foreach (self::elements($xpath, $within . '//p') as $node) {
            $text = trim((string) preg_replace('/\s+/u', ' ', $node->textContent));
            if ($text === '') {
                continue;
            }
            $parts[] = $text;
            if (strlen(implode(' ', $parts)) >= 500) {
                break;
            }
        }

        return substr(implode(' ', $parts), 0, 700);
    }

    private function title(string $html): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $heading = self::first($xpath, '//article[@role="main"]//h1[1]')
            ?? self::first($xpath, '//h1[1]')
            ?? self::first($xpath, '//title[1]');

        return $heading === null ? '' : self::plain($heading->textContent);
    }

    /**
     * The page body as compact Markdown-like text. Code examples and headings
     * keep their boundaries; navigation outside the main article is omitted.
     */
    private function content(string $html): string
    {
        $document = new \DOMDocument();
        if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR)) {
            return '';
        }

        $xpath = new \DOMXPath($document);
        $article = self::first($xpath, '//article[@role="main"]');
        if ($article === null) {
            return '';
        }

        $blocks = [];
        // A `dd` this loop has already printed beside its term. Held by the
        // node itself rather than by a position, because the same element is
        // reached twice: once from the `dt` it belongs to and once in document
        // order.
        /** @var \SplObjectStorage<\DOMElement, null> $paired */
        $paired = new \SplObjectStorage();
        foreach (self::elements($xpath, self::BLOCKS, $article) as $node) {
            if (in_array($node->tagName, ['p', 'li'], true) && self::elements($xpath, 'ancestor::li', $node) !== []) {
                continue;
            }
            if ($node->tagName === 'dd' && (isset($paired[$node]) || !self::isLeaf($xpath, $node))) {
                // Either the term above already carries it, or it is a wrapper
                // whose own children this loop reaches on their own — printing
                // its `textContent` would be every one of them a second time.
                continue;
            }

            if ($node->tagName === 'pre') {
                $text = trim($node->textContent);
                $block = $text === '' ? '' : "```\n" . $text . "\n```";
            } else {
                $text = self::plain($node->textContent);
                if ($node->tagName === 'dt' && strlen($text) > 300) {
                    $name = self::first(
                        $xpath,
                        './/*[contains(concat(" ", normalize-space(@class), " "), " sig-name ")]',
                        $node,
                    );
                    $text = $name === null ? '' : self::plain($name->textContent);
                }
                $block = match ($node->tagName) {
                    'h1', 'h2', 'h3', 'h4', 'h5', 'h6' => str_repeat('#', (int) substr($node->tagName, 1)) . ' ' . $text,
                    'li' => '- ' . $text,
                    'dt' => $text === '' ? '' : self::term($xpath, $node, $text, $paired),
                    default => $text,
                };
            }
            if ($block !== '' && end($blocks) !== $block) {
                $blocks[] = $block;
            }
        }

        return implode("\n\n", $blocks);
    }

    /**
     * A term with its definition, where the definition is one value.
     *
     * The TCA reference states the machine-readable half of every property as a
     * definition list — Type, Default, Path, Scope — and only the terms were
     * ever emitted, so a caller read `**Default**` with nothing under it and
     * had no way to tell a property with no documented default from a value
     * this reader dropped. That is worse than dropping both, and it cost a
     * review the default of `nullable` per `dbType`
     * (`feedback/2026-08-07-132457`).
     *
     * Only a definition that is one value is pulled up here. A `dd` carrying
     * paragraphs, a nested list or another definition list stays where it is
     * and is printed as its own blocks, because a term joined to a page of
     * prose is not a pair.
     *
     * @param \SplObjectStorage<\DOMElement, null> $paired
     */
    private static function term(\DOMXPath $xpath, \DOMElement $node, string $text, \SplObjectStorage $paired): string
    {
        $term = '**' . $text . '**';
        $next = $node->nextElementSibling;
        if (!$next instanceof \DOMElement || $next->tagName !== 'dd' || !self::isLeaf($xpath, $next)) {
            return $term;
        }

        $value = self::plain($next->textContent);
        if ($value === '') {
            return $term;
        }
        $paired[$next] = null;

        return $term . ': ' . $value;
    }

    /**
     * Whether an element carries text rather than blocks of its own.
     *
     * What makes a `dd` a value is that nothing inside it is one of the things
     * this reader prints separately.
     */
    private static function isLeaf(\DOMXPath $xpath, \DOMElement $node): bool
    {
        return self::elements($xpath, self::BLOCKS, $node) === [];
    }

    /**
     * The elements a query matches, and nothing else.
     *
     * `DOMXPath::query()` answers `false` on a query it cannot compile, and a
     * list that may hold a namespace node. Neither carries an element's text or
     * its tag name, so what leaves here is elements or nothing and a caller
     * reads one case instead of three.
     *
     * @return list<\DOMElement>
     */
    private static function elements(\DOMXPath $xpath, string $query, ?\DOMNode $context = null): array
    {
        $nodes = $xpath->query($query, $context);
        if ($nodes === false) {
            return [];
        }

        $elements = [];
        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) {
                $elements[] = $node;
            }
        }

        return $elements;
    }

    /** The first element a query matches, where anything else is nothing. */
    private static function first(\DOMXPath $xpath, string $query, ?\DOMNode $context = null): ?\DOMElement
    {
        return self::elements($xpath, $query, $context)[0] ?? null;
    }

    private static function plain(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function get(string $url): ?string
    {
        return $this->reader->get($url);
    }

    /**
     * @param 'answered'|'empty'|'unavailable' $status
     * @param list<string> $queries
     * @param 'search'|'page' $mode
     * @param list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string, content: string, coverage: float|null, matched: list<array{term: string, field: string}>}> $results
     * @param array{cause: string, reason: string}|null $unavailable
     * @return array{
     *   mode: 'search'|'page',
     *   status: 'answered'|'empty'|'unavailable',
     *   targetVersion: string,
     *   source: string,
     *   queries: list<string>,
     *   results: list<array{title: string, url: string, document: string, documentTitle: string, documentVersion: string, section: string, excerpt: string, content: string, coverage: float|null, matched: list<array{term: string, field: string}>}>,
     *   unavailable: array{cause: string, reason: string}|null
     * }
     */
    private function answer(
        string $mode,
        string $status,
        array $queries,
        string $targetVersion,
        array $results,
        ?array $unavailable,
    ): array {
        return [
            'mode' => $mode,
            'status' => $status,
            'targetVersion' => $targetVersion,
            'source' => Manuals::HOST,
            'queries' => $queries,
            'results' => $results,
            'unavailable' => $unavailable,
        ];
    }
}
