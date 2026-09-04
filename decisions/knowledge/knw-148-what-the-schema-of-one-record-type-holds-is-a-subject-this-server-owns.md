---
id: D-KNW-148
title: What the schema of one record type holds is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatASubSchemaHoldsIsStatedAgainstTheTableSchema
---

# D-KNW-148 — What the schema of one record type holds is a subject this server owns

**A sub-schema carries the columns its type's `showitem` names and no others,
and the corpus says so, because core TCA hides the difference from every suite
the core runs.**

A patch that swapped one for the other would have stopped indexing every
relation field an installation keeps out of a form.

## Evidence

- `feedback/2026-09-03-105641`. A core bugfix turned on three facts about the
  Schema API and none of them was here: the session read `TcaSchemaBuilder`,
  `RecordFactory` and `SchemaTypeInformation` and ran two greps instead, and
  says it would do all of it again next time.
- Read in `.checkouts/13.4`, `.checkouts/14.3` and `.checkouts/main`.
  `TcaSchemaBuilder::findRelevantFieldsForSubSchema()` walks the type's
  `showitem`, unpacks a `--palette--` into the columns of that palette, skips
  `--div--`, and keeps only what `columns` also declares. Nothing else is added:
  the sub-schema's `FieldCollection` is built from that list alone.
  `getFinalFieldConfiguration()` merges `columnsOverrides` over each with
  `array_replace_recursive`.
- The relation map is the table's. It is built once and passed into every
  sub-schema's field, under the builder's own comment that it is not possible to
  modify this for a subtype.
- `getSubSchema` over `typo3/sysext/*/Classes/` names the same twenty classes on
  14.3 and on main. `DataHandler`, `FlexFormTools`, `RecordFactory`,
  `PageDoktypeRegistry` and `SearchableSchemaFieldsCollector` are the core ones.
- `SchemaTypeInformation::isPointerToForeignFieldInForeignSchema()` is true
  where `ctrl.type` names a field of another table, which `sys_file_reference`
  does as `uid_local:type`. `RecordFactory::createRawRecord()` and `DataHandler`
  skip the resolution there, under a `@todo` saying the actual record type is
  defined in the foreign record.

## Decided

- Step 1a. `bin/cli hints:probe` for the subject reached `tca-schema-api`, which
  says to use the API and nothing about what it answers for one type.
- A hint of its own, `tca-sub-schema`, rather than four statements on
  `tca-schema-api`: "should I read `$GLOBALS['TCA']`" and "what does this type's
  schema hold" are two questions, and the first hint closes by naming the second
  and what it prevents.
- The Forge number stays out of the hint. `HintsTest` reads a bare number as a
  snapshot, and what the corpus owes a caller is the mechanism; the report is
  evidence and lives here.
- The per-row cost is stated as the core states it — the divisor is in the
  foreign record, so resolving it means reading that record — rather than as the
  query count the session inferred.

## Assumed

- That the twenty classes naming `getSubSchema` are the whole of the sub-schema
  side. The hint names the grep rather than the list, so a class added later is
  found by the reader rather than missed by the file.

## Wrong if

- A later major adds fields to a sub-schema that no `showitem` names — system
  fields, or the columns of a palette nothing shows — which would make the first
  statement wrong on that branch.
- Relations become overridable per type, which the builder's own comment is the
  only thing standing behind.
