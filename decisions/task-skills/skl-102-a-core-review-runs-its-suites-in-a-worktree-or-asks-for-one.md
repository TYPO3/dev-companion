---
id: D-SKL-102
title: A core review runs its suites in a worktree or asks for one
date: 2026-10-28
status: open
coveredBy:
  - SkillTest::aReviewAsksForAWorktreeBeforeItSkipsTheSuites
---

# D-SKL-102 — A core review runs its suites in a worktree or asks for one

**`typo3-core-patch-review` runs the suites in a `git worktree` on the patch set.
Where none exists, the review asks the user before it skips the run.**

## Evidence

- `feedback/archive/2026-10-28-031023-a-core-review-skips-the-suites-rather-than-run.md`.
  A review of change 97177 ran none of the suites `typo3_test_run_guide`
  returned. It read its own read-only posture as a reason against a worktree and
  leaned on the CI vote. The review the day before detached the main checkout
  onto the patch set instead.
- The maintainer said on 2026-10-28 that a session prefers a worktree, or at
  least asks whether to create one.
- The judgement is ladder step 4, wording. The skill permitted a run ("may run
  what cannot change the code") and said nowhere where it runs. The checkout
  skill offers the worktree as one way in of four, and the review never chose
  among them.

## Decided

- The verification section of the review skill states the worktree as the
  place, the question as the step before a skip, and the `Not run:` line as
  where a skip records both.
- The skill names `git worktree` only. A worktree tool of the user's own is the
  project's business, as `D-SKL-095` decided for both core skills.

## Assumed

- That the next review that skips a run does so for want of the worktree, and
  not because the install a worktree needs costs too much. The checkout skill
  and `typo3_test_run_guide` already state that install.

## Wrong if

- A review skips the suites again and its `Not run:` line names no question.
  Then the sentence arrived and did not take, and placement is the next read.
- A review creates a worktree without the question and the user objects to it.
  Then the default runs the other way: ask first, create on yes.
