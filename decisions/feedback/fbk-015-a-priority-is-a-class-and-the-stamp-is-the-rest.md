---
id: D-FBK-015
title: A priority is a class, and the stamp is the rest
date: 2026-08-02
status: open
coveredBy:
  - TodoTest::theQueueIsReadByPriorityAndThenByAge
---

# D-FBK-015 — A priority is a class, and the stamp is the rest

**A queued todo carries one of three words, the order below that is the date in
its name, and no move renames anything.**

The number in the name was a rank, which is a thing only one session at a time
can hand out. Three words and an arrival time say what it said, and two sessions
that queue work at once cannot pick the same one.

## Evidence

- `bin/cli todo:check` carried a collision report for two files with one number.
  [`working-todos-in-parallel.md`](../../documentation/records/working-todos-in-parallel.rst)
  named a renumber as the fix in the paragraph about the way branches come home.
  Both existed because both sessions read the same last number and both took it.
  A rank has to be unique, and nothing could make it so across two branches.
- The tens.
  [`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md)
  chose them so a todo could go between two others. It wrote as its **Wrong if**
  that a commit which renames more than a handful of files to move one would
  show them too tight. Nothing ever reordered the queue under that shape, so the
  mechanism was never used and its cost was paid on every insert.
- The page of what is deliberately not queued, on 2026-08-02. Its four catalog
  items say of themselves that nothing blocks them and that they serve no open
  feedback. That is "why it is below everything that does". That is a priority,
  as prose in a file outside the queue because the queue had no way to hold it.

## Decided

- Three words, `high`, `normal` and `low`, declared in `Todo::PRIORITIES` and
  nowhere else. A class rather than a rank: two todos may share one, and there
  is no between to put a third in.
- **Absence is the fourth thing it can say.** A todo with no `**Priority:**` is
  one nobody has judged, and it sorts below `low`. That is what a sighting used
  to do with what had only just arrived. It is what lets a card exist for a
  feedback before anybody has decided what the feedback is worth. So nothing
  needs a default, and a default would have hidden the state.
- The stamp in the name is the order below the word, in the shape a feedback is
  already named in. `read()` sorts by name and PHP's sort holds equals in place,
  so the second half of the order costs nothing.
- A claim keeps its name. The number was a place in one order and had to go
  where that order did not reach. The stamp is when the work arrived, which is
  as true in hand as in the queue. So a claim and a release are moves and
  nothing else.
- **`release()` no longer re-ranks.** A released todo used to get a new number
  at the end. The queue was one order and there was no other way to say "later".
  The two things that meant are now separate and a person says them. Where
  nobody can work it, `waiting/`; where somebody can and it should not be next,
  a lower priority. That is a line somebody wrote and somebody can disagree
  with, rather than a place a command moved it to.

## Assumed

- That three is enough. Two would not separate "next" from "not now", and five
  invites a rank by another name. But nothing has yet needed a fourth, because
  nothing has yet had more than a handful at one priority.
- That the order people want is a priority. The one case to hand is this. A tidy
  store before 67 cards land in it is a preference and not a constraint, either
  order works, so `high` says it exactly. What no number of priorities could say
  is "not before that one", and nothing has needed it yet. A todo that truly
  cannot start is `waiting/`, and two steps that must run in order are usually
  one todo.
- That a stamp keeps meaning arrival. Nothing rewrites one today, and the two
  moves that could — a claim and a release — deliberately do not.

## Wrong if

- Everything queued ends up `high`, or nothing carries a priority at all. The
  first is a rank on its way back through the vocabulary. The second means the
  field is ceremony and the order is really the age. `bin/cli todo:list` prints
  the word on every line, so either is visible from one command.
- A dependency appears as a priority often enough that the word no longer means
  importance. The assumption above says this has already happened once.
- Two todos at one priority and one age turn out to need a split. That would
  mean the stamp is too coarse or the order was a rank after all.

## Since then

The fourth thing a priority could say is gone, on this entry's day. A card for a
feedback starts at `low`, which is where absence put it, said in a word.

What it bought was the check. While absence meant *nobody has judged this*, a
priority somebody forgot and one left off on purpose were the same file. An
optional field cannot be a requirement, so the check had to stay silent about
the one case it most wanted to report. The first **Wrong if** is now the whole
of the watch.

## Since then

The stamp is an id — `T-<yymmdd>-<hash>` — and the day in it is what this entry
called the arrival time. `D-DOC-061` made that change for the reason this one
rejected the number. An id read off what already exists is one two sessions both
take. That is what happened to `D-ANS-114` on two branches cut from one `main`.
The day still orders the queue below the three words, and no move renames
anything.
