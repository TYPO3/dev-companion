---
id: D-GUI-028
title: A word match is held to the paths its work touches
date: 2026-10-05
status: open
coveredBy:
  - HintsTest::aSpeedUpUnderReviewIsRoutedToTheTimingPage
  - HintsTest::aWordMatchWhoseFilesThePathsDoNotNameIsConditional
  - HintsTest::everyIntentThatCanComeBackWeakStatesItsCondition
---

# D-GUI-028 — A word match is held to the paths its work touches

**An intent may declare `paths`, the fragments of the files its work touches.
Where the caller names paths and none of them carries one, a word match of that
intent is `weak`. `tca-field` declares `Configuration/TCA/` and
`ext_tables.sql`.**

## Evidence

- `feedback/archive/2026-10-05-094210-tcadatagenerator-paths-trigger-the-tca-field.md`.
  A review of a styleguide generator patch got `tca-field` as `strong`, with
  seven items about columns and showitem. No TCA file was in the paths.
- Re-run on 2026-10-05. The match came from "TCA" in the task text and not from
  `TcaDataGenerator` in the paths, as the feedback assumed. The same call
  without the word matched no field intent.

## Decided

- `weak` rather than gone. A `weak` intent's items carry their condition. A
  caller that adds a field may name the model class before the TCA file exists,
  and the intent must still reach that caller.
- A caller that states the intent through `changeType` keeps the match. So does
  a call with no paths, which gives nothing to hold the word to.
- Only `tca-field` declares paths so far. A second intent gets them when a
  session reports the same shape there.

## Assumed

- That a caller names the files of the change. A brief from a partial list is as
  weak as the list.

## Wrong if

- A session adds a field, names only files outside the TCA, and skips the field
  items because they came back `weak`.

## Since then

A second report came the same day, which is what the third decision waited for.
A review of a speed-up got `installation-setup` from "typo3 setup" and
`browser-tests` from "e2e" in its task, with the items of both.
`installation-setup` now declares `config/system/`, `.ddev/` and
`composer.json`, and `browser-tests` declares `playwright`, `.spec.ts` and
`/Acceptance/`. `browser-tests` also gained the condition it lacked, and a test
holds every intent that can come back weak to one.
