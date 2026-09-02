<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use Symfony\Component\Finder\Finder;
use TYPO3\DevCompanion\Paths;
use TYPO3\DevCompanion\Search\TermSearch;
use TYPO3\DevCompanion\Search\Text;

/**
 * Loads hints from the JSON files under
 * knowledge/hints/ and matches them against paths/topics.
 *
 * Which domain a hint is asked from is the `domains` field of the entry, not
 * the file it sits in. The file name was both jobs at once for as long as there
 * was one file per domain, and the cost of that was a hint belonging to two
 * domains having nowhere to go: it went into `general.json`, whose domain is
 * selected by every query there is. The file is the subject now — one per
 * subject, named after it — and no hint is `any`.
 *
 * @phpstan-type Hint array{id: string, title: string, domains: array<int, string>, appliesTo: array<int, string>, hints: array<int, array{text: string, since: ?int, until: ?int, scope: ?Scope}>, category: string, scope: ?Scope}
 */
final class Hints
{
    /**
     * The two categories that describe the TYPO3 backend's own interface.
     *
     * They are named that way because they read as general otherwise: the Sass
     * structure, the `--typo3-*` custom properties and the Lit components are
     * the backend's, and a frontend theme that follows them is following the
     * wrong conventions. Every other category — PHP, Fluid, TypoScript — holds
     * for whatever is written in it.
     */
    public const CATEGORY_CSS = 'Backend CSS';

    /**
     * Both halves in the heading, because a caller says the second one.
     *
     * The sources are TypeScript and the conventions are TypeScript's, but a
     * `.js` file is what the backend ships and "javascript" is what somebody
     * types who has not opened `Build/Sources/` yet. A heading that names only
     * the source reads as somebody else's subject.
     */
    public const CATEGORY_TYPESCRIPT = 'Backend TypeScript and JavaScript';

    /**
     * What each domain is called where a caller reads it.
     *
     * The domain is the vocabulary the matcher selects by and the label is the
     * heading an answer prints, and they are two fields because they change for
     * different reasons: `Domains::CSS` is what a `.scss` path detects as, while
     * "Backend CSS" is a sentence to somebody who has to be told whose
     * conventions these are.
     *
     * @var array<string, string>
     */
    private const DOMAIN_LABELS = [
        Domains::PHP => 'PHP',
        Domains::TYPOSCRIPT => 'TypoScript',
        Domains::FLUID => 'Fluid',
        Domains::TYPESCRIPT => self::CATEGORY_TYPESCRIPT,
        Domains::CSS => self::CATEGORY_CSS,
        Domains::XLIFF => 'Labels',
        Domains::DOCS => 'Documentation',
        Domains::ANY => 'General',
    ];


    /**
     * What a hint is searched by, and what each field is worth.
     *
     * `appliesTo` was long the only one, and the cost of that is what put this
     * here: a hint was reachable through the handful of keywords somebody
     * thought of while writing it, and its own 200 words were invisible. The
     * statements a hint is made of are the part that names the symptom — «the
     * failure is a service-not-found at request time» — and a caller arriving
     * with the symptom is the caller the hint was written for.
     *
     * The curated vocabulary still outweighs the prose, so where somebody
     * anticipated a phrasing, that hint is what comes back.
     *
     * @var array<string, int>
     */
    private const FIELD_WEIGHTS = ['title' => 4, 'appliesTo' => 4, 'text' => 1];

    /**
     * Share of the query a hint's own words have to cover to answer on their
     * own, when no `appliesTo` pattern was matched. Below it the hint mentions
     * the subject rather than being about it.
     *
     * A share is what this is, so it is asked only where there is a part of the
     * query the hint does not carry: `coversEveryTerm()` is the other way past
     * the floor, and the two together are what keep the number a statement
     * about the query rather than about the hint's length.
     *
     * `bin/cli hints:probe` prints a hit's coverage against it, because how
     * near the floor a hit stands is what says whether the next statement
     * written into the corpus takes it away — `D-ANS-115`.
     */
    public const MIN_COVERAGE = 0.5;

