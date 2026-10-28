---
id: D-ANS-191
title: What an answer owes the caller stands in the data
date: 2026-10-28
status: open
coveredBy:
  - ToolContractTest::everyToolTheTextNamesStandsInTheData
---

# D-ANS-191 — What an answer owes the caller stands in the data

**A sentence that tells the caller what to do stands in the data as well as in
the text. A sentence about one value takes a string field beside that value,
named `<value>Owed`. Every other one stands in `owed`, a list every answer
carries. The handler puts the stale and behind notices at its head.**

## Evidence

- `D-EVI-011` read in Claude Code's code that the client hands the model the
  data half and drops the text. The second `REVIEW-01` run got the surface list
  without the sentence that says what to do with it. `surfacesOwed` fixed that
  one case.
- A sweep of `src/` on 2026-10-28 found the same gap in the handler's two
  notices and in fourteen tools. The sweep stands in the history of
  `T-261028-f659`.

## Decided

- **The maintainer chose both shapes on 2026-10-28.** A field beside its value,
  where the sentence is about that value. One shared list for the rest, which
  `ToolResult::create()` fills and `Schema::withOwed()` declares on every tool.
  One list for every tool means that a client reads one field and no tool can
  name it in other words.
- **The text keeps the sentence.** A client that hands over the text half,
  opencode among them, reads it where it stood before.
- **A sentence whose substance a field already carries stays out.** A retry
  where the data holds the counts that narrow it is that case.
- **The test holds the tool names.** `everyToolTheTextNamesStandsInTheData`
  fails where a text names a `typo3_*` tool that the data does not. A command or
  a skill name that the text alone gives is not held. No check can tell an
  instruction from a description in prose.

## Wrong if

- A Claude Code session acts on a sentence that stands in the text alone, and a
  run shows it.
- A client hands the model both halves. Then each owed sentence is paid twice,
  and `D-ANS-162`'s cost doubles for those sentences.
