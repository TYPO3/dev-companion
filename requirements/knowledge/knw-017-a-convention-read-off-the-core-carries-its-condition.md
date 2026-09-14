---
id: R-KNW-017
title: 'A convention read off the core carries its condition'
status: held
heldBy:
  - HintsTest::whereBackendLayoutsGoIsAnsweredWithTheConditionItDependsOn
---

# R-KNW-017 — A convention read off the core carries its condition

**A convention read off a core reference implementation comes with the condition
that made it right there.**

The core is not a project, so the unconditional form is the one that transfers
wrongly. The condition is the test a reader can run on their own extension, not
"camino does it differently".

## From

Backend layouts placed at extension level in a project sitepackage whose set was
the only path into any backend. So the placement had no effect at all. That
stood as the rule because `theme_camino`, which ships an extension-level
`page.tsconfig` as well, has them there (2026-07-29).
