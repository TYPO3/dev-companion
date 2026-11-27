<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Http\Fetch;

/**
 * Forger, the index TYPO3 GmbH keeps of forge.typo3.org and review.typo3.org,
 * read over its JSON API.
 *
 * The provider `IssueTracker` asks first — `D-ANS-197`. It answered in a tenth
 * of a second on 2026-11-27, minutes behind the review server, while the
 * tracker itself answered nothing in 20 seconds. An issue carries its comments,
 * its relations and its changes in one read, so no answer here asks the review
 * server. Read only, no login. forger.typo3.org redirects to the host below.
 *
 * It answers for the changes too, over `/api/v1/changes` — `D-ANS-198`. A
 * change it lists carries no commit and no address of its owner, so a change
 * read by name stays with the review server.
 *
 * @phpstan-import-type Row from IssueProvider
 * @phpstan-import-type Page from IssueProvider
 * @phpstan-import-type Answer from ChangeProvider
 * @phpstan-import-type Waiting from ChangeProvider
 */
final class Forger implements IssueProvider, ChangeProvider
{
    public const HOST = 'https://forger.typo3.com';

    private const API = self::HOST . '/api/v1';

    /** Seconds an answered read stays in the cache, the tracker's own measure. */
    public const CACHE_DURATION = Forge::CACHE_DURATION;

    /** The most items one page holds, which is the API's own cap. */
    private const PAGE = 100;

    /** The most pages a whole-set read runs over, the bound the tracker keeps too. */
    private const PAGES = 10;

    /** The most hits a search answers with, the tracker's own. */
    private const HITS = 25;

    /**
     * The review states Forger names, in the review server's own words, which
     * is what every answer here speaks.
     */
    private const STATES = ['ready' => 'NEW', 'wip' => 'NEW', 'merged' => 'MERGED', 'abandoned' => 'ABANDONED'];

    /**
     * The labels of a change as Forger keys them, in the review server's
     * spelling, which is what every answer here speaks.
     */
    private const LABELS = ['codeReview' => 'Code-Review', 'verified' => 'Verified'];

    /** The most changes one search or one issue answers with, the review server's own. */
    private const MOST = 25;

    /** @var (\Closure(string): ?string)|null */
    private static ?\Closure $transport = null;

    private readonly Fetch $fetch;

    private ?int $readAt = null;

    /** @param (\Closure(string): ?string)|null $transport */
    public function __construct(?\Closure $transport = null)
    {
        $this->fetch = new Fetch($transport ?? self::$transport);
    }

    /**
     * What a test hands in, so nothing it drives reaches Forger. Null puts the
     * host back.
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
        $url = self::API . '/issues/' . $number;
        $answer = $this->api($url, 'id');
        if ($answer['status'] === 404) {
            return ['status' => 'empty', 'url' => $url, 'row' => null, 'cause' => null];
        }
        if ($answer['body'] === null) {
            return ['status' => 'unavailable', 'url' => $url, 'row' => null, 'cause' => $answer['cause']];
        }

        return ['status' => 'answered', 'url' => $url, 'row' => self::row($answer['body']), 'cause' => null];
    }

    public function comments(int $number): ?array
    {
        return $this->issue($number)['row']['comments'] ?? null;
    }

    /**
     * The fields of a set of issues, one read each.
     *
     * Several numbers in one search find the issues that mention them rather
     * than the issues: "99203 105953" answered neither on 2026-11-27. A read of
     * one issue answers in a tenth of a second and stays in the cache.
     */
    public function fields(array $numbers): array
    {
        $wanted = array_values(array_unique(array_filter($numbers, static fn(mixed $number): bool => is_int($number) && $number > 0)));

        $fields = [];
        foreach ($this->rows($wanted) as $row) {
            $fields[$row['issue']] = $row;
        }

        return $fields;
    }

