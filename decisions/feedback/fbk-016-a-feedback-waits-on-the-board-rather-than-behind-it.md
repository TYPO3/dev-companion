---
id: D-FBK-016
title: A feedback waits on the board rather than behind it
date: 2026-08-02
status: open
coveredBy:
  - TodoTest::everyOpenFeedbackIsOnTheBoard
  - TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead
---

# D-FBK-016 — A feedback waits on the board rather than behind it

**Every open feedback has a todo of its own, written by `bin/cli todo:sync`, and
what asks for is the judgement rather than the fix.**

The pile was reachable only through a sighting that ran when the queue was
empty. A card per feedback puts it in the one place a session already looks, and
the priority does what the group boundary did.

## Evidence

- 67 open feedback against an empty queue on 2026-08-02, every one of them
  unjudged. The sighting was therefore due in every session, and the pile was
  visible only through a command written for it.
  [`D-FBK-012`](fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md)
  carries the 56-against-38 measurement.
- The relation was already there. `Todo::serves()` reads the queue, what is in
  hand and what waits, and `OpenFeedback` marks a feedback judged when any of
  the three names it. So a card in `todo/open/` that marks a feedback as taken
  on needed no new state, only a command that writes one.
- Absence of a priority is a decision of the same day and for this.
  [`D-FBK-015`](fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md)
  gave the order a fourth thing to say, nobody has judged this. That is what a
  feedback card needs and what nothing else does.

## Decided

- One card per open feedback, named after the feedback so the pair is visible
  and the card's age is when the report arrived. `bin/cli todo:sync` writes what
  is absent and is idempotent, because what already has one comes from
  `Todo::serves()` rather than from a record.
- **The card points and does not copy.** `**Serves:**` names the file and the
  heading is the feedback's own, so a listing says which one it is. Nothing else
  of the report is in the card. Two copies of a report drift, and the one in
  `todo/` would be the one nobody corrects.
- The step is the same on every card because it is the same step: judge it, and
  `documentation/records/judging.rst` says how. What the six answers are does
  not belong in 67 files.
- More than one card for one feedback is legitimate. A single report can need
  more than one change, and the relation was always many-to-one.
- `bin/cli feedback:next` and the sighting that ran it are gone.
  `bin/cli todo:next` now hands over the oldest feedback without a judgement, by
  the priority rather than by a group. A second command for it is a second
  answer to one question. `bin/cli feedback:list` stays: a read of the pile is a
  different question from work on it.

## Assumed

- That the backlog sighting still comes round. It comes when the queue is empty,
  which now also requires that every feedback has its judgement. That is rarer,
  and correct as long as the judgement really is the more urgent of the two.
- That 67 unjudged cards do not drown the board. They sort below everything
  judged, so a session sees them only when nothing decided remains, and
  `bin/cli todo:list` prints one line each.
- That the sync runs. Nothing calls it on a schedule, and a feedback recorded
  after the last run has no card until somebody does.

## Wrong if

- Nobody reads the board any more because it is mostly cards without a
  judgement, and sessions go back to `bin/cli todo:list | head`. That is the
  sighting's failure from the other side: too much at once, seen all at once.
- The backlog sighting never comes round at all, because the queue is never
  empty. Then the judgement of a feedback and the name of a backlog entry
  compete for the same slot. The second needs a clock of its own.
- Cards accumulate for feedback that closed elsewhere, or feedback accumulate
  with no card. Either means the sync does not run, and the relation is then a
  claim the board makes and does not keep.

## Since then

The third assumption failed on its first day. A commit brought twenty feedback
in and wrote no card for any of them. The board said so only when the next
session ran the check. The gap was the caller, which `D-FBK-022` made a
pre-commit hook and `D-FBK-045` then made the record call itself. Everything
decided about the board stands and only what writes it has moved. So the third
assumption no longer exists at all: there is no run to forget.
