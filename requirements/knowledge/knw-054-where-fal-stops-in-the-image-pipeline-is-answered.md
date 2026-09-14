---
id: R-KNW-054
title: 'Where FAL stops in the image pipeline is answered'
status: held
restsOn: [D-KNW-042]
heldBy:
  - HintsTest::whereFalStopsInTheImagePipelineIsAnswered
---

# R-KNW-054 — Where FAL stops in the image pipeline is answered

**What runs below the processing task has an answer. That is the unwrap to a
local path, the path-based API under it, and which entry points are not a way
past FAL.**

A session that reasons about what can change in this area infers necessity from
the one call path it happens to read. The corpus answered up to the processor
and stopped there, so the last thing it said read as the foundation.

## From

A session asserted twice that image processing requires a FAL object and that
dimensions are unavailable without one. That was its reason to call a wrapped
package resource technically necessary (2026-08-02, judged as `D-KNW-042`).
