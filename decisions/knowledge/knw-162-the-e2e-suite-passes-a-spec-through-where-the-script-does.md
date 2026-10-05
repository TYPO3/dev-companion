---
id: D-KNW-162
title: The e2e suite passes a spec through where the script does
date: 2026-10-05
status: open
coveredBy:
  - KnowledgeTest::theE2eAnswerStatesThePriceOfAPlaywrightOnlyChange
---

# D-KNW-162 — The e2e suite passes a spec through where the script does

**The e2e suite entry names `PLAYWRIGHT_TEST_ARGS` as what decides whether a
spec path reaches Playwright. A core hint in `browser-tests`, bound to the
majors whose script has it, gives the targeted form.**

## Evidence

- `feedback/archive/2026-10-05-094210-e2e-suite-entry-says-no-spec-path-passes.md`.
  The entry said nothing passes through. On `main` the session ran one spec, and
  Playwright ran the login setup and that spec alone.
- Read in `.checkouts/` on 2026-10-05. `4d2b838490` on `main` and its backport
  `a740212999` on `14.3`, both of 2026-09-23, forward the positional arguments
  before `--project`. `13.4` has the e2e suite and no `PLAYWRIGHT_TEST_ARGS`.
  `12.4` has no e2e suite.

## Decided

- The suite entry stays one entry. `bin/cli version:check` holds its range to
  the branches whose script offers the suite, and that range is 13 onwards. So
  the entry says which variable to read rather than which major has it.
- The targeted form is a hint with `since: 14` and `scope: core`. It reaches a
  caller on 13 nowhere.
- `R-KNW-067` states the cost on both sides of the boundary.

## Assumed

- That a 14.3 checkout is at or past the backport. A 14.3 release older than
  2026-09-23 lacks it, and the suite entry tells a reader how to see that.

## Wrong if

- `13.4` gets the backport. Then the hint's range is one major short.
- A session on 14 runs one spec as the hint says and gets the whole suite.
