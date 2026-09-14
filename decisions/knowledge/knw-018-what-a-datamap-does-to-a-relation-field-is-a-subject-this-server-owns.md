---
id: D-KNW-018
title: 'What a datamap does to a relation field is a subject this server owns'
date: 2026-08-02
status: confirmed
---

# D-KNW-018 — What a datamap does to a relation field is a subject this server owns

**What a datamap does to a relation field is inside this server's boundary and
absent from it. So the feedback shrinks to that half and goes to the queue.**

The corpus tells a session to seed through DataHandler and says how to write a
scalar field. A session that follows it has nothing at the first inline field,
which is where this one reached for SQL.

## Evidence

- Two of the three things the **Suggestion** asks for are already answered, at
  more depth than it asks for. Called over stdio with the feedback's own query
  and `targetVersion: "14.3"`, `typo3_hint_lookup` returns
  `datahandler-persistence` and `sitepackage-initial-content`. The first ends on
  a sentence about the backend user. "DataHandler acts as a backend user … which
  is what makes DataHandler the right way to seed and a direct INSERT the wrong
  one". The second carries the seed flow the suggestion names. Seed with
  DataHandler and then export, the throwaway script that boots TYPO3 for one,
  and `impexp:export` with one `--table` per table.
- Both are older than the session that missed them. They landed on 2026-07-29 in
  `2d0c533` and `434b5c2`, and the feedback carries the date 2026-08-01. This is
  therefore not a version of the server that no longer exists.
- The relation half is in neither, and in nothing else here. No hint names
  `foreign_field` or a comma list of child uids. `inline` occurs below
  `knowledge/` only where a relation is *read* in a template
  (`frontend-records`) or *declared* in TCA (`content-elements`).
- The query reaches the right hint, and the hint has nothing to say.
  `bin/cli hints:probe "IRRE inline child records written through DataHandler datamap parent field"`
  returns `datahandler-persistence` at `appliesTo(18) + text(324)`. Its seven
  statements are the datamap, the position pid and the backend user.
- The feedback's claim about TYPO3 holds, and one method carries the answer. On
  `.checkouts/14.3`, `DataHandler::checkValue_inline_processDBdata()` starts a
  `RelationHandler` on the value array of child uids. Where the field declares
  `foreign_field` it calls `writeForeignField()` and stores `countItems(false)`
  in the parent's own column. The comment at the write site says the same: "list
  of children (csv) or number of relations (foreign_field)".
- So the int column that "rejects a comma list" is a counter DataHandler
  maintains. A write of the child's parent column by hand repeats work
  DataHandler had already done. That is one statement, and this run may not
  write it.

## Decided

- Step 1a of the ladder on the relation half, and queued rather than closed on
  the spot. What lands is a corpus statement about TYPO3, read across three
  majors.
- The feedback shrinks rather than goes to the archive. Two thirds of its
  suggestion have their answer in the words its own query uses, and the third is
  the todo.
- Not step 2 and not step 3 for that half. There is no rule here to move to
  where the task passes, and no entry to route the query at.
- The delivery half of the same session, that no lookup happened until the user
  demanded one, is
  `feedback/2026-08-01-003356-did-not-consult-the-mcp-knowledge-server-or.md`.
  It has its judgement on its own card. Walking it again here is how one gap
  gets a second entry.
- The pid and sort this feedback says the session guessed at are
  `feedback/2026-08-01-003927-no-understanding-of-pid-storage-semantics.md`.
  `datahandler-persistence` already carries two statements about them.

## Assumed

- The statement belongs on `datahandler-persistence` rather than on
  `content-elements`. A session arrives at it while it writes records, not while
  it declares the TCA that describes them.

## Wrong if

- What the todo establishes depends on the relation kind rather than on the
  datamap, so no single statement holds for "a relation in a datamap". The hint
  then has to separate `inline` with `foreign_field`, `inline` without one, `MM`
  and the plain csv case, or say nothing.
- The mechanism differs across 12.4, 13.4 and 14.3. The statement then needs a
  version range per branch, and the hint gains three statements where the todo
  planned one.

## Confirmed on 2026-08-02

Both **Wrong if** had their read and neither held, so the statement landed as
one and without a range. The relation kind does not split it. Two of the three
branches end in the same counter, and only a relation with neither storage keeps
the comma list. So it is "the parent column holds a count wherever the relation
lives elsewhere" rather than four cases. The majors do not split it either. What
differs is a default argument, a parameter type, and one field read off the
schema instead of the global array.
