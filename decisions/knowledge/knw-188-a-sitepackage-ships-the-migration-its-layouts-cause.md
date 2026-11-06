---
id: D-KNW-188
title: A sitepackage ships the migration its layouts cause
date: 2026-11-06
status: open
coveredBy:
  - HintsTest::aSitepackageThatMovesContentIsSentToTheUpgradeWizards
---

# D-KNW-188 — A sitepackage ships the migration its layouts cause

**The `sitepackage` intent names `upgrade-wizards` in an item that states its
own condition: the installation already holds content the change touches.**

## Evidence

- `feedback/archive/2026-10-30-200500-the-sitepackage-intent-never-says-to-ship-the.md`:
  a run moved seven pages by a script and shipped no PHP. The intent named no
  such step, while `upgrade-wizards` already answers the mechanism.

## Decided

- **A routing item rather than a new statement.** The rule is one sentence, and
  the mechanism stays in the hint.
- The item is conditional, because a greenfield package owes nothing here.

## Assumed

- That the conditional item costs a `SITE-11` run nothing, so `T-261029-bb2b`
  still measures what it set out to measure.

## Wrong if

- A `SITE-12` run reads the item, migrates its pages and still ships no wizard.
