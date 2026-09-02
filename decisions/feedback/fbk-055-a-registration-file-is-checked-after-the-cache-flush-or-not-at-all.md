---
id: D-FBK-055
title: A registration file is checked after the cache flush or not at all
date: 2026-09-02
status: open
---

# D-FBK-055 — A registration file is checked after the cache flush or not at all

**Five registration mistakes in one session were each caught by an installation
that had already been rebuilt.**

## Evidence

- `feedback/2026-09-02-135045` counts them and says how each was caught: a
  doubled sub-route path by `typo3_backend_module_lookup` and by chance, after
  the tests had passed against it; a parentless module's lost routes by a
  Playwright failure showing another module's chrome; a position that did not
  reach the end by reading the rendered menu; a missing `typeicon_classes`
  mapping by the person the session worked for looking at the page tree; a stale
  compiled container by the backend falling back to another module again.
- The session's cycle was: edit the file, flush the cache in the container, call
  the lookup, read `routes`, `position` and `navigationComponent`, repeat. It
  names that call the most useful of the session and a post mortem rather than a
  check.
- Three of the five now have statements in `backend-modules`, written the same
  day off the core checkouts. Those reach a session before the file is written,
  which is earlier than any check, and they say what the keys resolve to rather
  than what this file resolves to.
- `typo3_backend_module_lookup` already performs that resolution against the
  booted registry. What it cannot do is answer for a file that has not been
  saved into an installation and had its cache cleared.

## Decided

- It is built, and the boundary is the file rather than the registry: the tool
  takes a `Configuration/Backend/Modules.php` and reports what the registration
  resolves to — the route path per entry, whether the entry gets routes at all,
  where it lands among its siblings, and which named identifier does not exist
  in this installation.
- **The identifier half is the first step and stands on its own.** `parent`,
  `iconIdentifier` and the labels domain each fail when a user opens the module
  and never when the file is read, and each is resolvable against the
  installation this server already asks. The feedback names it as the cheap half
  and it is what the card carries.
- The hints stay the first line and the tool is the second. A statement about
  what a key resolves to reaches a session that has not written the file; a
  check reaches one that has.
- Nothing is decided here about the fifth mistake. A stale compiled container is
  a state of the installation rather than of the file, and the reading that
  would answer it is not this one.

## Assumed

- That the resolution is reproducible outside a booted installation. The core
  applies aliases, sorting and the standalone promotion in `ModuleRegistry`, and
  a second implementation of that here is a copy that goes stale — which is the
  argument for the identifier half first and the resolution half only where it
  is shown to be worth the copy.
- That one session's five mistakes say the shape. They are five in one file kind
  in one session, which is the strongest single count in this corpus and still
  one session.

## Wrong if

- The resolution half drifts from the core's. Two answers about where a module
  lands, one from a booted registry and one from a re-implementation, is worse
  than one answer that needs a cache flush.
- The hints written the same day close it. If no session reports a registration
  mistake again, what was missing was the knowledge and not the check, and this
  entry paid for a tool nobody needed.
