---
id: R-KNW-072
title: 'Which interpreter a covered version needs is answerable before anything is installed'
status: held
restsOn: [D-KNW-086, D-KNW-091]
heldBy:
  - HintsTest::eachCoveredLineCarriesItsOwnFloorAndTestedRange
  - HintsTest::whichInterpreterAVersionNeedsIsAnsweredFirst
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# R-KNW-072 — Which interpreter a covered version needs is answerable before anything is installed

**What PHP a covered TYPO3 line requires, resolves against and runs its own
suites on has an answer without an installation.**

The session chooses the number at the moment it declares the container, which is
before there is anything to ask. An installation lookup answers no-installation
there. By the time it answers at all the container exists and the number no
longer looks like a decision. So the answer belongs in the corpus, bound per
line, and the workflow step that declares the container is where the session
fetches it.

The answer owes three numbers and they are three claims. The constraint
`typo3/cms-core` declares is the requirement, and nothing else is.
`config.platform.php` is what the core repository resolves against, which is a
property a project has only if it sets the key. The `-p` option of
`Build/Scripts/runTests.sh` is the core's own test run. It says what a branch
runs against rather than what the project supports. An answer that returns the
numbers without a word about which is which is what the report already had.

The fourth demand is the relation, because that is the defect the report found.
A declared floor below the interpreter every configured environment runs is a
claim no run tests.

## From

`feedback/2026-08-17-211157`, whose session declared `^8.3` against a core that
requires `^8.2` and executed every command on 8.4. The half of it that derives
the same relation inside `typo3_project_describe` is a todo of its own
(2026-08-17).
