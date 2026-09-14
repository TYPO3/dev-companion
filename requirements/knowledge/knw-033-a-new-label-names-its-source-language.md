---
id: R-KNW-033
title: 'A new label names its source language'
status: held
restsOn: [D-KNW-011]
heldBy:
  - HintsTest::aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes
---

# R-KNW-033 — A new label names its source language

**A label answer says that the source XLF is English and where a locale's
translation goes. It says how to correct a source file that is not English.**

The translation goes into the locale-prefixed file beside the source file, under
the same unit id. A non-English source file already in a package is a defect to
report rather than a local convention to continue. The report says what to do
about it. The source file keeps its path and its unit ids and its words become
English. The words it replaced move into the locale-prefixed file beside it as
`<target>` under `source-language="en" target-language="<locale>"`. An
`en.`-prefixed file is not the correction. The unprefixed file reads as the
`default` locale that every other locale falls back to.

## From

A sitepackage whose German source XLF led a forward run to add every new
backend-module label in German too (2026-07-30). An audit of a package already
in that state offered "add en.xlf" and "switch the source to en and add de.xlf"
as equal remedies (2026-07-31).
