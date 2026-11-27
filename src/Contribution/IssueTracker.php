<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Contribution;

use TYPO3\DevCompanion\Http\Cache;
use TYPO3\DevCompanion\Knowledge\Catalog\SystemExtensions;
use TYPO3\DevCompanion\Search\Text;

/**
 * The issues of the core project, built from whichever provider answers first.
 *
 * Every answer a caller sees is built here from the rows a provider returns:
 * the handles a text names, the issues it cites, the code it names, the shape
 * of a set. So two providers answer in one shape, and the next one needs only
 * the reads — `D-ANS-197`.
 *
 * One answer comes from one provider. The provider whose first read answered
 * also serves the reads that fill that answer, so no answer mixes two copies of
 * the tracker.
 *
 * @phpstan-import-type Row from IssueProvider
 * @phpstan-import-type Page from IssueProvider
 */
final class IssueTracker
{
    /**
     * The providers in the order they are asked. The first is the index that
     * answers in a tenth of a second; the second is the tracker itself, which
     * answered nothing in 20 seconds on 2026-11-27 — `D-ANS-197`.
     *
     * @var list<class-string<IssueProvider>>
     */
    private const PROVIDERS = [Forger::class, Forge::class];

    /**
     * The `category` word that asks for the areas themselves.
     *
     * The tracker's own wildcard. No area has that name, so it cannot take a
     * subject's place.
     */
    public const EVERY_AREA = '*';

    /**
     * How many comments come back. The decision is usually in the last few, and
     * an issue with two hundred comments is one nobody reads whole.
     */
    private const NOTES = 15;

    /**
     * The most words a miss probes one at a time, and beyond it no probe at
     * all. A query longer than this is one whose answer is to pass fewer words,
     * which the miss says without a read of anything.
     */
    private const TERMS = 6;

    /**
     * The most issues an enumeration answers with. Higher than a search's,
     * because a triage picks candidates out of the set it sees.
     */
    private const LISTED = 50;

    /**
     * The most buckets a dimension answers with. The tail of an area count is
     * twenty subsystems with one issue each. 38 areas over the 621 issues one
     * person had filed on 2026-08-19, of which the largest four held two
     * thirds.
     */
    private const BUCKETS = 12;

    /**
     * The provider list a test hands in, as classes, so each is built after the
     * test set its transport. Null puts the real list back.
     *
     * @var list<class-string<IssueProvider>>|null
     */
    private static ?array $providers = null;

    /** @var list<IssueProvider> */
    private readonly array $order;

    /**
     * The providers this instance asked, for the age of its answer.
     *
     * @var list<IssueProvider>
     */
    private array $asked = [];

    /** The host of the provider that answered the last call. */
    private string $source = '';

    /** @param list<IssueProvider>|null $providers */
    public function __construct(?array $providers = null)
    {
        $this->order = $providers ?? array_map(
            static fn(string $class): IssueProvider => new $class(),
            self::$providers ?? self::PROVIDERS,
        );
    }

    /** @param list<class-string<IssueProvider>>|null $providers */
    public static function useProviders(?array $providers): void
    {
        self::$providers = $providers;
    }

    /**
     * The host that answered the last call, or the first one asked where none
     * did.
     */
    public function source(): string
    {
        return $this->source !== '' ? $this->source : $this->order[0]->host();
    }

    /** When the answer this instance gave was read — `D-ANS-049`. */
    public function readAt(): string
    {
        $reads = array_filter(array_map(
            static fn(IssueProvider $provider): ?int => $provider->readAt(),
            $this->asked,
        ), is_int(...));

        return Cache::timestamp($reads === [] ? null : min($reads));
    }

