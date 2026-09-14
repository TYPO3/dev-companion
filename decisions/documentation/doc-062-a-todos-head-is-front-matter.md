---
id: D-DOC-062
title: "A todo's head is front matter"
date: 2026-08-27
status: open
restsOn: [D-DOC-045, D-DOC-061, D-FBK-002]
coveredBy:
  - FeedbackTest::aRecordedFeedbackArrivesWithTheCardThatAsksForItsJudgement
  - TodoTest::everyTodoSaysWhatItIsBeforeItSaysAnythingElse
---

# D-DOC-062 — A todo's head is front matter

**A todo declares what it serves, where it stands and what it waits on in front
matter. The `Entry` that reads a requirement and a decision reads it.**

`D-FBK-002` put the head in the bold-label shape because that was the shape
`requirements/` and `decisions/` had. Those two moved to front matter with
`D-DOC-045` and the todos stayed. So the head was the last data in this
repository that came out of prose. The next concrete step is not part of the
move: it stays the paragraph under the title, which is what a session starts
from.

## Evidence

- What the labelled shape cost `Todo` was 104 lines. A pattern per line, and a
  rule that puts an indented line back onto the field above it. A rule that
  tells a blank line inside a head from the one that ends it. And a rule that
  tells a head from the first paragraph of a `reference/` file that has none.
  Reading the same six values out of front matter is 34.
- The labelled reader was the second one of the same idea, and it had a bug of
  its own. It appended through an offset and lost the pair, which `D-COD-005`
  records as one of the two behaviour fixes level 7 found.
- 40 files carried the head, 26 in the queue, 8 that wait, 5 recurrent. The one
  in `reference/` carried none, which the special case existed for. A file with
  no front matter now has no fields and needs no case.
- A question is what the labels could not carry. The eight todos that wait wrap
  theirs over several lines and two carry a second paragraph, which the reader
  joined with spaces. Folded YAML keeps the paragraph and needs no quotes for
  the colon two of them contain.

## Decided

- Six keys, and `Todo::FIELDS` is the list: `serves`, `priority`, `every`,
  `checked`, `run`, `waitingOn`. `bin/cli todo:check` reports a key that is none
  of them. So a misspelt `waiting_on` is a question the check raises rather than
  one nobody notices.
- No `id:` and no `title:`. A todo's id is its file name (`D-DOC-061`) and every
  listing prints its heading. So either would be a copy that nothing reads, what
  `D-DOC-045` names as the thing to refuse. An entry carries both because the
  generated group listing reads front matter. Its file name is a slug of the
  title rather than the id alone.
- `waitingOn` is a folded block, `>`. It takes a question of any length, and a
  plain scalar with a colon and a space in it is a mapping.
- `serves` and `run` are flow lists, so an ordinary todo's head is two lines and
  a `serves` of two ids stays one.
- The step stays the paragraph under the title. `D-FBK-002` rejected a field for
  it, and that is still what a session reads first and starts from.
- `Todo::park()` moves the file instead of rewrites it from what it read.
  Nothing in a move changes, and a fresh emission of the front matter would
  rewrite somebody's question as whatever a dumper makes of it.
- What writes `checked` back is the session that ran the todo, as before.
  `bin/cli todo:next` hands the recurrent todo over with that sentence, and no
  command edits a todo.

## Assumed

- That a session which writes a todo by hand writes the front matter correctly.
  It already writes one for every requirement and decision it adds, and
  `Card::write()` writes the card a feedback arrives with rather than a session.
- That nothing outside this checkout reads a todo. `todo/` is not on the site,
  no tool answers from it, and `bin/cli` and people read the queue.

## Wrong if

- Front matter that will not parse reads as front matter that is empty.
  `Entry::matter()` answers `[]` on a parse error, so a file nobody can read
  shows as a todo that serves nothing. What would show it is a session that
  repairs a `serves:` line that was never the problem.
- The keys grow into a second document. Six is what a command or a check reads,
  and a seventh that only a person reads belongs in the step.
- Nobody reads a long `waitingOn` any more. The question now stands above the
  title rather than under it, and `bin/cli prose:format` leaves front matter
  alone. `bin/cli todo:waiting` prints it whole, which is the read that matters.
