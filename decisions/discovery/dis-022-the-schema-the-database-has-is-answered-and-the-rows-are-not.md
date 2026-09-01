---
id: D-DIS-022
title: The schema the database has is answered and the rows are not
date: 2026-09-01
status: open
---

# D-DIS-022 — The schema the database has is answered and the rows are not

**`typo3_schema_lookup` gains what the database has beside what TYPO3 derives,
because the difference is the finding and neither side alone carries it.**

The boundary it had was decided by `D-DIS-008`, which is revoked, so what kept
the live schema out is a sentence no standing entry makes.

## Evidence

- `SchemaLookup` states the bound in its own docblock and cites `D-DIS-008` for
  it: what is derived, never what the database has. That entry was revoked by
  `D-DIS-012` on 2026-08-04, and `D-DIS-012` decides which driver reaches the
  server rather than which side the tool answers.
- The reason the bound was taken is in the same sentence: the live schema needs
  a schema to exist, and the tool is asked while writing the file that creates
  it. That holds for the derived side and says nothing against answering both
  where the schema is there.
- The audit checklist of `typo3-extension-health` names persistence as a surface
  — TCA, schema, relations, upgrade paths — and nothing this server answers says
  whether the installation's tables match what the package declares. A reviewer
  reads `ext_tables.sql` and the database is not asked.
- The maintainer named the two tools another TYPO3 MCP server ships on
  2026-09-01: one listing tables and a table's columns and indexes from the
  connection, one comparing the actual schema against `ext_tables.sql` and the
  TCA-derived one and reporting what is missing, extra or incompatible. Both are
  read-only and neither reads a row.
- `doesNotCover` draws its line at the record and gives the reason: the process
  boundary is the whole of this server's security, so reading a row would put
  the shell user's database access where a backend user's permissions belong. A
  column is not a row and that reason does not reach it.

## Decided

- One tool rather than two. The question is the same one — what a table is — and
  a caller asking it once gets the derived side, the actual side and where they
  disagree. Two tools would share an output schema, which `AGENTS.md` says is
  one tool.
- No new verb. `lookup` already names the shape: a table goes in, its columns
  come out.
- The rows stay out, and `doesNotCover` keeps saying so in the words it uses
  today. What changes there is the sentence claiming the tool answers only what
  the container assembles.
- The live side is asked for rather than read with everything else: it opens a
  connection and lists a schema, which a caller asking about an icon should not
  pay for. That is the shape `configuration` and `flexForm` already have.
- A schema that is not there is an answer rather than an error. An installation
  whose tables have never been created is the case the derived side was bounded
  for, and it says so per table instead of refusing the call.

## Assumed

- That the drift worth reporting is missing, extra and incompatible columns and
  indexes, which is what the other server reports. What a caller does with a
  type difference that is only a platform's spelling is unmeasured.
- That listing one table's columns costs one round trip to the database. A
  connection that is slow makes this the most expensive answer the server has.

## Wrong if

- The comparison reports differences on a healthy installation often enough that
  a caller stops reading them, which is the third **Wrong if** of `D-ANS-099`
  seen on a new surface.
- The live side is what callers ask for and the derived side stops being read,
  which would say the two were never one question.
- A row turns out to be needed to make the schema answer useful, which would put
  the boundary above back where `doesNotCover` has it.
