---
id: R-KNW-058
title: 'The placement answer says which page may hold the record'
status: held
restsOn: [D-KNW-023]
heldBy:
  - HintsTest::thePlacementAnswerSaysWhichPageMayHoldTheRecord
---

# R-KNW-058 — The placement answer says which page may hold the record

**The placement answer says which page may hold a record: the doktype decides,
and a folder is what allows a table of your own.**

The rest of the rule comes with it. A standard page allows `pages`,
`sys_category`, `sys_file_reference`, `sys_file_collection` and every table that
declares `ctrl.security.ignorePageTypeRestriction`. A `pid` of 0 is the table's
own `ctrl.rootLevel` instead, with admin or
`ctrl.security.ignoreRootLevelRestriction` on top.

The position is the second question and the choice of the pid is the first. A
session that seeds a table of its own has to choose a page before it can order
anything on it. The corpus answered only the order.

The failure it leaves is silent. DataHandler logs the refusal and continues, so
the datamap call returns without an error and the row is not there. That reads
as a write that did not happen for some other reason, and sends the session to
look at its field values.

The admin clause is part of the demand rather than a detail. A seed script runs
as the CLI user. An answer that leaves the doktype check to sound like an editor
restriction describes a check that session will meet anyway.

## From

A session that seeded a sysfolder, two groups, five testimonials and a content
element. It guessed at the pid and at the storage folder's role, and nothing in
the corpus said which page may hold what (2026-08-01).
