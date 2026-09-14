---
id: R-FBK-007
title: 'The work already judged comes before judging more'
status: held
restsOn: [D-FBK-012, D-FBK-016]
heldBy:
  - TodoTest::everyOpenFeedbackIsOnTheBoard
  - TodoTest::everyTodoInAStageSaysWhereItStands
  - TodoTest::theQueueIsReadByPriorityAndThenByAge
---

# R-FBK-007 — The work already judged comes before judging more

**The command hands over a todo somebody has given a priority before one nobody
has judged, and it hands over one of them at all.**

A judgement is what turns a feedback into work, so anything with a priority is a
decision already taken. To reach for an unjudged one while those wait is to
decide twice and do nothing. `feedback/` fills from every session everywhere
while one session judges one, so whatever comes first comes first in every
session there will ever be.

An order between three groups used to carry this: the queue before the
sightings, and a sighting only once the queue was empty. The priority itself
carries it now, because the feedback are on the board rather than behind it. A
card written for a feedback is `low` until somebody decides the feedback is
worth more. That is the same place the sighting left it, without a second
mechanism to say so.

The size of the read is the same requirement from the other end. What the
command hands over has to stay the size of a session's work while the directory
grows without limit. One card is one card however many are on the board. The
portion no longer carries that somebody who was not the session can read its
judgements. A session writes a judgement into the decision it judged against, or
into a new one where nothing says it yet. So the record outlives the run.

## From

56 open feedback against 38 queued items on 2026-08-01. No session had reached
the queue since somebody wrote it, and the sighting printed 57 lines before its
own instruction. See
[D-FBK-012](../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md),
which carries both halves of this and names
[D-FBK-005](../../decisions/feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md)
as the entry that recorded the measurement. The order became a priority on
2026-08-02, with 67 feedback and an empty queue —
[D-FBK-016](../../decisions/feedback/fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md).
