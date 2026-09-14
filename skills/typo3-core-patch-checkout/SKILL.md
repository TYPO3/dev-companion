---
name: typo3-core-patch-checkout
description: 'Get a patch under review on review.typo3.org into a core checkout and out again — onto the branch it targets, into a git worktree beside it, cherry-picked onto current code, or as the base for extending somebody else''s change. Trying one out, seeing whether it still applies, leaving the checkout clean. Pushing is typo3-core-patch-development.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Checkout

Put one change under review into the checkout, in the state it is in. Keep this
skill as routing and stopping rules. The refs, the remotes and the commands are
lookups. A copy of them here goes stale in somebody else's checkout, and nothing
reports it.

This is a workflow of its own because of what it must not do. A patch that no
longer applies is a finding: the branch moved under a change nobody rebased. A
session that quietly resolves its way to something that compiles has destroyed
that finding. It has produced a patch nobody wrote. So every step below has an
end, and to reach one is a result.

**One change, because the work needs it on disk.** What a change touches, what
its message says and where its review stands come back without a fetch. That is
step 2 below. So you triage a shortlist before you open this workflow at all.
You reach the checkout for the one change that survived the triage.

Afterwards nobody can tell refs you pulled in for a read from the branches with
the reader's own work. One session fetched eight open changes into somebody's
checkout, and the user stopped it over them.

## Establish the change before you touch the checkout

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in.
2. `typo3_gerrit_lookup` with the change number or the Change-Id. Use the issue
   number where you reached the change through its issue. Four things it answers
   decide everything below.

   The **branch the change targets** is what you apply it onto. It is regularly
   not the one you stand on. The **patch set that is current on the server**
   matters because an older one is still fetchable. An older one silently
   reviews a revision nobody looks at. The **status** matters since MERGED and
   ABANDONED are both answers that end the work. The **commit** says afterwards
   whether the checkout holds the revision under review.
3. `typo3_rule_lookup` for the Gerrit workflow. It carries the ref you fetch a
   patch set by. That is the one thing about a core change fetch nobody can
   guess. It says which remote the ref is on, which is not the one the checkout
   fetches from. It says the form the third way in below takes. It says what a
   patch set opened on somebody else's change owes its author.

   Those are sections of one page, and a search returns the one your words
   matched. So read it whole where the fetch is the task, and always on the
   fourth way in. That is `typo3_rule_lookup` with
   `documentId="core/contribution/gerrit-workflow"`, which also stands as
   `typo3://guides/core/contribution/gerrit-workflow`.

## Four ways in

The patch goes onto the branch it targets in the checkout you stand in, or into
a worktree beside it. Or it goes onto current code as a commit of your own. Or
it goes under work of your own, as the base you carry it on. The request usually
says which.

The first two hold the patch as its author wrote it. Whether the checkout is
free decides between them. The branch path needs it to itself. A worktree leaves
the current branch and everything uncommitted on it alone.

A worktree does not save that work, it moves it. It starts without the installed
dependencies, which git ignores and so does not bring. So no suite runs in it
until you install them there.

`typo3_test_run_guide` states that precondition above its suites. Beside it
stands the one check whose file list comes from git. In a worktree it reports
success after it read nothing. Read both before you run anything in one.

The third answers a different question: whether the change still applies to
current code and still passes there. "Cherry-pick it onto main" asks for it. It
takes the patch off the code its author wrote it on. So what the checkout then
holds is a commit this session made, not the revision under review.

That commit gets a name, `review/<change number>`, because the rest of the work
reads it. A reader can tell a named branch from the checkout's own work. A
reader can find it again after anything moves, and remove it on purpose. Where
the checkout is not free, this way in takes the worktree as the second does. You
create the branch there.

The fourth is the one the other three read as an obstacle. The patch is the
base, and work of your own goes on top of it. "Extend their patch with ours",
"amend somebody else's change" and "add this to the change" all ask for it. What
comes out is a further patch set on that change rather than a change of yours.
It uses the third's branch, started at the fetched patch set instead of at
current code. The section below says what it owes before you commit anything.

