---
id: D-GUI-035
title: A brief opens on the hint its confirmed intent names first
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aBriefOpensOnTheHintItsConfirmedIntentNames
---

# D-GUI-035 — A brief opens on the hint its confirmed intent names first

**A brief carries the first hint each confirmed intent names as `id=<hint>`
ahead of the hints the matcher ranked.**

## Evidence

- The contract reading of 2026-10-28 called `typo3_task_guide` with the prompts
  of `EXT-08` and `SITE-09`. The `EXT-08` brief carried `backend-modules` and
  listed `events-extension-points` only as omitted, on a task about a mail
  listener. The `SITE-09` brief carried `sitepackage-layout` and `site-sets` and
  omitted `site-set-settings`, the subject of the case. Both checklists named
  the right id.
- The matcher ranks on the words of the task, and those name the subject of the
  work: a mail, a footer. The intent is what recognized the work, and its
  checklist names the hint for it.

## Decided

- **One hint per confirmed intent, the first it names.** A trial that lifted
  every named hint filled all four slots of a sitepackage brief, because the
  `sitepackage` intent names six. That pushed out `tsconfig`, the hint the
  caller's own path reached.
- A weak intent lifts nothing. Its condition says the work may not be there.
- A lead hint the paths do not reach comes in by id. A lead an earlier group of
  paths already carries does not come in a second time.
- The tier `D-ANS-097` set still applies after the lift. A lead declared for
  another repository moves below the hints that bind.

## Assumed

- That an intent names its subject first. `content-element` names `extbase`
  first, because its first item is the decision whether the element needs
  Extbase. That brief now carries `extbase` in place of `sitepackage-templates`.

## Wrong if

- A brief opens on a lead hint that a run of the same case skips as off the
  subject. The order of the ids in that intent is then the defect, or the first
  id is the wrong measure of the lead.
