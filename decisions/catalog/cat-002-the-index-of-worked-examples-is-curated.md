---
id: D-CAT-002
title: 'The index of worked examples is curated'
date: 2026-07-29
status: revoked
revokedBy: D-CAT-007
---

# D-CAT-002 — The index of worked examples is curated

**A check runs the path of a worked example against every covered checkout;
nothing checks the sentence about what it is a reference for.**

An index of directories inside the core is the kind of answer that rots in
silence. A moved path leaves a caller to read a miss as "I looked in the wrong
place". So `bin/cli catalog:check` derives each entry's range again from whether
the path is there in every covered checkout. That is the way it derives the
component and system-extension catalogs.

## Decided

- The check reads the path, and not the sentence about what the entry is a
  reference for. Existence is what a script can know. Whether `theme_camino`
  still demonstrates the layout is a judgement. A wrong judgement here costs one
  read, while a wrong path costs the caller their trust in the tool.

## Assumed

- The list stays short enough to reread by hand when a major lands: seven
  entries, each a subject a session needed. The value is one line per directory,
  not coverage.

## Wrong if

- An entry's path still exists while what is inside moved.
  `Build/tests/playwright/e2e` survives a rewrite that puts the fixtures
  somewhere else. Then the check has to descend into the entry. The honest form
  names the two or three files that carry the shape rather than the directory.

## Revoked on 2026-08-01

On the entry this named, and it had already happened. 13.4 has
`Build/tests/playwright/e2e` with two spec files loose in it and the
accessibility scan a Playwright project of its own beside it. The layout the
entry describes, one directory per module with the scan among them, arrived in
14.3. Existence passed the entry on v13 and reported nothing. The entry now
names the four files that carry its sentence:
`e2e/accessibility/modules.spec.ts`, `fixtures/backend-page.ts`,
`helper/login.setup.ts` and `Build/playwright.config.ts`. `catalog:check` reads
those as well as the path. The derived range moved to v14, and a v13 caller gets
no entry rather than a route to a layout their checkout does not have. Only this
entry names files. For the other six, existence is still all the check reads.
