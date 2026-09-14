---
id: R-KNW-025
title: 'The two site-local settings sources carry their precedence'
status: held
heldBy:
  - HintsTest::siteLocalSettingsSourcesAreAnsweredWithTheirPrecedence
---

# R-KNW-025 — The two site-local settings sources carry their precedence

**The answer about the two site-local settings sources carries their
precedence.**

`config/sites/<identifier>/settings.yaml` replaces the inline `settings:` block
of `config.yaml` rather than merges with it, and the backend editor persists to
the former.

## From

One setting added in the backend dropped every value a sitepackage seed carried
inline, in silence (2026-07-30).
