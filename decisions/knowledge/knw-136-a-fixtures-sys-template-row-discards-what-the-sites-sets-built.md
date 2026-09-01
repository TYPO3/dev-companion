---
id: D-KNW-136
title: A fixture's sys_template row discards what the site's sets built
date: 2026-08-28
status: open
readings:
  - 2026-09-01
---

# D-KNW-136 — A fixture's sys_template row discards what the site's sets built

**`project-extension-tests` says that a functional test naming its sets in the
site configuration renders as if it named none, because the row
`setUpFrontendRootPage()` writes clears them.**

The session that reported this took the failure for a site set that was never
resolved, and it is one that was resolved and then thrown away.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-074036`](../../feedback/archive/2026-08-28-074036-nothing-says-how-to-assert-a-content-element-s.md).
  It wrote a site configuration naming the element's set, seeded a fixture
  through `setUpFrontendRootPage`, and got "Content Element with uid 1 and type
  table has no rendering definition!" over two runs of about eight seconds. It
  says outright that it did not root-cause whether the set was resolved at all.
- **The three calls that decide it, read in `.checkouts/13.4`, `14.3` and `main`
  on 2026-08-28.** `SysTemplateTreeBuilder::getTreeBySysTemplateRowsAndSite()`
  adds the site's own include as the first child wherever the site entity's own
  `isTypoScriptRoot()` answers true — which is a site naming any set — and
  appends every `sys_template` row after it.
  `IncludeTreeAstBuilderVisitor::visitBeforeChildren()` replaces the whole AST
  for the first row whose clear flag is set. And
  `FunctionalTestCase::setUpFrontendRootPage()` writes `'clear' => 3`, on the
  `8`, `9` and `main` lines of `typo3/testing-framework` alike.
- **So the sets are included and then discarded**, and the frontend's own
  message names the content element rather than the site — which is why the
  session read it as a set that never arrived.
- **12.4 has no such case.** It has no site set directory and no such method, so
  the statement is bound `since: 13`.

## Decided

- One statement on `project-extension-tests`, beside what the harness already
  does around a test: the row, the reset, the message, and importing the
  element's own TypoScript file instead.
- **Made in this run.** The reading is `.checkouts/` and
  `.checkouts/testing-framework`, which this judgement did, and `D-FBK-052`
  bounds the queueing rule to a lookup still to be made.
- The working route is the session's own and is stated as it ran it — the
  element's file and the helper it copies from — rather than as a rule about
  which files a fixture imports.
- Against the rest of what the report asks for. Whether a `CONTENT` object's
  `renderObj` reaches one element without a content type case, and what a
  package that defines one without requiring `fluid_styled_content` owes, are
  claims a render settles rather than a reading, and the session did not report
  which of the two its own fix rested on.

## Assumed

- That an unresolved constant costs the assertion nothing, which the session
  reports and no reading here contradicts.

## Wrong if

- A session reports the import route failing where the set route was the one
  that worked, which would say the reset is conditional on something neither
  class states.
- The testing framework stops writing the flag, and the statement then names a
  row that no longer clears anything.
