---
name: typo3-core-patch-checkout
description: 'Get a patch under review on review.typo3.org into a core checkout and out again — onto the branch it targets, into a git worktree beside it, cherry-picked onto current code, or as the base for extending somebody else''s change. Trying one out, seeing whether it still applies, leaving the checkout clean. Pushing is typo3-core-patch-development.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Core Patch Checkout

This skill covers how one change under review gets into a core checkout and back
out of it. Use it to try a patch out, to see whether it still applies, or to
build on somebody else's change. It stops before the push, which
`typo3-core-patch-development` covers. It also stops before a judgement of the
patch, which `typo3-core-patch-review` covers.

**A patch that no longer applies is a finding.** It shows that the branch moved
and nobody rebased the change. Do not guess your way through conflicts until the
code compiles. That produces a patch nobody wrote and destroys the finding. So
every step below can end the work, and such an end is a result.

**Fetch only the one change the work needs.** The review server tells you what a
change touches, its message and its review status without a fetch. So compare
the candidates first, and fetch only the one you chose. Every fetched change
leaves refs behind that later look like the user's own work.

Keep this skill as routing and stop rules: it says which tool to ask and when to
stop. The refs, the remotes and the commands come from the lookups. A copy of
them here would go out of date in somebody else's checkout.

## Steps at a glance

1. Establish the change: its target branch, current patch set and status.
2. Choose one of four ways to check it out.
3. Check the working tree, the target branch and your way back.
4. Fetch the current patch set and apply it.
5. Rebase it onto current code, only where the work needs that.
6. Run the suites that cover the change.
7. Put the checkout back, and report the end state.

With option 4, also follow "Option 4: add your own work to the patch set". When
a rule stops the work, follow "When a rule stops the work".

## 1. Establish the change

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in.

2. Call `typo3_gerrit_lookup` with the change number or the Change-Id. If you
   only have the issue, use the issue number. Read four things from the answer:

   - **The target branch.** Apply the change onto this branch. It is often not
     the branch you are on.
   - **The current patch set.** Older patch sets can still be fetched. If you
     fetch one, you work on a revision nobody reviews.
   - **The status.** MERGED and ABANDONED both end the work.
   - **The commit.** Later you compare it with the checkout. It shows whether
     you hold the revision under review.

3. Read the Gerrit workflow page whole, not through a search. A search returns
   only the section your words matched. Call `typo3_rule_lookup` with
   `documentId="core/contribution/gerrit-workflow"`. It is also served as
   `typo3://guides/core/contribution/gerrit-workflow`. The page gives:

   - the ref you fetch a patch set by, which nobody can guess;
   - the remote that ref is on, which is not the one the checkout fetches from;
   - the command for a rebase onto current code;
   - what the author must get from you when you upload to their change.

4. Call `typo3_task_guide` with the paths the change touches. Step 3 of the base
   order asks for it in every task. Only the answer of step 2 names those paths,
   so make this call after it and before the fetch.

A change can be part of a chain: several changes that build on each other. If
the answer of step 2 has a `chain` with more than one open link, read
[references/chain.md](references/chain.md) before the fetch.

## 2. Choose how to check it out

The request usually says which of the four it wants.

1. **On the target branch.** You get the patch as its author wrote it, in your
   current checkout. This needs a clean checkout.

2. **In a worktree beside the checkout.** You also get the patch as its author
   wrote it. Your current branch and your uncommitted work stay as they are.

3. **Rebased onto current code.** This answers another question: does the change
   still apply and pass on current code? "Cherry-pick it onto main" asks for it.
   The result is a commit you made, not the revision under review. Put it on a
   branch named `review/<change number>`. A reader can tell that branch from the
   user's work, find it again and delete it on purpose. If the checkout is not
   clean, create the branch in a worktree.

4. **As the base for your own work.** "Extend their patch with ours", "amend
   somebody else's change" and "add this to the change" ask for it. The result
   is a further patch set on that change, not a change of your own. Use the
   branch of option 3, but start it at the fetched patch set. Read "Option 4:
   add your own work to the patch set" before you commit anything.

**A worktree needs its own dependencies.** A new worktree has no installed
dependencies, because git does not track them. So no suite runs in it until you
install them there. `typo3_test_run_guide` says this above its suites. The same
answer names a check that gets its file list from git. In a worktree, that check
reports success after it read nothing. Read both before you run anything in a
worktree.

