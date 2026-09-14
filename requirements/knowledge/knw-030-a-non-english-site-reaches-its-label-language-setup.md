---
id: R-KNW-030
title: 'A non-English site reaches its label-language setup'
status: held
heldBy:
  - HintsTest::aGermanSiteTaskReachesItsLabelLanguageSetup
  - HintsTest::languagePackActivationUsesTheConfigurationOfTheTargetBranch
---

# R-KNW-030 — A non-English site reaches its label-language setup

**A non-English site setup reaches the complete label-language chain.**

The site language selects the pack key outright. The configuration key of the
target branch activates the pack before the update. Literal component fallbacks
such as the EXT:form submit button stand apart from translatable labels.

## From

A German-only site rendered core validation messages and the form submit button
in English, in silence (2026-07-30).
