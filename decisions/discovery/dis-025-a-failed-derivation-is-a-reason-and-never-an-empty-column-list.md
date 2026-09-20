---
id: D-DIS-025
title: A failed derivation is a reason and never an empty column list
date: 2026-09-20
status: open
coveredBy:
  - RecordLookupTest::aDerivationThatFailedIsReportedRatherThanReadAsNoColumns
---

# D-DIS-025 — A failed derivation is a reason and never an empty column list

**Every tool that checks a column against the `derivedColumns` topic answers
`unsupported` with the reason where that topic is `unavailable` or lacks the
table. The probe builds `DefaultTcaSchema` from what its constructor declares,
out of the container, and hands 12.4 the list it reads.**

`typo3_record_lookup` answered zero rows for `pages`, `tt_content` and
`be_users` on an installation that held 78, 470 and 2. Nothing in the answer
said that the read had failed.

## Evidence

- **The report.**
  [`feedback/2026-09-20-085751`](../../feedback/archive/2026-09-20-085751-record-lookup-answers-zero-rows-for-every-table.md),
  `bootstrap_package`, TYPO3 15.0.0-dev in DDEV on `mysqli`. The report
  suspected a connection held from before the database was filled. There is
  none: `Typo3Runtime` boots a subprocess per tool call and `Registry::call`
  forgets the read when the call ends.
- **Run again on 2026-09-20** against the same installation, before the change.
  All three calls answered
  `"<table> has no column <column>, so nothing was read"` with `matchCount: 0`.
  The `derivedColumns` topic was
  `ArgumentCountError: Too few arguments to function DefaultTcaSchema::__construct(), 0 passed … and exactly 2 expected`.
  The tool's column check read that topic as a table with no columns, so every
  column the calls named was unknown.
- **The signature moved on main.** Core commit `9d83ed2aa7` of 2026-07-10
  ("Require and use dependency injection in database services", #110163) made
  the class `readonly`. Its constructor takes `ConnectionPool` and
  `TcaSchemaFactory` with no defaults, and `makeInstance` built it with none.
  The class is a private service on every branch: the container `has()` it
  nowhere, and `has()` both its dependencies on main. 14.3 takes one optional
  `TcaSchemaFactory`, 13.4 and 12.4 take nothing.
- **12.4 never answered this topic.** `enrich()` there reads the incoming tables
  by position, and `getTableFirstPosition()` casts the key to `int`. So a
  name-keyed array reaches `$tables[0]` and fails with
  `Call to a member function addColumn() on null`. `D-DIS-008` read 13.4 and
  14.3 and named no 12.4 run. 13.4 on requires the name as the key and throws on
  a list.
- **After the change**, the derivation ran through the host PHP against
  `.environments/e-site-12.4`, `13.4` and `14.3`. It ran through DDEV against
  the reporting installation on main. Every one derives `tt_content` with `uid`,
  `pid`, `tstamp`, `crdate`, `deleted`, `hidden` in front. The three reported
  calls answer 470 rows in 45 `CType` values and 78 pages in 2
  `sys_language_uid` values. The 2 `be_users` rows come with `username` and
  `admin`.

## Decided

- **A reason is never read as an empty list.** `RecordLookup::derivedColumns()`
  answers the column names or the reason there are none to check against, and
  the tool answers `unsupported` on the reason. The check stays: a named column
  goes into the SQL as an identifier, `D-AUD-019`, and an unchecked read was
  rejected for that.
- **The probe builds the class by its constructor.** It reads the parameters and
  takes each from the container by its type. That is one path for four
  signatures rather than a branch per major. A fifth signature the core writes
  reaches the container the same way. A dependency the container lacks fails in
  the probe's `try` and reports as `unavailable`, which the first bullet then
  carries to the caller.
- **12.4 gets a list**, decided by `Typo3Version::getMajorVersion()`, and the
  name comes off each definition on every branch.
- `SchemaLookup` already answered `unsupported` on the same topic, so the rule
  holds for both readers of it. `RecordLookupTest` holds the new one on both
  shapes of a topic nothing can be checked against.
- The fixture's `Typo3Version` answers `getMajorVersion()`, since the real probe
  runs against it.

## Assumed

- The container `has()` every type the constructor declares, on main and on
  whatever the core writes next. Today those are `ConnectionPool` and
  `TcaSchemaFactory`, both public.
- `enrich()` on 13.4 and later keeps the name as the key. The 12.4 branch is the
  only one that reads a position.

## Wrong if

- A covered major's container stops to `has()` one of the constructor's
  parameters. Then the topic reports `unavailable` on that installation, and the
  record and schema lookups say so and answer nothing.
- A topic other than `derivedColumns` is read as data where it carries
  `unavailable`. Then the same defect stands in another tool, and the pattern
  here is what to search for.
