---
id: D-DOC-045
title: 'What a listing reads is front matter'
date: 2026-08-23
status: open
restsOn: [D-DOC-004, D-DOC-044]
coveredBy:
  - DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd
---

# D-DOC-045 — What a listing reads is front matter

**The title and the tests that hold an entry are front matter, and the heading
repeats the title for a reader of the file.**

Both came out of prose: the title from the `#` heading, the tests from a
`## Covered by` section read with a regex. What serves as data belongs where the
file already keeps data.

## Evidence

- Three readings parse the same section. `Decisions::uncovered()`,
  `unnamedByItsTests()` and `restingOn()` each matched
  `/^## Covered by$(.*?)(?=^## |\z)/ms` and then pulled the names out with a
  second pattern. The third arrived on 2026-08-23, which is when the same parser
  a third time no longer looked like a coincidence.
- The section is a bare list on 234 of the 243 entries that carry one. Of the
  nine that are not, five say that nothing runs over the entry and why. Three
  name a test and add a clause about what it holds, and one is empty.
- The front matter already carries what a listing reads: `id`, `date`, `status`,
  `revokedBy`, `restsOn`, and `judged` since `D-DOC-038`. Every one of those
  went there for the same reason.
- Two titles could not stand as bare YAML,
  `"Does it still work" is a question for the manual` and `` `main` carries the
  state ``. The parser answered nothing for them. Five in all needed quotes,
  which a dumper adds and a hand-written line does not.

## Decided

- `title:` and `coveredBy:` in the front matter of a decision, `title:` in a
  requirement's. The heading keeps the title as well, because a markdown file on
  GitHub or in an editor needs one. `bin/cli decisions:check` and
  `bin/cli requirements:check` fail where the two disagree.
- `symfony/yaml` parses the front matter rather than a reader a line at a time.
  It carries a list now, and a hand-rolled reader for one is a third parser over
  a format somebody else already implements. The reader asks for a date as a
  date, so `2026-08-23` does not come back as the Unix timestamp the parser
  answers by default.
- `coveredBy: []` where nothing holds the entry, with the reason as a bullet of
  **Decided**. An empty list somebody wrote and a list nobody wrote look
  identical otherwise. The five entries that explained the absence are the ones
  worth a legible form.
- A requirement's `## Held by` stays a section. 48 of its 222 entries carry a
  clause per test that says what that test holds, which is what `D-DOC-004`
  chose the shape for. A move of the names alone would leave the clause with
  nothing to point at. What that costs is the two corpora that say it in two
  places, which is the todo this leaves.
- The word is `coveredBy`, the section's own. `heldBy` is the requirement's,
  where it matches the `held` its status carries.

## Assumed

- That nothing outside this repository reads the sections. `bin/cli`, the tests
  and people read them; no published answer carries them.
- That 445 entries survived a scripted rewrite. The converter moved a section
  only where every line of it was a bare backticked name, and the ten it refused
  moved by hand.

## Wrong if

- A title in the front matter and a heading below it drift anyway, because
  somebody edits one and the check is not run. It fails the check and CI, so
  what would show it is a commit that never ran either.
- The front matter grows into a second document. It carries what a listing, a
  check or a failed test reads, and a field nothing reads is the thing to
  refuse.
- A decision needs a clause per test the way a requirement does. Then the list
  is the wrong shape for both and the requirement's section was right.

## Since then

`coveredBy` comes from a generator rather than a hand, since 2026-08-23: the
tests declare `#[Decision]` and `bin/cli decisions:cover` writes the front
matter from it, `D-DOC-048`. That is this entry's argument taken one step: what
serves as data stands in one place, and the entry carries the copy.

A requirement's **Held by** stays as it is, and the two corpora still say it in
two shapes. What the attribute makes cheap is a move of the second one, since
the clause per test would be what stays in the section.
