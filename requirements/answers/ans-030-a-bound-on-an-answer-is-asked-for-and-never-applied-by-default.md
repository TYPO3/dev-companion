---
id: R-ANS-030
title: 'A bound on an answer is asked for and never applied by default'
status: held
restsOn: [D-ANS-064]
heldBy:
  - ForgeTest::theJournalComesBackWholeUnlessACallerAsksForLessOfIt
  - ForgeTest::thePingsAreWhatALimitedReaderDropsAndTheChangesSurviveThem
  - HintsTest::aNarrowedSuiteListNamesTheDomainsItWithheldAndCountsThem
---

# R-ANS-030 — A bound on an answer is asked for and never applied by default

**Where an answer gets smaller, the caller asks for it, and the answer counts
what it left out either way.**

A default bound hits the caller who needed the whole thing, and it hits in
silence. The answer has the same shape with or without the cut. What makes a
bound safe is that a caller who reads one record keeps what it had. A caller who
asked for less can see how much less it got.

## From

`feedback/2026-08-07-231213` and `2026-08-07-233524`, 2026-08-07. The choice of
one real bug out of thirty candidates is per-issue judgement whose evidence is
in the comments. The session that reported it says it could not have afforded to
read them across ten. The same session filed the journal as what saved it three
times over, and a second session put numbers on that. The decisive note on 14858
was the sixteenth of sixteen and on 15984 the twelfth of sixteen. So "the most
recent N" is not the shape and neither is a sample. What may go is what a reader
was never going to use.

**Built on 2026-08-08.** `typo3_forge_lookup` takes `notes: "people"`, which
drops the notes a review bot wrote and nothing else. Measured against
forge.typo3.org the same day, issue 14858's journal falls from 2573 to 1480
characters. All eight notes a person wrote come back where fifteen of sixteen
did before; the pings were what the bound spent itself on. The change numbers
those notes carry are a field of their own by then, so the filter costs no
handle.

## Held by

- `HintsTest::aNarrowedSuiteListNamesTheDomainsItWithheldAndCountsThem`, on the
  other payload that takes a bound: the `paths` that narrow a suite list.

The count of what the filter dropped is what says the bot list has gone stale,
and no test can hold that. An author nobody has named passes the filter, and
only a zero in the answer shows it.
