---
id: R-KNW-061
title: 'A translation file is told what a missing `target-language` costs it'
status: held
restsOn: [D-KNW-050]
heldBy:
  - HintsTest::aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt
  - HintsTest::whatAMissingTargetLanguageCostsIsWithheldWhereItIsFree
---

# R-KNW-061 — A translation file is told what a missing `target-language` costs it

**A label answer states that v14 reads a locale-prefixed XLF with no
`target-language` as the default language, so it discards its `<target>` values
in silence.**

The labels then render in the source words, and nothing throws, logs or
deprecates. The rule that governs such a file today says what a correct one
declares. That is enough to write one and not enough to recognise one that is
already wrong. An audit reads a rule in that direction. So the statement has the
form of the defect and names what the reader observes. That is English in a
German backend, a maintained translation file in the package, no error and no
log line anywhere.

The version boundary is the second half, and the statement is wrong without it.
Up to 13.4 the same file worked, because the parser decided on the requested
language rather than on an attribute of the file. So this is what an upgrade to
v14 does to a package that was right before it. The answer has to reach a
session that asks about an upgrade as well as one that audits a file.

## From

`feedback/2026-08-03-164659`, a conformance audit of `EXT:guidedtour` against a
TYPO3 14.3.5 installation (2026-08-03). Its finding with the highest impact, 22
German translations lost from a maintained file, came from four hops into
installed source. The hint that governs that file offered no way to see it.
