---
id: D-KNW-020
title: 'What a preview template is handed is stated on both majors'
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo
---

# D-KNW-020 — What a preview template is handed is stated on both majors

**The corpus states what a preview template gets on each major, and what a field
read off the record resolves to.**

[`D-KNW-014`](knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md)
is the result: a session arrived at a template with one variable in it and
nothing here said what that variable was. This entry is what took its place. The
statements are on `content-elements`, and what they can now be wrong about is
their content rather than their absence.

## Evidence

- The `content-elements` hint carries the assignment split by major.
  `since: 14`: the template "is handed one variable, record", and a template
  that reads `{header}` renders an empty spot and logs nothing. `until: 13`:
  beside `record` it gets every column of the row and a plugin's FlexForm as
  `{pi_flexform_transformed}`.
- The resolution half carries `since: 13` rather than `since: 14`, because both
  majors assign the record. `.checkouts/13.4`'s
  `FluidBasedContentPreviewRenderer` calls `assignMultiple($row)` at line 95 and
  `assign('record', …)` at line 99; `.checkouts/14.3` assigns `record` alone at
  line 75.
- The mechanism the session could not find stands there. The record is a PSR-11
  container and Fluid asks `has()` and `get()` for a path segment before it
  tries any getter. So every field is `{record.<column>}`. What the schema does
  not declare is not on the record. The enable fields, the timestamps, the sort
  and the language and workspace columns are
  `{record.systemProperties.disabled}`. A path that hits neither resolves to
  null rather than raises.
- What a field resolves to has its name by TCA type rather than as one rule for
  "a relation". `type=select` with a relation, `group`, `inline`, `category` and
  `file` come back as records, a relation to many as a lazy collection `f:for`
  iterates. `type=select` without a relation stays values, the single value
  where `renderType` is `selectSingle`.
- The subject is in reach from the question it was absent for.
  `bin/cli hints:probe` on "Record API field access in a backend content element
  preview Fluid template" puts `content-elements` first at
  `appliesTo(15) + text(288)`, ahead of `fluid-templates`.

## Decided

- `D-KNW-014` stands revoked in place rather than rewritten. Its statement was
  that the answer is absent here, which no longer held the afternoon the
  statements landed. Neither of its **Wrong if** could fire against a gap that
  has closed.
- `R-KNW-041` now rests on this entry rather than on the revoked one. The
  argument under the requirement moved rather than went. Left with a pointer at
  `D-KNW-014` it reads out of `bin/cli unresolved:list` as a requirement whose
  ground is gone.
- The version split stays data on the statement — `since` and `until` — and no
  sentence names a major
  ([`D-VER-001`](../versions/ver-001-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md)).
- Not restated here: the research the statements came from. It is in
  `D-KNW-014`'s **Evidence** and its **Confirmed on**, which is what the revoked
  entry stays for.

## Assumed

- The five types are the whole of what a preview template meets as a relation.
  They came off the branch a field takes in `RecordFieldTransformer`, not from a
  template rendered over every TCA type.
- A session that writes a preview template arrives from the registration, so
  `content-elements` is where it reads this rather than `fluid-templates`. That
  is the assumption `D-KNW-014` made, kept because the probe above bears it out.

## Wrong if

- A sixth field type starts to resolve to records, or one of the five stops. The
  statement then names a set that is wrong. A template written to it renders an
  empty spot the author only sees in the page module.
- A later major changes the assignment again. The `since: 14` half then
  describes a boundary rather than the present. A caller on that major hears the
  row's columns are gone when something else is.
- Fluid no longer resolves a path segment through `has()` and `get()` ahead of
  the getters, or the core no longer hands the template a container.
  `{record.<column>}` then works for another reason, and the sentence that
  explains why is right by accident.

## Since then

A second question about the same variable got its judgement on 2026-08-18 and
sits in the queue beside this one. These statements say how to read a field off
the record and never what the record or the field is as a PHP type. That is what
a typed `f:argument` declares,
[`D-KNW-090`](knw-090-the-corpus-names-the-php-type-a-record-arrives-as.md).
Both land on `preview-record-variable`, so whichever lands second reads the
statements this one put there rather than adds beside them.

