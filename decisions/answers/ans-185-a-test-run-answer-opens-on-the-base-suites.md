---
id: D-ANS-185
title: A test run answer opens on the base suites
date: 2026-10-26
status: open
coveredBy:
  - KnowledgeTest::theTestRunGuideOpensOnTheBaseSuitesOfThePaths
---

# D-ANS-185 — A test run answer opens on the base suites

**`typo3_test_run_guide` marks each suite with `base` and lists the base suites
of the given paths first, where no query ranks the list. One sentence before the
suites names them as the checks `typo3_task_guide` hands over.**

## Evidence

- [`feedback/2026-10-22-105711-briefs`](../../feedback/archive/2026-10-22-105711-briefs-for-a-fluid-viewhelper-fix-carry-e2e.md).
  A fix in `SelectViewHelper.php` got the suites of its domains in file order,
  with the three e2e suites among them. The session picked the useful ones by
  hand.
- `TestSuiteHints::baseFor()` already knew the set, and `typo3_task_guide`
  already made it the checks of a brief. The test run answer was the one place
  that did not say so.

## Decided

- **A field rather than a second list.** `base` sits on the suite record both
  tools answer with, so a client reads one model.
- **The order changes only where nobody ranked it.** A query scores the suites,
  and the strongest stays first. The text then says that `base` marks them.
- **The base set itself is unchanged.** Which suites `test-suite-hints.json`
  marks `base` is data this change reads and does not judge.

## Assumed

- That the suites `baseFor()` returns are the narrow set a session needs first.
  The feedback named `phpstan` and `cglGit` among the useful ones, and neither
  is base for the `fluid` domain.

## Wrong if

- A session with this answer still runs the rest of the list first, or reports a
  base suite as one it did not need.
- A session reports a suite outside the base set as the one it needed for every
  change in a domain. The `base` flags in the data are then what is wrong.
