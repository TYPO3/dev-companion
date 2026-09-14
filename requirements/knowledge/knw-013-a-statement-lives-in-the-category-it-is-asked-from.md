---
id: R-KNW-013
title: 'A statement lives in the category it is asked from'
status: held
heldBy:
  - HintsTest::aMenuQuestionThatReadsAsFrontendWorkStillReachesTheMenuTrap
  - HintsTest::aNavigationIsAnsweredWhereMenusAreActuallyConfigured
---

# R-KNW-013 — A statement lives in the category it is asked from

**A statement lives in the category its question comes from, not in the one the
mechanism happens to live in.**

Domains withhold whole categories. So a trap about the configuration of a site
that sits among the PHP hints is invisible to every query that reads as frontend
work. A caller who was right that they could not find it reports it as absent a
second time.

## From

`excludeDoktypes` reported a second time, while the sentence about it was in
`frontend-dataprocessors`. That is a hint about the write of a processor, which
a sitepackage question never sees (2026-07-29).
