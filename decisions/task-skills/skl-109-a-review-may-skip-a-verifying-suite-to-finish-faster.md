---
id: D-SKL-109
title: A review may skip a verifying suite to finish faster
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aSuiteThatAsksGitForItsFilesVerifiesNothingFromAWorktree
  - SkillTest::aReviewWithNobodyToAskCreatesNoWorktree
  - SkillTest::aReviewsNotRunLineNamesOneOfFourReasons
  - SkillTest::aSpeedSkipRunsTheTestABlockingFindingRestsOn
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
- The fifth `REVIEW-03` run of 2026-10-28 skipped `functional` for speed, and
  with it the one test its blocker rests on. The blocker rested on the CI log
  alone. The fourth run had reproduced that failure with the test on MariaDB.
- The maintainer answered on 2026-10-29: whole suites may go for speed, and the
  narrowest test a High or blocking finding rests on runs anyway.

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
- A skip for speed never takes the test a High or blocking finding rests on. The
  review runs that test alone, in the targeted form `typo3_test_run_guide`
  returns for its suite, on the database the failure was seen on.
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
- A review skips for speed the suite of a test a High or blocking finding rests
  on, and runs no test of it. Then the exception needs a place in the report
  skeleton, beside the finding.
- A maintainer reading a report cannot tell a speed skip from a finished
  verification. Then the label needs a place above the `Ran:` line.
