---
id: D-ANS-124
title: A first-hit assertion rests on the hint's own vocabulary
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-ANS-124 — A first-hit assertion rests on the hint's own vocabulary

**An assertion that names the first hit is a claim about that hint. The tier
above the score decides first place, and no reachable weight shift moves it.**

`D-ANS-115` swept admission and assumed the other half was the worse one. A
shifted term weight also reorders what stays admitted. So an assertion that
names the first hit could break while every hint in it still comes back. It is
the cheaper half. The order is where this matcher is robust. The assertions that
name a first hit are the part of it nothing outside their own hint reaches.

## Evidence

- The population is every query `HintsTest` puts to `Hints::find()` rather than
  the literals somebody can grep for. That is 456 distinct queries against
  `D-ANS-115`'s 58, the ones that arrive through a variable, a data provider or
  a tool included. A log of the arguments of `find()` through one suite run
  collected them.
- The perturbation is `D-ANS-115`'s. One term's document frequency rises by `k`
  carriers with the candidate count held fixed, which takes that term's weight
  from `log(N/c)` to `log(N/(c+k))`. Then the suite runs again. It sat as a
  patch on `find()` for the length of the sweep.
- At `k=1`, the event that made a test red on 2026-08-26, nothing in `HintsTest`
  fails. One query of the 456 changes its first hit at all. The title
  "Referencing a Label by Its Domain" swaps `language-files` and
  `translation-domain`, under an assertion that asks for containment, which both
  of them keep.
- Coverage alone admits 588 of the 2052 (query, hint) pairs those queries
  return, against the 92 `D-ANS-115` counted over the 58 literals. 66 of them
  fall out of their answer at `k=1`. No assertion names one.
- A term's weight at zero is the most a corpus that grows can do to it, because
  a word every candidate carries separates nothing. That breaks no assertion for
  748 of the 773 terms these queries consist of.
- The 25 that do break start at 17 further carriers. Each is a hint that leaves
  the first six rather than a first hit that moves. The thinnest is
  "viewhelper": at 17, `fluid-templates` leaves the six returned for "what
  arguments a viewhelper takes", where the assertion is a containment one.
- Not one of the twenty assertions in `HintsTest` that name a first hit breaks
  at any `k`. `KnowledgeTest::theDiscriminatingTermsOfAQueryDecideTheAnswer` is
  the only such assertion outside it. `site-sets` stands first there on
  `appliesTo(11)` over `appliesTo(8)` while the highest score in the answer
  belongs to a third hint.
- What carries them is the tier order rather than a margin. Of the 456 queries,
  the curated vocabulary decides first place for 267, and 79 return a single
  hit. The answer tier decides 5, the backend-module rule 7, and 12 tie on score
  and fall to the title. The 86 the score decides stand a median 47% apart and
  3.7% apart at the closest. The shift `D-ANS-115` recorded took a weight down
  by two and a half percent.

## Decided

- A first-hit assertion that goes red reads as a change to the hint it names:
  its `appliesTo`, its title or its body. Admission is the other way round, and
  that is the difference `D-ANS-115` left open.
- Nothing is repaired. `D-ANS-115` moved a wording into `appliesTo` because one
  further carrier took its query below the floor. The nearest order assertion is
  seventeen carriers away and asks for containment.
- `bin/cli hints:probe` prints no order margin. First place stands before any
  weight read in 358 of the 456 queries, and the rest stand a median 47% apart.
  So it would be a read nobody has a use for.
- `coveredBy: []`, because a test that holds this ships the perturbation as a
  seam in `find()`. That is a sweep's parameter in the answer path, for a read
  taken twice.

## Assumed

- The candidate count stays fixed while the carriers rise, as in `D-ANS-115`. A
  hint written into the corpus raises both, and a higher total lifts every
  weight. So the model overstates the shift rather than understates it.
- One term at a time. A statement carries many words at once, and what several
  small shifts do together was not measured.
- Tests outside `HintsTest` were not swept, beyond the one first-hit assertion
  `KnowledgeTest` carries.

## Wrong if

- An assertion that names a first hit fails on a commit that touches no hint it
  names.
- A hint an assertion asks for leaves the first six on a corpus that grew by
  fewer than seventeen carriers. That is carriers of one of that query's words.
