---
id: R-FBK-010
title: 'Work somebody has in hand is offered to nobody else'
status: held
heldBy:
  - TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt
  - TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim
  - TodoTest::whatIsInHandIsTheTodoAWorktreeStandsOn
---

# R-FBK-010 — Work somebody has in hand is offered to nobody else

**The command that hands out work does not hand out a todo a session has taken
on.**

The queue is an order, not an assignment. `bin/cli todo:next` reads the same
first item for everybody who asks, which is exactly right while one session
works at a time. It is wrong the moment two do. Both get the same todo, and the
second finds out when it writes a change the first has already written.

So to take one on is to cut a worktree on the branch the todo derives. The
command that hands out work passes over every todo one stands on (`D-DOC-060`).
The todo does not move. It was a move into `todo/progress/` until 2026-08-27.
That said a third time what the branch and the worktree already said, and said
it in the one copy that outlives them.

What it took on stays taken on. A requirement whose todo dropped off the list of
what the queue answers for would go back onto `bin/cli unresolved:list` while
somebody works on it. The next session would queue it a second time, the same
trap `waiting/` already stayed out of.

The claim carries what tells it from one nobody came back to. That is the branch
the work is on, so somebody can find a half-finished diff. It is the day the
session took it, so a state that locks everybody else out reads as stale.

`bin/cli todo:next` stays where every session starts, worktree or not. A session
that gets its file name from whoever set it up would be the one session here
that begins differently. The failure that invites is silent. With the front of
the queue instead of its own claim, it reads a real todo and starts real work.
It is the second person on it.

## From

2026-08-01. Two sessions at work at once had no way to get different work, and
the whole of `todo/` stood on one session at a time.

## Held by

- `TodoTest::whatIsInHandIsTheTodoAWorktreeStandsOn` for what says somebody has
  a todo in hand.
- `TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim` for what
  `bin/cli todo:next` answers in a worktree.
- `TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt` for the way
  out that is not a deletion.
