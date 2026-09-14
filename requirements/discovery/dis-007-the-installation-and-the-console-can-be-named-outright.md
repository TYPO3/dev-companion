---
id: R-DIS-007
title: 'The installation and the console can be named outright'
status: held
heldBy:
  - InstanceTest::aNamedInstallationThatIsNotThereIsReported
  - InstanceTest::anInstallationNamedOutrightIsReadWithoutAnySearch
  - Typo3CliTest::aStatedCommandIsUsedInsteadOfWorkingOneOut
  - Typo3CliTest::aStatedCommandThatIsNoProgramIsReported
---

# R-DIS-007 — The installation and the console can be named outright

**A caller can set the installation root and the console command outright, and
the answer says which of the two it used.**

Every layout-specific discovery failure is then a one-line fix for the user
instead of five tools that go quiet in silence. The answer reports a stated
setting it cannot use, and never replaces it with a discovered one in silence.

## From

A session where two links broke at once, a moved bin-dir and a host PHP below
the required one, with no lever available (2026-07-29).
