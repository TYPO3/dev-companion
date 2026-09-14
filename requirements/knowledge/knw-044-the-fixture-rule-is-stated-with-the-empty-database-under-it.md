---
id: R-KNW-044
title: 'The fixture rule is stated with the empty database under it'
status: held
restsOn: [D-KNW-019]
heldBy:
  - HintsTest::theFixtureRuleIsStatedWithTheEmptyDatabaseUnderIt
---

# R-KNW-044 — The fixture rule is stated with the empty database under it

**A functional test's database holds nothing but what that test primed, and the
knowledge base states that beside the fixture rule that rests on it.**

Without the premise the CSV fixture rule reads as one convention among several,
possible another way. A session that reads it so fetches records nobody primed,
and the failure looks like a broken query rather than an empty table. So the
premise stands where the rule is, in the words a caller asks it in, an empty
database per test run. It stands with the two boundaries that make it too strong
without them: `$initializeDatabase = false` and
`withDatabaseSnapshot()`.

## From

A TYPO3 14 testimonials session that repeatedly did not understand the
functional test data model. It kept up fetches and checks of data nobody had
primed, and resorted to inserts on the live database. It needed the prompt "is
your dataset correct?" (`feedback/2026-08-01-003003`, 2026-08-01).
