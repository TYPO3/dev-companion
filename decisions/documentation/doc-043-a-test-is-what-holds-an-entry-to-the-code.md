---
id: D-DOC-043
title: 'A test is what holds an entry to the code'
date: 2026-08-22
status: open
restsOn: [D-DOC-041, D-DOC-042]
coveredBy:
  - DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOut
  - DecisionsTest::everyEntryATestHoldsIsNamedFromTheFailingEnd
---

# D-DOC-043 — A test is what holds an entry to the code

**`bin/cli decisions:check` names the entries that point at this repository's
code and name no test in `coveredBy`, and fails on none of them.**

A test named there fails when the behaviour moves, and
`DecisionsTest::everyTestADecisionNamesExists` fails when the test goes with it.
Nothing else couples the two.

## Evidence

- Read on 2026-08-22. Of 443 entries, 236 carry **Covered by**; 348 name a
  `Class::member` this repository declares, and 89 of those name no test.
- The three entries found stale that day carry none. `D-SCO-007` stood `open`
  while the call its statement describes changed twice. `D-ANS-045` recorded a
  closed list of thirteen directory names against a method that reads the
  directory, and `D-EVI-003` named a tool renamed weeks earlier.
- The two whose named code had moved under them and which were right both carry
  one. `TermSearchTest::aStemRunsPastItsOwnEndAndACuratedWordDoesNot` holds
  `D-ANS-050`'s claim, and three tests held `D-SKL-027`'s — of them
  `SkillTest::everyFrontMatterFieldIsOneTheStandardDefines` outlived the entry
  (`D-SKL-087`). `Text` and `Installer` both changed on 2026-08-18 and neither
  entry needed a reader.
- So the signal proposed first was the wrong one. "The statement names code that
  has changed since" would have reported exactly those two and none of the
  three.
- The other bond is weaker. `src/` names 156 decision ids and `tests/` 266, and
  neither names 155 entries. But an id in a comment fails nothing. No code names
  `D-SKL-027` and it is safe; no code names `D-FBK-004` and it is not.

## Decided

- A report beside the outgrown one in `bin/cli decisions:check`, most references
  first. `Decisions::uncovered()` is the report and
  `DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOut` holds it.
- Nothing fails. Most entries here decide something about process and no test
  could keep them, which the format says in as many words. A demand for
  **Covered by** would get a test name chosen to satisfy it, and that is worse
  than the silence it replaces.
- `Upkeep\Sources` is where the read of the PHP this repository declares lives,
  because this is the second caller. `RecordsTest` had the same scan and now
  asks for it. The scan happens once per process, since both readings run over
  the whole corpus.
- The count is of classes named rather than of references. So an entry that
  mentions one class ten times ranks below one that reaches into five.

## Assumed

- That a **Covered by** names a test that would catch a move of the entry's own
  claim. The format asks for exactly that and nothing measures it, so an entry
  that names a test about something else reads here as covered.
- That an entry that names none of our classes is not at risk in this way. It
  may still describe behaviour in prose and never name the class it belongs to,
  and nothing here sees that.

## Wrong if

- The number stands still while entries still go stale. That would mean readers
  take the report as a property of the corpus rather than as work. It is 89.
- An entry gains a **Covered by** that names a test which does not hold its
  claim, to leave the report. The name would resolve, the count would fall, and
  the entry would be no more coupled than before.
- An entry with a test goes stale anyway, because the test held a narrower claim
  than the statement. That is the assumption above as it fails, and a fourth
  stale entry with **Covered by** would show it.

## Since then

The **Assumed** above is not measurable and its proxy is: whether the test says
anything about the entry that rests on it. Read the same day over 555 test names
under **Covered by**, 150 do and 405 do not, across 187 entries. The names run
one way, and the way they do not run is the one where somebody stands when the
code moves. A session changes the behaviour, fixes the test and never learns
which entry rested on it. That is how `D-ANS-045` came to describe the opposite
of what its method does.

The report shows it as the second degree of the same bond rather than as a count
of its own, and nothing fails on it. **Covered by** has never asked for the name
back, so the corpus stood under the older rule. The six entries of 2026-08-22
were the newest instance and carry the correction.

## Since then

The proxy read the docblock alone, and this corpus writes half its reasons in
the body. Of 346 names called silent, 38 carry the entry's id in a comment
beside the assertion it explains. That is the name a session in the test already
gets. A second read found the same method twice more. It started at the last
`/**` before the declaration. So a method with a `@param` line read from the
second docblock and one with no comment from the previous method's. It reads
from where the run of comments begins now, and the count went up rather than
down. What none of that licenses is a lower count.

## Since then

The names reached zero on 2026-08-22 and are a problem from that day rather than
a report. What kept it out of the checks was that the corpus stood under the
older rule, and 405 names now exist. 313 of them are a clause on the comment
each method already carried, and 40 are a sentence where there was none.
`decisions:check` fails on a new one now, and a test in `DecisionsTest` is the
guard.

What that costs is a name per entry in a comment somebody has to keep true, and
it is the cheapest half of the bond. The id says which entry to open, and the
entry says what would falsify it. What it does not do is make the names correct.
An entry may still name a test that holds a different claim, which is the first
**Assumed** above and is not measurable.

## Since then

The name is one thing written once, since 2026-08-23: a test declares
`#[Decision]` and `bin/cli decisions:cover` writes the entry's `coveredBy` from
it, `D-DOC-048`. What that ends is the half of this entry that measured how much
of the prose said the id back, and the scan it needed. The **Assumed** above is
what it does not end: an attribute names an entry and nothing says the test
holds that entry's claim.