    /**
     * Hint length that still counts for what its terms say.
     *
     * It was the mean hint body, so the ordinary hint is undamped and the
     * outliers are what this corrects — and they are why it is needed: hint
     * bodies are uncapped and differ by more than twenty times, from thirty
     * words to over a thousand. Without it the longest hint in the corpus
     * answered "file upload storage configuration" at full confidence, because
     * a text that long contains every one of those words somewhere.
     *
     * The corpus has since grown past it and the number stayed, because the
     * sweep it came off says it should: re-measured at a mean of 266, recall is
     * whole anywhere from 120 to 320 and the hints returned for it climb the
     * whole way, so the low end of the range is the precise one. It is a floor
     * the ordinary hint sits above now rather than the mean it was picked as,
     * and MAX_MEAN_BODY_WORDS is what says how far that may go.
     */
    public const UNDILUTED_WORDS = 200;

    /**
     * How long the mean hint body may get before UNDILUTED_WORDS stops holding.
     *
     * Not a property of the corpus but of the two together, and measured the
     * same way: above a mean of 320 words, «how do I write a good sonnet» is
     * answered — by `installation-upgrade`, which is long enough to contain it.
     * That is the failure the dilution weight exists to prevent, so the ceiling
     * sits below it with the margin the last growth spurt took.
     *
     * Nothing reads it at answer time. `bin/cli hints:coverage` reports against
     * it and `HintsTest` fails on it, and when it does the fix is to re-run the
     * sweep and pick the constant again — not to raise this number.
     */
    public const MAX_MEAN_BODY_WORDS = 300;

    /**
     * @param int|array<int, int>|null $target
     * @return array<int, Hint>
     */
    public static function load(int|array|null $target = null): array
    {
        $hints = [];
        foreach (Finder::create()->files()->in(Paths::knowledge() . '/hints')->depth(0)->name('*.json')->sortByName() as $file) {
            $decoded = json_decode((string) file_get_contents($file->getPathname()), true);
            if (!is_array($decoded)) {
                throw new \RuntimeException(sprintf('Invalid hints/%s', $file->getFilename()));
            }

            foreach ($decoded as $entry) {
                $domains = array_map('strval', $entry['domains'] ?? []);
                if ($domains === []) {
                    throw new \RuntimeException(sprintf(
                        'hints/%s: %s names no domain, so no query selects it.',
                        $file->getFilename(),
                        $entry['id'] ?? '?',
                    ));
                }

                $hints[] = [
                    'id' => (string) $entry['id'],
                    'title' => (string) $entry['title'],
                    'domains' => $domains,
                    'appliesTo' => array_map('strval', $entry['appliesTo'] ?? []),
                    'hints' => array_map(self::statement(...), $entry['hints'] ?? []),
                    // The first domain is the one an answer files the hint
                    // under. A hint that crosses domains is asked from all of
                    // them and printed under one, and which one that is is a
                    // judgment its author makes by writing it first.
                    'category' => self::label($domains[0]),
                    'scope' => isset($entry['scope']) ? Scope::from((string) $entry['scope']) : null,
                ];
            }
        }

        return self::forVersion($hints, $target);
    }

    /**
     * What every hint's body weighs, in words, by id.
     *
     * The length the dilution weight is read against, and the same length it is
     * applied to at answer time: the whole body is one field, so the number a
     * report prints and the number the matcher damps by are the same number.
     *
     * @return array<string, int>
     */
    public static function bodyWords(): array
    {
        $words = [];
        foreach (self::load() as $hint) {
            $words[$hint['id']] = str_word_count(implode(' ', array_column($hint['hints'], 'text')));
        }

        return $words;
    }

    /**
     * One statement, with the versions it holds for.
     *
     * A bare string is the ordinary case and means "everywhere this knowledge
     * base reaches". Only a statement that stops being true somewhere has to
     * say where, and it says it as data rather than in the sentence — so the
     * sentence stays the same sentence on every version it holds for, and the
     * range can be filtered, rendered and checked.
     *
     * `scope` is the same idea for which kind of work a statement obliges
     * rather than which version it holds on, and it sits here for the same
     * reason: one sentence in an otherwise transferable hint is what a
     * changelog obligation is, and splitting the hint to say so would duplicate
     * the six statements around it. Absent means Scope::Any — an API that
     * throws is an API that throws, in the core and in a sitepackage alike.
     *
     * @return array{text: string, since: ?int, until: ?int, scope: ?Scope}
     */
    private static function statement(mixed $entry): array
    {
        if (!is_array($entry)) {
            return ['text' => (string) $entry, 'since' => null, 'until' => null, 'scope' => null];
        }

        return [
            'text' => (string) ($entry['text'] ?? ''),
            'since' => isset($entry['since']) ? (int) $entry['since'] : null,
            'until' => isset($entry['until']) ? (int) $entry['until'] : null,
            'scope' => isset($entry['scope']) ? Scope::from((string) $entry['scope']) : null,
        ];
    }

