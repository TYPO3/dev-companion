---
id: R-KNW-014
title: 'A file list covers the one on its way out'
status: held
heldBy:
  - HintsTest::theFileAnExtensionNoLongerNeedsIsCoveredWhereItsFilesAre
---

# R-KNW-014 — A file list covers the one on its way out

**A list of the files a subject consists of covers the one that is on its way
out, with the shape that replaces it.**

Absence reads as "not relevant", which is the one thing a deprecated file is
not.

## From

`extension-files` listed every current registration file and did not mention
`ext_emconf.php`, whose deprecation turned a first functional test run red
(2026-07-29).
