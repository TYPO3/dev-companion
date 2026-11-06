---
id: D-KNW-187
title: The describe answer carries the caveat the catalog holds
date: 2026-11-06
status: open
coveredBy:
  - ExtensionTest::theCatalogsCaveatReachesTheAnswerAboutThatExtension
---

# D-KNW-187 — The describe answer carries the caveat the catalog holds

**`typo3_extension_describe` answers `catalogCaveat` for an extension the
reference catalog warns about. `sitepackage-layout` keeps its wording.**

## Evidence

- `feedback/archive/2026-10-30-164400-the-statement-that-forbids-depending-on-theme.md`:
  a session called the tool for `theme_camino` three times, and no answer held
  the caveat. Only `typo3_reference_list` read that field.
- `SITE-12` records three pairs. The first pair's design and content disagreed,
  and that run broke the statement. In the two coherent pairs the run with this
  server left the theme out of its package.

## Decided

- **A route rather than a heavier statement.** The statement held wherever the
  task was coherent, so its wording is no lever yet.

## Assumed

- That one run in three is the incoherent task and not the rate. `D-EVI-013`
  asks for three runs per case, and these are one each.

## Wrong if

- A coherent run reads the statement and the caveat and still requires the
  theme. Then the weight of the statement is the defect.
