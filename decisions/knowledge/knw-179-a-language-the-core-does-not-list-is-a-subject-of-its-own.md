---
id: D-KNW-179
title: A language the core does not list is a subject of its own
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aLanguageTheCoreDoesNotListIsToldWhereItIsRegistered
---

# D-KNW-179 — A language the core does not list is a subject of its own

**The hints state where a site language that `Locales` does not list is
registered, and what else that language needs.**

That is the path, the configuration API that does not write it, and the
operating system locale.

## Evidence

- [`feedback/2026-10-28-070634`](../../feedback/archive/2026-10-28-070634-nothing-covers-a-site-language-typo3-ships.md)
  came from a 14.3 sitepackage that added Swahili. `site-label-language` sent
  the session to `typo3 language:update`, which refuses an iso code the core
  does not list.
- Step 1a and step 4. `bin/cli hints:probe` on the feedback's own query returned
  `site-label-language`, `plugin-template-override` and `language-files`. No
  hint below `knowledge/` named `SYS/localization`. The activation sentence of
  `site-label-language` did not say that it assumes a listed language.
- `Locales::__construct()` merges `SYS/localization/locales/user` into
  `$languages` in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `LanguagePackService::languagePackDownload()` throws "Language iso code
  <iso> not available or active" unless the iso code is in that list and
  active, on the same four.
- `ConfigurationManager::isValidLocalConfigurationPath()` accepts a path on
  `allowedSettingsPaths` or in `DefaultConfiguration.php`. Neither carries
  `SYS/localization` on any of the four, so `setLocalConfigurationValueByPath()`
  returns `false`. `ConfigurationSetCommand` exists from `.checkouts/14.3` on
  and reports that `false` as "Could not set" with exit code 1. That is the
  `since: 14` bound, and it corrects the feedback's "without saying so" for the
  command.
- `SiteResolver` calls `Locales::setSystemLocaleFromSiteLanguage()` on every
  frontend request, and `Locales::setLocale()` logs `Locale "<locale>" not
  found.` as an error, on all four.
- The feedback says that `sw` alone leaves every page of the language at
  not-found and that `sw_KE` has to be registered beside it. The code read does
  not support that. `Locales::isValidLanguageKey()` strips the region and
  accepts `sw_KE` once `sw` is listed, on all four. The hint states that, and
  nothing about a not-found page.

## Decided

- Closed on the spot under `D-FBK-052`. The run read the checkouts and holds the
  evidence, and no contract moves.
- A new hint `user-defined-locale` in `knowledge/hints/labels.json` carries the
  registration, the silent configuration API and the operating system locale.
  `site-label-language` gains one sentence that points at it.
- One session reported it, so nothing here goes beyond the hint.

## Assumed

- That a session which adds such a language asks in words the `appliesTo`
  reaches. The probe ranks the hint second on the feedback's query, behind
  `site-label-language`, which now names it.

## Wrong if

- A run shows a page of a language registered as `sw` alone answering
  not-found, as the feedback reports. Then something outside `Locales` checks
  the regional locale, and the region sentence is wrong.
- A session follows `site-label-language` into `language:update` for an unlisted
  language after this hint was in its answer. Then the gap is delivery rather
  than knowledge.