    /**
     * The same hints with every statement that does not hold on the target
     * removed, and a hint that has nothing left removed with them.
     *
     * The target is one major, or the several a repository serves at once. A
     * statement kept for the range is one that holds on at least one of them:
     * an extension declaring `^13.4 || ^14.3` has to know both the rule that
     * arrived in 14 and the one that is still true on 13, and dropping either
     * makes the other read as the whole answer. Which majors a statement holds
     * on it says beside itself, as always.
     *
     * @param array<int, Hint> $hints
     * @param int|array<int, int>|null $target
     * @return array<int, Hint>
     */
    public static function forVersion(array $hints, int|array|null $target): array
    {
        $targets = is_array($target) ? array_values($target) : ($target === null ? [] : [$target]);
        if ($targets === []) {
            return $hints;
        }

        $kept = [];
        foreach ($hints as $hint) {
            $statements = array_values(array_filter(
                $hint['hints'],
                static function (array $statement) use ($targets): bool {
                    foreach ($targets as $major) {
                        if (Versions::holds($statement['since'], $statement['until'], $major)) {
                            return true;
                        }
                    }

                    return false;
                },
            ));
            if ($statements === []) {
                continue;
            }
            $hint['hints'] = $statements;
            $kept[] = $hint;
        }

        return $kept;
    }

    /**
     * One hint by its id, for the tools that carry a fixed piece of guidance
     * with their own answer instead of waiting for a matching query.
     *
     * @param int|array<int, int>|null $target
     * @return Hint|null
     */
    public static function byId(string $id, int|array|null $target = null): ?array
    {
        foreach (self::load($target) as $hint) {
            if ($hint['id'] === $id) {
                return $hint;
            }
        }

        return null;
    }

