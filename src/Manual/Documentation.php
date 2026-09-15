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
 * selected result pages are then read for a short excerpt. A caller then hands
 * a canonical result URL back to read that page as text. A page is read as the
 * Markdown the host publishes beside it, and as the rendered HTML where the
 * manual has none yet — `D-ANS-157`. Nothing derives its API from an installed
 * source tree a second time.
 */
final class Documentation
{
    /**
     * The page of a manual that is not one, and the one page an inventory lists
     * that no answer sends a caller to.
     */
    private const NOT_A_PAGE = '404.html';

    /**
     * What an inventory lists that this searches: the pages, which is what a
     * table of contents consists of.
     *
     * The other roles are the addressable objects inside those pages, they are
     * what a permalink identifier names, and `Manual\Permalink` reads them
     * (`R-DOC-001`, `D-ANS-119`).
     */
    private const PAGE = 'std:doc';

    /**
     * The other roles this searches: what a manual declares under its own
     * name, with the anchor of the section that documents it.
     *
     * The TCA reference is a handful of large pages that carry hundreds of
     * properties as sections. So nothing in its table of contents has the name
     * `columnsOverrides`. A query that names that property reached six pages
     * that never mention it — `D-ANS-144`. TYPO3 Explained declares the
     * classes, the interfaces, the methods and the console commands it
     * documents the same way, and none of them titles a page — `D-ANS-158`.
     * `Manual\Permalink` reads the same objects for the other question asked
     * of them.
     *
     * @var list<string>
     */
    private const DECLARED = ['std:confval', 'php:class', 'php:interface', 'php:method', 'std:console:command'];

    /** What a console command's display name carries in front of the command. */
    private const CONSOLE = 'vendor/bin/typo3 ';

    /**
     * What a declared name has to look like before a query word may reach it:
     * an inner capital, an underscore, a dot, or a colon between two words.
     *
     * A property named like an English word is one every prose question carries
     * by accident — `template`, `title`, `default`. To admit those put three of
     * the seven ranked questions of `D-ANS-032` behind sections nobody asked
     * for. Written the way code is, it is the subject rather than a word of the
     * sentence. The colon is how a console command and a method are written.
     */
    private const IDENTIFIER = '/\p{Ll}\p{Lu}|[_.]|\p{L}:\p{L}/u';

    /**
     * What a search reads a page by. The title is its name. The path is the
     * section it sits in, which is the other half of what a table of contents
     * knows. "Assets" says little, `ApiOverview/Assets/Index.html` says where
     * it belongs. The manual is the third thing a caller names without the
     * intent to name a page. A question about TCA belongs in the TCA reference
     * before it belongs in any page of another manual that carries the word.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['title' => 4, 'path' => 2, 'manual' => 2];

    /**
     * The ordinary field of this corpus, which is what the score measures a
     * longer one against. A title runs to about four words over the pages the
     * four manuals index and a path to seven. So a path weighs less than a
     * title by design.
     *
     * It was 12, longer than any title the rendered navigation carried, so no
     * title was ever diluted and the field length did nothing. Not below 3
     * either. `Fluid ViewHelper Reference` is three words and the other three
     * books are two. A smaller reference weighs the books by the length of
     * their names — `D-ANS-065`.
     */
    private const UNDILUTED_WORDS = 3;

    /**
     * The headings of a page, which the inventory lists with the anchor of
     * each. A page is worth its title and its best heading, so a question the
     * title does not carry reaches the page whose section does. What the
     * heading is worth beside the title, and what it does to the seven ranked
     * questions, is `D-ANS-159`.
     */
    private const HEADING = 'std:title';

    /** How much of a heading's own score a page takes beside its own. */
    private const HEADING_WEIGHT = 0.5;

    /** What this reader prints as a block of its own, which is also what makes a `dd` more than a value. */
    private const BLOCKS = './/h1|.//h2|.//h3|.//h4|.//h5|.//h6|.//p|.//pre|.//li|.//dt|.//dd';

