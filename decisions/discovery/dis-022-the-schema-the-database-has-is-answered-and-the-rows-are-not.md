---
id: D-DIS-022
title: The schema the database has is answered and the rows are not
date: 2026-09-01
status: open
coveredBy:
  - SchemaLookupTest::anUnreadableDatabaseLeavesTheDerivedSideStanding
  - SchemaLookupTest::bothSidesStandBesideEachOtherAndAgreementIsSaidPlainly
  - SchemaLookupTest::whatTypo3WouldChangeIsNamedByItsOwnChangeType
---

# D-DIS-022 — The schema the database has is answered and the rows are not

**`typo3_schema_lookup` gains what the database has beside what TYPO3 derives,
because the difference is the result and neither side alone carries it.**

`D-DIS-008` decided the boundary it had, and that entry stands revoked. So what
kept the live schema out is a sentence no live entry makes.

## Evidence

- `SchemaLookup` states the bound in its own docblock and cites `D-DIS-008` for
  it: the derived side, never what the database has. `D-DIS-012` revoked that
  entry on 2026-08-04, and `D-DIS-012` decides which driver reaches the server
  rather than which side the tool answers.
- The reason for the bound is in the same sentence. The live schema needs a
  schema to exist, and the caller asks the tool while it writes the file that
  creates it. That holds for the derived side and says nothing against an answer
  for both where the schema is there.
- The audit checklist of `typo3-extension-health` names persistence as a
  surface: TCA, schema, relations, upgrade paths. Nothing this server answers
  says whether the installation's tables match what the package declares. A
  reviewer reads `ext_tables.sql` and the database is not asked.
- The maintainer named the two tools another TYPO3 MCP server ships on
  2026-09-01. One lists tables and a table's columns and indexes from the
  connection. One compares the actual schema against `ext_tables.sql` and the
  TCA-derived one and reports what is absent, extra or incompatible. Both are
  read-only and neither touches a row.
- `doesNotCover` draws its line at the record and gives the reason. The process
  boundary is the whole of this server's security. So a row read would put the
  shell user's database access where a backend user's permissions belong. A
  column is not a row and that reason does not reach it.

## Decided

- One tool rather than two. The question is the same one, what a table is. A
  caller who asks it once gets the derived side, the actual side and where they
  disagree. Two tools would share an output schema, which `AGENTS.md` says is
  one tool.
- No new verb. `lookup` already names the shape: a table goes in, its columns
  come out.
- The rows stay out, and `doesNotCover` still says so in the words it uses
  today. What changes there is the sentence that claims the tool answers only
  what the container assembles.
- A caller asks for the live side rather than gets it with everything else. It
  opens a connection and lists a schema, which a caller with a question about an
  icon should not pay for. That is the shape `configuration` and `flexForm`
  already have.
- A schema that is not there is an answer rather than an error. An installation
  whose tables do not exist yet is the case the bound on the derived side
  served. The answer says so per table instead of refuses the call.

## Assumed

- That the drift worth a report is absent, extra and incompatible columns and
  indexes, which is what the other server reports. What a caller does with a
  type difference that is only a platform's form has no measure.
- That listing one table's columns costs one round trip to the database. A
  connection that is slow makes this the most expensive answer the server has.

## Wrong if

- The comparison reports differences on a healthy installation often enough that
  a caller stops to read them. That is the third **Wrong if** of `D-ANS-099` on
  a new surface.
- The live side is what callers ask for and nobody reads the derived side any
  more, which would say the two were never one question.
- The schema answer turns out to need a row to be useful, which would put the
  boundary above back where `doesNotCover` has it.
