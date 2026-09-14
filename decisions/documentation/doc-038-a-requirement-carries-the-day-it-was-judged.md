---
id: D-DOC-038
title: 'A requirement carries the day it was judged'
date: 2026-08-22
status: open
coveredBy:
  - RequirementsTest::aJudgementIsTheDateItWasMadeOn
  - UnresolvedTest::aJudgedEntryCarriesTheDayItWasDecidedOn
---

# D-DOC-038 — A requirement carries the day it was judged

**A requirement nothing holds records the date a session read it and decided it
stays that way. That date sits in a `judged:` line of its own front matter.**

`bin/cli unresolved:list` could see one answer, and a requirement no test can
hold is the other. So the honest state and the unjudged one printed identically
and forever. The recurrent todo that reads the listing derived the same
judgement again in every session that ran it.

## Evidence

- The listing names three entries and has named the same three for weeks:
  `R-COD-003`, `R-FBK-012` and `R-SKL-009`. All three had their judgement
  already. `R-COD-003` carries it twice over in `D-COD-004`. Under **Decided**,
  "a test that reads the other tests… was written, it worked, and it was taken
  out". And under **Wrong if**, which names "the check that would prevent it is
  the one `R-COD-003` declines". `R-FBK-012` says in its **Held by** that both
  halves are prose pasted by hand into a session this repository cannot read.
  `R-SKL-009` records that its card went to the maintainer on 2026-08-04,
  2026-08-12 and 2026-08-19, and that the third answer deleted it.
- `Unresolved::requirements()` derived `queued` from `Todo::serves()` alone, and
  that reads `open/`, `progress/` and `waiting/`. Its docblock claimed "an entry
  no item names is one nobody has decided about either way", which was false of
  all three.
- The two are not the same answer.
  [`writing-a-requirement.rst`](../../documentation/records/writing-a-requirement.rst)
  has said since the directory's first day that **Held by** "lists the tests
  that hold it, one per line, or says in as many words that something is not
  guarded". A requirement whose **Held by** says that correctly could never
  leave the list, because the list had nowhere to see it.
- `UnresolvedList` exits nonzero while anything lacks a judgement, and
  `bin/cli todo:next` reads that as "still the next thing". So every session got
  the recurrent todo and nothing could stop that.

## Decided

- `judged: <date>` in the requirement's front matter, read by
  `Requirements::read()` and reported beside `queued`. The listing prints
  "judged on `<date>`" where a todo would print nothing.
- A date rather than a flag. The judgement is about the entry as it read that
  day, and a rewrite of the entry under it is possible. Nothing catches that,
  and the date is what lets a reader see that a judgement is older than what it
  judged. `bin/cli requirements:check` holds it to a date and to nothing else.
- The reason stays where the entry already keeps it — the sentence **Held by**
  owes, or the decision it rests on. Repeating it in the front matter would be
  the second copy that goes stale.
- Rejected: a `checked:` line on a decision, for the other half of the same
  list. `confirmed` already means somebody went back and it held, and
  `writing-a-decision.rst` already calls a return to one "a legitimate task with
  no feature behind it". The 344 open decisions needed the work queued, not a
  second word for the state they are in.
- Rejected: a failure on a `judged:` stamp that sits on a guarded requirement.
  It is harmless, and the branch would be one nobody takes.

## Assumed

- That three entries are the whole of what stood without a judgement, because
  the listing is the whole list and it named three. What that cannot say is that
  the next unguarded requirement will get a stamp rather than stay as it is.
- That a session which stamps an entry has read it. Nothing tells a judgement
  from a date somebody typed, which is the same thing
  [`working-a-todo.rst`](../../documentation/records/working-a-todo.rst) says of
  the visit itself under **What nothing holds**.

## Wrong if

- Somebody rewrites a requirement under its stamp and a reader takes the
  judgement as current. The date is what would show it, and nothing compares the
  two.
- The stamp becomes the cheap answer. An entry that should have had a todo gets
  a date instead, and the listing goes quiet on work somebody meant to queue.
- The listing exits 0 for long enough that nobody runs it. Both halves are now
  answerable, so a repository that owes nothing prints three judged lines and a
  count. A report nobody has a reason to read is where the last one went.
