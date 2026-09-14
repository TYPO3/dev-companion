---
id: R-KNW-026
title: 'A routed argument carries its cache-hash boundary'
status: held
heldBy:
  - HintsTest::routedArgumentsAreAnsweredWithTheirCacheHashBoundary
---

# R-KNW-026 — A routed argument carries its cache-hash boundary

**Routing answers tell static mapped arguments from dynamic query arguments. A
persisted or enumerated aspect needs no cHash, while a free argument on a
cacheable page does.**

A task in those terms reaches the routing answer before the unrelated
cache-framework hint.

## From

A route-enhancer answer that described the mapper but could not say whether the
URL that results still carried a cache hash (2026-07-30).
