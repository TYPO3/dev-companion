---
id: D-FBK-055
title: A registration file is checked after the cache flush or not at all
date: 2026-09-02
status: open
coveredBy:
  - Typo3RuntimeTest::aRegistrationFileIsCheckedBeforeItIsInstalled
  - Typo3RuntimeTest::aTranslationDomainNamesNoFileBelowTheVersionThatResolvesThem
  - Typo3RuntimeTest::theTitleUnitDecidesWhetherLabelsResolve
---

# D-FBK-055 — A registration file is checked after the cache flush or not at all

**Five registration mistakes in one session were each caught by an installation
that had already been rebuilt.**

## Evidence

- `feedback/2026-09-02-135045` counts them and says what caught each. A doubled
  sub-route path: `typo3_backend_module_lookup` and chance, after the tests had
  passed against it. A parentless module's lost routes: a Playwright failure
  that showed another module's chrome. A position that did not reach the end: a
  read of the rendered menu. An absent `typeicon_classes` mapping: the person
  the session worked for, with a look at the page tree. A stale compiled
  container: the backend, which fell back to another module again.
- The session's cycle was: edit the file, flush the cache in the container, call
  the lookup, read `routes`, `position` and `navigationComponent`, repeat. It
  names that call the most useful of the session and a post mortem rather than a
  check.
- Three of the five now have statements in `backend-modules`, written the same
  day off the core checkouts. Those reach a session before the file exists,
  which is earlier than any check. They say what the keys resolve to rather than
  what this file resolves to.
- `typo3_backend_module_lookup` already performs that resolution against the
  booted registry. What it cannot do is answer for a file that is not yet in an
  installation with a cache flush behind it.

## Decided

- It gets built, and the boundary is the file rather than the registry. The tool
  takes a `Configuration/Backend/Modules.php` and reports what the registration
  resolves to. The route path per entry, whether the entry gets routes at all,
  where it lands among its siblings. And which named identifier does not exist
  in this installation.
- **The identifier half is the first step and stands on its own.** `parent`,
  `iconIdentifier` and the labels domain each fail when a user opens the module
  and never on a read of the file. Each resolves against the installation this
  server already asks. The feedback names it as the cheap half and it is what
  the card carries.
- The hints stay the first line and the tool is the second. A statement about
  what a key resolves to reaches a session that has not written the file; a
  check reaches one that has.
- This entry decides nothing about the fifth mistake. A stale compiled container
  is a state of the installation rather than of the file. The read that would
  answer it is not this one.

## Assumed

- That the resolution is reproducible outside a booted installation. The core
  applies aliases, the sort and the standalone promotion in `ModuleRegistry`,
  and a second implementation of that here is a copy that goes stale. That is
  the argument for the identifier half first and the resolution half only where
  it proves worth the copy.
- That one session's five mistakes say the shape. They are five in one file kind
  in one session, which is the strongest single count in this corpus and still
  one session.

## Wrong if

- The resolution half drifts from the core's. Two answers about where a module
  lands, one from a booted registry and one from a second implementation. That
  is worse than one answer that needs a cache flush.
- The hints written the same day close it. If no session reports a registration
  mistake again, the gap was the knowledge and not the check. This entry then
  paid for a tool nobody needed.

## Since then

The identifier half is whole. `labels` resolves to the XLF file behind it.
Directly for an `LLL:` reference, and through a search for the file whose path
derives it for a translation domain. The answer is the trans-unit the module
title comes from, which is `mlang_tabs_tab` behind the one form and `title`
behind the other. A domain below the version that resolves them names no file at
all, and the check says so rather than searches. Nothing of the resolution half
got built, so both **Wrong if** stand unread. What would settle the first is a
session that reports a placement mistake the hints did not prevent.
