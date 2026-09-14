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
test can construct, are hints in `knowledge/`. They are no filesystem read in
`src/`.**

Two tools told a review of a core patch to
`Build/Sources/TypeScript/form/backend/form-editor/view-model.ts` that
`unitJavascript` was its suite. It had to establish in four checkout reads that
the layer cannot hold that module at all. The hint it received said where a test
goes and what discovers a group. Neither half of what decides the finding was
anywhere in the corpus.

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
  nothing else. There is no module-mock plugin. So the only way a test replaces
  a collaborator is a stub of a method on an imported object. Three of the tests
  there do that.
- The 21 tests there are all of the shape that permits it. Classes
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

- Three statements go into the `javascript-unit-tests` hint. The packages that
  carry a `tests/` directory, the module shape a test can construct, and the
  bootstrap-wired shape it cannot.
- The second one ends where a reviewer needs it to. An absent test on a
  bootstrap-wired module is a cost a review states rather than a change it
  demands.
- The feedback's first suggestion gets a refusal as stated. It asked
  `typo3_test_run_guide` to read the checkout for a path under
  `Build/Sources/TypeScript/<package>/` and report whether that package has a
  `tests/` directory. The set that read computes is the same three names on
  every covered branch. So the code would be a per-call `readdir` that returns a
  constant, and `knowledge/` is where a constant lives.
- The hint names `ls Build/Sources/TypeScript/*/tests` beside the three. So a
  caller on a branch this repository does not cover has the one-line check
  rather than a list to trust.
- No `since` or `until` applies. All four covered branches agree on the
  discovery, the set and the module shape.
- Nothing about delivery changes. The session reached the hint through
  `typo3_task_guide` and the probe still ranks it first for the question. So the
  gap was the content and not the route.
- `coveredBy: []`, because every **Wrong if** here is a fact about a move of the
  core. A test that asserts the three names would read this repository's own
  JSON and pass on the day the core gains a fourth package.

## Assumed

- A caller with a path under `Build/Sources/TypeScript/<package>/` reads the
  hint far enough to reach the package list. It is the third bullet of nine.
- The three-package set changes rarely enough that a stale list is caught by the
  `ls` beside it. It has held from 12.4 to main.

## Wrong if

- A fourth package gains a `tests/` directory and the hint still names three. So
  a caller hears the group does not exist where it does.
- A session reports that it read the hint, found its package absent from the
  list, and still could not price the finding. That would mean the module shape
  was the whole question and the package list is noise beside it.
- The runner gains a module-mock plugin, at which point a bootstrap-wired module
  comes in reach and the second statement is what goes stale.