    /** One read per issue, because only a read of one issue carries its relations. */
    public function rows(array $numbers): array
    {
        $rows = [];
        foreach ($numbers as $number) {
            $row = $this->issue($number)['row'];
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * The issues of any status the words find, ranked by Forger. A number in
     * the words also finds its issue.
     */
    public function search(string $words, int $limit): array
    {
        $url = self::API . '/issues?' . http_build_query([
            'q' => $words,
            'status' => 'all',
            'limit' => max(1, min(self::HITS, $limit)),
        ]);
        $answer = $this->api($url, 'items');
        if ($answer['body'] === null) {
            return self::unavailable($url, $answer['cause']);
        }

        $rows = array_map(self::row(...), self::items($answer['body']));

        return [
            'status' => $rows === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => self::total($answer['body']),
            'rows' => $rows,
            'complete' => true,
            'scanned' => count($rows),
            'cause' => null,
        ];
    }

    public function matchCount(string $word): ?int
    {
        $answer = $this->api(self::API . '/issues?' . http_build_query(['q' => $word, 'status' => 'all', 'limit' => 1]), 'items');

        return $answer['body'] === null ? null : self::total($answer['body']);
    }

    /**
     * The backlog as Forger filters it.
     *
     * Forger takes one area per read, and an issue sits in one area. So a word
     * naming several areas is one read each and their totals add up. Forger
     * matches the words itself, with stems.
     */
    public function backlog(array $filter, int $offset, int $limit, bool $whole): array
    {
        $query = array_filter([
            'status' => $filter['status'],
            'sort' => match ($filter['order']) {
                'stale' => 'least-updated',
                'newest' => 'newest',
                default => 'oldest',
            },
            'tracker' => $filter['tracker'],
            'q' => $filter['words'],
            'author' => $filter['reportedBy'],
            'assignee' => $filter['assignedTo'],
            'involving' => $filter['involving'],
            'createdSince' => $filter['createdSince'],
            'createdBefore' => $filter['createdBefore'],
            'updatedBefore' => $filter['updatedBefore'],
        ], static fn(string|int $value): bool => $value !== '' && $value !== 0);
        $reads = array_map(
            static fn(string $area): string => self::API . '/issues?' . http_build_query($query + ($area !== '' ? ['category' => $area] : [])),
            $filter['areas'] !== [] ? $filter['areas'] : [''],
        );
        $url = implode(' ', $reads);

        $read = $whole || count($reads) > 1 ? $this->whole($reads) : $this->page($reads[0], $offset, $limit);
        if ($read === null) {
            return self::unavailable($url, 'source-not-answering');
        }

        $rows = $read['rows'];
        if (count($reads) > 1) {
            $field = $filter['order'] === 'stale' ? 'updatedOn' : 'createdOn';
            $direction = $filter['order'] === 'newest' ? -1 : 1;
            usort($rows, static fn(array $one, array $other): int => $direction * strcmp($one[$field], $other[$field]));
        }
        if (!$whole) {
            $rows = $this->rows(array_column(count($reads) > 1 ? array_slice($rows, $offset, $limit) : $rows, 'issue'));
        }

        return [
            'status' => $rows === [] ? 'empty' : 'answered',
            'url' => $url,
            'total' => $read['total'],
            'rows' => $rows,
            'complete' => $read['complete'],
            'scanned' => $read['total'],
            'cause' => null,
        ];
    }

    /**
     * The areas the index has an issue in, read off the count of every issue
     * by area. Forger serves no list of them, and an area nobody filed under
     * filters to nothing anyway.
     */
    public function areas(): array
    {
        $answer = $this->api(self::API . '/issues?' . http_build_query(['status' => 'all', 'breakdown' => 1, 'limit' => 1]), 'items', Forge::LIST_CACHE_DURATION);
        $buckets = $answer['body']['breakdown']['category'] ?? [];

        $areas = [];
        foreach (is_array($buckets) ? $buckets : [] as $bucket) {
            $name = is_array($bucket) && is_string($bucket['name'] ?? null) ? $bucket['name'] : '';
            // `none` is the bucket of the issues under no area, not an area.
            if ($name !== '' && $name !== 'none') {
                $areas[] = $name;
            }
        }
        sort($areas);

        return $areas;
    }

    /**
     * The people on either side of the issues Forger finds for a name, whose
     * name carries every word of it.
     */
    public function people(string $name): array
    {
        $answer = $this->api(self::API . '/issues?' . http_build_query(['involving' => $name, 'status' => 'all', 'limit' => self::PAGE]), 'items', Forge::LIST_CACHE_DURATION);

        $people = [];
        foreach (self::items($answer['body']) as $item) {
            foreach ([$item['author'] ?? null, $item['assignedTo'] ?? null] as $party) {
                if (is_array($party) && is_string($party['name'] ?? null) && is_int($party['id'] ?? null)) {
                    $people[$party['name']] = $party['id'];
                }
            }
        }

        return IssueTracker::carrying($people, $name);
    }

    /**
     * The changes the issue names, as Forger reads them off the commit
     * messages.
     *
     * The read of the issue lists them, so no number in a message is taken for
     * a change of the same number. One read per change fills its votes and its
     * `Releases:` branches. An issue without a change asks the review server
     * too, because Forger can be minutes behind a push.
     */
    public function changesForIssue(int $issue, int $limit = 10): array
    {
        $url = self::API . '/issues/' . $issue;
        $answer = $this->api($url, 'id', Gerrit::CACHE_DURATION);
        if ($answer['status'] === 404) {
            return self::changeAnswer('empty', $url, [], false, null);
        }
        if ($answer['body'] === null) {
            return self::changeAnswer('unavailable', $url, [], false, $answer['cause']);
        }

        $numbers = [];
        foreach (is_array($answer['body']['changes'] ?? null) ? $answer['body']['changes'] : [] as $change) {
            if (is_array($change) && is_int($change['number'] ?? null)) {
                $numbers[] = $change['number'];
            }
        }
        $take = max(1, min(self::MOST, $limit));

        $changes = [];
        foreach (array_slice($numbers, 0, $take) as $number) {
            $read = $this->api(self::API . '/changes/' . $number, 'number', Gerrit::CACHE_DURATION);
            if ($read['body'] !== null) {
                $changes[] = self::change($read['body']);
            }
        }

        return self::changeAnswer($changes === [] ? 'empty' : 'answered', $url, $changes, count($numbers) > $take, null);
    }

    /**
     * The changes the words or the path find.
     *
     * Forger finds any of the words, with stems, and ranks by relevance. The
     * review server needs every word. The answer names the host, so a caller
     * reads which of the two answered.
     */
    public function changesMatching(string $words, string $path, bool $open = false, int $limit = 10): array
    {
        if (trim($words) === '' && trim($path) === '') {
            return self::changeAnswer('empty', '', [], false, null);
        }

        $url = self::API . '/changes?' . http_build_query(array_filter([
            'q' => trim($words),
            'path' => trim(trim($path), '/'),
            'status' => $open ? 'open' : 'all',
            'limit' => max(1, min(self::MOST, $limit)),
        ], static fn(string|int $value): bool => $value !== ''));
        $answer = $this->api($url, 'items', Gerrit::CACHE_DURATION);
        if ($answer['body'] === null) {
            return self::changeAnswer('unavailable', $url, [], false, $answer['cause']);
        }

        $changes = array_map(self::change(...), self::items($answer['body'], 'number'));

        return self::changeAnswer($changes === [] ? 'empty' : 'answered', $url, $changes, self::total($answer['body']) > count($changes), null);
    }

    /**
     * The backlog, where Forger knows every filter the caller set.
     *
     * Forger filters by size, branch and date, and sorts by created and by last
     * update. It names no voter and no reviewer, so a filter on a person or on
     * a vote is the review server's. Its open changes include those in
     * progress, which the backlog leaves out, so the set is read whole and
     * those drop here.
     */
    public function changesWaiting(array $filter, int $limit): array
    {
        $served = $filter['minCodeReview'] === 0 && $filter['negativeVotes'] && !$filter['mergeable']
            && $filter['owner'] === '' && $filter['reviewedBy'] === '' && $filter['involving'] === '' && $filter['reviewableBy'] === '';
        $read = self::API . '/changes?' . http_build_query(array_filter([
            'status' => 'open',
            'sort' => $filter['order'] === 'stale' ? 'least-updated' : 'oldest',
            'maxLines' => $filter['maxSize'],
            'branch' => $filter['branch'],
            'updatedBefore' => $filter['updatedBefore'],
        ], static fn(string|int $value): bool => $value !== '' && $value !== 0));
        if (!$served) {
            return [...self::changeAnswer('unavailable', $read, [], false, self::NOT_SERVED), 'read' => 0, 'complete' => false];
        }

        $changes = [];
        $offset = 0;
        do {
            $answer = $this->api($read . '&' . http_build_query(['limit' => self::PAGE, 'offset' => $offset]), 'items', Gerrit::CACHE_DURATION);
            if ($answer['body'] === null) {
                return [...self::changeAnswer('unavailable', $read, [], false, $answer['cause']), 'read' => count($changes), 'complete' => false];
            }
            foreach (self::items($answer['body'], 'number') as $item) {
                if (($item['state'] ?? '') !== 'wip') {
                    $changes[] = self::change($item);
                }
            }
            $offset += self::PAGE;
            $total = self::total($answer['body']);
        } while ($offset < $total && $offset < self::PAGE * self::PAGES);
        $take = max(1, $limit);

        return [
            ...self::changeAnswer($changes === [] ? 'empty' : 'answered', $read, array_slice($changes, 0, $take), count($changes) > $take, null),
            'read' => count($changes),
            'complete' => $offset >= $total,
        ];
    }

    /**
     * @param 'answered'|'empty'|'unavailable' $status
     * @param list<array<string, mixed>> $changes
     * @return Answer
     */
    private static function changeAnswer(string $status, string $query, array $changes, bool $more, ?string $cause): array
    {
        return ['status' => $status, 'query' => $query, 'changes' => $changes, 'dropped' => 0, 'more' => $more, 'cause' => $cause];
    }

    /**
     * One change as Forger sends it, in the shape `Gerrit` answers in.
     *
     * A list item carries the fields alone, and the read of one change carries
     * its `Releases:` branches and where it was cherry-picked from. Forger
     * names no commit and no address, so both stay empty. A vote is on the
     * row already, which the review server sends only for a change read by
     * name.
     *
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private static function change(array $item): array
    {
        $number = is_int($item['number'] ?? null) ? $item['number'] : 0;
        $patchSet = is_int($item['patchSets'] ?? null) ? $item['patchSets'] : 0;
        $project = preg_match('~/c/(.+)/\+/\d+~', self::text($item, 'url'), $found) === 1 ? $found[1] : Gerrit::PROJECT;
        $owner = is_array($item['owner'] ?? null) ? $item['owner'] : [];
        $whole = array_key_exists('commitMessage', $item);
        $picked = is_int($item['cherryPickOf'] ?? null) ? $item['cherryPickOf'] : 0;

        return [
            'number' => $number,
            'message' => null,
            'files' => null,
            'changeId' => self::text($item, 'changeId'),
            'subject' => self::text($item, 'subject'),
            'status' => self::STATES[self::text($item, 'state')] ?? '',
            'branch' => self::text($item, 'branch'),
            'patchSet' => $patchSet,
            'commit' => '',
            'project' => $project,
            'owner' => [
                'name' => self::text($owner, 'name'),
                'username' => self::text($owner, 'username'),
                'email' => '',
                'accountId' => is_int($owner['id'] ?? null) ? $owner['id'] : 0,
            ],
            'updated' => self::reviewTime(self::text($item, 'updatedOn')),
            'created' => self::reviewTime(self::text($item, 'createdOn')),
            'insertions' => is_int($item['insertions'] ?? null) ? $item['insertions'] : null,
            'deletions' => is_int($item['deletions'] ?? null) ? $item['deletions'] : null,
            'mergeable' => is_bool($item['mergeable'] ?? null) ? $item['mergeable'] : null,
            'url' => Gerrit::url($number, $project),
            'fetch' => $patchSet > 0 && $number > 0 ? ['ref' => Gerrit::ref($number, $patchSet), 'remote' => Gerrit::HOST . '/' . $project] : null,
            'labels' => self::labels($item['votes'] ?? null),
            'commentCount' => is_int($item['commentCount'] ?? null) ? $item['commentCount'] : 0,
            'unresolvedCommentCount' => is_int($item['unresolvedCommentCount'] ?? null) ? $item['unresolvedCommentCount'] : 0,
            'comments' => null,
            'chain' => null,
            'namedInMessages' => null,
            'issues' => null,
            'releases' => $whole && is_array($item['releases'] ?? null) ? array_values(array_filter($item['releases'], is_string(...))) : null,
            'messages' => null,
            'botMessageCount' => null,
            'ci' => null,
            'conflicts' => null,
            'cherryPickOf' => $picked > 0 ? ['change' => $picked, 'patchSet' => 0, 'url' => Gerrit::url($picked, $project)] : null,
        ];
    }

    /**
     * The two labels with their state and every vote on them.
     *
     * @return list<array<string, mixed>>|null
     */
    private static function labels(mixed $votes): ?array
    {
        if (!is_array($votes)) {
            return null;
        }

        $labels = [];
        foreach (self::LABELS as $key => $name) {
            $label = is_array($votes[$key] ?? null) ? $votes[$key] : null;
            if ($label === null) {
                continue;
            }
            $state = self::text($label, 'state');
            $cast = [];
            foreach (is_array($label['votes'] ?? null) ? $label['votes'] : [] as $vote) {
                if (is_array($vote)) {
                    $cast[] = [
                        'voter' => self::name($vote['account'] ?? null),
                        'value' => is_int($vote['value'] ?? null) ? $vote['value'] : 0,
                        'on' => self::reviewTime(self::text($vote, 'createdOn')),
                    ];
                }
            }
            $labels[] = ['label' => $name, 'state' => $state, 'satisfied' => $state === '' ? null : $state === 'OK', 'votes' => $cast];
        }

        return $labels;
    }

    /**
     * A time in the review server's own form, so the two providers sort and
     * read alike. Forger writes ISO 8601 in UTC, the review server a space and
     * nanoseconds.
     */
    private static function reviewTime(string $time): string
    {
        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $time, new \DateTimeZone('UTC'));

        return $parsed === false ? $time : $parsed->format('Y-m-d H:i:s') . '.000000000';
    }

    /**
     * One page of one read.
     *
     * @return array{rows: list<Row>, total: int, complete: bool}|null
     */
    private function page(string $read, int $offset, int $limit): ?array
    {
        $answer = $this->api($read . '&' . http_build_query(['limit' => min(self::PAGE, $limit), 'offset' => $offset]), 'items');
        if ($answer['body'] === null) {
            return null;
        }

        return ['rows' => array_map(self::row(...), self::items($answer['body'])), 'total' => self::total($answer['body']), 'complete' => true];
    }

    /**
     * Every issue the reads match, up to the bound, and whether the bound cut
     * the set.
     *
     * @param list<string> $reads
     * @return array{rows: list<Row>, total: int, complete: bool}|null
     */
    private function whole(array $reads): ?array
    {
        $rows = [];
        $total = 0;
        $complete = true;
        foreach ($reads as $read) {
            $offset = 0;
            do {
                $answer = $this->api($read . '&' . http_build_query(['limit' => self::PAGE, 'offset' => $offset]), 'items');
                if ($answer['body'] === null) {
                    return null;
                }
                $rows = [...$rows, ...array_map(self::row(...), self::items($answer['body']))];
                $offset += self::PAGE;
                $count = self::total($answer['body']);
            } while ($offset < $count && $offset < self::PAGE * self::PAGES);
            $total += $count;
            $complete = $complete && $offset >= $count;
        }

        return ['rows' => $rows, 'total' => $total, 'complete' => $complete];
    }

    /**
     * One read of the API, cached where it answered.
     *
     * A body without the key the call expects is an error object, a proxy or a
     * portal, and reads as no answer.
     *
     * @return array{status: int, body: ?array<mixed>, cause: ?string}
     */
    private function api(string $url, string $key, int $cacheDuration = self::CACHE_DURATION): array
    {
        /** @var array{status: int, body: ?array<mixed>, cause: ?string}|null $cached */
        $cached = Cache::get($url, $cacheDuration);
        if ($cacheDuration !== Forge::LIST_CACHE_DURATION) {
            $this->readAt = min($this->readAt ?? PHP_INT_MAX, Cache::storedAt($url));
        }
        if ($cached !== null) {
            return $cached;
        }

        $response = $this->fetch->read($url, ['Accept: application/json']);
        if ($response['body'] === null) {
            return ['status' => $response['status'], 'body' => null, 'cause' => 'source-not-answering'];
        }
        $decoded = Fetch::decode($response['body']);
        if (!array_key_exists($key, $decoded ?? [])) {
            return ['status' => $response['status'], 'body' => null, 'cause' => 'source-not-parseable'];
        }

        $answer = ['status' => $response['status'], 'body' => $decoded, 'cause' => null];
        Cache::put($url, $answer);

        return $answer;
    }

    /**
     * @param array<mixed>|null $body
     * @param string $key the field an issue or a change is numbered by
     * @return list<array<string, mixed>>
     */
    private static function items(?array $body, string $key = 'id'): array
    {
        $items = [];
        foreach (is_array($body['items'] ?? null) ? $body['items'] : [] as $item) {
            if (is_array($item) && is_int($item[$key] ?? null)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /** @param array<mixed> $body */
    private static function total(array $body): int
    {
        return is_int($body['total'] ?? null) ? $body['total'] : 0;
    }

    /** @return Page */
    private static function unavailable(string $url, ?string $cause): array
    {
        return ['status' => 'unavailable', 'url' => $url, 'total' => 0, 'rows' => [], 'complete' => false, 'scanned' => 0, 'cause' => $cause];
    }

    /**
     * One issue as Forger sends it, in the shape every provider answers in. A
     * list item carries the fields alone, and the read of one issue carries
     * the rest.
     *
     * @param array<string, mixed> $item
     * @return Row
     */
    private static function row(array $item): array
    {
        $issue = is_int($item['id'] ?? null) ? $item['id'] : 0;
        $whole = is_array($item['comments'] ?? null);

        return [
            'issue' => $issue,
            'subject' => trim(self::text($item, 'subject')),
            'tracker' => self::text($item, 'tracker'),
            'status' => self::text($item, 'status'),
            'priority' => self::text($item, 'priority'),
            'category' => self::text($item, 'category'),
            'targetVersion' => self::text($item, 'targetVersion'),
            'typo3Version' => self::text($item, 'typo3Version'),
            'phpVersion' => self::text($item, 'phpVersion'),
            'reportedBy' => self::name($item['author'] ?? null),
            'assignedTo' => self::name($item['assignedTo'] ?? null),
            'createdOn' => self::text($item, 'createdOn'),
            'updatedOn' => self::text($item, 'updatedOn'),
            'url' => self::text($item, 'url') !== '' ? self::text($item, 'url') : Forge::HOST . '/issues/' . $issue,
            'description' => $whole ? self::text($item, 'description') : null,
            'relations' => $whole ? self::relations($item) : null,
            'attachments' => $whole ? self::attachments($item) : null,
            'comments' => $whole ? self::notesOf($item['comments']) : null,
            'changes' => $whole ? self::changes($item) : null,
        ];
    }

    /**
     * The relations of an issue, its duplicates among them. Forger keeps the
     * duplicates apart and names every relation from this issue's side.
     *
     * @param array<string, mixed> $item
     * @return list<array{issue: int, relation: string}>
     */
    private static function relations(array $item): array
    {
        $relations = [];
        foreach (is_array($item['relations'] ?? null) ? $item['relations'] : [] as $relation) {
            if (is_array($relation) && is_int($relation['issue'] ?? null) && is_string($relation['type'] ?? null)) {
                $relations[] = ['issue' => $relation['issue'], 'relation' => $relation['type']];
            }
        }
        foreach (['duplicates' => 'duplicates', 'duplicatedBy' => 'duplicated'] as $field => $relation) {
            foreach (is_array($item[$field] ?? null) ? $item[$field] : [] as $other) {
                if (is_int($other)) {
                    $relations[] = ['issue' => $other, 'relation' => $relation];
                }
            }
        }

        return $relations;
    }

    /**
     * @param array<string, mixed> $item
     * @return list<array{filename: string, contentType: string, size: int, on: string, url: string}>
     */
    private static function attachments(array $item): array
    {
        $attachments = [];
        foreach (is_array($item['attachments'] ?? null) ? $item['attachments'] : [] as $file) {
            if (is_array($file) && is_string($file['filename'] ?? null)) {
                $attachments[] = [
                    'filename' => $file['filename'],
                    'contentType' => self::text($file, 'contentType'),
                    'size' => is_int($file['size'] ?? null) ? $file['size'] : 0,
                    'on' => self::text($file, 'createdOn'),
                    'url' => self::text($file, 'url'),
                ];
            }
        }

        return $attachments;
    }

    /**
     * @param array<mixed> $comments
     * @return list<array{author: string, on: string, note: string, bot: bool}>
     */
    private static function notesOf(array $comments): array
    {
        $notes = [];
        foreach ($comments as $comment) {
            $note = is_array($comment) ? trim(self::text($comment, 'text')) : '';
            if ($note === '') {
                continue;
            }
            $notes[] = [
                'author' => self::name($comment['author'] ?? null),
                'on' => self::text($comment, 'createdOn'),
                'note' => $note,
                'bot' => ($comment['bot'] ?? false) === true,
            ];
        }

        return $notes;
    }

    /**
     * @param array<string, mixed> $item
     * @return list<array{change: int, status: string, url: string}>
     */
    private static function changes(array $item): array
    {
        $changes = [];
        foreach (is_array($item['changes'] ?? null) ? $item['changes'] : [] as $change) {
            if (is_array($change) && is_int($change['number'] ?? null)) {
                $changes[] = [
                    'change' => $change['number'],
                    'status' => self::STATES[self::text($change, 'state')] ?? '',
                    'url' => self::text($change, 'url'),
                ];
            }
        }

        return $changes;
    }

    /** @param array<mixed> $item */
    private static function text(array $item, string $key): string
    {
        return is_string($item[$key] ?? null) ? $item[$key] : '';
    }

    private static function name(mixed $person): string
    {
        return is_array($person) && is_string($person['name'] ?? null) ? $person['name'] : '';
    }
}
