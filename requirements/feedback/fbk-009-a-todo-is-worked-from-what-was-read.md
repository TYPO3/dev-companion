---
id: R-FBK-009
title: 'A todo is worked from what was read'
status: held
heldBy:
  - TodoTest::everyTodoIsHandedWithThePageThatSaysHowOneIsWorked
---

# R-FBK-009 — A todo is worked from what was read

**One page says how a session works a todo, and every todo the command hands
over names it.**

That page carries what the session reads before the first change. It says that
the session judges the step rather than executes it. It says that the session
settles a question the work turns on from a source rather than recalls it. And
it says that the session asks what has no source here before the change instead
of a quiet decision.

A todo prints as an instruction, and to act on one takes no read at all. What
that skips is the half that decides whether the change is right. The todo's
paragraph is a claim written by a session that has ended, and the code it
describes has moved since. The checkouts, the official documentation or this
server's own tools answer the question the step turns on. Or nothing here
answers it, which is a result with three places to record it in.

The questions that stay open after all of that are the ones nothing in the
repository can close. Which of two shapes the maintainer wants, what an
ambiguous paragraph meant, whether a step is worth its cost. The session asks
those before the change rather than in the commit that presents it, with the
read done and the options named. A silent choice is not a question saved; it is
a question moved to where the answer costs a rewrite. To put the todo back is
one of the answers on offer, because the person asked may have none either. A
todo that carries its open question into the next session is worth more than one
closed on a guess. It goes to the end of the queue when it does.
`bin/cli todo:next` hands over the first queued item and has no notion of
blocked, so one left in place goes to every session behind it.

Nothing can check that the read happened. A todo worked from the checkouts has
exactly the shape of one worked from memory, and what this repository produces
goes into use unchecked. That is a statement in `knowledge/`, a skill in
somebody else's project, a requirement that holds the next author. So the demand
is that the procedure exists and travels with the todo rather than waits for a
lookup. `bin/cli todo:next` prints the pointer with every todo it hands over.

## From

2026-08-01. The order of the work had a full description: what is due, in which
order, what a finished todo leaves behind. How a session works one had a
description nowhere, which left the read and the research to whatever the
session brought with it.

## Held by

- `TodoTest::everyTodoIsHandedWithThePageThatSaysHowOneIsWorked`, which holds
  `Todo::PROCEDURE`, the page it names and the line `bin/cli todo:next` prints
  to each other. That a session read it is not guarded and cannot be.
