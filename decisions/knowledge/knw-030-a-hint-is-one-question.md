---
id: D-KNW-030
title: 'A hint is one question'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aRelationInADatamapSaysWhatTheParentColumnEndsUpHolding
  - HintsTest::readingRecordsIsAnswered
  - HintsTest::theSeedingAdviceCarriesTheStepsItAsksFor
  - HintsTest::theSeedingAnswerNamesImpexpAsTheWayATreeIsEstablishedAgain
---

# D-KNW-030 — A hint is one question

**A subject splits along the questions a caller arrives with rather than along
the subsystem. DataHandler becomes six hints, and a read of a record becomes
`persistence-reading`.**

`datahandler-persistence` was one hint over eight statements — the datamap,
relation resolution, record placement, the backend user, and three sentences of
patch-review obligation. Which of them a caller got depended on how much of its
329 words their words happened to cover.

## Evidence

- The subject had already split and nobody had said so. DataHandler statements
  sat in `datahandler-persistence`, in `sitepackage-initial-content` (the boot,
  the backend user, the datamap order) and in `core-tests` (the test base
  class). The axis was the file somebody had open, not the question.
- The gap that axis hid is `R-KNW-045`. The vocabulary for a read of records was
  on a hint with no statement about it. The corpus held one sentence that names
  any read API.
- After the split, `hints:coverage` reports a mean body of 278 against the
  300-word ceiling, up from 3 words of headroom to 22. No statement went. The
  General share falls from 63% to 59% for the same reason: six reachable PHP
  hints displace what the always-on bucket used to supply.
- `hints:probe "how do I read records without the hidden and starttime restrictions"`
  answers `persistence-reading` first.
  `"seed a page tree with content programmatically"` answers
  `datahandler-seeding`. Neither query reached anything about its subject
  before.

## Decided

- Six hints, one question each. What DataHandler is and why not an INSERT
  (`datahandler-basics`), the datamap (`-writing`), what it does to a relation
  field (`-relations`). Where a record lands (`-placement`), content that exists
  nowhere yet (`-seeding`), cover for any of it (`-testing`).
- Reading is not DataHandler and gets its own file. The write path is one API
  and the read path is a QueryBuilder with restrictions on it plus two overlays
  afterwards. The two in one file is what produced the gap.
- impexp stays with the ship subject, and the seed hint says why. A page tree
  that has to exist again is what the export is for. A seed script is for
  content that exists nowhere yet (`R-KNW-046`). The corpus had it the other way
  round — the script was the product and the export its residue.
- The entry hint names the other five. A family that a caller lands in the
  middle of has to say what else there is. Otherwise the split trades one hint
  nobody finds for five.
- The bare `DataHandler` pattern is on the entry hint and the test one only. On
  all six it made them tie on the curated score and crowd the answer. A core
  test question came back with four datamap hints and lost `core-tests` off the
  end of the limit. A pattern shared by every hint of a family discriminates
  nothing.

## Assumed

- Six is the granularity, not a step towards more. Each of the six is a question
  somebody has asked in a feedback or a scenario. A seventh would have to name
  the question it answers.
- The statements moved out of `sitepackage-initial-content` and `core-tests` are
  not missed there. Both keep their own subject whole, and the words a seed
  question uses reach the seed hint.

## Wrong if

- A DataHandler question comes back with three of the six and the one it needed
  is not among them. That is the family in its own way, and the fix is the
  vocabulary rather than a merge.
- `datahandler-basics` becomes the answer to every DataHandler question, which
  would mean the five specific ones are unreachable and the split is cosmetic.
- Somebody asks how to read a record and gets `persistence-reading` plus four
  write-path hints. `persistence` is a word both halves carry.
