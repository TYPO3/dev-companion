---
id: D-AUD-016
title: A count is answered and the row behind it stays with the installation
date: 2026-09-01
status: open
coveredBy:
  - RecordLookupTest::aTableFullEnoughToLeaveTheRecordListIsSaidToBe
  - RecordLookupTest::aTableNoProjectExtensionRegistersIsRefusedRatherThanCounted
  - RecordLookupTest::aTableOneScreenLongAsksForNothing
  - RecordLookupTest::everyCountSaysItWasReadWithoutBackendPermissions
  - RecordLookupTest::theTablesItWillCountAreListedWithoutOneBeingNamed
---

# D-AUD-016 — A count is answered and the row behind it stays with the installation

**A lookup on the subject `record` answers how many rows a project-owned table
holds and where they sit, and no field of any row is read.**

`D-AUD-010` put every record on the installation's side, and a count reads no
field while still running over the data with the shell user's database access.

## Evidence

- `feedback/archive/2026-08-31-233952`: 3101 records in one storage folder, a
  record list that takes minutes to open, and a session that read the count
  twice — once in a comment it wrote itself, once from the record list — and
  drew nothing from it. Nothing connected a count to the question of a backend
  module.
- The maintainer answered on 2026-09-01 that counting is not
  `typo3_extension_describe`'s work and that reading records is worth a tool of
  its own, which is what put this entry beside `D-AUD-010` rather than a field
  on an existing answer.
- The probe already opens a connection and lists a schema — the table names, the
  columns and the indexes of `liveSchema` (`D-DIS-022`). What it has never run
  is a query over rows, so the connection is not the new thing here and the
  query is.
- The three audiences are unchanged by it. A count is read by the extension
  author deciding where records are maintained and by the site developer looking
  at what a relaunch produced, and neither of them is asking what a record says.

## Decided

- **The answer is counts.** `COUNT(*)` per table, broken down by `pid` and by
  the state the enable fields put a row in — present, hidden, deleted. No column
  of any row is selected, so there is no field value for an answer to carry.
- **The verb is `lookup`.** A table goes in, the groupings that match come out,
  and a table with no rows is a legitimate answer rather than a failure. The
  subject is the record because that is what the caller is asking about.
- **It refuses every table a project-owned extension does not register.** Which
  extensions are the project's own is what `typo3_project_describe` already
  establishes and what each registers in its TCA is what
  `typo3_extension_describe` reads, so the rule is derived rather than listed.
  `be_users`, `fe_users`, `pages` and `tt_content` are outside it.
- **The probe may run that query**, bounded to the count and the grouping. It is
  asked for rather than read with everything else, as `liveSchema` is.
- **The answer says what it was read with**: the shell user's database access,
  no backend permissions applied, and the deleted and hidden rows counted
  separately rather than folded in.
- Rejected: the peek at the first rows. A label column is editorial text, and
  taking it would move `doesNotCover` from "no row is read" to "these columns of
  a row are read", where every further column is a judgement of its own.
- Rejected: counting any TCA table with the access stated in the answer. It buys
  `pages` and `tt_content` and costs a head count of the people in an
  installation.

## Assumed

- That the count is the whole of what the sighting needed. The session had the
  number twice and drew nothing from it, so what was missing may be the routing
  rather than the number — which is the half the content-element skill already
  took.
- That a project-owned table is the one a caller asks about. The relaunch case
  is that shape; a site developer asking how much content a page carries is not,
  and that question is refused.
- That `COUNT(*)` grouped by `pid` costs nothing worth guarding. It is one query
  per table and the storage engine answers it without reading a row.

## Wrong if

- A session asks for the count of a table the rule refuses and stops there,
  which is `D-AUD-010`'s first **Wrong if** in the shape this entry chose.
- The count arrives and changes nothing, because what was missing was the
  sentence connecting a number to the backend-module question rather than the
  number.
- A grouping turns out to be a field value after all: a `pid` is a page, and an
  installation where the storage folders are named after clients has its
  structure in the answer.
