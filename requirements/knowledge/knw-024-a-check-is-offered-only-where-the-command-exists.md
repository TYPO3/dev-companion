---
id: R-KNW-024
title: 'A check is offered only where the command exists'
status: held
heldBy:
  - HintsTest::theSuiteListItselfIsFilteredByTheBranchItIsAskedFor
  - KnowledgeTest::noProseDocumentNamesACheckOnlySomeBranchesHave
---

# R-KNW-024 — A check is offered only where the command exists

**An answer offers a check only where the command exists.**

Every check this server carries is a `runTests.sh` invocation, and which suites
that script offers changes between majors. So a check names a suite, and the
suite carries the range. `knowledge/test-suite-hints.json` declares the bound
once, and every task intent that names the suite in `-s <suite>` inherits it.
The same filter runs over the suite listing itself, and it carries its range
where it has one. A command the caller's checkout does not have is not a weaker
answer than none. It sends them to debug their own checkout for something this
server invented for another branch.

The prose documents cannot inherit anything, so the rule reaches them as a
restriction instead. A markdown document may name a suite only where every
covered major has it. A narrower one belongs in the hints, where `targetVersion`
can pick the right command.

## From

Seven checks that named a suite absent from at least one covered branch, found
during the unification of the obligation vocabulary. A 13.4 core contributor who
asked about labels got `runTests.sh -s checkIntegrityXliff`, which arrives in 14
(2026-07-30).

## Held by

- `bin/cli catalog:check`, which reads the range each suite holds on out of the
  `runTests.sh` of every covered branch. No test may reach `.checkouts/`, and
  the numbers these three tests filter by are a claim about that script.
