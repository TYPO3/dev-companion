---
id: R-ANS-029
title: 'An answer that names a record says enough of it to judge whether to open it'
status: held
restsOn: [D-ANS-064, D-ANS-069]
heldBy:
  - ForgeTest::aQueryUrlNamesNoChangeAndIsNotReportedAsOne
  - ForgeTest::aRelationCarriesEnoughOfTheOtherIssueToJudgeWhetherToReadIt
  - ForgeTest::aRelationTheFillCouldNotReachIsStillTheRelationThatWasFiled
  - ForgeTest::aReviewChangeIsLiftedOutOfTheProseThatCarriesIt
  - ForgeTest::aRowSaysWhetherTheReviewServerHoldsAChangeThatNamesIt
  - ForgeTest::theRelationsOfAWholePageAreFilledInOneCall
  - GerritTest::aChangeCarriesTheIssuesItsCommitMessageNames
---

# R-ANS-029 — An answer that names a record says enough of it to judge whether to open it

**Where an answer names another record this server can read, it carries what a
caller needs to decide whether to read it. The identifier alone is not that.**

An identifier with nothing beside it costs one call to evaluate, so a caller
with several of them evaluates none. The caller then skips the record that
mattered for the same reason as the ones that did not. The answer looks complete
while the caller acts on less than it received.

## From

`feedback/2026-08-07-231225`, 2026-08-07. `typo3_forge_lookup` answered issue
15984 with four relations as `{issue, relation}` pairs. The session spent no
reads on them and told the user nothing about them. One was `#32756`, "Massive
Memory Leak in 4.5.8+ / 4.6", marked `precedes`. That is the issue of the 2012
revert and the record that answers what a fix would cost. It surfaced afterwards
out of a git commit message.

`feedback/2026-08-07-231146` is the same shape one field over. The Gerrit change
references an issue's journal names are prose inside a note. So the session
never called `typo3_gerrit_lookup` and never even loaded its schema.

**Built on 2026-08-08.** A relation carries the subject, tracker and status a
search hit already carries. One bulk read of
`issues.json?issue_id=…&status_id=*` fills them for the whole set. The answer
parses the change references out of the journal into a `reviews` field that
names `typo3_gerrit_lookup`. Each carries the change number, the Change-Id and
the patch set a note gave it. It claims nothing about a change's state: a note
says what was true the day its author wrote it.

**Widened on 2026-08-09** to the enumeration, where the same two records are
what a backlog row is chosen on. A row carries its relations, filled by the one
bulk read the issue answer already made. It carries the changes on the review
server whose commit message names it, asked of Gerrit in one query per twelve
rows. The journal that carries a change reference is not in the index answer at
all (`D-ANS-069`).

## Held by

What no test reaches is the general form, every answer that names a record this
server can read. Tests hold the fields the feedback named; a further one that
arrives gets a test when somebody writes it.
