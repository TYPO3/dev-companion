---
id: R-KNW-010
title: 'An answer says which half of TYPO3 it is for'
status: held
heldBy:
  - HintsTest::theIconHintSaysWhichHalfOfTypo3ItIsAbout
  - IconLookupTest::everyAnswerSaysTheIdentifiersAreTheBackendRegistrys
  - IconLookupTest::theRoutingEntrySendsCallersThereForBackendWorkOnly
---

# R-KNW-010 — An answer says which half of TYPO3 it is for

**An answer says where it works when it works only in half of TYPO3.**

The icon identifiers are the backend registry's, so every `typo3_icon_lookup`
answer carries that sentence, in the text and in the data. It does not carry it
only where the query happens to sound like frontend work. A tool that gets a
query and not a task cannot tell the two apart. The hint that describes the same
registry states the same boundary. A list of backend APIs reads as "here is how
you render an icon" to whoever writes a page template.

## From

Backend icon identifiers about to go into a frontend template, stopped by the
user (2026-07-29). The words were "die icons welche du findest sind übrigens nur
für das backend gedacht, nicht für das frontend". Re-reported for the hint after
the tool half had shipped.
