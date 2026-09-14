---
id: D-VER-003
title: 'The Fluid engine gets no version axis of its own'
date: 2026-07-30
status: confirmed
---

# D-VER-003 — The Fluid engine gets no version axis of its own

**Fluid gets no version axis of its own. Each covered branch pins the engine in
its `composer.json`, so `since`/`until` on the TYPO3 major already carries it.**

A feedback said Fluid has no empty array literal, and a note of that turned out
to need three engines rather than one. Fluid's major is not the TYPO3 major, and
nothing in `knowledge/` had said which is which. It never came up before because
no Fluid statement had a version bound.

## Decided

- No second axis. Each covered branch pins the engine in its own
  `composer.json`: 12.4 on `^2.15.0`, 13.4 on `^4.6.1`, 14.3 and main on
  `^5.3.1`. So the Fluid major is a function of the TYPO3 major, and `since` /
  `until` on the TYPO3 major already carries it. A caller who asks with a
  `targetVersion` asks about an engine, whether they know it or not.
- To verify such a statement means to fetch the engine, not to read the
  checkouts. `.checkouts/` has no `vendor/`, and `typo3fluid/fluid` is a
  Composer dependency rather than part of the mono repository. So the parser the
  statement is about is not in the tree the rest of the knowledge stands
  against. The procedure is one throwaway directory per major with the engine
  required into it, and the behaviour rendered through a probe ViewHelper. It is
  worth the twenty minutes. The feedback's own diagnosis («`{}` is a string») is
  what a read of the source would plausibly have produced, and the measurement
  says null.

## Wrong if

- A branch loosens its constraint to span two engine majors, or a Fluid minor
  changes behaviour inside one. The strict argument processor arrived in 5 and
  is injectable. So a 5.x that turns lenient by default would make a `since: 14`
  statement wrong without any TYPO3 version on the move. Either one and the
  engine needs its own field.

## Confirmed on 2026-08-01

The four constraints are still what this recorded, 12.4 `^2.15.0`, 13.4
`^4.6.1`, 14.3 and main `^5.3.1`. Each pins one engine major, so the TYPO3 major
still carries the engine. The first half of **Wrong if** is no longer a promise.
`bin/cli catalog:check` reads `typo3fluid/fluid` out of every covered checkout's
`composer.json` and fails on a branch that admits two majors or none. Nothing
holds the second half, and nothing can from here. Only the throwaway-directory
procedure above, run again, sees a Fluid minor that changes behaviour inside one
major.
