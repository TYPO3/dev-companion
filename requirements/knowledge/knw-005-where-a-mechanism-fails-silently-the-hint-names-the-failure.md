---
id: R-KNW-005
title: 'Where a mechanism fails silently, the hint names the failure'
status: held
heldBy:
  - HintsTest::aNavigationIsAnsweredWhereMenusAreActuallyConfigured
  - HintsTest::anAssetThatNeverReachesThePageIsAnsweredByItsLayout
  - HintsTest::theTemplateTrapsThatFailWithoutAnErrorAreNamed
---

# R-KNW-005 — Where a mechanism fails silently, the hint names the failure

**Where a mechanism fails in silence, the hint names the failure, not only the
rule.**

A caller whose page comes back wrong with a 200 and an empty log has nothing to
search for. So the sentence worth a line is the one that says what it looks like
when it goes wrong.

## From

A variable assigned outside `<f:section>` in a template that declares a layout,
never executed and never reported. An HTML comment whose `{placeholders}`
resolved into the response. A layout root that put the page frame inside every
content element. And `excludeDoktypes` that replaced the default list so that
every storage folder appeared in the menu (2026-07-29).

## Held by

- `HintsTest::anAssetThatNeverReachesThePageIsAnsweredByItsLayout`, the other
  half: the named failure is what makes the hint reachable by it
