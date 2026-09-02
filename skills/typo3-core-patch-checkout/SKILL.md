---
name: typo3-core-patch-checkout
description: 'Get a patch under review on review.typo3.org into a core checkout and out again — onto the branch it targets, into a git worktree beside it, cherry-picked onto current code, or as the base for extending somebody else''s change. Trying one out, seeing whether it still applies, leaving the checkout clean. Pushing is typo3-core-patch-development.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Checkout

Put one change under review into the checkout, in the state it is actually in.
Keep this skill as routing and stopping rules; the refs, the remotes and the
commands are lookups, and a copy of them here goes stale in somebody else's
checkout with nothing to report it.

The reason this is a workflow of its own is what it must not do. A patch that no
longer applies is a finding — the branch moved under a change nobody rebased —
and a session that quietly resolves its way to something that compiles has
destroyed that finding and produced a patch nobody wrote. So every step below
has an end, and reaching one is a result.

**One change, because the work needs it on disk.** What a change touches, what
its message says and what its review stands at all come back from the review
server without a fetch, which is step 2 below — so a shortlist is triaged before
this workflow is opened at all, and the checkout is reached for the one change
that survived the triage. Refs pulled in to decide what to read cannot be told
apart afterwards from the branches the reader's own work sits on: one session
fetched eight open changes into somebody's working checkout and was stopped over
them.

## Establish the change before touching the checkout

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_gerrit_lookup` with the change number or the Change-Id, or with the
   issue number where the change was reached through its issue. Four things it
   answers decide everything below: the **branch the change targets**, which is
   what it has to be applied onto and is regularly not the one you are standing
   on; the **patch set that is current on the server**, because an older one is
   still fetchable and applying it silently reviews a revision nobody is looking
   at; the **status**, since MERGED and ABANDONED are both answers that end the
   work; and the **commit**, which is what says afterwards whether the checkout
   holds the revision under review.
3. `typo3_rule_lookup` for the Gerrit workflow. It carries the ref a patch set
   is fetched by, the one thing about fetching a core change that is not
   guessable — which remote the ref is on, which is not the one the checkout
   fetches from — the form the third way in below takes, and what a patch set
   opened on somebody else's change owes its author. Those are sections of one
   page and a search returns the one your words matched, so read it whole where
   the fetch is the task, and always on the fourth way in: `typo3_rule_lookup`
   with `documentId="core/contribution/gerrit-workflow"`, which also stands as
   `typo3://guides/core/contribution/gerrit-workflow`.

## Four ways in

The patch goes onto the branch it targets in the checkout you are standing in,
into a git worktree beside it, onto current code as a commit of your own, or
under work of your own as the base it is carried on. The request usually says
which.

The first two hold the patch as its author wrote it, and what decides between
them is whether the checkout is free: the branch path needs it to itself, and a
worktree leaves the current branch and everything uncommitted on it alone.

A worktree does not save that work, it moves it. It starts without the installed
dependencies, which are gitignored and so are not something git brings, and no
suite runs in it until they are installed there. `typo3_test_run_guide` states
that precondition above its suites, and beside it the one check whose file list
comes from git, which reports success having read nothing in a worktree. Read
both before running anything in one.

The third answers a different question — whether the change still applies to
current code and still passes there — and it is what "cherry-pick it onto main"
asks for. It takes the patch off the code it was written on, so what the
checkout then holds is a commit this session made and not the revision under
review. That commit gets a name, `review/<change number>`, because the rest of
the work reads it: a named branch can be told apart from the checkout's own
work, found again after anything moves, and removed on purpose. Where the
checkout is not free, this way in takes the worktree as the second does, and the
branch is created there.

The fourth is the one the other three read as an obstacle: the patch is the base
and work of your own goes on top of it. "Extend their patch with ours", "amend
somebody else's change" and "add this to the change under review" all ask for
it, and what comes out is a further patch set on that change rather than a
change of yours. It uses the third's branch, started at the fetched patch set
instead of at current code, and the section below is what it owes before
anything is committed.

## Before the checkout is changed

Establish these three, in this order, and stop at the first that fails. The
first and the third are about the working copy the patch is going into, which on
the worktree path is the worktree.

- **The working tree is clean.** Uncommitted work and a fetched patch set on top
  of each other cannot be told apart afterwards, and what carrying it does to
  the mixture is not recoverable from the checkout alone. Stop and say what is
  uncommitted; do not stash it as a convenience. Where that uncommitted work is
  what stands in the way, the worktree is the way past it. On the fourth way in
  it is the material instead: commit it where it stands, before anything is
  fetched, and say in the answer which branch and which commit that is. Then it
  has a base, which is what the file by file rule below is asked against.
