---
id: D-GUI-031
title: Translation beside record localization is no label work
date: 2026-10-21
status: open
coveredBy:
  - HintsTest::translationBesideRecordLocalizationIsNoLabelWork
---

# D-GUI-031 — Translation beside record localization is no label work

**Where a task names record localization, such as an overlay, `l10n_mode` or
`sys_language_uid`, the word translation names neither the XLIFF domain nor the
label intent. The label intent also declares the language file paths, so a call
whose paths name none of them gets it `weak`.**

## Evidence

- [`feedback/2026-10-21-154706-task-guide-reads-translation`](../../feedback/archive/2026-10-21-154706-task-guide-reads-translation-as-xlf-labels-when.md).
  A triage of stale Extbase issues about translation and localization, with the
  path `typo3/sysext/extbase`. The brief carried the Language Files hints, the
  XLF checklist and `checkIntegrityXliff` and `normalizeXliff`. The issues were
  about overlays, `l10n_mode` and language fallback.
- Re-run on 2026-10-21 against the server as it was. `domains` was `xliff`
  alone, and `labels` was `strong` off the word "translation".
- TYPO3 uses the word for both things. `Domains` and `task-intents.json` listed
  it for labels only.

## Decided

- A list of record localization words in `Domains`. Where the task carries one,
  `Domains::withoutRecordTranslation()` takes every translat… word out before
  both the domain and the intent match. A word for a label itself, such as
  `xlf` or `label`, still names the domain.
- The label intent declares `Resources/Private/Language/` and `.xlf` as its
  paths, the mechanism `D-GUI-028` built. That covers a task that says
  "translation" with no localization word and a path outside the language
  files.
- No domain for record localization. The hints about overlays sit under PHP,
  TCA and persistence, and the task words already reach them.

## Assumed

- That the list catches how a session writes about records. It is the words of
  one feedback and of the core's own field names.

## Wrong if

- A session adds a label to a localized record's form, writes "overlay" in its
  task and names no XLF path. The brief then lacks the label items it needed.
- A session asks about record translation in words the list does not carry and
  gets the XLF brief again.
