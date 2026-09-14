---
id: D-KNW-006
title: 'A backend word adds no domain to a backend-only task'
date: 2026-08-02
status: confirmed
coveredBy:
  - HintsTest::aBackendTaskIsNotCalledFluidAndTypoScriptWork
---

# D-KNW-006 — A backend word adds no domain to a backend-only task

**"content element", "sitepackage" and "site package" stay keywords of Fluid and
TypoScript. They no longer add those two domains where the task names only the
backend.**

`D-KNW-001` made "content element" a keyword of both. The exclusion in
`ArchitectureHints::find()` answered its hint half on 2026-08-02; the domain
half stood open. So a task about one TCA field still had its brief opened with
`Domains: fluid, typoscript` and got an answer out of both categories.

## Evidence

- The keyword deleted from `FLUID` and `TYPOSCRIPT` had its measure over seven
  task texts. It answers `SITE-08`: its three backend-only prompts drop to
  `php`. «Add a TCA field to the content element in the backend» gains
  `tca-formengine`, which the task actually named.
- It costs the other two. `SKILL-04`'s prompt names no half at all and loses
  both domains. «The content element renders nothing on the page» no longer
  reaches `frontend-page-rendering`, the one Fluid hint either of them gets.
  `SITE-05` stays the same either way, because "site package" carries the two
  domains on its own.
- The gate that separates them exists: `namesOnlyTheBackend()`, written for the
  hint half of `D-KNW-001`. Under it the three backend-only prompts drop to
  `php` and `SITE-05`, `SKILL-04` and the render question stay the same.

## Decided

- The suppression is a list of its own, `ADMINISTERED_FROM_THE_BACKEND`, and it
  reads `namesBackendModule() || namesOnlyTheBackend()`. The first is what
  "sitepackage" already had as its gate and stays in reach. A backend module in
  a site package names the website half in its own owner, so backend-only is
  false for it.
- The keyword stays in both lists. The word tells the matcher which thing the
  task is about, and a task that names neither half is the case it exists for.

## Assumed

- A task that names the backend and never the website asks about the backend. A
  backend preview template is the case where that is thin, since it is Fluid.
  But no Fluid hint reached that query before this change either.

## Wrong if

- A task about a content element's build or render comes back as `php` because
  it happened to say "backend form" as well. Or the two domains are out of reach
  for a content element at all outside a text that says "sitepackage" or
  "frontend".

## Confirmed on 2026-08-23

Neither half of the **Wrong if** holds, and the second has a measure rather than
an argument. Four forms of a content element task all select fluid and
typoscript. So the two domains are in reach without a text that says sitepackage
or frontend.

The first half has cover from a direction that did not exist on this entry's
day. A backend preview selects `php` alone, as this entry decided, and the first
hint it returns is a Fluid one. A curated phrase crosses the domain gate under
`D-ANS-084`.
