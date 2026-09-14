---
id: R-KNW-032
title: 'Project configuration states who owns which file'
status: held
heldBy:
  - HintsTest::projectSystemConfigurationStatesItsOwnershipBoundary
---

# R-KNW-032 — Project configuration states who owns which file

**Project configuration answers tell the TYPO3-owned `settings.php` from the
project-owned `additional.php` that loads after it. They state how DDEV's
generated marker changes that ownership.**

They warn that a regenerated ignore file can hide an untracked deployment
configuration, and they require a check that the project file stays tracked.
They identify DDEV as local-only. A shared project file guards its local
overrides and reads deployment secrets from the environment rather than commits
them.

## From

DDEV replaced deployment overrides and ignored the replaced file again in the
same project-configuration change (2026-07-30).
