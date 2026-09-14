---
id: R-ANS-003
title: 'A component answer carries its source and its version'
status: held
heldBy:
  - CatalogTest::aStatedVersionSaysWhatItDidToTheAnswer
  - CatalogTest::anInstalledContractDoesNotAnswerForAnotherTargetMajor
  - CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead
  - CatalogTest::theInstalledComponentContractWinsOverTheBundledSnapshot
  - CatalogTest::theSnapshotScopeSeparatesEntryValidityFromItsSourceCheckout
  - ToolContractTest
---

# R-ANS-003 — A component answer carries its source and its version

**A component answer qualifies what it describes by its source and version
inside the entry rather than only in a block at the end.**

When the target is the active installation, its backend CSS and JavaScript
decide component presence, classes, and custom properties. An installed
styleguide example that matches supplies markup. The bundled catalog stays the
curated search index and markup fallback. For another target or without usable
package evidence, every fallback entry says which majors somebody verified it
on. The answer withholds one nobody verified there.

## From

15.0 markup handed to a caller that supports 13.4 and 14.3 (2026-07-29). An
answer for 14.3 whose loudest version number was the 15.0 snapshot (2026-07-30).

## Held by

- `CatalogTest::anInstalledContractDoesNotAnswerForAnotherTargetMajor`, and the
- `describesVersion` field the component schema requires (`ToolContractTest`).
