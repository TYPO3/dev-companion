---
id: R-KNW-031
title: 'A persisted alias is answered in both directions'
status: held
heldBy:
  - HintsTest::persistedAliasesStateBothDirections
---

# R-KNW-031 — A persisted alias is answered in both directions

**A PersistedAliasMapper answer states both directions. Link generation takes a
record uid and emits the configured route-field value, and route matching
resolves that value back to the uid.**

It also states the consequences that make the design useful: site-unique values,
refused unmatched paths before the render, and no cHash for the mapped argument.

## From

An implementation that passed and validated the display value as a query
argument because the mapper's direction stayed implicit (2026-07-30).
