---
id: R-ANS-035
title: 'An answer that names a target branch names the lines that take a patch'
status: held
restsOn: [D-ANS-104, D-ANS-073]
heldBy:
  - GerritTest::theBranchesThatTakeAPatchStandBesideTheOneAChangeTargets
  - GerritTest::theListIsAnsweredWhateverTheReviewServerDid
  - GerritTest::thePlacementNamesTheToolThatReadsATrailerAgainstThoseLines
  - ReleaseLinesTest::theDayRegularSupportEndsIsReadableWithoutTheSentence
---

# R-ANS-035 — An answer that names a target branch names the lines that take a patch

**An answer that names the branch a change targets names the branches that take
a patch today. It leaves to the author which of them this change belongs on.**

A checkout answers neither half. `git branch -r` reaches back to `TYPO3_3-6` and
says nothing about which of those is still maintained. So a caller with one
branch in hand and a need for the rest rebuilds the list from remote branches
and changelog folders. That inference holds in a full clone and fails in a
shallow or a stale one.

## From

A session that reviewed Gerrit change 95179 and rewrote its `Releases:` trailer
after both reviewers refused the 13.4 backport. `typo3_gerrit_lookup` told it
the change targets `main` and nothing about the siblings.
`typo3_project_describe` told it the checkout is 15.0.0-dev. It settled the rest
with `git branch -r | grep -E "origin/1[3-5]"` and a listing of
`typo3/sysext/core/Documentation/Changelog/` against a remote 59 commits behind
(`feedback/2026-08-24-122348`, 2026-08-24).

## Held by

`typo3_gerrit_lookup` is the carrier, because it is the one answer here that
names a branch. The other half of the demand, that the answer stops at what the
lines are, is
[`D-ANS-073`](../../decisions/answers/ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md).
A bug fix and a task reach the development line and the one release line back
from it. To name an older one is a claim about severity the author makes rather
than a consequence of a list.
