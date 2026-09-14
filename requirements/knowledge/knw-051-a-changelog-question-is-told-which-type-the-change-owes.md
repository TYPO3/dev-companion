---
id: R-KNW-051
title: 'A changelog question is told which type the change owes'
status: held
restsOn: [D-KNW-039]
heldBy:
  - KnowledgeTest::aChangelogQuestionIsToldWhichTypeTheChangeOwes
---

# R-KNW-051 — A changelog question is told which type the change owes

**A caller who asks this server about a changelog entry learns which of the four
types the change owes, by the rule that separates them.**

Where the file goes and what its name is are mechanical, and `checkRst` tells a
session that gets them wrong. The type is the one part no check reports. A
`Feature` file where the change was `Important` passes every suite, and a review
finds it, or nobody does. So the clause per type that separates them is what the
answer has to carry. The list of four says nothing about which one the session
writes.

## From

`feedback/2026-08-02-145315` (2026-08-02), a session that produced a core patch
for Forge #105403. It settled the type from a read of neighbour entries. The
`changelog` intent of `knowledge/task-intents.json` had told it to write the
file "as in the neighbouring files". The same intent and
`knowledge/documents/typo3-commit-messages.md` both named a `Task-` prefix. No
branch's `Build/Scripts/validateRstFiles.php` accepts it and no entry in
`.checkouts/12.4`, `13.4`, `14.3` or `main` carries it.
