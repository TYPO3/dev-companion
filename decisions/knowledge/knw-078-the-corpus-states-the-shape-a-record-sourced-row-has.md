---
id: D-KNW-078
title: The corpus states the shape a Record-sourced row has
date: 2026-08-14
status: open
readings:
  - 2026-09-01
coveredBy:
  - HintsTest::theRecordShapeIsWithheldFromTheBranchThatHasNoRecordApi
  - HintsTest::theShapeOfARecordSourcedRowNamesTheFieldsThatMoved
---

# D-KNW-078 — The corpus states the shape a Record-sourced row has

**A record's enable fields are under `_system` and nowhere else, so
`$row['hidden']` on such a row is absent rather than false.**

`record-system-properties` carries it beside `persistence-reading` rather than
inside it: one hint is one question, and the two questions are which rows a
query returns and what the object built from one of them looks like.

## Evidence

- The reading is
  [`D-KNW-074`](knw-074-the-shape-a-record-sourced-row-has-is-a-subject-this-server-owns.md)'s
  **Confirmed on**, taken across `.checkouts/13.4`, `14.3` and `main` on the
  same day. What it settled is that `SystemProperties` is the same file on all
  three, that `pages` is an exception from 14, and that the property access
  answers on one table and throws on the next.
- The feedback's own query reaches the new hint first —
  `bin/cli hints:probe "Record API SystemProperties hidden starttime endtime fe_group enable fields"`
  on 2026-08-14, where it matches on `appliesTo` and on its own words, above the
  three hints the report was given.
- The neighbours keep their own questions.
  `hidden record is missing from my query result` still leads with
  `persistence-reading` and
  `fe_group on a parent page is not inherited by subpages` with
  `frontend-access-restriction`, probed the same day.

## Decided

- The hint is written around the silent read rather than around the accessor
  list. `$row['hidden']` is absent, an absent key reads as empty and empty reads
  as not disabled — a caller told only that `getSystemProperties()` exists has
  no reason to stop reading the array.
- The `pages` exception is one bound statement rather than a hint of its own. It
  is the same subject seen from one table, and a caller reading the shape has to
  be told where it does not hold.
- The domains are `php` alone. What a Fluid template gets when a record is
  assigned to it is
  [`D-KNW-075`](knw-075-how-fluid-resolves-an-object-path-is-a-subject-this-server-owns.md)'s
  question, and claiming it here would answer a template question from a reading
  of the PHP objects.
- `appliesTo` carries the class and accessor names and the moved field names,
  and not the bare `hidden`. A session that has the bug arrives with
  `starttime`, `endtime`, `crdate`, `tstamp` or `fe_group` and reaches it; the
  one word every question about a disabled record carries would take queries
  `persistence-reading` and `frontend-access-restriction` answer.
- `persistence-reading` gains one sentence pointing here, bound `since: 13`. It
  is where the reporting session landed, and it ends exactly where this begins.

## Assumed

- That a caller meets the shape through an array rather than through the object.
  Both accessors are stated, but the sentences are ordered for somebody holding
  `$row`, because that is the form the failure was reported in.
- That `_system` stays one level deep. `language` and `version` are objects
  inside an array that is otherwise scalars, and a caller serialising the row is
  told they are objects rather than what to do about it.
- That the two shapes are worth naming as two. The core carries no worked
  example of code consuming both — `RecordIdentityRenderer` is in no checkout —
  so the correction is stated as what follows from the shapes rather than as
  what the core does.

## Wrong if

- A covered branch starts leaving the enable fields among the properties, or
  `Page`'s rebuilt row spreads to another table. The first statement would then
  hold on fewer branches than it claims and the exception would be the rule.
- The `_system` keys gain or lose one. The list is stated in full and unbound,
  so a key added on `main` makes it wrong there rather than incomplete.
- A session reads the hint and still asks the record for the field by name. The
  lever would then be the `get()` asymmetry rather than the array read, and it
  is the last statement rather than the first.
- The same question is reported again from a Fluid template. `{record.hidden}`
  is the other surface of the same failure, and this hint answers PHP.
