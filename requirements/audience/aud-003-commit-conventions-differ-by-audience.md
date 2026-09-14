---
id: R-AUD-003
title: 'Commit conventions differ by audience'
status: held
restsOn: [D-GUI-017]
heldBy:
  - CommitMessageTest::outsideTheCoreATrailerTheCallerWroteIsStillKept
  - CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded
  - CommitMessageTest::outsideTheCoreTheSubjectAndBodyRulesStillHold
  - CommitMessageTest::theSecurityKeywordIsTheRepositoryOwnOutsideTheCore
---

# R-AUD-003 — Commit conventions differ by audience

**Commit conventions differ by audience.**

The subject line and body rules transfer. The `Releases:` trailer, the Forge
issue number and the Gerrit `Change-Id` are core rules and belong to core work
only. A site or extension repository has its own workflow, and the guide has to
work there without trailers that mean nothing in it.

What it writes there is what the caller passed. `Resolves:` and `Related:` are
the trailer names a TYPO3 repository on GitHub carries its own issue numbers in.
So the guide writes an issue the caller hands it out in either workflow. To
demand no trailer is not to add none, and every sentence that describes the
project workflow says which of the two it means (`D-GUI-017`).
