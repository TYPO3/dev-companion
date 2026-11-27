# A fix that somebody reverted

Read this when the issue answer shows a fix that somebody merged and then
reverted. A relation marked `precedes` or `duplicates` comes with its subject,
and `reviews` names every change the journal mentions. So this history shows
before you open the checkout.

Then "still happens" is not the answer people wait for. "It still happens" and
"somebody tried and reverted it" can be the same verdict about opposite
situations. Two questions tell them apart: why did somebody revert the fix, and
does that reason still hold?

## What to read

- **The related issue the revert was filed under.** Only that issue gives the
  reason. The revert commit says what it reverted. The issue says what the fix
  cost.

- **The attempt itself.** No lookup here returns it. The issue answer has the
  change numbers, and the Gerrit search by issue number has their state. What
  remains is the diff: fetch the patch set into the checkout and read it.

  The ref and the remote it is on are in two sections of one page. That remote
  is not the one a core checkout fetches from. So read the page whole: call
  `typo3_rule_lookup` with `documentId="core/contribution/gerrit-workflow"`. It
  is also served as `typo3://guides/core/contribution/gerrit-workflow`.

  An abandoned change is a verdict somebody wrote down. Its diff is the cheapest
  description of a fix against a current core. Read the patch set, but put it
  onto no branch. A rebase of an abandoned attempt writes the patch instead of
  judging the issue.

- **A `reviews` entry the issue search left out.** Call `typo3_gerrit_lookup`
  with its change number. The issue search finds a change whose commit message
  names the issue. So a missing entry is one whose branch, patch set and status
  nothing else has given you.

- **Every production caller of the method the reverted patch changed**, in the
  checkout. A fix for one call site is a different proposal from one that
  changes a shared path.

- **Whether the path the revert names still goes through that method.** A
  subsystem rebuilt since then turns the old objection into history.

## The general rule

**A reverted core fix becomes re-attemptable when somebody has rebuilt the
shared consumer that made it expensive. It also does when the caller set has
shrunk to the one site the fix needs.** Neither half shows on the issue. Each is
one grep and one file in the checkout.

## What to report

Report what a maintainer needs before the issue can move. That is the constraint
the last attempt broke. It is not a design and not a patch. Code that respects
the constraint is the work of `typo3-core-patch-development`.
