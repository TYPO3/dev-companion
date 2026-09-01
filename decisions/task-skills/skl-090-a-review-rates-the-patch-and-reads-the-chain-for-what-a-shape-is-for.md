---
id: D-SKL-090
title: 'A review rates the patch and reads the chain for what a shape is for'
date: 2026-09-02
status: open
coveredBy:
  - GerritTest::theChainSaysWhatAShapeAboveItExplains
  - SkillTest::theReviewChecklistReadsTheChainBeforeCallingAShapeADefect
---

# D-SKL-090 — A review rates the patch and reads the chain for what a shape is for

**A structural finding is checked against the change stacked on this one before
it is reported, and the patch is still rated on its own.**

A review of core change 94652 reported four structural findings and the change
above it in the same stack refuted all four.

## Evidence

- `feedback/2026-09-01-210110`. The one-class subnamespace, the non-final
  renderer, the public resolver and the protected accessors were groundwork that
  94653 fills in, and each is defensible read against the change alone.
- The session had the answer in its first call. `typo3_gerrit_lookup` returns
  `chain` with every change read by name, and it listed 94653 with
  `thisChange: false`.
- What the answer did not say is what that entry is evidence for. Its own
  paragraph explains what a chain is and how it differs from the Change-Id
  relation, and stops there.
- No skill named it. `chain` appears in one skill body across `skills/`, in the
  asset build one, about something else.

## Decided

- The tool answer carries it, because that is where the session already was: a
  shape that reads as an oversight is what the next change in the stack uses,
  and the entries above are read before it is reported.
- The review checklist carries the step rather than the skill body. The failure
  is in what a finding owes, which is the checklist's subject, and the body
  keeps its routing shape.
- **The patch is rated on its own.** That is the maintainer's wording on
  2026-09-02, asked as one of two questions before the file was committed, and
  it is what the first draft got wrong: the chain says whether a shape is
  preparation, and it does not excuse the shape. What the follow-up explains is
  reported as a question to the author.
- The `Tests` surface says what the follow-up decides there too: a test pinning
  behaviour the next change rewrites is churn, one covering what the follow-up
  leaves alone lasts.
- **Whether a review reports that the change is the bottom of a chain is not
  decided.** The report asks for it, because rebasing such a change obliges
  everything stacked on it. Put to the maintainer the same day and left unsure,
  so nothing states it — a sentence nobody is sure of is not what goes into a
  file that lands in somebody else's project.

## Assumed

- That a reviewer who reads the chain entry reads the follow-up's file list as
  well. The entry carries the number, the status and the subject, so the file
  list is a second call this does not spend for them.

## Wrong if

- A review reports a structural finding the stack explains again, which would
  put the lever in the skill body's order rather than in the checklist.
- The step turns a real defect into a question because a follow-up happens to
  touch the same file, which is the failure this trades against.
