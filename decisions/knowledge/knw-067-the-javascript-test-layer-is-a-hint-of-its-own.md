---
id: D-KNW-067
title: 'The JavaScript test layer is a hint of its own'
date: 2026-08-10
status: open
coveredBy:
  - HintsTest::aTypeScriptTestPathIsNotAnsweredWithPhpunit
---

# D-KNW-067 — The JavaScript test layer is a hint of its own

**The core's JavaScript unit test layer is one hint in the TypeScript domain.
The PHPUnit hints that arrive beside it are a domain-detection question and stay
open.**

A session told by `backend-typescript` to write JavaScript unit coverage asked
how, and got PHPUnit: `UnitTestCase`, CSV fixtures, `createStub`. It read the
layer out of the checkout itself.

## Evidence

- `feedback/2026-08-10-101644`. The larger half of the answer was about a test
  framework the task could not use. The one call the session made carried
  nothing about where a `.ts` test goes.
- The layer is uniform across the covered branches, so it is one unbound hint.
  `Build/web-test-runner.config.mjs` exists on `main`, `14.3`, `13.4` and
  `12.4`. Each builds one group per package under `Build/Sources/TypeScript`
  that has a `tests/` directory, over `tests/**/*.ts`. Each has the same import
  map onto `Resources/Public/JavaScript/` and the same `"test": "wtr"` script.
  Only the `~labels/` middleware is younger, absent on `13.4` and `12.4`, where
  no TypeScript source names `~labels` at all. So that statement alone carries a
  bound.
- `runTests.sh -s unitJavascript` runs `npm run test` in `Build/` and passes no
  arguments through on any of the four. That is why the hint says a targeted run
  means a direct call of the runner rather than offers a flag.
- The PHPUnit hints are not a ranking accident. `Domains::detect()` reads
  `unit test` as a PHP keyword, deliberately, since `D-KNW-009`: those phrasings
  are how somebody with no suite yet asks. A `.ts` path does not take it back,
  so the query selects the PHP domain and every PHPUnit hint becomes a
  candidate.

## Decided

- One hint, `javascript-unit-tests`, in the `typescript` domain and `core`
  scope. It carries where the file goes, what discovers it, that the import map
  points at built output so the branch has to be built first, the `~labels` stub
  and the `@open-wc/testing` plus mocha idiom.
- The suite entry for `unitJavascript` names it. `typo3_test_run_guide` answers
  how a session runs a suite and this hint answers how a session writes one of
  its tests. The session that has the first has no reason to guess that the
  second exists.
- The hint says what the layer cannot see, and names `browser-tests` for it. The
  same session's other report, `feedback/2026-08-10-101714`, is the layer above
  this one: no procedure to look at a backend change in a real browser. A
  session that reads this hint should not conclude that a green wtr run covers a
  positional defect.
- No skill and no document. The gap was a set of statements about one layer,
  which is what a hint is. The order of the work was never in question.
- The domain half is a carve-out in `Domains::detect()`, of the shape the
  `ADMINISTERED_FROM_THE_BACKEND` one already has: the seven testing phrasings
  do not add PHP where the paths carry a domain and PHP is not among them. Only
  those seven, and only against paths. Every other PHP keyword names a PHP thing
  rather than a kind of work. Free text cannot narrow anything, because a
  negated mention reads like a positive one. The feedback's own call now answers
  `javascript-unit-tests` first with no PHPUnit hint in it.
  `bin/cli hints:coverage` reports the same prompts and the same hints as
  before.
- The second call the feedback reports is worth what it says it is. For a
  TypeScript task `typo3_test_run_guide.suites` comes back identical to
  `typo3_task_guide.testSuites`, and `invocation` is the whole of what it adds —
  which `nextTools` already named. The block stays in both, because the tool is
  also a first call for a session that never asked for a brief. What changes is
  that `nextTools` now says the suite list is the one above, so the round trip
  is a decision rather than a discovery.

## Assumed

- That the hint in first place is enough for the case its feedback came from.
  The session's own call put `unit-test-doubles` at the top; it now ranks below
  `javascript-unit-tests`, with the PHPUnit hints still in the answer.

## Wrong if

- A session reads this hint and still writes the test against the TypeScript
  source rather than the built output. Then a sentence in a list does not carry
  build-before-test, and it belongs where the session runs the suite.
- A task that really does touch both layers loses its PHPUnit hints. The
  carve-out reads the paths, so naming one PHP path keeps them; a session that
  names neither is what would show the condition is the wrong one.

## Since then

On 2026-08-25 the two lists turned out to be identical only where the domains
hold no more suites than a brief carries. Measured with the arguments of
`feedback/2026-08-24-183319`, which names two PHP classes.
`typo3_test_run_guide` returns eighteen suites for the `php` domain. The
`testSuites` beside them are `checkIntegrityPhp`, `cglGit`, `composerInstall`
and `e2e`, the four strongest against that task text, of which one is also in
`checks`.

The TypeScript account stands and the round trip is still worth what this entry
says. What stood unbound is the `nextTools` sentence, which claimed the suites
that tool lists are the `testSuites` above. It now says the brief carries the
strongest few of what that call returns. So what the call adds is the rest of
them beside the invocation notes.
