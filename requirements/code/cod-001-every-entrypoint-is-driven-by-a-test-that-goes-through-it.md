---
id: R-COD-001
title: 'Every entrypoint is driven by a test that goes through it'
status: held
heldBy:
  - EntrypointTest::helpNamesTheCommandsAndTheClientsTheyTake
  - StdioServerTest::theServerAnnouncesItselfWithItsBoundary
  - UpkeepTest::everyReadingCommandRuns
---

# R-COD-001 — Every entrypoint is driven by a test that goes through it

**A test runs each binary in `bin/` as a subprocess, and a new one is not
finished until it has one.**

A unit test reaches a class at a time, and that is the half of a command it
cannot see. That is which arguments dispatch to it, which autoloader finds it,
and what it resolves the paths it reads from. A test can hold a command to every
rule it has while nobody can reach the command.

## From

The move of the upkeep into `src/Upkeep/` put its subjects one directory deeper.
Five of them resolved the repository root as `dirname(__DIR__, 2)` from their
own file, and `bin/cli requirements:check` died on a path that no longer
existed. All 483 tests stayed green, because none of them went through
`bin/cli`. A smoke layer existed and covered the other binary alone. A run of
the command by hand found it, one commit late (2026-08-01).
