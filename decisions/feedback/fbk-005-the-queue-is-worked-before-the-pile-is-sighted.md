---
id: D-FBK-005
title: The queue is worked before the pile is sighted
date: 2026-08-01
status: revoked
revokedBy: D-FBK-012
---

# D-FBK-005 — The queue is worked before the pile is sighted

**`bin/cli todo:next` asks what has a clock, then the queue, and only with the
queue empty what recurs every session.**

That sighting hands over five feedback at a time rather than the directory.

What recurs every session is the sighting: a read of `feedback/` and the backlog
and a decision what of it becomes work. It came first, on the argument that a
session should know what arrived before it starts. That holds while the pile is
small, and `feedback/` is the one directory in this repository that fills from
outside it.

## Evidence

- 56 open feedback on the day of the change, 55 named by no todo, against 38
  items in the queue. `bin/cli feedback:list` exited nonzero on the first of
  those 55, so the sighting was due in every session. It printed 57 lines of
  filenames before its own paragraph, and `next` had reached no queued item
  since the queue's first day. Two of the three recurrent todos are sightings;
  20 of the 56 were duplicates of two sessions.

## Decided

- Three groups in a fixed order. A cadence in days is an appointment and keeps
  its place at the front; a miss is a lost day. The queue is next, because a
  judgement of a feedback is what puts an item into it. A queue with entries is
  a queue of decisions already taken, and a sighting of more instead is two
  decisions and no work. The sightings come last, when the queue is empty and
  their whole output, new entries, is the need. The portion is five, so that
  somebody who is not the session that made the judgements can read them and
  disagree. The listing names each feedback's category, model and first line for
  the same reason. `bin/cli feedback:list` stays the whole of it, and reads
  rather than works.

## Assumed

- That the queue empties. It is the mirror image of what this corrects, and
  nothing has run long enough to show it. A queue that never runs dry starves
  the feedback exactly as the feedback starved the queue. The directory grows
  from every session everywhere either way.
- That oldest-first is the right end to take five from. A feedback that has
  waited while fresher ones arrived in front of it is the one at risk of no read
  at all. But the newest feedback is the one about the server as it is now.
  Evidence about a version that no longer exists is what a second run of the
  query is for.

## Wrong if

- `feedback/` grows while the queue never empties, so the five never go out at
  all. Then the sighting needs a place of its own rather than a position behind
  the queue, and the answer is probably a cadence in days like every other
  appointment. Or the five come round but nobody reads the judgements. Then the
  portion served a reader who does not exist and the number can be whatever a
  session can carry.

## Revoked on 2026-08-02

The portion is one, not five. The order stays and is what the evidence bought;
what was wrong is the number beside it. Five was the cut so a reader could
disagree with the judgements together. That reader had to find them in a commit,
which is the one place a judgement is not searchable. What replaces the portion
is where the judgement goes: into the entry it stands against, or a new one.
What it gives up is the run that sees two feedback at once, and `decisions/` is
where that has to become visible now.
