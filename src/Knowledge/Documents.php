<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Search\Subsets;
use TYPO3\DevCompanion\Search\TermSearch;

/**
 * Reads and searches the bundled markdown knowledge documents.
 *
 * Search works on whole `##` sections, not on single lines. A section comes
 * back with its heading and its original format, code fences included, so the
 * answer stays readable and quotable. A section only counts as a match when it
 * covers enough of the query. So a lookup that found nothing relevant says so
 * instead of returns the nearest unrelated prose.
 */
final class Documents
{
    /**
     * Share of the query's meaningful terms a section has to contain. Below it,
     * a section is noise rather than an answer.
     */
    private const MIN_COVERAGE = 0.5;

    /**
     * A term in the heading weighs more than the same term in the body. A
     * section titled "Design Tokens" is about design tokens, and one that
     * mentions them in a clause is not.
     *
     * The document title sits between the two, and it is what a caller who
     * names the subject before the question matches on, `D-ANS-037`.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['heading' => 4, 'title' => 2, 'body' => 1];

    /**
     * Section length that still counts for what its terms say.
     *
     * A section cuts at MAX_SECTION_LENGTH, which is roughly this many words.
     * So a section at that length is an ordinary one rather than an outlier.
     * Nothing here is long enough to contain a term by accident. The hint
     * corpus is the other case and sets its own reference.
     */
    private const UNDILUTED_WORDS = 400;

    /**
     * Longest section body that comes back verbatim before a cut on a line
     * boundary.
     */
    private const MAX_SECTION_LENGTH = 2400;

    /**
     * How a section says which majors it holds for.
     *
     * Two labelled lines directly under the heading, the shape of a todo head,
     * so one habit covers both. They are data rather than a sentence,
     * `D-VER-001`, and they come off before the body goes over. A section whose
     * body is a file would otherwise carry them into the file the caller writes
     * out.
     */
    private const BINDING = '/^\*\*(Since|Until):\*\*\s*(\d+)\s*$/';

    /**
     * A document's address from outside this process.
     *
     * The corpus owns it rather than the SDK adapter. The tools and the answer
     * shapes need it too, and neither may reach into `Sdk\` for a name. It
     * stood by hand in three classes before `D-KNW-059`.
     */
    public const URI_PREFIX = 'typo3://guides/';

    /** The resource URI of a document id. */
    public static function uri(string $id): string
    {
        return self::URI_PREFIX . $id;
    }

    /** The document id in a resource URI, or null where the URI is another kind. */
    public static function idOf(string $uri): ?string
    {
        return str_starts_with($uri, self::URI_PREFIX)
            ? substr($uri, strlen(self::URI_PREFIX))
            : null;
    }

    /**
     * How deep a document sits: the scope, one topic, one name — `D-KNW-058`.
     *
     * The depth is what publishes a file rather than the directory alone. A
     * place below `knowledge/documents/` used to be the whole condition, and a
     * readme beside the corpus became a resource without a decision by anybody.
     * A readme inside a topic directory would do the same.
     */
    private const DEPTH = 2;

    /**
     * The tool that hands a document over whole, named on every reference to
     * one.
     *
     * The field is `guides`, the argument is a `documentId` and the call is
     * this. No name joins the three, and a session that read the array as data
     * had the route only in the sentence above it, `D-GUI-012`.
     */
    private const READ_BY = 'typo3_rule_lookup';

    /** @return array<int, array{id: string, title: string, path: string, description: string, whenToUse: string, hints: array<int, string>}> */
    public static function documents(): array
    {
        $documents = [];
        $root = Paths::documents();
        if (!is_dir($root)) {
            return $documents;
        }

        foreach (Finder::create()->files()->in($root)->depth(self::DEPTH)->name('*.md')->sortByName() as $file) {
            $id = substr($file->getPathname(), strlen($root) + 1, -strlen('.md'));
            if (Scope::tryFrom((string) strtok($id, '/')) === null) {
                continue;
            }

            $content = (string) file_get_contents($file->getPathname());
            $documents[] = [
                'id' => $id,
                'title' => self::readTitle($content) ?? $file->getFilename(),
                'path' => $file->getPathname(),
            ] + self::declared($content);
        }

        return $documents;
    }

