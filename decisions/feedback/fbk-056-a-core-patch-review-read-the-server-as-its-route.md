---
id: D-FBK-056
title: A core patch review read the server as its route
date: 2026-10-05
status: open
coveredBy: []
---

# D-FBK-056 — A core patch review read the server as its route

**One review of change 96353 reports six answers that saved it work. Each marks
a boundary that holds. The re-read of the change before the report stays one
plain call rather than a flag.**

## Evidence

- `feedback/archive/2026-10-05-094211-what-worked-in-a-core-patch-review-gerrit-read.md`,
  from the debrief that filed eight other reports the same day.
- `typo3_gerrit_lookup` gave branch, patch set, commit, fetch ref, chain,
  mergeability, issue and `Releases:` in one call. The commit matched
  `git rev-parse` after the fetch.
- `typo3_test_run_guide` warned that `cglGit` reports success in a worktree
  after it read nothing, and that a worktree may lack `vendor/`.
- `typo3_rule_lookup` said a `[TASK]` owes no changelog entry, and that it goes
  to `main` and one line back. That made `Releases: 13.4` a finding.
- `typo3_project_describe` listed `core/testing/timing-a-code-path`, which the
  session found nowhere else. That is a code session that read the `guides`
  list, the other side of the cost `D-DIS-029` records for a prose session.
- The review skill's demand to name the suites it did not run kept the report
  honest, in the session's own words.

## Decided

- Nothing in these six changes. Each is the boundary the session met on the side
  that answered.
- Not built: a "changed since" flag on `typo3_gerrit_lookup`. The re-read
  already costs one call, and `D-FBK-020` prices calls rather than bytes. The
  flag would shorten the answer and save no call.

## Wrong if

- A session reports that the re-read of a change before its report missed a new
  patch set because the answer was too long to compare.

## Since then

A second review the same day, of change 96354, reports the same strengths and
three more. The `cglGit` worktree warning, the terminal `e2e-prepare` needs and
the `older-release-line` warning each changed what it did. It asked for tests on
them, and each already has one:
`KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold`,
`KnowledgeTest::aSuiteThatWaitsForAKeypressSaysItNeedsATerminal` and
`CommitMessageTest::aMaintainedLineFurtherBackSaysWhatItClaims`.
