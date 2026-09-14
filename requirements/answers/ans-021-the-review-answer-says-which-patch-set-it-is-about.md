---
id: R-ANS-021
title: 'The review answer says which patch set it is about'
status: held
restsOn: [D-ANS-033]
heldBy:
  - GerritTest::aChangeWithoutARevisionSaysSo
  - GerritTest::theAnswerCarriesThePatchSetACheckoutIsHeldAgainst
---

# R-ANS-021 — The review answer says which patch set it is about

**A change read from the review server carries the patch set that is current and
the commit it is.**

A change is a series of patch sets, and a checkout is one commit. Without the
commit nothing in the answer says which of the two the reader holds. A review of
a superseded revision is wrong in every finding after it. The number is what a
person reads; the commit is what settles it against a local `HEAD`.

## From

A core patch review of 9f6c6eb9093 (#110359) credited the Change-Id lookup with
the proof that it held the current patch set (`feedback/2026-08-03-144316`).
Neither half of that answer said so. The patch set the session reported came out
of the automated Gerrit note that `typo3_forge_lookup` returned about the issue.
That is a different call, and one that exists only because Gerrit happens to
comment there.

## Held by

- The sentence the text carries beside them, hold the commit against
  `git rev-parse HEAD`, is not guarded. The tool renders it, builds its own
  reader and reaches the network as it runs, and no test in this repository
  drives one that does.
