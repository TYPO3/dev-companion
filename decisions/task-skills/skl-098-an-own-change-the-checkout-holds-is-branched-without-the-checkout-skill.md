---
id: D-SKL-098
title: An own change the checkout holds is branched without the checkout skill
date: 2026-10-22
status: open
coveredBy:
  - SkillTest::anOwnChangeTheCheckoutHoldsIsBranchedWithoutTheCheckoutSkill
---

# D-SKL-098 — An own change the checkout holds is branched without the checkout skill

**Where the checkout holds the current patch set's commit of the requester's own
change and the tree is clean, `typo3-core-patch-development` branches it in
place. It invokes `typo3-core-patch-checkout` for every other patch set.**

## Evidence

- [`feedback/2026-10-22-105711-three-chained-skills`](../../feedback/2026-10-22-105711-three-chained-skills-for-an-own-change-that.md).
  A session reviewed and finished the user's own change 96975. The review skill
  handed over to the development skill. That skill sent it on to the checkout
  skill, which did one thing: `git switch -c review/96975 <commit>`. The three
  skill texts cost many thousand tokens.
- Step 4 of the ladder, wording. The development skill already sent only a
  change "this checkout no longer holds" through the checkout skill. The bold
  sentence above it names every patch set on the review server. The exception
  stood in the third sentence and never as an act.
- `D-SKL-022` records three sessions that did not cross into the checkout skill.
  So the crossing stays an act, and the short path names the one case it costs
  without return.

## Decided

- The short path needs three facts a session can check. The change is the
  requester's own, `git cat-file -e` finds the commit the lookup gives, and the
  tree is clean. Each fails safe: the crossing stands for every other case.
- The branch keeps the checkout skill's name, `review/<change number>`. The
  session that reported names it as what to keep.
- The feedback's second ask is not decided here. It asks the review skill to say
  what the development skill still owes after a review of the same change. The
  development skill makes the final review a step, because a session skipped it
  once. The reporting session skipped it too, on cost. Which of the two weighs
  more is the maintainer's answer.

## Wrong if

- A session on the short path misses what the checkout skill would have caught,
  such as a target branch that moved under the patch set. Then the three facts
  are too few, and the short path needs the checkout skill's second check.
- A session takes the short path for somebody else's change. Then "your own"
  needs the owner check the checkout skill makes on a chain.
