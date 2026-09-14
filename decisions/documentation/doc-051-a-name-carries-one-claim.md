---
id: D-DOC-051
title: A name carries one claim
date: 2026-08-23
status: open
restsOn: [D-DOC-046]
coveredBy:
  - ProseTest::theNamesThatCarryTwoClaimsAreReadOut
---

# D-DOC-051 — A name carries one claim

**A test name and an entry title state one thing and name their own subject, and
`bin/cli prose:check` counts the ones that do not.**

A reader meets a name where nothing else stands, a failure list, a listing of
hundreds. So what a reader cannot resolve from it is not resolvable at all.

## Evidence

- Read on 2026-08-23 over 1353 test methods. The median name is ten words; 243
  run past twelve, and 83 join two claims with `RatherThan`, `AndNot`, `ButNot`,
  `NotOnly` or `AsWellAs`.
- The join is what a reader takes apart. `everyLineIsSetUpOnAFile` states a case
  and the case it stands apart from. The second is a sentence in the docblock
  and never a word in the name.
- The titles fail the same way and differently. "Three audiences, not one"
  counts and never names what it counts. "Activation is the client's" refers to
  what only the body introduces. "What the scope excludes is not what the server
  answers" is a negation of something the reader has to construct first. 18
  titles were rewritten the same day.
- A negation is not the defect on its own. 27 test names open with one and read
  at a glance; `noFileCarriesAConflictMarker` is shorter and plainer than any
  affirmative that says the same.

## Decided

- The rule is in `AGENTS.md` under the names section, because it holds for every
  name this repository writes and not only for a test.
- `Prose::names()` is the count and `bin/cli prose:check` prints it beside the
  titles, worst first. Nothing fails on it. A long name can be the honest one,
  and a rename driven by a counter produces a name that satisfies the counter.
- Twelve words is where the report names a name, two past what the corpus reads
  at. The number is a threshold on a report and not a limit anything holds.
- The joins are a closed list, so the count is what somebody can look up rather
  than a heuristic that changes with the corpus.

## Assumed

- That a name a reader takes apart costs more than the second claim buys. The
  measure says that the join exists, never what it explained.
- That readers meet the docblock where they met the name. A failure prints the
  name and not the docblock, so a claim moved there is a claim moved out of the
  failure.

## Wrong if

- The count falls while the names get worse — a claim dropped out of the name
  and never written into the docblock. The join is what the count covers, and
  nothing reads what replaced it.
- A test genuinely holds two claims often enough that the rule reads as an
  obstacle. Then the second claim was a second test, and this entry is what says
  so.
