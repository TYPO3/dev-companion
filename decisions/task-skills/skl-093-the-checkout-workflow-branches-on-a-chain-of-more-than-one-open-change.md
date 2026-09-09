---
id: D-SKL-093
title: The checkout workflow branches on a chain of more than one open change
date: 2026-09-09
status: open
coveredBy: []
---

# D-SKL-093 — The checkout workflow branches on a chain of more than one open change

**`typo3-core-patch-checkout` carries a branch for a relation chain, because
"one change is one commit" is the shape it is built on and not the shape a large
refactoring arrives in.**

`chain` reached the review workflow a week ago and never reached the one that
puts commits on disk.

## Evidence

- **The report.**
  [`feedback/2026-09-09-190146`](../../feedback/archive/2026-09-09-190146-the-patch-checkout-skill-has-no-branch-for-a.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. The request was "bitte
  rebase mir die chain auf main", the skill activated on it, and the session
  names five things it worked out unaided: which links are still open, that the
  merged link was not an ancestor of `main`, `git cherry-pick BASE..TIP` in
  place of the skill's one-commit form, what the branch is named when it carries
  two changes, and that every later amend on a chain is a two-step.
- **The skill says the opposite, three times.** "Every patch is exactly one
  commit." "A rebase of the fetched commit and a cherry-pick onto current code
  are one move under two names, because a core patch is exactly one commit."
  "One change, because the work needs it on disk." Each is true per change and
  none of them covers a chain.
- **The word appears nowhere in it.** Read on 2026-09-09: `chain` occurs in
  neither `SKILL.md` nor `references/checklist.md` of this skill.
- **It reached the neighbouring workflow.** `D-SKL-090`, decided 2026-09-02, put
  the chain into `typo3-core-patch-review`'s checklist so a structural finding
  is checked against the change stacked above it. The same answer carries the
  same field to both.
- **The field the chain branch turns on is already answered.** The same
  session's positive report,
  [`feedback/2026-09-09-190227`](../../feedback/archive/2026-09-09-190227-what-carried-this-session-the-chain-and.md),
  names `chainedAt` against `patchSet` on the merged link as why that chain's
  base was not an ancestor of `main` and why several of its conflicts existed.
  Nothing in a checkout says it.
- **The hazard a chain rebase adds is a second report.**
  [`feedback/2026-09-09-190119`](../../feedback/archive/2026-09-09-190119-a-rebase-silently-reverts-a-merged-bugfix-when.md)
  from the same task: `main` had merged a one-line fix into a class the patch
  guts and moves, git reported no conflict because the files differ, and the
  rebased tree silently reverts it. The session reached it by accident, through
  a test `main` added that calls a method the patch removes.

## Decided

- **The chain branch goes in the skill**, at the first minute of the session,
  where the request says "chain" or the first answer carries one longer than a
  single link. The moves are a range rather than a commit, the branch keeps its
  `review/<number>` name off the tip change, and an amend below the tip is a
  two-step.
- **The stale-rebase hazard is a document rather than a rule.** What the session
  was missing is an order of steps, which written as a rule is one sentence
  saying the shape should be clear — `D-FBK-043`. It goes to
  `knowledge/documents/core/contribution/`, because the caller was in a core
  checkout and the steps are git calls against it.
- **The stopping rule gets its condition said out loud.** "More than a handful
  of hunks" is written for somebody else's patch, and this session resolved six
  conflicting hunks on the reasoning that the author was the requester. That
  reasoning is right and the skill does not say so, so a session either
  reconstructs it or stops on a patch its own author asked it to carry.
- **Against a `rewrittenPaths` argument on `typo3_test_run_guide`.** One report,
  and what it buys is covered by a step of the document at no schema cost: run
  the suites covering the paths the change rewrites or deletes, not only the
  ones it changes. What would earn the argument is a second session reporting
  the same false green.
- Taken on rather than closed here. The document states things about TYPO3 and
  the skill is a contract, and both are the far side of the line.

## Assumed

- That a chain is the normal shape of a large refactoring rather than this
  session's. Two of this burst's tasks arrived as chains, one of three links and
  one of fourteen, which is what says it is not rare.
- That the range move is what a chain rebase takes. Read off the report's own
  account and not run here.

## Wrong if

- A session reports the chain branch firing on a single-link chain and costing
  it the simple path. Then the condition is on the wrong signal and it is the
  count of open links that has to gate it.
- A session reports carrying a chain with the branch in place and still working
  out the amend two-step. Then the section says what to do and not how.
- The silent revert turns out to be reachable by something git already reports.
  Then the document's second step is a mechanism nobody needs.