    /**
     * One issue, with the decision about it.
     *
     * An index that does not carry the number may be minutes behind the
     * tracker. So a miss asks the next provider too, and the answer is a miss
     * only where none carries it.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, issue: ?array<string, mixed>, cause: ?string}
     */
    public function issue(int $number, string $notes = 'all'): array
    {
        $empty = null;
        $unavailable = null;
        foreach ($this->order as $provider) {
            $answer = $this->ask($provider, static fn(): array => $provider->issue($number));
            if ($answer['status'] === 'answered' && $answer['row'] !== null) {
                $found = $this->referenced($provider, [self::issueOf($answer['row'], $notes)])[0];

                return ['status' => 'answered', 'url' => $answer['url'], 'issue' => $found, 'cause' => null];
            }
            if ($answer['status'] === 'empty') {
                $empty ??= ['status' => 'empty', 'url' => $answer['url'], 'issue' => null, 'cause' => null];
            } else {
                $unavailable ??= ['status' => 'unavailable', 'url' => $answer['url'], 'issue' => null, 'cause' => $answer['cause']];
            }
        }

        return $empty ?? $unavailable ?? ['status' => 'unavailable', 'url' => '', 'issue' => null, 'cause' => null];
    }

    /**
     * The issues whose text matches these words.
     *
     * Nothing here ranks, and the query comes back with the answer, because one
     * wording does not settle which issue is the duplicate (`D-ANS-038`).
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, query: string, total: int, terms: list<array{term: string, matchCount: int}>, results: list<array<string, mixed>>, cause: ?string}
     */
    public function search(string $query, int $limit = 15): array
    {
        $words = trim($query);
        $page = null;
        $answered = null;
        foreach ($this->order as $provider) {
            $page = $this->ask($provider, static fn(): array => $provider->search($words, $limit));
            if ($page['status'] !== 'unavailable') {
                $answered = $provider;
                break;
            }
        }
        if ($answered === null) {
            return ['status' => 'unavailable', 'url' => $page['url'] ?? '', 'query' => $words, 'total' => 0, 'terms' => [], 'results' => [], 'cause' => $page['cause'] ?? null];
        }

        $results = array_map(self::hit(...), $page['rows']);

        return [
            'status' => $results === [] ? 'empty' : 'answered',
            'url' => $page['url'],
            'query' => $words,
            'total' => $page['total'],
            'terms' => $results === [] ? self::reach($answered, $words) : [],
            'results' => $results,
            'cause' => null,
        ];
    }

    /**
     * The fields of a set of issues, from the first provider that answers for
     * them. Empty where none did.
     *
     * @param list<mixed> $numbers
     * @return array<int, Row>
     */
    public function fields(array $numbers): array
    {
        foreach ($this->order as $provider) {
            $fields = $this->ask($provider, static fn(): array => $provider->fields($numbers));
            if ($fields !== []) {
                return $fields;
            }
        }

        return [];
    }

