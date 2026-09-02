<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Tool;

use TYPO3\DevCompanion\Contribution\Forge;
use TYPO3\DevCompanion\Contribution\Gerrit;
use TYPO3\DevCompanion\Knowledge\ReleaseLines;
use TYPO3\DevCompanion\Result\Schema;
use TYPO3\DevCompanion\Result\ToolResult;
use TYPO3\DevCompanion\Result\Unreachable;

/**
 * Whether a core patch already exists, read from the review server.
 *
 * The question is asked before every core task and answerable from no checkout,
 * and the sessions that asked it paid a round trip for the search and another
 * for the XSSI prefix the response opens with. This is one call
 * (`D-FBK-027`).
 */
final class GerritLookup extends ReadOnlyTool
{
    /** The change is read from the review server at review.typo3.org. */
    protected const OPEN_WORLD = true;

    /** Why nothing was answered, in the caller's terms rather than the transport's. */
    private const UNREACHABLE = [
        Unreachable::NOT_ANSWERING => 'The review server did not answer. It is reachable at ' . Gerrit::HOST
            . ' in a browser; nothing here can answer this question offline.',
        Unreachable::NOT_PARSEABLE => 'The host answered with something that is not the review API, which is what a '
            . 'proxy or a login page looks like from here.',
    ];

    public static function name(): string
    {
        return 'typo3_gerrit_lookup';
    }

    /** @return array<int, Source> */
    public static function answersFrom(): array
    {
        return [Source::Network];
    }

    public static function description(): string
    {
        return 'Whether a TYPO3 core patch already exists and what state its review is in, read from review.typo3.org. It is the surface a checkout cannot see: a clone carries what landed and says nothing about what is open. Six ways in, one per call. issue with a Forge number searches every commit message for it. change with a Change-Id or a change number, or commit with a hash out of a checkout, reads that one change. query and path search by words in the commit message and by repository path, and open narrows both to what is still under review. backlog enumerates the open changes, oldest pushed or longest untouched, narrowed by size, vote state, whether they still merge, branch, date and person. Every change carries its identity, status, current patch set, size, age and label state. One read by name adds its message and paths, votes, comments, relation chain, its Change-Id siblings, the Forge issues its trailers name and whether it carries conflict markers. An empty answer says whether it can be read as an absence, since a private change is invisible to an anonymous read. The issue itself is typo3_forge_lookup. Reading only: reviewing, voting and uploading stay yours.';
    }