## 3. Before you change the checkout

Check these three, in this order. Stop at the first that fails. On the worktree
path, the first and the third apply to the worktree.

- **The working tree is clean.** Uncommitted work and a fetched patch set cannot
  be told apart afterwards. Stop and say what the uncommitted work is. Do not
  stash it to get it out of the way. Where that work is in the way, use a
  worktree.

  With option 4 the uncommitted work is the material you add. Commit it on its
  current branch, before you fetch anything. Say in the answer which branch and
  which commit that is. The file-by-file rule in the checklist compares against
  it.

- **The target branch is there and current.** A change applied onto the wrong
  release branch produces conflicts. Those look exactly like a stale patch.

- **You know your way back.** Write down the current commit before anything
  moves it. A worktree changes nothing in your checkout. To undo it, you remove
  the worktree.

## 4. Fetch and apply

Fetch the patch set the server reports as current. It is somebody else's commit,
so do not put it on a local branch of yours as it is. The fetch is the same for
all four options. Only the destination differs:

- Option 1: check out the commit detached.
- Option 2: create a worktree on the commit. Your branch stays where it is.
- Option 3: cherry-pick the commit onto a new branch `review/<change number>` at
  current code.
- Option 4: start the branch `review/<change number>` at the commit itself.

Then compare the checkout's commit with the commit from step 1. If they differ,
explain why. With option 3 they always differ, and step 5 says what to report.

## 5. Rebase onto current code only where the work needs it

Rebase when the change does not sit on current code and the work needs it there.
That is when you run the suites or read it against code that changed since. It
is also when the user asks for it. Do not rebase by default. A patch read on its
original base is the patch its author wrote, and every move can go wrong.

A core patch is one commit, so a rebase and a cherry-pick onto current code do
the same. Either way the result is a commit made here that exists nowhere else.
That is why it sits on a named branch, and why the clean-up deletes that branch.
The Gerrit workflow page gives the command.

**A clean rebase.** Say that it applied clean, and nothing more. A clean rebase
does not prove that the patch still works. The change can rewrite, move or
delete a class that `main` fixed in the meantime. Then the rebase can silently
drop that fix. Git shows no conflict, because the patch and the fix change
different files. Before you report that the patch survived, follow the four
steps of `typo3_rule_lookup` with
`documentId="core/contribution/rebasing-a-stale-patch"`.

**Name the commit.** The rebased commit has another hash than the patch set.
Every result after this point is about your commit, so say that. Otherwise a
finding names a revision nobody else can look up.

**A conflict.** Read [references/checklist.md](references/checklist.md) at the
first conflict, not after you resolved a few. It decides, one conflict at a
time, whether you resolve or stop. You cannot check resolutions against it after
the fact.

## 6. Run the suites

`typo3_test_run_guide` with the paths the change touches names the suites that
can fail on it, with targeted commands. Run them through the checkout's own
runner. A suite run through a globally installed binary is a result nobody can
reproduce. A check that inspected no files has not passed. In a worktree, the
check that reads its file list from git does exactly that by default. The same
answer says what to install in a worktree first.

Say which branch and which patch set every result is about. Name the worktree
where you ran in one, and the commit where you rebased. A pass without them
means nothing once somebody pushes a new patch set.

## 7. Put the checkout back

Do not leave the checkout on somebody's patch set, and do not start the next
work from it. Restore it whether the patch applied or a rule stopped the work.

**Exception: the handover to `typo3-core-patch-development`.** There the work
goes on, on the review branch. Steps 2, 3, 5 and 6 below would move the checkout
off the result or delete it. So run only steps 1 and 4. Then report the branch,
the commit, and what on it is not the author's. Say that you left the checkout
there on purpose. Neither "restored" nor silence is a correct report.

On the branch path, run these six steps in this order. Each one makes the next
one possible.

1. **End whatever is in progress.** A half-applied or stopped rebase blocks the
   tree until you abort it. Every later step fails against it in a way that
   looks like something else.

2. **Return to the branch you wrote down at the start**, not to whichever branch
   looks right. Where you only fetched the patch, it was on no branch. The
   review server still has it, so nothing is lost. Name its commit in the
   answer, because that is its only local name.

