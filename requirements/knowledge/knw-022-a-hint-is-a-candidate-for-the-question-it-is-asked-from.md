---
id: R-KNW-022
title: 'A hint is a candidate for the question it is asked from'
status: held
heldBy:
  - HintsTest::aPhpPathIsNeverAnsweredWithFrontendConventions
  - HintsTest::everyHintIsReachedByItsOwnTitle
  - HintsTest::whatACallerCanSeeReachesTheHintAboutIt
  - ScopeTest
---

# R-KNW-022 — A hint is a candidate for the question it is asked from

**A hint is a candidate for the question its subject comes up in, not only for
the one that carries its category's name.**

Domains withhold whole categories before the matcher scores anything. So a
category whose vocabulary is the vocabulary of somebody who already knows the
answer is invisible. The words a caller arrives with are what they can see: a
colour, a dark mode, a shadow, a spacing. The words the hints sit under are
`sass`, `scss`, `css`. A hint that its own title does not reach is unreachable,
and that is the floor this holds. It is
[`R-KNW-013`](knw-013-a-statement-lives-in-the-category-it-is-asked-from.md)
at the gate rather than in the filing, and it does not widen the answers.
`namesTheFrontend` still withholds the backend's own design system where the
task is about the website. A component asked for by name is
`typo3_component_lookup`'s and stays there.

## From

The first `bin/cli hints:coverage` reading (2026-07-30). Eight of the nineteen
Backend CSS hints were out of reach for their own title, and all nineteen out of
reach for every scenario prompt.

## Held by

- `HintsTest::whatACallerCanSeeReachesTheHintAboutIt`, with
- `HintsTest::aPhpPathIsNeverAnsweredWithFrontendConventions` and
- `ScopeTest`'s frontend withhold, which hold the other direction
