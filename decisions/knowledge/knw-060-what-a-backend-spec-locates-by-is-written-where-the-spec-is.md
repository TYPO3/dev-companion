---
id: D-KNW-060
title: What a backend spec locates by is written where the spec is
date: 2026-08-04
status: open
---

# D-KNW-060 — What a backend spec locates by is written where the spec is

**The markup a Playwright spec addresses the backend through belongs to the
`browser-tests` hint and to the Playwright document. It belongs to no catalog
and no skill.**

A session that wrote four backend specs found nothing with any of its locators.
A backend module renders inside an iframe and nothing in this corpus says so.

## Evidence

- `feedback/2026-08-04-175916`: four specs failed for that one reason, and what
  established the DOM was a throwaway probe spec and three extra Playwright
  runs. What it had to discover was `id="typo3-contentIframe"`,
  `name="list_frame"`, and the tile `.t3-page-ce` with
  `id="element-tt_content-<uid>"`. Then the preview body `.t3-page-ce-body`, and
  the module menu as `role=navigation` named "Module Menu". Its first guess, the
  custom element `<typo3-backend-module-menu>`, does not exist in 14.3.
- `knowledge/documents/project/testing/playwright.md` already ships a backend
  spec, and it asserts the URL alone: "whether the backend answered the module
  or the login form is a question the URL answers". So the corpus carries a
  backend spec that never enters the module, and the first spec that does is on
  its own.
- The `browser-tests` hint carries the layout — `e2e/` per module, `fixtures/`
  for page objects, `helper/` for the login setup — and no selector.
- `knowledge/catalog/component/entries.json` comes off the core's Sass and
  answers what an author builds: a root class, its variants, its custom
  properties and markup to write. The session read that description and passed
  the tool over, which is the finding it reported as an unverified assumption.
  The assumption held.
- [writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
  keeps backend markup out of a published skill, so
  `skills/typo3-extension-testing/references/playwright.md` cannot carry it
  either.

## Decided

- The judgement is **step 1a**, the knowledge is absent. The hint that routed
  the session correctly is `content-element-preview`, which says a browser test
  asserts a preview. It stops exactly where the selectors begin.
- It lands in two places a browser task already passes. One sentence in
  `browser-tests` that a backend module renders inside `#typo3-contentIframe`.
  And the paragraph with the tile and preview-body selectors in the Playwright
  document, beside the backend spec that is already there.
- Not the component catalog. Its entries answer what an author writes and come
  off the core's Sass, and `bin/cli catalog:check` verifies them as markup to
  produce. What a spec locates by is the other direction.
- The writer reads a backend that runs rather than the report. The selectors
  above are one session's account and nothing here reproduced them.

## Assumed

- That the frame identifier is stable across the covered majors. The session saw
  it on 14.3 alone, and a selector that moved would be a statement that needs
  `since`. The verification step decides that.

## Wrong if

- A session that has the iframe sentence still writes locators that find
  nothing. Then the gap is a page object the suite composes from, not a fact.
- The tile markup turns out to differ between the covered majors and the
  statement carries no bound. Then this belonged in a catalog a check holds
  against `.checkouts/` after all.

## Since then

A session read the selectors off the core rather than off the report, and the
assumption did not hold as written. The two the report named are in the shipped
element on every covered major, so the statement needs no bound. The tile
carries its own class, id and data attributes.

The module menu is where the report is worth a correction rather than a copy.
Its accessible name is a translated label, so an assertion on it asserts the
backend's language along with the element. The document names the id instead.
