<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\CitedCode;
use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Installation\Instance;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unreachable;

/**
 * What a Forge issue actually says, including the part that decides it.
 *
 * Four round trips and a trap by hand, and the decision sits in the journal
 * rather than in the description (`D-FBK-027`). What the tool description opens
 * on is what a request written by hand cannot do, because the core's own
 * `AGENTS.md` hands the caller a `curl` recipe for this tracker (`D-AUD-014`).
 * A number is not the only way in:
 * whether somebody else already reported this is asked before a patch and no
 * number answers it, so `query` searches the tracker by words (`D-ANS-038`). Nor
 * is a wording — a triage starts before there is an issue in hand at all, and
 * the issue nobody has touched since 2015 is worded the way nobody thinks of, so
 * `open` is that way in. Both ends of it: the neglected one a triage reads, and
 * the recent one a duplicate of a fresh defect is at (`D-ANS-116`).
 */
final class ForgeLookup extends ReadOnlyTool
{
    /** The issue is read from the tracker at forge.typo3.org. */
    protected const OPEN_WORLD = true;

    /**
     * How many cited names a row prints, the rest being in the data.
     *
     * A page is read to choose one row out of thirty, and a stack trace naming
     * a file per frame would take that page's whole screen.
     */
    private const CITED_PER_ROW = 6;

    /**
     * Why nothing was answered, in the caller's terms rather than the
     * transport's — one shape for all three ways in, because what a caller does
     * about it is the same whichever question it asked.
     */
    private const UNREACHABLE = [
        Unreachable::NOT_ANSWERING => 'The tracker did not answer. It is reachable at ' . Forge::HOST
            . ' in a browser; nothing here can answer this offline.',
        Unreachable::NOT_PARSEABLE => 'Something answered with a page rather than with the API. The tracker sits '
            . 'behind bot protection, and what it challenges is a browser-shaped request.',
    ];

    public static function name(): string
    {
        return 'typo3_forge_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        // The tracker answers the issue, and the files the installed packages
        // ship answer whether the code it names is still there — `D-ANS-122`.
        return [Source::Network, Source::Packages];
    }