    /**
     * The front matter every Markdown page opens with, and the body after it.
     *
     * It carries the title the manual states, the manual, the version and the
     * permalink. A body that does not open with it is not a page: the 404 the
     * host renders is HTML, and so is a challenge page with a 200 in front of
     * it (`D-ANS-034`).
     */
    private const FRONT_MATTER = '/^---\n(.*?)\n---\n\s*/s';

    /**
     * Whether the manual at a base publishes Markdown, learned from the first
     * page of it read in this process.
     *
     * The host has Markdown for a manual once it is rendered again since the
     * format arrived, per manual and per version. So one page that answers as
     * HTML and not as Markdown says the whole manual answers that way, and the
     * rest of its pages cost one read rather than a miss and a read. Static
     * because the tool builds an instance per call — `D-ANS-157`.
     *
     * @var array<string, bool>
     */
    private static array $markdown = [];

    private readonly Fetch $reader;

    private readonly Inventory $inventory;

    /** @param (\Closure(string): ?string)|null $fetch */
    public function __construct(?\Closure $fetch = null)
    {
        $this->reader = new Fetch($fetch ?? Manuals::reader());
        $this->inventory = new Inventory($this->reader);
    }

    /** What a reader handed in leaves behind: another reader is another host. */
    public static function forget(): void
    {
        self::$markdown = [];
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

            foreach ([...$index, ...$this->declared($base, $named)] as $page) {
                $pages[$document . '|' . $page['url']] = [
                    'score' => 0,
                    'coverage' => 0.0,
                    'matched' => [],
                    'title' => $page['title'],
                    'url' => $page['url'],
                    'section' => $page['title'],
                    'document' => $document,
                    'documentTitle' => $manual['title'],
                    'searchable' => [
                        'title' => self::split($page['title']),
                        'path' => self::split($page['path']),
                        'manual' => $manual['title'],
                    ],
                    'headings' => $page['headings'] ?? [],
                ];
            }
        }

        if ($indexed === []) {
            return $this->answer('search', 'unavailable', $queries, $targetVersion, [], [
                'cause' => 'source-not-answering',
                'reason' => 'The versioned TYPO3 documentation indexes could not be reached.',
            ]);
        }

        // The score weighs every manual against every other manual's pages.
        // What makes a term worth something is how few of all the pages there
        // are carry it, in a title, a path or a heading.
        $searchable = array_map(
            static fn(array $page): array => $page['searchable'] + ['headings' => implode("\n", $page['headings'])],
            array_values($pages),
        );
        foreach ($queries as $query) {
            $book = self::book($query, $indexed);
            $weights = TermSearch::weights(TermSearch::terms(self::split($query)), $searchable);
            $askedFor = array_sum($weights);
            $scores = [];
            $covered = [];
            $matched = [];
            $sections = [];
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
                [$scores[$key], $covered[$key], $matched[$key], $sections[$key]] = self::withHeading(
                    $page['headings'],
                    $weights,
                    [$scores[$key], $covered[$key], $matched[$key]],
                );
            }

