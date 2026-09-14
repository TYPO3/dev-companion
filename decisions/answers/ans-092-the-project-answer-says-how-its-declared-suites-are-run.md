---
id: D-ANS-092
title: 'The project answer says how its declared suites are run'
date: 2026-08-21
status: open
coveredBy:
  - HintsTest::aSuiteThatWillNotStartIsAnsweredBeforeTheHarnessIs
  - ProjectTest::aDeclaredSuiteOutsideTheCoreIsToldWhatARunNeedsFirst
---

# D-ANS-092 — The project answer says how its declared suites are run

**`typo3_project_describe` points a repository outside the core at what its
declared test commands need before their first assertion. It already points a
core checkout at `typo3_test_run_guide`.**

That arm of the answer exists only for a core checkout. Everywhere else the same
answer lists `test:php:functional` beside the rest and says nothing about how to
run it. The knowledge that would have answered it sits one lookup away.

## Evidence

- `feedback/2026-08-19-094248` reports eight round trips in an extension
  repository to work out how to run its own suite.
  `composer test:php:functional` returned 114 tests and 114 errors with
  *Database credentials for tests are neither set through environment variables,
  and can not be found in an existing LocalConfiguration file*. The session
  nearly filed that as a repository defect before it diagnosed it as
  environmental.
- The session passed over the tool the feedback names, and correctly so.
  `typo3_test_run_guide` opens its description with *Say what this core checkout
  needs*. A path that reads as a project or an extension already gets a decline
  rather than commands. Calling it would have cost the session a round trip and
  answered nothing.
- The answer is here and a caller who asks reaches it.
  `bin/cli hints:probe "composer test:php:functional fails with 114 errors database credentials"`
  returns `project-extension-tests` as its only hit, at a text score of 297.
  That hint states the five environment variables and the message that does not
  name them. It states that the account needs the right to create a database per
  test class. That is root rather than the user the site runs as, under DDEV.
  `knowledge/documents/extension/testing/phpunit.md` says the same under
  *Database credentials for the functional suite*.
- Nothing routed the session there, and that reproduces today.
  `typo3_task_guide` with
  `task="full audit of the blog extension before its v14 release"`,
  `changeType="audit"` and one `packages/blog/Classes/` path matches
  `routing-request-handling` and `extension-ter-release` and no test hint. Its
  `nextTools` names `typo3_project_describe`, `typo3_extension_describe`,
  `typo3_changelog_lookup`, `typo3_hint_lookup` with the file paths,
  `typo3_task_guide` again and `typo3_feedback_record`. Its checklist carries
  *Run the checks this repository declares rather than recommending them* and
  names no answer for one that does not start.
- `ProjectDescribe::suites()` is where the core checkout gets that pointer, and
  it returns the empty string for every other kind of instance. The answer that
  lists the declared commands therefore says how to run them only where the
  suites are the core's.
- `D-ANS-086` decided the other precondition on the same answer, the PHP bound
  Composer wrote into the vendor tree. It rejected a wider
  `typo3_test_run_guide` for the reason this feedback ran into.

## Decided

- Step 3 of the ladder, routing. The answer exists, and the surface that should
  have named it is the one the caller was already in.
- The pointer belongs on the project answer rather than on the task brief. It is
  the one surface that knows which kind of checkout it read. So it can name an
  extension-only lookup and keep it away from a core patch review.
- Rejected, again: a renamed or wider `typo3_test_run_guide`, which this
  feedback asks for first. Its suites are `Build/Scripts/runTests.sh`
  invocations and that script is in the core repository — `D-ANS-086`. `core`
  stays out of the name because this server is about the core throughout, so the
  segment separates nothing.
- Rejected: `typo3_test_run_guide` in the audit brief's `nextTools`, which this
  feedback asks for second. `TaskGuide` drops it outside the core through
  `Scope::isCoreOnly`, and its return would hand over commands that do not exist
  in that repository.
- Rejected: a line in the `audit` intent's `tools` or `checklist`. Both filters
  in `TaskGuide` only drop core-only lines outside the core, and neither drops
  an extension-only line inside it. So the pointer would reach a core patch
  review as well.
- Not step 1b. No tool and no skill is absent. `typo3_project_describe` already
  owns what this repository declares and whether it starts, and
  `typo3-extension-testing` already owns the run of a suite.
- The two things the feedback wants that this repository has not established
  stay out of the change and are the todo's first step. One is that `ddev exec`
  refuses from a git worktree. The other is that the PHP a container runs and
  the PHP a lockfile resolves PHPUnit for can disagree.

## Assumed

- That a caller who looks at a declared test command has the project answer in
  front of it. The brief names it first, and that tool's own description ends
  with the line to call it before the caller recommends or runs a check. Whether
  the session that reported it called it is not in the report.
- That the credentials are what such a command actually stops on. One report
  says so and the hint states it.

## Wrong if

- The session that hits this never calls `typo3_project_describe` and reads
  `composer.json` itself. The pointer would then stand where nobody stands, and
  the routing would have to move to the brief after all. That needs a scope
  filter the intents do not have.
- A declared test command stops on something else in the repositories people run
  this against. That is a container the suite cannot reach, a driver it lacks
  configuration for, a fixture path. The pointer would then name the wrong half
  of the problem.
- A caller reads the lookup and abandons it. `project-extension-tests` has the
  title *Setting a Test Suite Up in an Extension* and its first three hints are
  about how to write the harness. So a caller whose harness exists may stop
  before the credentials. It would then take no round trip off anybody, which is
  the measure `D-FBK-027` sets.

## Since then

The pointer landed and the fourth **Wrong if** went off the table rather than
stayed. The hint opens on the suite that does not start and the harness half
follows it.

The matcher and not the prose decided how much could go in. The body had room
for two statements and not three. At three the dilution put one statement below
the coverage floor and the hint no longer answered a question it owns. The two
facts the todo held back had their measurement in a project started for it and
deleted afterwards.
