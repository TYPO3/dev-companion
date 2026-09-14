---
id: D-GUI-007
title: The brief carries a selection of the hints and says whose they are
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::theHintsABriefCarriesNameTheLookupTheyCameFrom
---

# D-GUI-007 — The brief carries a selection of the hints and says whose they are

**`typo3_task_guide` states that its hints are `typo3_hint_lookup`'s and that it
carries the strongest few of them. The separate call to that lookup stays in the
review skill.**

A report that followed the review checklist cited two of the brief's hints as
`typo3_hint_lookup` while no call to that tool had happened. Two corrections
were open, and which one is right is a fact about the two payloads rather than a
judgement.

## Evidence

- Measured on the call the fifth recorded `REVIEW-03` run actually made, against
  the server at `99785b8`. `typo3_task_guide` came back with
  `fluid-viewhelpers`, `system-extension-boundaries`, `core-tests` and
  `fal-basics`. `typo3_hint_lookup`, given the same five paths, the same task
  text and the same `targetVersion`, came back with those four in the same
  order. Then `fal-reading` and `fal-processing` at its default limit, and
  `dependency-injection` at `limit=10`.
- Each of the four is the same record on both sides — same statements, same
  ranges, same scope markers. So the guide quotes rather than summarises, and
  the run's rules were correctly attributed even though the tool it named had
  not answered.
- The brief takes four per group of paths where the lookup's default is six and
  its ceiling ten. The lookup also answers a hint by `id` and lists the ids that
  exist when nothing matched. It says when it withheld a category because the
  task names the frontend. A brief has none of the three. `id` is not among its
  arguments, its miss branch is one sentence, and the withheld categories its
  own matcher computes never render or return.

## Decided

- The brief says whose the hints are where it prints them, and the `hints` field
  of the payload says the same. The correction sits on the answer that carries
  the copy. So it costs the caller nothing and reaches every client of the tool
  rather than the readers of one skill.
- The count is a named constant, `TaskGuide::HINTS_PER_GROUP`, and the sentence
  renders from it. The number a caller reads and the number the slice stops at
  cannot then disagree.
- Rejected: the separate `typo3_hint_lookup` call out of
  `typo3-core-patch-review`, which `D-SKL-009` left open as the other candidate.
  Its premise was that the brief's copy makes the call redundant, and the
  measure above refutes it. Three of the seven hints for that patch's own paths
  are reachable only from the lookup. One of them is `dependency-injection` on a
  patch that injects a new service.

## Assumed

- That a citation is what the run got wrong, rather than the call it did not
  make. On this task the four hints the brief carried were the ones the results
  rested on, so the absent three cost that report nothing. What it cost was a
  reader who followed the citation.
- That a reader takes the source named beside the copy as attribution rather
  than as permission. The sentence says both halves for that reason, and the
  second half is the one under load.

## Wrong if

- A run cites the hints correctly and no longer calls `typo3_hint_lookup` on a
  patch whose subsystem the four carried hints do not cover. The sentence would
  then read as a substitute for the call. What remains is to say in the skill
  what the brief cannot say: that four is not a subsystem sweep.
- `HINTS_PER_GROUP` is ever raised to the lookup's own default. The second half
  of the sentence no longer holds where the brief carries everything the lookup
  would.

## Confirmed on 2026-08-03

The call ran again against this branch and answers as measured. The sentence
renders from `HINTS_PER_GROUP`, the four hints are the same, and the lookup
given the same paths answers those four and three more. Neither **Wrong if** has
its case, since no run has a record since the sentence landed.

What the visit adds is the rejected alternative priced by the session that
proposed it. It argues the separate call is a second fetch, and then names what
the skip cost. The dependency-injection hint, established instead by a grep of
three call sites. What that session asks for beyond this entry is which three
the brief left, which is `R-GUI-012`.