- **The target branch is there and current.** A change targeting a release
  branch carried onto the wrong one produces conflicts that are an artefact of
  the mistake, and they look exactly like a stale patch.
- **Where you are is where you will be able to get back to.** Write down the
  commit the checkout is on before anything moves it. A worktree moves nothing,
  so what is written down there is where the worktree is: removing it is the
  whole of the undo.

## Fetch and apply

Fetch the patch set the server says is current, and put the checkout on it. It
is somebody else's commit and belongs on no local branch of yours for as long as
it is still that commit, which is what the detached checkout and the worktree
say. The fetch is the same on every path and only its destination differs: the
checkout detached onto the commit, a worktree created on it while the branch you
were on stays where it is, the branch named for the change with the commit
carried onto it, or that same branch started at the commit itself.

Then establish what you are holding before judging anything about it: the
checkout's commit is the change's current revision, or it is not, and only the
second needs explaining. Carrying it onto current code is the second by
construction, and the section below is what has to be said instead.

## Carry it onto current code only where the work needs it

Carry the patch when the change does not sit on current code and the work needs
it to — running the suites, reading it against code that has since changed, or
because carrying it is what was asked for. Not as a matter of course: a patch
read against the code it was written on is the patch its author wrote, and
moving it is a step that can go wrong.

A rebase of the fetched commit and a cherry-pick onto current code are one move
under two names, because a core patch is exactly one commit. Either way what
comes out is a commit made here that exists nowhere else, which is why it is on
a branch named for the change and why the undo below deletes that branch. The
command form belongs to the page the fetch is on.

Where it applies clean, say so. That is itself an answer about the change: it
still applies.

Say which commit every result after this is about. The carried commit's hash is
not the patch set's, and a finding quoting the local one without saying so is
about a revision nobody else can look up.

Where it conflicts, [references/checklist.md](references/checklist.md) is what
decides whether to resolve or to stop, one conflict at a time. Read it at the
first conflict rather than after resolving a few — the rule it carries is about
what you are allowed to know, and it cannot be applied backwards.

## Carry your own work onto the patch set

The ask comes first and it is the author's to answer. Extending a change under
review is ordinary practice and it is asked for before it is done, so a session
that has not been told the ask was made makes it rather than assumes it. Where
it is unanswered, or where what you would change is the author's own decision
rather than a correction, the answer is a comment on the change and this
workflow stops with what it found. That is a result and not a failure to
deliver.

The page step 3 reads whole is what says the rest: what the amend does to the
author and to the committer, what the upload owes the author on the change
itself, and what stays fixed whoever pushes. Read it before the first commit and
not before the push — the ask is not a step that can be taken afterwards.

Then the work moves one file at a time, and the rule is the one that already
decides a conflict. The checklist carries both halves: whether a file of yours
can go onto the patch whole, and whether a hunk where the two collide is yours
to write.

Say what came from where. The patch set the branch started at, the commit your
own work was on, and which files came from which side: the author is going to
read the diff between two patch sets, and that diff says what moved and never
why.

**Once the result stands, invoke `typo3-core-patch-development`.** Pushing it
belongs to that workflow — the amend, the message and the question of what goes
up visible to everyone are its steps. What crosses over is the change number,
the patch set the branch started at, the branch the result sits on, and every
decision taken on the author's behalf, because that last one is what the comment
on the change has to carry.

## Stopping is the normal ending

When a rule above ends the work, undo what was started rather than leaving the
checkout half-way — the section below is that undo, and it is the same one that
ends a run which went fine. A checkout left half-way through a carry is a trap
for whoever opens it next, including you.

Report what was found and not what was attempted. The change, its patch set, its
target branch, how far it got, and the specific thing that stopped it — the
files that conflicted, the hunks, and why the change alone did not decide them.
That report is the useful outcome, and it is what somebody rebasing the patch
properly starts from.

## Once it is in and applies

`typo3_test_run_guide` with the paths the change touches names the suites that
can fail on it and their targeted invocations. Run them through the checkout's
own runner: a suite run through an installed binary is a result nobody can
reproduce, and a check that inspected no files is not a green. The second is
what a worktree does by default, and what has to be installed there before any
suite runs at all is in the same answer.

Say which branch and which patch set every result is about, which working copy
it ran in where that was a worktree, and which commit where the patch was
carried. A green reported without them is unattributable the moment a new patch
set is pushed.

