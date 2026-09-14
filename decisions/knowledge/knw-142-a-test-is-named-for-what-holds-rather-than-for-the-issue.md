---
id: D-KNW-142
title: 'A test is named for what holds rather than for the issue'
date: 2026-09-02
status: open
coveredBy:
  - HintsTest::aTestIsNamedForWhatHoldsRatherThanForTheIssue
---

# D-KNW-142 — A test is named for what holds rather than for the issue

**The name says what holds and the comment says which case the test exercises,
and the issue number is in neither.**

`core-tests` carried the attribute, the base classes and the fixtures, and said
nothing about a test's name or what its docblock is for.

## Evidence

- The maintainer ruled it on 2026-09-01 and said it holds for every test rather
  than for the ones where it first came up — `D-FBK-053`.
- The core does not name a test after an issue. No test method below
  `typo3/sysext/*/Tests/` carries a five- or six-digit number in its name, read
  in `.checkouts/main` on 2026-09-02.
- The frame is rare rather than absent there. Twenty-one lines across those same
  directories carry the word "regression", a handful of them as "Regression test
  for ...".
- The number is already written down twice: the commit's `Resolves:` trailer,
  which `core/contribution/commit-messages` states, and the changelog file name,
  which `core/contribution/changelog` does.

## Decided

- One statement in `core-tests`, beside the sentence about the mirror of the
  class path. The name and the comment are one decision and the hint that owns
  test shape is where it goes.
- The example is a name read out of the checkout —
  `typeSpecificTitleOverridesCtrlTitle` — rather than one invented for the
  sentence.
- The statement gives the reason as what a later reader needs, because that is
  what makes it more than style. A reviewer without the issue open, and the same
  test read years after the fix.
- The rare counter-examples are not named. A hint that lists them teaches the
  shape it stands against.
- `regression test` is not in the vocabulary, though it is the words a caller
  would use. It carried this core hint into an extension brief. The scenario
  `ScopeTest::anExtensionTestBriefRoutesTheHarnessTheExtensionHas` asks for
  functional regression tests outside the core. The hint names
  `typo3_test_run_guide`, which has no answer for those paths. The words that
  stayed reach the statement anyway.

## Assumed

- That a docblock is where the case has its description. The core marks cases
  with `#[Test]` and many carry no docblock at all. So what this asks for goes
  where one exists rather than a docblock on every test.

## Wrong if

- A reviewer asks for the issue number in a test after all, which would make
  this the repository's rule rather than the core's practice.
- The name grows into a sentence to carry what the comment should, which is the
  failure the other half of the statement trades against.
