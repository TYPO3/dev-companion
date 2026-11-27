<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Http\Fetch;
use TYPO3\DevCompanion\Search\Text;

/**
 * The issue tracker a core patch starts from, read over its Redmine API.
 *
 * One of the providers `IssueTracker` asks, and the one every other provider
 * indexes — `D-ANS-197`. An answer is only an answer when it parses as the
 * API. This host answers a browser-shaped request with 200 and a challenge
 * page. A read of that as "no issue" would be the same mistake in the other
 * direction. The request goes to the `.json` endpoint rather than the page,
 * because the fields that decide anything would come off a scrape anywhere
 * else (`D-ANS-038`).
 *
 * @phpstan-import-type Row from IssueProvider
 */
final class Forge implements IssueProvider
{
    public const HOST = 'https://forge.typo3.org';

    /** The core's own project on the tracker, which is the one this reads. */
    public const PROJECT = 'typo3cms-core';

    /** Where the project answers its own areas, which is the only read `*` does. */
    private const AREAS_URL = self::HOST . '/projects/' . self::PROJECT . '.json?include=issue_categories';

    private const VERSIONS_URL = self::HOST . '/projects/' . self::PROJECT . '/versions.json';

    /**
     * Seconds an answered read stays in the cache.
     *
     * Longer than the review server's, because nothing the caller does through
     * this server changes what the tracker says. An issue's status, its target
     * version and its comments move when somebody else works on them, at the
     * pace people work. What this is against is a session that walks a list of
     * issues and asks the tracker the same thing a dozen times.
     */
    public const CACHE_DURATION = 300;

    /**
     * Seconds the project's own lists, its categories and its members, stay in
     * the cache.
     *
     * Two orders of magnitude longer than an issue's, because they answer how
     * the core organises itself rather than what happened to one issue. A
     * category arrives with a subsystem and a membership when somebody joins,
     * which are things that happen between releases. A session that filters by
     * area or by person asks for the same names on every call it makes.
     */
    public const LIST_CACHE_DURATION = 86400;

    /**
     * The authors whose notes are a review server's ping to the tracker.
     *
     * A list and not a rule. These are the two the core project has seen, and a
     * bot nobody has named here passes the filter. That is why the count comes
     * back whether or not the caller asked for it. A journal full of patch-set
     * pings that answers zero filtered is this list gone stale, visible without
     * a read of the journal to find out.
     */
    private const BOTS = ['Gerrit Code Review', 'Mr. Hudson'];

    /**
     * The most hits a search answers with. The order is the tracker's own and
     * nothing here ranks. So a caller who reaches the end of one asks again in
     * other words rather than deeper. That is the answer to a set that looks
     * too narrow (`D-ANS-038`).
     */
    private const HITS = 25;

    /** The most memberships one page of them holds, which is Redmine's own cap. */
    private const MEMBERS = 100;

    /** The most issues one page of a counted read holds, the same cap. */
    private const COUNTED = 100;

    /**
     * The most pages of them a shape count runs over. Ten reads take about
     * seven seconds against forge.typo3.org, measured on 2026-08-19. What a set
     * larger than a thousand owes the caller is the bound rather than the wait.
     */
    private const COUNTED_PAGES = 10;

    /**
     * The most pages of them read, whatever the project says it has. The count
     * comes from the tracker and the loop ends on it. This is what keeps a
     * wrong count from a turn of one call into a thousand. The core project had
     * 185 members on 2026-08-19.
     */
    private const MEMBER_PAGES = 5;

    /**
     * The tracker ids `/trackers.json` answered with on 2026-08-05.
     *
     * The API filters by id and nobody says "tracker 1", so the caller's word
     * turns into the id here. Kept as data rather than fetched. A twelfth
     * tracker would cost every enumeration a second round trip to find out
     * about, and its absence costs one filter nobody can ask for.
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
     * `IssueTracker` builds its own providers, so a test that drives the tool
     * has nowhere else to hand a transport in. `R-COD-003`.
     *
     * @var (\Closure(string): ?string)|null
     */
    private static ?\Closure $transport = null;

