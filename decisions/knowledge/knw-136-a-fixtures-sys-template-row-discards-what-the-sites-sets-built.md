---
id: D-KNW-136
title: A fixture's sys_template row discards what the site's sets built
date: 2026-08-28
status: open
readings:
  - 2026-09-01
---

# D-KNW-136 — A fixture's sys_template row discards what the site's sets built

**`project-extension-tests` says that a functional test that names its sets in
the site configuration renders as if it named none. The row
`setUpFrontendRootPage()` writes clears them.**

The session that reported this took the failure for a site set the frontend
never resolved. It is one the frontend resolved and then threw away.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-074036`](../../feedback/archive/2026-08-28-074036-nothing-says-how-to-assert-a-content-element-s.md).
  It wrote a site configuration that names the element's set and seeded a
  fixture through `setUpFrontendRootPage`. It got "Content Element with uid 1
  and type table has no rendering definition!" over two runs of about eight
  seconds. It says outright that it did not root-cause whether the frontend
  resolved the set at all.
- **The three calls that decide it, read in `.checkouts/13.4`, `14.3` and `main`
  on 2026-08-28.** `SysTemplateTreeBuilder::getTreeBySysTemplateRowsAndSite()`
  adds the site's own include as the first child wherever the site entity's own
  `isTypoScriptRoot()` answers true. That is a site that names any set. It
  appends every `sys_template` row after it.
  `IncludeTreeAstBuilderVisitor::visitBeforeChildren()` replaces the whole AST
  for the first row with the clear flag on. And
  `FunctionalTestCase::setUpFrontendRootPage()` writes `'clear' => 3`, on the
  `8`, `9` and `main` lines of `typo3/testing-framework` alike.
- **So the frontend includes the sets and then discards them.** Its own message
  names the content element rather than the site, which is why the session read
  it as a set that never arrived.
- **12.4 has no such case.** It has no site set directory and no such method, so
  the statement binds `since: 13`.

## Decided

- One statement on `project-extension-tests`, beside what the harness already
  does around a test. The row, the reset, the message, and the import of the
  element's own TypoScript file instead.
- **Made in this run.** The read is `.checkouts/` and
  `.checkouts/testing-framework`, which this judgement did, and `D-FBK-052`
  bounds the queue rule to a lookup still open.
- The route that works is the session's own and stands as it ran it, the
  element's file and the helper it copies from. It is no rule about which files
  a fixture imports.
- Against the rest of what the report asks for. Whether a `CONTENT` object's
  `renderObj` reaches one element without a content type case is a claim a
  render settles rather than a read. So is what a package that defines one with
  no requirement of `fluid_styled_content` owes. The session did not report
  which of the two its own fix rested on.

## Assumed

- That an unresolved constant costs the assertion nothing, which the session
  reports and no reading here contradicts.

## Wrong if

- A session reports that the import route failed where the set route was the one
  that worked. That would say the reset is conditional on something neither
  class states.
- The testing framework stops writing the flag, and the statement then names a
  row that no longer clears anything.
