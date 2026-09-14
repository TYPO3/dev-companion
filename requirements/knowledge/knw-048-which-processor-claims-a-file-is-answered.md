---
id: R-KNW-048
title: 'Which processor claims a file is answered'
status: held
heldBy:
  - HintsTest::whichProcessorClaimsAFileIsAnswered
---

# R-KNW-048 — Which processor claims a file is answered

**How a file becomes a processed one has an answer: the order the registry asks
in, and the first `canProcessTask()` that says yes.**

The order is the whole of it. A processor registered after the one that already
claims a case never runs, and nothing says so at the point of its registration.

## From

A patch review that replaced GD read seven core classes by hand, because nothing
below `knowledge/` said which of them runs when. Those were
`GraphicalFunctions`, `LocalImageProcessor`, `SvgImageProcessor`,
`ThumbnailViewHelper`, `DeferredBackendImageProcessor`, `PreviewProcessing` and
`PreviewNotAvailable.svg` (2026-08-01, judged as `D-KNW-028`).
