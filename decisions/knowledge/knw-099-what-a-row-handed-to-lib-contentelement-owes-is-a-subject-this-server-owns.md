---
id: D-KNW-099
title: 'What a row handed to lib.contentElement owes is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::whatAPartialRowCostsIsReachedFromItsException
  - HintsTest::whatAPartialRowCostsIsWithheldWhereItRenders
---

# D-KNW-099 — What a row handed to lib.contentElement owes is a subject this server owns

**What a row for `lib.contentElement` has to carry since v14, and that
`RecordFactory` rejects one that does not, is inside this server's boundary and
absent.**

The change arrived with no changelog entry, so the corpus is the only source
that could carry it. A caller with the exception has three words: the class, its
code and the field it names. None of them reaches anything here or in the
changelog.

## Evidence

- `feedback/2026-08-18-074350` reports a session that never asked, on the
  assumption that a changelog query would not reach the change. The assumption
  holds, and for a reason no query shape fixes: there is no entry.
- The change is real and is a v14 one.
  `fluid_styled_content/Configuration/TypoScript/Helper/ContentElement.typoscript`
  carries `dataProcessing { 1770716912 = record-transformation }` in
  `.checkouts/14.3` and nothing of the kind in `.checkouts/13.4`.
- It shipped as `b0ee153010`, "[TASK] Use f:render.text in fluid_styled_content"
  (#108935, `Releases: main`), six files, none of them a changelog entry. So a
  behaviour change that breaks third-party code is absent from the changelog.
  The commit that made it was not the kind that writes one.
- `IncompleteRecordException` occurs in no changelog entry at all in
  `.checkouts/14.3`, only in `Domain/RecordFactory.php`, its own class and
  `RecordFactoryTest.php`. `RecordTransformationProcessor` occurs in exactly one
  — `13.3/Feature-103581-AutomaticallyTransformTCAFieldValuesForRecordObjects` —
  which is about TCA value transformation and names `lib.contentElement`
  nowhere.
- The corpus is silent in the same words.
  `bin/cli hints:probe "IncompleteRecordException"` reaches nothing, and the
  term occurs nowhere below `knowledge/` or `skills/`.
- The two hints a caller does reach are each about something else.
  `page-content-element-rendering` is the template, partial and layout roots of
  `lib.contentElement` and says nothing about its data processing.
  `frontend-records` names `record-transformation` as a nested processor under
  `database-query` and never as something `lib.contentElement` runs by itself.
- Those words reach the manual neither. It matches against page titles and
  section paths, so no page has a PHP identifier as its title. That is the read
  that narrowed `D-ANS-010`, which is why this is a statement to write rather
  than a routing to fix.
- The mechanism is three guards rather than one, which is what made the session
  that reported iterate. `RecordFactory` throws `1726046917` for a
  language-aware table without a language field, and `1726046918` for a
  workspace-aware one. It throws `1726046919` for any field a system capability
  declares, at `RecordFactory.php:241`, `:267` and `:289` in `.checkouts/14.3`.
  A caller who fixes the field one exception names meets the next.

## Decided

- **Queued**, not closed on the spot. The gap is a statement about TYPO3. A
  session has to read what the field set actually is against the checkouts,
  which this judgement did not do.
- The subject is the row rather than the processor. That is what a synthetic
  `tt_content` row handed to `lib.contentElement` through `f:cObject` owes since
  v14, and what happens when it does not. Which rows a query returns stays with
  `persistence-reading`, what a `Record` hands out stays with
  `record-system-properties`, and the template roots stay with
  `page-content-element-rendering`.
- Nothing changes about `typo3_changelog_lookup`. The feedback's suggestion,
  make the entry reachable from the symptom, has no entry to reach. The
  identifier search `D-ANS-042` already built would find one if it existed.
- The card goes to `normal`. One session reported it, which does not lift a card
  on its own. What lifts this one is that the changelog cannot answer it at all.
  So a session that does the right thing still ends up in vendor source.
- Recorded here rather than against `D-ANS-010`. That entry is about a silence
  the manual answers, and this silence belongs to neither corpus. The event
  happened and nobody wrote it down anywhere outside the commit.

## Assumed

- That the field set is stable enough across the covered majors to be one bound
  statement rather than a table. The guards are the same three in
  `.checkouts/14.3`; which fields `tt_content` declares capabilities for was not
  read.
- That the third failure of the session that reported is a property of the
  transformation rather than of that extension's row. That failure is `fe_group`
  read as a string. The feedback reports it and this run did not verify it.
- That a caller meets this with the exception rather than the processor's name,
  so `appliesTo` has to carry the class and the three codes.

## Wrong if

- The read finds `lib.contentElement` runs the processor on 13.4 as well through
  some other route. That would make this a statement without a version boundary
  and the feedback's account of what changed wrong.
- A row without those fields turns out to render anyway on a `tt_content` whose
  TCA a project has trimmed. That would make the guard a property of the schema
  rather than something a hint can tell a caller.
- The core writes a changelog entry for this after all, in a 14.x still to come.
  That would make the gap a delivery question and put the answer back in
  `typo3_changelog_lookup`.

## Confirmed on 2026-08-18

A hint of its own fills the gap rather than a statement added to the neighbour.
A caller with the exception and one who asks about template roots share the
entry point and nothing else.

The first **Wrong if** does not hold. The older major registers the processor
too, and what the newer ones add is the wire-up, which is what the bound says.
The second holds and the statement survives it. It states the rule as every
field the schema declares a capability for and gives the core's list as what
that comes to.

Both **Assumed** hold. The reported failure is a property of the transformation
rather than of that row. The date fields are the same trap the other way round.
Two failures the judgement did not know about are in the hint, because both come
before the exception a caller would arrive with.
