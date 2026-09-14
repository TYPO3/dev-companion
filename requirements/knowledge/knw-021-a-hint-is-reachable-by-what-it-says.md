---
id: R-KNW-021
title: 'A hint is reachable by what it says'
status: held
heldBy:
  - HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# R-KNW-021 — A hint is reachable by what it says

**A hint is reachable by what it says, not only by the words in its index.**

`appliesTo` is a curator's guess at how a caller will ask about the subject. A
hint's own statements are where the symptom stands. «The failure is a
service-not-found at request time» is the sentence a caller arrives with. It was
the one thing the matcher could not see. The matcher scores both, the curated
vocabulary above the prose, so a phrase somebody anticipated still decides. This
does not withdraw
[`R-KNW-002`](knw-002-a-hint-carries-the-words-its-subject-is-asked-about-in.md).
It removes its cost, which was that the author had to foresee every phrase. The
corollary of a wider match is an answer to everything, so this holds the other
half too. A term the corpus does not carry lowers what any answer can cover. So
a query about a subject nobody wrote down still misses, and the index
[`R-ANS-006`](../answers/ans-006-a-miss-says-what-there-would-have-been-to-find.md)
requires answers it.

## From

A measurement of the matcher on 2026-07-30: 57 hints and 11,501 words of hint
body reachable through 9.3 keywords each. Of eighteen realistic queries, seven
reached nothing, two of them the `dependency-injection-services` hint that names
the symptom outright.

## Held by

- `HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay`, the
- Measurement itself, both halves of it. That is the queries that reached
  nothing before and the two the corpus has no answer for. Those two say the
  matcher did not simply start to answer everything.
- `HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten` holds the
- Rank, where the sweep only holds the membership.

- A sweep is still a sample, so `bin/cli hints:coverage` is what says how much
  of the corpus is reachable at all. That is which hints their own title does
  not reach, which no scenario prompt reaches, and which prompts reach nothing.
