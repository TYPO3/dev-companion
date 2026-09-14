---
id: D-AUD-016
title: A count is answered and the row behind it stays with the installation
date: 2026-09-01
status: revoked
revokedBy: D-AUD-017
coveredBy: []
---

# D-AUD-016 — A count is answered and the row behind it stays with the installation

**A lookup on the subject `record` answers how many rows a project-owned table
holds and where they sit. It reads no field of any row.**

`D-AUD-010` put every record on the installation's side. A count reads no field
while it still runs over the data with the shell user's database access.

## Evidence

- `feedback/archive/2026-08-31-233952`: 3101 records in one storage folder, and
  a record list that takes minutes to open. A session read the count twice, once
  in a comment it wrote itself and once from the record list, and drew nothing
  from it. Nothing connected a count to the question of a backend module.
- The maintainer answered on 2026-09-01 that a count is not
  `typo3_extension_describe`'s work and that a read of records is worth a tool
  of its own. That put this entry beside `D-AUD-010` rather than a field on an
  answer that exists.
- The probe already opens a connection and lists a schema — the table names, the
  columns and the indexes of `liveSchema` (`D-DIS-022`). What it has never run
  is a query over rows, so the connection is not the new thing here and the
  query is.
- The three audiences stay as they are. The extension author reads a count to
  decide where records live, and the site developer reads it to see what a
  relaunch produced. Neither of them asks what a record says.

## Decided

- **The answer is counts.** `COUNT(*)` per table, broken down by `pid` and by
  the state the enable fields put a row in — present, hidden, deleted. The query
  selects no column of any row, so there is no field value for an answer to
  carry.
- **The verb is `lookup`.** A table goes in, the groupings that match come out,
  and a table with no rows is a legitimate answer rather than a failure. The
  subject is the record because that is what the caller asks about.
- **It refuses every table a project-owned extension does not register.**
  `typo3_project_describe` already establishes which extensions are the
  project's own, and `typo3_extension_describe` reads what each registers in its
  TCA. So the rule follows from those rather than from a list. `be_users`,
  `fe_users`, `pages` and `tt_content` are outside it.
- **The probe may run that query**, bounded to the count and the groups. A
  caller asks for it rather than gets it with everything else, as with
  `liveSchema`.
- **The answer says what access it ran with**: the shell user's database access,
  and no backend permissions. It counts the deleted and hidden rows separately
  rather than folds them in.
- Rejected: the peek at the first rows. A label column is editorial text. To
  take it would move `doesNotCover` from "no row is read" to "these columns of a
  row are read". There, every further column is a judgement of its own.
- Rejected: a count over any TCA table with the access the answer states. It
  buys `pages` and `tt_content` and costs a head count of the people in an
  installation.

## Assumed

- That the count is the whole of what the observation needed. The session had
  the number twice and drew nothing from it. So the gap may be the routing
  rather than the number, which is the half the content-element skill already
  took.
- That a project-owned table is the one a caller asks about. The relaunch case
  is that shape. A site developer who asks how much content a page carries is
  not, and the tool refuses that question.
- That `COUNT(*)` grouped by `pid` costs nothing worth a guard. It is one query
  per table and the storage engine answers it and reads no row.

## Wrong if

- A session asks for the count of a table the rule refuses and stops there. That
  is `D-AUD-010`'s first **Wrong if** in the shape this entry chose.
- The count arrives and changes nothing, because the gap was the sentence that
  connects a number to the backend-module question rather than the number.
- A group turns out to be a field value after all. A `pid` is a page, and an
  installation whose storage folders carry client names has its structure in the
  answer.

## Revoked on 2026-09-01

Revoked on the day of its decision, by the maintainer who decided it. The ask is
a read of records, a list or a count. A tool that only counts is not worth a
tool of its own. `D-AUD-017` replaces it. The count survives inside it as one
half of an ordinary lookup answer rather than as the whole of one.

What still holds from here is the boundary around it: only the tables a
project-owned extension registers, and the access every answer states. What does
not is the sentence that the tool reads no column of any row.
