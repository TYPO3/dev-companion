---
id: D-SKL-109
title: A review may skip a verifying suite to finish faster
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aSuiteThatAsksGitForItsFilesVerifiesNothingFromAWorktree
  - SkillTest::aReviewWithNobodyToAskCreatesNoWorktree
  - SkillTest::aReviewsNotRunLineNamesOneOfFourReasons
---

# D-SKL-109 — A review may skip a verifying suite to finish faster

**`typo3-core-patch-review` leaves a suite that verifies unrun for one of four
reasons, and speed is the fourth. Its `Not run:` line names the reason.**

## Evidence

- The fourth `REVIEW-03` run of 2026-10-28 (`scenarios/runs/REVIEW-03.json`) ran
  headless in the worktree that holds the patch. It left `phpstan`, `lintPhp`
  and the full unit and functional suites unrun, each labelled "by my own
  choice, not a valid reason". The first **Wrong if** of `D-SKL-106` fired for
  the second time, after three rewordings of the same section.
- The same run gave "nobody was there to ask" for `e2e` and `unitJavascript`.
  That reason is for a review where no worktree holds the patch, and one stood.
- The maintainer answered on 2026-10-28: a review may skip a suite to finish
  faster, and it notes each skip.

## Decided

- The closed list keeps its three reasons and takes a fourth: the review skipped
  the suite to finish faster. A narrower suite that passed and a scope the
  session chose are that reason, under its name.
- The skill says that the third reason is false where a worktree holds the
  patch. A suite left unrun there takes the fourth.
- A skip for speed takes no question. The question stays the gate for a worktree
  the review would create, as `D-SKL-102` has it. A review creates no worktree
  without it.
- This entry revokes `D-SKL-104` and `D-SKL-106` and is the `revokedBy` of both.
  Their statements forbade the skip the maintainer allows.
- `filesFromGit` stays as `D-SKL-106` decided it. From a worktree the guide
  marks `cglGit` and `cglHeaderGit` `verifies: false`.

## Assumed

- That the cost of the suites was what the four runs traded the skip against.
  The label that called the skip invalid read as a time budget each time.

## Wrong if

- A review leaves a verifying suite off both lines. Then the gap was never the
  reason, and the list of names is the next read.
- A review in a worktree still says that nobody was there to ask. Then the
  sentence about the third reason did not arrive.
- A maintainer reading a report cannot tell a speed skip from a finished
  verification. Then the label needs a place above the `Ran:` line.
