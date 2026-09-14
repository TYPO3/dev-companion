---
id: D-FBK-009
title: A todo nobody can start waits where it says why
date: 2026-08-01
status: open
---

# D-FBK-009 — A todo nobody can start waits where it says why

**A todo blocked on an answer this repository cannot produce leaves the queue
for `todo/waiting/` and carries the question it waits on.**

Until now it went to the end of the queue. `D-FBK-007` decided that, and for a
todo whose question merely awaits its ask it is still right. Last is also a
timer, and the todo comes round again as the queue drains. What it gets wrong is
the todo that no answer here can unblock. That one then sits at the bottom of
the order and looks like the least important thing in the repository.

## Evidence

- `Wait for the producer D-KNW-004 needs`, at the end of a queue of 34 on this
  entry's day. Its own paragraph said it waits on something outside this
  repository entirely, and its position said it is lower priority than 33 pieces
  of work. Two different claims, one of them from the file system. Every session
  that reached it would have read the same paragraph again to learn it cannot
  start.

## Decided

- A fourth place beside the queue, what recurs and what stays for reference. A
  todo there has no number, because it has no place in an order. It carries
  `**Waiting on:**`, the question in the words it went out in, and
  `bin/cli todo:check` holds it. The answer is what numbers it back into the
  queue.
- That the way back is a recurrent todo rather than a habit. Every seven days
  `bin/cli todo:waiting` prints the blocked todos with their question and exits
  nonzero. That is what makes that todo due and puts the questions in front of a
  session before the queue. Without it the state is a directory nobody has a
  reason to open. The end of the queue, for all it says wrong, at least came
  round again.
- That `bin/cli todo:next` names how many wait and nothing more, as one field of
  the line that already says how many are behind this todo. A blocked todo
  speaks to whoever can answer it, and a state nothing ever mentions is a file
  nobody opens again. The paragraph stays the one todo's (`D-FBK-003`);
  `bin/cli todo:list` is where the questions are readable.

## Assumed

- That a session can tell the two cases apart at the commit. A question that
  merely awaits its ask goes last, one that nobody here can answer waits.
  Nothing checks it, and the cheap mistake is the wrong one. A todo parked in
  `waiting/` is out of every session's way, which is exactly what a session that
  does not want to work it would choose.

## Wrong if

- ~~`waiting/` grows past two or three, which would mean it has become where
  todos go to rot rather than where questions live.~~ Fired on the number on
  2026-08-22, at six, and not on what the number stands for; see the visit
  below.
- A todo sits there with its question answered in the conversation and nobody
  moved it back. That would mean the seven-day todo runs and nobody acts on it.
- The count in `next` turns out to read as work and sessions start to ask about
  it; then it belongs only in `todo list`.

## Since then

The first **Wrong if** fired on the number and stands struck. `waiting/` holds
six where this entry named two or three as the point at which the directory has
become where todos rot. It has not.

Two of the six went to the maintainer and the answer was *wait*, so each carries
a different question from the one it arrived with. This entry did not anticipate
that state, and the seven-day visit is what asked both. One waits on nothing of
its own and says so. What it needs is the skill that blocks its sibling, which
is the shape closest to rot, and it is one todo of six. So the number is a proxy
for four questions rather than six todos.
