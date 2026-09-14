---
id: D-FBK-011
title: The suite holds what one branch can be right about
date: 2026-08-02
status: open
coveredBy: []
---

# D-FBK-011 — The suite holds what one branch can be right about

**`requirements:check` and `decisions:check` hold the generated group listing,
and nothing in `composer ci` does.**

A session on one of several todos has to leave that block alone. It comes from
every file in a group and a worktree can see only its own new entry. The suite
held it anyway, so the branch could be right or green, never both.

## Evidence

- Measured on 2026-08-02: `status: confirmed` on `D-SCO-002` failed the listing
  assertion `DecisionsTest` carried, on `decisions/readme.md` and on
  `decisions/scope/readme.md`, and failed nothing else. A new entry has the same
  shape, with the listing short by one until the merge.
- The rule and the check served different runs and neither named the other.
  `working-todos-in-parallel.md` says the merge runs both index commands once
  and that `requirements:check` says so if it forgets. `composer ci` ran the
  same assertion on every branch.

## Decided

- The assertion leaves both suites — it was `everyGroupListsWhatIsInIt` in each
  of them. It stays in the two check commands, which is where the merge
  procedure already runs it, and `repository:check` carries them.
- The line is which run can be right about what. The suite holds what one entry
  is on its own: its id, its group, its date, its status, its fields, the tests
  it names. The commands hold what only a checkout with every file in it can
  satisfy.
- Rejected: a test that reads which run it is in. A check that behaves
  differently on a branch is off exactly where the work happens. The two
  behaviours are then a thing to keep in step.
- Rejected: a branch that regenerates. Two branches that add to one group then
  insert lines in conflict into the same block, and the procedure rebases rather
  than merges. The conflict lands on whoever is second, per branch, for a file
  neither session wrote.
- The queue collision is not the same case and stays in the suite. Two branches
  that pick the same number are each right on their own and wrong once merged.
  So `TodoTest` fails on `main`, which is where a fix is possible.

- Nothing in the suite holds it, because what this decides is that one assertion
  is absent from it. The check it moved to is `bin/cli decisions:check`.
## Assumed

- A stale listing between merges costs a reader one absent line in an index, and
  nothing that reads it as data. That is why it is the half that may wait for a
  command.

## Wrong if

- A listing goes stale on `main` and stays there. The merge procedure is the
  only thing that runs the command and a session that works alone never does.
  Then the index belongs in a hook or in `todo:next`, not in a suite that
  branches cannot pass.

## Since then

The rejection moved on 2026-08-18, and only the half about when. A session that
regenerates mid-work is still out, for the reason given. `todo:home` runs both
index commands in the worktree after the rebase, where that objection is gone
because the branch already stands on what `main` has. It amends what they wrote
onto its own commit. What that replaces is a commit on `main` with the same
rewrite, of which there were 37 in the 200 before that day. Each held a listing
line a commit boundary away from the entry it lists.
