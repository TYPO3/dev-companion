---
id: D-FBK-012
title: The queue comes first, and the sighting hands over one
date: 2026-08-02
status: open
coveredBy:
  - CliTest::theSightingsWaitForAnEmptyQueue
  - CliTest::whatRecursIsEitherAnAppointmentOrASighting
---

# D-FBK-012 — The queue comes first, and the sighting hands over one

**`bin/cli todo:next` asks what has a clock, then the queue, then what recurs
every session. That sighting hands over one feedback.**

The sightings come only with the queue empty, and `bin/cli feedback:next` hands
over the oldest one no todo has judged.

This is what survived `D-FBK-005` and what replaced the rest of it. The order is
that entry's, untouched, and the evidence below is the evidence that bought it.
The portion is not. Five was the cut for a reader who then had to look for the
judgements in a commit. What replaces it is where a judgement goes rather than
how many happen at once.

## Evidence

- 56 open feedback against 38 queued items on 2026-08-01, 55 of them named by no
  todo. `bin/cli feedback:list` exited nonzero on the first of those 55, so the
  sighting was due in every session. It printed 57 lines of filenames before its
  own paragraph, and `next` had reached no queued item since the queue's first
  day. Two of the three recurrent todos are sightings. Measured and recorded in
  [`D-FBK-005`](fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md),
  which is where it is still read.
- The second **Wrong if** of that entry, on 2026-08-02. The five came round and
  nobody read the judgements, because the only place they stood was the commit
  that made them. That is the one place nothing searches. The portion served a
  reader who could not have existed.

## Decided

- Three groups in a fixed order. A cadence in days is an appointment and keeps
  its place at the front, because a miss is a lost day. The queue is next,
  because a judgement of a feedback is what puts an item into it. A queue with
  entries is a queue of decisions already taken, and a sighting of more instead
  is two decisions and no work. The sightings come last, when the queue is empty
  and their whole output is the need.
- One feedback rather than a handful. `bin/cli feedback:next` hands over the
  oldest one no todo has judged and exits nonzero while any remain. So a run is
  one judgement and the loop ends when nothing awaits one rather than after a
  fixed number. It prints the category, the model and the first line, because
  nobody can disagree with a filename alone.
- The judgement goes into `decisions/`. The entry it stands against gets the
  update, and where the judgement establishes something no entry says yet, a new
  one arrives. So the judgement survives the run instead of stays a window onto
  five files. There is no journal beside the archive: a second list of the same
  judgements is a second thing to keep true.
- `bin/cli feedback:list` stays the whole of it, newest first, and reads rather
  than works.

## Assumed

- That the queue empties. Carried over from `D-FBK-005` with no measure, and it
  is the mirror image of what the order corrects. A queue that never runs dry
  starves the feedback exactly as the feedback starved the queue.
- That oldest-first is the right end to take one from. A feedback that waited
  while fresher ones arrived in front of it is the one at risk of no read at
  all. The newest is the one about the server as it is now, which is what a
  second run of the query is for.

## Wrong if

- `feedback/` grows while the queue never empties, so the one never goes out at
  all. Then the sighting needs a place of its own rather than a position behind
  the queue. Most likely a cadence in days like every other appointment.
- The judgements no longer arrive in `decisions/`. A run that judges a feedback
  and writes nothing anywhere is the failure five stood against, reached by the
  other road. Nothing fails on an entry nobody wrote. The only trace is a
  feedback a todo marks as judged while it says less than the judgement did.
- What stands between feedback goes unsaid. One feedback that corrects three
  earlier ones, or the same gap from four sessions, is what a portion of several
  could see and one cannot. `decisions/` is where that has to become visible
  now, and a directory that only ever restates single feedback would mean it has
  not.

## Since then

The second half of the statement is gone with the command it names. Every open
feedback got a card in the queue instead, and what writes that card became
`typo3_feedback_record` itself. So a feedback joins the queue when it arrives
and gets its judgement in the order the queue has. The first **Wrong if** got
its answer as a moot point.

The first half stands and is what this entry still serves. `todo:next` asks the
three groups in that order, and the sightings come with the queue empty.
