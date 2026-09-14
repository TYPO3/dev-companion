---
id: R-KNW-062
title: 'The import answer says what it rewrites in a site configuration'
status: held
restsOn: [D-KNW-048]
heldBy:
  - HintsTest::theImportAnswerSaysWhatItRewritesInASiteConfiguration
---

# R-KNW-062 — The import answer says what it rewrites in a site configuration

**The answer about a site configuration inside an export file names the base the
import overwrites it with, and when that import does not run.**

A statement that only the root page id gets a new value names the route it holds
for. The two routes differ in exactly that.

## From

A session that seeded a TYPO3 14.3 installation from a distribution package. The
frontend answered 404 at the project root. The corpus carried two sentences that
read as coverage of the case and pointed the other way
(`feedback/2026-08-03-162836`, 2026-08-03).
