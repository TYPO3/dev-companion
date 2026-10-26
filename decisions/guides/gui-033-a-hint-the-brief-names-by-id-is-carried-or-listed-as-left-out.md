---
id: D-GUI-033
title: A hint the brief names by id is carried or listed as left out
date: 2026-10-26
status: open
coveredBy:
  - HintsTest::aHintTheBriefNamesByIdIsCarriedOrListedAsLeftOut
---

# D-GUI-033 — A hint the brief names by id is carried or listed as left out

**Every hint that the checklist or the tools of a brief's intents name as
`id=<hint>` stands in `hints` or in `omittedHints`.**

## Evidence

- [`feedback/2026-10-26-134232-task-guide-omittedhints`](../../feedback/archive/2026-10-26-134232-task-guide-omittedhints-left-out-five-hint-ids.md).
  A brief for a new sitepackage named three hints by id in its checklist and
  listed none of them, because no path reached them. `skills/base.md` calls
  `omittedHints` the list the caller still owes. A caller that trusts it fetches
  none of the three, and two of them prevented a defect in that run.

## Decided

- **The ids come from the text of the intents the brief carries**, the same
  `id=` form the checklist already uses. A named id that is no hint on the
  target version stays out.
- **The skill's own ids stay the skill's.** The brief does not read skill
  bodies, and the skill names its ids where they are needed.

## Wrong if

- A brief lists so many named hints as left out that a caller stops reading
  `omittedHints`.