## One change, or a chain of them

`typo3_gerrit_lookup` answers a `chain` with every change read by name. A chain
longer than one open link changes the whole of what follows. A large refactoring
arrives this way, and the request says so. "Rebase the chain" names it outright.

Read the chain before the fetch and take four things off it.

- **Which links are still open.** You have to carry those, and their number is
  how many commits the move is. The merged ones below them are already on the
  branch, or should be.
- **Whether the merged link below is an ancestor.** `chainedAt` against
  `patchSet` on that link says it. A link that shows `chainedAt` 18 and
  `patchSet` 23 merged as a revision the chain never sat on. So
  `git merge-base --is-ancestor` answers no, and a share of the conflicts below
  follow from that one difference. Nothing in a checkout says it.
- **The move is a range.** Where one change is `git cherry-pick FETCH_HEAD`, a
  chain is `git cherry-pick BASE..TIP`. `git rebase --onto origin/main BASE TIP`
  is the same thing. The branch keeps the `review/<change number>` convention.
  It takes the number of the **tip** change, which is the one the request named.
- **Every later amend is two steps.** You cannot amend a conflict resolution or
  a fixer finding that belongs to the lower commit from the tip. Detach to it,
  amend, apply again what sat on top, and move the branch. Budget for it rather
  than discover it at the end.

The stopping rules below are for somebody else's patch, and a chain is regularly
the requester's own. The answer's owner field settles whether the change owner
is the person who asks. Where they are, the rule that stops you from an absent
author's decisions has nothing left to protect. To resolve past a handful of
hunks is the right call. Say in the result that you did, and on whose change.

## Before you change the checkout

Establish these three, in this order, and stop at the first that fails. The
first and the third are about the working copy the patch goes into. On the
worktree path that is the worktree.

- **The working tree is clean.** Afterwards nobody can tell uncommitted work and
  a fetched patch set on top of each other apart. What a carry does to the
  mixture is not recoverable from the checkout alone. Stop and say what the
  uncommitted work is. Do not stash it as a convenience.

  Where that uncommitted work is what stands in the way, the worktree is the way
  past it.

  On the fourth way in it is the material instead. Commit it where it stands,
  before you fetch anything. Say in the answer which branch and which commit
  that is. Then it has a base, which is what the file by file rule below reads
  against.
- **The target branch is there and current.** A change that targets a release
  branch, carried onto the wrong one, produces conflicts. Those are an artefact
  of the mistake, and they look exactly like a stale patch.
- **Where you are is where you can get back to.** Write down the commit the
  checkout is on before anything moves it. A worktree moves nothing, so what you
  write down there is where the worktree is. To remove it is the whole of the
  undo.

## Fetch and apply

Fetch the patch set the server says is current, and put the checkout on it. It
is somebody else's commit. It belongs on no local branch of yours for as long as
it is still that commit. The detached checkout and the worktree say that.

The fetch is the same on every path, and only its destination differs. That is
the checkout detached onto the commit, or a worktree on it while your branch
stays where it is. Or it is the branch named for the change with the commit
carried onto it. Or it is that same branch started at the commit itself.



Then establish what you hold before you judge anything about it. The checkout's
commit is the change's current revision, or it is not, and only the second needs
an explanation. A carry onto current code is the second by construction. The
section below says what you have to say instead.

## Carry it onto current code only where the work needs it

Carry the patch when the change does not sit on current code and the work needs
it to. That is when you run the suites or read it against code that has since
changed. It is also when the user asked for the carry. Not as a matter of
course. A patch read against the code its author wrote it on is the patch its
author wrote. A move is a step that can go wrong.