    /**
     * The issues of the core project, at whichever end of it the caller asked
     * for.
     *
     * This answers the question a triage starts from and neither of the two
     * above reaches: the issue nobody has looked at since 2015, which no number
     * and no words find. `total` comes back with the page, so a caller can see
     * whether 30 was the set or a first screenful (`D-ANS-116`, `D-ANS-090`,
     * `D-ANS-180`).
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, total: int, categories: list<string>, categoriesUsed: list<string>, people: list<array{filter: string, asked: string, name: string, id: int, candidates: list<string>}>, breakdown: ?array<string, mixed>, wordsScan: ?array{read: int, complete: bool}, results: list<array<string, mixed>>, cause: ?string}
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
        string $words = '',
        int $offset = 0,
    ): array {
        $answer = null;
        foreach ($this->order as $provider) {
            $answer = $this->ask($provider, fn(): array => $this->backlogOf(
                $provider,
                $order,
                $tracker,
                $category,
                $createdBefore,
                $createdSince,
                $updatedBefore,
                $limit,
                $status,
                $reportedBy,
                $assignedTo,
                $involving,
                $breakdown,
                $words,
                $offset,
            ));
            if ($answer['status'] !== 'unavailable') {
                return $answer;
            }
        }

        return $answer ?? ['status' => 'unavailable', 'url' => '', 'total' => 0, 'categories' => [], 'categoriesUsed' => [], 'people' => [], 'breakdown' => null, 'wordsScan' => null, 'results' => [], 'cause' => null];
    }

    /**
     * The backlog as one provider answers it.
     *
     * @return array{status: 'answered'|'empty'|'unavailable', url: string, total: int, categories: list<string>, categoriesUsed: list<string>, people: list<array{filter: string, asked: string, name: string, id: int, candidates: list<string>}>, breakdown: ?array<string, mixed>, wordsScan: ?array{read: int, complete: bool}, results: list<array<string, mixed>>, cause: ?string}
     */
    private function backlogOf(
        IssueProvider $provider,
        string $order,
        string $tracker,
        string $category,
        string $createdBefore,
        string $createdSince,
        string $updatedBefore,
        int $limit,
        string $status,
        string $reportedBy,
        string $assignedTo,
        string $involving,
        bool $breakdown,
        string $words,
        int $offset,
    ): array {
        $areas = $category !== '' ? $provider->areas() : [];
        // The vocabulary asked for on purpose, and no issue read at all. Every
        // other call gets the areas only where a word named none or several,
        // which keeps 54 names off the answers nobody asked them of
        // (`D-KNW-113`).
        if ($category === self::EVERY_AREA) {
            return [
                'status' => $areas === [] ? 'unavailable' : 'answered',
                'url' => $provider->host(),
                'total' => 0,
                'categories' => $areas,
                'categoriesUsed' => [],
                'people' => [],
                'breakdown' => null,
                'wordsScan' => null,
                'results' => [],
                'cause' => $areas === [] ? 'source-not-answering' : null,
            ];
        }
        if ($category !== '' && $areas === []) {
            return ['status' => 'unavailable', 'url' => $provider->host(), 'total' => 0, 'categories' => [], 'categoriesUsed' => [], 'people' => [], 'breakdown' => null, 'wordsScan' => null, 'results' => [], 'cause' => 'source-not-answering'];
        }

        $vocabulary = array_flip($areas);
        $used = $category === '' ? [] : self::named($vocabulary, $category);
        // A word that names no area is regularly a system extension key, which
        // is what a caller in a checkout holds (`D-ANS-142`).
        if ($used === [] && $category !== '') {
            $filed = SystemExtensions::forgeCategory($category);
            $used = isset($vocabulary[$filed]) ? [$filed] : [];
        }
        // Only where it can do the work it answers for: a correction of a word
        // that named no area or several.
        $known = $category !== '' && count($used) !== 1 ? $areas : [];

        $people = [];
        $ids = ['reportedBy' => 0, 'assignedTo' => 0, 'involving' => 0];
        // `involving` is the same person on either side, so it takes the place
        // of the two that name one side each.
        $roles = $involving !== ''
            ? ['involving' => $involving]
            : ['reportedBy' => $reportedBy, 'assignedTo' => $assignedTo];
        foreach ($roles as $filter => $word) {
            if ($word !== '') {
                $person = self::person($provider->people($word));
                $people[] = ['filter' => $filter, 'asked' => $word] + $person;
                $ids[$filter] = $person['id'];
            }
        }

        // A word that matches no category, and a name that matches no person,
        // would otherwise get the unfiltered backlog: a set about everything in
        // the shape of a set about one thing.
        $unresolved = array_filter($people, static fn(array $person): bool => $person['id'] === 0);
        if (($category !== '' && $used === []) || $unresolved !== []) {
            return ['status' => 'empty', 'url' => $provider->host(), 'total' => 0, 'categories' => $known, 'categoriesUsed' => $category !== '' ? $used : [], 'people' => $people, 'breakdown' => null, 'wordsScan' => null, 'results' => [], 'cause' => null];
        }

        $take = max(1, min(self::LISTED, $limit));
        $page = $provider->backlog([
            'status' => in_array($status, ['closed', 'all'], true) ? $status : 'open',
            'order' => in_array($order, ['stale', 'newest'], true) ? $order : 'oldest',
            'tracker' => $tracker,
            'areas' => $used,
            'createdSince' => self::isDate($createdSince) ? $createdSince : '',
            'createdBefore' => self::isDate($createdBefore) ? $createdBefore : '',
            'updatedBefore' => self::isDate($updatedBefore) ? $updatedBefore : '',
            'reportedBy' => $ids['reportedBy'],
            'assignedTo' => $ids['assignedTo'],
            'involving' => $ids['involving'],
            'words' => $words,
        ], $offset, $take, $breakdown);
        if ($page['status'] === 'unavailable') {
            return ['status' => 'unavailable', 'url' => $page['url'], 'total' => 0, 'categories' => $known, 'categoriesUsed' => $used, 'people' => $people, 'breakdown' => null, 'wordsScan' => null, 'results' => [], 'cause' => $page['cause']];
        }

        $rows = $page['rows'];
        $counted = null;
        if ($breakdown) {
            $counted = ['read' => count($rows), 'complete' => $page['complete'], 'counts' => self::counts($rows)];
            // A set a page holds whole comes back with its rows. The counts
            // alone sent a session back for the 18 rows they were read off
            // (`D-ANS-090`). Where the second read does not answer, the rows
            // stand as they came, which is a row with no relation rather than
            // no row.
            if ($page['complete'] && count($rows) <= self::LISTED) {
                $rows = $provider->rows(array_column($rows, 'issue')) ?: $rows;
            } else {
                $rows = [];
            }
        }

        $results = [];
        foreach ($rows as $row) {
            $results[] = self::entry($row);
        }
        $results = $this->referenced($provider, $results);
        if ($order === 'stale') {
            $results = self::noted($provider, $results);
        }

        return [
            'status' => $results === [] && ($counted === null || $counted['read'] === 0) ? 'empty' : 'answered',
            'url' => $page['url'],
            'total' => $page['total'],
            'categories' => $known,
            'categoriesUsed' => $used,
            'people' => $people,
            'breakdown' => $counted,
            'wordsScan' => $words !== '' ? ['read' => $page['scanned'], 'complete' => $page['complete']] : null,
            'results' => $results,
            'cause' => null,
        ];
    }