            // Each query is its own question and its scores are its own scale.
            // One made of common words scores everything higher than one made
            // of rare ones. So a page is worth how well it answers a question
            // rather than what that question's words happen to be worth. It
            // keeps the best question it answers rather than the sum of the
            // ones it brushes past. Two questions in one call otherwise return
            // one question's pages twice over.
            $best = max([0, ...$scores]);
            foreach ($scores as $key => $score) {
                $relative = $best === 0 ? 0 : (int) round($score / $best * 1000);
                if ($relative <= $pages[$key]['score']) {
                    continue;
                }
                // The reported match is the one of the question the page stays
                // for. So it is the words of that query rather than of
                // whichever one came last. The coverage is that query's too:
                // the share of its weight this page carries. The score has
                // always returned it and this lookup dropped it in the
                // destructure. It labels rather than filters — `D-ANS-051`.
                $pages[$key]['score'] = $relative;
                $pages[$key]['matched'] = $matched[$key];
                $pages[$key]['coverage'] = $askedFor > 0.0 ? $covered[$key] / $askedFor : 0.0;
                $pages[$key]['section'] = $sections[$key] ?? $pages[$key]['title'];
            }
        }

        $candidates = array_filter($pages, static fn(array $page): bool => $page['score'] > 0);
        uasort($candidates, static fn(array $left, array $right): int => $right['score'] <=> $left['score']);
        $results = [];
        foreach (array_slice($candidates, 0, $limit) as $candidate) {
            // An anchor names a section, and only the rendered page carries
            // the ids a section is found by. So a property is read from the
            // HTML, and a page from what the host publishes it as.
            $url = $candidate['url'];
            if (is_array($candidate['section'])) {
                $url .= '#' . $candidate['section']['anchor'];
            }
            $anchor = (string) parse_url($url, PHP_URL_FRAGMENT);
            $page = $anchor === ''
                ? $this->fetch(self::base($candidate['document'], $targetVersion), $url)
                : $this->html($url);
            $results[] = [
                'title' => $candidate['title'],
                'url' => $url,
                'document' => $candidate['document'],
                'documentTitle' => $candidate['documentTitle'],
                'documentVersion' => $targetVersion,
                'section' => is_array($candidate['section']) ? $candidate['section']['title'] : $candidate['section'],
                'excerpt' => match (true) {
                    $page === null => '',
                    isset($page['markdown']) => self::lead($page['markdown']),
                    default => $this->excerpt($page['html'], $anchor),
                },
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
                $owner = ['document' => $document, 'title' => $manual['title'], 'base' => $base];
                break;
            }
        }
        if ($owner === null) {
            throw new \InvalidArgumentException(
                'page must be a canonical result URL for targetVersion from typo3_documentation_lookup',
            );
        }

        $page = $this->fetch($owner['base'], $url);
        if ($page === null) {
            return $this->answer('page', 'unavailable', [], $targetVersion, [], [
                'cause' => 'source-not-answering',
                'reason' => 'The selected TYPO3 documentation page could not be reached.',
            ]);
        }

        if (isset($page['markdown'])) {
            [$title, $content] = self::document($page['markdown']);
        } else {
            $content = $this->content($page['html']);
            $title = $this->title($page['html']);
        }
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
            // The caller asked nothing, so there is no query to cover. That is
            // the null beside the empty match, rather than a zero that says
            // this page answers nothing.
            'coverage' => null,
            'matched' => [],
        ]], null);
    }

    /** Where the host publishes one manual, at one version. */
    private static function base(string $document, string $targetVersion): string
    {
        return Manuals::base(Manuals::searched()[$document]['collection'], $document, $targetVersion);
    }

    /**
     * What this manual declares that a question named, with the anchor of the
     * section that documents each.
     *
     * @param list<string> $named the lowercased names a question may reach one by
     * @return list<array{title: string, path: string, url: string}>
     */
    private function declared(string $base, array $named): array
    {
        $inventory = $named === [] ? null : $this->inventory->of($base);
        if ($inventory === null) {
            return [];
        }

        $declared = [];
        $seen = [];
        foreach ($inventory['objects'] as $object) {
            if (!in_array($object['role'], self::DECLARED, true)) {
                continue;
            }
            // `-` is what the writer puts where the display name is the
            // object's own name. That is how a property with no label of its
            // own arrives.
            $title = in_array($object['display'], ['-', ''], true) ? $object['name'] : $object['display'];
            $url = $base . $object['uri'];
            if (array_intersect(self::names($object['role'], $title), $named) === [] || isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            // It sits under the path of the page it is a section of. So it
            // stands in its chapter the way a page does, and the anchor's own
            // slug adds no words to match against.
            $declared[] = ['title' => $title, 'path' => (string) strtok($object['uri'], '#'), 'url' => $url];
        }

        return $declared;
    }

    /**
     * The names a query reaches one declared object by, lowercased.
     *
     * A class by its short name or its qualified one, without the leading
     * backslash the manual writes. A method by `Class::method` in either
     * spelling of the class, and never by the method alone: `getRequest` is
     * declared on 48 pages of TYPO3 Explained at 14.3, and a query that names
     * only it asks for none of them in particular — `D-ANS-158`. A console
     * command by what a caller types after the binary.
     *
     * @return list<string>
     */
    private static function names(string $role, string $display): array
    {
        $display = mb_strtolower($display);
        switch ($role) {
            case 'php:class':
            case 'php:interface':
                $qualified = ltrim($display, '\\');

                return [$qualified, substr((string) strrchr('\\' . $qualified, '\\'), 1)];
            case 'php:method':
                [$class, $method] = explode('::', $display, 2) + [1 => ''];
                $qualified = ltrim($class, '\\');

                return [$qualified . '::' . $method, substr((string) strrchr('\\' . $qualified, '\\'), 1) . '::' . $method];
            case 'std:console:command':
                return [str_starts_with($display, self::CONSOLE) ? substr($display, strlen(self::CONSOLE)) : $display];
            default:
                return [$display];
        }
    }

    /**
     * The names a query reaches a declared object by, lowercased.
     *
     * Two ways in, and both keep the sections out of a question asked in prose.
     * A word written the way code is names its subject wherever it stands. A
     * question that is one word asks about that word whatever its name. That is
     * the only way a query reaches `showitem` or `label`, since either of them
     * inside a sentence is a word of the sentence. A word keeps its colons and
     * backslashes, so `cache:flushtags` and `AssetCollector::addJavaScript`
     * arrive whole.
     *
     * @param list<string> $queries
     * @return list<string>
     */
    private static function identifiers(array $queries): array
    {
        $identifiers = [];
        foreach ($queries as $query) {
            if (preg_match('/\s/u', $query) !== 1) {
                $identifiers[mb_strtolower(ltrim($query, '\\'))] = true;
            }
            foreach (preg_split('/[^\p{L}\p{N}_.:\\\\]+/u', $query) ?: [] as $word) {
                $word = ltrim(trim($word, ':'), '\\');
                if ($word !== '' && preg_match(self::IDENTIFIER, $word) === 1) {
                    $identifiers[mb_strtolower($word)] = true;
                }
            }
        }

        return array_keys($identifiers);
    }

    /**
     * The pages of one manual, each with the title the host published it under
     * and the headings it carries.
     *
     * Null is a manual that did not answer and has not answered before, which
     * is `Inventory`'s whole error vocabulary. An empty list is a book that
     * answered and lists no page.
     *
     * A heading is one the inventory lists under the page with an anchor, and
     * that is not the page's own title again: Sphinx lists the first heading
     * of every page as a section too.
     *
     * @return list<array{title: string, path: string, url: string, headings: array<string, string>}>|null
     */
    private function index(string $base): ?array
    {
        $inventory = $this->inventory->of($base);
        if ($inventory === null) {
            return null;
        }

        $pages = [];
        foreach ($inventory['objects'] as $object) {
            if ($object['role'] !== self::PAGE) {
                continue;
            }
            // `<Unknown>` is what the writer puts where a page has no title of
            // its own — three pages of the ViewHelper reference at 14.3. The
            // document name is what the navigation showed for them.
            $title = $object['display'] === '<Unknown>' ? $object['name'] : $object['display'];
            if ($object['uri'] === self::NOT_A_PAGE || $title === '' || isset($pages[$object['uri']])) {
                continue;
            }
            $pages[$object['uri']] = ['title' => $title, 'path' => $object['uri'], 'url' => $base . $object['uri'], 'headings' => []];
        }
        foreach ($inventory['objects'] as $object) {
            [$path, $anchor] = explode('#', $object['uri'], 2) + [1 => ''];
            if ($object['role'] !== self::HEADING || $anchor === '' || !isset($pages[$path])) {
                continue;
            }
            if (mb_strtolower($object['display']) === mb_strtolower($pages[$path]['title'])) {
                continue;
            }
            $pages[$path]['headings'][$anchor] = $object['display'];
        }

        return array_values($pages);
    }

    /**
     * The page's score with its best heading added, and that heading where it
     * carries the question better than the page's own title does.
     *
     * The heading is scored as a title, over the terms the page carries
     * nowhere else, and the page takes the best one. The best rather than the
     * sum, because a long page carries a heading for every word of a question
     * and would win on length. The section comes back only where the heading
     * outscores the page, so a question the title answers sends the caller to
     * the page and not into it.
     *
     * @param array<string, string> $headings the anchor of each heading, and its text
     * @param array<string, float> $weights
     * @param array{0: int, 1: float, 2: array<string, string>} $page
     * @return array{0: int, 1: float, 2: array<string, string>, 3: array{anchor: string, title: string}|null}
     */
    private static function withHeading(array $headings, array $weights, array $page): array
    {
        [$score, $covered, $matched] = $page;
        // Only the terms the page carries nowhere yet. A heading that repeats
        // the title's word says nothing the title did not, and a page with
        // more sections would win on repetition.
        $remaining = array_diff_key($weights, $matched);
        $best = null;
        foreach ($remaining === [] ? [] : $headings as $anchor => $heading) {
            $scored = TermSearch::score(['title' => self::split($heading)], $remaining, ['title' => self::FIELD_WEIGHTS['title']], self::UNDILUTED_WORDS);
            if ($scored[0] > ($best[0] ?? 0)) {
                $best = [...$scored, 'anchor' => $anchor, 'title' => $heading];
            }
        }
        if ($best === null) {
            return [$score, $covered, $matched, null];
        }

        foreach ($best[2] as $term => $field) {
            $matched[$term] = 'section';
        }

        return [
            $score + (int) round($best[0] * self::HEADING_WEIGHT),
            $covered + $best[1],
            $matched,
            $best[0] > $score ? ['anchor' => $best['anchor'], 'title' => $best['title']] : null,
        ];
    }

    /**
     * The one manual a query names by the namespace prefix it carries.
     *
     * `f:` is the Fluid namespace prefix, which a session that reports what a
     * template did writes instead of the word Fluid. It is a domain keyword for
     * the hints since `D-KNW-024`, and nothing here reads it. It selects the
     * book rather than weighs it, which is the difference `D-ANS-036` measured.
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
     * Both sides need it, and for the same reason. The search reads a table of
     * contents — page titles and paths — and no page carries the name of the
     * class it documents. A caller arrives with the words that are in the code:
     * `AssetCollector`, `FunctionalTestCase`, `executeFrontendSubRequest`.
     * Split, those reach the pages whose names are "Assets" and "Functional
     * tests", and nothing keeps a list of the identifiers there are. The
     * candidate side splits for the mirror image of it. A term matches at a
     * word boundary, and `AfterPageColumnsSelectedForLocalizationEvent` has one
     * word in it until the split takes it apart.
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
     * What matched the page, in the order of the query's words. The stem each
     * came down to, and the field of the table of contents that carried it. A
     * word of the query that is not here reached the page nowhere, which is
     * what tells an aimed answer from a confident one (`R-DOC-002`).
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
     * One page, as the Markdown the host publishes beside it where the manual
     * has it, and as the rendered HTML where it does not.
     *
     * The Markdown sits at the page's own URL with `.md` for `.html`. It is the
     * whole page after the build, with a front matter that states the title,
     * and a tenth of the HTML on the wire. A manual has it once it is rendered
     * again since the format arrived, so a 404 is per manual and per version.
     * That is what this remembers, once a page answers as HTML alone —
     * `D-ANS-157` has the sizes.
     *
     * @return array{markdown: string}|array{html: string}|null
     */
    private function fetch(string $base, string $url): ?array
    {
        $page = (string) strtok($url, '#');
        if (self::$markdown[$base] ?? true) {
            $markdown = $this->get(substr($page, 0, -strlen('.html')) . '.md');
            if ($markdown !== null && preg_match(self::FRONT_MATTER, $markdown) === 1) {
                self::$markdown[$base] = true;

                return ['markdown' => $markdown];
            }
        }

        $html = $this->get($page);
        if ($html === null) {
            return null;
        }
        self::$markdown[$base] ??= false;

        return ['html' => $html];
    }

    /**
     * The rendered page alone, for a read that needs the ids in it.
     *
     * @return array{html: string}|null
     */
    private function html(string $url): ?array
    {
        $html = $this->get($url);

        return $html === null ? null : ['html' => $html];
    }

    /**
     * The title a Markdown page states and the page after its front matter.
     *
     * The front matter writes the title as a double-quoted YAML scalar, which
     * `json_decode` reads. The first heading is the same title with the
     * markup of its subject in it, and stands in where the front matter
     * states none.
     *
     * @return array{string, string}
     */
    private static function document(string $markdown): array
    {
        $title = '';
        $body = $markdown;
        if (preg_match(self::FRONT_MATTER, $markdown, $front) === 1) {
            $body = substr($markdown, strlen($front[0]));
            if (preg_match('/^title: *(".*")$/m', $front[1], $stated) === 1) {
                $title = is_string($decoded = json_decode($stated[1])) ? trim($decoded) : '';
            }
        }
        if ($title === '' && preg_match('/^# +(.+)$/m', $body, $heading) === 1) {
            $title = self::plain(str_replace('`', '', $heading[1]));
        }

        return [$title, trim($body)];
    }

    /**
     * The first prose of a Markdown page.
     *
     * The paragraphs, and none of the front matter, the headings, the lists,
     * the quotes, the tables and the code. The list of sections every page
     * opens with is a bold label over a list, so both halves of it stay out.
     * It is the mirror of `excerpt()`, which takes the paragraphs of the
     * article.
     */
    private static function lead(string $markdown): string
    {
        $parts = [];
        $paragraph = [];
        $code = false;
        $body = (string) preg_replace(self::FRONT_MATTER, '', $markdown, 1);
        foreach ([...explode("\n", $body), ''] as $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $code = !$code;
                continue;
            }
            if ($code) {
                continue;
            }
            if (trim($line) !== '') {
                $paragraph[] = trim($line);
                continue;
            }
            if ($paragraph !== [] && preg_match('/^(?:[#>|`*-]|\d+\.\s)/', $paragraph[0]) !== 1) {
                $parts[] = implode(' ', $paragraph);
                if (strlen(implode(' ', $parts)) >= 500) {
                    break;
                }
            }
            $paragraph = [];
        }

        return substr(implode(' ', $parts), 0, 700);
    }

    /**
     * The first prose of what the URL names.
     *
     * An anchor names a section rather than the page, and the page's own
     * introduction says nothing about it. Every property of the TCA reference's
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
            // A heading's anchor is an empty `<a>` at the top of its section,
            // and a property's is the section itself. So the prose is inside
            // the named element or inside the section around it.
            $within = sprintf('//*[@id="%s"]', $anchor);
            if (self::first($xpath, $within . '//p') === null && self::first($xpath, $within . '/ancestor::section[1]//p') !== null) {
                $within .= '/ancestor::section[1]';
            }
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
     * keep their boundaries; the reader drops navigation outside the main
     * article.
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
        // node itself rather than by a position, because the loop reaches the
        // same element twice. Once from the `dt` it belongs to and once in
        // document order.
        /** @var \SplObjectStorage<\DOMElement, null> $paired */
        $paired = new \SplObjectStorage();
        foreach (self::elements($xpath, self::BLOCKS, $article) as $node) {
            if (in_array($node->tagName, ['p', 'li'], true) && self::elements($xpath, 'ancestor::li', $node) !== []) {
                continue;
            }
            if ($node->tagName === 'dd' && (isset($paired[$node]) || !self::isLeaf($xpath, $node))) {
                // Either the term above already carries it, or it is a wrapper
                // whose own children this loop reaches on their own. A print of
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
     * definition list — Type, Default, Path, Scope. This reader emitted only
     * the terms. So a caller read `**Default**` with nothing under it. Nothing
     * told a property with no documented default from a value this reader
     * dropped. That is worse than a drop of both, and it cost a review the
     * default of `nullable` per `dbType` (`feedback/2026-08-07-132457`).
     *
     * Only a definition that is one value comes up here. A `dd` that carries
     * paragraphs, a nested list or another definition list stays where it is
     * and prints as its own blocks. A term joined to a page of prose is not a
     * pair.
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
     * its tag name. So what leaves here is elements or nothing, and a caller
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
