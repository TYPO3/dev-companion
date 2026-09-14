---
id: D-FBK-002
title: The order of the work is declared, not inferred
date: 2026-07-31
status: confirmed
coveredBy:
  - TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead
---

# D-FBK-002 — The order of the work is declared, not inferred

**Every todo declares what it serves and how it recurs, and `bin/cli todo:next`
reads that rather than guesses at the prose around it.**

The file always held the order of the work, and only a person could see it.
Three kinds of section sit in it and nothing tells them apart from outside. The
permanent ones nobody deletes, the queue, and the sections that are neither.
Those are the environment table, and the list of what stays out of the queue on
purpose. A command that says what to do next has to tell them apart, and the
question was whether to declare it or derive it.

## Evidence

- The file on the day the line arrived. Eleven sections: three permanent, six
  items, two that are neither. Six of the six items already opened with "This
  serves X and Y" in prose. So the bond existed as a sentence and nothing read
  it. The one thing that did read the file, `Unresolved`, searched it for an id
  anywhere. The "not queued, and deliberately so" section answers that as loudly
  as an item does.

## Decided

- One line per section, in the bold-label shape `requirements/` and `decisions/`
  are already read by. Three alternatives fell with it. A kind inferred from
  position (the permanent ones come first) breaks the day somebody inserts a
  section, silently and in the direction that hides work. A queue in a
  structured file turns the next concrete step into a field. That paragraph,
  written for whoever has read nothing else, is the most valuable thing in the
  repository's upkeep. A tool of the server rather than a command of `bin/cli`
  fell for the reason `Cli` already gives. This server is about the TYPO3 core,
  not about the repository it lives in.

## Assumed

- That a session with the start as one command runs it, rather than reads the
  four files itself as before. Nothing in this repository can show that. A
  forward run happens in someone else's checkout, and a session here may not
  grade its own behaviour as evidence. What it rests on instead is that the
  command is strictly cheaper than what it replaces and returns what the four
  files would have.
- That the line stays current because it is also what makes an item readable. It
  arrives with the item, and it names what the commit that finishes the item
  will delete. `bin/cli todo:check` fails when it names a feedback that is
  already gone. A marker nobody would otherwise need would have been the kind
  that rots.

## Wrong if

- Sections start to arrive without the line and `bin/cli repository:check`
  becomes the thing that adds it after the fact. Then it is bureaucracy, and the
  kind has to come from the text after all. Or if the paragraph under a heading
  thins out while the `Serves:` line grows. That is the same file on its way to
  a fourth backlog by another route. What a session cannot start from is a list
  of ids.

## Since then

The one line became a head of several, and the three kinds became two. A todo
that recurs carries a cadence and a todo that does not is the queue. The file
then became a directory, where the kind is what the todo sits in and the head
carries the rest. The head is front matter since 2026-08-27. What **Decided**
chose was the shape `requirements/` and `decisions/` had, and the head moved
when they did, `D-DOC-062`. What this entry settled survived both, which is the
point of the declaration: each was an edit rather than a rewrite. See
[`D-FBK-003`](fbk-003-a-session-is-handed-one-todo-not-the-file.md) and
[`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md).

## Confirmed on 2026-08-02

Neither **Wrong if** has happened, measured against a queue that had grown from
six items to twenty-six. Every queued todo carries the line, so nothing had to
arrive after the fact. `bin/cli todo:check` reports a bad one rather than
repairs it. It did that on the day for a `Serves:` that named a file where a
directory belonged. The second was the one worth a measure. The shortest body in
the queue is 79 words, the median 95, and not one is under 40. The correlation
the entry feared runs the other way. The only two todos that name two ids have
the longest bodies of all, 142 and 188 words. A step that answers for two things
needs more words, not fewer. What holds the mechanical half was already there.
`TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead` asserts a body on
every todo, and over all four states rather than the queue alone.
