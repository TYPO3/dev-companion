---
id: D-SCO-001
title: Outside the core the core test guide declines rather than adapts
date: 2026-07-29
status: revoked
---

# D-SCO-001 — Outside the core the core test guide declines rather than adapts

**Outside the core `typo3_test_run_guide` returns no suite at all, while
`typo3_hint_lookup` keeps its hints and drops only their check commands.**

`typo3_test_run_guide` recognises work outside the core, and the question was
whether it should adapt its answer to the checkout it finds itself in.

## Decided

- The two payloads consist of different things. A hint is a convention and
  travels; a suite is a command against a script that lives in the core
  repository and does not.

## Wrong if

- A project-suite source turns up whose answer has the same targeted invocation
  shape as `typo3_test_run_guide`. Until then, project commands come from the
  checkout and the task skill, not from a core suite adapted by analogy.

## Revoked on 2026-07-29

The entry assumed that nothing in `knowledge/` described how an extension runs
its tests. So anything the guide offered instead would have been an invention.
`project-extension-tests` now carries that harness, verified against the
`typo3/testing-framework` tags that match. The `typo3_test_run_guide` still
declines because its answer shape is a core suite invocation. The
`typo3-extension-testing` skill takes the other branch: it verifies the
checkout's harness and routes setup or repair through the extension-test hint
and versioned documentation. Only then does it add coverage.

## Since then

A requirement carries the half that still holds, and nobody owes a successor
decision. `R-SCO-002` says that a scope outside the core changes the payload
entry by entry.
`ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests` calls this
guide from a site package and asserts that no suite and no `CI=true` come back.
Its own comment gives the reason above: every suite here is a
`Build/Scripts/runTests.sh` invocation and looks copy-pasteable where the script
is not. The decline names what it hands over instead: `project-extension-tests`,
`browser-tests` and `extension-static-analysis`.
