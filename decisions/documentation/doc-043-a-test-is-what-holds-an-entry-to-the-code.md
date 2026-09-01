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

**`bin/cli decisions:check` names the entries pointing at this repository's code
that name no test in `coveredBy`, and fails on none of them.**

A test named there fails when the behaviour moves, and
`DecisionsTest::everyTestADecisionNamesExists` fails when the test goes with it.
Nothing else couples the two.

## Evidence

- Read on 2026-08-22. Of 443 entries, 236 carry **Covered by**; 348 name a
  `Class::member` this repository declares, and 89 of those name no test.
- The three entries found stale that day carry none. `D-SCO-007` stood `open`
  while the call its statement describes was replaced twice, `D-ANS-045`
  recorded a closed list of thirteen directory names against a method that reads
  the directory, and `D-EVI-003` named a tool renamed weeks earlier.
- The two whose named code had moved under them and which were right both carry
  one. `TermSearchTest::aStemRunsPastItsOwnEndAndACuratedWordDoesNot` holds
  `D-ANS-050`'s claim, and three tests held `D-SKL-027`'s — of them
  `SkillTest::everyFrontMatterFieldIsOneTheStandardDefines` outlived the entry
  (`D-SKL-087`). `Text` and `Installer` both changed on 2026-08-18 and neither
  entry needed a reader.
- So the signal proposed first was the wrong one. "The statement names code that
  has changed since" would have reported exactly those two and none of the
  three.
- The other coupling is weaker. 156 decision ids are named from `src/` and 266
  from `tests/`, and 155 entries are named in neither — but an id in a comment
  fails nothing. `D-SKL-027` is named nowhere in the code and is safe;
  `D-FBK-004` is named nowhere and is not.

## Decided

- A report beside the outgrown one in `bin/cli decisions:check`, most references
  first. `Decisions::uncovered()` is the reading and
  `DecisionsTest::anEntryNamingThisCodeWithNoTestIsReadOut`
  holds it.
- Nothing fails. Most entries here decide something about process and no test
  could keep them, which the format says in as many words — a demand for
  **Covered by** would be answered with a test name chosen to satisfy it, and
  that is worse than the silence it replaces.
- `Upkeep\Sources` is where the PHP this repository declares is read, because
  this is the second caller: `RecordsTest` had the same scan and now asks for
  it. The scan happens once per process, since both readings run over the whole
  corpus.
- The count is of classes named rather than of references, so an entry
  mentioning one class ten times ranks below one that reaches into five.

## Assumed

- That a **Covered by** names a test that would catch the entry's own claim
  moving. The format asks for exactly that and nothing measures it, so an entry
  naming a test about something else reads here as covered.
- That an entry naming none of our classes is not at risk in this way. It may
  still describe behaviour in prose without naming the class it belongs to, and
  nothing here sees that.

## Wrong if

- The number stands still while entries go on going stale, which would mean the
  report is read as a property of the corpus rather than as work. It is 89.
- An entry gains a **Covered by** naming a test that does not hold its claim, to
  leave the report. The name would resolve, the count would fall, and the entry
  would be no more coupled than before.
- An entry with a test goes stale anyway, because the test held a narrower claim
  than the statement. That is the assumption above failing, and a fourth stale
  entry carrying **Covered by** would show it.

## Since then

The **Assumed** above is not measurable and its proxy is: whether the test says
anything about the entry resting on it. Read the same day over 555 test names
under **Covered by**, 150 do and 405 do not, across 187 entries. The naming runs
one way, and the way it does not run is the one where somebody is standing when
the code moves — a session that changes the behaviour, fixes the test and never
learns which entry rested on it is how `D-ANS-045` came to describe the opposite
of what its method does.

It is reported as the second degree of the same coupling rather than as a
reading of its own, and nothing fails on it: **Covered by** has never asked for
the return naming, so the corpus was written under the older rule. The six
entries of 2026-08-22 were the newest instance and are corrected.

## Since then

The proxy read the docblock alone, and this corpus writes half its reasons in
the body: of 346 names called silent, 38 carry the entry's id in a comment
beside the assertion it explains, which is the naming a session standing in the
test already gets. Reading it again found the same method twice more — it
started at the last `/**` before the declaration, so a method with a `@param`
line was read from the second docblock and one with no comment from the previous
method's. It reads from where the run of comments begins now, and the count went
up rather than down. What none of that licenses is reading the count down.

## Since then

The naming reached zero on 2026-08-22 and is a problem from that day rather than
a report. What kept it out of the checks was that the corpus had been written
under the older rule, and 405 names were: 313 of them are a clause on the
comment each method already carried, and 40 are a sentence written where there
was none. `decisions:check` fails on a new one now, and a test in
`DecisionsTest` is the guard.

What that costs is a name per entry in a comment somebody has to keep true, and
it is the cheapest half of the coupling: the id says which entry to open, and
the entry says what would falsify it. What it does not do is make the naming
correct — an entry may still name a test holding a different claim, which is the
first **Assumed** above and is not measurable.

## Since then

The naming is one thing written once, since 2026-08-23: a test declares
`#[Decision]` and `bin/cli decisions:cover` writes the entry's `coveredBy` from
it — `D-DOC-048`. What that ends is the half of this entry that measured how
much of the prose said the id back, and the reading it needed. The **Assumed**
above is what it does not end: an attribute names an entry and nothing says the
test holds that entry's claim.
