---
id: R-ANS-033
title: A path names the repository it is in
status: held
restsOn: [D-ANS-097]
heldBy:
  - HintsTest::aCoreBriefSpendsItsSlotsOnTheCoreHints
  - HintsTest::anExtensionBriefSpendsItsSlotsOnTheExtensionHints
---

# R-ANS-033 — A path names the repository it is in

**A brief with paths in one repository does not carry a hint declared for
another in place of a hint that binds work there.**

A caller that names a path has said which repository the work is in, and the
brief states that verdict back in `scopes`. Where the payload has a ceiling, a
hint declared for a different repository costs one that binds this caller. So
the verdict has to reach the order and not only the label printed beside each
block.

## From

`feedback/2026-08-24-100427`, a core patch review on 2026-08-24. Two `core`
paths, four hints carried, two of them declared `extension` and `project`, and
the two `core` hints the paths name left in `omittedHints`. A session measured
the mirror the same day and it is worse. `feedback/2026-08-24-140340` reports
the same shape from the extension side in another checkout. `D-ANS-097` carries
both readings.

## Held by

- `HintsTest::aCoreBriefSpendsItsSlotsOnTheCoreHints`, the call the demand came
  from. Every hint the brief carries for the two core paths declares `core` or
  declares nothing, and `omittedHints` names the two it moved down.
- `HintsTest::anExtensionBriefSpendsItsSlotsOnTheExtensionHints`, the mirror,
  and the half a scope assertion cannot state. What the brief carries and what
  it names between them are what `typo3_hint_lookup` holds for those paths. So
  the tier moved a hint rather than lost one.
