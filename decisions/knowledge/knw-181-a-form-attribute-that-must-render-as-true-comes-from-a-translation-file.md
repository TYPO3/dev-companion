---
id: D-KNW-181
title: A form attribute that must render as true comes from a translation file
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aFormAttributeThatMustRenderAsTrueIsToldWhereTheStringComesFrom
---

# D-KNW-181 — A form attribute that must render as true comes from a translation file

**The `form-framework` hint states where a form attribute that must render as
the string true comes from, and when a changed definition renders.**

## Evidence

- [`feedback/2026-10-28-085931`](../../feedback/archive/2026-10-28-085931-what-ext-form-accepts-and-then-ignores.md)
  carried two notes that `D-KNW-177` left open, because both needed an
  installation.
- `YamlSource::load()` returns
  `ArrayUtility::convertBooleanStringsToBooleanRecursive()` of the file in
  `.checkouts/12.4`, `13.4`, `14.3` and `main`. So `aria-hidden: 'true'` reaches
  the element as the boolean `true`. The database storage of 14 does not go
  through `YamlSource`, and nobody rendered from it.
- `TagBuilder::addAttribute()` in Fluid 4.6.1 and 5.3.1 renders `true` as the
  attribute name and removes `false`. Fluid 2.15.0 casts the value to a string.
- The `E-SITE` environments rendered a `Text` element from
  `EXT:acme_events/Resources/Private/Forms/`. On 12.4.45 both the boolean and
  the quoted string rendered `aria-hidden="1"` and `data-off=""`. On 13.4.33,
  14.3.5 and `main` both rendered `aria-hidden="aria-hidden"`, and `false`
  dropped out.
- A `translationFiles` entry `element.tstring.properties.aria-hidden` with the
  source `true` rendered `aria-hidden="true"` in all four environments.
- In all four environments a changed definition kept its old label until a
  flush. The first, second and third request after `typo3 cache:flush` showed
  the new one. So did the first request after `cache:flush --group pages`. A
  submit of the page rendered before the change, sent after the flush, rendered
  the new definition on 14.3.

## Decided

- Closed on the spot under `D-FBK-052`. The environments and the checkouts hold
  the evidence, and no contract moves.
- The conversion holds on every line and carries no bound. The rendering of a
  boolean carries `until: 12` and `since: 13`, the Fluid 2 boundary.
- The feedback's first request that still showed the old definition did not
  reproduce. The hint says where an old definition after a flush comes from
  rather than state the observation.

## Assumed

- That the feedback's session flushed through the same caches as
  `typo3 cache:flush`. The backend toolbar flushes the same groups.

## Wrong if

- A request after a completed flush renders the previous definition in an
  installation with no cache in front of TYPO3. Then the last statement is
  wrong, and a cache this read did not reach answers.
- A core release drops the boolean conversion from `YamlSource`. Then the first
  statement needs an `until`.