    /**
     * One read, with the provider remembered as the source and as a read the
     * answer's age counts.
     *
     * @template T
     * @param \Closure(): T $read
     * @return T
     */
    private function ask(IssueProvider $provider, \Closure $read): mixed
    {
        $answer = $read();
        if (!in_array($provider, $this->asked, true)) {
            $this->asked[] = $provider;
        }
        $this->source = $provider->host();

        return $answer;
    }

    /**
     * The person a name means, out of the people a provider found for it.
     *
     * Only a name carried whole decides. Half of "Andreas Kießling" is four
     * other people called Andreas. A name that reaches two of them resolves to
     * neither and answers with both, because two people merged into one backlog
     * is a wrong answer nothing about it says is wrong.
     *
     * @param array<string, int> $found
     * @return array{name: string, id: int, candidates: list<string>}
     */
    private static function person(array $found): array
    {
        if (count($found) === 1) {
            return ['name' => (string) array_key_first($found), 'id' => (int) reset($found), 'candidates' => []];
        }

        return ['name' => '', 'id' => 0, 'candidates' => array_map(strval(...), array_keys($found))];
    }

    /**
     * The entries of a name-to-id list a caller's words name, in the tracker's
     * own spelling. Its areas and its people are both such a list.
     *
     * Nobody types "RTE (rtehtmlarea + ckeditor)". They type "rte", and they
     * type "backend ui" for a name that carries neither word whole. So the
     * words match one at a time and at a word boundary. Every word carried
     * wins and one word carried is the fallback.
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
     * The entries that carry every word, which is how a provider narrows the
     * people it knows to the ones a name means.
     *
     * @param array<string, int> $entries
     * @return array<string, int>
     */
    public static function carrying(array $entries, string $words): array
    {
        return array_intersect_key($entries, array_flip(self::matching($entries, $words)[0]));
    }

