---
id: R-KNW-073
title: 'A step that reads from a cache says what invalidates it'
status: held
restsOn: [D-KNW-089]
heldBy:
  - HintsTest::theSchemaStepIsSaidToMigrateFromTheCachedTca
---

# R-KNW-073 — A step that reads from a cache says what invalidates it

**Where a step reads its input from a cache, the hint says what invalidates that
cache and what a stale one does to the step.**

The rule without the precondition reads as a habit. So a caller who has the
order right runs the step against yesterday's input. A step whose success
message is unconditional then reports the same success it would have reported
for the work it skipped.

## From

`typo3 extension:setup` run after two TCA files went into an active package. The
schema step migrated from the TCA cached before them, created neither table and
answered `[OK] Extension(s) ... successfully set up.`. The seed script that
followed died on a table that does not exist (feedback/2026-08-17-212117,
2026-08-17).
