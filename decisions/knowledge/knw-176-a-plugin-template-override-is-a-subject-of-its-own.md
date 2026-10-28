---
id: D-KNW-176
title: A plugin template override is a subject of its own
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::overridingAPluginsTemplatesIsAnsweredWithItsOwnViewRoots
---

# D-KNW-176 — A plugin template override is a subject of its own

**The hints state how a sitepackage overrides an Extbase plugin's templates
through `plugin.tx_<key>.view`, and which roots the sitepackage collisions
cover.**

## Evidence

- [`feedback/2026-10-28-060232`](../../feedback/archive/2026-10-28-060232-no-subject-covers-overriding-a-third-party.md)
  came from a 14.3 sitepackage with indexed_search. The session read the
  collision warnings of `sitepackage-templates` as a rule about every root. It
  styled the plugin with CSS and hid markup a visitor needed.
- Step 1a and step 4. `bin/cli hints:probe` on the feedback's own query returned
  `fluid-templates`, `sitepackage-layout` and a breaking-change hint on text
  alone. No hint below `knowledge/` named `plugin.tx_<key>.view`. The partial
  sentence of `sitepackage-templates` said "an extension adding
  partialRootPaths" and did not say which object's.
- `FrontendConfigurationManager::getPluginConfiguration()` merges
  `plugin.tx_<ext>` with `plugin.tx_<ext>_<plugin>` in `.checkouts/14.3` and
  `.checkouts/13.4` — `D-ANS-018`.
- `ActionController::addDefaultPathToPaths()` puts the extension's own
  `EXT:` path in front of each configured list in `.checkouts/13.4`,
  `.checkouts/14.3` and `.checkouts/main`. `.checkouts/12.4` hands a configured
  list to `setTemplateRootPaths()` as it stands. That is the `since: 13`
  boundary.
- indexed_search fills key 10 from its own constant in all four checkouts. Its
  `Partials/Form` wraps the advanced search link in
  `settings.displayAdvancedSearchLink`, set to 1 in its setup on all four. So
  the feedback's "only the template decides whether that link is written" does
  not hold, and the hint names the setting as the cheaper lever.
- `LocalizationUtility::translate()` merges `plugin.tx_<ext>._LOCAL_LANG`
  over the language file in `.checkouts/12.4`, `13.4` and `14.3`. On 14.3 it
  derives the extension from a `<ext>.messages` domain, which is what
  indexed_search's `f:translate` calls pass.

## Decided

- Closed on the spot under `D-FBK-052`. The run read the checkouts and holds the
  evidence, and no contract moves.
- A new hint `plugin-template-override` in `knowledge/hints/fluid.json` carries
  the roots, the per-file fallback, the trade and what CSS cannot reach.
  `sitepackage-templates` gains one sentence that scopes its collisions to
  `lib.contentElement` and PAGEVIEW and points at the new hint.
- One session reported it, so nothing here goes beyond the hint.

## Assumed

- That a session which asks how to restyle a plugin uses words the `appliesTo`
  reaches. The probe reaches it on the feedback's query and on "make the
  indexed_search plugin output match the site design".

## Wrong if

- A session restyles a third-party plugin with CSS where a setting or a
  template override was the lever, after this hint was in its answer. Then the
  gap is delivery rather than knowledge.
- A plugin's own view roots turn out to collide with `lib.contentElement` or
  PAGEVIEW in some installation. Then "these roots belong to that plugin alone"
  is too strong.