    public static function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'issue' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Forge issue number, with or without the leading #, for example "105403". Searches every change whose commit message names it, which is where Resolves: and Related: put it. Not with change, commit, query, path or backlog.',
                ],
                'change' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'One change to read, by the Change-Id its commit message carries, for example "I0f4c5b9a3e2d1c7b8a6f5e4d3c2b1a0f9e8d7c6b", or by the change number a review URL ends with, for example "89011". Prefer the Change-Id where the commit is in front of you. It is part of the patch, it survives an amend, and it cannot be mistaken for the Forge issue number the way a bare change number can. Not with issue, commit, query, path or backlog.',
                ],
                'commit' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'A commit hash out of a checkout, abbreviated as git log prints it or whole, for example "cf227b18e20". Answers the change that commit is a patch set of, with the changes sharing its Change-Id. That is how a hash in your own history reaches the backports beside it and the branches each targets. Pass a hash here rather than as change: the review server answers "Invalid change format" to it there, which arrives as the server not answering at all. Not with issue, change, query, path or backlog.',
                ],
                'query' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Words to search the review server for, for example "impexp translation". Every word has to appear, matched against the commit message — subject and body, so a change whose subject lacks the word is still found. They are not matched against the diff: change 89000 added writePagesOrder and a search for that name answers nothing, so a zero says no commit message names the word. Ask again in the words a commit message would use, or pass path for the changes touching a file whatever they are called. Combine with path to narrow one by the other, and with open for what is still under review. Not with issue, change, commit or backlog.',
                ],
                'path' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'A path in the repository, for example "typo3/sysext/impexp" or "typo3/sysext/impexp/Classes/Import.php". Answers the changes touching it, the path itself and everything under it. With open it asks whether somebody is working on a file now, before a patch is written for it. Without open it reaches the abandoned and merged changes too, where an earlier attempt at the same fix is found. Combine with query to narrow one by the other. Not with issue, change, commit or backlog.',
                ],
                'open' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'Narrow a search to the changes still under review. False, the default, reaches every state, which "has anybody ever tried this" needs, since an abandoned or merged attempt answers it. True is "who is working on this now". Narrows query and path, and is ignored by issue, change and commit.',
                ],
                'messages' => [
                    'type' => 'string',
                    'enum' => ['none', 'people', 'all'],
                    'default' => 'none',
                    'description' => 'The review log of a change: every message its patch sets and its reviewers left. Ask for it to find out why a vote is gone. Gerrit writes "Outdated Votes: * Code-Review+1 (copy condition: ...)" into the message of the upload that dropped it, and the labels afterwards look like a change nobody has voted on. "none" leaves it out and is the default, since it is 57.9 KB against 14.3 KB on a change with 21 patch sets. "people" drops what a service user wrote — 20 of 46 messages on that change, every one a CI pipeline report. "all" keeps them. How many were dropped, and whether the current patch set carries git conflict markers, is answered whichever you ask for. Narrows change and commit, and is ignored by every other way in.',
                ],
                'backlog' => [
                    'type' => 'string',
                    'enum' => ['oldest', 'stale'],
                    'description' => 'Enumerate the open changes of the TYPO3 core instead of reading one or matching words. "oldest" orders them by when they were pushed, "stale" by how long nobody has touched them. Pushed long ago is about the patch, untouched for months about the attention it got, and a change that is both is what a review session is looking for. The filters beside it are what "small", "has votes" and "still applies" mean: maxSize, minCodeReview, negativeVotes and mergeable. The changes their own authors marked work in progress are left out, since a draft is not offered for review; query says so. maxSize, minCodeReview, negativeVotes, mergeable, branch, updatedBefore, owner, reviewedBy, involving and reviewableBy narrow this way in and no other. Not with issue, change, commit, query or path.',
                ],
                'maxSize' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'description' => 'Only changes whose insertions and deletions add up to at most this, for example 60. That is what "small in scope" comes to, and it decides whether a review fits into a session at all.',
                ],
                'minCodeReview' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 2,
                    'description' => 'Only changes somebody holds at least this Code-Review vote on: 1 for a change a reviewer has been through once, 2 for one that is approved. With negativeVotes false this is "almost ready": somebody is for it and nobody against.',
                ],
                'negativeVotes' => [
                    'type' => 'boolean',
                    'default' => true,
                    'description' => 'Whether changes carrying a Code-Review-1 or a Verified-1 are in the answer. True, the default, keeps them. False drops both — a reviewer objecting and a pipeline failing, the two reasons a change is not one to pick up now.',
                ],
                'mergeable' => [
                    'type' => 'boolean',
                    'default' => false,
                    'description' => 'True answers only the changes that still merge into their target branch. It is the review server\'s own last computation and not a merge run now, so it says which changes are worth fetching rather than promising one will apply. False, the default, keeps every change; the ones that no longer merge are usually the oldest, which makes an unfiltered "oldest first" page a list of conflicts.',
                ],
                'branch' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only changes targeting this branch, spelled as the branch is: "main", "13.4". Worth setting when the checkout in front of you is on one line, since a patch for another branch is reviewed against code you do not have.',
                ],
                'updatedBefore' => [
                    'type' => 'string',
                    'pattern' => '^\d{4}-\d{2}-\d{2}$',
                    'description' => 'Only changes nobody has touched since this day, as YYYY-MM-DD. It finds the review everybody has walked past, which age alone does not: a change pushed in 2023 and commented on last week is being worked. It reads the last update and never the push date — the review server indexes no created date, which is also why backlog "oldest" is ordered here.',
                ],
                'owner' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only changes this person pushed, by name or e-mail address: "Benjamin Kott", "benjamin.kott@outlook.com", or part of either. This answers "which open changes are mine", which query cannot: it matches the commit message, and a name there is as often somebody else writing it. The review server resolves the name; a name it does not know answers no changes, which looks exactly like a person with none.',
                ],
                'reviewedBy' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only changes this person has voted on, resolved the same way as owner. The other half of a person and a different question: what somebody pushed is theirs to finish, what they voted on is theirs to have judged already.',
                ],
                'involving' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only changes this person is on either side of — pushed or voted on, as one set. Passed instead of owner and reviewedBy, not beside them: those two together mean pushed AND voted on, a set nobody wants.',
                ],
                'reviewableBy' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'description' => 'Only changes this person neither pushed nor has voted on, named the same way as owner. That is "which of these could I review": everybody else\'s open work minus what I have already judged. The three filters above cannot be combined into it, since each of them selects. It reads no permissions: what is taken out is this person\'s own changes and votes. It composes with the three that select: owner with this one is what somebody else could review of a third person\'s queue. The same name here and on involving answers nothing. A name the review server cannot place takes nothing out and answers the whole backlog, the opposite of what a misspelling does to owner. Check it against a change of theirs before reading a wide answer as "nothing of theirs is in here".',
                ],
                'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 25, 'default' => 10],
            ],
            // A search is one way in carrying two arguments, so the path branch
            // is the one that excludes rather than the one that is excluded: a
            // call passing both would otherwise match two branches and fail the
            // rule it satisfies.
            'oneOf' => [
                ['required' => ['issue']],
                ['required' => ['change']],
                ['required' => ['commit']],
                ['required' => ['query']],
                ['required' => ['path'], 'not' => ['required' => ['query']]],
                ['required' => ['backlog']],
            ],
        ];
    }

    public static function outputSchema(): array
    {
        return Schema::object([
            'status' => Schema::answerStatus(),
            'source' => Schema::string('The review server the answer came from.'),
            'query' => Schema::string('The Gerrit query this was answered with, so the same question can be asked again by hand.'),
            'changes' => Schema::listOf(Schema::object([
                'number' => Schema::integer('Change number, the digits its review URL ends with.'),
                'changeId' => Schema::string('The Change-Id its commit message carries, empty where the server named none. It survives an amend and a rebase onto another branch, so it is what to hold the commit in front of you against. Changes sharing one are the same patch on more than one branch, which passing it back as change reads all of.'),
                'subject' => Schema::string(),
                'message' => [
                    'type' => ['string', 'null'],
                    'description' => 'The commit message of the current patch set, whole: the subject, the body and every '
                        . 'trailer. It is the change\'s own account of itself and what typo3_commit_message_guide '
                        . 'takes as its argument; holding the subject alone is what makes a trailer uncheckable. '
                        . 'Null means it was not read, which is a search by words or by path. An issue search reads '
                        . 'it to decide which hits name the issue and answers null all the same.',
                ],
                'status' => Schema::string('NEW while it is open, MERGED once it landed, ABANDONED when it was given up.'),
                'branch' => Schema::string('The branch the change targets.'),
                'patchSet' => Schema::integer('The patch set that is current on the server, counting from 1. Zero where the server named none.'),
                'commit' => Schema::string('The commit the current patch set is. A checkout whose HEAD is another commit is not the revision under review.'),
                'project' => Schema::string('The Gerrit project it was pushed to.'),
                'updated' => Schema::string('When the change last moved.'),
                'created' => Schema::string('When it was pushed, which says how long it has been waiting. A change pushed years ago and touched last week is being worked on; one where the two dates are far apart is not.'),
                'insertions' => [
                    'type' => ['integer', 'null'],
                    'description' => 'Lines the current patch set adds. Null where the review server stated none. '
                        . 'With deletions this is the size a reviewer picks a change by, and it is the diff of the '
                        . 'whole change rather than of what is left to read.',
                ],
                'deletions' => ['type' => ['integer', 'null'], 'description' => 'Lines the current patch set removes. Null where the review server stated none.'],
                'files' => [
                    'type' => ['array', 'null'],
                    'description' => 'Every path the current patch set touches, sorted by path, with what the patch '
                        . 'does to each. It is the changed paths a review establishes first and the argument '
                        . 'typo3_hint_lookup and typo3_test_run_guide take, so a change is triaged without being '
                        . 'fetched. The diff is not here: the hunks are what a fetch is for. A path this list '
                        . 'calls renamed can be one a reading of the hunks would call rewritten. Null means the '
                        . 'paths were not read, which is a search and an issue search; an empty list would be a '
                        . 'patch set touching nothing.',
                    'items' => Schema::object([
                        'path' => Schema::string('The path as the review server spells it, from the repository root.'),
                        'action' => [
                            'type' => 'string',
                            'enum' => ['modified', 'added', 'deleted', 'renamed', 'copied', 'rewritten'],
                            'description' => 'What the patch set does to this file. renamed and copied name where '
                                . 'the file came from in movedFrom; rewritten is a change large enough that the '
                                . 'review server stopped relating the two versions.',
                        ],
                        'insertions' => Schema::integer('Lines added in this file. Zero on a binary, where there are no lines to count.'),
                        'deletions' => Schema::integer('Lines removed in this file.'),
                        'binary' => ['type' => 'boolean', 'description' => 'Whether the file is binary, which is what makes the two zero counts beside it mean nothing. An image or a fixture archive is not an untouched file.'],
                        'movedFrom' => ['type' => ['string', 'null'], 'description' => 'The path this file was renamed or copied from, null on every other action.'],
                    ], ['path', 'action', 'insertions', 'deletions', 'binary', 'movedFrom']),
                ],
                'mergeable' => [
                    'type' => ['boolean', 'null'],
                    'description' => 'Whether the current patch set still merges into its target branch. It is the '
                        . 'review server\'s own last computation and not a merge run now, so false is grounds to '
                        . 'expect a rebase rather than a finding. Null where it computed none, which is not "it '
                        . 'does not merge".',
                ],
                'conflicts' => [
                    'type' => ['array', 'null'],
                    'description' => 'The files Gerrit reported git conflicts in when the current patch set was '
                        . 'created, so the markers are committed lines in them. The patch is broken rather than '
                        . 'merely unreviewed, and nothing else in this answer says so. A change created with the '
                        . 'web Cherry pick action or rebased through it can land this way. Its status, votes, '
                        . 'comment count and subject all read as a fresh patch set. Empty means the current patch '
                        . 'set carries none; a report on an earlier one is history and is not here. Null means the '
                        . 'review log was not read, which is a search and an enumeration.',
                    'items' => Schema::string(),
                ],
                'cherryPickOf' => [
                    'type' => ['object', 'null'],
                    'description' => 'The change and patch set this one was cherry-picked from, null where it was '
                        . 'pushed rather than cherry-picked. It is provenance and not a warning: most backports are '
                        . 'cherry-picks and almost none of them conflicted, so what says a patch set is broken is '
                        . 'conflicts beside it.',
                    'properties' => [
                        'change' => Schema::integer('The change number it was picked from, which reads it by being passed back as change.'),
                        'patchSet' => Schema::integer('The patch set of that change it was picked from, which is not necessarily the one that change stands at now.'),
                        'url' => Schema::string('Where a person reads that change.'),
                    ],
                    'required' => ['change', 'patchSet', 'url'],
                ],
                'url' => Schema::string('Where a person reads the review.'),
                'fetch' => [
                    'type' => ['object', 'null'],
                    'description' => 'How to get this patch set into a checkout. Null where the server named no '
                        . 'patch set, since a ref names one.',
                    'properties' => [
                        'ref' => Schema::string('The static ref this patch set is filed under. Every patch set keeps its own, so an earlier one stays fetchable after a newer is pushed.'),
                        'remote' => Schema::string('What to fetch that ref from. It is the review server rather than origin: a core clone fetches from the GitHub mirror, and refs/changes is not there.'),
                    ],
                    'required' => ['ref', 'remote'],
                ],
                'labels' => [
                    'type' => ['array', 'null'],
                    'description' => 'What the change stands at, one entry per label. The state of each label is on '
                        . 'every row, since the review server states it unasked. The voters behind it are read '
                        . 'for one change, so votes is null on a search and a list there.',
                    'items' => Schema::object([
                        'label' => Schema::string('Code-Review and Verified are the two the core project votes with.'),
                        'state' => Schema::string('What the submit rule makes of this label. OK where it is met, NEED where it still wants a vote, REJECT where a vote is blocking it, IMPOSSIBLE where no vote available could satisfy it. NEED is not "nobody has voted": a change held at Code-Review+1 where the rule asks for +2 stands there too, and the votes beside it say which. The pair to tell apart is NEED against REJECT — a change waiting for a reviewer, and one somebody has already turned down. Empty where no rule names the label; where several rules name it, the most consequential of their states is here.'),
                        'satisfied' => [
                            'type' => ['boolean', 'null'],
                            'description' => 'Whether the submit rule counts this label as met — the state beside it '
                                . 'read as a boolean. False is the ordinary state of an open change, and null means '
                                . 'no rule asks for it. What it stands at is in the votes: the range is the '
                                . 'project\'s own, and Verified runs to +2 here, so a +1 is not the top of it.',
                        ],
                        'votes' => [
                            'type' => ['array', 'null'],
                            'description' => 'Everyone on the label, those holding nothing included. Null where the '
                                . 'voters were not read, which is every hit a search or an enumeration answers; a '
                                . 'list with zeros in it means nobody has voted, a different answer. Pass the '
                                . 'change number back as change for them.',
                            'items' => Schema::object([
                                'voter' => Schema::string(),
                                'value' => Schema::integer('What this voter holds now. Zero is a reviewer who was added and has not voted. A vote a later patch set dropped is absent rather than zero, and only the review log says it was ever there.'),
                                'on' => Schema::string('When it was cast, empty where nothing was.'),
                            ], ['voter', 'value', 'on']),
                        ],
                    ], ['label', 'state', 'satisfied', 'votes']),
                ],
                'commentCount' => Schema::integer('How many comments the change carries, which the review server states whether or not they were read.'),
                'unresolvedCommentCount' => Schema::integer('How many of the threads those comments form are open, which the review server states whether or not the comments were read. It counts threads and not comments, so it is smaller than the number of comments carrying the flag wherever somebody replied. It is the flag as each thread\'s last writer left it rather than a count of unanswered questions. On a change to pick up it is the work still owed to the last reviewer.'),
                'comments' => [
                    'type' => ['array', 'null'],
                    'description' => 'The comments left on the change, oldest first, each saying which thread it is '
                        . 'in and what that thread stands at. Empty means it carries none. '
                        . 'Null means they were not read: a search asks for none, and a change lookup whose '
                        . 'comment call did not answer says so here rather than with an empty list. Hold it '
                        . 'against commentCount.',
                    'items' => Schema::object([
                        'id' => Schema::string('What the inReplyTo of a reply names.'),
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'patchSet' => Schema::integer('The patch set it was left on. One older than the current patch set is a comment written about code that may since have changed, and it is still unanswered until somebody answers it.'),
                        'file' => Schema::string('The file it sits on. /PATCHSET_LEVEL is a comment on the change itself rather than on a place in it.'),
                        'line' => ['type' => ['integer', 'null'], 'description' => 'Null on a comment about the change rather than about a line.'],
                        'unresolved' => ['type' => 'boolean', 'description' => 'The flag on this one comment, as its own writer left it. It is not what the thread stands at — that is threadUnresolved beside it — and the two differ on every comment somebody resolved by replying to it.'],
                        'inReplyTo' => ['type' => ['string', 'null'], 'description' => 'The id of the comment this answers, null where it starts a thread.'],
                        'thread' => Schema::string('The id of the comment this thread starts with, which is this comment\'s own id where it starts one. Comments carrying the same one are one thread, and they stand in the order they were written.'),
                        'threadUnresolved' => ['type' => 'boolean', 'description' => 'Whether the thread this comment sits in is open: the unresolved flag on that thread\'s last comment. Gerrit stores a thread\'s state there and counts the open ones as unresolvedCommentCount, so every comment in a thread carries the same value here. It is a flag somebody set rather than a judgement that the question was answered.'],
                        'message' => Schema::string('The comment as it was written.'),
                    ], ['id', 'author', 'on', 'patchSet', 'file', 'line', 'unresolved', 'inReplyTo', 'thread', 'threadUnresolved', 'message']),
                ],
                'chain' => [
                    'type' => ['array', 'null'],
                    'description' => 'The relation chain this change sits in, child first: the changes stacked on '
                        . 'it, then itself, then the changes it is built on. This is the other relation and not '
                        . 'the Change-Id one. A chain is different changes built on one another, a shared '
                        . 'Change-Id is one patch on several branches, and reading the two as one set overstates '
                        . 'both. Empty means the change stands alone, the ordinary case. Null means the chain was '
                        . 'not read: a search asks for none, and a change lookup whose call did not answer says so '
                        . 'here rather than with an empty list.',
                    'items' => Schema::object([
                        'number' => Schema::integer('The entry\'s change number, which reads it by passing it back as change.'),
                        'status' => Schema::string('NEW, MERGED or ABANDONED — the entry\'s own state, not the state of the change this answer is about. A MERGED entry says that part of the stack landed.'),
                        'subject' => Schema::string('The commit subject of the patch set the chain names.'),
                        'thisChange' => [
                            'type' => 'boolean',
                            'description' => 'Whether this entry is the change the answer is about. Its place in '
                                . 'the list is what says how much is stacked on it and how much it is built on.',
                        ],
                        'patchSet' => Schema::integer('The patch set the entry stands at now.'),
                        'chainedAt' => Schema::integer('The patch set of the entry that the chain is built on. Lower than patchSet means the stack holds the older one and that change has moved on since. Act on the entry by its number rather than on the patch set named here.'),
                        'url' => Schema::string('Where a person reads that change.'),
                    ], ['number', 'status', 'subject', 'thisChange', 'patchSet', 'chainedAt', 'url']),
                ],
                'issues' => [
                    'type' => ['array', 'null'],
                    'description' => 'The Forge issues this change\'s commit message names in its Resolves: and '
                        . 'Related: trailers, each filled with what says whether to read it. That is the join '
                        . 'between the patch and the tracker, and where a second issue nobody mentioned elsewhere '
                        . 'is seen. Empty means the message names none. Null means the message was not read: a '
                        . 'search asks for none of this, and reading one hit by name is what answers it.',
                    'items' => Schema::object([
                        'issue' => Schema::integer('The issue number, which reads it whole by passing it to typo3_forge_lookup as issue.'),
                        'trailer' => Schema::string('resolves where the message carries Resolves:, related where it carries Related:. The two are different claims: what the patch closes, and what it touches.'),
                        'subject' => Schema::string('What the issue is about, so it can be judged without being read. Empty where the tracker did not answer the one call that fills the whole set.'),
                        'tracker' => Schema::string('Bug, Feature, Task.'),
                        'status' => Schema::string('Where the issue stands, which is the tracker\'s own state and not the state of this change.'),
                        'url' => Schema::string('Where a person reads it.'),
                    ], ['issue', 'trailer', 'subject', 'tracker', 'status', 'url']),
                ],
                'releases' => [
                    'type' => ['array', 'null'],
                    'description' => 'The branches this change\'s commit message names in its Releases: trailer, '
                        . 'spelled as the trailer spells them. It is the author\'s claim about which branches the '
                        . 'patch belongs on, written before it went to any of them. What was pushed is the changes '
                        . 'above sharing a Change-Id, one per branch and each with its own status. A branch named '
                        . 'here with no change targeting it is a backport nobody has pushed. Empty means the '
                        . 'message carries no such trailer, which every change outside the core project is. Null '
                        . 'means the message was not read, which is a search by words or path.',
                    'items' => Schema::string(),
                ],
                'messages' => [
                    'type' => ['array', 'null'],
                    'description' => 'The review log, oldest first, where messages asked for it. Null otherwise, '
                        . 'which is the default and every hit a search answers.',
                    'items' => Schema::object([
                        'author' => Schema::string(),
                        'on' => Schema::string(),
                        'patchSet' => Schema::integer('The patch set it was written about.'),
                        'bot' => ['type' => 'boolean', 'description' => 'Whether a service user wrote it, read off the account rather than off its name. On the core project that is the CI reporting a pipeline.'],
                        'message' => Schema::string('The message as it stands. The upload of a patch set carries the votes it dropped and the copy condition that dropped them, which is the one place that is written down.'),
                    ], ['author', 'on', 'patchSet', 'bot', 'message']),
                ],
                'botMessageCount' => [
                    'type' => ['integer', 'null'],
                    'description' => 'How many of the log a service user wrote, which messages: "people" is what '
                        . 'drops. Answered whichever way it was asked. A log full of pipeline reports answering '
                        . 'zero here is Gerrit no longer tagging its service users rather than a change no bot '
                        . 'has been near. Null where the log was not read.',
                ],
            ]), 'The changes that matched, newest activity first — oldest or longest untouched first where backlog asked for an enumeration. A change named by change or commit comes with the changes sharing its Change-Id, which is how a backport on a release branch is reached.'),
            'backlog' => [
                'type' => ['object', 'null'],
                'description' => 'What the enumeration read, where backlog asked for one; null on every other way '
                    . 'in. The review server states no total for a query and offers no created date to sort by. '
                    . 'So the matched set is read whole and ordered here, which is what these two numbers are '
                    . 'about.',
                'properties' => [
                    'order' => ['type' => 'string', 'enum' => ['oldest', 'stale'], 'description' => 'oldest: by when the change was pushed. stale: by when it last moved.'],
                    'read' => Schema::integer('How many changes the filters matched and this answer sorted, of which changes above carries at most limit. Where the two differ this is a page, and what reaches the rest of it is a narrower filter rather than a larger limit.'),
                    'complete' => ['type' => 'boolean', 'description' => 'Whether read is the whole matched set. False where the read stopped at the bound, and then the ordering is over one end of the set rather than all of it. Narrow the filters before reading the page as the oldest changes there are.'],
                ],
                'required' => ['order', 'read', 'complete'],
            ],
            'releaseLines' => Schema::object([
                'branches' => Schema::listOf(Schema::object([
                    'branch' => Schema::string('The branch, spelled as a Releases: trailer spells it and as the branch field of a change above does.'),
                    'state' => [
                        'type' => 'string',
                        'enum' => [ReleaseLines::DEVELOPMENT, ReleaseLines::MAINTAINED],
                        'description' => 'development: the line every core change is written against first. '
                            . 'maintained: in regular support, so a patch pushed here is released from this branch. '
                            . 'A line out of regular support is not in this list at all — what it releases comes '
                            . 'from the ELTS partners rather than from the branch.',
                    ],
                    'maintainedUntil' => Schema::nullableString('The day regular support ends, as the release calendar states it. Null on the development line, which has no such date.'),
                ], ['branch', 'state', 'maintainedUntil']), 'Newest first, the development line at the head.'),
                'source' => Schema::string('Where the calendar was read, so it can be read again rather than trusted.'),
                'readAt' => Schema::string('The day it was read. A branch released since is one this list could not carry, and a change above targeting a branch absent here is either that or a line out of regular support.'),
            ], ['branches', 'source', 'readAt'], 'The branches that take a patch today, from a list this server ships rather than from the review server, so it is answered whatever the status above says. It is what a Releases: trailer may name, and a core clone supplies it nowhere. git branch -r reaches back to TYPO3_3-6 and says nothing about which of those is still maintained. Which of these lines a change belongs on is not here — that is the author\'s claim, and typo3_commit_message_guide with workflow="core" is what reads a trailer against them.'),
            'unavailable' => Schema::unavailable([
                'source-not-answering' => 'review.typo3.org did not answer this time, and the same call may answer '
                    . 'the next.',
                'source-not-parseable' => 'something answered and it was not the review API, which is what a proxy '
                    . 'or a captive portal looks like from here.',
            ]),
            // Required and nullable, the shape `unavailable` beside it has. A
            // caller that has to branch on whether the key is there cannot
            // tell an answer with nothing to qualify from a server too old to
            // qualify anything, and this field exists precisely because that
            // distinction was being got wrong one level down.
            'indistinguishable' => [
                'type' => ['string', 'null'],
                'description' => 'Why an empty answer cannot be read as an absence, or null where it can. This '
                    . 'server reads the review server without credentials, so a change that is private or work in '
                    . 'progress is invisible to it and looks exactly like one nobody pushed. Null means empty '
                    . 'really does mean nothing matched.',
            ],
        ], ['status', 'source', 'query', 'changes', 'backlog', 'releaseLines', 'unavailable', 'indistinguishable']);
    }

    /**
     * The branches that take a patch today, beside the branch each change names.
     *
     * The one thing this answer said nothing about while naming a branch: a
     * session rewriting a `Releases:` trailer was told the change targets `main`
     * and rebuilt the rest from `git branch -r` and a listing of the changelog
     * folders — an inference that holds in a full clone and nowhere else
     * (`D-ANS-104`). Which of the lines a change belongs on stays out, because
     * that is the author's claim rather than a consequence of the list
     * (`D-ANS-073`), and the tool that reads a trailer against them is named
     * instead. Separated from `answer()` so it can be held without a review
     * server.
     *
     * @return array{
     *     lines: list<string>,
     *     record: array{
     *         branches: list<array{branch: string, state: string, maintainedUntil: ?string}>,
     *         source: string,
     *         readAt: string
     *     }
     * }
     */
    public static function releaseLines(): array
    {
        $branches = [];
        $said = [];
        foreach (ReleaseLines::releasable() as $branch) {
            $state = ReleaseLines::state($branch);
            $branches[] = [
                'branch' => $branch,
                'state' => $state,
                'maintainedUntil' => ReleaseLines::maintainedUntil($branch),
            ];
            $said[] = $state === ReleaseLines::DEVELOPMENT
                ? $branch . ' is the development line, which every core change is written against first'
                : ReleaseLines::describe($branch);
        }

        return [
            'lines' => ['', sprintf(
                'The branches that take a patch today, whichever one the change above targets: %s. Read from %s on '
                    . '%s; a core clone carries no such list, since "git branch -r" reaches back to TYPO3_3-6 and '
                    . 'says nothing about which of those is still maintained. Which of these a change belongs on is '
                    . 'the author\'s claim rather than a consequence of the list — `typo3_commit_message_guide` with '
                    . '`workflow="core"` is what reads a `Releases:` trailer against them.',
                implode('; ', $said),
                ReleaseLines::source(),
                ReleaseLines::readAt(),
            )],
            'record' => [
                'branches' => $branches,
                'source' => ReleaseLines::source(),
                'readAt' => ReleaseLines::readAt(),
            ],
        ];
    }

    /**
     * Which of the two forms `change` was given in.
     *
     * A Change-Id is the `I` and forty hex digits a commit message carries; a
     * change number is the digits a review URL ends with. Saying "no change
     * with this number" back to a caller that passed a Change-Id is wrong
     * twice over, and one review read it as its own commit never having been
     * pushed (`feedback/2026-08-07-132416`).
     */
    private static function isChangeId(string $change): bool
    {
        return preg_match('/^I[0-9a-f]{40}$/i', $change) === 1;
    }

    /**
     * What an empty answer cannot separate, where it cannot separate anything.
     *
     * A caller that named one change has named something it read somewhere, and
     * an empty answer there is a restricted change at least as often as an
     * absent one: this server reads Gerrit anonymously (`R-ANS-027`). A search
     * owes the same caveat and one more of its own (`D-ANS-100`), and an issue
     * search owes neither — "no change names this issue" is a claim about a
     * query, and the text half states it there. Separated from `answer()` so it
     * can be held without a review server.
     *
     * @param string $direction the argument the caller passed, since what an
     *     empty answer fails to separate is different for each of them
     * @param array{author: string, url: string}|null $review what Gerrit posted
     *     on the issue, where a search for one came back empty and the tracker
     *     had a note
     */
    public static function indistinguishable(string $status, string $direction, ?array $review = null): ?string
    {
        if ($status !== 'empty') {
            return null;
        }

        // The issue case, where the tracker settled it. Gerrit Code Review
        // posts a note on the issue for every patch set it receives, so a
        // review URL there and nothing here is not two possibilities: the
        // change exists and this reader may not see it. That is the report's
        // own idea, and it is only buildable on this side —
        // `feedback/2026-08-07-132416`.
        if ($review !== null) {
            return sprintf(
                'A change for this issue does exist and is not one an anonymous reader may see. %s posted %s on '
                    . 'the issue, and this server reads %s without credentials, so a private or work-in-progress '
                    . 'change is invisible to it. Read the change there while signed in.',
                $review['author'],
                $review['url'] === '' ? 'a review note' : $review['url'],
                Gerrit::HOST,
            );
        }

        $anonymous = 'This server reads ' . Gerrit::HOST . ' without credentials, so a change that is private or '
            . 'work in progress is invisible to it. ';

        return match ($direction) {
            'change' => $anonymous . 'Such a change answers exactly like one that does not exist, so this is either '
                . '"no such change" or "not one an anonymous reader may see", and nothing here separates them. Where '
                . 'the id came from a commit you have, the second is the more likely of the two.',
            // A hash reaches the review server only where somebody pushed it,
            // and a checkout carries commits nobody did — `D-ANS-106`.
            'commit' => $anonymous . 'A commit that was never pushed for review answers exactly the same, so this is '
                . '"no patch set is this commit" or "not one an anonymous reader may see", and nothing here '
                . 'separates them. Ask again with `query` in the words of the commit subject, which reaches the '
                . 'change whatever commit its patch set stands at.',
            // The word direction's own trap, and the one that reads as an
            // established negative: `feedback/2026-08-24-110833` took a zero for
            // an identifier as nobody having attempted the fix — `D-ANS-100`.
            'query' => $anonymous . 'A word is matched against the commit message rather than against the diff: '
                . 'change 89000 added `writePagesOrder`, and a search for that name answers nothing. So a zero says '
                . 'that no commit message names the word, not that nobody has touched the code. Ask again in the '
                . 'words a commit message would use, and pass `path` for the changes that touch a file whatever '
                . 'they are called.',
            // The enumeration's own trap is the person filter. The review server
            // answers a name it cannot place with no changes and no error, so
            // "nobody by that name" arrives as "this person has nothing open" —
            // measured on 2026-08-25, where `owner:zzzznotauser` came back 200
            // with an empty list.
            'backlog' => $anonymous . 'A name the review server cannot place answers the same way as a person with '
                . 'nothing open, so where owner, reviewedBy or involving was passed, check the spelling against a '
                . 'change of theirs before reading this as an empty backlog. Part of a name or an address reaches '
                . 'them; a name nothing there carries reaches nobody.',
            'path' => $anonymous . 'Such a change answers exactly like a path nothing touches, so this is either '
                . '"nobody is working on it" or "nobody an anonymous reader may see is", and nothing here separates '
                . 'them. What is matched is the paths a change touches, so a fix for this file that landed '
                . 'elsewhere is not in the answer either.',
            default => null,
        };
    }

    /**
     * The workflow a patch set in front of a caller is in, and the order it
     * takes, where there is one.
     *
     * A review session that opened no skill asked this tool for a change and was
     * handed a ref, a remote and nothing about the work it had just begun
     * (`D-SKL-038`). Naming the two workflows was the first answer to that and
     * it was one step short: a second session read the names, opened neither,
     * and reviewed change 95179 and rebased it by hand
     * (`feedback/2026-08-24-122413`). So the order is here too, in the shape
     * `TestRunGuide::SCRIPTS_GUIDE` took — the whole of it is still the skill,
     * and what the answer carries is the steps that decide the result. The
     * `change` form alone, because a search has no one workflow to name
     * whichever way it was asked. Separated from `answer()` so it can be held
     * without a review server.
     */
    public static function workflow(string $status, string $change): ?string
    {
        if ($status !== 'answered' || trim($change) === '') {
            return null;
        }

        return "## What a patch set in front of you opens\n"
            . 'One of two workflows: `typo3-core-patch-review` reviews it, and `typo3-core-patch-checkout` '
            . 'fetches it into a checkout and backs out again. Open the one this task is before reading the '
            . "diff, and start it at `typo3_project_describe`. Where neither is open, this is the order:\n"
            . '- Establish the patch before judging it: the changed paths, the branch it targets, the commit '
            . 'message and the issue it names. All four are above, so this costs no fetch — the target branch '
            . "decides which conventions apply.\n"
            . '- Three ways in, and a branch of your own naming is none of them: the branch the change '
            . 'targets, a worktree beside the checkout, or current code on `review/<change number>`. The '
            . "third makes a commit that exists nowhere else, so say which of the two each result is about.\n"
            . "- A patch that no longer applies is the finding. Resolving past it produces a patch nobody wrote.\n"
            . '- Reading is the whole of the review: voting, commenting and uploading stay yours. An '
            . 'instruction to change the patch — fix it, amend it, answer the comments — ends the review and '
            . 'opens `typo3-core-patch-development`.';
    }

    /**
     * The newest review URL Gerrit posted on an issue, or null.
     *
     * Read from the journal rather than from the description, because that is
     * where it is: the note is authored by Gerrit itself and names the patch
     * set and the change. Only asked where a search over commit messages came
     * back empty, so the second host is reached on the path where the answer
     * would otherwise be a guess, and not on the ordinary one.
     *
     * The same cross-check for a caller that named a change rather than an
     * issue was measured on 2026-08-07 and is not built: searching the tracker
     * for `95162` costs 2.5 seconds and answers two issues, one of them
     * unrelated, and searching for the Change-Id answers nothing at all. That
     * is a second guess rather than evidence, and it is the case the report was
     * about.
     *
     * @return array{author: string, url: string}|null
     */
    private static function reviewPostedOnIssue(string $issue): ?array
    {
        $answer = (new Forge())->issue($issue);
        if ($answer['status'] !== 'answered' || !is_array($answer['issue'])) {
            return null;
        }

        $newest = null;
        foreach ((array) ($answer['issue']['notes'] ?? []) as $note) {
            if (!is_array($note) || !str_contains(strtolower((string) ($note['author'] ?? '')), 'gerrit')) {
                continue;
            }
            if (preg_match('~https?://\S*review\.typo3\.org/\S+~', (string) ($note['note'] ?? ''), $found) !== 1) {
                continue;
            }
            $newest = ['author' => (string) $note['author'], 'url' => rtrim($found[0], '.,)')];
        }

        return $newest;
    }

    /** @param array<string, mixed> $args */
    public static function answer(array $args): ToolResult
    {
        $issue = is_string($args['issue'] ?? null) ? trim($args['issue']) : '';
        $change = is_string($args['change'] ?? null) ? trim($args['change']) : '';
        $commit = is_string($args['commit'] ?? null) ? trim($args['commit']) : '';
        $query = is_string($args['query'] ?? null) ? trim($args['query']) : '';
        $path = is_string($args['path'] ?? null) ? trim($args['path']) : '';
        $open = (bool) ($args['open'] ?? false);
        $limit = is_int($args['limit'] ?? null) ? $args['limit'] : 10;
        $messages = is_string($args['messages'] ?? null) ? trim($args['messages']) : 'none';
        $backlog = is_string($args['backlog'] ?? null) ? trim($args['backlog']) : '';
        $owner = is_string($args['owner'] ?? null) ? trim($args['owner']) : '';
        $reviewedBy = is_string($args['reviewedBy'] ?? null) ? trim($args['reviewedBy']) : '';
        $involving = is_string($args['involving'] ?? null) ? trim($args['involving']) : '';
        $reviewableBy = is_string($args['reviewableBy'] ?? null) ? trim($args['reviewableBy']) : '';
        // A person filter is a narrowing of the enumeration and the schema says
        // so. Passing one without `backlog` is a call no schema allows, and what
        // a client that validates nothing would otherwise reach is a search for
        // the empty string rather than the question it plainly asked.
        if ($backlog === '' && ($owner !== '' || $reviewedBy !== '' || $involving !== '' || $reviewableBy !== '')) {
            $backlog = 'oldest';
        }

        // Which of the six the caller passed, which is what decides the query,
        // what a hit carries, and what an empty answer fails to separate. The
        // words carry the search where both were given, because their caveat is
        // the wider one.
        $direction = match (true) {
            $issue !== '' => 'issue',
            $change !== '' => 'change',
            $commit !== '' => 'commit',
            $backlog !== '' => 'backlog',
            $query !== '' => 'query',
            default => 'path',
        };

        $gerrit = new Gerrit();
        $answer = match ($direction) {
            'issue' => $gerrit->changesForIssue($issue, $limit),
            'change' => $gerrit->change($change, $limit, $messages),
            'commit' => $gerrit->commit($commit, $limit, $messages),
            'backlog' => $gerrit->backlog(
                order: $backlog,
                maxSize: is_int($args['maxSize'] ?? null) ? $args['maxSize'] : 0,
                minCodeReview: is_int($args['minCodeReview'] ?? null) ? $args['minCodeReview'] : 0,
                negativeVotes: ($args['negativeVotes'] ?? true) !== false,
                mergeable: ($args['mergeable'] ?? false) === true,
                branch: is_string($args['branch'] ?? null) ? trim($args['branch']) : '',
                updatedBefore: is_string($args['updatedBefore'] ?? null) ? trim($args['updatedBefore']) : '',
                owner: $owner,
                reviewedBy: $reviewedBy,
                involving: $involving,
                reviewableBy: $reviewableBy,
                limit: $limit,
            ),
            default => $gerrit->changesMatching($query, $path, $open, $limit),
        };

        // The tracker is asked only where the review server answered
        // nothing for an issue, which is the one path where a second host
        // buys an answer instead of a hedge. It cost 0.12 seconds measured
        // against forge.typo3.org on 2026-08-07.
        $review = $direction === 'issue' && $answer['status'] === 'empty'
            ? self::reviewPostedOnIssue($issue)
            : null;
        $indistinguishable = self::indistinguishable($answer['status'], $direction, $review);
        $releaseLines = self::releaseLines();
        // Whether the caller named one change, which is what asked for the
        // review, the chain, the issues and the trailer. A commit names one as
        // squarely as a change number does, so silence about any of them means
        // the same thing on both — `D-ANS-106`.
        $byName = $direction === 'change' || $direction === 'commit';

        $data = [
            'status' => $answer['status'],
            'source' => Gerrit::HOST,
            'query' => $answer['query'],
            'changes' => $answer['changes'],
            'backlog' => $direction === 'backlog' ? [
                'order' => $backlog,
                'read' => $answer['read'],
                'complete' => $answer['complete'],
            ] : null,
            'releaseLines' => $releaseLines['record'],
            'indistinguishable' => $indistinguishable,
            'unavailable' => Unreachable::of($answer['cause'], self::UNREACHABLE),
        ];

        $lines = ['TYPO3 core review server: ' . Gerrit::HOST, 'Query: ' . $answer['query']];
        if ($answer['status'] === 'unavailable') {
            $lines[] = 'Could not answer: ' . $data['unavailable']['reason'];
        } elseif ($answer['status'] === 'empty') {
            $lines[] = match ($direction) {
                'issue' => 'No change names this issue in its commit message. This reads the review server anonymously, so a change pushed as private is invisible here — the answer is that nothing public exists, not that nobody has fixed it.',
                'change' => 'No change an anonymous reader may see matches this ' . (self::isChangeId($change) ? 'Change-Id' : 'change number') . '.',
                'commit' => 'No change an anonymous reader may see was pushed with this commit.',
                'backlog' => 'No open change an anonymous reader may see matches these filters. That is an answer about the filters and not about the backlog, and the query above says which of them was asked: widen maxSize, drop minCodeReview, or leave mergeable out to reach the changes that no longer merge.',
                default => 'No change an anonymous reader may see matches this search.',
            };
            if ($indistinguishable !== null) {
                $lines[] = $indistinguishable;
            }
            if ($answer['dropped'] > 0) {
                $lines[] = self::held($answer['dropped']);
            }
        } else {
            $named = false;
            $fetchable = false;
            $ids = [];
            $commented = false;
            $voted = false;
            $stacked = false;
            $moved = false;
            $tracked = false;
            $touched = false;
            if ($direction === 'backlog') {
                $lines = [...$lines, ...self::page($data['backlog'], count($answer['changes']), $reviewableBy)];
            }
            foreach ($answer['changes'] as $entry) {
                $lines[] = '';
                $lines[] = sprintf('## %s (%s)', $entry['subject'], $entry['status']);
                $lines[] = sprintf('Change %d · %s · %s', $entry['number'], $entry['branch'], $entry['url']);
                if ($entry['changeId'] !== '') {
                    $ids[] = strtolower($entry['changeId']);
                    $lines[] = 'Change-Id: ' . $entry['changeId'];
                }
                if ($entry['patchSet'] > 0) {
                    $named = $named || $entry['commit'] !== '';
                    $lines[] = $entry['commit'] === ''
                        ? sprintf('Patch set %d', $entry['patchSet'])
                        : sprintf('Patch set %d · %s', $entry['patchSet'], $entry['commit']);
                }
                if ($entry['fetch'] !== null) {
                    $fetchable = true;
                    $lines[] = sprintf('Fetch: git fetch %s %s', $entry['fetch']['remote'], $entry['fetch']['ref']);
                }
                if ($entry['updated'] !== '') {
                    $lines[] = 'Last moved: ' . $entry['updated'];
                }
                $standing = self::standing($entry);
                if ($standing !== '') {
                    $lines[] = $standing;
                }
                $lines = [...$lines, ...self::conflicts($entry, $byName), ...self::cherryPick($entry)];
                $lines = [...$lines, ...self::releases($entry)];
                foreach ($entry['labels'] ?? [] as $label) {
                    // Only where the voters were read, since the paragraph this
                    // decides is about a vote that is gone and a state carries
                    // no voter for one to be gone from.
                    $voted = $voted || $label['votes'] !== null;
                    $lines[] = self::vote($label);
                }
                $commented = $commented || ($entry['comments'] ?? []) !== [];
                $stacked = $stacked || ($entry['chain'] ?? []) !== [];
                $tracked = $tracked || ($entry['issues'] ?? []) !== [];
                $touched = $touched || ($entry['files'] ?? []) !== [];
                foreach ($entry['chain'] ?? [] as $related) {
                    $moved = $moved || self::behind($related);
                }
                // Only a change read by name asked for any of it, so silence
                // elsewhere is not a claim that it could not be read — which a
                // search would otherwise make about every hit it answers.
                $lines = [
                    ...$lines,
                    ...self::commitMessage($entry),
                    ...self::touches($entry, $byName),
                    ...self::issues($entry, $byName),
                    ...self::chain($entry, $byName),
                    ...self::comments($entry, $byName),
                    ...self::log($entry, $messages),
                ];
            }
            if ($touched) {
                $lines[] = '';
                $lines[] = 'The paths above are what the current patch set touches, and they are the argument the '
                    . 'work after this takes: `typo3_hint_lookup` for the conventions of each subsystem in the list, '
                    . '`typo3_test_run_guide` for the suites that can fail on them. What is not here is the diff — '
                    . 'the hunks are what a fetch is for, and a shortlist is triaged without fetching anything.';
            }
            if ($tracked) {
                $lines[] = '';
                $lines[] = 'The issues above are what the commit message names, and a status there is the issue\'s '
                    . 'own rather than this change\'s. Pass one to `typo3_forge_lookup` as `issue` to read it whole, '
                    . 'which is where a maintainer said why something was closed or reassigned.';
            }
            if ($stacked) {
                $lines = [...$lines, ...self::relations($moved)];
            }
            if ($commented) {
                $lines[] = '';
                $lines[] = 'A heading above is what its thread stands at: the `unresolved` flag on the last '
                    . 'comment in it, which is where Gerrit keeps a thread\'s state and what it counts beside the '
                    . 'change. The flag on one comment is its own writer\'s, and the data half carries both. '
                    . 'Neither is a judgement that a question was answered: a resolved thread can still hold one, '
                    . 'and an unresolved thread can carry the reply that settled it. Which of them this review '
                    . 'would otherwise make a second time is yours to read.';
            }
            if ($voted && $messages === 'none') {
                $lines[] = '';
                $lines[] = 'A vote a later patch set dropped is absent here rather than zero, and the copy '
                    . 'condition that dropped it is written in the review log alone — ask again with '
                    . '`messages: "people"` where a label stands at nothing and you need to know whether it '
                    . 'ever stood elsewhere.';
            }
            // What the pair is, said where a reader would otherwise read two
            // changes with one subject as a duplicate — `D-ANS-080`.
            if (count($ids) !== count(array_unique($ids))) {
                $lines[] = '';
                $lines[] = 'More than one change above carries the same Change-Id. That is what a backport keeps, '
                    . 'so they are one patch on the branches each of them names. Gerrit relates them by nothing '
                    . 'else, and the state of one says nothing about the state of the other.';
            }
            // What the trailer above is worth, before the lines it names are
            // held against the ones that take a patch today.
            $lines = [...$lines, ...self::releaseClaim($answer['changes'])];
            // Printed where a change came back, because that is where a branch
            // was named — the placement `D-ANS-104` asks for. A search that
            // matched nothing named none, and the data half carries the list
            // either way.
            $lines = [...$lines, ...$releaseLines['lines']];
            // The one thing this answer knows and the checkout does not: which
            // revision the review is of. Nothing here can read a local `HEAD`,
            // so the comparison is the caller's and the sentence is what says
            // there is one to make.
            if ($named) {
                $lines[] = '';
                $lines[] = 'Hold the commit against `git rev-parse HEAD` in the checkout. Where the two '
                    . 'differ, the checkout is not the revision under review, and a review says which of '
                    . 'the two it read.';
            }
            // The remote is spelled out because `origin` is the wrong one, and
            // wrong in the way that reads as the change not existing —
            // `D-SKL-021` measured the fetch coming back empty over the mirror.
            if ($fetchable) {
                $lines[] = '';
                $lines[] = 'The fetch goes to the review server rather than to `origin`: a core clone fetches '
                    . 'from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach '
                    . 'FETCH_HEAD` is what puts the checkout on the patch set afterwards.';
            }
            // A search is the one direction whose set has no natural end, so a
            // full page is as likely to be where the answer stopped as where the
            // matches did, and a caller counting it reports the limit. The
            // review server's own flag says which of the two it was, and the
            // enumeration says it with a count instead.
            if (!$byName && $direction !== 'backlog' && $answer['more']) {
                $lines[] = '';
                $lines[] = sprintf(
                    'The answer stopped at the %d asked for and the review server has more, so this is a page of what '
                        . 'matched rather than the whole of it. Narrow it with more words, a longer path or open '
                        . 'before reading the count as one.',
                    $limit,
                );
            }
            if ($answer['dropped'] > 0) {
                $lines[] = '';
                $lines[] = self::held($answer['dropped']);
            }
        }

        $workflow = self::workflow($answer['status'], $change);
        if ($workflow !== null) {
            $lines[] = '';
            $lines[] = $workflow;
        }

        return ToolResult::create(implode("\n", $lines), $data);
    }

    /**
     * One label, as a reviewer picking the change up reads it: where it stands
     * and who put it there.
     *
     * The state is said in words rather than in the submit rule's own, because
     * the pair a caller acts on is `NEED` against `REJECT` — a change waiting
     * for a reviewer and a change somebody has already turned down — and
     * "not satisfied" was what this said for both of them. The voters follow
     * where they were read, which is a change read by name; a search asks for
     * none, and a list of zeros there would read as nobody having voted.
     *
     * The values carry their sign, because +1 and -1 are the vote and 1 is a
     * number. A label nobody has voted on still lists its reviewers at 0, which
     * is who was asked rather than who answered.
     *
     * @param array<string, mixed> $label
     */
    public static function vote(array $label): string
    {
        $standing = match ($label['state']) {
            'OK' => 'satisfied',
            'NEED' => 'needs a vote',
            'REJECT' => 'a vote is blocking it',
            'IMPOSSIBLE' => 'no vote available satisfies the rule',
            default => 'not required',
        };
        if ($label['votes'] === null) {
            return sprintf('%s: %s', $label['label'], $standing);
        }

        $held = [];
        foreach ($label['votes'] as $vote) {
            $held[] = sprintf('%s %s', $vote['voter'], $vote['value'] === 0 ? '0' : sprintf('%+d', $vote['value']));
        }

        return sprintf(
            '%s: %s%s',
            $label['label'],
            $standing,
            $held === [] ? ' · nobody has been asked' : ' · ' . implode(' · ', $held),
        );
    }

    /**
     * What a page of the backlog is a page of, said before the rows.
     *
     * The size of the set leads, because a page read as the backlog is a triage
     * that believes it has seen it — the tracker's enumeration owes the same
     * sentence and for the same reason. What follows it is the one thing the
     * ordering cannot supply: of the five oldest open core changes measured on
     * 2026-08-25, three were over 250 lines and three no longer merged, so age
     * on its own is the opposite of what a review session is looking for
     * (`D-ANS-107`). Separated from `answer()` so it can be held without a
     * review server.
     *
     * @param array<string, mixed> $backlog
     * @param string $reviewableBy the person the enumeration was asked to leave
     *     out, whose misspelling widens this answer rather than emptying it
     * @return list<string>
     */
    public static function page(array $backlog, int $shown, string $reviewableBy = ''): array
    {
        $lines = [sprintf(
            '%d of %d open core changes, %s first.',
            $shown,
            $backlog['read'],
            $backlog['order'] === 'stale' ? 'longest untouched' : 'oldest pushed',
        )];
        if (!$backlog['complete']) {
            $lines[] = sprintf(
                'The read stopped at %d matches, so that is one end of the set rather than all of it. Narrow the '
                    . 'filters before reading these as the oldest changes there are.',
                $backlog['read'],
            );
        } elseif ($shown < $backlog['read']) {
            $lines[] = 'This is a page and not the set, and limit stops at 25. What comes after it is reached by a '
                . 'narrower filter — a smaller maxSize, a branch, an earlier updatedBefore — rather than by a larger '
                . 'limit, because more of one order is more of the same end.';
        } else {
            $lines[] = 'That is the whole set on these filters.';
        }
        if ($reviewableBy !== '') {
            // The trap the empty-answer caveat cannot carry: this filter fails
            // wide. A name the review server cannot place takes nothing out, and
            // the answer is then the backlog wearing the shape of a filtered one
            // — `D-ANS-109`.
            $lines[] = sprintf(
                'What "%s" pushed and voted on is out of this. A name the review server cannot place takes nothing '
                    . 'out, so where a change of theirs is in the list above, the spelling reached nobody rather than '
                    . 'this being everything left.',
                $reviewableBy,
            );
        }
        $lines[] = 'Age is a candidate and never a finding. The oldest changes are regularly the largest and the ones '
            . 'that no longer merge, so maxSize and mergeable are what turn this order into a shortlist — and what a '
            . 'change is actually waiting on is read by passing its number back as change, which answers the votes '
            . 'with their voters and the comments with their threads.';
        $lines[] = '`typo3-core-patch-review` is the workflow a change picked off this page opens, and '
            . '`typo3-core-patch-checkout` is what gets the patch set into a checkout. Open the one this task is '
            . 'before reading a diff.';

        return $lines;
    }

    /**
     * The size, the merge and the age of one change, on the line a page is
     * scanned by.
     *
     * The three readings a review candidate is picked by, which the two reports
     * behind `D-ANS-107` each scored by hand: how much there is to read, whether
     * it still applies, and how long it has been waiting. Nothing is printed for
     * a field the review server stated nothing for, because a size of zero and
     * an unstated size are different claims. Separated from `answer()` so it can
     * be held without a review server.
     *
     * @param array<string, mixed> $entry
     */
    public static function standing(array $entry): string
    {
        return implode(' · ', array_filter([
            $entry['insertions'] === null && $entry['deletions'] === null
                ? ''
                : sprintf('+%d -%d', $entry['insertions'] ?? 0, $entry['deletions'] ?? 0),
            match ($entry['mergeable']) {
                true => 'merges',
                false => 'no longer merges',
                default => '',
            },
            $entry['unresolvedCommentCount'] > 0
                ? sprintf(
                    '%d unresolved thread%s of %d comments',
                    $entry['unresolvedCommentCount'],
                    $entry['unresolvedCommentCount'] === 1 ? '' : 's',
                    $entry['commentCount'],
                )
                : '',
            $entry['created'] !== '' ? 'pushed ' . substr($entry['created'], 0, 10) : '',
        ]));
    }

    /**
     * What a relation chain is, and which of the two relations in this answer
     * it is.
     *
     * Read as the other one, a chain would say the Change-Id was the whole of
     * the work — so the paragraph the pair gets under `D-ANS-080` is what this
     * one sits beside, and neither says what the other says. The staleness
     * sentence is printed only where an entry is behind, because it is a
     * warning about entries in this answer rather than a property of chains.
     * Separated from `answer()` so it can be held without a review server.
     *
     * @return list<string>
     */
    public static function relations(bool $moved): array
    {
        $lines = ['', 'A relation chain is a stack of different changes built on one another, listed child first: '
            . 'what stands above a change is stacked on it, and what stands below it is what it is built on. Each '
            . 'entry\'s status is that entry\'s own, so a MERGED entry says that change landed and says nothing '
            . 'about the change you asked for. Gerrit relates a chain by the commits, which is not the Change-Id '
            . 'relation a backport keeps, and neither set contains the other.'];
        $lines[] = '';
        $lines[] = 'What stands above a change is evidence about the shape of the change itself: a namespace '
            . 'holding one class, a class left non-final, a service declared public with no caller in this patch. '
            . 'Read those entries before reporting any of that, because groundwork for the next change in the '
            . 'stack reads exactly like an oversight in this one.';
        if ($moved) {
            $lines[] = '';
            $lines[] = 'An entry chained at an earlier patch set than it stands at now has moved on since the '
                . 'stack was built on it. Read it by its number rather than acting on the patch set the chain '
                . 'names.';
        }

        return $lines;
    }

    /**
     * Whether the stack holds an earlier patch set of this entry than the one
     * it stands at now.
     *
     * @param array<string, mixed> $related
     */
    private static function behind(array $related): bool
    {
        return $related['chainedAt'] > 0 && $related['chainedAt'] < $related['patchSet'];
    }

    /**
     * The stack the change sits in, where there is one.
     *
     * A change read alone says a feature exists; the stack under it says what
     * the feature consists of, which parts landed and which were given up
     * (`D-ANS-094`). Nothing is printed for a change standing alone, which is
     * the ordinary case rather than a finding. Separated from `answer()` so it
     * can be held without a review server.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the chain was asked for, which only a change
     *                   read by name does
     * @return list<string>
     */
    public static function chain(array $entry, bool $read): array
    {
        if ($entry['chain'] === null) {
            return $read
                ? ['', 'The relation chain of this change could not be read: the review server answered the change '
                    . 'and not what it is stacked on, so this says nothing about whether there is a stack.']
                : [];
        }
        if ($entry['chain'] === []) {
            return [];
        }

        $place = array_search(true, array_column($entry['chain'], 'thisChange'), true);
        $lines = ['', $place === false
            ? sprintf('### Relation chain (%d changes)', count($entry['chain']))
            : sprintf(
                '### Relation chain (%d changes, %d stacked on this one and %d under it)',
                count($entry['chain']),
                $place,
                count($entry['chain']) - $place - 1,
            )];
        foreach ($entry['chain'] as $related) {
            $said = [sprintf('%d · %s · %s', $related['number'], $related['status'], $related['subject'])];
            if ($related['thisChange']) {
                $said[] = 'this change';
            }
            if (self::behind($related)) {
                $said[] = sprintf('chained at patch set %d, now at %d', $related['chainedAt'], $related['patchSet']);
            }
            $said[] = $related['url'];
            $lines[] = '- ' . implode(' · ', $said);
        }

        return $lines;
    }

    /**
     * The issues the commit message names, where it was read.
     *
     * The trailer is said with each of them, because what a patch closes and
     * what it touches are different claims and a reader acting on the second as
     * the first reports work that is not being done here. Nothing is printed
     * for a message naming none, which a change outside the core project is
     * ordinarily. Separated from `answer()` so it can be held without a review
     * server — `D-ANS-098`.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the commit message was asked for, which an
     *                   read by name does
     * @return list<string>
     */
    public static function issues(array $entry, bool $read): array
    {
        if ($entry['issues'] === null) {
            return $read
                ? ['', 'The commit message of this change did not come back, so nothing here says which issues it '
                    . 'names. The review page carries it.']
                : [];
        }
        if ($entry['issues'] === []) {
            return [];
        }

        $lines = ['', sprintf('### Issues named in the commit message (%d)', count($entry['issues']))];
        foreach ($entry['issues'] as $named) {
            $lines[] = sprintf(
                '- %s #%d — %s',
                $named['trailer'],
                $named['issue'],
                implode(' · ', array_filter([$named['tracker'], $named['status'], $named['subject'], $named['url']])),
            );
        }

        return $lines;
    }

    /**
     * The commit message of the change, whole.
     *
     * One of the four things a review is told to establish, and until now
     * reachable only by fetching the patch set: the answer carried the subject
     * and the readings taken off the message, and a caller holding those cannot
     * check a trailer (`D-ANS-112`). Nothing is printed where it was not read —
     * the issues section below says that once, and a second sentence for one
     * silence is two. Separated from `answer()` so it can be held without a
     * review server.
     *
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    public static function commitMessage(array $entry): array
    {
        $message = $entry['message'] ?? null;
        if (!is_string($message) || trim($message) === '') {
            return [];
        }

        return ['', '### Commit message', '', self::quoted(rtrim($message))];
    }

    /**
     * The paths this patch set touches, each with what it does to one.
     *
     * The first thing a review establishes and the one a session went to the
     * checkout for: eight open changes were fetched into the user's own working
     * tree to triage a shortlist none of them was reviewed from (`D-ANS-112`).
     * The list is printed whole — the median open core change touched 5 files
     * when this was measured, and a page of it would be a cap on the one thing
     * the answer is here to carry. Separated from `answer()` so it can be held
     * without a review server.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the paths were asked for, which only a change
     *                   read by name does
     * @return list<string>
     */
    public static function touches(array $entry, bool $read): array
    {
        if ($entry['files'] === null) {
            return $read
                ? ['', 'The paths this patch set touches could not be read: the review server answered the change '
                    . 'and not its files, so nothing here says what the patch is about.']
                : [];
        }
        if ($entry['files'] === []) {
            return [];
        }

        $lines = ['', sprintf('### Files (%d)', count($entry['files']))];
        foreach ($entry['files'] as $file) {
            $said = [$file['action'] . ' ' . $file['path']];
            if ($file['movedFrom'] !== null) {
                $said[] = 'from ' . $file['movedFrom'];
            }
            // A binary carries no line counts, and printing its two zeros is
            // the answer saying the file was left alone.
            $said[] = $file['binary']
                ? 'binary'
                : sprintf('+%d -%d', $file['insertions'], $file['deletions']);
            $lines[] = '- ' . implode(' · ', $said);
        }

        return $lines;
    }

    /**
     * That this patch set carries git conflict markers, beside the line a
     * reader decides on.
     *
     * The fact the default answer hid: change 95412 was cherry-picked through
     * the web UI, landed with the markers committed into a shipped JavaScript
     * file, and read as a fresh patch set in every field beside this one
     * (`D-ANS-121`). So the line says which of the two it is rather than naming
     * the paths and leaving the reading to a caller who has no other sign of it.
     * Nothing is printed for a patch set carrying none, which is what almost
     * every change is. Separated from `answer()` so it can be held without a
     * review server.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the review log was asked for, which only a
     *                   change read by name does
     * @return list<string>
     */
    public static function conflicts(array $entry, bool $read): array
    {
        $named = $entry['conflicts'] ?? null;
        if (!is_array($named)) {
            return $read
                ? ['The review log of this change could not be read, and Gerrit reports a patch set carrying git '
                    . 'conflict markers there and nowhere else — so nothing here says whether this one does.']
                : [];
        }
        if ($named === []) {
            return [];
        }

        return [sprintf(
            'Git conflicts in this patch set: %s. Gerrit reported them when the patch set was created, so the '
                . 'markers are committed lines in those files and the patch is broken rather than merely '
                . 'unreviewed.',
            implode(', ', $named),
        )];
    }

    /**
     * The change this one was cherry-picked from, where it was one.
     *
     * Provenance rather than a warning, and said plainly for that reason: a
     * cherry-pick is how a backport is ordinarily made, and 133 of 400 recent
     * merged core changes are one (`D-ANS-121`). What says a patch set is broken
     * is the line above. Separated from `answer()` so it can be held without a
     * review server.
     *
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    public static function cherryPick(array $entry): array
    {
        $picked = $entry['cherryPickOf'] ?? null;
        if (!is_array($picked)) {
            return [];
        }

        return [sprintf(
            'Cherry-picked from change %d patch set %d · %s',
            $picked['change'],
            $picked['patchSet'],
            $picked['url'],
        )];
    }

    /**
     * The branches this change's commit message claims, on the line under it.
     *
     * The fact the reporting session reached last, after four git calls per
     * commit, and reached only after telling the user a release set the trailer
     * contradicted (`D-ANS-106`). Nothing is printed where the trailer names
     * none, which every change outside the core project is, and nothing where
     * the message was not read — what that silence means is said once below
     * rather than per change. Separated from `answer()` so it can be held
     * without a review server.
     *
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    public static function releases(array $entry): array
    {
        $claimed = $entry['releases'] ?? [];
        if (!is_array($claimed) || $claimed === []) {
            return [];
        }

        return ['Releases: ' . implode(', ', $claimed)];
    }

    /**
     * What a `Releases:` trailer claims and what it does not settle.
     *
     * Two claims stand in this answer and a reader taking the first for the
     * second reports a fix as released where nobody pushed it: a trailer says
     * where the author meant the patch to go, and a change on a branch is a
     * patch that is there (`D-ANS-073`, `D-ANS-106`). Printed once, and only
     * where a trailer was read, because it is about the lines above rather
     * than about trailers. Separated from `answer()` so it can be held without
     * a review server.
     *
     * @param list<array<string, mixed>> $changes
     * @return list<string>
     */
    public static function releaseClaim(array $changes): array
    {
        $claimed = array_filter(
            $changes,
            static fn(array $entry): bool => is_array($entry['releases'] ?? null) && $entry['releases'] !== [],
        );
        if ($claimed === []) {
            return [];
        }

        return ['', 'A `Releases:` line is the author\'s claim about which branches the patch belongs on, written '
            . 'before it went to any of them. What was pushed is the changes above sharing a Change-Id, one per '
            . 'branch and each with a status of its own — so a branch the trailer names with no change targeting it '
            . 'is a backport nobody has pushed, and a merged change is on its branch whatever the trailer says. '
            . 'Which release carries it is neither: that is the first release cut from the branch after the change '
            . 'merged, which this server does not read.'];
    }

    /**
     * The comment threads, and what an absent one means where the change said
     * it has comments.
     *
     * One thread is one heading and the comments under it in the order they
     * were written, because the thread is what carries the state and what the
     * review server counts (`D-ANS-111`). The reading a session made for itself
     * out of the flags and the reply ids is the listing itself.
     *
     * A reply is said to answer somebody only where that is not the comment
     * above it, which is where the order stops saying it — a thread nobody
     * branched says it on every line otherwise. Separated from `answer()` so it
     * can be held without a review server.
     *
     * @param array<string, mixed> $entry
     * @param bool $read whether the review was read for this change, which an
     *                   read by name does
     * @return list<string>
     */
    public static function comments(array $entry, bool $read): array
    {
        if ($entry['comments'] === null) {
            return $read && $entry['commentCount'] > 0
                ? ['', sprintf(
                    'The %d comment%s on this change could not be read: the review server answered the change and '
                        . 'not its comments, so this says nothing about whether one of them is unanswered.',
                    $entry['commentCount'],
                    $entry['commentCount'] === 1 ? '' : 's',
                )]
                : [];
        }
        if ($entry['comments'] === []) {
            return [];
        }

        $by = [];
        $threads = [];
        foreach ($entry['comments'] as $comment) {
            $by[$comment['id']] = $comment['author'];
            $threads[$comment['thread']][] = $comment;
        }
        $open = count(array_filter($threads, static fn(array $thread): bool => $thread[0]['threadUnresolved']));

        $count = count($entry['comments']);
        $lines = ['', sprintf(
            '### Comments (%d comment%s in %d thread%s, %s)',
            $count,
            $count === 1 ? '' : 's',
            count($threads),
            count($threads) === 1 ? '' : 's',
            $open === 0 ? 'none unresolved' : sprintf('%d unresolved', $open),
        )];
        foreach ($threads as $thread) {
            $head = $thread[0];
            $said = [$head['threadUnresolved'] ? 'Unresolved' : 'Resolved', $head['author']];
            if ($head['file'] !== '/PATCHSET_LEVEL') {
                $said[] = $head['line'] === null ? $head['file'] : $head['file'] . ':' . $head['line'];
            }
            $lines[] = '';
            $lines[] = '#### ' . implode(' · ', $said);
            foreach ($thread as $at => $comment) {
                $wrote = [$comment['author'], 'patch set ' . $comment['patchSet']];
                if ($at > 0 && $comment['inReplyTo'] !== $thread[$at - 1]['id'] && isset($by[$comment['inReplyTo']])) {
                    $wrote[] = 'answering ' . $by[$comment['inReplyTo']];
                }
                $lines[] = '';
                $lines[] = '- ' . implode(' · ', $wrote);
                $lines[] = self::quoted($comment['message']);
            }
        }

        return $lines;
    }

    /**
     * What somebody wrote, indented under the line that says who and where.
     *
     * The blank line inside a message keeps no indent, because trailing spaces
     * are what a diff and a terminal both show and neither is what the message
     * says.
     */
    private static function quoted(string $said): string
    {
        return implode("\n", array_map(
            static fn(string $line): string => $line === '' ? '' : '  ' . $line,
            explode("\n", $said),
        ));
    }

    /**
     * The review log, where it was asked for.
     *
     * @param array<string, mixed> $entry
     * @return list<string>
     */
    private static function log(array $entry, string $messages): array
    {
        if ($entry['messages'] === null || $entry['messages'] === []) {
            return [];
        }

        $lines = ['', sprintf(
            '### Review log (%d messages, %d %s)',
            count($entry['messages']),
            (int) ($entry['botMessageCount'] ?? 0),
            $messages === 'people' ? 'more a service user wrote held back' : 'of them a service user\'s',
        )];
        foreach ($entry['messages'] as $message) {
            $lines[] = '';
            $lines[] = sprintf('- %s · %s · patch set %d', $message['on'], $message['author'], $message['patchSet']);
            $lines[] = self::quoted($message['message']);
        }

        return $lines;
    }

    /**
     * What the same query answers by hand and this one does not.
     *
     * The `query` field is there so the question can be asked again outside
     * this server, and a hand-run one comes back with more than this — so what
     * was held back is said, rather than left as a difference the caller finds
     * and reads as this answer being short.
     */
    private static function held(int $dropped): string
    {
        return sprintf(
            '%d change%s the review server matched by its own change number rather than by its commit message %s '
                . 'held back. The number a query carries is indexed both ways there, so a search for an issue '
                . 'answers with the change of the same number whatever it is about.',
            $dropped,
            $dropped === 1 ? '' : 's',
            $dropped === 1 ? 'was' : 'were',
        );
    }
}
