---
id: D-SKL-107
title: The test run guide lists the suites that verify by name
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::theGuideListsTheSuitesThatVerifyByName
  - SkillTest::aReviewsNotRunLineIsTheGuidesVerifyingMinusWhatRan
---

# D-SKL-107 — The test run guide lists the suites that verify by name

**`typo3_test_run_guide` answers `verifying`, the names of the suites it marks
`verifies`, and a review's `Not run:` line is that list minus what ran.**

## Evidence

- The third `REVIEW-03` run of 2026-10-28 (`scenarios/runs/REVIEW-03.json`)
  quoted the closed list of `D-SKL-106` and skipped anyway: "none of the three
  accepted reasons applies; I limited the scope to the targeted suites". So the
  first **Wrong if** of `D-SKL-106` fired.
- The same run left five verifying suites off both lines: `lintYaml`,
  `lintServicesYaml`, `checkComposer`, `checkExtensionScannerRst` and
  `normalizeXliff`. The second run had left seven off. The marks stood on every
  entry, and the session did not collect them.

## Decided

- The list is a field of the answer, with `verifyingOwed` beside it
  (`D-ANS-191`). The text names the same suites in one sentence above the suite
  blocks.
- `D-SKL-106` rejected the field because it would repeat `verifies`. It does,
  and that is the use: the list is the one a report holds its lines against, and
  the marks are scattered over every entry.
- The skill text stays as it was otherwise. A second wording of the three
  reasons is the lever that did not move the run.

## Assumed

- That a list the session can subtract from makes a skipped suite visible to the
  session, where the scattered marks did not.

## Wrong if

- A review leaves a suite of `verifying` off both its `Ran:` and `Not run:`
  lines. Then the list did not arrive.
- A headless review still skips a suite of `verifying` with a reason off the
  closed list. Then neither the skill nor the guide holds the lever. The next
  read is the transcript: what the client did with the session's time, and
  whether the session chose its scope before it read the list.