## Put the checkout back

A checkout sitting on somebody's patch set is not a state to leave behind, and
it is not a state to start the next piece of work from either. Restoring it is a
step of its own, taken whether the patch applied or stopped. On the branch path
it is these six, in this order, because each part makes the next one possible.

There is one ending it does not run for, and it is the handover above: the work
continues on the review branch, so steps 2, 3, 5 and 6 would move the checkout
off the result or delete it. What that ending owes is steps 1 and 4 and then the
answer — the branch, the commit, what on it is not the author's, and that the
checkout was left there on purpose. Neither "restored" nor silence is what such
a session reports.

1. **End whatever is in progress first.** A carry that is half applied, or one
   that stopped in a conflict, owns the working tree until it is aborted, and
   every later step fails against it in a way that reads like something else.
2. **Return to the branch that was recorded at the start**, not to whichever
   branch looks right. Where the patch was only fetched, it was on no branch, so
   leaving it loses nothing that is not still on the review server — but say the
   commit in the answer, because that is the only local name it had.
3. **Delete the branch the patch was carried onto**, where there was one. Git
   refuses the ordinary deletion, and the forced one gets past that refusal
   rather than answering it. `typo3_gerrit_lookup` with `commit` and the tip the
   branch is on is what answers it: a commit somebody pushed comes back as the
   change it is a patch set of, superseded patch sets included, and the ref
   beside it is the undo — say it in the answer before the branch goes. An empty
   answer is a commit nobody pushed, or one an anonymous reader may not see, and
   nothing brings that one back. A carried commit is the second kind by
   construction, so read what is about to go: what was resolved in a conflict is
   in it and in nothing else. Ask the same of any other ref this workflow is
   told to remove, where nothing says in advance which of the two it is. Then
   say in the answer that the branch is gone, because "the checkout is back on
   its branch" is true with the review branch still sitting beside it.
4. **Establish that nothing of the patch is left.** An aborted carry can leave
   files the change added lying untracked, and they belong to no commit and to
   no branch: the next suite run picks them up and fails for a reason that has
   nothing to do with anything. What the working tree holds and what is
   untracked are two different questions and both have to be asked.
5. **Update the branch from the remote it is fetched from, not from the review
   server.** These are two different URLs on a core clone — `typo3_rule_lookup`
   for the Gerrit workflow says which is which — and the change refs live on
   only one of them. Take the update as a fast-forward: a merge commit on a
   local branch tracking the core is a state nothing here asked for.
6. **Bring the installed dependencies back in step with the branch.** Moving
   between a patch set and current code can change what the lock file pins, and
   a suite run against dependencies belonging to the other revision fails for a
   reason that is not in the diff. This is the step that is skipped and then
   spends an hour being diagnosed as a test failure.

The worktree path ends shorter, and the difference is not a shortcut. Nothing
moved the branch, so steps 2, 5 and 6 have nothing to put back — they are about
a checkout that went somewhere. What is left is ending whatever is in progress
and then removing the worktree, which is refused while anything in it is
modified or untracked: that refusal is step 4 arriving one layer out, and
forcing past it throws away the only copy of whatever was resolved in there.
Step 3 stands there too, because removing a worktree leaves the branch it was
created on behind.

Say the end state in the answer: which branch, which commit, and that the tree
is clean; on the worktree path, that the worktree is gone, or where it still is
and why keeping it was deliberate; and where the patch was carried, that the
branch it was carried on is deleted. "Restored" without those is the claim
rather than the result.

This skill owns getting a change under review into a checkout and back out of
it. What that covers, in order:

- Finding it, and fetching the patch set.
- Putting it somewhere: on the branch it targets, into a worktree beside it,
  onto current code on a branch named for the change, or under work of your own
  where that change is being extended.
- Resolving what the change itself decides, and stopping where it does not.
- Leaving behind a clean branch current with its remote, no worktree of its own
  and no branch it made. It owns the undo as much as the do, and the undo is
  what runs whichever way the rest went — the one exception being the branch it
  hands over, which is the result. It does not own judging the patch — where the
  request is to say what is wrong with it, `typo3-core-patch-review` owns that,
  and it starts from the working copy this leaves the patch in, worktree,
  checkout or review branch, before the undo runs; carry across which commit the
  findings will be about. It does not own changing what is on the review server
  either: amending a change into a new patch set and pushing it belongs to
  `typo3-core-patch-development`, and carry over the change number, the patch
  set that was fetched, whether it had to be carried onto current code to apply,
  and what was decided on the author's behalf.
