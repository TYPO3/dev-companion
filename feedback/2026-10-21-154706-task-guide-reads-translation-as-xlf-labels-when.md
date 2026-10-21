---
date: 2026-10-21T15:47:06+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# task_guide reads "translation" as XLF labels when the task is about record localization

## Observation

Task: list stale Forge issues about Extbase and translation. The issues are about record localization: overlays, l10n_mode, sys_language_uid, language fallback. task_guide matched the domain "xliff" and the intent "labels" with strong confidence. It returned 13 Language Files hints, XLF checklist items and the suites checkIntegrityXliff and normalizeXliff. None of that applies to Extbase record localization or to a triage. The answer did correctly name the skill typo3-core-issue-triage and gave the triage checklist. In TYPO3, "translation" means two things: labels (XLF) and records (localization, overlays). The guide picked the wrong one even with the path typo3/sysext/extbase. Separately, nextTools says: typo3_forge_lookup with open="oldest" or open="stale". The parameter is called backlog, not open.

## Query

typo3_task_guide task="Triage the Forge backlog: list stale open issues about Extbase and translation/localization" changeType="triage" paths=["typo3/sysext/extbase"]

## Suggestion

When the task says translation or localization together with extbase, records, overlay or a sysext path that is not a Language file, prefer the record localization domain. Do not attach XLF checks to a triage that writes no file. Fix the nextTools text: use backlog="oldest"/"stale", not open=.
