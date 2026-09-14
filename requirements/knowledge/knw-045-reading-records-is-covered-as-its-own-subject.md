---
id: R-KNW-045
title: 'Reading records is covered as its own subject'
status: held
heldBy:
  - HintsTest::readingRecordsIsAnswered
---

# R-KNW-045 — Reading records is covered as its own subject

**How a record read works is a subject of its own. That is what a QueryBuilder
restricts unasked, and what the core overlays after the query rather than
selects in it.**

Both fail the same way, the record is in the database and not in the result, and
neither shows in the SQL the session wrote.

## From

A corpus in which `datahandler-persistence` carried `querybuilder`,
`restriction`, `enablecolumns`, `hidden record` and `deleted record` in its
`appliesTo` and not one statement about a read. A grep for the read APIs over
every hint in the corpus found one sentence, and it was about the doktypes of a
menu (2026-08-03).
