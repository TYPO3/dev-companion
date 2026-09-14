---
id: R-ANS-012
title: 'An answer that cannot read something says so'
status: held
heldBy:
  - ProjectTest::aRegistrationBuiltInALoopIsNotDeterminable
  - ProjectTest::anExtbasePluginIsToldApartFromAnElementWithoutATemplate
  - ProjectTest::anIdentifierThatTookADetourThroughAVariableIsStillRead
  - ProjectTest::theFilesThatRegisterByRunningAreSaidToBeUnread
---

# R-ANS-012 — An answer that cannot read something says so

**An installation answer that cannot read something says so instead of returns a
shorter list.**

A read of a declaration file follows a value the file assigns to a variable
once. It still declines a value assembled at runtime, taken from a constant, or
read from a variable the file reassigns. The answer names which of those it
cannot follow. Where a whole file yields nothing, because its list exists only
once it has run, the answer names the file itself. A section left out for its
emptiness reads the same as a file that was never there. The answer says which
file it never opens at all, and says it apart from those. That is not a
degradation, so the list of files that defeated the parser is no place to look
for it. Its emptiness is no claim that the answer read everything.

## From

The third `REVIEW-01` run (2026-07-31), where `typo3_extension_describe`
reported three content elements of four. The fourth wrote `$contentType = '…'`
above its `addRecordType()` call. An earlier run had already read the omission
as a template with no registration, a defect the extension does not have.