    public static function description(): string
    {
        return 'Reads the TYPO3 issue tracker at forge.typo3.org through the bot protection the core\'s own AGENTS.md warns a hand-written request about. It tells a tracker that did not answer from a search that matched nothing. Read it before writing a patch. Three ways in, one per call. issue reads one issue whole: the report, the comments that decided it, the issues and review changes it names, and whether the code it cites is still shipped here. query finds the other issues describing the same thing, which the relations of one issue carry only where somebody linked them. open enumerates the core project\'s unresolved backlog without a number or a wording: oldest filed, longest untouched or newest. Narrow it by tracker, area, date and person, widen it with status, and breakdown answers how a large set is distributed instead of a page of it. A miss is an answer. An issue that does not exist is answered as such, and words matching nothing are counted one word at a time — which is not that nobody reported it. The patch for an issue on review.typo3.org is typo3_gerrit_lookup. Reading only, and no credential: commenting, assigning and closing stay yours.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'issue' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Forge issue number, with or without the leading #, for example "110348". Reads that one issue whole, comments included — narrow those with notes when reading many. Not with query or open.',
                ],
                'query' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Words to search the tracker for, for example "image cache busting". A full-text search over subject, description and comments, which is how a duplicate nobody has linked is found at all. Every word has to be in the same issue. A term nobody would have written — a method name, a class — empties the answer whatever else is in it. Pass the two or three words that name the subject rather than every word you have; a miss counts what each word reaches on its own, in terms. Nothing is ranked and one wording does not settle it: ask again in the reporter\'s words as well as your own. A person\'s name matches only where somebody wrote it, so pass it as reportedBy or assignedTo with open instead. Not with issue or open.',
                ],
                'open' => [
                    'type' => 'string',
                    'enum' => ['oldest', 'stale', 'newest'],
                    'description' => 'Enumerate the core project\'s unresolved issues instead of reading one or matching words. "oldest" orders them by when they were filed, "stale" by how long nobody has touched them, "newest" by what came in last. Filed long ago is about the report, untouched for years about the attention it got, and an issue that is both is the candidate a triage is looking for. "stale" with tracker and category is where a triage of the backlog starts. "newest" is where a duplicate of a defect somebody has just found is. A wording reaches only the issues worded that way, so reading the subjects filed since it could have been is what settles a negative. Pair it with createdSince, which turns that end into a set the count says you have seen the whole of. Unresolved is the tracker\'s own set of open statuses: New, Accepted, Under Review, Needs Feedback, On Hold and Postponed. tracker, category, createdBefore, createdSince, updatedBefore, reportedBy, assignedTo, involving and breakdown narrow this way in and no other; status widens it. Not with issue or query.',
                ],
                'notes' => [
                    'type' => 'string',
                    'enum' => ['all', 'people'],
                    'default' => 'all',
                    'description' => 'Which comments come back with an issue. "all" is every one of them, which is what reading a single issue wants. The comments are where the decision is, and the one that settles it is regularly the last of sixteen. "people" drops the patch-set pings a review bot wrote, which on some issues is half the volume. The change numbers in them are lifted into reviews either way, so nothing is lost. Ask for it when reading candidates one issue at a time, where the cost of ten such reads decides whether the comments get read at all. How many were dropped is answered whichever you ask for. Narrows issue and is ignored by query and open.',
                ],
                'tracker' => [
                    'type' => 'string',
                    'enum' => ['Bug', 'Feature', 'Major Feature', 'Support', 'Task', 'Story', 'Suggestion', 'Impediment', 'Epic', 'Work Package', 'Topic'],
                    'description' => 'Only issues filed under this tracker, for example "Bug". Worth setting before reading a set: an old Bug claims something is broken today, an old Feature that something was wanted once.',
                ],
                'category' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only issues the core files under this area, in your own words: "rte", "backend ui", "workspaces", "fluid". Matched against the project\'s own category names one word at a time, so a half-remembered name reaches the right area. A word naming several — "backend" — selects all of them and says which. It is the way in for "are there known bugs in the RTE" and "the oldest issues in the backend UI". It answers "has this already been reported" too: enumerate the area and read the subjects. A word naming none or several is answered with every area the project has. categoriesUsed carries the tracker\'s own spelling of the areas reached, which a report filed by hand has to carry. Pass "*" for the list of areas on its own, which reads no issues.',
                ],
                'createdBefore' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only issues filed before this day, as YYYY-MM-DD. With createdSince it is the far end of one window rather than a second filter.',
                ],
                'createdSince' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only issues filed on or after this day, as YYYY-MM-DD. It is what makes the recent end a set instead of a page. limit stops at 50 against thousands of open issues, so a day to count from brings the page and the set together; total says whether it did. It also reaches where category cannot: an issue filed under no Category is in no area at all, and the report you are looking for is regularly one of those.',
                ],
                'updatedBefore' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only issues nobody has touched since this day, as YYYY-MM-DD. It finds the report everybody has walked past, which age alone does not: an issue filed in 2009 and commented on last month is being worked.',
                ],
                'reportedBy' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only issues this person filed, by their name rather than a tracker id: "Frank Nägler", or "nägler". This answers "what has this person reported", which query cannot: it matches text, and a name in the text is as often somebody else writing it. The name is resolved against the core project\'s members and, where they hold no membership, against the people the issues carrying that name were filed by or handed to. A name reaching several people resolves to none of them and the answer says which they were. Pair it with status "all" for everything somebody has ever filed, and with breakdown for the shape of it rather than the first page. involving is the union of this and assignedTo.',
                ],
                'assignedTo' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only issues this person holds, by their name, resolved the same way as reportedBy. What somebody reported is their history; what they are assigned is what they are on the hook for. An assignee on an old issue is usually who last touched it rather than who is working on it. Passing both of these is the issues somebody filed and holds; involving is their union.',
                ],
                'involving' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only issues this person is on either side of — what they filed and what they hold, as one set. The tracker cannot be asked this: it ANDs its filters, so reportedBy and assignedTo together mean filed AND holds, a set nobody wants. Passed instead of those two, not beside them. Every row says which side it came in on.',
                ],
                'breakdown' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Answer how the matched set is distributed instead of the rows of it: how many issues per status, per tracker, per area and per year. For a person this is the answer rather than a summary of it. "621 filed, 617 closed, 4 open, concentrated 2014-2016, mostly Backend User Interface" says what a page of 50 out of 621 cannot. Ask for it whenever the question is what somebody or some area has been about, and for the rows once it is which issue to read. It costs a read per hundred issues and stops at a thousand, saying so where it did.',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['open', 'closed', 'all'],
                    'default' => 'open',
                    'description' => 'Which statuses the enumeration covers. "open", the default, is the tracker\'s own unresolved set; "closed" is what it has marked closed, Rejected included; "all" is both. A question about a person needs "all": what somebody has filed over the years is mostly closed, and an enumeration hiding those answers 4 where the number is 621.',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 50,
                    'default' => 15,
                    'description' => 'How many entries come back. A search answers with at most 25 whatever is asked for: a set that has to be paged through is answered by other words rather than by more of these. Nothing reaches past 50 and there is no offset; a matched set larger than that is answered by breakdown.',
                ],
            ],
            'oneOf' => [
                ['required' => ['issue']],
                ['required' => ['query']],
                ['required' => ['open']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'source' => Schema::string('The tracker the answer came from.'),
            'url' => Schema::string('What was read, so the same question can be asked again by hand. A union is two reads and both are named, separated by a space.'),
            'query' => Schema::string('The words the tracker was searched for, so a set that looks too narrow can be asked again in other words. Empty where an issue was read by number and where the open issues were enumerated.'),
            'placedAgainst' => Schema::string('The TYPO3 version of the installation the names in cites were placed against, so a verdict about a symbol is read at a version. Empty where no installation was found, and then every cited name is unplaced — a statement about this machine and not about the code.'),
            'total' => Schema::integer('How many issues matched in total, of which results carries at most limit. Where the two differ the answer is a page and not the set, and what reaches more of it is a narrower filter rather than a bigger limit. Zero where an issue was read by number.'),
            'terms' => Schema::listOf(Schema::object([
                'word' => Schema::string('One word of the query, as it was passed.'),
                'total' => Schema::integer('How many issues that word reaches on its own. Zero is a word no issue on the tracker carries, which empties every query it is in whatever else is in it.'),
            ], ['word', 'total']), 'What each word of the query reaches on its own, which says which of them emptied the answer. Two class names look alike from here and the tracker may know only one, so this is read rather than guessed at. Asked on a miss alone, one read per word, which is why a query holds more than one word and no more than a few. Empty otherwise, and short of the query where the tracker stopped answering partway through it.'),
            'categories' => Schema::listOf(Schema::string(), 'Every area the core files its issues under, read from the project itself. A category word matching none or several is corrected from the answer rather than from a second call. Answered where category was passed and did not resolve to exactly one area, and where it was passed as "*". Empty otherwise, which says nothing about the project: the project administers the vocabulary and a copy here would go stale.'),
            'categoriesUsed' => Schema::listOf(Schema::string(), 'The categories the category word resolved to, in the tracker\'s own spelling. Empty where none was asked for. Empty too where the word matched none, which is answered as no issues and is a statement about the word rather than about the backlog.'),
            'people' => Schema::listOf(Schema::object([
                'filter' => ['type' => 'string', 'enum' => ['reportedBy', 'assignedTo', 'involving'], 'description' => 'Which argument the entry answers for.'],
                'asked' => Schema::string('The name that was passed, as it was passed.'),
                'name' => Schema::string('The person it resolved to, in the tracker\'s own spelling. Empty where it resolved to nobody, which is answered as no issues and is a statement about the name rather than about the backlog.'),
                'id' => Schema::integer('The tracker\'s own user id, which is what it filters by and the only thing it takes. Zero where the name resolved to nobody.'),
                'candidates' => Schema::listOf(Schema::string(), 'The people the name could have meant, where it reached more than one. A name reaching two resolves to neither and nothing is read, because merging two people into one backlog is a wrong answer nothing says is wrong. Ask again with one of these. Empty where the name resolved, and where nothing here carries it — a name this server cannot place rather than a person who has filed nothing.'),
            ], ['filter', 'asked', 'name', 'id', 'candidates']), 'What reportedBy, assignedTo and involving resolved to, one entry per name the call carried, in that order. A name is resolved against the core project\'s members and, where they hold no membership, against the people the issues carrying that name were filed by or handed to. Empty where no name was passed.'),
            'breakdown' => [
                'type' => ['object', 'null'],
                'description' => 'How the matched set is distributed, where breakdown was asked for. Null otherwise, and null where nothing matched.',
                'properties' => [
                    'read' => Schema::integer('How many issues the counts are over. Equal to total where the whole set was read.'),
                    'complete' => ['type' => 'boolean', 'description' => 'Whether that is the whole matched set. False where the bound cut the read. Then the counts are over the first issues read in the order asked for, the oldest or the longest untouched. That is a shape of that end and not of the set. Narrow the filters for the whole one.'],
                    'counts' => Schema::listOf(Schema::object([
                        'dimension' => ['type' => 'string', 'enum' => ['status', 'tracker', 'category', 'year'], 'description' => 'What the issues are counted by. year is the year they were filed in.'],
                        'buckets' => Schema::listOf(Schema::object([
                            'name' => Schema::string('The value, or "none" for the issues that carry none. An issue filed under no area is a bucket rather than a row left out, so the buckets add up to read.'),
                            'count' => Schema::integer(),
                        ], ['name', 'count']), 'The largest buckets first, and by name where two are the same size.'),
                        'withheldBuckets' => Schema::integer('How many further buckets this dimension has, zero where it has none. The tail of an area count is subsystems holding one issue each.'),
                        'withheldCount' => Schema::integer('How many issues those hold together, so the listed buckets and this add up to read.'),
                    ], ['dimension', 'buckets', 'withheldBuckets', 'withheldCount']), 'One entry per dimension, always the four.'),
                ],
                'required' => ['read', 'complete', 'counts'],
            ],
            'issue' => [
                'type' => ['object', 'null'],
                'description' => 'The issue, where status says answered and a number was asked for. Null otherwise.',
                'properties' => [
                    'id' => Schema::integer(),
                    'subject' => Schema::string(),
                    'status' => Schema::string('New, Accepted, Resolved, Closed, Rejected — the tracker\'s own word.'),
                    'tracker' => Schema::string('Bug, Feature, Task, Epic.'),
                    'priority' => Schema::string(),
                    'assignedTo' => Schema::string('Who the tracker says holds this, empty where nobody does. An assignee is not a promise that somebody is working on it — on an issue nothing has moved on for years it is usually who last did.'),
                    'targetVersion' => Schema::string('The release it is scheduled for, empty where none is set.'),
                    'typo3Version' => Schema::string('The TYPO3 version it was reported against, which is not the version it still reproduces on.'),
                    'phpVersion' => Schema::string(),
                    'createdOn' => Schema::string(),
                    'updatedOn' => Schema::string(),
                    'url' => Schema::string('Where a person reads it.'),
                    'description' => Schema::string('The report as it was written, which is what the reporter saw and not what was decided.'),
                    'relations' => Schema::listOf(self::relation(), 'Issues this one is filed against, which is where a duplicate, a blocker, and the issue a revert was filed under are named. Each carries its subject, so which of them is worth reading is decided from here rather than from one call each.'),
                    'mentioned' => Schema::listOf(Schema::object([
                        'issue' => Schema::integer('The issue the text cites.'),
                        'subject' => Schema::string('What it is about, so the claim is weighed without a call per number.'),
                        'tracker' => Schema::string('Bug, Feature, Task.'),
                        'status' => Schema::string('Where the cited issue stands, which is what says whether the prior art was dealt with.'),
                        'url' => Schema::string('Where a person reads it.'),
                        'where' => ['type' => 'string', 'enum' => ['description', 'note'], 'description' => 'Which text cites it. A number both of them carry is a description, which is where the reporter framed the report.'],
                    ], ['issue', 'subject', 'tracker', 'status', 'url', 'where']), 'The issues the description and the comments cite and no relation carries, written as #NNNN or as a URL. A relation is somebody\'s triage; this is the writer\'s own claim about prior art, which on an old report is regularly load-bearing and regularly wrong. Read it before a patch is framed against it, and never pass this issue over as a duplicate on its strength. Only a number the tracker answered for is here, which keeps a version out of it. Empty where the texts cite nothing and where every citation is already a relation.'),
                    'reviews' => Schema::listOf(Schema::object([
                        'change' => Schema::integer('The change number on review.typo3.org, which is what typo3_gerrit_lookup takes as change.'),
                        'changeId' => Schema::string('The Change-Id the commit message carries, empty where no note named one. typo3_gerrit_lookup takes this too, and it is what survives a rebase onto another branch.'),
                        'patchSet' => Schema::integer('The highest patch set a note mentioned, zero where none did. The review server may be further along.'),
                        'on' => Schema::string('When the last note naming this change was written, which is how old the reference is and not when the change last moved.'),
                        'url' => Schema::string('Where a person reads the change.'),
                        'status' => Schema::string('NEW while the change is open, MERGED once it landed, ABANDONED when it was given up — where it stood when the review server was asked. Empty where it was not asked or named no state, which includes every change only the prose names.'),
                    ], ['change', 'changeId', 'patchSet', 'on', 'url', 'status']), 'Every change on review.typo3.org this issue is known to have, joined from two sources. One is the handles the description and the journal name, lifted out of the prose. The other is the changes whose commit message names the issue number, asked of the review server. Neither half contains the other, so an empty list means neither source has one. A change the prose named carries the patch set and the date of that prose, while the state beside it is the review server\'s. What a reviewer objected to is the argument on the change, which is a typo3_gerrit_lookup call. An ABANDONED is grounds to read that argument rather than to pass the issue over.'),
                    'attachments' => Schema::listOf(self::attachment(), 'The files hanging off the issue. On a report about rendering these are usually screenshots and regularly where the evidence is: a comment made of !image.jpg! references reads as empty otherwise. Empty where the issue carries none.'),
                    'cites' => self::cites('Read from the subject, the description and every comment, which is where a reproduction regularly names the class the description never did.'),
                    'noteCount' => Schema::integer('How many comments the issue carries in total.'),
                    'botNoteCount' => Schema::integer('How many of those a review bot wrote, which notes: "people" is what drops. Answered whichever way notes was asked. A journal full of patch-set pings answering zero here is the list of bot names gone stale, not an issue nobody pushed a patch for.'),
                    'notes' => Schema::listOf(Schema::object([
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'note' => Schema::string(),
                    ], ['author', 'on', 'note']), 'The most recent comments, oldest first. A closure, a reassignment and a "we will not do this" are here rather than in the description.'),
                ],
                'required' => ['id', 'subject', 'status', 'tracker', 'priority', 'assignedTo', 'targetVersion', 'typo3Version', 'phpVersion', 'createdOn', 'updatedOn', 'url', 'description', 'relations', 'mentioned', 'attachments', 'reviews', 'cites', 'noteCount', 'botNoteCount', 'notes'],
            ],
            'results' => Schema::listOf(Schema::object([
                'issue' => Schema::integer('The issue number, which is what this tool reads whole.'),
                'subject' => Schema::string(),
                'tracker' => Schema::string('Bug, Feature, Task, Epic.'),
                'status' => Schema::string('Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.'),
                'category' => Schema::string('The area the core files it under, empty where none is set. A search hit is a title and carries none of the five fields below, so they are read for the whole page in one further call. Empty here can mean that call did not reach the tracker rather than that the issue has no area.'),
                'reportedBy' => Schema::string('Who filed it. This is the dimension reportedBy selects on, and reading it off a set answers who a backlog is being reported by without a call per row.'),
                'assignedTo' => Schema::string('Who the tracker says holds this, empty where nobody does. What it decides for a triage is whether the issue is free to take. On an old one it is usually who last touched it rather than who is on it.'),
                'createdOn' => Schema::string('When it was filed.'),
                'updatedOn' => Schema::string('When anything last moved on it, which is the measure of neglect rather than of age.'),
                'url' => Schema::string('Where a person reads it.'),
                'relations' => Schema::listOf(self::relation(), 'The issues this one is filed against, each with its subject, so a row that duplicates something already decided is seen without being read. Answered on an enumeration and empty on a search hit, where nothing asked for them.'),
                'attachments' => Schema::listOf(self::attachment(), 'The files hanging off the issue, which on a report about rendering are usually where the evidence is. A report whose evidence is a screenshot is a different candidate to one whose evidence is prose. Answered on an enumeration and empty on a search hit, where nothing asked for them.'),
                'cites' => self::cites('Read from the subject and the description, which is what the page carries. An enumerated row holds no comment, so a report that names its code only in one is answered here as citing nothing. Empty on a search hit, where it is not asked.'),
                'reviews' => Schema::listOf(Schema::object([
                    'change' => Schema::integer('The change number on review.typo3.org, which is what typo3_gerrit_lookup takes as change.'),
                    'status' => Schema::string('NEW while the change is open, MERGED once it landed, ABANDONED when it was given up — where it stood when the page was read. Empty where the review server named no state.'),
                    'url' => Schema::string('Where a person reads the change.'),
                ], ['change', 'status', 'url']), 'The changes whose commit message names this issue, asked of the review server in one query for the whole page, each with the state it is in. A state and not a verdict: what a reviewer objected to is the argument on the change, which is a typo3_gerrit_lookup call. An ABANDONED is grounds to read that argument rather than to pass the issue over — the approach can be the rejected part while the defect is real. Empty where nothing on the review server names the issue and where the review server did not answer, which this does not separate. Empty on a search hit too, where it is not asked.'),
            ], ['issue', 'subject', 'tracker', 'status', 'category', 'reportedBy', 'assignedTo', 'createdOn', 'updatedOn', 'url', 'relations', 'attachments', 'cites', 'reviews']), 'The issues the query matched or the enumeration selected, in the tracker\'s own order — nothing here ranks them. An enumerated row also carries its relations, its attachments and its reviews: the three that say it was answered elsewhere or already attempted, without reading it whole. Empty where an issue was read by number.'),
            'unavailable' => Schema::unavailable([
                'source-not-answering' => 'the tracker did not answer this time.',
                'source-not-parseable' => 'something answered with a page rather than with the API, which is what '
                    . 'the bot protection in front of it looks like from here.',
            ]),
        ], ['status', 'source', 'url', 'query', 'placedAgainst', 'total', 'terms', 'categories', 'categoriesUsed', 'people', 'breakdown', 'issue', 'results', 'unavailable']);
    }

    /**
     * One related issue, in the one shape both answers carry it in.
     *
     * A row of an enumeration and an issue read whole name the same thing, and
     * a caller that reads two shapes for it reads the second one wrong.
     *
     * @return array<string, mixed>
     */
    private static function relation(): array
    {
        return Schema::object([
            'issue' => Schema::integer('The other issue.'),
            'relation' => Schema::string('duplicates, relates, blocked, precedes.'),
            'subject' => Schema::string('What the other issue is about, so it can be judged without being read. Empty where the tracker did not answer the one call that fills the whole set.'),
            'tracker' => Schema::string('Bug, Feature, Task.'),
            'status' => Schema::string('Where the other issue stands.'),
            'url' => Schema::string('Where a person reads it.'),
        ], ['issue', 'relation', 'subject', 'tracker', 'status', 'url']);
    }

    /**
     * The code a report names, as the section an issue read whole carries it
     * in: one line per name, with where it was found.
     *
     * @param list<array<string, mixed>> $cites
     * @return list<string>
     */
    private static function citedSection(array $cites, string $version): array
    {
        if ($cites === []) {
            return [];
        }

        $lines = [
            '',
            sprintf('## Code this report names (%d)', count($cites)),
            'Where each of them stands in the packages this installation ships'
                . ($version !== '' ? ', at TYPO3 ' . $version : '')
                . '. That is where a symbol is and not whether the defect still reproduces: a report whose names are'
                . ' all gone is a candidate to drop, and one whose names all stand is a candidate to read. A name'
                . ' nothing here could place is unplaced rather than gone.' . self::placedNowhere(),
        ];
        foreach ($cites as $entry) {
            $lines[] = '- ' . implode(' · ', array_filter([
                self::citedName($entry),
                self::stands($entry),
                implode(', ', array_column($entry['in'], 'path')),
            ]));
        }

        return $lines;
    }

    /**
     * The same names on one line, which is what a row of an enumeration has
     * room for. The paths are in the data either way.
     *
     * @param list<array<string, mixed>> $cites
     */
    private static function citedLine(array $cites): string
    {
        $shown = array_slice($cites, 0, self::CITED_PER_ROW);

        return 'Cites: ' . implode(' · ', [
            ...array_map(
                static fn(array $entry): string => self::citedName($entry) . ' — ' . self::stands($entry),
                $shown,
            ),
            ...(count($cites) > count($shown) ? [sprintf('and %d more in the data', count($cites) - count($shown))] : []),
        ]);
    }

    /**
     * Why every name came back unplaced, where that is what happened.
     *
     * The states alone read as a statement about the code, and this one is
     * about the machine: a client started outside an installation has nothing
     * to place a name in and nothing about the list says so.
     */
    private static function placedNowhere(): string
    {
        return Instance::isAvailable()
            ? ''
            : ' No installation was found from here, so every name is unplaced: this is what the reports name and'
                . ' not what is still there.';
    }

    /** @param array<string, mixed> $entry */
    private static function citedName(array $entry): string
    {
        return $entry['name'] . ($entry['method'] !== '' ? '::' . $entry['method'] : '');
    }

    /**
     * What one cited name's state says, in the words a triage decides on.
     *
     * @param array<string, mixed> $entry
     */
    private static function stands(array $entry): string
    {
        $extensions = implode(' and ', array_unique(array_column($entry['in'], 'extension')));

        return match (true) {
            $entry['state'] === CitedCode::SHIPPED => 'shipped by ' . $extensions,
            $entry['state'] === CitedCode::UNPLACED => 'no installed package owns it',
            $entry['in'] !== [] => $extensions . ' ships the class and not the method',
            default => 'no installed package ships it',
        };
    }

    /**
     * The code a report names, in the one shape both answers carry it in.
     *
     * What a caller does with it is rank candidates, so the field says where a
     * symbol stands and stops there — `D-ANS-122`. The two ways it may not be
     * read are stated on the states themselves, because that is where a client
     * reads them.
     *
     * @return array<string, mixed>
     */
    private static function cites(string $read): array
    {
        return Schema::listOf(Schema::object([
            'name' => Schema::string('The class, or the path of the file, as the report names it: a namespace without its leading backslash, and a path from typo3/sysext/.'),
            'kind' => [
                'type' => 'string',
                'enum' => [CitedCode::QUALIFIED, CitedCode::UNQUALIFIED, CitedCode::FILE],
                'description' => 'How the report named it. "qualified" is a class with its namespace, which places it without guessing. "unqualified" is a bare class name, placed by the name of its file. It can land on a package the report was never about, and one matching two packages names both. A bare name is taken only where the report marks it as code or an installed package ships one under it. A capitalised word is as often the label of a button. "file" is a path in the core tree, as a pasted stack trace writes it.',
            ],
            'method' => Schema::string('The method the report names on that class, empty where it names none. A ::class and a ::CONSTANT are not one and are answered as the class alone.'),
            'state' => [
                'type' => 'string',
                'enum' => [CitedCode::SHIPPED, CitedCode::NOT_SHIPPED, CitedCode::UNPLACED],
                'description' => '"shipped" is a name an installed package carries, the method included where one was named. "notShipped" is a name nothing installed carries: core having removed it and an extension you never installed look the same, and the report does not tell the two apart. Where in is filled it means the class stands and the method named on it does not. "unplaced" is a name this could not place at all: no installed package owns the namespace, or there is no installation to read here. Never read unplaced as gone.',
            ],
            'in' => Schema::listOf(Schema::object([
                'extension' => Schema::string('The extension key of the package that carries it.'),
                'path' => Schema::string('Where the file sits, from the installation root, so it is opened without being searched for.'),
            ], ['extension', 'path']), 'Where it was found, one entry per package carrying it — several where a bare name matches more than one, and picking one of those is the caller\'s. Empty where it was not found and where nothing could place it.'),
        ], ['name', 'kind', 'method', 'state', 'in']), 'The classes, methods and core files this report names, each with where it stands in the packages this installation ships. A stale issue\'s status is untouched by definition, so this is what says a 2015 report is about code rewritten since. Read it before opening the checkout, as where a symbol is rather than whether the defect reproduces. ' . $read . ' Empty where the text names none, which is the ordinary case for a report about a TCA key, a TypoScript path or a table column.');
    }

    /**
     * One file hanging off an issue, in the one shape both answers carry it in.
     *
     * @return array<string, mixed>
     */
    private static function attachment(): array
    {
        return Schema::object([
            'filename' => Schema::string('The name the file was uploaded under, which is also how a comment refers to it: Redmine writes an inline image as !name.png! and says nothing else about it.'),
            'contentType' => Schema::string('image/png, image/jpeg, text/plain.'),
            'size' => Schema::integer('Bytes.'),
            'on' => Schema::string('When it was uploaded, which is what says which comment it belongs to.'),
            'url' => Schema::string('Where the file itself is. It answers without a credential, and reading it is the caller\'s: nothing here fetches or transcribes one.'),
        ], ['filename', 'contentType', 'size', 'on', 'url']);
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $query = is_string($args['query'] ?? null) ? trim($args['query']) : '';
        $open = is_string($args['open'] ?? null) ? trim($args['open']) : '';
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 15;
        $reportedBy = is_string($args['reportedBy'] ?? null) ? trim($args['reportedBy']) : '';
        $assignedTo = is_string($args['assignedTo'] ?? null) ? trim($args['assignedTo']) : '';
        $involving = is_string($args['involving'] ?? null) ? trim($args['involving']) : '';

        if ($issue !== '') {
            return self::read($issue, is_string($args['notes'] ?? null) ? trim($args['notes']) : 'all');
        }
        // A person filter is a narrowing of the enumeration and the schema says
        // so. Passing one without `open` is a call no schema allows, and what a
        // client that validates nothing would otherwise reach is a search for
        // the empty string rather than the question it plainly asked.
        if ($open !== '' || $reportedBy !== '' || $assignedTo !== '' || $involving !== '') {
            return self::enumerated(
                $open !== '' ? $open : 'oldest',
                is_string($args['tracker'] ?? null) ? trim($args['tracker']) : '',
                is_string($args['category'] ?? null) ? trim($args['category']) : '',
                is_string($args['createdBefore'] ?? null) ? trim($args['createdBefore']) : '',
                is_string($args['createdSince'] ?? null) ? trim($args['createdSince']) : '',
                is_string($args['updatedBefore'] ?? null) ? trim($args['updatedBefore']) : '',
                $limit,
                is_string($args['status'] ?? null) ? trim($args['status']) : 'open',
                $reportedBy,
                $assignedTo,
                $involving,
                ($args['breakdown'] ?? false) === true,
            );
        }

        return self::searched($query, $limit);
    }

    /** One issue, whole, which is what a number is asked for. */
    private static function read(string $issue, string $notes): ToolResult
    {
        $answer = (new Forge())->issue($issue, $notes);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => '',
            'placedAgainst' => Instance::typo3Version() ?? '',
            'total' => 0,
            'terms' => [],
            'categories' => [],
            'categoriesUsed' => [],
            'people' => [],
            'breakdown' => null,
            'issue' => $answer['issue'],
            'results' => [],
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create('TYPO3 issue tracker: ' . $answer['url'] . "\nCould not answer: " . $data['unavailable']['reason'], $data);
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create('TYPO3 issue tracker: no issue ' . $issue . ' at ' . Forge::HOST . '.', $data);
        }

        $found = $answer['issue'];
        $lines = [
            sprintf('#%d %s', $found['id'], $found['subject']),
            sprintf('%s · %s · priority %s · %s', $found['tracker'], $found['status'], $found['priority'], $found['url']),
        ];
        $lines[] = $found['assignedTo'] !== ''
            ? 'Assigned to ' . $found['assignedTo'] . ' — which says who holds it and not that somebody is working on it.'
            : 'Assigned to nobody.';
        if ($found['targetVersion'] !== '') {
            $lines[] = 'Target version: ' . $found['targetVersion'];
        }
        if ($found['typo3Version'] !== '') {
            $lines[] = 'Reported against TYPO3 ' . $found['typo3Version']
                . ($found['phpVersion'] !== '' ? ', PHP ' . $found['phpVersion'] : '')
                . ' — which is what the reporter had, not what it still reproduces on.';
        }
        foreach ($found['relations'] as $relation) {
            $lines[] = self::relationLine($relation);
        }
        // Beside the relations and on every issue that has one, because a
        // citation says the same kind of thing — and where `relations` is empty
        // this is the whole of what the answer had to say about prior art,
        // which is the sentence `D-ANS-123` was written about.
        if ($found['mentioned'] !== []) {
            $lines[] = 'Cited in the text below and filed as no relation, so this is the writer\'s own claim about'
                . ' prior art rather than somebody\'s triage. It is regularly wrong: read it before framing a patch'
                . ' against it, and never pass this issue over as a duplicate of it.';
            foreach ($found['mentioned'] as $mention) {
                $lines[] = sprintf(
                    'Mentioned in the %s: #%d — %s',
                    $mention['where'],
                    $mention['issue'],
                    implode(' · ', array_filter([$mention['tracker'], $mention['status'], $mention['subject'], $mention['url']])),
                );
            }
        }
        $lines[] = '';
        $lines[] = '## Reported';
        $lines[] = $found['description'];
        array_push($lines, ...self::citedSection($found['cites'], $data['placedAgainst']));
        if ($found['reviews'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Changes on review.typo3.org (%d)', count($found['reviews']));
            $lines[] = 'Two sources joined: the handles the report and the comments name, and the changes whose commit'
                . ' message names this issue, asked of the review server. Neither half contains the other, so this'
                . ' list being empty is what says no change is in flight. A patch set and a date are what the prose'
                . ' said the day it was written; the state beside them is where the change stood just now. What a'
                . ' reviewer objected to is the argument on the change — pass the number as change to'
                . ' typo3_gerrit_lookup, or the Change-Id, which is what survives a rebase onto another branch.';
            foreach ($found['reviews'] as $review) {
                $lines[] = '- ' . implode(' · ', array_filter([
                    $review['change'] > 0 ? 'change ' . $review['change'] : '',
                    $review['status'],
                    $review['patchSet'] > 0 ? 'patch set ' . $review['patchSet'] : '',
                    $review['changeId'],
                    $review['on'] !== '' ? 'named ' . substr($review['on'], 0, 10) : '',
                    $review['url'],
                ]));
            }
        }
        if ($found['attachments'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Attachments (%d)', count($found['attachments']));
            $lines[] = 'On a report about rendering these are usually where the evidence is, and Redmine writes an'
                . ' inline image into a comment as !filename! — so a comment below that is nothing but a filename is'
                . ' referring to one of these. Read the ones the report turns on; this server does not fetch them.';
            foreach ($found['attachments'] as $file) {
                $lines[] = sprintf(
                    '- %s · %s · %s',
                    $file['filename'],
                    implode(' · ', array_filter([
                        $file['contentType'],
                        $file['size'] > 0 ? sprintf('%.0f kB', $file['size'] / 1000) : '',
                        $file['on'] !== '' ? substr($file['on'], 0, 10) : '',
                    ])),
                    $file['url'],
                );
            }
        }
        if ($found['notes'] !== []) {
            $lines[] = '';
            $lines[] = sprintf('## Comments (%d of %d, oldest first)', count($found['notes']), $found['noteCount']);
            $lines[] = 'What was decided is here rather than above.';
            if ($notes === 'people') {
                $lines[] = sprintf(
                    '%d of them a review bot wrote and they were dropped. The changes they named are above. Ask for'
                        . ' notes "all" to read them; a count of 0 on an issue with patch-set pings means this filter'
                        . ' does not know the bot that wrote them.',
                    $found['botNoteCount'],
                );
            }
            foreach ($found['notes'] as $note) {
                $lines[] = '';
                $lines[] = sprintf('**%s**, %s', $note['author'], $note['on']);
                $lines[] = $note['note'];
            }
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * One relation, with the subject that decides whether to read it and the
     * URL that reaches it — so which of them is worth an issue read is settled
     * here rather than by reading all of them, and the number a reader repeats
     * carries where it points (`D-ANS-103`). Separated from the two answers it
     * is printed in so it can be held without a tracker.
     *
     * @param array<string, mixed> $relation
     */
    public static function relationLine(array $relation): string
    {
        return sprintf(
            'Relation: %s #%d — %s',
            $relation['relation'],
            $relation['issue'],
            implode(' · ', array_filter([$relation['tracker'], $relation['status'], $relation['subject'], $relation['url']])),
        );
    }

    /**
     * The issues a set of words matches, with what a caller has to know about
     * the set: these words found it, and other words find other issues.
     *
     * An empty answer is where that matters most. A report worded differently
     * is invisible to a word match, so nothing matching is a statement about
     * the query and never about whether the bug was reported — `D-ANS-038`
     * names reading it the other way as the failure this is written against.
     *
     * What it offers is the other way in and not another wording. A session
     * that read a rewording went round eight times and was settled by the
     * enumeration on its ninth call, so `open` is named here as a call to
     * compose (`R-ANS-006`). Which end of it is `D-ANS-116`: a duplicate of a
     * defect somebody has just found is among the newest issues, and what
     * bounds that end to a set is a day to count from rather than an area,
     * which an issue filed under no Category is in none of.
     *
     * Which of the caller's own words emptied it is the one thing no advice
     * here can supply, so it is read from the tracker rather than guessed at.
     */
    private static function searched(string $query, int $limit): ToolResult
    {
        $answer = (new Forge())->search($query, $limit);

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => $answer['query'],
            'placedAgainst' => '',
            'total' => $answer['total'],
            'terms' => $answer['terms'],
            'categories' => [],
            'categoriesUsed' => [],
            'people' => [],
            'breakdown' => null,
            'issue' => null,
            'results' => $answer['results'],
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
        ];

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create(
                'TYPO3 issue tracker, searched for "' . $answer['query'] . "\"\nCould not answer: " . $data['unavailable']['reason'],
                $data,
            );
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create(implode("\n", array_merge(
                [
                    'TYPO3 issue tracker: no issue matches "' . $answer['query'] . '" at ' . Forge::HOST . '.',
                    'These words matched nothing, which is not that nobody reported it: an issue worded differently is '
                        . 'invisible to a full-text search.',
                    'Every word has to be in the same issue, so one word nobody wrote empties the answer whatever '
                        . 'else is in it.',
                ],
                self::reached($answer['terms']),
                [
                    'What no wording of the report reaches is enumerated instead: open "newest" with createdSince from '
                        . 'the day the defect could first have been reported, and limit 50. Add category in your own '
                        . 'words — "import export", "rte" — only where the area is certain: thousands of the open bugs '
                        . 'carry no Category at all, and an area filter reaches none of them.',
                    'Those subjects settle whether somebody already reported this where total and the rows agree, and '
                        . 'are the recent end of a larger set where they do not — narrow the window until they do.',
                    'Where the words are a person, pass them as reportedBy or assignedTo with open.',
                ],
            )), $data);
        }

        $lines = [
            sprintf('TYPO3 issue tracker: %d issues match "%s"', count($answer['results']), $answer['query']),
            'A full-text match over subject, description and comments, in the tracker\'s own order and unranked.'
                . ' Another wording finds another set, so this is which issues mention it rather than which one it'
                . ' duplicates. Read one whole by passing its number as issue.',
            'Where those words are a person\'s name, this is the issues that mention them and not the issues that are'
                . ' theirs: pass the name as reportedBy or assignedTo with open for that, which is a different set and'
                . ' regularly two orders of magnitude larger.',
        ];
        foreach ($answer['results'] as $hit) {
            $lines[] = '';
            $lines[] = sprintf('## #%d %s', $hit['issue'], $hit['subject']);
            // The tracker and the status where the title carried them, which is
            // every hit the tracker words as it words its own; the area, the
            // assignee and the two dates where the fields behind them were
            // reachable.
            $lines[] = implode(' · ', array_filter([
                $hit['tracker'],
                $hit['status'],
                $hit['category'],
                $hit['reportedBy'] !== '' ? 'filed by ' . $hit['reportedBy'] : '',
                $hit['assignedTo'] !== '' ? 'assigned to ' . $hit['assignedTo'] : '',
                $hit['createdOn'] !== '' ? 'filed ' . substr($hit['createdOn'], 0, 10) : '',
                $hit['updatedOn'] !== '' ? 'last touched ' . substr($hit['updatedOn'], 0, 10) : '',
                $hit['url'],
            ]));
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * What each word reached on its own, which is what says which of them
     * emptied the answer.
     *
     * The generic advice cannot: it names a class as the kind of term that
     * empties a query, and on the query this was written from one class name
     * reached five issues while the other reached none (`D-ANS-038`). So the
     * counts lead and the advice is what is left where none were read.
     *
     * @param list<array{word: string, total: int}> $terms
     * @return list<string>
     */
    private static function reached(array $terms): array
    {
        if ($terms === []) {
            return ['Ask again with the two or three words that name the subject: a term nobody would have written — '
                . 'a method name, a class — is regularly the one that emptied it.'];
        }

        $absent = [];
        $narrowest = null;
        foreach ($terms as $term) {
            if ($term['total'] === 0) {
                $absent[] = '"' . $term['word'] . '"';
            } elseif ($narrowest === null || $term['total'] < $narrowest['total']) {
                $narrowest = $term;
            }
        }

        $lines = [sprintf('Asked one word at a time: %s.', implode(' · ', array_map(
            static fn(array $term): string => sprintf('"%s" reaches %d', $term['word'], $term['total']),
            $terms,
        )))];
        if ($absent !== []) {
            $lines[] = sprintf(
                'No issue on the tracker carries %s. A query %s is in is empty whatever else is in it, so drop %s.',
                implode(' or ', $absent),
                count($absent) === 1 ? 'it' : 'one of them',
                count($absent) === 1 ? 'it' : 'them',
            );
        }
        if ($narrowest !== null) {
            $lines[] = sprintf(
                '"%s" is the narrowest of the rest and reaches something: ask it on its own, then read the subjects.',
                $narrowest['word'],
            );
        }

        return $lines;
    }

    /**
     * The workflow a caller holding a page of the backlog is in, and the
     * readings that decide a row.
     *
     * `D-SKL-038` is the placement, in the shape `GerritLookup::workflow()`
     * took, and `D-SKL-031` the five readings. The two calls named with them are
     * the ones `feedback/2026-08-24-173116` says it never made, having chosen
     * ten candidates itself and found four of them already fixed. The `issue`
     * form takes none of this, and a breakdown returns above it holding no
     * candidates. Separated from `answer()` so it can be held without a tracker.
     *
     * The recent end takes none of it either. A caller who ordered by `newest`
     * is asking whether a defect is filed already, and the first thing this
     * says is that choosing from the page is somebody else's — which is the one
     * step that question cannot hand over (`D-ANS-116`).
     */
    public static function workflow(string $status, string $order): ?string
    {
        if ($status !== 'answered' || !in_array(trim($order), ['oldest', 'stale'], true)) {
            return null;
        }

        return "## What a page of the backlog opens\n"
            . '`typo3-core-issue-triage` is the workflow a caller holding this page is in, and opening it comes '
            . 'before deciding anything about a row. Hand the page over rather than choosing from it: triaging a '
            . 'backlog and triaging one issue are two jobs, and the second takes a number. Where the choice is '
            . "yours, read these in order and stop at the first that decides:\n"
            . '- What has already happened to it. The row carries the change and the state it stands in; the '
            . 'reading is the argument under that state. `typo3_gerrit_lookup` answers it by the number, before '
            . "the checkout is opened, and an ABANDONED is grounds to read it rather than to pass the row over.\n"
            . '- Whether the code it names is still there. The row carries every class, method and core file its text '
            . 'cites with where each stands in the packages installed here, so a report whose names are all gone is '
            . "settled without opening the checkout. A name it could not place decides nothing.\n"
            . '- The category, against the branch you are standing on. One naming a subsystem the branch no longer '
            . "ships settles the issue unread.\n"
            . '- Where the symptom appears. A rendered fragment, a stored row or a resolved value needs no '
            . "installation; one that shows only after a backend interaction needs one standing up.\n"
            . '- How far the mechanism reaches. One class and the behaviour in it is the settleable shape, and '
            . "several with the order between them is an interaction.\n"
            . '- What the suite already models. A case added to a test file that exists is a reproduction with no '
            . "fixture to build.\n"
            . '`typo3_changelog_lookup` is what says whether the area was reworked since, which is what turns a valid '
            . 'report into one about code that is gone. Say which reading decided, and say of the rows you passed '
            . 'over that you passed over them.';
    }

    /**
     * The shape of a set rather than a page of it, which for a person is the
     * answer and not a summary of one.
     *
     * A page of 50 out of 621 leaves the rest reachable by nothing: there are
     * no other words to narrow a person's history by, and every filter that
     * would make it fit answers a smaller question than the one asked
     * (`feedback/2026-08-19-134651`).
     *
     * @param array<string, mixed> $breakdown
     */
    private static function shaped(array $breakdown, int $total, string $selection): string
    {
        $lines = [
            sprintf('TYPO3 issue tracker: %d %s', $total, $selection),
            $breakdown['complete']
                ? sprintf('Counted over all %d of them, as the shape of the set rather than its rows.', $breakdown['read'])
                : sprintf(
                    'Counted over %d of %d — the read stops there. That is one end of the set and not a shape of the'
                        . ' whole of it, so narrow the filters before reading anything off the proportions.',
                    $breakdown['read'],
                    $total,
                ),
            'Ask again without breakdown, narrowed to the part this points at, for the issues themselves.',
        ];
        foreach ($breakdown['counts'] as $counted) {
            $named = implode(' · ', array_map(
                static fn(array $bucket): string => $bucket['name'] . ' ' . $bucket['count'],
                $counted['buckets'],
            ));
            $lines[] = sprintf(
                '%s: %s%s',
                match ($counted['dimension']) {
                    'status' => 'Status',
                    'tracker' => 'Tracker',
                    'category' => 'Area',
                    default => 'Filed in',
                },
                $named !== '' ? $named : 'nothing',
                $counted['withheldBuckets'] > 0
                    ? sprintf(' · and %d more holding %d', $counted['withheldBuckets'], $counted['withheldCount'])
                    : '',
            );
        }

        return implode("\n", $lines);
    }

    /**
     * How the selection names the person one of the two filters resolved to.
     *
     * @param list<array<string, mixed>> $people
     */
    private static function filedBy(array $people, string $filter): string
    {
        foreach ($people as $person) {
            if ($person['filter'] === $filter && $person['name'] !== '') {
                return match ($filter) {
                    'reportedBy' => 'filed by ',
                    'assignedTo' => 'assigned to ',
                    default => 'filed by or assigned to ',
                } . $person['name'];
            }
        }

        return '';
    }

    /**
     * The issues of the core project, ordered by the thing that was asked about
     * them.
     *
     * What this owes a caller beyond the entries is the size of what it is
     * looking at. A backlog answers a filter with thousands, and a page of
     * thirty read as the set is a triage that believes it has seen the problem.
     * So the count of everything that matched leads, and where it is larger
     * than the page the answer says which way to make it smaller: a narrower
     * filter and not a larger limit, because the order is the tracker's and
     * more of it is more of the same end.
     */
    private static function enumerated(
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
    ): ToolResult {
        $answer = (new Forge())->open(
            order: $order,
            tracker: $tracker,
            category: $category,
            createdBefore: $createdBefore,
            createdSince: $createdSince,
            updatedBefore: $updatedBefore,
            limit: $limit,
            status: $status,
            reportedBy: $reportedBy,
            assignedTo: $assignedTo,
            involving: $involving,
            breakdown: $breakdown,
        );

        $data = [
            'status' => $answer['status'],
            'source' => Forge::HOST,
            'url' => $answer['url'],
            'query' => '',
            'placedAgainst' => Instance::typo3Version() ?? '',
            'total' => $answer['total'],
            'terms' => [],
            'categories' => $answer['categories'],
            'categoriesUsed' => $answer['categoriesUsed'],
            'people' => $answer['people'],
            'breakdown' => $answer['breakdown'],
            'issue' => null,
            'results' => $answer['results'],
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
        ];

        $narrowed = implode(', ', array_filter([
            $tracker !== '' ? 'tracker ' . $tracker : '',
            $answer['categoriesUsed'] !== [] ? 'in ' . implode(' and ', $answer['categoriesUsed']) : '',
            self::filedBy($answer['people'], 'reportedBy'),
            self::filedBy($answer['people'], 'assignedTo'),
            self::filedBy($answer['people'], 'involving'),
            $createdSince !== '' ? 'filed since ' . $createdSince : '',
            $createdBefore !== '' ? 'filed before ' . $createdBefore : '',
            $updatedBefore !== '' ? 'untouched since ' . $updatedBefore : '',
        ]));
        $selection = match ($status) {
            'closed' => 'closed issues of the TYPO3 Core project',
            'all' => 'issues of the TYPO3 Core project whatever their status',
            default => 'open issues of the TYPO3 Core project',
        }
        . ($narrowed !== '' ? ', ' . $narrowed : '')
        . ', ' . match ($order) {
            'stale' => 'longest untouched first',
            'newest' => 'newest filed first',
            default => 'oldest filed first',
        };

        if ($answer['status'] === 'unavailable') {
            return ToolResult::create(
                'TYPO3 issue tracker, ' . $selection . "\nCould not answer: " . $data['unavailable']['reason'],
                $data,
            );
        }
        // The areas asked for rather than corrected into. What a report being
        // filed by hand needs is the tracker's own spelling, and until this the
        // only way to it was a word wrong enough to fail (`D-KNW-113`).
        if ($category === Forge::EVERY_AREA) {
            return ToolResult::create(
                'TYPO3 issue tracker: the ' . count($answer['categories']) . ' areas the core files its issues'
                . " under, read from the project itself. No issue was read.\n"
                . 'The spelling here is what the Category field of a new issue takes, and passing one of these as '
                . "category enumerates what stands open in it.\n"
                . 'An area is where an issue was filed and not everything it is about, so a report about one subject '
                . "regularly sits under another.\n"
                . implode(', ', $answer['categories']),
                $data,
            );
        }
        // A word that named no category is a different answer to a filter that
        // excluded everything, and reading it as an empty backlog is the one
        // mistake this path can make.
        if ($category !== '' && $answer['categoriesUsed'] === []) {
            return ToolResult::create(
                'TYPO3 issue tracker: "' . $category . '" names no area the core files issues under, so nothing was'
                . " read. That is about the word and not about the backlog.\nThe areas are: "
                . implode(', ', $answer['categories']),
                $data,
            );
        }
        // A name that named nobody is a different answer to a filter that
        // excluded everything, the same way a word naming no area is — and the
        // set it would otherwise be answered with is the backlog of everybody.
        foreach ($answer['people'] as $person) {
            if ($person['id'] > 0) {
                continue;
            }

            return ToolResult::create(
                'TYPO3 issue tracker: "' . $person['asked'] . '" as ' . $person['filter'] . ' names '
                . ($person['candidates'] === [] ? 'nobody this server can place' : 'more than one person')
                . ", so nothing was read. That is about the name and not about the backlog.\n"
                . ($person['candidates'] === []
                    ? 'A name is resolved against the core project\'s members, and against the people the issues'
                        . ' carrying that name were filed by or handed to. Somebody who is neither is not reachable'
                        . ' by name here: read one issue of theirs and take the person off it.'
                    : 'Ask again with one of: ' . implode(', ', $person['candidates'])),
                $data,
            );
        }
        if ($answer['status'] === 'empty') {
            return ToolResult::create(
                'TYPO3 issue tracker: nothing matches ' . $selection . ".\n"
                . 'The filters excluded everything, which is an answer about them and not about the backlog. Widen the '
                . 'dates, drop the tracker, or reach the closed ones with status.',
                $data,
            );
        }

        if ($answer['breakdown'] !== null) {
            return ToolResult::create(self::shaped($answer['breakdown'], $answer['total'], $selection), $data);
        }

        $shown = count($answer['results']);
        $lines = [
            sprintf('TYPO3 issue tracker: %d of %d %s', $shown, $answer['total'], $selection),
        ];
        if ($shown >= $answer['total']) {
            $lines[] = 'That is the whole set on these filters.';
        } elseif ($answer['people'] !== []) {
            // A backlog is narrowed by other words and a person's history is
            // not: every filter that would make it fit answers a smaller
            // question than the one asked (`feedback/2026-08-19-134651`).
            $lines[] = 'This is a page and not the set, and limit stops at 50. What reaches the rest is breakdown, which'
                . ' answers how the whole set is distributed — there are no other words to narrow a person by, and a'
                . ' tracker or a date answers a smaller question than the one asked.';
        } elseif ($order === 'newest') {
            // The narrowing that reaches the rest of this end is a later day
            // and never an earlier one, which is what the sentence below would
            // have said (`D-ANS-116`).
            $lines[] = 'This is a page and not the set, and what it leaves out is older than its last row. A question'
                . ' about whether something has been reported is settled by a whole window rather than by more of this'
                . ' page: pass createdSince from the day the defect could first have been reported — a later day where'
                . ' you passed one already — until total and the rows agree, and then the subjects here are every issue'
                . ' filed since that day.';
        } else {
            $lines[] = 'This is a page and not the set. What comes after it is reached by a narrower filter — an earlier'
                . ' date, one tracker — rather than by a larger limit, because the order is the tracker\'s own and more of'
                . ' it is more of the same end. breakdown answers how the whole of it is distributed.';
        }
        $lines[] = $order === 'newest'
            ? 'A subject that reads like yours is a candidate and never a duplicate: read it whole by passing its'
                . ' number as issue, and what it actually reports is decided there. Nothing here decides that for you.'
            : 'Age is a candidate and never a finding: read one whole by passing its number as issue, and what it'
                . ' still claims is established in the checkout rather than off this list.';
        $lines[] = 'A row carries what the page came back with: the issues it is filed against, the files hanging off'
            . ' it, and the changes on review.typo3.org whose commit message names it, each with the state it is in.'
            . ' That state is where a change stands and not a verdict on the issue: an ABANDONED one is grounds to read'
            . ' the argument on it with typo3_gerrit_lookup, where the objection was written down and is regularly to'
            . ' the approach rather than to the defect. A row with no such line is one nothing there names — or one the'
            . ' review server did not answer for, which this list does not separate.';
        // Only where a row names something. A page whose reports are all about
        // a TCA key or a TypoScript path has no such line to read.
        if (array_filter(array_column($answer['results'], 'cites')) !== []) {
            $lines[] = 'A row that names code carries it: the classes, methods and core files its own text cites,'
                . ' each with whether the packages installed here still carry it'
                . ($data['placedAgainst'] !== '' ? ', at TYPO3 ' . $data['placedAgainst'] : '')
                . '. A report whose names are all gone is a candidate to drop without opening the checkout, and one'
                . ' whose names all stand is a candidate to read. It is read from the subject and the description,'
                . ' because the page carries no comment, and a name it could not place is unplaced rather than gone.'
                . self::placedNowhere();
        }
        if ($answer['categoriesUsed'] !== []) {
            // Where the reporter filed it, which is not everything about the
            // subject: three of the RTE reports a session went looking for on
            // 2026-08-05 sat under System/Bootstrap/Configuration and under
            // Link Handling.
            $lines[] = 'An area is where an issue was filed and not everything it is about. A report about this one'
                . ' regularly sits under another area, so what came back is a floor rather than the set — query the'
                . ' words as well where the question is about a subject. An issue carrying no Category at all is in no'
                . ' area, and thousands of the open bugs carry none, so no wording of this reaches one: ask again'
                . ' without category, narrowed by createdSince instead, where the question is whether it was reported.';
        }
        foreach ($answer['results'] as $entry) {
            $lines[] = '';
            $lines[] = sprintf('## #%d %s', $entry['issue'], $entry['subject']);
            $lines[] = implode(' · ', array_filter([
                $entry['tracker'],
                $entry['status'],
                $entry['category'],
                $entry['reportedBy'] !== '' ? 'filed by ' . $entry['reportedBy'] : '',
                $entry['assignedTo'] !== '' ? 'assigned to ' . $entry['assignedTo'] : 'unassigned',
                $entry['createdOn'] !== '' ? 'filed ' . substr($entry['createdOn'], 0, 10) : '',
                $entry['updatedOn'] !== '' ? 'last touched ' . substr($entry['updatedOn'], 0, 10) : '',
                $entry['url'],
            ]));
            foreach ($entry['relations'] as $relation) {
                $lines[] = self::relationLine($relation);
            }
            if ($entry['attachments'] !== []) {
                // Named and not linked: the files are read after the issue is,
                // and a page of thirty rows is read to choose which one that is.
                $lines[] = sprintf(
                    'Files (%d): %s',
                    count($entry['attachments']),
                    implode(', ', array_column($entry['attachments'], 'filename')),
                );
            }
            if ($entry['cites'] !== []) {
                $lines[] = self::citedLine($entry['cites']);
            }
            foreach ($entry['reviews'] as $review) {
                $lines[] = 'Review: ' . implode(' · ', array_filter([
                    'change ' . $review['change'],
                    $review['status'],
                    $review['url'],
                ]));
            }
        }
        // Under the rows and nowhere else. A breakdown returned above holds no
        // candidates, and the paths that answered nothing have no row to decide.
        $workflow = self::workflow($answer['status'], $order);
        if ($workflow !== null) {
            $lines[] = '';
            $lines[] = $workflow;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }
}
