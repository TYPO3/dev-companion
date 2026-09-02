<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Http\Recent;
use TYPO3\DevCompanion\Result\Unreachable;
use TYPO3\DevCompanion\Search\Text;

/**
 * The issue tracker a core patch starts from, read over its Redmine API.
 *
 * An answer is only an answer when it parses as the API: this host answers a
 * browser-shaped request with 200 and a challenge page, and reading that as
 * "no issue" would be the same mistake in the other direction. The `.json`
 * endpoint is asked for rather than the page, because the fields that decide
 * anything would be scraped anywhere else. Three ways in, because only one of
 * the three questions asked of one tracker starts from a number — `D-ANS-038`.
 */
final class Forge
{
    public const HOST = 'https://forge.typo3.org';

    /** The core's own project on the tracker, which is the one this reads. */
    public const PROJECT = 'typo3cms-core';

    /**
     * The `category` word that asks for the areas themselves.
     *
     * The tracker's own wildcard, which this file already passes it as
     * `status_id`. No area is named for it, so it cannot take a subject's place.
     */
    public const EVERY_AREA = '*';

    /** Where the project answers its own areas, which is the only read `*` does. */
    private const AREAS_URL = self::HOST . '/projects/' . self::PROJECT . '.json?include=issue_categories';

    /**
     * Seconds an answered read is held for.
     *
     * Longer than the review server's, because nothing the caller does through
     * this server changes what the tracker says: an issue's status, its target
     * version and its comments move when somebody else works on them, at the
     * pace people work. What this is against is a session walking a list of
     * issues and asking the tracker the same thing a dozen times.
     */
    public const HELD_FOR = 300;

    /**
     * Seconds the project's own lists — its categories and its members — are
     * held for.
     *
     * Two orders of magnitude longer than an issue's, because they answer how
     * the core is organised rather than what happened to one issue. A category
     * is added when a subsystem is and a membership when somebody joins, which
     * are things that happen between releases, while a session filtering by
     * area or by person asks for the same names on every call it makes.
     */
    public const LISTS_HELD_FOR = 86400;

    /**
     * How many journal entries come back. The decision is usually in the last
     * few, and an issue with two hundred comments is one nobody reads whole.
     */
    private const NOTES = 15;

    /**
     * The authors whose notes are a review server pinging the tracker.
     *
     * A list and not a rule: these are the two the core project has seen, and a
     * bot nobody has named here passes the filter. Which is why the count comes
     * back whether or not it was asked for — a journal full of patch-set pings
     * answering zero filtered is this list gone stale, visible without reading
     * the journal to find out.
     */
    private const BOTS = ['Gerrit Code Review', 'Mr. Hudson'];

    /**
     * The most hits a search answers with. The order is the tracker's own and
     * nothing here ranks, so a caller who reaches the end of one asks again in
     * other words rather than deeper — which is the answer to a set that looks
     * too narrow (`D-ANS-038`).
     */
    private const HITS = 25;

    /**
     * The most words a miss is asked about one at a time, and beyond it nothing
     * is asked at all.
     *
     * A read takes about two and a half seconds against forge.typo3.org,
     * measured on 2026-08-25, so the probe is what a caller waits through on
     * the path that answered nothing. A query longer than this is one whose
     * answer is to pass fewer words, which the miss says without reading
     * anything.
     */
    private const TERMS = 6;

    /**
     * The most issues an enumeration answers with. Higher than a search's,
     * because a triage picks candidates out of the set it is shown and a set
     * that has to be paged through is one nobody sees the shape of.
     */
    private const LISTED = 50;

    /** The most memberships one page of them holds, which is Redmine's own cap. */
    private const MEMBERS = 100;

    /** The most issues one page of a counted read holds, the same cap. */
    private const COUNTED = 100;

    /**
     * The most pages of them a shape is counted over. Ten reads take about
     * seven seconds against forge.typo3.org, measured on 2026-08-19, and what
     * a set larger than a thousand owes the caller is the bound rather than
     * the wait.
     */
    private const COUNTED_PAGES = 10;

    /**
     * The most buckets a dimension answers with. The tail of an area count is
     * twenty subsystems holding one issue each: 38 areas over the 621 issues
     * one person had filed on 2026-08-19, of which the largest four held two
     * thirds.
     */
    private const BUCKETS = 12;

    /**
     * The most pages of them read, whatever the project says it has. The count
     * comes from the tracker and the loop ends on it; this is what keeps a
     * count that is wrong from turning one call into a thousand. The core
     * project had 185 members on 2026-08-19.
     */
    private const MEMBER_PAGES = 5;

    /**
     * The tracker ids `/trackers.json` answered with on 2026-08-05.
     *
     * The API filters by id and nobody says "tracker 1", so the caller's word
     * is translated here. Held as data rather than fetched: a twelfth tracker
     * would cost every enumeration a second round trip to find out about, and
     * what it costs to be missing is one filter nobody can ask for.
     */
    private const TRACKERS = [
        'Bug' => 1,
        'Feature' => 2,
        'Major Feature' => 5,
        'Support' => 3,
        'Task' => 4,
        'Story' => 6,
        'Suggestion' => 7,
        'Impediment' => 9,
        'Epic' => 10,
        'Work Package' => 11,
        'Topic' => 12,
    ];

    /**
     * The transport every instance built without one takes.
     *
     * `ForgeLookup` builds its own, so a test driving the tool itself — its
     * text half, which is where a caller reads what to do about a miss — has
     * nowhere else to hand a transport in. `R-COD-003`.
     *
     * @var (\Closure(string): ?string)|null
     */
    private static ?\Closure $transport = null;

    private readonly Fetch $fetch;