3. **Delete the branch you rebased the patch onto**, if there is one. Git
   refuses the normal deletion, because the commit is on no other branch. Before
   you force it, call `typo3_gerrit_lookup` with `commit` and the branch tip. A
   pushed commit comes back as the change it belongs to, superseded patch sets
   included. Name the ref from that answer before you delete the branch. With
   that ref, anybody can fetch the commit again.

   An empty answer means nobody pushed the commit, or an anonymous reader may
   not see it. Then nothing brings that commit back. A rebased commit is always
   that case. So read what you are about to delete, because your conflict
   resolutions exist only there. Do the same for any other ref you must remove.

   Then say in the answer that the branch is gone. "The checkout is back on its
   branch" is also true with the review branch still there.

4. **Check that nothing of the patch remains.** An aborted rebase can leave the
   files the change added as untracked files. They belong to no commit. The next
   suite run picks them up and fails for an unrelated reason. Check both what
   the working tree holds and what git does not track.

5. **Update the branch from the remote it fetches from, not from the review
   server.** On a core clone, these are two different URLs. The Gerrit workflow
   page says which is which. Update as a fast-forward. A merge commit on a local
   branch that tracks the core is a state nobody asked for.

6. **Bring the installed dependencies in line with the branch.** The lock file
   can differ between the patch set and current code. A suite run against the
   wrong dependencies fails for a reason that is not in the diff. People skip
   this step and then lose an hour on a test failure.

**On the worktree path** the clean-up is shorter, because nothing moved the
branch. Steps 2, 5 and 6 have nothing to restore. End whatever is in progress,
then remove the worktree. Git refuses that while anything in it is modified or
untracked. That is the check of step 4. If you force the removal, you delete the
only copy of what you resolved there. Step 3 still applies, because a removed
worktree leaves its branch behind.

**Report the end state:** the branch, the commit, and that the tree is clean. On
the worktree path, say that the worktree is gone, or where it is and why you
kept it. Where you rebased, say that you deleted the branch. "Restored" without
these details is a claim, not a result.

Close the answer with the steps of the base order you skipped. Write one line
each: `Not reached: <step> — <why>`.

## Option 4: add your own work to the patch set

Ask the author first. To extend a change under review is normal practice, and
you ask before you do it. If you do not know whether somebody asked, ask.

If the author does not answer, write a comment on the change instead. Do the
same where your change would replace a decision of the author rather than
correct an error. Then stop with what you found. That is a result, not a
failure.

The Gerrit workflow page from step 1 says the rest. It says what the amend does
to the author and the committer. It says what the upload must give the author on
the change. It says what must stay unchanged, whoever pushes. Read it before the
first commit, not before the push. You cannot ask afterwards.

Then move your work onto the patch one file at a time. The checklist decides
this with the same rule as for a conflict. It says whether a file of yours can
go onto the patch whole. It says whether a hunk where the two collide is yours
to write.

Say where everything came from. Name the patch set the branch started at and the
commit your work was on. Say which files came from which side. The author will
read the diff between two patch sets. That diff shows what moved and never why.

**When the result is ready, invoke `typo3-core-patch-development`.** That skill
covers the push, the amend, the message and what becomes public. Hand over the
change number, the patch set the branch started at, and the result's branch.
Hand over every decision you took for the author too. The comment on the change
must name those decisions.

## When a rule stops the work

A stop is a normal end. Undo what you started with the clean-up in step 7, and
do not leave the checkout half-way. A checkout left half-way through a rebase
breaks the next work in it, yours included.

Report what you found, not what you tried. Name the change, its patch set, its
target branch, how far it got, and what stopped it. For a conflict, name the
files, the hunks, and why the change alone did not decide them. The person who
rebases the patch later starts from that report.

## Where this skill stops

- **A judgement of the patch** is `typo3-core-patch-review`. It starts from the
  working copy this skill leaves the patch in, before the clean-up. That is the
  worktree, the checkout or the review branch. Hand over which commit the
  findings will be about.

- **A new patch set on the review server** is `typo3-core-patch-development`.
  Hand over the change number and the patch set you fetched. Hand over whether
  you had to rebase it to apply, and every decision you took for the author.
