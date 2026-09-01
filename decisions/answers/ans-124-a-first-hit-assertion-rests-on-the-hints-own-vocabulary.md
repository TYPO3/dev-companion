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

**An assertion naming the first hit is a claim about that hint: the tier above
the score decides first place, and no reachable weight shift moves it.**

`D-ANS-115` swept admission and assumed the other half was the worse one — a
shifted term weight also reorders what stays admitted, so an assertion naming
the first hit could break while every hint involved is still returned. It is the
cheaper half. Ordering is where this matcher is robust, and the assertions
naming a first hit are the part of it nothing outside their own hint reaches.

## Evidence

- The population is every query `HintsTest` puts to `Hints::find()` rather than
  the literals somebody can grep for: 456 distinct queries against `D-ANS-115`'s
  58, the ones arriving through a variable, a data provider or a tool included.
  They were collected by logging the arguments of `find()` through one suite
  run.
- The perturbation is `D-ANS-115`'s. One term's document frequency is raised by
  `k` carriers with the candidate count held fixed, which takes that term's
  weight from `log(N/c)` to `log(N/(c+k))`, and the suite is run again. It was
  applied as a patch to `find()` for the length of the sweep.
- At `k=1` — the event that made a test red on 2026-08-26 — nothing in
  `HintsTest` fails, and one query of the 456 changes its first hit at all. The
  title "Referencing a Label by Its Domain" swaps `language-files` and
  `translation-domain`, under an assertion asking for containment that both of
  them keep.
- 588 of the 2052 (query, hint) pairs those queries return are admitted by
  coverage alone, against the 92 `D-ANS-115` counted over the 58 literals, and
  66 of them fall out of their answer at `k=1`. No assertion names one.
- Driving a term's weight to zero is the most a growing corpus can do to it,
  because a word every candidate carries separates nothing. That breaks no
  assertion for 748 of the 773 terms these queries are made of.
- The 25 that do break start at 17 further carriers, and each is a hint leaving
  the first six rather than a first hit moving. The thinnest is "viewhelper": at
  17, `fluid-templates` leaves the six returned for "what arguments a viewhelper
  takes", where the assertion is a containment one.
- Not one of the twenty assertions in `HintsTest` naming a first hit breaks at
  any `k`. `KnowledgeTest::theDiscriminatingTermsOfAQueryDecideTheAnswer` is the
  only such assertion outside it, and `site-sets` stands first there on
  `appliesTo(11)` over `appliesTo(8)` while the highest score in the answer
  belongs to a third hint.
- What carries them is the tier order rather than a margin. Of the 456 queries,
  267 have first place decided by the curated vocabulary, 79 return a single
  hit, 5 are decided by the answering tier, 7 by the backend-module rule, and 12
  tie on score and fall to the title. The 86 the score decides stand a median
  47% apart and 3.7% apart at the closest, while the shift `D-ANS-115` recorded
  took a weight down by two and a half percent.

## Decided

- A first-hit assertion that goes red is read as a change to the hint it names —
  its `appliesTo`, its title or its body. Admission is the other way round, and
  that is the difference `D-ANS-115` left open.
- Nothing is repaired. `D-ANS-115` moved a phrasing into `appliesTo` because one
  further carrier took its query below the floor; the nearest ordering assertion
  is seventeen carriers away and asks for containment.
- `bin/cli hints:probe` prints no ordering margin. First place is decided before
  a weight is read in 358 of the 456 queries and the rest stand a median 47%
  apart, so it would be a reading nobody has a use for.
- `coveredBy: []`, because holding this means shipping the perturbation as a
  seam in `find()` — a sweep's parameter in the answer path, for a reading taken
  twice.

## Assumed

- The candidate count is held fixed while the carriers rise, as in `D-ANS-115`.
  A hint written into the corpus raises both, and a higher total lifts every
  weight, so the model overstates the shift rather than understating it.
- One term at a time. A statement carries many words at once, and what several
  small shifts do together was not measured.
- Tests outside `HintsTest` were not swept, beyond the one first-hit assertion
  `KnowledgeTest` carries.

## Wrong if

- An assertion naming a first hit fails on a commit that touches no hint it
  names.
- A hint an assertion asks for leaves the first six on a corpus that grew by
  fewer than seventeen carriers of one of that query's words.
