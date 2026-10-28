---
id: D-SKL-105
title: A review's Not run line lists the suites that verify
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::theEntriesThatVerifyNothingPrepareServeOrList
  - SkillTest::aReviewsNotRunLineListsTheSuitesThatVerify
---

# D-SKL-105 — A review's Not run line lists the suites that verify

**`typo3_test_run_guide` marks each entry `verifies`, and a core review's
`Not run:` line lists the marked ones it did not start.**

## Evidence

- The `REVIEW-03` run of 2026-10-28 (`scenarios/runs/REVIEW-03.json`). The guide
  returned 29 entries for the patch's paths on 15. `npm`, `composer`,
  `composerInstall`, `listExceptionCodes`, `watchRst`, `e2e-prepare` and
  `e2e-browser` were among them. The report named 8 as not run and left out
  `lintPhp`, `normalizeXliff` and `checkExtensionScannerRst`, which the diff
  reaches.
- Each of the seven entries already says in its own `whenToUse` that a run
  checks nothing. `composerInstall` is "a precondition and not a step".
  `listExceptionCodes` "confirms nothing". `e2e-prepare` says "What a review
  reports is the `-s e2e` run". `npm` and `composer` dispatch, and `watchRst`
  writes a file and serves it.
- `D-SKL-009` foresaw the other failure: a list of every entry with no thought
  about any of them. A list of 29 invites it.

## Decided

- The mark is data on the entry, because the guide owns the list and a skill
  that names suites goes stale. It defaults to true, so an entry states it only
  where it is false. The text says it beside the command.
- `build`, `build-css`, `cglGit` and `cglHeaderGit` keep the mark. Each fails on
  something the change did, even where a sibling is the better run.

## Assumed

- That the length of the list is what lost the three suites, rather than a read
  of the list by domain or by base.

## Wrong if

- A review lists an entry the guide marks false, or leaves out one it marks
  true. Then the mark did not arrive, and its place in the text is the next
  read.

## Since then

`cglGit` and `cglHeaderGit` keep the mark in a normal checkout alone. From a git
worktree they read nothing, so the guide marks them false where the checkout it
reads is one (`D-SKL-106`).
