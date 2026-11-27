---
id: D-SKL-112
title: A skill opens on its scope and a list of its steps
date: 2026-11-27
status: open
coveredBy: []
---

# D-SKL-112 — A skill opens on its scope and a list of its steps

**A skill says what it covers and where it stops in its first paragraph, then
lists its steps before their detail. Its prose uses plain words, lists and
headings, so a person can read it too.**

The maintainer asked on 2026-11-27 for skills a person can read. The pilot
rewrote `skills/base.md`, `typo3-core-patch-checkout` and its references.

## Evidence

- Every skill stated its scope in its last lines. `typo3-core-patch-checkout`
  stated it at line 363 of 386.
- Across `skills/`, "carry" stood 163 times, "owe" 69 times and "owns" 85 times.
  The maintainer named "owns" as unclear. It meant the task of a skill, the
  answer of a tool and the files of another skill.
- The deprecation sweep of the base was one list item of about ninety lines.
- The pilot cut `typo3-core-patch-checkout` from 3918 to about 2700 words. The
  chain case moved to `references/chain.md`.
- `SkillTest` holds many sentences word for word. The pilot changed some forty
  assertions to the new wording and kept what each one checks.

## Decided

- The scope sentence is "This skill covers …", and
  `SkillTest::everySkillSaysWhatItCovers` holds it. It stands at the start of
  the skill, which no test checks.
- A step gives its instruction first and its reason after it. The id of a
  decision replaces the story of the session behind it.
- An enumeration is a list. Items with more than one sentence have a blank line
  between them. A section with parts has subheadings.
- A rare case moves to a reference. The step names the reference together with
  the trigger to read it.
- The base asks for the calls a task needs. Steps 1 and 3 hold for every task
  (`D-SKL-034`). Steps 2, 4 and 5 each state their own condition.
- The report line is `Not reached: <step> — <why>`.
- `coveredBy` is empty. Whether a sentence is clear is a read, not a property a
  test can find.

## Wrong if

- A session skips `references/chain.md` on a chain the old body covered. Then
  the move into a reference cost the behaviour.
- A session leaves out step 2, 4 or 5 where the step would have found something.
  Then "the calls a task needs" reads as an invitation, which `D-SKL-034` warned
  against.
- A remaining skill cannot take this shape without a loss of a tested behaviour.
