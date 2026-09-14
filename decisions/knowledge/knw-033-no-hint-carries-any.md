---
id: D-KNW-033
title: 'No hint carries `any`'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions
  - HintsTest::hintsAreGroupedByDomainWithPhpFirst
  - HintsTest::nothingIsTaggedAnyWithoutSayingWhy
  - ScopeTest::aCoreContributorOnFrontendLosesTheBackendUiSections
---

# D-KNW-033 — No hint carries `any`

**The 38 hints that carried `any` name the domains they really answer from. The
withhold rule reads a hint's domains rather than the query's.**

`any` was `general.json` under a new name in `D-KNW-029`, and `D-KNW-032` showed
that a split does not dissolve it. The share stood at 63% over 38 of 120 hints
because a split inherits the tag of its origin.

## Evidence

- Three of 41 scenario answers moved, and a dump of the whole corpus of hint
  titles and prompts before and after says so. `CORE-03`, a commit message came
  back from review, loses `frontend-records`, which the always-selected bucket
  had handed it. `SKILL-02` gains `sitepackage-initial-content`, `SKILL-05`
  gains `core-tests`.
- Nothing lost its answer. The same ten prompts reach nothing as before, every
  hint's own title still reaches it, and the `any` share is 0 of 120.
- Two domains had detection and nowhere to go, which is half of why
  `general.json` held what it did: `xliff` and `docs`. Labels, the translation
  domain, label retirement and the changelog are theirs.
- Four vocabulary gaps showed up as answers that got worse, each with its fix
  where it belonged. `icon` and `upgrade` were not PHP words, `translate` was
  not an XLIFF word beside `translation`, and `backend layout` was neither Fluid
  nor TypoScript. `any` had carried each rather than the vocabulary.

## Decided

- A domain tag is what a query selects by, so a hint carries every domain a
  caller could arrive from and no more. `content-elements` is PHP, Fluid and
  TypoScript; `preview-record-variable` is Fluid alone.
- The first tag is the heading, so it is the domain the hint is most about.
  `extension-asset-build` leads with PHP rather than CSS: it is a build setup,
  not a backend design-system rule, and the heading is what a reader believes.
- The frontend withhold drops a hint whose domains are `css` and `typescript`
  and nothing else, rather than removes those two domains from the selection.
  Those two tags mean the backend's own design system; a hint that carries a
  third domain is not that hint. A sitepackage's asset build and a contrast scan
  of a site come in exactly the same words and used to fall with it.
- `any` stays a tag nobody uses. `HintsTest::nothingIsTaggedAnyWithoutSayingWhy`
  fails on the first new one, because a hint every query selects is a decision
  rather than a tag picked at the keyboard.

## Assumed

- The tags are right where nothing measured them. Three answers moved, so 37 of
  the 38 were only ever in reach through the bucket. What they really answer
  from is a judgement per hint that the scenario prompts do not all exercise.
- Losing `frontend-records` from a commit-message review is an improvement
  rather than a regression. Nothing in that prompt is about a render of records.

## Wrong if

- A question comes back with nothing where it used to have an answer, and the
  hint that would have answered it is one domain away. That is a tag too narrow,
  and the fix is the tag rather than a new `any`.
- A hint carries four or five domains so that a query finds it, which is `any`
  in a longer form and the failure this replaced.
- The withhold lets a backend Sass convention through to a theme extension,
  which would mean the domains-subset test is the wrong rule for it.
