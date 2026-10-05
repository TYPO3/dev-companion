---
id: D-KNW-164
title: A speed claim is checked by counting its cause
date: 2026-10-05
status: open
coveredBy: []
---

# D-KNW-164 — A speed claim is checked by counting its cause

**`core/testing/timing-a-code-path` says how a review checks a speed claim: it
counts the cause before it times anything. A `core-tests` hint names the e2e
setup as the harness for a console command that setup runs.**

## Evidence

- `feedback/archive/2026-10-05-094210-reviewing-a-speed-claim-needs-a-count-of-the.md`.
  A review timed `styleguide:generate -c tca` on MariaDB and reported that the
  claim did not reproduce. The claim was about how often a password hash runs. A
  count of distinct salts, 10 against 2, and a run on SQLite confirmed it.
- `feedback/archive/2026-10-05-094210-e2e-setup-is-a-ready-benchmark-for-the-cli.md`.
  The same session found the e2e setup as its best harness by itself.
- Read in `.checkouts/` on 2026-10-05. On `main`, `14.3` and `13.4`,
  `setupAcceptanceComposer.sh` runs `typo3 setup`, `dataset:import` and
  `styleguide:generate`, and `runTests.sh` installs the e2e instance on SQLite.
  The testing framework on 8, 9 and `main` sets no `passwordHashing`, so a test
  instance hashes with the default the core configuration names.

## Decided

- Two sections on the page, and one sentence in `typo3-core-patch-review` that
  points at it.
- The e2e harness is a hint rather than a section, because `12.4` has no e2e
  suite and a page carries no version bound. `KnowledgeTest` holds that rule.
- A counter in the class under review is not the advice. It is a second change
  in the diff the review reads. A probe or a side effect counts instead.

## Wrong if

- A review of a speed claim still times the whole command first and reports the
  claim as wrong from it.
