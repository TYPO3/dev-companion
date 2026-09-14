---
id: D-DOC-005
title: A number is three digits so a group lists in order
date: 2026-08-02
status: confirmed
coveredBy:
  - DecisionsTest::everyNumberIsThreeDigitsWideSoAGroupListsInOrder
  - RequirementsTest::everyNumberIsThreeDigitsWideSoAGroupListsInOrder
---

# D-DOC-005 — A number is three digits so a group lists in order

**Every requirement and decision number is three digits wide, in the file name
and in the id alike. Every reference to one carries the same form.**

Nothing about the corpus was wrong; it listed wrong. A directory of 39 entries
opened with the tenth, and so did the index generated from it.

## Evidence

- Five groups had passed nine entries — `requirements/knowledge/` at 39,
  `requirements/discovery/` at 22, `requirements/answers/` at 14, and
  `requirements/feedback/` and `decisions/feedback/` at 10 each. In every one of
  them a file listing ran 1, 10, 11, …, 2.
- `Decisions::all()` compares ids as text, so the generated index carried the
  same disorder as the directory. `decisions/feedback/readme.md` listed
  `D-FBK-10` first, then 6 through 9, then 1. `Requirements::all()` sorts with
  `strnatcmp` and was already right, which is why the commands looked fine from
  one side and not from the other.
- Padding it cost 183 renames and 1393 ids rewritten across 289 files. All of it
  was mechanical: the number is the only thing that changed.

## Decided

- Three digits rather than two. `requirements/knowledge/` is at 39 and gains an
  entry per feedback worked off, so two digits puts this same rename back on the
  table at 100. `todo/` already numbers three wide, so this is the width the
  repository had anyway.
- The pad goes on the id, not only on the file name. A pad on the name alone
  would have sorted the directories and left R-KNW-39 and `knw-039-` as two
  forms of one number. `D-KNW-003` refuses that for a word, and it is no more
  acceptable for a number. One number, one form, everywhere it stands.
- A letter suffix survives it: `R-ANS-008b` is still the entry split off
  `R-ANS-008`. Three digits and an optional letter is the whole shape.
- Every mention got the rewrite, archived feedback,
  `scenarios/runs/REVIEW-02.json` and the four todos that carry an id in their
  file name among them. So a search for an id finds every place that names it
  rather than the places written after today.
- `bin/cli requirements:check` and `bin/cli decisions:check` fail on any other
  width. A test per corpus sorts each group's file names as text and as numbers
  and requires the same order. The check is on the width because that is the
  thing a hand-written entry gets wrong. The test is on the order because that
  is the thing the width is for.

## Assumed

- That no group reaches 1000 entries. `requirements/knowledge/` took a year to
  reach 39.
- That a rename of an id inside a record of something that already happened does
  not falsify the record. A number is a name for a requirement, not a
  measurement taken at the time. A run that named R-DIS-9 in the old form was
  about the entry now called `R-DIS-009`. Archived feedback, the recorded run
  and the todo names were rewritten on that basis.

## Wrong if

- Somebody reads a commit message, a review comment or a note outside this
  checkout that names an id in the old form. They search the corpus for it and
  find nothing. The history keeps that form and there is no redirect from it;
  whether it costs anything is the open question here.
- A group passes 999 and the width has to move again, with the same rewrite over
  a corpus that will be larger than 289 files.
- The ids read as noise in prose. Every one of the 1393 is a character or two
  longer than it was, most of them inside sentences a person reads.

## Confirmed on 2026-08-22

The width holds and the shape it was chosen for is what happened. The largest
group is `D-KNW` at 105, so two digits would have run out three weeks after this
entry. The rename the entry priced at 183 files would be back, against a corpus
that has roughly doubled. `R-ANS-008b` is still the only letter suffix and still
lists where it belongs.

Nothing has come back on the first **Wrong if**. The two-digit form survives in
six places outside the git history, and all six quote it deliberately. Three in
this entry and three in the test that holds it. No feedback, todo or run names
an id in it. The second **Wrong if** is further off than it was: 105 of 999.

The third is the one a sweep cannot settle. Whether 1393 ids read as noise in
prose is a judgement about every sentence they stand in. What would say so is a
report from somebody rather than a sweep.
