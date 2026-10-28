---
id: D-SKL-106
title: A review's Not run line names one of three reasons
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aSuiteThatAsksGitForItsFilesVerifiesNothingFromAWorktree
  - SkillTest::aReviewsNotRunLineNamesOneOfThreeReasons
---

# D-SKL-106 — A review's Not run line names one of three reasons

**`typo3-core-patch-review` leaves a suite that verifies unrun for one of three
reasons, and names that reason on its `Not run:` line.**

## Evidence

- The second `REVIEW-03` run of 2026-10-28 (`scenarios/runs/REVIEW-03.json`) ran
  headless in a worktree. It left `phpstan`, `e2e`, `lintPhp` and seven more
  unrun with "scoped to targeted suites; not asked". `D-SKL-104` had told it to
  run them, so its second **Wrong if** fired.
- The section the session read said the verification a review proposes is "the
  narrowest applicable suite first, the broader ones named after it". That
  sentence licenses the scope the run chose, and it stood two paragraphs above
  the `D-SKL-104` sentence.
- The same run's Not run line left out seven verifying suites, `cglGit` and
  `cglHeaderGit` among them. Both read no file from a worktree and report
  SUCCESS (`R-KNW-049`). The guide marked them `verifies` all the same, because
  `D-SKL-105` kept the mark for a normal checkout.

## Decided

- The skill says a review runs the broader suites, rather than names them. The
  `Not run:` line takes one of three reasons. The suite writes and has no
  report-only form, the user answered no, or no worktree stood with nobody to
  ask. The reasons are a closed list because a free reason gave the scope room.
- A field in the guide that names what a review owes was the other lever. The
  guide does not know that the caller reviews, so the field would repeat
  `verifies`. The lever that remains is the sentence that contradicted it.
- The suite entry carries `filesFromGit`. The guide reads the `.git` of the
  checkout it serves, and where that names a gitdir outside the checkout, the
  entry answers `verifies: false` with a line of its own. The axis is the
  gitdir, as `D-ANS-154` has it.

## Assumed

- That the review runs its suites in the checkout the server was started in.
  Where it runs them in another worktree, the mark answers for the wrong
  directory, and the entry's own text still carries the condition.

## Wrong if

- A headless review in a worktree still leaves a verifying suite unrun with a
  reason off the list. Then the skill is not where the lever is, and the next
  read is what the client did with the session's time.
- A review lists `cglGit` as not run from a worktree the server read. Then the
  mark did not arrive.

## Since then

The first **Wrong if** fired in the third `REVIEW-03` run of 2026-10-28. The
session quoted the three reasons and skipped with a fourth. The second **Wrong
if** held: neither git-based cgl suite stood on its lines. The guide now lists
the suites a report owes a line, which the second bullet of **Decided** rejected
(`D-SKL-107`).
