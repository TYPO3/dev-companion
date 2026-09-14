---
id: D-DOC-044
title: A failing test names the decisions it was holding
date: 2026-08-23
status: open
restsOn: [D-DOC-043]
coveredBy:
  - DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd
---

# D-DOC-044 — A failing test names the decisions it was holding

**A test that fails prints the entries whose generated list names it, with the
path to each.**

The session that made it red goes to the decision rather than makes the test
green again.

`D-DOC-043` coupled the two directions and stopped there: an entry names its
test, and since 2026-08-22 the test names the entry back. Both namings live in a
file somebody has to open. A failure prints neither.

## Evidence

- Read on 2026-08-23 over the readings of the two days before it. 44 entries
  carry a **Since then** or a **Confirmed on** dated 2026-08-22 or 2026-08-23,
  and 22 of them name no test at all. A count holds the count; it holds nothing
  about the entry.
- The word that promises most holds least. 22 of the 79 `confirmed` entries
  point at this repository's code and name no test. `confirmed` is the status
  that reads as "somebody checked", where `open` at least says nobody has been
  back.
- The guard mostly exists and sits in the wrong place. Of those 22, 18 name a
  test somewhere in their prose and only in prose. `D-COD-001` names
  `StructureTest::everyFileDeclaresOneClass` in **Decided**, `D-COD-003` its
  finder test in a dated section, `D-DOC-001` two `ProseTest` methods from the
  same day. `Decisions::uncovered()` reads **Covered by** and sees none of them.
- What a failure prints today is the assertion message and the file and line of
  the assertion. Nothing in it says which entry rested on the behaviour that
  moved.

## Decided

- A PHPUnit extension, `Tests\Support\HeldEntries`, registered in
  `phpunit.xml.dist`. It collects the failed and errored tests, and at
  `ExecutionFinished` prints each entry that names one, with its id, its title
  and its path. A run where nothing fails prints nothing.
- `Decisions::restingOn()` is the lookup, and it is the other direction of the
  attribute: which entries a test held rather than what one test declares. A
  list that names a whole class holds every method in it, which the format
  already allows.
- The extension is thin and a test holds the lookup.
  `DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd` asserts it over
  the whole corpus: every entry that names a test is reachable from that test. A
  mapping that misses an entry is a decision that quietly loses its pointer.
- No test changes for it. The bond it prints is the one the entry's list already
  declares. So a decision joins the mechanism through its name there and through
  nothing else.

## Assumed

- That the session which reads a failure reads to the end of the run. The report
  prints once, after the failures and before the summary, rather than beside
  each one. A failure list of thirty would otherwise carry the same entry thirty
  times.
- That the entry's name is enough to send somebody to it. What the line carries
  is the id, the title and the path. Whether a reader takes that as an
  instruction is not something this can hold.

## Wrong if

- A session reports that it made a test green while the entry it named said the
  decision went the other way. Then the pointer arrived and nobody read it, and
  the gap is in the entry rather than in the run.
- The report grows long enough to scroll past. It is one line pair per entry per
  failed test. A suite where fifty tests fail is a run nobody reads that far
  into anyway.
- `Covered by` starts to collect names chosen to make this print something. That
  is `D-DOC-043`'s second **Wrong if** with a new incentive behind it. A count
  that falls while entries still go stale is what would show it.

## Since then

It prints requirements as well, since 2026-08-23. `#[Requirement]` put a
requirement's tests in its front matter the way `#[Decision]` did a decision's,
`D-DOC-049`. From that moment the two corpora were one lookup away from each
other, so the extension is `Tests\Support\HeldEntries` and names both. A session
that made a test red goes to every entry that rested on it, whichever corpus
wrote it down.
