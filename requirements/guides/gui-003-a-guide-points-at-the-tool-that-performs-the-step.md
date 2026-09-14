---
id: R-GUI-003
title: 'A guide points at the tool that performs the step'
status: held
heldBy:
  - ScopeTest::theBriefPointsAtTheGuideForTheStepItEndsWith
---

# R-GUI-003 — A guide points at the tool that performs the step

**A guide that names a step points at the tool that performs it, in the answer
where the step appears.**

A session reads the routing table once, at the start. It takes the step hours
later, out of whatever the last answer listed.

## From

Four commit messages written in one session that never called
`typo3_commit_message_guide`. Its brief ended with "Summarize changed behavior",
and its next lookups never named the tool that does exactly that (2026-07-29).
