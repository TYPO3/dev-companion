---
id: D-KNW-008
title: Tooling is a row the answer crosses, not a dimension the corpus stores
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::aProjectExtensionIsToldHowToGetASuiteAtAll
  - HintsTest::theTestApiAProjectWritesItsTestsWithReachesTheProjectHint
  - ScopeTest::aStaticAnalysisQuestionFromOutsideTheCoreIsSentToItsOwnCell
  - ScopeTest::noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt
---

# D-KNW-008 — Tooling is a row the answer crosses, not a dimension the corpus stores

**How a change gets its test stays stored as it is, and the cross happens where
the answer composes.**

One file for the core harness, one hint per audience, one document for the
scripts. A tool that owns one of them names the others.

The scatter is real: `test-suite-hints.json`, `core-tests`,
`project-extension-tests`, `browser-tests`, `extension-static-analysis` and
`typo3-core-scripts.md` are one row of the knowledge base in six places. The
question was whether it should become a dimension across the audience one.

## Evidence

- The cross already happens, by name. `typo3_test_run_guide` on an extension
  path declines the suites and routes to `typo3_hint_lookup` with
  `id=project-extension-tests` and `id=browser-tests`. Those are the two cells
  of the same row for the other column, and it says why `runTests.sh` is not one
  of them.
- The third cell got its name on 2026-08-02, after this entry. Asked "set up
  phpstan for our extension" on an extension path, `typo3_test_run_guide`
  declined and routed to `project-extension-tests` and `browser-tests` alone.
  Neither says what goes into a phpstan configuration. But `phpstan`, `cgl` and
  `lintPhp` are its own suites, and static analysis is therefore one of the
  things an extension arrives there to ask for. Both of its decline sentences
  now name `extension-static-analysis` beside the other two.
  `skills/typo3-extension-testing/references/static-quality.md` names the same
  id where it stops at which packages to require.
- The cells are reachable. Of five project test queries put to
  `ArchitectureHints::find()` on an extension path, four reach the right ones.
  "how do I test my extension" and "add functional tests to the extension" reach
  `project-extension-tests`. "set up phpstan for our extension" reaches
  `extension-static-analysis`, "browser tests for the site package" reaches
  `browser-tests`.
- The one that misses — "Set up tests for our site package extension" — comes
  back with `sitepackage-layout` and `sitepackage-initial-content`. That is the
  rank: "site package" is the sharper term in a corpus where two hints carry its
  name. A storage dimension does not change which term wins.
- The rot the axis stood against already has a mechanism, and three of them. A
  statement carries `since` and `until`, and `TestSuiteHints` filters the suites
  by the target major. `src/Upkeep/TestingFramework.php` pins one
  `typo3/testing-framework` release line per covered major, and
  `bin/cli catalog:check` reads it back.
- Since `D-KNW-007` the cells say which column they are. `core-tests` declares
  `core` and carries a convention label when it reaches an extension answer,
  `project-extension-tests` declares `extension`.

## Decided

- No tooling dimension. What it would add over the current shape is a second
  place to say what `scope`, `since`/`until` and the ids already say. Every cell
  would have to carry its own versions, the expensive half, for a cross the
  answers already make.
- The route crosses the row rather than the structure. A tool that owns one cell
  names the ids of the others when the caller is not in its column. That is what
  `typo3_test_run_guide` does, and it is what a new cell has to do.
- The rank miss is a match question and sits in the queue as one.

## Assumed

- Six places for one row is readable as long as each says who it is for and each
  names the others. That is a property of the prose rather than of the model, so
  nothing but a read catches it when it goes wrong.

## Wrong if

- A cell arrives that no answer routes to, so a caller reaches it only with a
  guess at its words. That is the failure `hints:probe` exists to make visible.
- Two cells of the row start to disagree about the same fact for the same
  audience. That is what a single stored dimension would have prevented and six
  places cannot.

## Since then

The rank miss has its fix: the harness hint comes back first for the query that
used to return two sitepackage hints. The row is fourteen cells now and a route
reaches every one. This section first said three had none, and the sweep behind
that had excluded the one place a hint routes to another hint.

So the first **Wrong if** has not fired and the cross is in reach rather than
only on paper. The second is unread: whether two cells disagree about the same
fact for the same audience is a comparison of fourteen bodies.