A rebase of the fetched commit and a cherry-pick onto current code are one move
under two names. A core patch is exactly one commit. A chain is the range above.
Either way, what comes out is a commit made here that exists nowhere else. That
is why it sits on a branch named for the change, and why the undo below deletes
that branch. The command form belongs to the page the fetch is on.

Where it applies clean, say so, and only that. A clean rebase is not a patch
that still holds. Where the change rewrites, moves or deletes a class, the
rebase reverts a fix `main` landed there. There is no conflict to show for it.
The files are different ones.

`typo3_rule_lookup` with `documentId="core/contribution/rebasing-a-stale-patch"`
holds the four steps that settle it. You owe them before the result says the
patch survived.

Say which commit every result after this is about. The carried commit's hash is
not the patch set's. A finding that quotes the local one without a word about
that names a revision nobody else can look up.

Where it conflicts, [references/checklist.md](references/checklist.md) decides
whether you resolve or stop, one conflict at a time. Read it at the first
conflict rather than after you resolved a few. The rule it carries is about what
you may know, and you cannot apply it backwards.

## Carry your own work onto the patch set

The ask comes first, and the author answers it. To extend a change under review
is ordinary practice, and you ask before you do it. So a session that does not
know whether somebody made the ask makes it rather than assumes it.

Where the ask has no answer, the answer is a comment on the change. The same
holds where what you would change is the author's own decision rather than a
correction. This workflow then stops with what it found. That is a result and
not a failure to deliver.

The page step 3 reads whole says the rest. It says what the amend does to the
author and to the committer. It says what the upload owes the author on the
change itself. It says what stays fixed whoever pushes. Read it before the first
commit and not before the push. The ask is not a step you can take afterwards.

Then the work moves one file at a time, and the rule is the one that already
decides a conflict. The checklist carries both halves. It says whether a file of
yours can go onto the patch whole. It says whether a hunk where the two collide
is yours to write.

Say what came from where. Say the patch set the branch started at and the commit
your own work was on. Say which files came from which side. The author will read
the diff between two patch sets, and that diff says what moved and never why.

**Once the result stands, invoke `typo3-core-patch-development`.** The push
belongs to that workflow. The amend, the message and the question of what goes
up visible to everyone are its steps. What crosses over is the change number,
the patch set the branch started at, and the result's branch. It is every
decision you took on the author's behalf. That last one is what the comment on
the change has to carry.

## Stopping is the normal ending

When a rule above ends the work, undo what you started rather than leave the
checkout half-way. The section below is that undo, and it is the same one that
ends a run that went fine. A checkout left half-way through a carry is a trap
for whoever opens it next, you included.

Report what you found and not what you attempted. Report the change, its patch
set, its target branch, how far it got, and the specific thing that stopped it.
That is the files that conflicted, the hunks, and why the change alone did not
decide them. That report is the useful outcome. Somebody who rebases the patch
properly starts from it.

## Once it is in and applies

`typo3_test_run_guide` with the paths the change touches names the suites that
can fail on it and their targeted invocations. Run them through the checkout's
own runner. A suite run through an installed binary is a result nobody can
reproduce. A check that inspected no files is not a green. The second is what a
worktree does by default. The same answer says what you have to install there
before any suite runs at all.

Say which branch and which patch set every result is about. Say which working
copy it ran in where that was a worktree, and which commit where you carried the
patch. A green reported without them has no owner the moment somebody pushes a
new patch set.

## Put the checkout back

A checkout on somebody's patch set is not a state to leave behind. It is not a
state to start the next piece of work from either. To restore it is a step of
its own, whether the patch applied or stopped. On the branch path it is these
six, in this order, because each part makes the next one possible.

There is one ending it does not run for, and it is the handover above. The work
continues on the review branch. So steps 2, 3, 5 and 6 would move the checkout
off the result or delete it.

What that ending owes is steps 1 and 4 and then the answer. That is the branch,
the commit, and what on it is not the author's. It is that you left the checkout
there on purpose. Neither "restored" nor silence is what such a session reports.

