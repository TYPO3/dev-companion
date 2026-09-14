---
id: R-ANS-006
title: 'A miss says what there would have been to find'
status: held
heldBy:
  - ForgeTest::aMissNamesTheEnumerationAsACallToCompose
  - ForgeTest::aMissNamesWhatEachWordReachesOnItsOwn
  - HintsTest::aHintCanBeAskedForByItsIdInsteadOfGuessedAt
  - HintsTest::aMissNamesWhatThereWouldHaveBeenToFind
  - HintsTest::anIdThatDoesNotExistIsAnsweredWithTheOnesThatDo
  - KnowledgeTest::aMissInsideTheCoreNamesTheWords
  - KnowledgeTest::aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt
  - KnowledgeTest::aSubsetIsNamedInTheWordsTheQueryWasWrittenIn
  - KnowledgeTest::whatAMissOffersToAskAgainWithReturnsSections
  - LabelSearchTest::aQueryNoResourceHoldsWholeIsToldSo
  - LabelSearchTest::aResourceHoldingNothingNamesTheResourcesThatDo
  - LabelSearchTest::anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach
  - PackageSourcesTest::aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit
  - PackageSourcesTest::aMissNarrowedByAVersionOpensWithTheVersionThatEmptiedIt
  - PackageSourcesTest::whereNoTwoWordsMeetInOneEntryThePerWordReachIsWhatToAskWith
---

# R-ANS-006 — A miss says what there would have been to find

**A lookup that returns nothing says what there would have been to find, and a
caller can ask for what it names outright.**

`typo3_hint_lookup` lists the hint ids of the searched domains on every miss and
accepts one as `id`. So a caller can tell "your words did not match" from
"nobody wrote this down" without a second phrase.

## From

A query that named XLF, labels and language files returned the TCA hint and
nothing else. Nothing showed that a Language Files hint existed (2026-07-29).
