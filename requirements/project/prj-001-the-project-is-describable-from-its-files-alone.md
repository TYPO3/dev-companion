---
id: R-PRJ-001
title: 'The project is describable from its files alone'
status: held
heldBy:
  - ProjectTest::theProjectIsDescribedFromItsFilesAlone
  - ProjectTest::theRepositoryIsDescribedBeforeAnythingIsInstalledInIt
  - ProjectTest::withoutAnInstallationThereIsNoProjectToDescribe
---

# R-PRJ-001 — The project is describable from its files alone

**The files of the repository the session stands in describe it on their own.**

That is its TYPO3 and PHP constraints, and the extensions that are its own
rather than TYPO3's. It is the sites it configures with the sets they depend on,
and the commands it declares.

No console, no database, so it answers on a fresh clone. It answers before
`composer install` as well. There the four fields that come out of the installed
tree wait for it, and the answer says which state it is in.

## From

Three sessions that asked for a project mode, and a guide that recommended
`runTests.sh` to repositories that declare `composer t3g:cgl` (2026-07-29).
