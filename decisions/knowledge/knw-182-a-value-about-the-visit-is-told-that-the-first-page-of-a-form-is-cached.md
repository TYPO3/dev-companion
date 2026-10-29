---
id: D-KNW-182
title: A value about the visit is told that the first page of a form is cached
date: 2026-10-29
status: open
coveredBy:
  - HintsTest::aValueAboutTheVisitIsToldThatTheFirstPageOfAFormIsCached
---

# D-KNW-182 — A value about the visit is told that the first page of a form is cached

**The `form-framework` hint states beside its prefill statement that the first
page renders once per page-cache entry, and what that does to the honeypot.**

## Evidence

- [`feedback/2026-10-28-115456`](../../feedback/archive/2026-10-28-115456-a-value-an-event-writes-into-a-form-at-render.md)
  stamped a timestamp from an `AfterCurrentPageIsResolvedEvent` listener, as
  the hint recommends. On 14.3 every visitor got the same stamp until the
  session listed `render` as non-cacheable.
- `bin/cli hints:probe` with the feedback's terms ranked `form-framework` first.
  Its last statement already said that the first page renders in a cached
  action, but only about a changed `.form.yaml`. The prefill statement said
  nothing about the cache. So this is step 4 of the ladder, the wording.
- `typo3/sysext/form/ext_localconf.php` lists only `perform` as non-cacheable in
  `.checkouts/12.4`, `13.4`, `14.3` and `main`.
- `FormRuntime` in `14.3` and `main` dispatches the event from
  `initializeCurrentPageFromRequest()`, inside the render. `13.4` has no such
  event, so the statement keeps `since: 14`.
- `ExtensionUtility::registerControllerActions()` in `14.3` overwrites the
  controller entry of a plugin. `Extbase\Core\Bootstrap` converts a `USER`
  plugin to `USER_INT` when the action is non-cacheable. So a second
  `configurePlugin()` call that loads after EXT:form is the fix the feedback
  measured.
- `FormRuntime::renderHoneypot()` writes the random name into the frontend
  session, and `initializeHoneypotFromRequest()` reads it back on submit. The
  same code stands in all four checkouts. A cache hit writes no session entry,
  so the submit adds no validator.

## Decided

- Closed on the spot under `D-FBK-052`. The checkouts hold the evidence, and no
  contract moves.
- The prefill statement carries the cache, the fix and the missing symptom. The
  fact sits where the reader decides on the listener, and not only in the
  statement about a changed definition.
- The honeypot statement has no bound, because its code is the same on every
  covered line.

## Assumed

- That an installation with no frontend session for a cache-hit visitor behaves
  as the code reads. Nobody here submitted a cached form in an environment.

## Wrong if

- A core release lists `render` as non-cacheable, or renders the first page in
  an uncached part. Then both statements need an `until`.
- A cached first page rejects a filled honeypot field in an `E-SITE`
  environment. Then the honeypot statement is wrong.
