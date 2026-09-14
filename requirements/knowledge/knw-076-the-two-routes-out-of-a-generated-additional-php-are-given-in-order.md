---
id: R-KNW-076
title: 'The two routes out of a generated additional.php are given in order'
status: held
restsOn: [D-KNW-085]
heldBy:
  - HintsTest::theRoutesOutOfAGeneratedAdditionalPhpAreOrdered
---

# R-KNW-076 — The two routes out of a generated additional.php are given in order

**The second start is the route out of an absent `config/system/additional.php`.
The committed project-owned file is the second choice, named with its cost and
which repository may pay it.**

The two are not symmetrical. A second start ends the order problem for good and
leaves the file where DDEV writes it. A committed file puts installation state
under version control. A repository that deploys from the checkout already
carries that, and an extension repository does not. Offered as a coordinate
pair, the second reads as the route to a single-command start. A session that
takes it in an extension repository commits `config/` and sees the commit
rejected.

## From

`feedback/2026-08-24-140222` (2026-08-24), a session that set a DDEV development
installation up for a TYPO3 extension on TYPO3 14.3.6. The checklist item that
answered its case told it to leave the file to DDEV. The item beside it offered
the committed file with no condition on it. It took the second, wrote a
project-owned `additional.php`, set `disable_settings_management: true`, and
un-ignored the path. The user corrected it against a reference repository that
does what the first item says.