    /**
     * Matches hints against paths and task text, restricted to the domains the
     * input actually touches.
     *
     * A hint asked for by id is returned as it is. Matching is a guess about
     * what the caller meant; an id is not, and the index every answer carries
     * exists so that guessing at phrasings can be replaced by naming one.
     *
     * @param array<int, string> $paths
     * @param int|array<int, int>|null $target
     * @return array{
     *     matchedHints: array<int, array<string, mixed>>,
     *     domains: array<int, string>,
     *     withheldCategories: array<int, string>,
     *     availableHints: array<int, array{id: string, title: string, category: string}>,
     *     availableHintsWithheld: int
     * }
     */
    public static function find(array $paths, string $task, int $limit, ?string $id = null, int|array|null $target = null, bool $index = true): array
    {
        $id = trim((string) $id);
        if ($id !== '') {
            $hint = self::byId($id, $target);

            // A miss lists every id there is, because nothing narrows it and
            // because a miss that says nothing cannot be told from a subject
            // nobody wrote down — R-ANS-006, and not what the bound below is
            // about.
            if ($hint === null) {
                return [
                    'matchedHints' => [],
                    'domains' => [],
                    'withheldCategories' => [],
                    'availableHints' => self::index(null, [], $target),
                    'availableHintsWithheld' => 0,
                ];
            }

            // A hit lists what stands beside the hint in its own domains, and
            // that list is what 21 measured calls spent two thirds of their
            // answer on. So it is withheld unless the caller asks, and the
            // count is what says it was there — D-ANS-075.
            $beside = self::index($hint['domains'], [$id], $target);

            return [
                'matchedHints' => [$hint],
                // Nothing was inferred from paths or a task, so nothing is
                // claimed about them.
                'domains' => [],
                'withheldCategories' => [],
                'availableHints' => $index ? $beside : [],
                'availableHintsWithheld' => $index ? 0 : count($beside),
            ];
        }

        $task = trim($task);
        // Kept apart as well as together: a pattern is matched against the two
        // halves by different rules, because only one of them ends its words —
        // scoreKeywords().
        $pathsText = mb_strtolower(implode("\n", $paths));
        $taskText = mb_strtolower($task);
        $haystack = mb_strtolower(implode("\n", array_merge($paths, [$task])));
        $backendModule = Domains::namesBackendModule($paths, $task);
        $backendOnly = $backendModule || Domains::namesOnlyTheBackend($paths, $task);

        $domains = Domains::detect($paths, $task);
        $selected = Domains::hintDomains($domains);

        // Where the task is about the website, the two backend UI domains
        // are withheld rather than applied. A missing answer sends the caller
        // to the frontend documentation; an inverted one sends them to rewrite
        // a working theme against the backend's tokens.
        //
        // What is withheld is a hint whose domains are those two and nothing
        // else, because that is what makes it the backend's design system.
        // Building a sitepackage's assets and scanning a site for contrast are
        // asked in the same words and are not that hint; they say so by
        // carrying a third domain (`D-KNW-033`).
        $backendUi = [Domains::CSS, Domains::TYPESCRIPT];
        $withheld = [];
        $withholding = Domains::namesTheFrontend($paths, $task);
        if ($withholding) {
            $withheld = array_map(
                self::label(...),
                array_values(array_intersect($selected, $backendUi)),
            );
        }

        $inDomain = [];
        $outside = [];
        foreach (self::load($target) as $hint) {
            if ($withholding && array_diff($hint['domains'], $backendUi) === []) {
                continue;
            }
            if (array_intersect($hint['domains'], $selected) !== []) {
                $inDomain[] = $hint;
                continue;
            }
            $outside[] = $hint;
        }
        $candidates = array_merge($inDomain, self::crossingTheGate($outside, $inDomain, $taskText));
        if ($backendOnly) {
            // "sitepackage" is ownership context and "records" are what a
            // backend task works on. Neither asks for the package's frontend
            // layout or for rendering records on a page. Those two large hints
            // otherwise displace what the task explicitly named — and they win
            // on their bodies rather than on their vocabulary, because a
            // sitepackage layout is written in the words of the backend it is
            // administered from.
            $excluded = [];
            if (!self::asksForFrontendRendering($haystack)) {
                $excluded[] = 'frontend-records';
            }
            if (!self::asksForPackageLayout($haystack)) {
                $excluded[] = 'sitepackage-layout';
            }
            if (!self::asksForInitialContent($haystack)) {
                $excluded[] = 'sitepackage-initial-content';
            }
            $candidates = array_values(array_filter(
                $candidates,
                static fn(array $hint): bool => !in_array($hint['id'], $excluded, true),
            ));
        }

        // The weights are taken over the candidates rather than over every
        // hint there is, so a term says what it separates within the domains
        // actually being searched.
        $searchable = array_map(self::searchable(...), $candidates);
        $weights = TermSearch::weights(TermSearch::terms($task), $searchable);
        $askedFor = array_sum($weights);

        $scored = [];
        // What the floor turns away, kept where it was scored rather than
        // dropped: it is the second half of the index, and the score is the
        // only thing that orders it — `D-ANS-075`.
        $refused = [];
        foreach ($candidates as $index => $hint) {
            $keywords = self::scoreKeywords($hint, $taskText, $pathsText);
            [$score, $covered, $matchedTerms] = TermSearch::score(
                $searchable[$index],
                $weights,
                self::FIELD_WEIGHTS,
                self::UNDILUTED_WORDS,
            );
            $coverage = $askedFor > 0.0 ? $covered / $askedFor : 0.0;

            // Two ways in, and they answer different questions. A matched
            // pattern means somebody anticipated this phrasing — or the caller
            // named a path, which no term match can read. Coverage means the
            // hint is about what was asked whether or not anybody anticipated
            // it.
            if ($keywords === 0 && !self::coversEveryTerm($matchedTerms, $weights) && $coverage < self::MIN_COVERAGE) {
                $refused[] = ['hint' => $hint, 'score' => $score];
                continue;
            }

            // How it got here travels with it. Nothing in an answer reads it —
            // `bin/cli hints:probe` does, and a matcher that cannot say why it
            // returned something is one nobody can tune without guessing.
            //
            // The weights are the query's rather than this hint's, and they are
            // here because the question a probe is opened with is which word
            // decided: a term is worth what it separates within the candidates,
            // so writing an unrelated statement that uses a common word makes
            // that word cheaper for every query carrying it — `D-ANS-115`.
            $hint['matchedOn'] = [
                'keywords' => $keywords,
                'score' => $score,
                'coverage' => $coverage,
                'terms' => $matchedTerms,
                'weights' => $weights,
            ];
            $scored[] = ['hint' => $hint, 'keywords' => $keywords, 'score' => $score];
        }

        // Answering first, then curation. A pattern says somebody anticipated
        // this phrasing — or, for a path, only that the caller is standing
        // somewhere the hint claims — while the score says the hint's own words
        // are about what was asked. A hint scoring zero has answered nothing,
        // and it was outranking hints that had.
        //
        // `system-extension-boundaries` is what made that visible: it claims
        // `typo3/sysext/` and `sysext`, so it matched 19 characters' worth on
        // every core path there is and scored 0 on every query measured,
        // standing second on a FAL question above `fal-basics` and
        // `fal-storages-drivers`. The two false positives of `D-ANS-060` are
        // the same shape and this is the tier order that question left open.
        //
        // Within each half the old order stands, so a hint that answers and was
        // anticipated still comes before one that only answers: the longer
        // pattern is the more specific claim, and `D-ANS-050` still decides
        // what a pattern reaches.
        //
        // The keywords tier is above the score, so first place is usually
        // settled before a term weight is read at all — which is what makes a
        // test naming the first hit a claim about that hint rather than about
        // the rest of the corpus (`D-ANS-124`).
        usort($scored, static function (array $a, array $b): int {
            return ($b['score'] > 0 ? 1 : 0) <=> ($a['score'] > 0 ? 1 : 0)
                ?: $b['keywords'] <=> $a['keywords']
                ?: $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['title'], $b['hint']['title']);
        });
        if ($backendModule) {
            usort($scored, static function (array $a, array $b): int {
                $aModule = $a['hint']['id'] === 'backend-modules';
                $bModule = $b['hint']['id'] === 'backend-modules';

                return $bModule <=> $aModule;
            });
        }

