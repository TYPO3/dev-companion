---
id: D-KNW-138
title: A hint names its next call in a statement
date: 2026-09-01
status: open
coveredBy: []
---

# D-KNW-138 — A hint names its next call in a statement

**A hint that implies a tool or a scope of repair says so in one of its own
statements, and carries no field for either.**

Three feedback asked for both as data beside the statements, and the corpus
already answers them as prose in a quarter of its entries.

## Evidence

- 26 of 160 hints name a `typo3_` tool in their statements, read on 2026-09-01 —
  `fluid-templates` names four. `ToolNamingTest` already holds every one of
  those names to the registry, so the prose is guarded the way a field would be.
- Both hints the feedback named were half right. `browser-tests` names
  `typo3_test_run_guide` in the statement about looking at a defect, and
  `frontend-dataprocessors` named no tool at all until this change.
- The scope of a repair is the same shape.
  `verifying-a-change-against-the-installation` states it once, for every
  change: the search that found a defect is run again for every sibling before
  the fix is reported.
- The field that does exist is derived rather than written.
  `Documents::forHint()` inverts a document's own `hints:` list, so the crossing
  is one statement in one file — `D-KNW-008`. A tool cannot declare which hints
  it belongs to that way, so the same field here would be a list somebody keeps
  by hand in 160 places.

## Decided

- No `tools` field and no `whereElse` field on a hint. What a hint implies is
  said in the statement that implies it, where the matcher searches it and
  `ToolNamingTest` holds the name.
- `D-ANS-129` is the same move on the other surface and came out the other way:
  a tool's answer names the next call in prose it composes per call, and a hint
  is a file somebody writes. The two are one idea and two questions.
- `frontend-dataprocessors` gains the sentence it was missing: the resolved
  value is `typo3_configuration_lookup`'s.
- The general form of the repair scope stays one statement in one hint rather
  than a line on each. Where a particular entry's unit is surprising — an option
  read per table, a value stored per row — that entry says so itself.

## Assumed

- That a caller reads a statement naming a tool as a call to make. The
  `documents` array is the one place this was measured, and it is a list rather
  than a sentence.

## Wrong if

- A sweep finds hints whose subject plainly implies a call and which name none,
  often enough that writing it per entry is what a field would have forced. The
  count above is the baseline: 26 of 160.
- The same name has to be corrected in twenty statements after a rename, which
  is the cost a derived field would not have.
