---
id: D-KNW-168
title: The trusted properties are stated where a converter or a form field is written
date: 2026-10-21
status: open
coveredBy:
  - HintsTest::aConverterAndAFormViewHelperReachTheTrustedProperties
---

# D-KNW-168 — The trusted properties are stated where a converter or a form field is written

**`extbase-arguments` states that a form field's name decides what a request may
map. It reaches the paths of a type converter and of a form ViewHelper.
`tca-formengine` claims `Classes/Form/` instead of `/Form/`.**

## Evidence

- [`feedback/2026-10-21-153139-no-hint-says-trusted-properties`](../../feedback/archive/2026-10-21-153139-no-hint-says-trusted-properties-decide-whether.md).
  A fix for Forge #79571 changed `ObjectStorageConverter` to map a scalar to one
  child. Its own test passed, and a real request failed with "It is not allowed
  to map property". The brief for the converter path did not carry
  `extbase-arguments`.
- [`feedback/2026-10-21-153139-path-query-for-a-fluid-viewhelper`](../../feedback/archive/2026-10-21-153139-path-query-for-a-fluid-viewhelper-returns.md).
  `SelectViewHelper.php` reached `tca-formengine` and `content-element-shape`.
  `hints:probe` and the hint files showed why. `tca-formengine` declared the
  fragment `/Form/`, which `Classes/ViewHelpers/Form/` carries. "collection" in
  the task reached `content-element-shape`.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main` on 2026-10-21.
  `MvcPropertyMappingConfigurationService::generateTrustedPropertiesToken()`
  stores a name without `[]` as a scalar, and one with `[]` as a list.
  `modifyPropertyMappingConfiguration()` allows a child only below a list.
  `SelectViewHelper` appends `[]` only with `multiple` and then registers the
  name once per option. `PropertyMapper` throws 1355155913 for a child that is
  not allowed. The same on all four lines, so the statement has no bound.

## Decided

- The statement goes into `extbase-arguments`, beside the one about
  `allowProperties()`, rather than into a hint of its own. Both answer the same
  error message.
- `appliesTo` gains `Classes/ViewHelpers/Form/`,
  `Classes/Property/TypeConverter/`, "trusted properties" and "type converter".
  `ObjectStorage` stays out, because a model task names it as often as a request
  task does.
- `content-element-shape` keeps "collection". It is the word a content element
  task uses for repeatable items, and one report of a stray match is no
  threshold for the matcher.
- The `availableHints` index stays whole. The maintainer chose that on
  2026-10-21 against `D-ANS-128`, whose **Wrong if** this report does not meet.

## Wrong if

- A session changes a converter or a form field name, gets this hint, and the
  request still fails for a reason the trusted properties explain.
- An extension's FormEngine element lives outside `Classes/Form/`, and its path
  no longer reaches `tca-formengine`.

## Since then

A session finished change 96975 on 2026-10-22 and got this hint for
`SelectViewHelper.php`
([`feedback/2026-10-22-105711-the-trusted-properties-hint`](../../feedback/archive/2026-10-22-105711-the-trusted-properties-hint-stopped-a-mapping.md)).
It stopped a test that builds its own `PropertyMappingConfiguration` and could
not fail. The session then found `BlogPostEditingControllerTest` in ext:extbase,
which maps through real trusted properties. That file is on 13.4, 14.3 and main
and not on 12.4. So a name in the hint would bind it, for what one search found.
The hint stays, and the re-run brief for the same paths carries it. The test
above holds the keep. The first **Wrong if** did not fire.