    /**
     * Who a document answers for, which is the directory it sits in.
     *
     * Declared once and in the one place a move cannot leave behind —
     * `D-KNW-058`. It was the coverage row before that, and the file name
     * carried the same word beside it.
     */
    public static function scopeOf(string $id): Scope
    {
        return Scope::tryFrom((string) strtok($id, '/')) ?? Scope::Uncertain;
    }

    /** Whether a document is the core repository's own. */
    public static function isCoreOnly(string $id): bool
    {
        return self::scopeOf($id) === Scope::Core;
    }

    /**
     * What a document declares about itself — `D-KNW-057`.
     *
     * Absent fields come back empty rather than gone, so a caller reads one
     * shape whether the file declares anything or not.
     *
     * @return array{description: string, whenToUse: string, hints: array<int, string>}
     */
    private static function declared(string $content): array
    {
        preg_match('/\A---\R(.*?)\R---\R/s', $content, $matches);
        // Yaml rather than a value per key, the way a decision reads. A
        // description is a sentence and a sentence carries colons.
        $declared = $matches === [] ? [] : Yaml::parse($matches[1]);
        $declared = is_array($declared) ? $declared : [];

        return [
            'description' => is_string($declared['description'] ?? null) ? trim($declared['description']) : '',
            'whenToUse' => is_string($declared['whenToUse'] ?? null) ? trim($declared['whenToUse']) : '',
            'hints' => array_values(array_filter(
                array_map('strval', is_array($declared['hints'] ?? null) ? $declared['hints'] : []),
            )),
        ];
    }

    /**
     * The documents that declare themselves the long form of this hint.
     *
     * @return array<int, array{id: string, title: string, path: string, description: string, whenToUse: string, hints: array<int, string>}>
     */
    public static function forHint(string $hintId): array
    {
        return array_values(array_filter(
            self::documents(),
            static fn(array $document): bool => in_array($hintId, $document['hints'], true),
        ));
    }

    /**
     * What a client reads to understand the offer, for a document on offer as a
     * resource.
     *
     * A resource comes out of a list rather than a call mid-task, so the list
     * is the whole of what the choice rests on, `R-ANS-022`. It says what the
     * page is and when to reach for it, which the file declares. It says who
     * the answers oblige, which is the directory it sits in.
     */
    public static function description(string $id): ?string
    {
        $declared = null;
        foreach (self::documents() as $document) {
            if ($document['id'] === $id) {
                $declared = $document;
                break;
            }
        }
        if ($declared === null || $declared['description'] === '') {
            return null;
        }

        $card = $declared['description'];
        if ($declared['whenToUse'] !== '') {
            $card .= ' ' . $declared['whenToUse'];
        }

        return $card . ' ' . match (self::scopeOf($id)) {
            Scope::Core => "The TYPO3 core's own process, which does not transfer to extension or site work.",
            // Named rather than folded into the sentence below it. A document
            // about the setup of a package answers for the package. A line to a
            // core contributor that it holds for their work too is how the
            // core's own harness gets a rebuild by hand.
            Scope::Extension => 'Answers for a package rather than for the core repository, whose own harness is a different one.',
            Scope::Project => 'Answers for the repository around an installation rather than for the core repository.',
            default => 'Holds for core contribution, extension development and site work alike.',
        };
    }

    /**
     * One document as an answer names it. The call that reads it, what it is,
     * and what the caller has to do for it to be the page to read.
     *
     * The orientation answer and a brief both carry these. So the map from what
     * a document declares to what an answer says lives here rather than in each
     * of them, `D-GUI-012`.
     *
     * The scope stands in words rather than in the id. A session filtered the
     * listing on the prefix, read `core/` as "not mine" and worked out the
     * procedure itself. It filtered correctly that time, and what it did was
     * treat a path segment as data, `D-ANS-150`.
     *
     * @param array{id: string, title: string, whenToUse: string, ...} $document
     * @return array{id: string, title: string, when: string, scope: string, tool: string}
     */
    public static function reference(array $document): array
    {
        return [
            'id' => $document['id'],
            'title' => $document['title'],
            'when' => $document['whenToUse'],
            'scope' => self::scopeOf($document['id'])->value,
            'tool' => self::READ_BY,
        ];
    }

