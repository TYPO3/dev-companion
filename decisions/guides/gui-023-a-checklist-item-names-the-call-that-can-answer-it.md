---
id: D-GUI-023
title: A checklist item names the call that can answer it
date: 2026-08-27
status: open
coveredBy:
  - HintsTest::theBugfixChecklistNamesTheCallThatSettlesTheReleaseBranches
---

# D-GUI-023 — A checklist item names the call that can answer it

**Where a brief asks for something the caller's checkout cannot supply and a
tool here can, the item names the call instead of the read.**

The bugfix checklist asked whether the defect also affects maintained older
release branches, and a core checkout regularly holds one branch.

## Evidence

- `feedback/2026-08-27-145507` reports exactly that substitution by hand. "I
  could not act on this from the checkout — I only have `main` — and I resolved
  the branch question from typo3_commit_message_guide instead, which told me
  that a BUGFIX goes to main and 14.3 and that naming 13.4 claims a severity I
  would have to justify. That was the better answer, and the checklist item
  pointed at work I had no way to do."
- The tool does answer it. Run again on 2026-08-27 for `changeType="BUGFIX"` and
  `workflow="core"`, the Releases caveat names the lines that can take a patch
  at all. It says an older one belongs there only where the severity earns it.
- The item as written names no source. Every other item of that checklist either
  states the rule or names the call. That is what made this one read as a read
  to go and make.

## Decided

- The item names `typo3_commit_message_guide` and keeps the half the tool cannot
  answer. Whether the defect is on those branches is the caller's read, and the
  item says a one-branch checkout cannot make it. Dropping that half would turn
  a question the caller owes into one the tool has answered.
- Not a condition on the item. The session's other suggestion is that items say
  what makes them apply, which is a change to every item rather than to this
  one. `T-260827-5c50` carries that and this entry does not pre-empt it.

## Assumed

- One session, and its account of what it did instead. That the substitution was
  the better answer is its judgement, and the tool's own text is what agrees
  with it here.
- That a core checkout usually holds one branch. It is the shape of this
  repository's own `.checkouts/`, one worktree per covered version, and what the
  session that reported had.

## Wrong if

- A session reports the item as a call it did not need, with the branches in
  front of it. The read would then be cheaper than the call for anyone at work
  across worktrees.
- A session takes the tool's answer as settled about which branches carry the
  defect. That is the half the item keeps. A session that acts on the Releases
  line alone would say the sentence did not hold it apart.