    private readonly Fetch $fetch;

    /**
     * The review server, asked which changes name an issue. The tracker cannot
     * answer it, because the journal that carries a change reference is not in
     * the index answer at all (`D-ANS-069`).
     */
    private readonly Gerrit $review;

    /** The earliest of this instance's reads of an issue, null before the first. */
    private ?int $readAt = null;

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

    public function host(): string
    {
        return self::HOST;
    }

    public function readAt(): ?int
    {
        return $this->readAt;
    }

    public function issue(int $number): array
    {
        $url = self::HOST . '/issues/' . $number . '.json?include=journals,relations,attachments';

        $answer = $this->api($url, 'issue');
        // A tracker that says 404 has answered. There is no such issue, which
        // is a different thing to tell a caller than that it was out of reach.
        if ($answer['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'row' => null, 'cause' => null];
        }
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'row' => null, 'cause' => $answer['cause']];
        }

        $row = self::row($answer['part'] + ['id' => $number]);
        $row['changes'] = $this->changes([$row['issue']])[$row['issue']] ?? [];

        return ['status' => 'answered', 'url' => $url, 'row' => $row, 'cause' => null];
    }

    /**
     * The comments of one issue, at the address a read of the issue takes too,
     * so the next call for it comes from the cache.
     */
    public function comments(int $number): ?array
    {
        $answer = $this->api(self::HOST . '/issues/' . $number . '.json?include=journals,relations,attachments', 'issue');

        return $answer['part'] === null ? null : self::notesOf(is_array($answer['part']['journals'] ?? null) ? $answer['part']['journals'] : []);
    }

    /**
     * The fields of a set of issues, read in one request.
     *
     * What makes filling a search page and an issue's relations cheaper than
     * explaining why they are bare. The review server fills the issues a commit
     * message names from here too (`D-ANS-098`).
     */
    public function fields(array $numbers): array
    {
        $fields = [];
        foreach ($this->issuesOf($numbers) as $raw) {
            $row = self::row($raw);
            $fields[$row['issue']] = $row;
        }

        return $fields;
    }

    public function rows(array $numbers): array
    {
        $raw = $this->included(array_map(static fn(int $number): array => ['id' => $number], $numbers));

        return $this->reviewed(array_values(array_map(
            self::row(...),
            array_filter($raw, static fn(array $row): bool => isset($row['subject'])),
        )));
    }

    /**
     * The issues whose text matches these words.
     *
     * `issues=1` is what keeps wiki pages, forum posts and changesets out of an
     * answer whose entries are issue numbers.
     */
    public function search(string $words, int $limit): array
    {
        $url = self::HOST . '/search.json?q=' . rawurlencode($words)
            . '&issues=1&limit=' . max(1, min(self::HITS, $limit));

        $answer = $this->api($url, 'results');
        if ($answer['part'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'rows' => [], 'complete' => false, 'scanned' => 0, 'cause' => $answer['cause']];
        }

        $rows = [];
        foreach ($answer['part'] as $hit) {
            if (is_array($hit)) {
                $rows[] = self::hit($hit);
            }
        }

        return [
            'status' => $rows === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => $answer['total'],
            'rows' => $this->filled($rows),
            'complete' => true,
            'scanned' => count($rows),
            'cause' => null,
        ];
    }

    /**
     * One read per word rather than one re-read of the query with the AND off.
     * That re-read answers the union in issue number order at the commonest
     * word's size.
     */
    public function matchCount(string $word): ?int
    {
        $answer = $this->api(self::HOST . '/search.json?q=' . rawurlencode($word) . '&issues=1&limit=1', 'results');

        return $answer['part'] === null ? null : $answer['total'];
    }

    /**
     * The fields a search hit is not made of, read for the whole page at once.
     *
     * `/search.json` answers with a title and a URL. A record with the area,
     * the people and the dates empty is a false statement rather than an absent
     * one: it reads as an issue nobody has categorised, nobody holds and
     * nothing has moved on (`feedback/2026-08-05-033902`). Where the read is out
     * of reach the hits stand as they came back.
     *
     * @param list<Row> $rows
     * @return list<Row>
     */
    private function filled(array $rows): array
    {
        $fields = $this->fields(array_column($rows, 'issue'));
        foreach ($rows as $at => $hit) {
            $read = $fields[$hit['issue']] ?? null;
            if ($read === null) {
                continue;
            }
            foreach (['category', 'reportedBy', 'assignedTo', 'createdOn', 'updatedOn'] as $field) {
                $rows[$at][$field] = $read[$field];
            }
            // The tracker and the status come off the title, which is the
            // tracker's own words and usually parses. Where it did not, they
            // are fields here.
            $rows[$at]['tracker'] = $hit['tracker'] !== '' ? $hit['tracker'] : $read['tracker'];
            $rows[$at]['status'] = $hit['status'] !== '' ? $hit['status'] : $read['status'];
        }

        return $rows;
    }

    /**
     * The backlog as the tracker filters it.
     *
     * The tracker's own `status_id=open` is the way in to the issue no number
     * and no words find. `involving` is a union the tracker cannot answer, so
     * it is two reads and a merge, `D-ANS-090`. The whole set is read where
     * the answer is over all of it: the counts, the words matched inside it,
     * and a later page of a union, whose two sides the tracker cannot page as
     * one (`D-ANS-180`).
     */
    public function backlog(array $filter, int $offset, int $limit, bool $whole): array
    {
        $filters = [
            // The tracker's own three: `open` is every status it has not marked
            // closed, and `*` is what puts a person's whole history in reach.
            'status_id' => match ($filter['status']) {
                'closed' => 'closed',
                'all' => '*',
                default => 'open',
            },
            'sort' => match ($filter['order']) {
                'stale' => 'updated_on:asc',
                'newest' => 'created_on:desc',
                default => 'created_on:asc',
            },
        ];
        if (isset(self::TRACKERS[$filter['tracker']])) {
            $filters['tracker_id'] = (string) self::TRACKERS[$filter['tracker']];
        }
        if ($filter['areas'] !== []) {
            $categories = $this->categories();
            // The tracker's own alternation, which is what makes a word naming
            // three categories one call rather than three.
            $filters['category_id'] = implode('|', array_map(
                static fn(string $name): int => $categories[$name] ?? 0,
                $filter['areas'],
            ));
        }
        $sides = [];
        if ($filter['involving'] > 0) {
            $sides = [['author_id' => (string) $filter['involving']], ['assigned_to_id' => (string) $filter['involving']]];
        }
        if ($filter['reportedBy'] > 0) {
            $filters['author_id'] = (string) $filter['reportedBy'];
        }
        if ($filter['assignedTo'] > 0) {
            $filters['assigned_to_id'] = (string) $filter['assignedTo'];
        }
        // A date the tracker cannot read gets the unfiltered set, which is the
        // wrong answer in a right one's shape. Only a date reaches the query.
        $filed = self::within($filter['createdSince'], $filter['createdBefore']);
        if ($filed !== '') {
            $filters['created_on'] = $filed;
        }
        if (self::isDate($filter['updatedBefore'])) {
            $filters['updated_on'] = '<=' . $filter['updatedBefore'];
        }

        $words = $filter['words'];
        $scan = $whole || $words !== '' || ($offset > 0 && count($sides) > 1);
        // A page asks for the relations and the files it hands on. A scan
        // wants none of them, because it reads a thousand rows for four
        // fields; the rows it hands on are read again with them.
        $page = $scan
            ? ['limit' => (string) self::COUNTED]
            : ['limit' => (string) $limit, 'include' => 'relations,attachments']
                + ($offset > 0 ? ['offset' => (string) $offset] : []);
        $reads = array_map(
            static fn(array $side): string => self::HOST . '/projects/' . self::PROJECT . '/issues.json?'
                . http_build_query($page + $side + $filters),
            $sides === [] ? [[]] : $sides,
        );
        $url = implode(' ', $reads);

        $answer = $scan ? $this->shape($reads) : $this->page($reads, $limit, $filters['sort']);
        // Two reads answer one question, and their counts overlap by the issues
        // this person both filed and holds. That overlap is a third read of one
        // row and the only thing that makes the union countable at all.
        if ($answer['rows'] !== null && count($reads) > 1) {
            $answer['total'] -= $this->overlap($sides, $filters);
        }
        if ($answer['rows'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'rows' => [], 'complete' => false, 'scanned' => 0, 'cause' => $answer['cause']];
        }

        $rows = $scan && count($reads) > 1 ? self::inOrder($answer['rows'], $filters['sort']) : $answer['rows'];
        $total = $answer['total'];
        $scanned = count($rows);
        if ($words !== '') {
            $rows = self::about($rows, $words);
            $total = count($rows);
        }
        if ($scan && !$whole) {
            $rows = $this->included(array_slice($rows, $offset, $limit));
        }

        $normalised = array_map(self::row(...), $rows);
        if (!$whole) {
            $normalised = $this->reviewed($normalised);
        }

        return [
            'status' => $normalised === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => $total,
            'rows' => $normalised,
            'complete' => $answer['complete'],
            'scanned' => $scanned,
            'cause' => null,
        ];
    }

    public function areas(): array
    {
        return array_map(strval(...), array_keys($this->categories()));
    }

    /**
     * The members whose name carries every word, and the people the issues
     * name where no member does.
     *
     * The members are who works the project rather than everybody who has ever
     * filed something. 24 of the 100 newest issues came from outside it,
     * measured on 2026-08-19, which is what `found()` is the fallback for.
     */
    public function people(string $name): array
    {
        $members = IssueTracker::carrying($this->members(), $name);

        return $members !== [] ? $members : IssueTracker::carrying($this->found($name), $name);
    }

    /**
     * The changes the review server holds for these issues, one query per
     * twelve. The state comes with the handle, because the batched query
     * already answers it per change (`D-ANS-069`).
     *
     * @param list<int> $numbers
     * @return array<int, list<array{change: int, status: string, url: string}>>
     */
    private function changes(array $numbers): array
    {
        $changes = [];
        foreach ($this->review->changesForIssues($numbers) as $issue => $found) {
            foreach ($found as $change) {
                $changes[$issue][] = [
                    'change' => (int) $change['number'],
                    'status' => is_string($change['status'] ?? null) ? $change['status'] : '',
                    'url' => is_string($change['url'] ?? null) ? $change['url'] : '',
                ];
            }
        }

        return $changes;
    }

    /**
     * The rows with the changes on review for each. Where the review server
     * is out of reach the rows stand with none.
     *
     * @param list<Row> $rows
     * @return list<Row>
     */
    private function reviewed(array $rows): array
    {
        $changes = $this->changes(array_column($rows, 'issue'));
        foreach ($rows as $at => $row) {
            $rows[$at]['changes'] = $changes[$row['issue']] ?? [];
        }

        return $rows;
    }

    /**
     * The rows read again with their relations and their files, in one
     * request, in the order they came.
     *
     * Where the read does not answer, the rows stand as they came, which is a
     * row with no relation rather than no row.
     *
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private function included(array $rows): array
    {
        $numbers = array_values(array_filter(array_map(
            static fn(array $row): int => is_numeric($row['id'] ?? null) ? (int) $row['id'] : 0,
            $rows,
        )));
        if ($numbers === []) {
            return $rows;
        }

        $answer = $this->api(self::HOST . '/issues.json?' . http_build_query([
            'issue_id' => implode(',', $numbers),
            'status_id' => '*',
            'include' => 'relations,attachments',
            'limit' => (string) count($numbers),
        ]), 'issues');
        $read = self::merged([], $answer['part'] ?? []);

        return array_map(
            static fn(array $row): array => $read[is_numeric($row['id'] ?? null) ? (int) $row['id'] : 0] ?? $row,
            $rows,
        );
    }

    /**
     * The rows whose subject or description carries one of the words at the
     * start of a word, so "translat" reaches translation and translated.
     *
     * Any of them rather than all, because the words of a topic are its
     * synonyms. A topic triage left the tool for a regex of six stems
     * (`D-ANS-180`).
     *
     * @param list<array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private static function about(array $rows, string $words): array
    {
        $terms = array_values(array_filter(preg_split('/[\s,]+/', mb_strtolower(trim($words))) ?: []));

        return array_values(array_filter($rows, static function (array $row) use ($terms): bool {
            $text = mb_strtolower(
                (is_string($row['subject'] ?? null) ? $row['subject'] : '') . ' '
                    . (is_string($row['description'] ?? null) ? $row['description'] : ''),
            );
            foreach ($terms as $term) {
                if (Text::startsWord($text, $term)) {
                    return true;
                }
            }

            return false;
        }));
    }

    /**
     * One page of each read, as one page of the union.
     *
     * Where two reads answer one question, take the first `limit` of each and
     * the first `limit` of the merge. That is the first `limit` of the union.
     * Both come back in the same order, so nothing dropped from either page can
     * sort ahead of what stayed.
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
     * it by. So a page of 50 out of 621 leaves the rest reachable by nothing
     * (`feedback/2026-08-19-134651`). What answers that question is how the set
     * distributes, and what that costs is a read of it. A hundred rows to a
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
     * The rows in the order of the request to the tracker, which a merge of two
     * answers no longer carries.
     *
     * @param array<int, array<string, mixed>> $rows
     * @return list<array<string, mixed>>
     */
    private static function inOrder(array $rows, string $sort): array
    {
        $field = str_starts_with($sort, 'updated_on') ? 'updated_on' : 'created_on';
        // Which way round, and not only which field. The recent end is the same
        // sort read backwards. A merge that ignores that keeps the oldest rows
        // of a page the caller asked the newest of.
        $direction = str_ends_with($sort, ':desc') ? -1 : 1;
        $sorted = array_values($rows);
        usort($sorted, static fn(array $one, array $other): int => $direction * strcmp(
            is_string($one[$field] ?? null) ? $one[$field] : '',
            is_string($other[$field] ?? null) ? $other[$field] : '',
        ));

        return $sorted;
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
     * Both ends as two filters would be one filter and the other silently gone.
     * The tracker takes one value per field, and `><a|b` is its own way to say
     * a window, read from it in `D-ANS-116`.
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
     * is one the core can add to with no report from anything. The addition is
     * exactly the subsystem somebody would filter for. A category nobody can
     * name is a filter that answers nothing and looks like an empty backlog.
     * What keeps it from being a round trip per call is the cache, not a copy.
     *
     * @return array<string, int>
     */
    public function categories(): array
    {
        $answer = $this->api(self::AREAS_URL, 'project', self::LIST_CACHE_DURATION);

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
     * The versions the core project schedules a fix into, name to id, the open
     * ones alone.
     *
     * The form's Target version takes an id, and a person names it by what the
     * form shows. Read rather than written down for the reason `categories()`
     * gives: the project opens versions as majors arrive.
     *
     * @return array<string, int>
     */
    public function versions(): array
    {
        $answer = $this->api(self::VERSIONS_URL, 'versions', self::LIST_CACHE_DURATION);

        $versions = [];
        $listed = $answer['part'];
        foreach (is_array($listed) ? $listed : [] as $version) {
            if (is_array($version) && ($version['status'] ?? '') === 'open'
                && is_string($version['name'] ?? null) && is_numeric($version['id'] ?? null)) {
                $versions[$version['name']] = (int) $version['id'];
            }
        }

        return $versions;
    }

    /**
     * The members of the core project, name to id.
     *
     * The tracker filters by a numeric user id, and it serves no public user
     * list. `/users.json` answers 401 without an administrator's credential. So
     * a caller with a name and nothing else cannot ask a question about a
     * person at all. The memberships of the project are the one place it
     * answers names and ids together for nobody in particular.
     *
     * It is who works the project rather than everybody who has ever filed
     * something. 24 of the 100 newest issues came from outside it, measured on
     * 2026-08-19, which is what `found()` is the fallback for.
     *
     * @return array<string, int>
     */
    private function members(): array
    {
        $people = [];
        $offset = 0;
        do {
            $url = self::HOST . '/projects/' . self::PROJECT . '/memberships.json?'
                . http_build_query(['limit' => self::MEMBERS, 'offset' => $offset]);
            $answer = $this->api($url, 'memberships', self::LIST_CACHE_DURATION);
            foreach ($answer['part'] ?? [] as $membership) {
                // A membership is a person or a group, and only a person files
                // and holds issues.
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
     * The people named by the issues whose text carries a name, name to id.
     *
     * What resolves somebody the project holds no membership for, which is a
     * quarter of the reporters. It is the step the session that asked for this
     * filter took by hand. Read an issue that person touched, and lift the id
     * out of its author. Best effort and no more. A reporter nobody has written
     * the name of stays unresolved, and the answer says so rather than shows an
     * empty backlog.
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
            // Both sides of a row. A name in an issue's text is as often the
            // person who got it as the person who filed it.
            foreach ([$row['author'] ?? null, $row['assigned_to'] ?? null] as $party) {
                if (is_array($party) && is_string($party['name'] ?? null) && is_numeric($party['id'] ?? null)) {
                    $people[$party['name']] = (int) $party['id'];
                }
            }
        }

        return $people;
    }

    /**
     * The rows a set of issue numbers are, as the tracker sends them.
     *
     * `/issues.json` filtered by an id list answers subject, tracker, status,
     * area, author, assignee and both dates for the whole set in one request.
     * `status_id=*` is what keeps a closed one in it. Every caller here asks
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
     * One read of the API, as the part of the answer the call asked for.
     *
     * The three states are the same whichever question the call asked, and so
     * is the one retry. A body that did not parse is the protection rather than
     * an outage. The way past it is a plainer agent rather than a more
     * browser-like one. One, because a second failure is an answer about the
     * host rather than about the request.
     *
     * @return array{status: int, part: ?array<mixed>, total: int, cause: ?string}
     */
    private function api(string $url, string $key, int $cacheDuration = self::CACHE_DURATION): array
    {
        /** @var array{status: int, part: ?array<mixed>, total: int, cause: ?string}|null $cached */
        $cached = Cache::get($url, $cacheDuration);
        // The areas and the people are cached for a day and describe no issue,
        // so they do not age the answer.
        if ($cacheDuration === self::CACHE_DURATION) {
            $this->readAt = min($this->readAt ?? PHP_INT_MAX, Cache::storedAt($url));
        }
        if ($cached !== null) {
            return $cached;
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
        // this minute. Cached, either turns one bad minute into five.
        if ($part !== null) {
            Cache::put($url, $answer);
        }

        return $answer;
    }

    /**
     * The part of an API answer a call asked for, the issue, the hits, the page
     * of issues. Null for everything that is not one.
     *
     * The tracker's own count of what matched comes with it. A page is not the
     * set, and only the envelope says which of the two the caller has. An
     * answer carrying no count is one issue read whole, where the question does
     * not arise.
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
     * One issue as the tracker sent it, in the shape every provider answers
     * in. A field the read did not include stays null.
     *
     * @param array<string, mixed> $raw
     * @return Row
     */
    private static function row(array $raw): array
    {
        $issue = is_numeric($raw['id'] ?? null) ? (int) $raw['id'] : 0;

        return [
            'issue' => $issue,
            'subject' => is_string($raw['subject'] ?? null) ? trim($raw['subject']) : '',
            'tracker' => self::name($raw['tracker'] ?? null),
            'status' => self::name($raw['status'] ?? null),
            'priority' => self::name($raw['priority'] ?? null),
            'category' => self::name($raw['category'] ?? null),
            'targetVersion' => self::name($raw['fixed_version'] ?? null),
            'typo3Version' => self::custom($raw, 'TYPO3 Version'),
            'phpVersion' => self::custom($raw, 'PHP Version'),
            'reportedBy' => self::name($raw['author'] ?? null),
            'assignedTo' => self::name($raw['assigned_to'] ?? null),
            'createdOn' => is_string($raw['created_on'] ?? null) ? $raw['created_on'] : '',
            'updatedOn' => is_string($raw['updated_on'] ?? null) ? $raw['updated_on'] : '',
            'url' => self::HOST . '/issues/' . $issue,
            'description' => is_string($raw['description'] ?? null) ? $raw['description'] : null,
            'relations' => is_array($raw['relations'] ?? null) ? self::relationsOf($raw, $issue) : null,
            'attachments' => is_array($raw['attachments'] ?? null) ? self::attachments($raw) : null,
            'comments' => is_array($raw['journals'] ?? null) ? self::notesOf($raw['journals']) : null,
            'changes' => null,
        ];
    }

    /**
     * The journal entries that carry a note. One with no note is a field
     * change, which the fields already report in their current state.
     *
     * @param array<mixed> $journals
     * @return list<array{author: string, on: string, note: string, bot: bool}>
     */
    private static function notesOf(array $journals): array
    {
        $comments = [];
        foreach ($journals as $journal) {
            $note = is_array($journal) && is_string($journal['notes'] ?? null) ? trim($journal['notes']) : '';
            if ($note === '') {
                continue;
            }
            $author = self::name($journal['user'] ?? null);
            $comments[] = [
                'author' => $author,
                'on' => is_string($journal['created_on'] ?? null) ? $journal['created_on'] : '',
                'note' => $note,
                'bot' => in_array($author, self::BOTS, true),
            ];
        }

        return $comments;
    }

    /**
     * One search hit, with the tracker and the status read off its title.
     *
     * `title` arrives as `Bug #105403 (Under Review): f:image and cache busting
     * issue`. A title in some other shape is not a broken hit, because the
     * number and the URL are fields of their own. So what does not come off it
     * stays empty and the whole title stands as the subject.
     *
     * @param array<string, mixed> $entry
     * @return Row
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

        $row = self::row(['id' => $issue, 'subject' => $subject]);
        $row['tracker'] = $tracker;
        $row['status'] = $status;
        if ($url !== '') {
            $row['url'] = $url;
        }

        return $row;
    }

    /**
     * The issues a record stands against, each named once.
     *
     * A relation names both sides, and which of the two is the other issue
     * depends on who filed it. Taking one field blindly reports an issue as
     * related to itself, which is what the first live call did.
     *
     * @param array<string, mixed> $raw
     * @return list<array{issue: int, relation: string}>
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
            ];
        }

        return $relations;
    }

    /**
     * The files hanging off an issue, named rather than fetched.
     *
     * On a bug report about a render, the evidence is regularly a screenshot,
     * and Redmine's inline syntax puts it into a comment as `!name.jpg!`. So
     * the text of that comment is a bare filename and reads as an empty
     * comment. On #88556 two of the seven attachments decided the triage. One
     * showed the editor's source view, the other the reporter's literal
     * database content. A session that read only the text would have filed one
     * wrong verdict for two different defects (`feedback/2026-08-05-033846`).
     *
     * What comes back is the list and not the bytes. The URLs answer without a
     * credential, a caller that can read images reads an image, and this server
     * transcribes nothing.
     *
     * @param array<string, mixed> $raw
     * @return list<array{filename: string, contentType: string, size: int, on: string, url: string}>
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
     * A custom field is a list rather than a map, so the name it carries finds
     * it. The TYPO3 version an issue reports against lives in one.
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
