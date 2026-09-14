---
id: R-SCO-005
title: 'Every tool applies the same outside-the-core check'
status: held
heldBy:
  - ScopeTest::aScriptAnswerSaysWhichRepositoryItsCommandsRunIn
  - ScopeTest::noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt
---

# R-SCO-005 — Every tool applies the same outside-the-core check

**To recognise work outside the core is not one tool's business.**

Every tool whose payload is core-only applies the same check and opens with the
same sentence. So a caller does not have to know which of them looked.

## From

`typo3_task_guide` put the disclaimer in front of paths that
`typo3_test_run_guide` answered with four unrunnable commands in the same
session (2026-07-29).

## Held by

- the two tests above, and
