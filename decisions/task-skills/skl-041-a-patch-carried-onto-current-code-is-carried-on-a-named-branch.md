---
id: D-SKL-041
title: A patch carried onto current code is carried on a named branch
date: 2026-08-14
status: open
coveredBy:
  - KnowledgeTest::theCarryOntoCurrentCodeNamesTheBranchItLandsOnAndTheUndo
---

# D-SKL-041 — A patch carried onto current code is carried on a named branch

**`typo3-core-patch-checkout` gains a way in whose result is a local branch
named for the change, and an undo that deletes it.**

The two ways in it has are both about a read of the patch as its author wrote
it. So every local result is disposable and the skill says so in as many words.
A request to cherry-pick a change onto current `main` and review it there makes
that local result the thing the work is about. The skill argues against a name
for it.

## Evidence

- `feedback/2026-08-13-214754`: the user asked the session to review change
  93319 and cherry-pick it onto `main`. Neither way in fitted, so it detached
  onto `main` and cherry-picked there, which left a new commit on no branch. The
  maintainer corrected it mid-session: a checkout that cherry-picks onto `main`
  always gets a review branch. The rest of the review ran from `review/93319`.
- `knowledge/task-intents.json` already matches "cherry-pick" and "cherry pick"
  onto `patch-checkout`. The routing fired and the skill it fired into has no
  form for the request, so this is not a routing failure.
- `bin/cli hints:probe "cherry-pick a gerrit patch onto main review branch"`
  matched nothing. The one cherry-pick the corpus documents is a merger's
  backport of a merged commit onto a release branch
  (`knowledge/documents/core/contribution/gerrit-workflow.md`). That is author
  work rather than a read. Nothing here covers a patch set carried locally.
- The detach rationale stands twice — in the skill's "Fetch and apply" and in
  that document — and both state it without its scope. It is about the fetched
  object, and a commit this session made is not one.
- The gap reaches the rebase path the skill already has. "Put the checkout back"
  step 2 says a detached commit left behind "loses nothing that is not still on
  the review server". That stopped to be true the moment a rebase or a
  cherry-pick produced a commit that exists only locally.
- One session behind it: the five feedback of 2026-08-13 are one review of one
  change. What carries the shape is the maintainer's correction, not a count.

## Decided

- Ladder step 1b, the shape is absent, and queued rather than closed on the
  spot. A published skill is a contract installed into somebody else's project,
  where no release here corrects it (`D-SKL-021`).
- The result of a carry of a patch onto current code gets a name. That is the
  boundary. Where the local commit is what the rest of the work reads, it stops
  to be disposable, so it has a branch. The undo deletes that branch explicitly,
  and the answer says the branch is gone rather than that the checkout stands
  attached again.
- The findings say which commit they are about. A cherry-pick's hash is not the
  patch set's, and a review that quotes the local one without a word about it is
  unattributable.
- Rules already here fix where the two halves land. The command form and the
  scope of the detach rationale go to
  `knowledge/documents/core/contribution/gerrit-workflow.md`, because a skill
  carries no command nobody asked the checkout for. The way in, the name and the
  undo go to the skill.
- Priority `normal`. The intents file promises the path. So a session that asks
  for it by name lands in a skill that steers it away from the answer.
- Left to the read: the branch name, and whether the rebase path that exists
  folds into the new way in or stays beside it. Both have a source, the
  contribution guide's cherry-pick page and the skill itself, and this judgement
  settles neither.

## Assumed

- The correction is the practice rather than one review's convenience. It came
  from the person who maintains this repository, which is the strongest source a
  judgement here has and still one session.
- Naming the local result costs less than it saves. A branch that outlives the
  work is a state the next session cannot tell from its own. That is the failure
  the detach prevented, moved one step along.

## Wrong if

- A recorded run takes the new way in and leaves the branch behind, so the state
  the detach rule guarded against arrives under a name.
- The maintainer corrects a later session the other way, that the local carry
  wanted no branch. That would say the correction was about that one review.
- Reviews start to quote the local hash because there is now a local name for
  it. That is the attributability this decision claims to improve, lost the way
  it came.
- Sessions ask for the cherry-pick and the skill's rebase path already answers
  them. That would say this was a wording fix to one paragraph and not a way in.

## Since then

Both questions got a read and an answer. The contribution guide's cherry-pick
page names no branch at all. It cleans the checkout onto `main` and runs the
line Gerrit's Download menu copies, so the commit lands there. That is worse
than what the skill prescribed rather than a source for the name, so the branch
name is the maintainer's correction written down. The rebase path folds into the
new way in rather than stays beside it. Every core patch is one commit, so a
rebase and a cherry-pick onto current code produce the same thing. Two paths
would state the boundary twice, and the stale copy was the rebase one.