1. **End whatever is in progress first.** A carry that is half applied, or one
   that stopped in a conflict, owns the tree until you abort it. Every later
   step fails against it in a way that reads like something else.
2. **Return to the branch you recorded at the start**, not to whichever branch
   looks right. Where you only fetched the patch, it was on no branch. So to
   leave it loses nothing that is not still on the review server. Say the commit
   in the answer, because that is the only local name it had.
3. **Delete the branch you carried the patch onto**, where there was one. Git
   refuses the ordinary deletion, and the forced one gets past that refusal
   rather than answers it. `typo3_gerrit_lookup` with `commit` and the tip the
   branch is on answers it. A commit somebody pushed comes back as the change it
   is a patch set of, superseded patch sets included. The ref beside it is the
   undo, and you say it in the answer before the branch goes.

   An empty answer is a commit nobody pushed, or one an anonymous reader may not
   see. Nothing brings that one back. A carried commit is the second kind by
   construction. So read what is about to go: what you resolved in a conflict is
   in it and in nothing else. Ask the same of any other ref this workflow has to
   remove. Nothing says in advance which of the two it is.

   Then say in the answer that the branch is gone. "The checkout is back on its
   branch" is true with the review branch still beside it.
4. **Establish that nothing of the patch remains.** An aborted carry can leave
   files the change added untracked. They belong to no commit and to no branch.
   The next suite run picks them up and fails for a reason that has nothing to
   do with anything. What the working tree holds and what git does not track are
   two different questions, and you ask both.
5. **Update the branch from the remote it fetches from, not from the review
   server.** These are two different URLs on a core clone. `typo3_rule_lookup`
   for the Gerrit workflow says which is which. The change refs live on only one
   of them. Take the update as a fast-forward. A merge commit on a local branch
   that tracks the core is a state nothing here asked for.
6. **Bring the installed dependencies back in step with the branch.** A move
   between a patch set and current code can change what the lock file pins. A
   suite run against the other revision's dependencies fails for a reason that
   is not in the diff. This is the step people skip and then spend an hour on as
   a test failure.

The worktree path ends shorter, and the difference is not a shortcut. Nothing
moved the branch, so steps 2, 5 and 6 have nothing to put back. They are about a
checkout that went somewhere.

What remains is to end whatever is in progress and then remove the worktree. Git
refuses that while anything in it has a modification or no tracking. That
refusal is step 4 one layer out. To force past it throws away the only copy of
whatever you resolved in there. Step 3 stands there too, because a removed
worktree leaves the branch you created it on behind.

Say the end state in the answer: which branch, which commit, and that the tree
is clean. On the worktree path, say that the worktree is gone. Or say where it
still is and why you kept it on purpose. Where you carried the patch, say that
you deleted the branch you carried it on. "Restored" without those is the claim
rather than the result.

This skill owns how a change under review gets into a checkout and back out of
it. What that covers, in order:

- Find it, and fetch the patch set.
- Put it on the branch it targets, into a worktree beside it, or onto current
  code on a named branch. Or put it under work of your own where you extend that
  change.
- Resolve what the change itself decides, and stop where it does not.
- Leave behind a clean branch current with its remote, no worktree of its own
  and no branch it made. It owns the undo as much as the do, and the undo runs
  whichever way the rest went. The one exception is the branch it hands over,
  which is the result.

It does not own the judgement of the patch. Where the request is to say what is
wrong with it, `typo3-core-patch-review` owns that. That skill starts from the
working copy this one leaves the patch in, before the undo runs. That is the
worktree, the checkout or the review branch. Carry across which commit the
findings will be about.

It does not own a change to what is on the review server either. To amend a
change into a new patch set and push it belongs to
`typo3-core-patch-development`. Carry over the change number and the patch set
you fetched. Carry over whether you had to carry it onto current code to apply.
Carry over what you decided on the author's behalf.
