---
id: D-ANS-075
title: The hint index is ordered by the rank the matcher already computed
date: 2026-08-11
status: open
coveredBy:
  - HintsTest::aMissNamesWhatThereWouldHaveBeenToFind
  - HintsTest::anAnswerStillNamesTheIdsItDidNotReturn
  - HintsTest::theIdsOfferedAreTheOnesThatMajorHas
  - HintsTest::theIndexIsNotOfferingWhatTheSameAnswerWithheld
  - HintsTest::theIndexNamesWhatTheLimitCutBeforeWhatTheFloorRefused
---

# D-ANS-075 — The hint index is ordered by the rank the matcher already computed

**`availableHints` comes from the candidates `find()` has just scored, closest
first, instead of from a second read of the corpus in file order.**

The fourth **Wrong if** of
[`D-KNW-055`](../knowledge/knw-055-the-first-check-a-standalone-extension-gets-is-a-subject-this-server-owns.md)
fired and disproved the remedy it named. A session gave up on the index, and the
id it needed was in it. It stood three from the bottom, while the matcher had
just ranked it seventh.

## Evidence

- **The reported call, re-measured on 2026-08-11 at `targetVersion=15`.** The
  query of `feedback/2026-08-10-182451` selects `css`, `fluid` and `typescript`,
  scores 52 candidates, admits 13 and returns 6. `javascript-unit-tests` is rank
  **7 of 13** — `limit=6` cut it by one place — and stands at position **43 of
  46** in `availableHints`. The session that reported it spent six calls on a
  rebuild of that hint's subject from the filesystem.
- **The rank is in hand when the tool builds the index.** `Hints::index()` runs
  a second `load()` and orders what it finds by file, in the same `return` where
  `find()` still holds the sorted `$scored`. Nothing separates a hint the
  matcher ranked seventh from one that answered nothing.
- **The index is two things, and only one of them can carry a rank.** Of the 52,
  seven passed and the limit cut them, and the coverage floor refused 39. The
  first seven carry a rank the matcher stands behind; the rest carry a score it
  has already judged too low to answer on.
- **The second id of the same report is not this question, and not a gap
  either.** `css-tokens-specificity` is 16th of the 39 refused, tied at 47 with
  `css-light-dark-mode` and `css-rtl-logical-properties`. It stood at position 5
  by file order, a coincidence rather than an answer. The query never asked for
  it, and a query that does gets it. `bin/cli hints:probe` on 2026-08-11 returns
  it first and on the curated vocabulary for three queries. Those are "css
  custom property token specificity", "derive a css variable from another
  instead of hardcoding a value", and the session's own dilemma, "should this
  fade distance get its own custom property or borrow --typo3-spacing".
- **The index offers what the same answer refuses.** A measurement on 2026-08-11
  covered a frontend query that withholds `Backend CSS` as inverted advice. The
  answer says it withheld the category and then lists 19 of its hints by id.
  `index()` filters on `$selected` while the matcher filters the candidates. The
  three `backendOnly` exclusions appear the same way.
- **Withdrawal removes the only place that named the id.** `D-KNW-055` measured
  the index at 47 of an answer's 126 lines and 3545 of its 13080 bytes. Its own
  account of the trigger is that the count was never the cost.

## Decided

- **Ordered whole, not cut to a band.** The near misses come first, in the
  matcher's own order, and what the floor refused follows by score. A cut of the
  tail had its price and failed. It would have dropped `css-tokens-specificity`
  out of the answer altogether, and that id is half of what the report is about.
- **Built from the candidates rather than filtered afterwards.** The refused
  hints stay where the matcher scores them instead of drop out, so the index is
  the list the matcher already worked on. What follows from that is what the
  index no longer carries. A hint withheld for the frontend and one excluded
  because it displaces no longer appear by id. Nor does one whose statements do
  not hold on the target. None of them was ever an answer this call would give.
- **Flat, with the order said in the copy.** A tier marker in the payload would
  be a second concept for the reader. The first entry is what a caller acts on
  either way.
- **`index()` is what the id path uses, and nothing else.** It answers where no
  query matched and no rank exists, which is the one case a corpus read is the
  right source for.
- **The report's own suggestion is not taken.** It asks for a split of the ids
  whose category matches an established domain from the rest. Every entry of
  this index already matches one, so the split would be the whole list. What
  separates them is how far each got, which is what the order now says.

## Assumed

- **That a caller reads the head of a list and not its middle.** It is what the
  report describes: the list "is long, uniform, and arrives attached to an
  answer about something else". It is why this entry expects the order to be
  worth more than the length. Nothing has measured a session against an ordered
  one.
- **That the refused tail is worth its lines.** It stays because
  `css-tokens-specificity` was in it and because a miss has nothing else to say,
  not because anybody has read one.

## Wrong if

- A session gets an ordered index, the id it needed is at the top, and it goes
  to the filesystem anyway. Then the order was never what stopped it, and what
  remains is the length or the place the list stands in.
- A hint the floor refused turns out to be what a session needed, and it was
  below the seven the limit cut. Then the two tiers are the wrong split and the
  matcher's floor is what has to move.
- The index no longer names a subject a caller then fails to find at all,
  because the candidate filter took it out. Then a filter on the index by what
  the matcher refused is narrower than the field's purpose, and the domain read
  should build it.

## Since then

The measurement does not settle **Decided**. It prices the index and shows the
ids were reachable, and it cannot say what the sessions that do use the index
would lose.

The maintainer answered on 2026-08-18, and **Decided** turns around on the id
path. A call that names an id and matches one carries the index empty and counts
what it left out. A call that matches by paths or task stays as it was. Measured
after the change on one id, the answer falls from 8,227 characters of text to
1,565 and from 10,859 of payload to 1,843. It counts 93 neighbours rather than
lists them. The **Wrong if** is a session with the count in hand that needs one
of those 93 and does not ask.
