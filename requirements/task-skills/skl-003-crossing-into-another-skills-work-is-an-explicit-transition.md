---
id: R-SKL-003
title: "Crossing into another skill's work is an explicit transition"
status: held
restsOn: [D-EVI-002]
heldBy:
  - SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition
---

# R-SKL-003 — Crossing into another skill's work is an explicit transition

**A task skill that crosses into work another skill owns performs an explicit
transition.**

It names the verified stop point, stops before it edits the new owner's files,
and activates that owner. It carries forward only the scope and verified
behavior the next workflow needs.

Backend-module documentation belongs to the extension that contains the
functionality, not to the project around it.

## From

`EXT-04`, where the backend-module skill stayed active through edits of the
project README and never activated the documentation skill (2026-07-30).

## Held by

- `SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition`,
  `SKILL-07`. That a session performs the transition is not guarded, and will
  not be; see `D-EVI-002`.
