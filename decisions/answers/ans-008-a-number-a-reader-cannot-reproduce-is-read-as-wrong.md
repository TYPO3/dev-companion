---
id: D-ANS-008
title: A number a reader cannot reproduce is read as wrong
date: 2026-08-02
status: open
coveredBy:
  - ProjectTest::aClassCountSaysWhatItCounted
---

# D-ANS-008 — A number a reader cannot reproduce is read as wrong

**A count in an answer says what it counted, because a caller who checks it and
gets a different number reports the answer as false.**

The first feedback judged off the board was exactly this. A correct count,
checked against the obvious command, disagreed with it, and the session filed it
as a wrong answer.

## Evidence

- `feedback/2026-07-31-172754`, re-run against
  `/home/benji/projects/bootstrap_package` on 2026-08-02. The tool reported
  `Updates (27)`; the session counted 21 and reported the count as overstated.
  Both numbers are right: 21 PHP files sit directly in `Classes/Updates/` and
  six more in `Classes/Updates/Criteria/`, which `Extension::countPhpFiles()`
  includes because its Finder carries no `depth(0)`.
- The count was not stale either. The last change to `Criteria/` in that
  checkout is from 2024-10-10 and its HEAD is 2026-06-30, so the directory was
  there on the feedback's date. Nothing about the answer had changed since.
- Neither place the number appears says which it is. The rendered line is
  `Updates (27)` with no qualifier. The schema says `PHP files below it`, which
  reads as "directly in" as easily as "anywhere under".

## Decided

- The judgement is **step 4 of the ladder**, wording, not a wrong answer and not
  a gap. What failed is that the reader could not reproduce the number. A reader
  who cannot reproduce a number does not conclude they measured something else;
  they conclude the answer is wrong. This one arrived as a `wrong-answer`
  feedback from a session that had done nothing careless.
- It is **queued rather than closed on the spot**, because the fix touches
  `src/` and a declared `outputSchema`.
  [judging.md](../../documentation/records/judging.rst) puts those on the
  reviewed side of that line.
- The judgement names the gap and not the fix. The count could become the
  shallow one, so that `ls` reproduces it, or stay deep and say so. That is a
  question about what the section is for. The todo settles it against the tool
  rather than here.
- Recorded as its own entry rather than against the tool, because the property
  is not about this count. The same method counts every kind under `Classes/`,
  and any number this server states has the same exposure.

## Assumed

- That callers check. This one did, and reported the difference rather than took
  the tool's word. That is the behaviour worth a design even though nothing
  measures how often it happens.
- That a statement of what the count covers is enough, and that a reader given
  "27 files, including subdirectories" reproduces it. The alternative, a match
  with the number a reader would produce unaided, assumes what command they
  would reach for.

## Wrong if

- A second feedback disputes a different number the same way. That would mean
  this is a property of every count here and belongs in a requirement rather
  than one todo.
- The qualifier lands and a later feedback still reports the count as wrong.
  Then the number was wrong and not the wording, and the shallow count was the
  right one all along.
- Nothing else in this server states a number a caller could check, which would
  make the generalisation above one case in a rule's clothes.

## Since then

The first **Wrong if** happened from beside it rather than head on. A session
disputes the same section again and not a number in it. A directory under no
recognised kind is in no line of the answer. So a count over the tree found
three files where the answer accounted for two.

The qualifier this entry asked for landed and holds. What the session could not
reproduce is the list of kinds. That is a property of that list rather than of
any count, and nothing decided here moves.
