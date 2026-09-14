---
id: D-FBK-003
title: A session is handed one todo, not the file
date: 2026-07-31
status: revoked
revokedBy: D-FBK-002
---

# D-FBK-003 — A session is handed one todo, not the file

**`bin/cli todo:next` prints the first todo that is due and nothing else.**

The overview it used to print is `bin/cli todo:list`, and a cadence and a
nonzero exit of the todo's own command decide what is due.

The command existed to spare a session four files, and it replaced them with one
text of its own that was nearly as long. What it costs to hand an agent context
it did not ask for is not zero. The file it read from had grown to match. Items
carried three moves each, with the move behind the paragraph that explains why
the order is what it is.

## Evidence

- The output on the day of the change: 62 lines, of which the one item at the
  front was 396 words. Two of that item's three paragraphs were history (why it
  moved ahead on 2026-07-31, what the second run of that day produced). The
  first imperative sentence was in the third. It also said "name it as `E-EXT`
  **above**", a pointer at a section marked `**Not an item.**` that `next` does
  not print. So the instruction resolved to nothing for its only intended
  reader. Of the three permanent sections, two ran and one printed a command to
  run, and nothing in the output told them apart. The listings both exited 0
  whatever they found. What replaced it is 19 lines and 139 words.

## Decided

- One todo, whole, with its `Run:` command already executed. Due is the cadence:
  `session`, or a number of days with a `Checked:` date. So five sessions in an
  afternoon do not ask five times whether the SDK has released. Then the
  command's exit code. It lets the feedback cease to be the next thing the
  moment the last one has its judgement, with no edit to the file. With that, a
  todo that recurs is a todo with a cadence rather than a kind of section. So
  `**Standing:** feedback | backlog | by hand` became fields any todo can carry
  and the three special cases in the dispatcher went with it. One paragraph is
  one step, and the six items that carried more became ten.

## Assumed

- That the judgement, not the work, is the right threshold for "there is
  something here". A feedback some todo already names is under work in the order
  the queue has it. A stop to re-read it is how the queue never gets reached.
  Nothing has run long enough to show that the ones already named stay named.
- That a session which never sees the queue does not need it to work in the
  right order. The order is the file's, and the file decides which todo prints.
  What this gives up is the reader who would have noticed that the order is
  wrong. `bin/cli todo:list` is where that reader has to go on purpose now.

## Wrong if

- Sessions start to ask for context `next` withheld, or open with `todo list`
  anyway. Then one todo is less than the minimum and the cut was in the wrong
  place. Or a recurrent todo blocks the queue for more than a session or two.
  That would mean its command answers "there is work" to a state nobody can
  finish. Or the todos grow back into packages, because the split is a habit and
  nothing checks it. The paragraph is prose by design, and no check can tell one
  step from three.

## Since then

The first **Wrong if** had its measure on three parallel sessions and did not
happen. Each opened with the check it had to run, called `todo:next` exactly
once, and across 146 shell calls never ran `todo:list`. None asked for context
beyond the todo and none recorded a note in `waiting/`. Read from the
transcripts rather than from what the sessions reported about themselves.

Two things it does not settle. The message that started all three steered them
to the command, and a worktree session is not the plain case this entry serves.

## Revoked on 2026-08-01

The second **Wrong if** happened, in the form the first **Assumed** described. A
recurrent todo blocked the queue, not for a session or two but for every session
there was. The feedback it sighted were what nobody could finish. 56 open, 55 of
them named by no todo, against a queue of 38 items that `next` never reached.
The threshold was right and the order was wrong. A judgement of a feedback is
what puts an item *into* the queue, so the sighting first means two decisions
and no work. The directory it decides over grows from every session everywhere
while one session judges a handful. `next` now asks in three groups: what has a
clock, then the queue, then the sightings once the queue is empty. The sighting
hands over five rather than the directory, because five judgements are the
number somebody can disagree with before the commit.
