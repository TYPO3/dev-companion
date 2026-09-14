---
id: D-KNW-079
title: The corpus states what a new backend label costs
date: 2026-08-14
status: open
coveredBy:
  - HintsTest::aNewBackendLabelIsToldWhatItCostsBeforeItResolves
  - HintsTest::theLabelModuleIsWithheldFromTheMajorsThatHaveNone
---

# D-KNW-079 — The corpus states what a new backend label costs

**The corpus states both halves of the repair. The `l10n` cache keys on nothing
about the file, and the module's URL does not move when a label changes.**

`javascript-labels` carries it in `labels.json`, beside the hints on how to
author, reference and retire a label. The import of one into a module is the
fourth thing a session does to a label rather than a second subject.

## Evidence

- The reading is
  [`D-KNW-076`](knw-076-what-a-new-backend-label-costs-is-a-subject-this-server-owns.md)'s
  **Confirmed on**, taken on `.checkouts/14.3` and `main` on the same day.
  Neither of its two **Wrong if** fired, so the report's two steps are both real
  and the statement is a procedure rather than a correction.
- The feedback's own query reaches the new hint first,
  `bin/cli hints:probe "JavaScript labels module cache flush after adding XLF trans-unit"`
  on 2026-08-14. It stands at `appliesTo(30)` above the three hints the report
  got.
- The failure queries reach it too, probed the same day.
  `Label is not defined at runtime after adding a new label` leads with it. So
  does `my new label does not show up in the backend JavaScript module`.
- The neighbours keep their questions.
  `authoring a new XLF label file for my extension` still returns
  `language-files` alone, and `clear the caches after a TCA change` still leads
  with `page-cache-flushing`.

## Decided

- One hint rather than a sentence in `page-cache-flushing`. That hint is which
  cache holds a rendered page's old output, asked from `fluid`, `typoscript` and
  `php`. To reach this caller would mean two more domain tags on a long hint.
  The answer here is a browser cache as much as a server one.
- `labels.json` rather than `backend-ui.json`. The subject is what happens to a
  label, and the hint a caller needs next is in the same file. That is
  `translation-domain`, for the name the module has in an import.
- `xliff` first of the two domains, so the answer files it under Labels. The
  third domain also keeps it out of the frontend withhold rule, which drops a
  hint whose domains are the two backend UI ones (`D-KNW-033`).
- Every statement bound `since: 14`, so the hint does not exist on 12 or 13.
  There is no hint-level `since`. A hint the matcher offers on an LTS that has
  no `~labels/` at all would describe a mechanism the caller cannot reach.
- The green build is one statement and `scope: "core"`. `grunt scripts` and
  `Build/types/labels/` are the core's own build. An extension author gets the
  runtime throw with no stub in front of it, which is why the first statement
  carries the throw instead.
- Two pointers, and they are the two hints the session that reported got.
  `language-files` says a unit reaches a module later than it reaches PHP.
  `backend-typescript` says the bundle is not one of the generated files it is
  about, which is the wrong conclusion that hint makes available.
- `appliesTo` claims the artefacts and the failure phrasings and not "use
  labels". That phrase is what somebody who asks how to use a label in a Lit
  component types. It also matches the same question about a Fluid template,
  where this hint answers nothing.

## Assumed

- That a caller arrives with the failure rather than with the feature. The
  statements stand in order for somebody who holds a throw. The import stands as
  where the module comes from rather than as how to write one.
- That the hard reload is the whole of the browser half. Nothing here drove a
  browser. This run read the `max-age` and the identifier the URL carries. It
  did not test a reload that a service worker or a proxy answers instead.
- That the hint states the `cacheBustInfix` at the right altitude. It names it
  as the version, the project path and the package list, which is what
  `PackageDependentCacheIdentifier` composes. It does not name which of the two
  package caches supplies it.

## Wrong if

- A covered branch starts to feed the label files into the identifier, on either
  side. The `l10n` entry would then clear itself, or the URL would move, and the
  statement would name a step nobody owes.
- The two steps turn out to be one in practice, because the flush already
  happened for another reason and the reload is all that remains. The hint would
  then read as twice the work it is.
- A session reads it and flushes `all` rather than `system`. The hint states the
  group to be precise, and `page-cache-flushing` is where the groups have their
  explanation. The lever would be the neighbour rather than the group name.
- A session reports the same question again from an extension. Nothing bound to
  the core here except the build, and an extension's own type stubs would make
  that statement wrong rather than core-only.
