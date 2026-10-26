---
id: D-KNW-172
title: The sitepackage hints answer a project without the core theme
date: 2026-10-26
status: open
coveredBy:
  - HintsTest::aPageTemplateIsToldWhereItsAreasAndTitleComeFrom
  - HintsTest::aSitepackageWithoutTheCoreThemeIsAnsweredOnItsOwnTerms
---

# D-KNW-172 — The sitepackage hints answer a project without the core theme

**The sitepackage hints name the package behind each object, and answer a
project without `theme_camino` with a tree of their own.**

## Evidence

The first run of `SITE-10` on 2026-10-26, in `E-SITE` 14.3 without
`theme_camino`, filed three feedbacks about it:

- [`feedback/2026-10-26-121054-sitepackage-checklist`](../../feedback/archive/2026-10-26-121054-sitepackage-checklist-says-reference-lib.md).
  The `sitepackage` checklist and `sitepackage-typoscript-reference` said to
  reach `lib.fluidPage` with `=<`. Only the camino set defines that object,
  measured in `.checkouts/14.3` and `.checkouts/main`. The session found no
  definition in `vendor/typo3` and wrote `PAGEVIEW` itself.
- [`feedback/2026-10-26-121108-page-layout-hints`](../../feedback/archive/2026-10-26-121108-page-layout-hints-omit-what-a-page-with-no.md).
  The session read `PageLayoutResolver` and `DefaultDataProvider` to learn what
  a page with no selection renders. Both checkouts from 13.4 on resolve it
  through `backend_layout_next_level` to `default`, with one column `main` at
  colPos 0. `BackendLayout` refuses `__` in an identifier on every covered line.
- [`feedback/2026-10-26-121117-sitepackage-layout`](../../feedback/archive/2026-10-26-121117-sitepackage-layout-sends-the-reader-to-theme.md).
  `sitepackage-layout` sent the reader to `theme_camino`, which the project did
  not have, and gave no tree of its own.

## Decided

- **Step 1 of the ladder, a wrong answer, and step 3, missing knowledge.** The
  checklist item that the `sitepackage` intent gained the same day carried the
  error from the hint it routes to. Both now bind `lib.fluidPage` to the camino
  set.
- **The tree stands in the layout hint**, beside the pointer to the theme. A
  project without the theme is the common case for a new sitepackage.
- **The fallback stands in `sitepackage-backend-layouts`**, bound from 13, where
  `PageLayoutResolver` arrived.
- **Rejected: drop the checklist item where no active package defines
  `lib.fluidPage`.** The brief does not read the installation for its checklist,
  and a conditional item says the same in one sentence.

## Assumed

- That the tree is the one the other sitepackage hints already state, so a
  session that follows it meets no contradiction.

## Wrong if

- A session on a project without the theme still reads core source for the page
  layout fallback or for the tree.
- A session with the camino set as a dependency drops `lib.fluidPage` because of
  the new statement.

## Since then

Three runs with `typo3-sitepackage-development` on 2026-10-26 still read core
source for the page layouts. They read `PageViewContentObject` for how an
identifier becomes a template name, and `GridColumn` for the keys of a layout
file. One asked how its own `default` stands beside the core's.
`sitepackage-backend-layouts` now answers all three. The name rule was read in
`.checkouts/13.4`, where PAGEVIEW upper-cases the first letter, and in
`.checkouts/14.3` and `main`, where Fluid 5 tries the exact name first.

A fourth question had its answer already. `page-variables-and-processors` says
that PAGEVIEW hands the set's settings to the template, and the run that asked
never fetched it, because neither the skill nor the brief named it. The skill
now names it, and the statement carries the dotted path as an example.

## Since then

The sixth run asked three more things. `page-content-areas` now says that 13
needs the `page-content` processor, and that in 14 the same processor beside
PAGEVIEW replaces what PAGEVIEW assigned. Both are read in `.checkouts/13.4` and
`14.3`. `page-variables-and-processors` names the site and the language title,
and a language does not inherit the site's. `sitepackage-start` places each
directory of the builder's tree in the hints' tree, rather than list the
builder's defects of the day.
