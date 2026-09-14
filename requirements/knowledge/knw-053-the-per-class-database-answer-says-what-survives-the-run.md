---
id: R-KNW-053
title: 'The per-class database answer says what survives the run'
status: held
restsOn: [D-KNW-022]
heldBy:
  - HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun
---

# R-KNW-053 — The per-class database answer says what survives the run

**The statement that every test class gets its own database says what becomes of
that database, and what tells one from the live database.**

Without them it describes databases that appear and nothing that says which of
them the harness will reclaim. A session that reads it watches the set grow and
cannot tell a leftover from the database the site runs on. It starts to account
for records by hand. So three things stand with it. Nothing drops one when the
run ends, and the next run of that class is what reclaims it. The suffix is a
hash of the test class rather than of the run, so the classes bound the set.
That suffix is what marks a test database, with the two cases that carry no such
name, `$initializeDatabase = false` and `pdo_sqlite`.

## From

A TYPO3 14 testimonials session that accumulated a database per functional test
class over a session. It lost track of which records it had modified by hand
across the live database and the test ones (`feedback/2026-08-01-003929`,
2026-08-01). Its words: "created tons of databases, lost track what manually
were modified instead of using api".