        $matchedHints = array_map(
            static fn(array $entry): array => $entry['hint'],
            array_slice($scored, 0, $limit)
        );

        return [
            'matchedHints' => $matchedHints,
            'domains' => $domains,
            'withheldCategories' => $withheld,
            // What there was to find, minus what was found. A caller that
            // phrased the query differently from the hint has no way to tell
            // that from a subject nobody wrote down, and tries another phrasing
            // either way — and the answer that matched three hints about
            // something else leaves it in exactly that position, without even
            // an empty result to read as an absence (`D-KNW-055`).
            'availableHints' => self::available($scored, $refused, $limit),
            // Nothing is withheld from a query call: what it carries is the
            // matcher's own ranking of what it just scored, which is the list
            // D-ANS-075 was written for.
            'availableHintsWithheld' => 0,
        ];
    }

    /**
     * The hints outside the selected domains a symptom reaches anyway.
     *
     * A task names the layer it is in, while a symptom names the layer the
     * failure showed in and the mechanism that explains it lives in another. So
     * a hint crosses on a phrase somebody curated onto it, and only where no
     * selected hint claims that phrase too — without the claim check, "unit
     * test" puts test doubles back into a query about a `.ts` test and undoes
     * `D-KNW-067`. `D-ANS-084` carries the measurement and the wider rules it
     * ruled out. Only the task text is read: a path carries its own domain in
     * its extension.
     *
     * @param array<int, Hint> $outside
     * @param array<int, Hint> $inDomain
     * @return array<int, Hint>
     */
    private static function crossingTheGate(array $outside, array $inDomain, string $task): array
    {
        $claimed = [];
        foreach ($inDomain as $hint) {
            foreach (self::spelledOutPhrases($hint, $task) as $phrase) {
                $claimed[$phrase] = true;
            }
        }

        return array_values(array_filter(
            $outside,
            static fn(array $hint): bool => array_diff_key(
                array_flip(self::spelledOutPhrases($hint, $task)),
                $claimed,
            ) !== [],
        ));
    }

    /**
     * The curated phrases of a hint the task writes out, each reduced to its
     * terms so that the two hints claiming one phrase in different word forms
     * claim the same phrase: `javascript-unit-tests` carries "unit test" and
     * `unit-test-doubles` carries "unit tests", and compared as written those
     * are two.
     *
     * A pattern of one word is not one of these. That is what a hint is filed
     * under rather than what somebody anticipated being asked — `icon`, `menu`,
     * `fal` — and a single word is what the domain gate is there to place.
     *
     * @param array<string, mixed> $hint
     * @return array<int, string>
     */
    private static function spelledOutPhrases(array $hint, string $task): array
    {
        $spelled = [];
        foreach ($hint['appliesTo'] as $pattern) {
            $normalized = mb_strtolower((string) $pattern);
            // A path is not a phrase somebody anticipated being asked: it
            // carries its domain in its extension, which the gate already has.
            if (str_contains($normalized, '/') || str_contains($normalized, '\\')) {
                continue;
            }
            // And one word is what a hint is filed under rather than a
            // phrasing: placing one is what the domain gate is for. Counted in
            // terms rather than in spaces, so a quoted error message is the
            // phrase it reads as — `D-KNW-145`.
            $terms = TermSearch::terms($normalized);
            if (count($terms) < 2) {
                continue;
            }
            if (TermSearch::carriesWord($task, $normalized)) {
                $spelled[] = implode(' ', $terms);
            }
        }

        return $spelled;
    }

    private static function asksForFrontendRendering(string $haystack): bool
    {
        foreach ([
            'frontend', 'front-end', 'front end', 'page rendering', 'page template',
            'content element', 'detail view', 'list view', 'route enhancer',
        ] as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    private static function asksForPackageLayout(string $haystack): bool
    {
        foreach ([
            'sitepackage layout', 'site package layout', 'directory structure',
            'folder structure', 'page layout', 'backend layout',
        ] as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    private static function asksForInitialContent(string $haystack): bool
    {
        foreach ([
            'initial content', 'initialisation/', 'extension:setup', 'seed content',
            'ship content', 'data.t3d', 'data.xml', 'impexp', 'distribution',
        ] as $marker) {
            if (Text::containsWord($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The candidates this query left over, closest first.
     *
     * The rank is the matcher's own and exists at the moment the index is built,
     * so nothing here computes an order: what the limit cut keeps the order it
     * was cut in, and what the floor turned away follows by score — the index
     * used to be a second read of the corpus in file order (`D-ANS-075`). The
     * two tiers are not merged into one sort, because a refused hint can
     * outscore an admitted one: admission is the floor's judgement, a score is
     * not.
     *
     * @param array<int, array{hint: array<string, mixed>, keywords: int, score: int}> $scored
     * @param array<int, array{hint: array<string, mixed>, score: int}> $refused
     * @return array<int, array{id: string, title: string, category: string}>
     */
    private static function available(array $scored, array $refused, int $limit): array
    {
        usort($refused, static function (array $a, array $b): int {
            return $b['score'] <=> $a['score']
                ?: strcmp($a['hint']['title'], $b['hint']['title']);
        });

        return array_map(
            static fn(array $entry): array => self::reference($entry['hint']),
            array_merge(array_slice($scored, $limit), $refused),
        );
    }

    /**
     * Every hint there is, by id and title, optionally narrowed to domains and
     * without the ones an answer already carries in full.
     *
     * This is the id path, where nothing was matched and there is no rank to
     * order by. A query is answered from what it scored instead — available().
     *
     * The target is what the caller is on, so the index is what that caller can
     * ask for. Read without one it named ids `byId()` then refuses, and the
     * answer offered `record-routing` on 12 in the same breath as saying there
     * is no such hint.
     *
     * @param array<int, string>|null $domains
     * @param array<int, string> $except
     * @param int|array<int, int>|null $target
     * @return array<int, array{id: string, title: string, category: string}>
     */
    private static function index(?array $domains, array $except = [], int|array|null $target = null): array
    {
        $index = [];
        foreach (self::load($target) as $hint) {
            if ($domains !== null && array_intersect($hint['domains'], $domains) === []) {
                continue;
            }
            if (in_array($hint['id'], $except, true)) {
                continue;
            }
            $index[] = self::reference($hint);
        }

        return $index;
    }

    /**
     * What an entry of the index is: enough to ask for the hint by name, and
     * the category so a caller can tell whose subject it is.
     *
     * @param array<string, mixed> $hint
     * @return array{id: string, title: string, category: string}
     */
    private static function reference(array $hint): array
    {
        return [
            'id' => (string) $hint['id'],
            'title' => (string) $hint['title'],
            'category' => (string) $hint['category'],
        ];
    }

    /**
     * Groups matched hints into sections, preserving each hint's relative order
     * within a section and ordering sections by the known section order.
     *
     * @param array<int, array<string, mixed>> $hints
     * @return array<int, array{category: string, hints: array<int, array<string, mixed>>}>
     */
    public static function groupByCategory(array $hints): array
    {
        $grouped = [];
        foreach ($hints as $hint) {
            $grouped[$hint['category']][] = $hint;
        }

        $order = array_values(self::DOMAIN_LABELS);
        $rank = static function (string $category) use ($order): int {
            $index = array_search($category, $order, true);
            return $index === false ? count($order) : $index;
        };

        $sections = [];
        foreach ($grouped as $category => $sectionHints) {
            $sections[] = ['category' => (string) $category, 'hints' => $sectionHints];
        }

        usort($sections, static function (array $a, array $b) use ($rank): int {
            return $rank($a['category']) <=> $rank($b['category'])
                ?: strcmp($a['category'], $b['category']);
        });

        return $sections;
    }

    /**
     * The fields of a hint the matcher reads, keyed the way FIELD_WEIGHTS
     * names them.
     *
     * @param Hint $hint
     * @return array<string, string>
     */
    private static function searchable(array $hint): array
    {
        return [
            'title' => (string) $hint['title'],
            'appliesTo' => implode("\n", $hint['appliesTo']),
            'text' => implode("\n", array_column($hint['hints'], 'text')),
        ];
    }

    /**
     * Whether the hint's own words cover the whole query rather than part of it.
     *
     * MIN_COVERAGE asks what share of the query a hint accounts for and the
     * dilution weight damps that share by how much other text the terms were
     * found among. Where every term is in the hint there is no share left to
     * doubt, so damping it turns a full cover into a partial one — which took a
     * body past `UNDILUTED_WORDS * e` words out of queries it used to answer, at
     * a stroke rather than by degrees. Dilution stays where it earns its keep,
     * which is the partial cover — `D-ANS-025` is the sweep.
     *
     * @param array<string, string> $matchedTerms
     * @param array<string, float> $weights
     */
    private static function coversEveryTerm(array $matchedTerms, array $weights): bool
    {
        // A query with no terms of its own — paths alone — covers nothing
        // rather than everything, or saying nothing would admit every
        // candidate there is.
        return $weights !== [] && count($matchedTerms) === count($weights);
    }

    /**
     * How much of the curated vocabulary the query spells out.
     *
     * This runs the other way round from the term scoring beside it: the pattern
     * is searched in the query, not the query in the hint, because a pattern is
     * a phrase or a path fragment and what makes it a match is the caller naming
     * it. A longer pattern is the more specific claim and counts for more. The
     * two halves of the query are asked differently, which is the whole of what
     * `D-ANS-050` left here: a sentence ends its words and a path does not, so
     * the task text is asked for the word and the paths for the prefix, and a
     * pattern in both is still one match.
     *
     * @param Hint $hint
     */
    private static function scoreKeywords(array $hint, string $task, string $paths): int
    {
        $score = 0;
        foreach ($hint['appliesTo'] as $pattern) {
            $normalized = mb_strtolower($pattern);
            // A pattern carrying punctuation is a path fragment or a token —
            // `Classes/Resource/`, `.xlf`, `lll:` — and is specific enough that
            // it cannot land by accident, while anchoring it at a word boundary
            // would break on the punctuation itself. A pattern that is a bare
            // word gets the same treatment a query term does, because it goes
            // wrong the same way: `fal` matched "falsch" as a plain substring
            // and handed the File Abstraction Layer to a question about a
            // label.
            $matched = preg_match('/^[\p{L}\p{N} ]+$/u', $normalized) === 1
                ? TermSearch::carriesWord($task, $normalized) || TermSearch::carries($paths, $normalized)
                : str_contains($task, $normalized) || str_contains($paths, $normalized);
            if ($matched) {
                $score += strlen($normalized);
            }
        }

        return $score;
    }

    /**
     * What a domain is called in an answer. A domain nobody named a label for
     * is printed as it was written, which is how a new one announces itself.
     */
    public static function label(string $domain): string
    {
        return self::DOMAIN_LABELS[$domain] ?? ucfirst($domain);
    }
}
