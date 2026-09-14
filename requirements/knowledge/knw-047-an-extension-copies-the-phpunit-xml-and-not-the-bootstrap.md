---
id: R-KNW-047
title: 'An extension copies the phpunit XML and not the bootstrap'
status: held
heldBy:
  - HintsTest::theBootstrapIsReferenced
---

# R-KNW-047 — An extension copies the phpunit XML and not the bootstrap

**The test-setup answer says to copy `UnitTests.xml` and `FunctionalTests.xml`
into the extension. It says to reference the framework's bootstraps from the
vendor directory rather than copy them.**

The XML needs a copy because its testsuite directory points into the core mono
repository. The bootstrap holds nothing an extension configures, a `Testbase`,
`ORIGINAL_ROOT` and two directories below `typo3temp/var/`. So a copy is a file
nobody updates afterwards.

## From

The corpus said "copy all four", read off the header of the upstream XML, which
tells extensions to copy the bootstrap along with it. That is boilerplate
maintenance advice rather than a requirement, and it costs the extension a file
that drifts (2026-08-03).
