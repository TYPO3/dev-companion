---
id: D-KNW-137
title: A relaunch is a kind of work rather than a hint beside one
date: 2026-09-01
status: open
readings:
  - 2026-09-02
coveredBy: []
---

# D-KNW-137 — A relaunch is a kind of work rather than a hint beside one

**Carrying an existing body of content onto TYPO3 is a task intent whose
checklist states what the modelling owes, and no hint repeats those statements
beside it.**

The session that reported this had already called `typo3_task_guide`, so the
brief is where the answer reaches it; a hint would be the same four sentences in
a second place, found by a query nobody made.

## Evidence

- The task text of 2026-08-31 named the migration —"Migrate content from a
  legacy Drupal 6 site" — and matched `installation-setup`, `tca-field` and
  `site-setting` and nothing about content modelling. Run through the matcher
  again on 2026-09-01 with the intent in place, it matches `content-relaunch`
  strongly, and that intent stands before `installation-setup` in the catalog,
  which is the order the brief names the skills in.
- The session called `typo3_task_guide` twice, both in its first hour, and made
  no hint lookup about the modelling in five more. The brief is the surface it
  was actually on.
- The one statement in the checklist that is a claim about TYPO3 was verified on
  both `.checkouts/main` and `.checkouts/13.4`: the `html` element's template is
  `<f:format.raw>{data.bodytext}</f:format.raw>`, so what an import stored is
  what a visitor is served.

## Decided

- The intent carries the statements: the model before the crawler, the recurring
  shapes as the elements, the `html` element as no migration target, and the
  count as an input.
- No `legacy-content-relaunch` hint. The same sentences in `knowledge/hints/`
  would be the second place one fact lives, which is what the corpus is written
  against.
- The intent sits before `installation-setup`, because what the feedback reports
  is an order rather than an absence: both workflows existed and the scaffolding
  was named first.

## Assumed

- That a relaunch arrives through `typo3_task_guide`. A session that never calls
  it reaches none of this, and no hint answers the symptom either.

## Wrong if

- A session reports the modelling gap having called no task guide, which would
  put the statements where a symptom finds them after all.
- The needles reach a task that is upgrading code inside an installation rather
  than carrying content into one.
  `KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther` holds one
  brief per kind, and the weak needles — legacy, migrate, drupal — are the ones
  to watch.
