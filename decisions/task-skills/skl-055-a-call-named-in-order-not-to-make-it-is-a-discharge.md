---
id: D-SKL-055
title: 'A call named in order not to make it is a discharge'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
  - SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence
---

# D-SKL-055 — A call named in order not to make it is a discharge

**A tool a skill names in order not to call it is written as a discharge, and a
routing is the first mention outside one.** The construct is the tool's name,
`is discharged by`, and what answers it instead.

`everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` asserts a routing by
finding the tool's name anywhere in the body, so the sentence telling a caller
to skip the call satisfies the assertion for it. Nobody can read which of the
two a mention is off free prose. So the disowned half gets a form and the routed
half is everything else.

## Evidence

- `73cff0ab` rewrote `typo3-development-installation`'s step to say the base's
  `typo3_project_describe` discharges `typo3_server_scope` (`D-ANS-083`).
  `ROUTING_SKILLS` went on to list the tool first for that skill. Nothing
  failed. `everySkillStartsFromTheBaseBeforeItsOwnEvidence` took the same
  sentence as the skill's first routing, so it asserted that the base is
  established before a call the body says is not made.
- The mentions are not uniform enough for the opposite rule. Read over `skills/`
  on 2026-08-18. Its 116 backticked tool mentions continue with `with`, `for`,
  `before`, `says`, `answers`, `owns`, `carries`, `names`, `states`, `reports`,
  `marks`, `takes` and `is what`. They stand as the object of a sentence that
  began three lines earlier. No shape separates a routing from a discharge. The
  discharge is the marked case and there is one of it.
- A count per skill does not generalise. The guard this replaces held
  `typo3-development-installation` at one mention of `typo3_server_scope`. A
  tool a skill legitimately routes to twice is ordinary, and that same body
  names `typo3_hint_lookup` eight times.
- The construct was already in the file. `73cff0ab` wrote "is discharged by the
  base's", and the word is this repository's own for the relation. `D-ANS-083`
  states that the step is "discharged by any `typo3_project_describe` answer".

## Decided

- `DISCHARGED_TOOLS` records which tool each skill discharges, beside the
  routings and exclusive with them.
- The routing helper in `SkillTest` is what both order assertions read a
  position from: the first mention outside a discharge, and `false` where every
  mention is one.
- `everyDischargedCallIsWrittenAsOneAndRoutedNowhere` runs over the directory
  and holds both directions: a discharge nobody recorded, and a recorded one
  nobody wrote. So it holds the next skill that discharges a call whether or not
  its author has seen the list.
- The test reads the bodies flat. The construct is a sentence, and a rewrap at
  80 columns moves its line break through the middle of it.
- Rejected: a vocabulary of discharge words, "skip", "already answered", "no
  need", matched against the sentence the routing stands in. It bans the
  phrasings that have happened and lets the next one through, and it misreads a
  routing whose own sentence carries one of the words.
- Rejected: a hold on every mention of a tool as either a routing or a
  discharge. Bodies that do not route to the four calls the base fixes name
  them, deliberately, so most mentions are neither.

## Assumed

- That an author who wants to discharge a call reaches for the construct rather
  than invents a sentence. What makes that likely is that the author contract
  states it and that there is nowhere else to record the tool. The routing
  assertion fails on a tool left in `ROUTING_SKILLS`.

## Wrong if

- A skill disowns a call in prose that carries no discharge, keeps the tool in
  its routing list, and passes. The construct would then be documentation rather
  than a discriminator, and what separates the two would have to be read off the
  routing side instead — the mention written as a call, which the corpus does
  not support today.
- A second skill discharges a call and the sentence reads worse in these forced
  words. The construct would then cost prose to buy an assertion, and the
  assertion is worth less than one readable step.
- `DISCHARGED_TOOLS` grows past a handful of entries. A skill that discharges
  several calls is a skill that restates what the base already fixes, which is
  the thing the base exists to stop.
