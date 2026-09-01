---
id: D-FBK-052
title: A judgement that holds the evidence makes the change
date: 2026-08-24
status: open
---

# D-FBK-052 — A judgement that holds the evidence makes the change

**A judgement that already read the checkout makes the change in the same run,
and only one touching `src/`, a declared schema or a skill's contract still
waits.**

The rung said a todo is owed however small the change, "because the run that
judged it has read nothing but this repository". That reason is true of a run
that has not looked and false of one that has, and the second is the common
case: judging a knowledge gap means reading `.checkouts/` to find out whether
the gap is real.

## Evidence

- Measured over one day of five sessions at once, 2026-08-24. Of about
  twenty-four cards worked, ten were judgements, and roughly half came back as a
  build card that a later session took.
- Two of them re-read what the judging run had already read. `D-KNW-108` read
  `Import.php`, `ImportExport.php` and the placement code on all four covered
  majors; the session that wrote the hint read them again. `D-ANS-100` measured
  the Gerrit query operators against `review.typo3.org`; the session that built
  the search measured them again.
- Both re-readings were correct under the rules as they stood. Neither found
  anything the first had got wrong, which is what says the cost bought nothing
  in these two cases.
- The queue is what the cost shows up in: twenty-four cards worked moved it from
  36 to 33, because a feedback took two slots and new feedback arrived at a
  comparable rate.
- Approved by the maintainer on 2026-08-24, together with the half that was
  refused: allocating decision ids at claim time, which would have removed the
  four id collisions the same day produced.

## Decided

- The second bullet of *Closed on the spot* is bounded to a lookup that is still
  to be made. One the run made is evidence in hand, and the entry it was written
  into says what was read.
- The first bullet stands unchanged. A change touching `src/`, a tool's declared
  schema or a skill's contract is reviewed rather than improvised, whatever the
  session holds — that is the half this does not relax, and the two build steps
  above would both still have been queued under it.
- `Card::STEP` says it, because the card is what a judging session reads and the
  old wording — *judge rather than fix* — is what it acted on.
- The judgement is still written. What changes is when the work waits, not
  whether `decisions/` records why.

## Assumed

- That a run holding the evidence judges the size of its own step honestly. The
  rung above it is unchanged, so a session that finds the change larger than it
  looked queues it as before.
- That the re-reading was the cost rather than the verification. Two cases found
  nothing wrong; a third that had found something would say the second reading
  is worth its price.

## Wrong if

- A change made in the judging run is reverted or corrected, and the correction
  names something the second reading would have caught.
- Judgements start arriving with the change and without the entry, because the
  same run now has somewhere else to put its finding.
- The queue stops shortening anyway. Then the two slots per feedback were not
  what held it, and what did is the rate feedback arrives at.

## Since then

Twenty feedback from one debrief were judged on 2026-09-01, most of them with
the change in the same run, and the first **Wrong if** fired once: a hint
written that day said the record list draws an invalid value, and the next
judgement read `StandardContentPreviewRenderer` and corrected it to the page
module. One statement of six, caught the same day by the reading the next
feedback needed anyway. The third did not fire — the queue went from twenty
cards to none.

