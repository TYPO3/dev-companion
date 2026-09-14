---
id: D-AUD-010
title: The content model is answered and the records stay with the installation
date: 2026-08-12
status: open
---

# D-AUD-010 — The content model is answered and the records stay with the installation

**The three audiences stay three: this server answers for the content model, a
record is the installation's own, and `doesNotCover` says so.**

The boundary was half crossed and written down nowhere. `typo3_schema_lookup`
answers what a table is, and no tool touches a row of one. A caller had to infer
the line from which tools happen to exist.

## Evidence

- The trust model is what decides it rather than taste. The client launches this
  server as a stdio subprocess, so the process boundary is the whole of its
  security. A record read puts the shell user's database access where a backend
  user's permissions belong.
- Opening the record side is a second server rather than a further tool. Backend
  identity, workspaces and the DataHandler come with it, and each of them is a
  question about who asks.
- Nothing has arrived since the question was first put on 2026-08-04 to move it.
  The draft RFC read that day is a community proposal and settles nothing
  (`D-SCO-010`), and no session has asked for a record. What the archived
  feedback asked for is DataHandler *knowledge*, how to write code that writes
  records. That is the model side, and a hint answers it.
- `R-AUD-001` counts three audiences because somebody chose three, and this is
  that choice made again with the record question named.

## Decided

- One `doesNotCover` entry that names the record and the reason, so a caller
  reads the boundary rather than infers it from the tool list.
- No fourth audience, and `R-AUD-001` stands as written.
- Rejected: the record side taken on. It is a different server with a different
  trust model, and this one would have to grow an identity before its first row.

## Assumed

- That the backend and the console serve an agent that acts on records, which is
  where the permissions apply.
- That the model half stays enough for the three audiences. Extension and site
  work stands against the schema rather than against rows.

## Wrong if

- Sessions still arrive at a task that needs a record and stop at the boundary.
  That is the evidence this entry asked for and it has not come.
- TYPO3 adopts an interface contract on identity. Then the map from a backend
  user to a client session is a solved problem rather than an open one.
- The model half turns out to answer wrongly because it never sees a row, a
  schema question whose real answer depends on the stored data.

## Since then

The `doesNotCover` entry is there, and it names the record, the trust model and
where the work goes instead. The first **Wrong if** met its first real case and
came out the other way. A session that wanted a flex field resolved against a
real row read the boundary itself and stopped at it. What came of it takes
column values from the caller and no uid. The other two stay.

The second case is a count rather than a row, and it is open.
`feedback/2026-08-31-233952` asks that a describe answer say how many rows a
declared table holds. A count reads no field of any row and still runs over the
data with the shell user's access. So which side it is on is the question
`todo/waiting/T-260831-029e.md` carries.

## Since then

Asked on 2026-09-01 whether a row count belongs in `typo3_extension_describe`. A
session had maintained 3101 records through the generic record list and reported
that nothing connected a count to the question of a backend module. The answer
was that a count is not that tool's work, and that a read of records is worth a
tool of its own. So the boundary this entry drew is the one under review rather
than the describe answer, and what a record tool may answer is `T-260901-2b7e`.

## Since then

The count stands settled and it is a tool of its own: `D-AUD-016`, decided on
2026-09-01. It answers how many rows a project-owned table holds, grouped by
`pid` and by the state the enable fields put a row in. It reads no column of any
row. So the boundary this entry drew moves by a query and not by a field. What
stays refused is every table a project-owned extension does not register,
`pages` and `tt_content` among them. And the peek at the first rows that would
have made a label column part of an answer.
