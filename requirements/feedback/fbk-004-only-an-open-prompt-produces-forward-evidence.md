---
id: R-FBK-004
title: 'Only an open prompt produces forward evidence'
status: held
heldBy:
  - ScenariosTest::aTargetedContractCaseIsNotSomethingARunCanAnswer
  - ScenariosTest::everyCaseHasAFileOfItsOwn
  - ScenariosTest::everyContractCaseNamesWhatHoldsIt
---

# R-FBK-004 — Only an open prompt produces forward evidence

**Only a prompt that names no subsystem, skill, tool, expected defect or
implementation shape produces forward evidence.**

Those are the open forward reviews in `scenarios/forward/`, one per working
context, and they are the only cases with a recorded run. A case that names its
own task shape lives in `scenarios/contracts/`. It carries a contract state
instead of a mark, and a session prints it for inspection rather than runs it.
Either kind is one file with one prompt, so a judgment cannot be about a prompt
nobody can identify. The environment a prompt names is a kind of working
directory rather than one installation on somebody's machine. Which checkout
plays it belongs in `todo/reference/`. No run settles a contract state. So every
case names the tests that hold it, or says that something is not guarded, and a
test it names has to exist.

## From

A suite whose prompts prescribed the feature, subsystem and often the
implementation shape they were there to discover. Its site prompts named one
person's project (2026-07-31).

## Held by

- `ScenariosTest::everyContractCaseNamesWhatHoldsIt`; that a prompt stays free
- Of a named installation is not guarded.
