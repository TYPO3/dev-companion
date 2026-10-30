---
id: D-KNW-183
title: A decision a new site makes is handed to the user with its options
date: 2026-10-30
status: open
coveredBy:
  - HintsTest::aNewSitesOpenDecisionsAreHandedToTheUser
  - HintsTest::aNewSitesOpenQuestionReachesItsHint
---

# D-KNW-183 — A decision a new site makes is handed to the user with its options

**A hint states the options of a decision a new site makes and what each one
costs. The `sitepackage` intent tells the session to put that decision to the
user rather than settle it alone.**

## Evidence

- `feedback/2026-10-29-105200-where-a-requirement-is-open-the-model-settles.md`:
  two runs of 2026-10-29 built a sitepackage from one design and one sentence.
  Each settled eight open questions alone, and the two runs did not settle them
  alike. Neither asked the user.
- Read on 2026-10-30 against `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `LanguageAspectFactory` appends `pageNotFound` to the fallback chain of every
  site language. So `fallbacks` decides an untranslated page and `fallbackType`
  decides the records on a translated one, on all four lines.
- Same read: the EXT:form mail templates declare the `SystemEmail` layout, the
  XML sitemap filters by doktype and `no_index` and never by `nav_hide`, and no
  core class processes an `index_config` record. `form:cleanup:uploads` exists
  on `14.3` and `main` and on neither LTS line.
- `bin/cli hints:probe` reached no hint for any of the six subjects before this
  change.

## Decided

- Five hints state the options: `site-language-fallback`,
  `page-group-container`, `form-mail-layout`, `form-data-retention` and
  `site-search`. `content-model-vs-markup` carries the statement on an
  accessible variant.
- Each hint states what TYPO3 does and leaves the choice to the site owner.
  What Easy Read requires and how long a site keeps personal data are named as
  outside TYPO3.
- One checklist item of the `sitepackage` intent routes all five and asks the
  session to hand the decision over.
- `site-search` routes alternatives to `typo3_ter_lookup` and names no package
  outside the core. The maintainer chose that on 2026-10-30.

## Assumed

- That a session follows a checklist item that tells it to ask. The routes
  worked in both runs. Whether the step after a route works is what
  `T-261029-bb2b` measures.

## Wrong if

- A forward run of a greenfield case reaches the checklist item and still
  settles one of these decisions without asking the user.
