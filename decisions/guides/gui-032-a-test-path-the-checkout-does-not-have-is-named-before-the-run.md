---
id: D-GUI-032
title: A test path the checkout does not have is named before the run
date: 2026-10-21
status: open
coveredBy:
  - ProjectTest::aTestPathTheCheckoutDoesNotHaveIsNamedWithTheLayersItHas
---

# D-GUI-032 — A test path the checkout does not have is named before the run

**`typo3_test_run_guide` names a given path below a system extension's `Tests/`
that the core checkout does not have. It names the layers that extension does
have, and it says what runTests.sh prints for such a path.**

## Evidence

- [`feedback/2026-10-21-153139-a-functional-run`](../../feedback/archive/2026-10-21-153139-a-functional-run-on-a-sysext-without-tests.md).
  `runTests.sh -s functional` on `typo3/sysext/belog/Tests/Functional`. belog
  has `Tests/Unit` alone. PHPUnit printed `Test file "…" not found`, and the
  summary said FAILURE. The session filtered its output and read a regression.
- Re-run on 2026-10-21 against the server as it was. The guide said only that
  no suite covers the path, which reads as a fact about the suites.
- PHPUnit 11.5 in this repository prints the same message for a missing path.
  `.checkouts/main/typo3/sysext/belog/Tests` holds `Unit` alone.

## Decided

- The guide reads the checkout it found, and only a core checkout. Elsewhere no
  `typo3/sysext` exists to read, and `missingPaths` stays empty.
- `missingPaths` is data beside `uncoveredPaths`. A missing path and a path no
  suite selects are two different facts.

## Wrong if

- A session gets `missingPaths` and runs the path anyway, because the call came
  after the run rather than before it. Then the sentence belongs in the
  runner's notes that `typo3_script_lookup` answers.
