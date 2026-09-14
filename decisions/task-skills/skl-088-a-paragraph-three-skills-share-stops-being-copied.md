---
id: D-SKL-088
title: A paragraph three skills share stops being copied
date: 2026-09-01
status: open
coveredBy:
  - SkillTest::aParagraphThreeSkillsShareStopsBeingCopied
---

# D-SKL-088 — A paragraph three skills share stops being copied

**`skills/base.md` is the shared start of a task and nothing is the shared
ending, so two skills may copy a paragraph and the third gives it a home.**

The base came from five hand-written copies of one order. The argument stops at
the order because that is what the file's title names.

## Evidence

- Measured over the published bodies on 2026-09-01. One paragraph over 120
  characters stands identically in two of them, the 356 characters that hand a
  vulnerability over in `typo3-core-issue-triage` and
  `typo3-core-patch-development`. Nothing else stands twice, the pointer at the
  base aside, which every skill carries by contract.
- Two more are close and not identical. Those are the severity bands the two
  patch reviews share, and the paragraph on the form of a report that
  `typo3-extension-health` shares with `typo3-extension-patch-review`.
- What the base does not reach is an ending. A session reads every rule in it
  before it opens the checkout. It reads a stop at a vulnerability or the form
  of a report at the other end of the work.
- Few bodies rather than most name the endings that recur. Two skills end in
  public, three produce a report, and four close on a commit.

## Decided

- Two copies stay. The rest do not pay for a rule that holds for two of the
  published skills, and `D-SKL-026`'s listing arithmetic is what that budget is.
- The third copy is where it stops to be a copy. It moves into `skills/base.md`
  where it holds for every task, or into a shared reference the installer copies
  into the skills that name it.
- `SkillTest::aParagraphThreeSkillsShareStopsBeingCopied` fails on it, so the
  count comes off the files rather than off memory.
- Rejected: building the selective shared reference now. It costs the installer,
  the author contract and the tests behind both, against 356 characters that
  have not drifted.

## Assumed

- That identical text is what drifts. Two paragraphs that say one thing in two
  wordings are already apart, and the count does not read the near-duplicates
  above.
- That the paragraph is the unit. A shared rule written as one paragraph in a
  body and two in another is not read by it either.

## Wrong if

- Those 356 characters drift while they are two, which is the pair nobody
  watches.
- The third copy arrives reworded, so the check stays silent and one ending is
  written three times in three wordings.
