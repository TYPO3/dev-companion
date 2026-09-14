---
id: D-AUD-017
title: Records are read and the boundary is the table they are in
date: 2026-09-01
status: revoked
revokedBy: D-AUD-018
coveredBy: []
---

# D-AUD-017 — Records are read and the boundary is the table they are in

**A lookup on the subject `record` reads the rows of a table a project-owned
extension registers, and every other table stays with the installation.**

`D-AUD-016` drew the line at the count on the same day, and the maintainer who
drew it revoked it. A tool that only counts does not earn a tool of its own.

## Evidence

- `feedback/archive/2026-08-31-233952`: 3101 records in one storage folder, and
  a record list that takes minutes to open. A session read the count twice and
  drew nothing from it.
- The count alone stood built and shown on 2026-09-01. What came back was that
  the ask is records, a list or a count. And that `pid` belongs in the same
  filter as any other column. The count is therefore a parameter of a lookup,
  not the shape of one.
- The shape is already this repository's. Every lookup here answers `matchCount`
  beside its matches, so the list and the number are two halves of one answer
  and never two tools.
- The probe already opened a connection and listed a schema (`D-DIS-022`). What
  is new is the read over rows, run against the `E-SITE` 14.3 installation on
  2026-09-01. That is the grouped count, the filter on a string column, and the
  rows with the label resolved from the table's own `ctrl`.
- Neither describe answer carries a row count and nobody asks either to. The
  maintainer settled that on 2026-09-01, and it is what put this work in a tool
  of its own.

## Decided

- **The rows come back**, ordered by uid: the identifiers, the column the table
  names as its label in `ctrl`, the timestamps and the two flags. Beside them
  the counts, per page and per state.
- **What a row shows is the probe's to decide.** A column list the caller
  composes would make every column of a readable table readable. That is a wider
  boundary than this, and one to decide rather than to arrive at. One added
  later is additive; one taken back is not.
- **`where` is exact equality and nothing else.** No operator, no wildcard, no
  range, and `pid` is a column in it like any other. That is what keeps this a
  lookup rather than the query language `D-AUD-010` rejected as a second server.
- **`count` is its own parameter and `limit` means what it says.** A magic
  `limit: 0` that stands for "count only" was the first draft, and it is a value
  a caller has to learn. Zero is no limit, and the default is the record list's
  own page size.
- **The tool checks a filter's column names against what TYPO3 derives for the
  table.** A column name goes into the SQL as an identifier, where the value
  beside it is a bound parameter.
- **The boundary stays**: only the tables a project-owned extension registers in
  its TCA, attributed through the `EXT:` reference in the ctrl title. `pages`,
  `tt_content` and the user tables are outside it, and every answer says it ran
  with the shell user's database access.

## Assumed

- That the label, the identifiers and the state make a row recognizable enough.
  A table whose label column is a code rather than a name is where that fails.
  The caller then has the uid and the backend.
- That a caller who asks for every row of a full table means it. The tool
  honours zero and the answer is the whole table, which the description says.
- That a read of a project's own rows is not a different trust model from a read
  of its schema. The agent that holds this server already has the shell user's
  database access; what moved is what this server answers for.

## Wrong if

- A caller asks for a column the row does not carry and stops there, which is
  the boundary drawn one notch too tight.
- An answer of every row of a large table turns out to be unusable at that size.
  That would make zero a value to cap rather than to honour.
- A caller wants a filter that equality cannot state, a range of dates, a null,
  a substring. The answer becomes "read it in the backend" often enough that
  callers go around the tool.
- The label column carries something an installation would not want in an
  answer. That is the case `D-AUD-016` drew its line to avoid and this entry
  accepts.

## Revoked on 2026-09-04

The statement's second half stopped to describe this server. The tool reads
`pages`, `tt_content` and every other table TCA describes now. What a row
carries beside its fixed shape is the caller's to name.
[`D-AUD-018`](aud-018-records-are-read-and-the-boundary-is-the-tca-the-installation-has.md)
carries everything decided here that still holds.

What revoked it is `feedback/2026-09-04-053618`. A session decided a whole
replacement layout from six `ddev mysql` queries over the two tables this entry
kept out, and never called the tool. The first **Wrong if** is that one notch,
read at the table rather than at the column.
