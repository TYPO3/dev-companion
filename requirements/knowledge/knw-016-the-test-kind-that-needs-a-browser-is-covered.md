---
id: R-KNW-016
title: 'The test kind that needs a browser is covered'
status: held
heldBy:
  - HintsTest::theTestKindThatNeedsABrowserIsCovered
---

# R-KNW-016 — The test kind that needs a browser is covered

**The kind of test that needs a browser is a subject here, apart from the one
that does not.**

A render test through `executeFrontendSubRequest()` runs no script, applies no
stylesheet and speaks no HTTP. So a suite made only of those has never seen the
page a reader gets, and the name frontend test is what hides that.

## From

Browser tests answered with the id index and a section about site sets, while
the core works the conventions out in `Build/tests/playwright/`. And a first axe
run on a theme that passed every other test, which failed on contrast four times
and was right each time (2026-07-29).
