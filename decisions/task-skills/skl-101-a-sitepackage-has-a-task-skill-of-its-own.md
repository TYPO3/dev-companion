---
id: D-SKL-101
title: A sitepackage has a task skill of its own
date: 2026-10-26
status: open
coveredBy: []
---

# D-SKL-101 — A sitepackage has a task skill of its own

**`typo3-sitepackage-development` owns the creation of a sitepackage and the
change of its layout. It offers the get.typo3.org builder as one start, beside a
tree written by hand.**

## Evidence

- The first run of `SITE-10`, 2026-10-26, in a copy of `E-SITE` 14.3 outside
  this checkout. No skill named a sitepackage, so the session loaded none. It
  read seven core files to fill what the hints left open, and it worked the
  wiring out alone. That run is the baseline the new skill is measured against
  (`D-SKL-035`).
- [`feedback/2026-10-26-121101-no-skill-or-guide`](../../feedback/archive/2026-10-26-121101-no-skill-or-guide-carries-create-a-sitepackage.md)
  asked for a guide or a skill.
- [`feedback/2026-10-26-121124-what-worked`](../../feedback/archive/2026-10-26-121124-what-worked-welcome-setup-typoscript-override.md)
  names the hints that prevented an error. The skill routes to each of them by
  id.
- The maintainer decided on 2026-10-26 that the task needs a skill. A session
  had judged a checklist item enough and was overruled.
- The maintainer chose the scope and the start. The skill owns the creation and
  the layout. It offers the builder, whose schema the session fetches first,
  because the official tutorial starts there.

## Decided

- **Construction, so no checklist.** The hints and a rendered page per page
  layout answer what the skill would otherwise judge.
- **The builder's tree and the hints' tree are left to one convention per
  package**, which the session names to the user. Which of the two the skill
  prefers is open.
- **The wiring is the skill's last section**, ending on one rendered page per
  page layout.

## Assumed

- That a session with the skill reads no core source for the steps the baseline
  run had to read it for.

## Wrong if

- A run of `SITE-10` with the skill installed reads core source for the tree,
  the wiring or the page layout fallback, or skips a step of the wiring.
- A session offered both starts cannot finish the builder's one without the
  user's help beyond the base package and the author.
