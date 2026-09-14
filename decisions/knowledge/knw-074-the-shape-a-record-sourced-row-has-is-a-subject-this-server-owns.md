---
id: D-KNW-074
title: 'The shape a Record-sourced row has is a subject this server owns'
date: 2026-08-14
status: confirmed
---

# D-KNW-074 — The shape a Record-sourced row has is a subject this server owns

**What `RecordFactory` moves out of a record's properties, and that
`$row['hidden']` is therefore empty rather than false, is inside this server's
boundary and absent.**

The corpus answers which rows a query brings back and stops there. The shape of
the object it brings back is one layer further on, and the failure it leaves
open is silent. An enable field that is not in the array reads as permitted, so
a hidden record renders and nothing throws.

## Evidence

- Re-run on 2026-08-14 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own task, "Record API SystemProperties hidden starttime
  endtime fe_group enable fields", reaches `persistence-reading`,
  `frontend-access-restriction` and `frontend-records`. Those are the three the
  feedback reported and in that order.
- The vocabulary is absent. `SystemProperties`, `RecordFactory`,
  `getSystemProperties`, `isDisabled` and `toArray(true)` occur nowhere below
  `knowledge/` or `skills/`.
- The two neighbours are each about something else. `persistence-reading` is
  restriction containers, `versionOL` and the language overlay — which rows come
  back at all. `frontend-records` names the `record-transformation` data
  processor that produces the objects and says nothing about what is in one. So
  a caller who reaches it learns where records come from and assumes the fields
  are still there.
- The mechanism holds. In `.checkouts/main`, `RecordFactory` unsets from
  `$properties` and constructs `SystemProperties`, at `RecordFactory.php:215`,
  `:272`, `:331` and `:334`. `Record::toArray()` adds `'_system'` only when
  asked, `Record.php:65` and `:75`.
- The subject has a version boundary. `Domain/Record.php`,
  `Domain/RecordFactory.php` and `Domain/Record/` are in `.checkouts/13.4`,
  `14.3` and `main`, and none of them is in `12.4`.
- The worked example the feedback names does not resolve.
  `RecordIdentityRenderer`, `resolveUserGroupRestriction` and `resolveTimestamp`
  are in no covered checkout. The session that reported it worked in a core
  checkout of its own, which is where an unmerged change would be.

## Decided

- Built, as a hint of its own rather than more sentences on
  `persistence-reading`. One hint is one question (`D-KNW-030`). That one
  answers which rows a query returns, this one the shape of the row it returns.
  The second is what a caller asks after the first has already worked.
- The boundary is the object a `Record` hands out. That is what `RecordFactory`
  moves into `SystemProperties`, the `_system` keys with their types, and the
  accessors. And the two shapes a row reaches a caller in, a flat array with the
  enable fields at top level against `toArray(true)`. Which rows come back stays
  with `persistence-reading`, and what a data processor produces stays with
  `frontend-records`.
- The hint stands around the silent failure rather than around the accessor
  list. A caller who hears the accessors exist still has no reason to stop with
  `$row['hidden']`, because that read returns a value and the value looks right.
- The card goes to `normal`. One session reported it, which is not the weight
  that lifts a card on its own. What lifts this one is that the failure reads as
  a correct answer. A session pays a gap whose cost is a wrong lookup once, and
  whoever visits the rendered page pays this one.
- What any of it says about TYPO3 waits for the reading. This judgement ran a
  probe, two searches over this repository and one over `.checkouts/main`. So a
  session establishes the `_system` key list, the types and the second shape
  against `13.4`, `14.3` and `main` and binds them there. That is the todo's
  first step.

## Assumed

- That a caller reaches this with no idea that the class exists. The session
  that reported queried `SystemProperties` by name, but a session that has the
  bug asks about `hidden` on a row. So `appliesTo` has to carry both the class
  names and the field names.
- That the second shape is FormEngine's `databaseRow`. That is where the session
  that reported met it and this run did not read it. So the research may find
  the pair is with something else or with several things.

## Wrong if

- The read finds the enable fields are still among the properties on one of the
  covered lines. That would make this a version boundary rather than a
  statement.
- `typo3_schema_lookup` turns out to answer the record's shape from the
  installation, which would make it an answer rather than a hint.
- The `_system` keys differ enough across `13.4`, `14.3` and `main` that the
  hint is a table of versions rather than something a caller can hold.

## Confirmed on 2026-08-14

Read across the three checkouts that have the API. The third **Wrong if** does
not hold and could not have. The files are the same on all three but for a typo
fix. So the key list, the types and the accessors are one bound statement.

The first holds in one place and moves the boundary rather than the subject. On
the newest major one table's object rebuilds the row from the raw record, so
every selected column is there. What the judgement did not have is the asymmetry
in property access. The raw record is in reach only where the record has no
type, so the advice fails on exactly the tables the report came from.

The second **Assumed** stays open and the hint no longer rests on it. The value
formats in the flat shape are a data provider's rather than the row's. The
concrete case the report named is not reproducible in a covered checkout either.

## Since then

The work this entry queued landed: `record-system-properties` carries the shape,
and
[`D-KNW-078`](knw-078-the-corpus-states-the-shape-a-record-sourced-row-has.md)
is what states it. Its **Wrong if** is a different list. What can go wrong from
there is a core that moves under a statement, not a subject nobody wrote down.