    /**
     * The review server, asked one question: whether a change names a row of
     * an enumeration. The tracker cannot answer it — the journal that carries a
     * change reference is not in the index answer at all (`D-ANS-069`).
     */
    private readonly Gerrit $review;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport ?? self::$transport);
        $this->review = new Gerrit($transport ?? self::$transport);
    }

    /**
     * What a test hands in, so nothing it drives reaches forge.typo3.org. Null
     * puts the host back.
     *
     * @param (\Closure(string): ?string)|null $reader
     */
    public static function useReader(?\Closure $reader): void
    {
        self::$transport = $reader;
    }

    /**
     * One issue, with what was decided about it.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, issue: ?array<string, mixed>, cause: ?string}
     */
    public function issue(string $issue, string $notes = 'all'): array
    {
        $number = ltrim(trim($issue), '#');
        $url = self::HOST . '/issues/' . rawurlencode($number) . '.json?include=journals,relations,attachments';

        $answer = $this->api($url, 'issue');
        // A tracker that says 404 has answered: there is no such issue, which
        // is a different thing to tell a caller than that it could not be
        // reached.
        if ($answer['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'issue' => null, 'cause' => null];
        }
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'issue' => null, 'cause' => $answer['cause']];
        }

        $found = $this->referenced([self::issueOf($answer['part'], $number, $notes)])[0];
        $found['reviews'] = $this->changesOnReview($found['reviews'], (int) $found['id']);

        return [
            'status' => 'answered',
            'url' => $url,
            'issue' => $found,
            'cause' => null,
        ];
    }

    /**
     * The handles the issue's own text names and the changes the review server
     * holds for its number, as one list.
     *
     * Neither half contains the other. A change discussed in a comment whose
     * commit message never named the issue is missing from the review server's
     * answer; a change that names the issue only in its commit message is
     * missing from the text. An empty list means neither has one, which is what
     * a caller was already reading it as — `D-ANS-125`.
     *
     * Deduplicated by change number, since a change named in a comment and
     * found by commit message is one change; a handle that carries a Change-Id
     * and no number has nothing to match on and stands as it came.
     *
     * @param list<array<string, mixed>> $named what the notes carry
     * @return list<array<string, mixed>>
     */
    private function changesOnReview(array $named, int $number): array
    {
        $reviews = [];
        foreach ($named as $review) {
            $reviews[] = $review + ['status' => ''];
        }

        $at = [];
        foreach ($reviews as $index => $review) {
            if ($review['change'] > 0) {
                $at[(int) $review['change']] = $index;
            }
        }

        foreach ($this->review->changesForIssues([$number])[$number] ?? [] as $change) {
            $status = is_string($change['status'] ?? null) ? $change['status'] : '';
            if (isset($at[$change['number']])) {
                $reviews[$at[$change['number']]]['status'] = $status;
                continue;
            }
            $reviews[] = [
                'change' => $change['number'],
                'changeId' => '',
                'patchSet' => 0,
                'on' => '',
                'url' => $change['url'],
                'status' => $status,
            ];
        }

        return $reviews;
    }

    /**
     * The issues these records name, filled with what decides whether to read
     * one — the relations they are filed against, and the citations an issue's
     * prose carries.
     *
     * One bulk read for everything handed in rather than one per reference
     * (`D-ANS-064`), which is why a whole page goes through here at once. Where
     * it cannot be reached the relations stand as they came back.
     *
     * A citation is the other way round: it is kept only where the read
     * answered for the number, because three digits in a sentence are as often
     * a version as an issue and the resolving read is what tells the two apart
     * (`D-ANS-123`). So a tracker that did not answer drops them, which is the
     * same answer as a number nobody filed.
     *
     * @param list<array<string, mixed>> $records
     * @return list<array<string, mixed>>
     */
    private function referenced(array $records): array
    {
        $numbers = [];
        foreach ($records as $record) {
            $numbers = [
                ...$numbers,
                ...array_column($record['relations'], 'issue'),
                ...array_column($record['mentioned'] ?? [], 'issue'),
            ];
        }

        $fields = $this->fields($numbers);
        foreach ($records as $at => $record) {
            foreach ($record['relations'] as $index => $relation) {
                $read = $fields[$relation['issue']] ?? null;
                $records[$at]['relations'][$index]['subject'] = $read['subject'] ?? '';
                $records[$at]['relations'][$index]['tracker'] = $read['tracker'] ?? '';
                $records[$at]['relations'][$index]['status'] = $read['status'] ?? '';
            }
            if (!isset($record['mentioned'])) {
                continue;
            }
            $mentioned = [];
            foreach ($record['mentioned'] as $mention) {
                $read = $fields[$mention['issue']] ?? null;
                if ($read === null) {
                    continue;
                }
                $mention['subject'] = $read['subject'];
                $mention['tracker'] = $read['tracker'];
                $mention['status'] = $read['status'];
                $mentioned[] = $mention;
            }
            $records[$at]['mentioned'] = $mentioned;
        }

        return $records;
    }

    /**
     * The changes the review server holds for these rows, one query per twelve.
     *
     * The state comes with the handle, because the batched query already
     * answers it per change and dropping it made a caller pay a call for what
     * was in the payload (`D-ANS-069`); where the review server cannot be
     * reached the rows stand as they came back.
     *
     * @param list<array<string, mixed>> $results
     * @return list<array<string, mixed>>
     */
    private function reviewed(array $results): array
    {
        $changes = $this->review->changesForIssues(array_column($results, 'issue'));
        foreach ($results as $at => $entry) {
            foreach ($changes[$entry['issue']] ?? [] as $change) {
                $results[$at]['reviews'][] = [
                    'change' => $change['number'],
                    'status' => is_string($change['status'] ?? null) ? $change['status'] : '',
                    'url' => $change['url'],
                ];
            }
        }

        return $results;
    }

    /**
     * The issues whose text matches these words.
     *
     * Nothing here ranks, and the query comes back with the answer, because one
     * wording does not settle which issue is the duplicate (`D-ANS-038`).
     * `issues=1` is what keeps wiki pages, forum posts and changesets out of an
     * answer whose entries are issue numbers.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, query: string, total: int, terms: list<array{term: string, matchCount: int}>, results: list<array<string, mixed>>, cause: ?string}
     */
    public function search(string $query, int $limit = 15): array
    {
        $words = trim($query);
        $url = self::HOST . '/search.json?q=' . rawurlencode($words)
            . '&issues=1&limit=' . max(1, min(self::HITS, $limit));

        $answer = $this->api($url, 'results');
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'query' => $words, 'total' => 0, 'terms' => [], 'results' => [], 'cause' => $answer['cause']];
        }

        $results = [];
        foreach ($answer['part'] as $hit) {
            if (is_array($hit)) {
                $results[] = self::hit($hit);
            }
        }

        return [
            'status' => $results === [] ? 'empty' : 'answered',
            'url' => $url,
            'query' => $words,
            'total' => $answer['total'],
            'terms' => $results === [] ? $this->reach($words) : [],
            'results' => $this->filled($results),
            'cause' => null,
        ];
    }

    /**
     * What each word of an emptied query reaches on its own.
     *
     * This is the half of a miss nothing on this side can supply. Two class
     * names look alike from here and the tracker knows one of them:
     * `RendererRegistry` reached 5 issues on 2026-08-25 and
     * `FileRendererInterface` reached none, so the advice to drop the
     * identifiers would have dropped the word that answers (`D-ANS-038`).
     *
     * One read per word rather than one re-read of the query with the AND off.
     * That re-read is a single call and answers something else: the union is
     * ordered by issue number and its size is the commonest word's, so the same
     * four words answered 14673 that day with none of the five in the first
     * page of them.
     *
     * Asked on the miss alone, and each is held like any other read, so a
     * session rewording around one term pays for it once.
     *
     * @return list<array{term: string, matchCount: int}>
     */
    private function reach(string $query): array
    {
        $words = [];
        foreach (preg_split('/\s+/', $query) ?: [] as $word) {
            if ($word !== '') {
                $words[strtolower($word)] = $word;
            }
        }
        // One word has nothing to tell apart, and its count is the zero the
        // caller is already holding.
        if (count($words) < 2 || count($words) > self::TERMS) {
            return [];
        }

        $reach = [];
        foreach ($words as $word) {
            $answer = $this->api(self::HOST . '/search.json?q=' . rawurlencode($word) . '&issues=1&limit=1', 'results');
            // A host that stopped answering says nothing about the words left,
            // and a miss that already answered is not turned into an outage by
            // it.
            if ($answer['part'] === null) {
                return $reach;
            }
            $reach[] = ['term' => $word, 'matchCount' => $answer['total']];
        }

        return $reach;
    }

    /**
     * The fields a search hit is not made of, read for the whole page at once.
     *
     * `/search.json` answers with a title and a URL, so the area, the reporter,
     * the assignee and the two dates are absent from every hit — and a record carrying them
     * empty is a false statement rather than a missing one: it reads as an
     * issue nobody has categorised, nobody holds and nothing has moved on. A
     * session took 50 of 50 rows that way on 2026-08-05
     * (`feedback/2026-08-05-033902`), and the search path is where a triage
     * asks about age.
     *
     * Where it cannot be reached the hits stand as they came back. A search
     * that answered is not turned into an outage by a second call that did not.
     *
     * @param list<array<string, mixed>> $results
     * @return list<array<string, mixed>>
     */
    private function filled(array $results): array
    {
        $fields = $this->fields(array_column($results, 'issue'));
        if ($fields === []) {
            return $results;
        }

        foreach ($results as $at => $hit) {
            $read = $fields[$hit['issue']] ?? null;
            if ($read === null) {
                continue;
            }
            $results[$at]['category'] = $read['category'];
            $results[$at]['reportedBy'] = $read['reportedBy'];
            $results[$at]['assignedTo'] = $read['assignedTo'];
            $results[$at]['createdOn'] = $read['createdOn'];
            $results[$at]['updatedOn'] = $read['updatedOn'];
            // The tracker and the status are read off the title, which is the
            // tracker's own wording and usually parses. Where it did not, they
            // are fields here.
            $results[$at]['tracker'] = $hit['tracker'] !== '' ? $hit['tracker'] : $read['tracker'];
            $results[$at]['status'] = $hit['status'] !== '' ? $hit['status'] : $read['status'];
        }

        return $results;
    }

    /**
     * The fields of a set of issues, read in one request.
     *
     * What makes filling a search page and an issue's relations cheaper than
     * explaining why they are bare. The review server fills the issues a commit
     * message names from here too, which is what it is public for
     * (`D-ANS-098`).
     *
     * An empty answer is what could not be reached, and every caller here reads
     * it as "leave what came back alone".
     *
     * @param list<mixed> $numbers
     * @return array<int, array<string, mixed>>
     */
    public function fields(array $numbers): array
    {
        $fields = [];
        foreach ($this->issuesOf($numbers) as $entry) {
            $read = self::entry($entry);
            $fields[$read['issue']] = $read;
        }

        return $fields;
    }

    /**
     * The rows a set of issue numbers are, as the tracker sends them.
     *
     * `/issues.json` filtered by an id list answers subject, tracker, status,
     * area, author, assignee and both dates for the whole set in one request.
     * `status_id=*` is what keeps a closed one in it — every caller here asks
     * about issues that are usually closed, and the default of that endpoint is
     * open ones.
     *
     * @param list<mixed> $numbers
     * @return list<array<string, mixed>>
     */
    private function issuesOf(array $numbers): array
    {
        $wanted = [];
        foreach ($numbers as $number) {
            if (is_int($number) && $number > 0) {
                $wanted[$number] = $number;
            }
        }
        if ($wanted === []) {
            return [];
        }

        $url = self::HOST . '/issues.json?' . http_build_query([
            'issue_id' => implode(',', $wanted),
            'status_id' => '*',
            'limit' => count($wanted),
        ]);
        $answer = $this->api($url, 'issues');

        $rows = [];
        foreach ($answer['part'] ?? [] as $entry) {
            if (is_array($entry)) {
                $rows[] = $entry;
            }
        }

        return $rows;
    }

    /**
     * The issues of the core project, at whichever end of it was asked for.
     *
     * What this answers is the question a triage starts from and neither of the
     * two above reaches: an issue nobody has looked at since 2015 is found by
     * no number, because nobody holds it, and by no wording, because its
     * wording is the one nobody thought of. So the filter is the way in — the
     * tracker's own `status_id=open`, which is every status it has not marked
     * closed and therefore includes `Postponed`.
     *
     * `newest` is the other end, where the question is whether a defect
     * somebody has just found is already filed. `createdSince` is what makes
     * that end a set rather than a page of one, and it narrows where an area
     * cannot — `D-ANS-116`.
     *
     * `total` comes back with the page, because a caller shown 30 of something
     * has to be able to see whether 30 was the set or the first screenful of
     * it.
     *
     * The person filters are the other way in and the one a status widens for:
     * what somebody still has open is a question about the backlog, what they
     * have filed over the years is a question about a person, and only the
     * second needs the closed issues in the set. `involving` is that question
     * asked the way somebody says it out loud, which the tracker cannot be
     * asked at all — it ANDs its filters, so a union is two reads and a merge
     * (`D-ANS-090`).
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, total: int, categories: list<string>, categoriesUsed: list<string>, people: list<array{filter: string, asked: string, name: string, id: int, candidates: list<string>}>, breakdown: ?array<string, mixed>, results: list<array<string, mixed>>, cause: ?string}
     */
    public function backlog(
        string $order = 'oldest',
        string $tracker = '',
        string $category = '',
        string $createdBefore = '',
        string $createdSince = '',
        string $updatedBefore = '',
        int $limit = 15,
        string $status = 'open',
        string $reportedBy = '',
        string $assignedTo = '',
        string $involving = '',
        bool $breakdown = false,
    ): array {
        $categories = $this->categories();
        // The vocabulary asked for on purpose, and no issue read at all. Every
        // other call is answered with the areas only where a word named none or
        // several, which is what keeps 54 names off the answers nobody asked
        // them of (`feedback/2026-08-19-134717`) — and left the enumeration
        // reachable by a wrong word alone (`D-KNW-113`).
        if ($category === self::EVERY_AREA) {
            return [
                'status' => $categories === [] ? 'unavailable' : 'answered',
                'url' => self::AREAS_URL,
                'total' => 0,
                'categories' => array_keys($categories),
                'categoriesUsed' => [],
                'people' => [],
                'breakdown' => null,
                'results' => [],
                'cause' => $categories === [] ? Unreachable::NOT_ANSWERING : null,
            ];
        }
        $used = $category === '' ? [] : self::named($categories, $category);

        $filters = [
            // The tracker's own three: `open` is every status it has not marked
            // closed, and `*` is what puts a person's whole history in reach.
            'status_id' => match ($status) {
                'closed' => 'closed',
                'all' => '*',
                default => 'open',
            },
            'sort' => match ($order) {
                'stale' => 'updated_on:asc',
                'newest' => 'created_on:desc',
                default => 'created_on:asc',
            },
        ];
        if (isset(self::TRACKERS[$tracker])) {
            $filters['tracker_id'] = (string) self::TRACKERS[$tracker];
        }
        if ($used !== []) {
            // The tracker's own alternation, which is what makes a word naming
            // three categories one call rather than three.
            $filters['category_id'] = implode('|', array_map(
                static fn(string $name): int => $categories[$name],
                $used,
            ));
        }
        $people = [];
        // `involving` is the same person on either side, so it takes the place
        // of the two that name one side each.
        $roles = $involving !== ''
            ? ['involving' => $involving]
            : ['reportedBy' => $reportedBy, 'assignedTo' => $assignedTo];
        $sides = [];
        foreach ($roles as $filter => $word) {
            if ($word === '') {
                continue;
            }
            $person = $this->person($word);
            $people[] = ['filter' => $filter, 'asked' => $word] + $person;
            if ($person['id'] === 0) {
                continue;
            }
            if ($filter === 'involving') {
                $sides = [['author_id' => (string) $person['id']], ['assigned_to_id' => (string) $person['id']]];
            } else {
                $filters[$filter === 'reportedBy' ? 'author_id' : 'assigned_to_id'] = (string) $person['id'];
            }
        }

        // A date the tracker cannot read is answered with the unfiltered set,
        // which is the wrong answer wearing a right one's shape. Only a date
        // reaches the query.
        $filed = self::within($createdSince, $createdBefore);
        if ($filed !== '') {
            $filters['created_on'] = $filed;
        }
        if (self::isDate($updatedBefore)) {
            $filters['updated_on'] = '<=' . $updatedBefore;
        }

        // Only where it can do the work it is answered for: correcting a word
        // that named no area or several. On every other call it is 54 names the
        // caller did not ask for, three times over in one session
        // (`feedback/2026-08-19-134717`).
        $known = $category !== '' && count($used) !== 1 ? array_keys($categories) : [];

        // What the index answers for nothing where it is asked for: the
        // journal is the one thing it will not serve however it is asked, which
        // is why the review server is a call of its own below. A counted read
        // wants none of it — it is reading a thousand rows for four fields.
        $page = $breakdown
            ? ['limit' => (string) self::COUNTED]
            : ['limit' => (string) max(1, min(self::LISTED, $limit)), 'include' => 'relations,attachments'];
        $reads = array_map(
            static fn(array $side): string => self::HOST . '/projects/' . self::PROJECT . '/issues.json?'
                . http_build_query($page + $side + $filters),
            $sides === [] ? [[]] : $sides,
        );
        $url = implode(' ', $reads);

        // A word matching no category, and a name matching no person, would
        // otherwise be answered with the unfiltered backlog — a set about
        // everything wearing the shape of a set about one thing.
        $unresolved = array_filter($people, static fn(array $person): bool => $person['id'] === 0);
        if (($category !== '' && $used === []) || $unresolved !== []) {
            return ['status' => 'empty', 'url' => $url, 'total' => 0, 'categories' => $known, 'categoriesUsed' => $category !== '' ? $used : [], 'people' => $people, 'breakdown' => null, 'results' => [], 'cause' => null];
        }

        $answer = $breakdown ? $this->shape($reads) : $this->page($reads, max(1, min(self::LISTED, $limit)), $filters['sort']);
        // Two reads answer one question, and their counts overlap by the issues
        // this person both filed and holds. The tracker ANDs its filters, so
        // that overlap is a third read of one row and the only thing that makes
        // the union countable at all.
        if ($answer['rows'] !== null && count($reads) > 1) {
            $answer['total'] -= $this->overlap($sides, $filters);
        }
        if ($answer['rows'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'categories' => $known, 'categoriesUsed' => $used, 'people' => $people, 'breakdown' => null, 'results' => [], 'cause' => $answer['cause']];
        }

        if ($breakdown) {
            return [
                'status' => $answer['rows'] === [] ? 'empty' : 'answered',
                'url' => $url,
                'total' => $answer['total'],
                'categories' => $known,
                'categoriesUsed' => $used,
                'people' => $people,
                'breakdown' => [
                    'read' => count($answer['rows']),
                    'complete' => $answer['complete'],
                    'counts' => self::counts($answer['rows']),
                ],
                'results' => [],
                'cause' => null,
            ];
        }

        $results = [];
        foreach ($answer['rows'] as $raw) {
            $row = self::entry($raw);
            // The index answer carries the description and no journal, so a row
            // is read for what its subject and its report name and says nothing
            // about the comments — `D-ANS-122`.
            $row['cites'] = CitedCode::in(
                is_string($raw['subject'] ?? null) ? $raw['subject'] : '',
                is_string($raw['description'] ?? null) ? $raw['description'] : '',
            );
            $results[] = $row;
        }

        return [
            'status' => $results === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => $answer['total'],
            'categories' => $known,
            'categoriesUsed' => $used,
            'people' => $people,
            'breakdown' => null,
            'results' => $this->reviewed($this->referenced($results)),
            'cause' => null,
        ];
    }

    /**
     * One page of each read, as one page of the union.
     *
     * Where two reads answer one question, taking the first `limit` of each and
     * the first `limit` of what they merge to is the first `limit` of the
     * union: both come back in the same order, so nothing dropped from either
     * page can sort ahead of what was kept.
     *
     * @param list<string> $reads
     * @return array{rows: ?list<array<string, mixed>>, total: int, complete: bool, cause: ?string}
     */
    private function page(array $reads, int $limit, string $sort): array
    {
        $rows = [];
        $total = 0;
        foreach ($reads as $read) {
            $answer = $this->api($read, 'issues');
            if ($answer['part'] === null) {
                return ['rows' => null, 'total' => 0, 'complete' => false, 'cause' => $answer['cause']];
            }
            $total += $answer['total'];
            $rows = self::merged($rows, $answer['part']);
        }

        return [
            'rows' => count($reads) === 1 ? array_values($rows) : array_slice(self::inOrder($rows, $sort), 0, $limit),
            'total' => $total,
            'complete' => true,
            'cause' => null,
        ];
    }

    /**
     * Every issue the reads match, for the shape of the set rather than a page
     * of it.
     *
     * A person's history is one well-defined set with no other words to narrow
     * it by, so a page of 50 out of 621 leaves the rest reachable by nothing
     * (`feedback/2026-08-19-134651`). What answers that question is how the set
     * is distributed, and what that costs is reading it — a hundred rows to a
     * request, bounded, and the answer says where the bound cut it.
     *
     * @param list<string> $reads
     * @return array{rows: ?list<array<string, mixed>>, total: int, complete: bool, cause: ?string}
     */
    private function shape(array $reads): array
    {
        $rows = [];
        $total = 0;
        $complete = true;
        foreach ($reads as $read) {
            $offset = 0;
            do {
                $answer = $this->api($read . '&offset=' . $offset, 'issues');
                if ($answer['part'] === null) {
                    return ['rows' => null, 'total' => 0, 'complete' => false, 'cause' => $answer['cause']];
                }
                $rows = self::merged($rows, $answer['part']);
                $offset += self::COUNTED;
                $bounded = $offset >= self::COUNTED * self::COUNTED_PAGES;
            } while ($offset < $answer['total'] && !$bounded);
            $total += $answer['total'];
            $complete = $complete && $offset >= $answer['total'];
        }

        return ['rows' => array_values($rows), 'total' => $total, 'complete' => $complete, 'cause' => null];
    }

    /**
     * How many issues both reads of a union carry, which is what its two counts
     * count twice.
     *
     * @param list<array<string, string>> $sides
     * @param array<string, string>       $filters
     */
    private function overlap(array $sides, array $filters): int
    {
        $answer = $this->api(
            self::HOST . '/projects/' . self::PROJECT . '/issues.json?'
                . http_build_query(['limit' => '1'] + array_merge(...$sides) + $filters),
            'issues',
        );

        return $answer['total'];
    }

    /**
     * The rows of an answer, added to what the reads before it carried, each
     * issue once.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param array<mixed>                     $answered
     * @return array<int, array<string, mixed>>
     */
    private static function merged(array $rows, array $answered): array
    {
        foreach ($answered as $entry) {
            if (is_array($entry) && isset($entry['id']) && is_numeric($entry['id'])) {
                $rows[(int) $entry['id']] = $entry;
            }
        }

        return $rows;
    }

    /**
     * The rows in the order the tracker was asked for them, which a merge of
     * two answers no longer carries.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private static function inOrder(array $rows, string $sort): array
    {
        $field = str_starts_with($sort, 'updated_on') ? 'updated_on' : 'created_on';
        // Which way round, and not only which field: the recent end is the same
        // sort read backwards, and a merge that ignores that keeps the oldest
        // rows of a page the caller asked the newest of.
        $direction = str_ends_with($sort, ':desc') ? -1 : 1;
        $sorted = array_values($rows);
        usort($sorted, static fn(array $one, array $other): int => $direction * strcmp(
            is_string($one[$field] ?? null) ? $one[$field] : '',
            is_string($other[$field] ?? null) ? $other[$field] : '',
        ));

        return $sorted;
    }

    /**
     * How a set of issues is distributed, one list per dimension.
     *
     * The four a person's history is read by: what came of them, what kind of
     * work they were, which part of the core, and when. Ordered by size and
     * bounded, because the tail of an area count is twenty subsystems holding
     * one issue each and what it says is already said by the head.
     *
     * @param list<array<string, mixed>> $rows
     * @return list<array{dimension: string, buckets: list<array{name: string, count: int}>, withheldBuckets: int, withheldCount: int}>
     */
    private static function counts(array $rows): array
    {
        $counts = ['status' => [], 'tracker' => [], 'category' => [], 'year' => []];
        foreach ($rows as $row) {
            foreach (['status', 'tracker', 'category'] as $dimension) {
                // An issue nobody filed under an area is a bucket of its own,
                // because leaving it out makes the areas add up to less than
                // the set and nothing says why.
                $name = self::name($row[$dimension] ?? null);
                $counts[$dimension][$name === '' ? 'none' : $name] ??= 0;
                $counts[$dimension][$name === '' ? 'none' : $name]++;
            }
            $year = substr(is_string($row['created_on'] ?? null) ? $row['created_on'] : '', 0, 4);
            if ($year !== '') {
                $counts['year'][$year] ??= 0;
                $counts['year'][$year]++;
            }
        }

        $answer = [];
        foreach ($counts as $dimension => $buckets) {
            // By size, and by name where two are the same size: an answer that
            // reorders between two identical calls is one nobody can diff.
            uksort($buckets, static fn(string $one, string $other): int => [$buckets[$other], $one] <=> [$buckets[$one], $other]);
            $shown = array_slice($buckets, 0, self::BUCKETS, true);
            $answer[] = [
                'dimension' => $dimension,
                'buckets' => array_map(
                    static fn(string $name, int $count): array => ['name' => $name, 'count' => $count],
                    array_keys($shown),
                    array_values($shown),
                ),
                'withheldBuckets' => count($buckets) - count($shown),
                'withheldCount' => array_sum($buckets) - array_sum($shown),
            ];
        }

        return $answer;
    }

    /** A day the tracker can filter on, and nothing else. */
    private static function isDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }

    /**
     * The two ends of one date field as one filter, and empty where neither is
     * a day.
     *
     * Both ends written as two filters would be one filter and the other
     * silently gone: the tracker takes one value per field, and `><a|b` is its
     * own way of saying a window, read from it in `D-ANS-116`.
     */
    private static function within(string $since, string $before): string
    {
        $from = self::isDate($since) ? $since : '';
        $to = self::isDate($before) ? $before : '';
        if ($from !== '' && $to !== '') {
            return '><' . $from . '|' . $to;
        }

        return match (true) {
            $from !== '' => '>=' . $from,
            $to !== '' => '<=' . $to,
            default => '',
        };
    }

    /**
     * The categories the core project files its issues under, name to id.
     *
     * Read from the project rather than written down here. A list in this file
     * is one the core can add to without anything reporting it, and the
     * addition is exactly the subsystem somebody would be filtering for — a
     * category nobody can name is a filter that answers nothing and looks like
     * an empty backlog. What keeps it from being a round trip per call is the
     * hold, not a copy.
     *
     * @return array<string, int>
     */
    public function categories(): array
    {
        $answer = $this->api(self::AREAS_URL, 'project', self::LISTS_HELD_FOR);

        $categories = [];
        $listed = $answer['part']['issue_categories'] ?? null;
        foreach (is_array($listed) ? $listed : [] as $category) {
            if (is_array($category) && is_string($category['name'] ?? null) && is_numeric($category['id'] ?? null)) {
                $categories[$category['name']] = (int) $category['id'];
            }
        }

        return $categories;
    }

    /**
     * The people the core project is worked by, name to id.
     *
     * The tracker filters by a numeric user id, and it serves no public user
     * list: `/users.json` answers 401 without an administrator's credential. So
     * a caller holding a name and nothing else cannot ask a question about a
     * person at all, and the memberships of the project are the one place it
     * answers names and ids together for nobody in particular.
     *
     * It is who the project is worked by rather than everybody who has ever
     * filed something — 24 of the 100 most recently filed issues were reported
     * from outside it, measured on 2026-08-19, which is what `found()` is the
     * fallback for.
     *
     * @return array<string, int>
     */
    public function people(): array
    {
        $people = [];
        $offset = 0;
        do {
            $url = self::HOST . '/projects/' . self::PROJECT . '/memberships.json?'
                . http_build_query(['limit' => self::MEMBERS, 'offset' => $offset]);
            $answer = $this->api($url, 'memberships', self::LISTS_HELD_FOR);
            foreach ($answer['part'] ?? [] as $membership) {
                // A membership is a person or a group, and only a person files
                // and is assigned issues.
                $user = is_array($membership) ? $membership['user'] ?? null : null;
                if (is_array($user) && is_string($user['name'] ?? null) && is_numeric($user['id'] ?? null)) {
                    $people[$user['name']] = (int) $user['id'];
                }
            }
            $offset += self::MEMBERS;
        } while ($answer['part'] !== null && $offset < min($answer['total'], self::MEMBERS * self::MEMBER_PAGES));

        return $people;
    }

    /**
     * The person a name means, as the id the tracker filters by.
     *
     * Only a name carried whole decides, which is where this parts from the way
     * an area is named: half of "backend ui" is still the backend, and half of
     * "Andreas Kießling" is four other people called Andreas. A name reaching
     * two of them resolves to neither and answers with both, because merging
     * two people into one backlog is a wrong answer nothing about it says is
     * wrong.
     *
     * @return array{name: string, id: int, candidates: list<string>}
     */
    public function person(string $name): array
    {
        $members = $this->people();
        [$carried] = self::matching($members, $name);
        if (count($carried) === 1) {
            return ['name' => $carried[0], 'id' => $members[$carried[0]], 'candidates' => []];
        }
        if ($carried !== []) {
            return ['name' => '', 'id' => 0, 'candidates' => $carried];
        }

        $others = $this->found($name);
        [$filed] = self::matching($others, $name);
        if (count($filed) === 1) {
            return ['name' => $filed[0], 'id' => $others[$filed[0]], 'candidates' => []];
        }

        return ['name' => '', 'id' => 0, 'candidates' => $filed];
    }

    /**
     * The people named by the issues whose text carries a name, name to id.
     *
     * What resolves somebody the project holds no membership for, which is a
     * quarter of the reporters. It is the step the session that asked for this
     * filter took by hand: read an issue that person touched, and lift the id
     * out of its author. Best effort and no more — a reporter nobody has
     * written the name of stays unresolved, which is answered as such rather
     * than as an empty backlog.
     *
     * @return array<string, int>
     */
    private function found(string $name): array
    {
        $url = self::HOST . '/search.json?q=' . rawurlencode($name) . '&issues=1&limit=' . self::HITS;
        $answer = $this->api($url, 'results');

        $numbers = [];
        foreach ($answer['part'] ?? [] as $hit) {
            if (is_array($hit) && is_numeric($hit['id'] ?? null)) {
                $numbers[] = (int) $hit['id'];
            }
        }

        $people = [];
        foreach ($this->issuesOf($numbers) as $row) {
            // Both sides of a row, because a name in an issue's text is as often
            // the person it was handed to as the person who filed it.
            foreach ([$row['author'] ?? null, $row['assigned_to'] ?? null] as $party) {
                if (is_array($party) && is_string($party['name'] ?? null) && is_numeric($party['id'] ?? null)) {
                    $people[$party['name']] = (int) $party['id'];
                }
            }
        }

        return $people;
    }

    /**
     * The entries of a name-to-id list a caller's words name, in the tracker's
     * own spelling. Its areas and its people are both such a list.
     *
     * Nobody types "RTE (rtehtmlarea + ckeditor)". They type "rte", and they
     * type "backend ui" for a name that carries neither word whole, so the
     * words are matched one at a time and at a word boundary — a substring
     * match answers "rte" with every category whose name contains "reporte" or
     * "Renderer", and "kai" with everybody called Kaiser.
     *
     * Carrying every word is preferred and carrying one is the fallback,
     * because the first is what an exact name does and the second is what a
     * half-remembered one does. Which entries that produced is answered back,
     * since "backend ui" reaching three of them is a set the caller may want to
     * narrow and cannot see from the issues alone.
     *
     * @param array<string, int> $entries
     * @return list<string>
     */
    public static function named(array $entries, string $words): array
    {
        [$all, $any] = self::matching($entries, $words);

        return $all !== [] ? $all : $any;
    }

    /**
     * The entries carrying every word, and the entries carrying some of them.
     *
     * @param array<string, int> $entries
     * @return array{list<string>, list<string>}
     */
    private static function matching(array $entries, string $words): array
    {
        $terms = preg_split('/\s+/', trim($words)) ?: [];
        $terms = array_values(array_filter($terms, static fn(string $term): bool => $term !== ''));
        if ($terms === []) {
            return [[], []];
        }

        $all = [];
        $any = [];
        foreach (array_keys($entries) as $name) {
            $carried = 0;
            foreach ($terms as $term) {
                $carried += Text::containsWord($name, $term) ? 1 : 0;
            }
            if ($carried === count($terms)) {
                $all[] = $name;
            } elseif ($carried > 0) {
                $any[] = $name;
            }
        }

        return [$all, $any];
    }

    /**
     * One read of the API, as the part of the answer that was asked for.
     *
     * The three states are the same whichever question was asked, and so is the
     * one retry: a body that did not parse is the protection rather than an
     * outage, and the way past it is a plainer agent rather than a more
     * browser-like one. One, because a second failure is an answer about the
     * host rather than about the request.
     *
     * @return array{status: int, part: ?array<mixed>, total: int, cause: ?string}
     */
    private function api(string $url, string $key, int $heldFor = self::HELD_FOR): array
    {
        /** @var array{status: int, part: ?array<mixed>, total: int, cause: ?string}|null $held */
        $held = Recent::held($url, $heldFor);
        if ($held !== null) {
            return $held;
        }

        $response = $this->fetch->read($url, ['Accept: application/json']);
        if ($response['body'] === null) {
            return ['status' => $response['status'], 'part' => null, 'total' => 0, 'cause' => 'source-not-answering'];
        }

        $part = self::part($response['body'], $key);
        if ($part === null) {
            $part = self::part($this->fetch->read($url, ['Accept: application/json'], Fetch::PLAIN_AGENT)['body'], $key);
        }

        $answer = [
            'status' => $response['status'],
            'part' => $part['part'] ?? null,
            'total' => $part['total'] ?? 0,
            'cause' => $part === null ? 'source-not-parseable' : null,
        ];
        // Only what the tracker actually answered. A 404 for an issue nobody
        // has filed yet and a body the protection replaced are both states of
        // this minute, and holding either turns one bad minute into five.
        if ($part !== null) {
            Recent::hold($url, $answer);
        }

        return $answer;
    }

    /**
     * The part of an API answer a call was asking for — the issue, the hits,
     * the page of issues — or null for everything that is not one.
     *
     * The tracker's own count of what matched comes with it, because a page is
     * not the set and only the envelope says which of the two the caller is
     * holding. An answer carrying no count is one issue read whole, where the
     * question does not arise.
     *
     * @return array{part: array<mixed>, total: int}|null
     */
    private static function part(?string $body, string $key): ?array
    {
        $decoded = Fetch::decode($body);
        if (!is_array($decoded[$key] ?? null)) {
            return null;
        }

        $total = $decoded['total_count'] ?? null;

        return ['part' => $decoded[$key], 'total' => is_numeric($total) ? (int) $total : 0];
    }

    /**
     * One hit, as the identity and the triage state a caller sorting a set of
     * them needs.
     *
     * `title` arrives as `Bug #105403 (Under Review): f:image and cache busting
     * issue`, so the tracker and the status are readable here rather than in a
     * second call per hit. A title in some other shape is not a broken hit —
     * the number and the URL are fields of their own — so what cannot be read
     * off it is left empty and the whole title stands as the subject.
     *
     * @param array<string, mixed> $entry
     * @return array<string, mixed>
     */
    private static function hit(array $entry): array
    {
        $title = is_string($entry['title'] ?? null) ? trim($entry['title']) : '';
        $issue = isset($entry['id']) && is_numeric($entry['id']) ? (int) $entry['id'] : 0;
        $tracker = '';
        $status = '';
        $subject = $title;
        if (preg_match('~^(.+?) #(\d+) \((.+?)\): (.*)$~s', $title, $matched) === 1) {
            $tracker = $matched[1];
            $issue = $issue > 0 ? $issue : (int) $matched[2];
            $status = $matched[3];
            $subject = $matched[4];
        }

        $url = is_string($entry['url'] ?? null) ? trim($entry['url']) : '';

        return [
            'issue' => $issue,
            'subject' => $subject,
            'tracker' => $tracker,
            'status' => $status,
            // A search hit is a title and a URL. The five below are fields of
            // the issue, and `filled()` is what reads them for the whole page —
            // what is left empty here is what that call could not reach.
            'category' => '',
            'reportedBy' => '',
            'assignedTo' => '',
            'createdOn' => '',
            'updatedOn' => '',
            'url' => $url !== '' ? $url : self::HOST . '/issues/' . $issue,
            // Nothing asks the tracker or the review server about these for a
            // search: a search answers which issues mention a wording, and the
            // three are what a backlog row is chosen on.
            'relations' => [],
            'attachments' => [],
            'reviews' => [],
            'cites' => [],
        ];
    }

    /**
     * One issue of an enumeration, in the shape a search hit comes back in.
     *
     * Everything here is a field, where a search hit has to be read out of its
     * title — which is what makes the two dates answerable at all, and they say
     * different things: filed long ago is about the report, untouched for years
     * is about the attention it got. The relations and the files come with the
     * page and decide which row is worth reading — `D-ANS-069`.
     *
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private static function entry(array $raw): array
    {
        $issue = isset($raw['id']) && is_numeric($raw['id']) ? (int) $raw['id'] : 0;

        return [
            'issue' => $issue,
            'subject' => is_string($raw['subject'] ?? null) ? trim($raw['subject']) : '',
            'tracker' => self::name($raw['tracker'] ?? null),
            'status' => self::name($raw['status'] ?? null),
            'category' => self::name($raw['category'] ?? null),
            'reportedBy' => self::name($raw['author'] ?? null),
            'assignedTo' => self::name($raw['assigned_to'] ?? null),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . $issue,
            'relations' => self::relationsOf($raw, $issue),
            'attachments' => self::attachments($raw),
            // Filled by `reviewed()`, which is one call for the page rather
            // than a field of the row.
            'reviews' => [],
            // Filled by `open()` from the row the tracker sent, because only an
            // enumerated row carries the description this is read out of.
            'cites' => [],
        ];
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, mixed>
     */
    private static function issueOf(array $raw, string $number, string $wanted = 'all'): array
    {
        $journals = is_array($raw['journals'] ?? null) ? $raw['journals'] : [];
        $notes = [];
        $written = [];
        foreach ($journals as $journal) {
            if (!is_array($journal)) {
                continue;
            }
            $note = is_string($journal['notes'] ?? null) ? trim($journal['notes']) : '';
            if ($note === '') {
                // A journal with no note is a field change, which the fields
                // below already report in their current state.
                continue;
            }
            $author = self::name($journal['user'] ?? null);
            $entry = [
                'author' => $author,
                'on' => is_string($journal['created_on'] ?? null) ? $journal['created_on'] : '',
                'note' => $note,
            ];
            $notes[] = $entry;
            if (!in_array($author, self::BOTS, true)) {
                $written[] = $entry;
            }
        }
        // Filtered before the slice, so dropping the pings is what lets more of
        // what a person wrote fit inside the bound rather than only shortening
        // the answer.
        $shown = $wanted === 'people' ? $written : $notes;

        $own = (int) ($raw['id'] ?? $number);
        $description = is_string($raw['description'] ?? null) ? trim($raw['description']) : '';
        $relations = self::relationsOf($raw, $own);
        // The description is a text like a note and is read as one. A review URL
        // pasted into the report was dropped while the same URL in a comment was
        // a handle, and 5 of the 100 newest open bugs carry one there
        // (`D-ANS-123`).
        $texts = [
            [
                'author' => self::name($raw['author'] ?? null),
                'on' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
                'note' => $description,
            ],
            ...$notes,
        ];

        return [
            'id' => (int) ($raw['id'] ?? $number),
            'subject' => is_string($raw['subject'] ?? null) ? $raw['subject'] : '',
            'status' => self::name($raw['status'] ?? null),
            'tracker' => self::name($raw['tracker'] ?? null),
            'priority' => self::name($raw['priority'] ?? null),
            'assignedTo' => self::name($raw['assigned_to'] ?? null),
            'targetVersion' => self::name($raw['fixed_version'] ?? null),
            'typo3Version' => self::custom($raw, 'TYPO3 Version'),
            'phpVersion' => self::custom($raw, 'PHP Version'),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . (int) ($raw['id'] ?? $number),
            'description' => $description,
            'relations' => $relations,
            // Filled by `referenced()`, which resolves the numbers in the read
            // the relations already make.
            'mentioned' => self::mentions($description, $notes, $own, array_column($relations, 'issue')),
            'attachments' => self::attachments($raw),
            // Read from every note rather than from the ones that come back, so
            // a patch-set ping older than the bound is still a handle.
            'reviews' => self::reviews($texts),
            // From every note rather than from the ones that come back, the way
            // the changes are: what a report names its code in is as often the
            // comment that reproduced it as the description — `D-ANS-122`.
            'cites' => CitedCode::in(
                is_string($raw['subject'] ?? null) ? $raw['subject'] : '',
                is_string($raw['description'] ?? null) ? $raw['description'] : '',
                ...array_column($notes, 'note'),
            ),
            'noteCount' => count($notes),
            'botNoteCount' => count($notes) - count($written),
            'notes' => array_slice($shown, -self::NOTES),
        ];
    }

    /**
     * The issues a record is filed against, each named once.
     *
     * A relation names both sides, and which of the two is the other issue
     * depends on who filed it. Taking one field blindly reports an issue as
     * related to itself, which is what the first live call did.
     *
     * @param array<string, mixed> $raw
     * @return list<array<string, mixed>>
     */
    private static function relationsOf(array $raw, int $own): array
    {
        $relations = [];
        foreach (is_array($raw['relations'] ?? null) ? $raw['relations'] : [] as $relation) {
            if (!is_array($relation)) {
                continue;
            }
            $from = (int) ($relation['issue_id'] ?? 0);
            $to = (int) ($relation['issue_to_id'] ?? 0);
            $other = $from === $own ? $to : $from;
            if ($other === 0 || $other === $own) {
                continue;
            }
            $relations[] = [
                'issue' => $other,
                'relation' => is_string($relation['relation_type'] ?? null) ? $relation['relation_type'] : '',
                'url' => self::HOST . '/issues/' . $other,
            ];
        }

        return $relations;
    }

    /**
     * The issues the report and the comments cite, which no relation carries.
     *
     * A relation is somebody's triage and a citation is the writer's own claim
     * about prior art, which on an old report is regularly the load-bearing one
     * and sits in the first line of a description while the answer says
     * `relations: []` (`D-ANS-123`). So the two are separate fields, and a
     * number already filed as a relation is left to the relation, which says
     * more about it.
     *
     * Each once, in the order it was written, and a citation in both texts is a
     * description: that is where the reporter framed the report.
     *
     * @param list<array{author: string, on: string, note: string}> $notes
     * @param list<int>                                             $linked
     * @return list<array<string, mixed>>
     */
    private static function mentions(string $description, array $notes, int $own, array $linked): array
    {
        $where = [];
        foreach (self::cited($description) as $number) {
            $where[$number] = 'description';
        }
        foreach ($notes as $note) {
            foreach (self::cited($note['note']) as $number) {
                $where[$number] ??= 'note';
            }
        }

        $mentioned = [];
        foreach ($where as $number => $text) {
            if ($number === $own || in_array($number, $linked, true)) {
                continue;
            }
            $mentioned[] = [
                'issue' => $number,
                'subject' => '',
                'tracker' => '',
                'status' => '',
                'url' => self::HOST . '/issues/' . $number,
                'where' => $text,
            ];
        }

        return $mentioned;
    }

    /**
     * The issue numbers one text cites, in the two forms people write them in.
     *
     * A URL is the form the report this was written from used and the rare one:
     * 5 of 200 open bugs read on 2026-08-27 carried one, against 29 of them
     * carrying Redmine's own `#NNNN`. That form is bounded to what an issue
     * number is — a TYPO3 exception code is ten digits, and two of them stand in
     * the description of #76202 (`D-ANS-123`).
     *
     * @return list<int>
     */
    private static function cited(string $text): array
    {
        // One group for both forms, so the bound on the bare one is a lookahead
        // rather than a second alternative to read out.
        preg_match_all('~(?:forge\.typo3\.org/issues/|#(?=\d{3,6}(?!\d)))(\d+)~', $text, $found);

        $numbers = [];
        foreach ($found[1] as $citation) {
            $numbers[(int) $citation] = (int) $citation;
        }

        return array_values($numbers);
    }

    /**
     * The review changes the report and the journal name, as handles rather than
     * as prose.
     *
     * They are in the payload already and only inside a sentence, which is where
     * a triage stops reading them (`D-ANS-064`). Nothing is claimed about their
     * state, which is one `typo3_gerrit_lookup` call away. Two passes, because
     * the bot's note names the change id and the number together where a human's
     * later note is a bare URL.
     *
     * @param list<array{author: string, on: string, note: string}> $notes The
     *     description first and then the journal, in the order they were
     *     written, so the date on a handle is the last text that named it.
     * @return list<array<string, mixed>>
     */
    private static function reviews(array $notes): array
    {
        $numberOf = [];
        foreach ($notes as $note) {
            [$numbers, $changeId] = self::handles($note['note']);
            if ($changeId !== '' && count($numbers) === 1) {
                $numberOf[strtolower($changeId)] = $numbers[0];
            }
        }

        $reviews = [];
        foreach ($notes as $note) {
            [$numbers, $changeId] = self::handles($note['note']);
            if ($numbers === [] && $changeId !== '') {
                $numbers = [$numberOf[strtolower($changeId)] ?? 0];
            }
            foreach ($numbers as $number) {
                $key = $number > 0 ? (string) $number : strtolower($changeId);
                if ($key === '') {
                    continue;
                }
                $review = $reviews[$key] ?? [
                    'change' => $number,
                    'changeId' => '',
                    'patchSet' => 0,
                    'on' => '',
                    'url' => $number > 0 ? Gerrit::HOST . '/c/' . $number : Gerrit::HOST,
                ];
                // Only where the note names one change. A note naming two
                // carries a change id belonging to neither of them for certain.
                if ($changeId !== '' && count($numbers) === 1) {
                    $review['changeId'] = $changeId;
                }
                $review['patchSet'] = max($review['patchSet'], self::patchSet($note['note']));
                // The journal is in order, so the last note naming a change is
                // what says how old the reference is.
                $review['on'] = $note['on'];
                $reviews[$key] = $review;
            }
        }

        return array_values($reviews);
    }

    /**
     * The change numbers and the change id one note names.
     *
     * A review URL carries the number in two shapes — `review.typo3.org/1186`
     * from before the move to the current server, and
     * `review.typo3.org/c/Packages/TYPO3.CMS/+/38419` since. A URL with neither
     * is a query rather than a change: `review.typo3.org/#q,status:open+…`
     * names a topic and no number, and matching digits anywhere in the URL
     * would report it as change 3129.
     *
     * @return array{list<int>, string}
     */
    private static function handles(string $note): array
    {
        preg_match_all('~review\.typo3\.org/(?:c/[^\s]*?\+/)?(\d+)~', $note, $found);
        $numbers = array_values(array_unique(array_map(intval(...), $found[1])));

        preg_match('~\bI[0-9a-f]{40}\b~i', $note, $id);

        return [$numbers, $id[0] ?? ''];
    }

    /** Which patch set the note is about, and zero where it does not say. */
    private static function patchSet(string $note): int
    {
        return preg_match('~\bpatch set (\d+)~i', $note, $matched) === 1 ? (int) $matched[1] : 0;
    }

    /**
     * The files hanging off an issue, named rather than fetched.
     *
     * On a bug report about rendering, the evidence is regularly a screenshot,
     * and Redmine's inline syntax puts it into a comment as `!name.jpg!` — so
     * the text of that comment is a bare filename and reads as an empty
     * comment. On #88556 two of the seven attachments decided the triage: one
     * showed the editor's source view, the other the reporter's literal
     * database content, and a session that read only the text would have filed
     * one wrong verdict for two different defects
     * (`feedback/2026-08-05-033846`).
     *
     * What comes back is the list and not the bytes. The URLs answer without a
     * credential, an image is read by a caller that can read images, and this
     * server transcribes nothing.
     *
     * @param array<string, mixed> $raw
     * @return list<array<string, mixed>>
     */
    private static function attachments(array $raw): array
    {
        $attachments = [];
        foreach (is_array($raw['attachments'] ?? null) ? $raw['attachments'] : [] as $file) {
            if (!is_array($file) || !is_string($file['filename'] ?? null)) {
                continue;
            }
            $attachments[] = [
                'filename' => $file['filename'],
                'contentType' => is_string($file['content_type'] ?? null) ? $file['content_type'] : '',
                'size' => isset($file['filesize']) && is_numeric($file['filesize']) ? (int) $file['filesize'] : 0,
                'on' => is_string($file['created_on'] ?? null) ? $file['created_on'] : '',
                'url' => is_string($file['content_url'] ?? null) ? $file['content_url'] : '',
            ];
        }

        return $attachments;
    }

    /** @param mixed $field */
    private static function name($field): string
    {
        return is_array($field) && is_string($field['name'] ?? null) ? $field['name'] : '';
    }

    /**
     * A custom field is a list rather than a map, so it is found by the name it
     * carries. The TYPO3 version an issue was reported against lives in one.
     *
     * @param array<string, mixed> $raw
     */
    private static function custom(array $raw, string $name): string
    {
        foreach (is_array($raw['custom_fields'] ?? null) ? $raw['custom_fields'] : [] as $field) {
            if (is_array($field) && ($field['name'] ?? null) === $name) {
                $value = $field['value'] ?? '';

                return is_string($value) ? $value : implode(', ', array_filter((array) $value, is_string(...)));
            }
        }

        return '';
    }
}