    /**
     * The entries with every word, and the entries with some of them.
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
            $name = (string) $name;
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
     * What each word of an emptied query reaches on its own.
     *
     * The half of a miss nothing on this side can supply. Two class names look
     * alike from here and the tracker knows one of them, so the advice to drop
     * the identifiers would have dropped the word that answers (`D-ANS-038`).
     *
     * @return list<array{term: string, matchCount: int}>
     */
    private static function reach(IssueProvider $provider, string $query): array
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
            $count = $provider->matchCount((string) $word);
            // A host that stopped answering says nothing about the words left,
            // and a miss that already answered is not turned into an outage.
            if ($count === null) {
                return $reach;
            }
            $reach[] = ['term' => (string) $word, 'matchCount' => $count];
        }

        return $reach;
    }

    /**
     * The issues these records name, filled with what decides whether to read
     * one: the relations they stand against, and the citations an issue's prose
     * carries.
     *
     * One bulk read for everything handed in (`D-ANS-064`). A citation stays
     * only where the read answered for the number. Three digits in a sentence
     * are as often a version as an issue (`D-ANS-123`).
     *
     * @param list<array<string, mixed>> $records
     * @return list<array<string, mixed>>
     */
    private function referenced(IssueProvider $provider, array $records): array
    {
        $numbers = [];
        foreach ($records as $record) {
            $numbers = [
                ...$numbers,
                ...array_column($record['relations'], 'issue'),
                ...array_column($record['mentioned'] ?? [], 'issue'),
            ];
        }

        $fields = $numbers === [] ? [] : $provider->fields($numbers);
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
     * When a person last wrote a comment on each row.
     *
     * The tracker's own date moves on any edit. Four issues one person touched
     * within minutes and without a word read as fresh on the stale end
     * (`D-ANS-181`).
     *
     * @param list<array<string, mixed>> $results
     * @return list<array<string, mixed>>
     */
    private static function noted(IssueProvider $provider, array $results): array
    {
        foreach ($results as $at => $row) {
            foreach ($provider->comments((int) $row['issue']) ?? [] as $comment) {
                if (!$comment['bot'] && $comment['on'] > $results[$at]['lastNoteOn']) {
                    $results[$at]['lastNoteOn'] = $comment['on'];
                }
            }
        }

        return $results;
    }

    /**
     * One hit of a search, as the identity and the triage state a caller who
     * sorts a set of them needs.
     *
     * Nothing asks for the relations, the files or the changes of a hit. A
     * search answers which issues mention some words, and those three are what
     * a triage chooses a backlog row on.
     *
     * @param Row $row
     * @return array<string, mixed>
     */
    private static function hit(array $row): array
    {
        return [
            'issue' => $row['issue'],
            'subject' => $row['subject'],
            'tracker' => $row['tracker'],
            'status' => $row['status'],
            'category' => $row['category'],
            'reportedBy' => $row['reportedBy'],
            'assignedTo' => $row['assignedTo'],
            'createdOn' => $row['createdOn'],
            'updatedOn' => $row['updatedOn'],
            'url' => $row['url'],
            'relations' => [],
            'attachments' => [],
            'reviews' => [],
            'cites' => [],
            'lastNoteOn' => '',
            'reviewAbandoned' => false,
        ];
    }

    /**
     * One issue of an enumeration, in the shape a search hit comes back in.
     *
     * The two dates say different things. Filed long ago is about the report,
     * untouched for years is about the attention it got. The relations, the
     * files and the changes decide which row is worth reading — `D-ANS-069`.
     * The row reads for what its subject and its report name and says nothing
     * about the comments, `D-ANS-122`.
     *
     * @param Row $row
     * @return array<string, mixed>
     */
    private static function entry(array $row): array
    {
        $reviews = array_map(
            static fn(array $change): array => ['change' => $change['change'], 'status' => $change['status'], 'url' => $change['url']],
            $row['changes'] ?? [],
        );

        return [
            'issue' => $row['issue'],
            'subject' => $row['subject'],
            'tracker' => $row['tracker'],
            'status' => $row['status'],
            'category' => $row['category'],
            'reportedBy' => $row['reportedBy'],
            'assignedTo' => $row['assignedTo'],
            'createdOn' => $row['createdOn'],
            'updatedOn' => $row['updatedOn'],
            'url' => $row['url'],
            'relations' => self::relations($row),
            'attachments' => $row['attachments'] ?? [],
            'reviews' => $reviews,
            'cites' => CitedCode::in($row['subject'], $row['description'] ?? ''),
            // Filled by `noted()` on the stale end.
            'lastNoteOn' => '',
            'reviewAbandoned' => $row['status'] === 'Under Review'
                && array_values(array_unique(array_column($reviews, 'status'))) === ['ABANDONED'],
        ];
    }

    /**
     * One issue read whole, with what its text names.
     *
     * @param Row $row
     * @return array<string, mixed>
     */
    private static function issueOf(array $row, string $wanted): array
    {
        $notes = [];
        $written = [];
        foreach ($row['comments'] ?? [] as $comment) {
            $entry = ['author' => $comment['author'], 'on' => $comment['on'], 'note' => $comment['note']];
            $notes[] = $entry;
            if (!$comment['bot']) {
                $written[] = $entry;
            }
        }
        // Filtered before the slice. So the drop of the pings is what lets more
        // of what a person wrote fit inside the bound.
        $shown = $wanted === 'people' ? $written : $notes;

        $description = trim($row['description'] ?? '');
        $relations = self::relations($row);
        // The description is a text like a note and reads as one. A review URL
        // pasted into the report is a handle as much as one in a comment
        // (`D-ANS-123`).
        $texts = [
            ['author' => $row['reportedBy'], 'on' => $row['createdOn'], 'note' => $description],
            ...$notes,
        ];

        return [
            'id' => $row['issue'],
            'subject' => $row['subject'],
            'status' => $row['status'],
            'tracker' => $row['tracker'],
            'priority' => $row['priority'],
            'assignedTo' => $row['assignedTo'],
            'targetVersion' => $row['targetVersion'],
            'typo3Version' => $row['typo3Version'],
            'phpVersion' => $row['phpVersion'],
            'createdOn' => $row['createdOn'],
            'updatedOn' => $row['updatedOn'],
            'url' => $row['url'],
            'description' => $description,
            'relations' => $relations,
            // Filled by `referenced()`, which resolves the numbers in the read
            // the relations already make.
            'mentioned' => self::mentions($description, $notes, $row['issue'], array_column($relations, 'issue')),
            'attachments' => $row['attachments'] ?? [],
            // Read from every note rather than from the ones that come back, so
            // a patch-set ping older than the bound is still a handle.
            'reviews' => self::onReview(self::reviews($texts), $row['changes'] ?? []),
            // What a report names its code in is as often the comment that
            // reproduced it as the description, `D-ANS-122`.
            'cites' => CitedCode::in($row['subject'], $description, ...array_column($notes, 'note')),
            'noteCount' => count($notes),
            'botNoteCount' => count($notes) - count($written),
            'notes' => array_slice($shown, -self::NOTES),
        ];
    }

    /**
     * The relations of a row, each with the URL that reaches the other issue.
     *
     * @param Row $row
     * @return list<array<string, mixed>>
     */
    private static function relations(array $row): array
    {
        return array_map(
            static fn(array $relation): array => $relation + ['url' => Forge::HOST . '/issues/' . $relation['issue']],
            $row['relations'] ?? [],
        );
    }

    /**
     * The handles the issue's own text names and the changes the provider
     * holds for its number, as one list.
     *
     * Neither half contains the other. A change under discussion in a comment
     * whose commit message never named the issue is absent from the review
     * server's answer, and the reverse holds too. An empty list means neither
     * has one — `D-ANS-125`. Once per change number.
     *
     * @param list<array<string, mixed>> $named what the notes carry
     * @param list<array{change: int, status: string, url: string}> $changes
     * @return list<array<string, mixed>>
     */
    private static function onReview(array $named, array $changes): array
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

        foreach ($changes as $change) {
            if (isset($at[$change['change']])) {
                $reviews[$at[$change['change']]]['status'] = $change['status'];
                continue;
            }
            $reviews[] = [
                'change' => $change['change'],
                'changeId' => '',
                'patchSet' => 0,
                'on' => '',
                'url' => $change['url'],
                'status' => $change['status'],
            ];
        }

        return $reviews;
    }

    /**
     * The issues the report and the comments cite, which no relation carries.
     *
     * A relation is somebody's triage and a citation is the writer's own claim
     * about prior art (`D-ANS-123`). A number already on file as a relation
     * stays with the relation, which says more about it. Each once, in the
     * order of the text, and a citation in both texts is a description.
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
                'url' => Forge::HOST . '/issues/' . $number,
                'where' => $text,
            ];
        }

        return $mentioned;
    }

    /**
     * The issue numbers one text cites, in the two forms people write them in.
     *
     * A URL is the rare form. 5 of 200 open bugs read on 2026-08-27 carried
     * one, against 29 of them with Redmine's own `#NNNN`. That form binds to
     * what an issue number is, because a TYPO3 exception code is ten digits
     * (`D-ANS-123`).
     *
     * @return list<int>
     */
    private static function cited(string $text): array
    {
        preg_match_all('~(?:forge\.typo3\.org/issues/|#(?=\d{3,6}(?!\d)))(\d+)~', $text, $found);

        $numbers = [];
        foreach ($found[1] as $citation) {
            $numbers[(int) $citation] = (int) $citation;
        }

        return array_values($numbers);
    }

    /**
     * The review changes the report and the comments name, as handles rather
     * than as prose (`D-ANS-064`).
     *
     * Two passes, because the bot's note names the change id and the number
     * together where a human's later note is a bare URL.
     *
     * @param list<array{author: string, on: string, note: string}> $notes The
     *     description first and then the comments, in the order of the text,
     *     so the date on a handle is the last text that named it.
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
                // The comments are in order, so the last note naming a change
                // is what says how old the reference is.
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
     * is a query rather than a change, and a match on digits anywhere in it
     * would report a topic as a change.
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
     * How a set of issues distributes, one list per dimension.
     *
     * What came of them, what kind of work they were, which part of the core,
     * and when. Ordered by size and bounded, because the head already says
     * what the tail would.
     *
     * @param list<Row> $rows
     * @return list<array{dimension: string, buckets: list<array{name: string, count: int}>, withheldBuckets: int, withheldCount: int}>
     */
    private static function counts(array $rows): array
    {
        $counts = ['status' => [], 'tracker' => [], 'category' => [], 'year' => [], 'touched' => []];
        foreach ($rows as $row) {
            foreach (['status', 'tracker', 'category'] as $dimension) {
                // An issue nobody filed under an area is a bucket of its own.
                // Left out, the areas add up to less than the set.
                $name = $row[$dimension];
                $counts[$dimension][$name === '' ? 'none' : $name] ??= 0;
                $counts[$dimension][$name === '' ? 'none' : $name]++;
            }
            // The year it was filed, and the year anything last moved on it.
            foreach (['year' => 'createdOn', 'touched' => 'updatedOn'] as $dimension => $field) {
                $year = substr($row[$field], 0, 4);
                if ($year !== '') {
                    $counts[$dimension][$year] ??= 0;
                    $counts[$dimension][$year]++;
                }
            }
        }

        $answer = [];
        foreach ($counts as $dimension => $buckets) {
            // By size, and by name where two are the same size: an answer that
            // reorders between two identical calls is one nobody can diff.
            uksort($buckets, static fn(int|string $one, int|string $other): int => [$buckets[$other], (string) $one] <=> [$buckets[$one], (string) $other]);
            $shown = array_slice($buckets, 0, self::BUCKETS, true);
            $answer[] = [
                'dimension' => $dimension,
                'buckets' => array_map(
                    static fn(int|string $name, int $count): array => ['name' => (string) $name, 'count' => $count],
                    array_keys($shown),
                    array_values($shown),
                ),
                'withheldBuckets' => count($buckets) - count($shown),
                'withheldCount' => array_sum($buckets) - array_sum($shown),
            ];
        }

        return $answer;
    }

    /** A day a provider can filter on, and nothing else. */
    private static function isDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1;
    }
}
