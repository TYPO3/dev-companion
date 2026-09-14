---
id: R-ANS-036
title: 'A suite list names the paths no suite covers'
status: held
restsOn: [D-GUI-022]
heldBy:
  - HintsTest::aCallWhoseEveryPathReachesASuiteNamesNoUncoveredPath
  - HintsTest::aPathNoSuiteCoversIsNamedInTheAnswer
---

# R-ANS-036 — A suite list names the paths no suite covers

**A suite list narrowed by paths names the paths that reached no suite.**

The suites come back for the paths that select them, and a path that selects
none is in the input and nowhere in the answer. What a caller has then is a list
of commands and a file none of them runs over, with nothing that says which. So
the silence reads as coverage, and the alternative is to derive per path what
the narrowed answer already derived.

## From

`feedback/2026-08-25-105324`, the debrief of a review of a link swap across git
hooks, docblocks, an XML reference and five Fluid templates (2026-08-25). The
answer narrowed to `php` and `fluid`. The session established by hand that no
suite covers `typo3/sysext/backend/Resources/Private/tsref.xml`: "silence reads
as coverage". Judged on 2026-08-27 as
[`D-GUI-022`](../../decisions/guides/gui-022-the-paths-a-brief-is-composed-from-name-a-subsystem-rather-than-a-diff.md).
That entry is also where the two halves the same report asked of
`typo3_task_guide` got declined and queued.

## Held by

`typo3_test_run_guide` is the carrier, as the one answer here that a caller
narrows with the files it changed.
`aCallWhoseEveryPathReachesASuiteNamesNoUncoveredPath` holds the bound. Where
every path reached a suite there is nothing to name, and the sentence would be
noise. That is
[`D-ANS-074`](../../decisions/answers/ans-074-a-path-narrowed-suite-list-names-what-it-withheld.md)'s
rule for the withheld domains one level down.
