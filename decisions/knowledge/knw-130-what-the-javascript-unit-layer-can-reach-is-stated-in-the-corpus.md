---
id: D-KNW-130
title: What the JavaScript unit layer can reach is stated in the corpus
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-130 — What the JavaScript unit layer can reach is stated in the corpus

**Which packages the JavaScript unit runner discovers, and which module shape a
test can construct, are hints in `knowledge/` rather than a filesystem read in
`src/`.**

A review of a core patch to
`Build/Sources/TypeScript/form/backend/form-editor/view-model.ts` was told by
two tools that `unitJavascript` was its suite, and had to establish in four
checkout reads that the layer cannot hold that module at all. The hint it
received said where a test goes and what discovers a group; neither half of what
decides the finding was anywhere in the corpus.

## Evidence

- `ls .checkouts/<branch>/Build/Sources/TypeScript/*/tests` on 12.4, 13.4, 14.3
  and main returns the same three packages: `backend`, `core` and the
  rte-ckeditor one. Every other package — `form`, `install`, `dashboard`,
  `filelist` and the rest — has none, so the runner makes no group for it.
- `Build/web-test-runner.config.mjs` discovers groups by `readdirSync` plus
  `existsSync(.../tests)` on all four branches, at the same lines, over
  `tests/**/*.ts`. The discovery is not version-bound and neither is the set it
  returns.
- The config's plugins on main are `esbuildPlugin` and `importMapsPlugin` and
  nothing else. There is no module-mocking plugin, so the only way a test
  replaces a collaborator is by stubbing a method on an imported object, which
  three of the existing tests do.
- The 21 existing tests are all of the shape that permits it: classes
  (`AjaxRequest`, `UrlFactory`, `SecurityUtility`), pure functions (`literals`,
  `css-prefixer`), exported singleton objects (`Notification`, `Icons`) and a
  custom element.
- `form/backend/form-editor/view-model.ts` keeps six module-level `let`
  bindings, assigned only by `bootstrap()`, which runs
  `structureComponentSetup()`, `modalsComponentSetup()`,
  `inspectorsComponentSetup()`, `stageComponentSetup()`, `buttonsSetup()`,
  `addPropertyValidators()` and `loadAdditionalModules()` against the DOM. The
  shape is identical on 12.4, 13.4, 14.3 and main.
- `@typo3/form/backend/` is in the import map, so the import is not what blocks
  a test there. The group and the module shape are.

## Decided

- Three statements go into the `javascript-unit-tests` hint: the packages that
  carry a `tests/` directory, the module shape a test can construct, and the
  bootstrap-wired shape it cannot.
- The second one ends where a reviewer needs it to — a missing test on a
  bootstrap-wired module is a cost a review states rather than a change it
  demands.
- The feedback's first suggestion is rejected as stated. It asked
  `typo3_test_run_guide` to read the checkout for a path under
  `Build/Sources/TypeScript/<package>/` and report whether that package has a
  `tests/` directory. The set that read computes is the same three names on
  every covered branch, so the code would be a per-call `readdir` returning a
  constant, and `knowledge/` is where a constant lives.
- The hint names `ls Build/Sources/TypeScript/*/tests` beside the three, so a
  caller on a branch this repository does not cover has the one-line check
  rather than a list to trust.
- No `since` or `until` is written. All four covered branches agree on the
  discovery, the set and the module shape.
- Nothing about delivery is changed. The reporting session reached the hint
  through `typo3_task_guide` and the probe still ranks it first for the
  question, so the gap was the content and not the route.
- `coveredBy: []`, because every **Wrong if** here is a fact about the core
  moving. A test asserting the three names would read this repository's own JSON
  and pass on the day the core gains a fourth package.

## Assumed

- A caller holding a path under `Build/Sources/TypeScript/<package>/` reads the
  hint far enough to reach the package list. It is the third bullet of nine.
- The three-package set changes rarely enough that a stale list is caught by the
  `ls` beside it. It has held from 12.4 to main.

## Wrong if

- A fourth package gains a `tests/` directory and the hint keeps naming three,
  so a caller is told the group does not exist where it does.
- A session reports that it read the hint, found its package absent from the
  list, and still could not price the finding — which would mean the module
  shape was the whole question and the package list is noise beside it.
- The runner gains a module-mocking plugin, at which point a bootstrap-wired
  module becomes reachable and the second statement is what goes stale.
