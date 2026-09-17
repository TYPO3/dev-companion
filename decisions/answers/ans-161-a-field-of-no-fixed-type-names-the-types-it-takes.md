---
id: D-ANS-161
title: A field of no fixed type names the types it takes
date: 2026-09-17
status: open
coveredBy:
  - ToolContractTest::everyFieldSaysWhatItIs
---

# D-ANS-161 — A field of no fixed type names the types it takes

**A field whose value can be several things declares each of them as an
`anyOf` branch, and no field stands with a description alone.**

A schema with no keyword but `description` is the object spelling of `true`. It
accepts any value. Some MCP clients refuse a tool over it, and the reference
printed `object` for a field that never holds one.

## Evidence

- **A client's schema check over the server on 2026-09-17.** It warned on the
  five value fields of `typo3_record_lookup`: the echoed filter, the grouped
  value, `groupDefault`, the departing value and the row values.
- **Ten such fields, in four tools.** The five above, the column `default` of
  `typo3_schema_lookup` twice, the field `default` of `typo3_flexform_lookup`
  twice, and the `value` of `typo3_configuration_lookup`.
- **What the probe hands back.** A row value is what Doctrine fetches, a
  string, a number or null. A column default is the same. The FlexForm probe
  keeps a default only where `is_scalar()` holds. The configuration value is
  the array or the scalar the path holds, whatever that is.

## Decided

- `Schema::scalar()` is a string, a number, a boolean or null, and it is what
  every value a database or a declaration holds declares.
- `Schema::any()` is the six JSON types as branches, and the configuration
  value is the one field that takes it. It says what a bare description said,
  as a keyword a client reads.
- `ToolSurface` prints every branch of an `anyOf`, so the reference says
  `string or number or boolean or null` where it said `object`.
- `ToolContractTest::everyFieldSaysWhatItIs` walks both schemas of every tool
  and fails by path on a field that carries a description alone.

## Assumed

- That a row value is never an array. Doctrine returns a blob as a string, and
  the probe decodes nothing.

## Wrong if

- A tool answers a row value or a default as an array or an object. The
  contract test validates every answer against its schema, and that answer
  fails it.
- The same check warns on `anyOf` over six types as it did on the bare field.
