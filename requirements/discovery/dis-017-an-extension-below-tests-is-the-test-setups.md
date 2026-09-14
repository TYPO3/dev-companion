---
id: R-DIS-017
title: "An extension below Tests/ is the test setup's"
status: held
heldBy:
  - InstanceTest::aPackageBelowTestsBelongsToTheTestSetup
---

# R-DIS-017 — An extension below Tests/ is the test setup's

**The answer reports an extension below a `Tests/` directory as `fixture` rather
than as the project's own, in the project and extension scope alike.**

It stays in the answer. A package an installation loads and the answer omits is
one nobody can account for.

## From

`REVIEW-02`. `demo_package` below `Tests/Packages/` came back with origin
`project`, which the schema defines as "inside the repository, so what it is
working on" (2026-07-31).
