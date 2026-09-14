---
id: D-DOC-036
title: A todo serves a decision by its id
date: 2026-08-18
status: open
coveredBy:
  - TodoTest::whatATodoServesIsCheckedAgainstThePlaceThatOwnsIt
---

# D-DOC-036 — A todo serves a decision by its id

**A `Serves:` line may name one decision by its id, and `decisions/` stays what
a todo that sorts the pile names.**

## Evidence

- The two lists never agreed. `Todo::unreadable()` took a requirement, a
  scenario, a feedback and a directory from its first day, 2026-07-31.
  `documentation/records/working-a-todo.rst` arrived the next day and named a
  requirement, a decision, a feedback and a directory. Neither list moved
  afterwards. A `**Serves:** D-DOC-035` written on 2026-08-18 failed
  `bin/cli todo:check` while the page said it was one of the four.
- `bin/cli unresolved:list` reports the open decisions as a count and asks only
  whether some todo serves `decisions/`. An entry whose **Wrong if** somebody
  goes back to therefore sat in the queue under the same word as the whole pile.
- [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  weighed a move of the todos derived from a feedback onto the decision entry
  instead. It refused that for what `closed` would then mean to an agent, not
  because a todo cannot name a decision.

## Decided

- `Todo::unreadable()` reads the `D-XXX-000` shape against `Decisions::all()`,
  beside the requirement branch it already had.
- Both lists name the same five, and `todo/readme.md` says which of the two
  forms a decision takes. The id where the step is a return to that entry's
  **Wrong if**, `decisions/` where it is the pile under sort.
- Against a correction of the page down to four. The sentence under the list
  describes a visit that has no other field to name its subject in. It says a
  todo that serves a decision is usually that entry's **Wrong if** compressed
  into a sentence.

## Assumed

- That the recommendation the todo carried is what the maintainer wanted. The
  question is one nothing here answers, and nobody asked it. The todo sat in the
  queue with both options and a recommendation and then went out as work. That
  reads as the answer rather than as a blank.
- That an id on a `Serves:` line means the entry itself and not the pile. So
  nothing counts such a todo as the sort `unresolved:list` asks about.

## Wrong if

- No todo ever names one. Then the branch is a shape nobody writes, and the page
  rather than the code was the thing that should have moved.
- `bin/cli unresolved:list` comes to want which open decisions sit in the queue.
  It asks about `decisions/` alone. So an id it cannot see would make an entry
  somebody works on look like one nobody has been back to.
