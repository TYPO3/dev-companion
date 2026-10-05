---
id: D-KNW-163
title: A probe that counts rows takes the restrictions off
date: 2026-10-05
status: open
coveredBy: []
---

# D-KNW-163 — A probe that counts rows takes the restrictions off

**The `core-tests` hint says that a probe which counts what a run wrote calls
`getRestrictions()->removeAll()` first, and why `assertCSVDataSet` never shows
the gap.**

## Evidence

- `feedback/archive/2026-10-05-094211-a-probe-that-counts-rows-misses-disabled-rows.md`.
  A functional probe counted password hashes and missed the two demo `be_users`,
  which carry `disable=1`.
- Read in `.checkouts/` on 2026-10-05. `QueryBuilder` takes the
  `DefaultRestrictionContainer` when nobody passes one, on `main`, `13.4` and
  `12.4`. That container holds the deleted, hidden, start time and end time
  restrictions. `be_users` declares `disable` as its `disabled` enable column.
  `FunctionalTestCase::getAllRecords()`, which `assertCSVDataSet` reads through,
  calls `removeAll()` on testing-framework 8, 9 and `main`.

## Decided

- One sentence in `core-tests`, beside the CSV fixture line. The hint reaches a
  session through a path below `Tests/Functional/`, which is where such a probe
  sits.

## Wrong if

- A session writes a counting probe below `Tests/Functional/` and still counts
  through the default restrictions.
