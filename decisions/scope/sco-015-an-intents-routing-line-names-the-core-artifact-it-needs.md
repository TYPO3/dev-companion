---
id: D-SCO-015
title: An intent's routing line names the core artifact it needs
date: 2026-08-28
status: open
coveredBy:
  - ScopeTest::aRuleSectionOutsideTheCoreSaysWhereAConventionIsBound
  - ScopeTest::anExtensionChangelogTaskIsRoutedAwayFromTheCoresOwnProcedure
  - ScopeTest::anExtensionDeprecationIsCommittedUnderItsOwnRepositorysConvention
  - ScopeTest::anExtensionTestBriefRoutesTheHarnessTheExtensionHas
---

# D-SCO-015 — An intent's routing line names the core artifact it needs

**The core-only check reads a `tools` line in `knowledge/task-intents.json`. So
a call that works in the core alone names the artifact it works on.**

`typo3_task_guide` drops the core-only entries of a brief outside the core by
what each rendered line names (`D-SCO-003`). An intent's own routing lines go
through that check like the rest. Four of them name the core nowhere and are the
core's own call.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001345`](../../feedback/archive/2026-08-28-001345-task-guide-routes-an-extension-test-task-at.md).
  It classified both paths as `extension` and put `typo3_test_run_guide` first
  in `nextTools`, whose own description declines for such a path.
- **Re-run in this checkout on 2026-08-28.** `typo3_task_guide` with
  `paths: ["Classes/Parser/AbstractParser.php"]` and `changeType: "test"`
  answers `scope: "extension"` and still leads with
  `typo3_test_run_guide, for the targeted invocation form`.
- **The filter fired and this line cleared it.** The same answer's
  `typo3_commit_message_guide` entry carries the outside-core wording, so
  `Scope::isCoreOnly()` ran over the list; `for the targeted invocation form`
  names no marker.
- **The unmarked candidate masked the marked one.** `TaskGuide` adds a candidate
  for the same tool that reads `for the targeted runTests.sh invocation`, which
  the check does drop. `nextTools()` keeps one entry per tool, the intent's
  first.
- **Three more lines of the same shape**, read over every intent on 2026-08-28
  and measured with the same extension path. `deprecation` and `breaking` route
  `typo3_commit_message_guide with workflow="core"`. `changelog` routes
  `typo3_rule_lookup with documentId=core/contribution/changelog` beside the
  `documentation-changelog` hint, whose own declared scope is `core`.
- **`D-SCO-002` assumed the opposite for three of them**: "it is their `checks`
  that are core-only, which `R-SCO-002` handles". Their `tools` are core-only
  too.

## Decided

- **The `tests` line names `runTests.sh`.** Outside the core both candidates for
  that tool drop, and `typo3_hint_lookup id=project-extension-tests` stands
  first. That is the entry the session which reported it used to find the
  invocation form.
- **Against a second line that routes `typo3_project_describe`**, which the
  report also names. An intent's tools are unconditional, so it would enter
  every core test brief as well, where the suites are the guide's.
- **Against a scope flag per line.** `D-SCO-003` weighed that, and the marker is
  the mechanism this repository has. This entry adds a test that asserts the
  route rather than the marker. So the next session that reorders the list finds
  out from the suite.
- **The other three go to the queue**, because their repair is `src/`. The two
  `typo3_commit_message_guide` lines may not simply drop: the caller still needs
  that tool, in the project wording the generic candidate carries. So the
  core-only filter has to run before the deduplication rather than after it.

## Assumed

- That a session outside the core loses nothing when the brief does not offer
  `typo3_test_run_guide`. Its own description declines for those paths, so what
  the drop costs is the decline.

## Wrong if

- A test brief in a core checkout comes back without `typo3_test_run_guide`.
  Then the marker drops the line where it applies.
- A repository outside the core turns up with a `Build/Scripts/runTests.sh` of
  its own. Then the drop takes the one call that would have answered for it.
- A fifth intent line arrives core-only and unmarked. The test this entry adds
  asserts one route, so only a reread holds the population.

## Since then

The queued half landed the same day. `TaskGuide::nextTools()` drops the
core-only candidates before it keeps one entry per tool. So a line the check
recognises leaves the generic candidate for that tool in place, and the four
lines carry their artefact now. An extension deprecation brief gets the project
wording where it got `workflow="core"` before.

One route of this shape was not an intent's. `Result\Prose` opened every
rendered rule section with a line that asked `typo3_test_run_guide` for a
`runTests.sh` command whatever the scope. It takes the caller's scope now.
