---
id: D-FBK-007
title: How a todo is worked travels with the todo
date: 2026-08-01
status: open
coveredBy:
  - TodoTest::everyTodoIsHandedWithThePageThatSaysHowOneIsWorked
---

# D-FBK-007 — How a todo is worked travels with the todo

**The reads and the research a todo needs stand as a procedure page.**

`bin/cli todo:next` prints the pointer to it with every todo it hands over
rather than leaves it to a lookup.

Everything about the order of the work stood written and nothing about the work
on one piece of it did. A session that gets one todo, printed as an imperative
paragraph, has every incentive to start at the first sentence of the step. The
two things that decide whether the change is right happen before it. A read of
what the todo serves against what the code does today. And a question settled
from the checkouts, the manuals or this server's own tools instead of from
recall.

## Evidence

- The queue on this entry's day: 35 sections, nearly all of them for a decision.
  Most are in the shape "the entry names the failure; read whether it happened".
  Every one of those is a research task whose output is either a **Tested on**
  line or a test, and none of them says so. The author page for skills had just
  gained the same two steps (`R-SKL-006`, 2026-08-01) after a skill came from
  recall. That is the same failure one directory over. What `next` printed was
  the todo, the run output and one line about its removal afterwards.

## Decided

- One page,
  [documentation/records/working-a-todo.rst](../../documentation/records/working-a-todo.rst).
  It covers what a session reads first, and that the step gets a judgement
  rather than an execution. It covers that a question the work turns on settles
  from a source or stands recorded as open. It covers that what has no source
  here is a question before the change, and what the file has to say afterwards.
  Plus one last block on `bin/cli todo:next` that names it. `Todo::PROCEDURE`
  holds the path so the pointer and the page cannot drift apart, and `R-FBK-009`
  carries the demand. The handover half stayed in the command rather than moved
  to the page with everything else. Which of the three cases applies comes off
  the todo, and the page cannot know which one its reader has.
- That the offer to put the todo back comes with the question rather than stays
  as a fallback. And that a todo put back goes to the end of the queue. The
  person asked can be out of answers too, and a session that has only "decide
  it" on the table will decide it. So the todo keeps its file, gains the
  question in the words it went out in and what the reads already established,
  and moves last. Last rather than down, because `next` hands over the first
  queued todo and has no notion of blocked. One left in place goes to every
  session after this one, which is a queue that does not move at all. Nothing
  new holds either half. It is the rule that already exists, a change of order
  stands written before the work, applied to a todo that never started.

## Assumed

- That a session reads a pointer handed over with the work where it does not
  read a page in `documentation/`. That is the whole bet: the page has existed
  for one commit and nobody has observed a session read it. The alternative, the
  reads themselves in the output, fell because `next` exists to print one todo
  and nothing else (`D-FBK-003`). A command that grows a second paragraph of
  instruction every time somebody forgets something ends as the 62-line output
  that decision cut.
- That the research a todo needs is worth a name by source at all. Most sessions
  here run without an installation and with the checkouts already present, so
  the sources are few and stable. If that no longer holds the list becomes a
  menu nobody reads to the end.
- That a question is cheap where it is right, which holds while the person who
  queues the todos is the one the session talks to. A session that runs
  unattended, a scheduled run, a forward review in somebody else's agent, has
  nobody to ask. For it the instruction degrades to a record of the open
  question instead, which is the same three places research that ran out already
  uses.

## Wrong if

- A commit lands with no source behind it. It states a version-bound fact, a
  default of a tool this repository does not own, or the current state of a
  feedback. The page is then present and inert, and what remains is the question
  in the todo itself rather than in a page it points at. Or sessions start to
  echo the last lines back as a plan ("first I will read what it serves") and
  open no file. That is the failure mode of every instruction handed to an agent
  and is readable in a transcript. Or the queue is no longer answerable this way
  at all. The research a todo needs turns out to need an installation more often
  than not. Then the page names sources that are not there.

## Since then

The second **Decided** is narrower now. A todo that no answer here can unblock
leaves the queue instead of goes last, and says what it waits on. Going last
stays what a merely unasked question earns. See
[`D-FBK-009`](fbk-009-a-todo-nobody-can-start-waits-where-it-says-why.md).
