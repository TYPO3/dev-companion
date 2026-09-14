---
id: R-DOC-001
title: 'The live manuals answer for the version they were asked for'
status: held
heldBy:
  - DocumentationTest
  - PermalinkTest
  - PermalinkTest::aManualServedFromAnotherBranchSaysWhichOneAnswered
  - ScopeTest::everyToolIsReachableThroughTheScope
  - ScopeTest::everyToolNamedInTheScopeExists
  - ToolContractTest::aToolCallAnswersWithTextAndMatchingData
  - ToolContractTest::everyToolDeclaresSchemasAndAnnotations
---

# R-DOC-001 — The live manuals answer for the version they were asked for

**The official live documentation answers broad API, reference and tutorial
questions for a TYPO3 version the caller selects.**

Every result carries its canonical URL, document identifier, document version,
section and source. A requested release never falls back to another release or
`main` in silence. No match and an unreachable service are different structured
answers. Live documentation adds to the bundled conventions and their version,
audience and bound data, and does not replace them. The manuals in the search
are the ones a question can be about, TCA among them. The search reads the index
the way the rest of this server searches. A term is worth what it separates one
page from the others, and the search takes a compound name apart on both sides.
A table of contents holds page titles, so `AssetCollector` and
`FunctionalTestCase` appear in it nowhere. The pages that answer them carry the
title of their subject, which is assets and functional tests. A caller can pass
a canonical result URL back with the same target version. Then the page comes
back as text with its headings and code examples. So a caller does not have to
rebuild the API from installed sources after the search found the right manual
page.

## From

`EXT-07`; and two sessions answered with whatever else carried one of their
words. That was TCA `inline` with PSR-14 events, and the test APIs with the
content-element pages (2026-07-30).