    public static function read(string $id): string
    {
        foreach (self::documents() as $document) {
            if ($document['id'] === $id) {
                return (string) file_get_contents($document['path']);
            }
        }

        throw new \RuntimeException(sprintf('Unknown knowledge document: %s', $id));
    }

    /**
     * The `##` headings of one document, in the order the page carries them.
     *
     * Each once, because one subject bound to two ranges is two sections under
     * one heading. A reader who counts the headings of `playwright.md` gets
     * nine where its `##` lines are ten (`D-ANS-008`).
     *
     * @return array<int, string>
     */
    public static function headings(string $id): array
    {
        return array_values(array_unique(array_filter(array_map(
            static fn(array $section): string => $section['heading'],
            self::sections(self::read($id)),
        ))));
    }

    /**
     * The topics each document covers, for orientation and for no-match answers.
     *
     * @return array<int, array{id: string, title: string, topics: array<int, string>}>
     */
    public static function topics(): array
    {
        return array_map(static fn(array $document): array => [
            'id' => $document['id'],
            'title' => $document['title'],
            'topics' => self::headings($document['id']),
        ], self::documents());
    }

    /**
     * Ranks whole document sections against a free-text query. Sections below
     * the coverage threshold drop out, and a section that repeats across
     * documents comes back once.
     *
     * @param array<int, string> $documentIds Restrict the search to these documents.
     * @param int|array<int, int>|null $target The major, or the majors a repository serves at once.
     * @return array<int, array{id: string, title: string, heading: string, body: string, since: ?int, until: ?int, score: int, coverage: float, truncated: bool}>
     */
    public static function search(string $query, array $documentIds = [], int $limit = 6, int|array|null $target = null): array
    {
        $terms = TermSearch::terms($query);
        if ($terms === []) {
            return [];
        }

        $candidates = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::forVersion(self::sections($content), $target) as $section) {
                $candidates[] = [
                    'id' => $document['id'],
                    'title' => $document['title'],
                    'heading' => $section['heading'],
                    'body' => $section['body'],
                    'since' => $section['since'],
                    'until' => $section['until'],
                ];
            }
        }

        $weights = TermSearch::weights($terms, array_map(self::distinguishing(...), $candidates));
        $askedFor = array_sum($weights);

        $matches = [];
        foreach ($candidates as $candidate) {
            [$score, $covered] = TermSearch::score(
                self::searchable($candidate),
                $weights,
                self::FIELD_WEIGHTS,
                self::UNDILUTED_WORDS,
            );
            $coverage = $askedFor > 0.0 ? $covered / $askedFor : 0.0;
            if ($coverage < self::MIN_COVERAGE) {
                continue;
            }

            $matches[] = $candidate + [
                'score' => $score,
                'coverage' => $coverage,
                'truncated' => false,
            ];
        }

        usort($matches, static function (array $a, array $b): int {
            return $b['coverage'] <=> $a['coverage']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['heading'], $b['heading']);
        });

        $seen = [];
        $results = [];
        foreach ($matches as $match) {
            $fingerprint = md5(preg_replace('/\s+/', ' ', $match['body']) ?? $match['body']);
            if (isset($seen[$fingerprint])) {
                continue;
            }
            $seen[$fingerprint] = true;

            [$body, $truncated] = self::shorten($match['body']);
            $match['body'] = $body;
            $match['truncated'] = $truncated;
            $results[] = $match;

            if (count($results) >= $limit) {
                break;
            }
        }

        return $results;
    }

    /**
     * The sections that hold on the target, the way `Hints::forVersion()` keeps
     * a statement.
     *
     * The target is one major, or the several a repository serves at once. A
     * package that declares `^13.4 || ^14.3` has to see both variants of a file
     * it ships on both. The range beside each is what says which is which.
     * Without a target nothing filters and every variant comes back with its
     * range. That is the honest answer when nobody said which version this is
     * for.
     *
     * @param array<int, array{heading: string, body: string, since: ?int, until: ?int}> $sections
     * @param int|array<int, int>|null $target
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
     */
    private static function forVersion(array $sections, int|array|null $target): array
    {
        $targets = is_array($target) ? array_values($target) : ($target === null ? [] : [$target]);
        if ($targets === []) {
            return $sections;
        }

        return array_values(array_filter($sections, static function (array $section) use ($targets): bool {
            foreach ($targets as $major) {
                if (Versions::holds($section['since'], $section['until'], $major)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * The most of a query some section still carries, as a query a caller can
     * ask outright.
     *
     * What a miss here owes that the topic list cannot say: which words emptied
     * it. MIN_COVERAGE drops a query longer than the topic it names whole. So
     * the section that answers part of it is out of reach and nothing in the
     * answer points at it.
     *
     * The matcher is this corpus's own, and the fields are the ones `search()`
     * reads. A subset is only worth an offer where the same call returns
     * something for it. What comes back is the caller's own form of each term
     * rather than the stem behind it; both reach the same sections.
     *
     * The count is what carries every word of the subset, which here is a floor
     * rather than the length of the answer. `search()` keeps a section that
     * covers half the query's weight, so the re-query returns those sections
     * and whatever else clears MIN_COVERAGE. It is one pass and it is
     * comparable across the subsets, which is what the caller picks one by.
     *
     * @param array<int, string> $documentIds Restrict to these documents.
     * @param int|array<int, int>|null $target The majors the same call searched.
     * @return array<int, array{terms: array<int, string>, matchCount: int}> Narrowest first.
     */
    public static function largestReachingSubsets(string $query, array $documentIds = [], int|array|null $target = null): array
    {
        $texts = [];
        foreach (self::documents() as $document) {
            if ($documentIds !== [] && !in_array($document['id'], $documentIds, true)) {
                continue;
            }

            $content = (string) file_get_contents($document['path']);
            foreach (self::forVersion(self::sections($content), $target) as $section) {
                $texts[] = $section['heading'] . ' ' . $section['body'];
            }
        }

        $words = TermSearch::words($query);
        $subsets = Subsets::largestReaching($texts, TermSearch::terms($query), TermSearch::carries(...));

        return array_map(static fn(array $subset): array => [
            'terms' => array_map(static fn(string $term): string => $words[$term] ?? $term, $subset['terms']),
            'matchCount' => $subset['matchCount'],
        ], $subsets);
    }

    /**
     * Splits a document into its `##` sections. The heading line and the body
     * stay as they are, so code fences and nested lists survive. The range
     * lines below the heading come off.
     *
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
     */
    private static function sections(string $content): array
    {
        // The front matter describes the document rather than answering a query
        // — `D-KNW-057`. Left in, it matches words about a page instead of
        // words in it. It lands in the body of the section above the first
        // heading, which is one this corpus returns.
        $content = (string) preg_replace('/\A---\R.*?\R---\R/s', '', $content);
        $lines = preg_split('/\R/', $content) ?: [];

        $sections = [];
        $heading = '';
        $buffer = [];
        $inFence = false;

        foreach ($lines as $line) {
            if (str_starts_with(ltrim($line), '```')) {
                $inFence = !$inFence;
            }

            if (!$inFence && preg_match('/^##\s+(.+)$/', $line, $matches) === 1) {
                $sections = self::flushSection($sections, $heading, $buffer);
                $buffer = [];
                $heading = trim($matches[1]);
                continue;
            }

            // Skip the document title; it travels apart.
            if (!$inFence && preg_match('/^#\s+/', $line) === 1) {
                continue;
            }

            $buffer[] = $line;
        }

        return self::flushSection($sections, $heading, $buffer);
    }

    /**
     * Appends the buffered section, unless it has no body: a heading with
     * nothing under it is not an answer. A section that is nothing but its
     * range is one of those, and it drops out rather than comes back empty.
     *
     * @param array<int, array{heading: string, body: string, since: ?int, until: ?int}> $sections
     * @param array<int, string> $buffer
     * @return array<int, array{heading: string, body: string, since: ?int, until: ?int}>
     */
    private static function flushSection(array $sections, string $heading, array $buffer): array
    {
        $bound = self::readBinding($buffer);
        $body = trim(implode("\n", $bound['lines']));
        if ($body !== '') {
            $sections[] = [
                'heading' => $heading,
                'body' => $body,
                'since' => $bound['since'],
                'until' => $bound['until'],
            ];
        }

        return $sections;
    }

    /**
     * The binding declared at the top of a section, and the section without it.
     *
     * Only the run of lines before the first line of content counts, so a
     * `**Since:**` further down is body text and binds nothing. The declaration
     * has one place, which is what spares a reader a search of a section for
     * the range it holds on.
     *
     * @param array<int, string> $lines
     * @return array{since: ?int, until: ?int, lines: array<int, string>}
     */
    private static function readBinding(array $lines): array
    {
        $since = null;
        $until = null;

        foreach ($lines as $index => $line) {
            if (trim($line) === '') {
                continue;
            }
            if (preg_match(self::BINDING, trim($line), $matches) !== 1) {
                break;
            }

            if ($matches[1] === 'Since') {
                $since = (int) $matches[2];
            } else {
                $until = (int) $matches[2];
            }
            unset($lines[$index]);
        }

        return ['since' => $since, 'until' => $until, 'lines' => array_values($lines)];
    }

    /**
     * The fields of a section the matcher reads, keyed the way FIELD_WEIGHTS
     * names them.
     *
     * @param array<string, mixed> $candidate
     * @return array<string, string>
     */
    private static function searchable(array $candidate): array
    {
        return [
            'heading' => (string) $candidate['heading'],
            'title' => (string) $candidate['title'],
            'body' => (string) $candidate['body'],
        ];
    }

    /**
     * The fields that say how much a term separates one section from the next.
     *
     * The title is not one of them, because it is the same string in every
     * section of its document. A count of it there makes a term look common in
     * proportion to how many sections that document happens to have. That is a
     * fact about its length rather than about what the term tells apart. It is
     * enough to sink a query. `commit message sitepackage` answered with the
     * commit conventions until the title of the document with them counted
     * against its own words.
     *
     * @param array<string, mixed> $candidate
     * @return array<string, string>
     */
    private static function distinguishing(array $candidate): array
    {
        $fields = self::searchable($candidate);
        unset($fields['title']);

        return $fields;
    }

    /** @return array{0: string, 1: bool} */
    private static function shorten(string $body): array
    {
        if (strlen($body) <= self::MAX_SECTION_LENGTH) {
            return [$body, false];
        }

        $cut = substr($body, 0, self::MAX_SECTION_LENGTH);
        $lastBreak = strrpos($cut, "\n");
        $cut = $lastBreak === false ? $cut : substr($cut, 0, $lastBreak);

        // A cut that lands between the two fences of a code block hands over a
        // first ``` with nothing to close it. Every line the caller reads after
        // that is inside a code block that never ends. Which byte the budget
        // falls on is a property of the text above it, so this held only as
        // long as nobody edited the document. The half-open fence goes with the
        // cut.
        if (substr_count($cut, '```') % 2 !== 0) {
            $fence = strrpos($cut, '```');
            $cut = $fence === false ? $cut : substr($cut, 0, $fence);
        }

        return [rtrim($cut), true];
    }

    private static function readTitle(string $content): ?string
    {
        foreach (preg_split('/\n/', $content) ?: [] as $line) {
            $line = trim($line);
            if (str_starts_with($line, '# ')) {
                return trim(preg_replace('/^#\s+/', '', $line) ?? $line);
            }
        }

        return null;
    }
}
