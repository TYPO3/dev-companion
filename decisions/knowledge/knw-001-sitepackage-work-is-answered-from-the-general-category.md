---
id: D-KNW-001
title: Sitepackage work is answered from the General category
date: 2026-07-29
status: revoked
revokedBy: D-KNW-006
coveredBy: []
---

# D-KNW-001 — Sitepackage work is answered from the General category

**Sitepackage work answers from the always-selected General category. The task
text that asks for it names none of the technical domains the categories
mirror.**

The hint categories mirror the technical domains: PHP, TypoScript, Fluid,
Backend CSS, Backend TypeScript, General. A section of a website crosses all of
them at once. A TCA file, a data processor in TypoScript, a YAML route enhancer,
a Fluid template. The task text that asks for it names none of them, so a
domain-scoped hint is out of reach exactly at the moment of need.

## Decided

- `sitepackage-layout` and `frontend-records` go into `general.json`, whose
  category is always selected. A new category file would never match.
  `Domains::hintCategories()` returns a fixed list, and a category outside it
  loads and then falls to the filter.
- "sitepackage" and "content element" also became domain keywords for Fluid and
  TypoScript, and frontend markers besides. So such a task pulls the page render
  and site set hints too, and it does not get the backend's own CSS and
  TypeScript conventions.

## Assumed

- The noise this adds is smaller than the miss it removes. Two entries in the
  always-on category is the cost, paid by every query.

## Wrong if

- A backend-only task comes back with the sitepackage layout because it
  mentioned a content element. Or the General category grows until it is what
  every answer consists of. The fix in that case is a category for the audience
  rather than for the domain. That is a larger change than this one earns today.

## Revoked on 2026-08-02

Both halves fired. Five backend-only task texts that name a content element went
through `typo3_task_guide`, and two came back with `sitepackage-layout`. «Add a
TCA field to the content element in the backend» and «The backend preview of the
content element is broken in the page module». `bin/cli hints:probe` says how it
got there — `text only(150)`, not one `appliesTo` pattern matched. The hint runs
to 670 words against a corpus mean of 268. It describes the package from the
backend that administers it, so a backend query reads well against its body. On
the size, General holds 18 of 65 hints and supplied 35 of 54 matches over the
scenario prompts. 16 of 29 answers consist of it alone. `bin/cli hints:coverage`
prints that from here on, and fails on none of it, because the number to watch
is the growth.

## Revoked on 2026-08-02

The fix named above is not the one that answered it. An audience category would
have left the hit in place. The second **Decided** bullet made "content element"
a keyword of Fluid and TypoScript. So both are candidate categories for such a
task however the corpus sorts. A move of `sitepackage-layout` out of General
only moves it into one of them. What answered it was the exclusion already in
`ArchitectureHints::find()`, until now reached by the words "backend module"
alone. Nothing about it was ever about modules. It reads
`Domains::namesOnlyTheBackend()` now: backend markers present, no frontend
marker beside them. That is not `namesTheFrontend()` negated, where the backend
markers win. A task that names both halves asks for both; `SITE-05` is one, and
it keeps the layout. `SITE-08` holds the shape that failed.

## Since then

The second **Decided** bullet stood as it was while the first got its answer.
Such a brief still opened with `Domains: fluid, typoscript`. So the hint
exclusion was what kept that work out of the answer rather than the scope.
`D-KNW-006` closes it on 2026-08-02, and by the gate this entry's second
revocation wrote rather than by a deleted keyword. That had its measure and
costs `SKILL-04` both domains and a render question its one Fluid hint.
