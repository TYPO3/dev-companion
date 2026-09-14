---
id: R-DIS-003
title: 'The console is looked for where the installation declares it'
status: held
heldBy:
  - Typo3CliTest::aBinDirectoryOutsideTheRootIsNamed
  - Typo3CliTest::aConsoleInTheDeclaredBinDirectoryIsFound
  - Typo3CliTest::anAbsoluteBinDirectoryBelowTheRootIsTheSameDirectory
---

# R-DIS-003 — The console is looked for where the installation declares it

**The search looks for the console at the `config.bin-dir` the installation
declares, before the Composer defaults, an absolute declaration below the root
included.**

Absolute is a form of the same directory, and the invocation needs it relative
to the root. One outside the root has no relative form. So the reason names it
together with `TYPO3_DEV_COMPANION_CONSOLE` rather than passes over it in
silence.

## From

`.build/bin/typo3` existed, worked, and no probe ever reached it (2026-07-29);
the same directory declared absolutely dropped out again (2026-08-01).
