---
id: D-KNW-035
title: The corpus and the tool that answers from it are called hints
date: 2026-08-03
status: open
coveredBy:
  - ToolAnswersTest::everyToolTheTableDrivesHasARecordedAnswer
  - ToolNamingTest::everyToolNameWrittenInTheKnowledgeBaseIsRegistered
  - ToolSurfaceTest::theIndexReachesEveryToolAndTheDirectoryHoldsNoOther
---

# D-KNW-035 — The corpus and the tool that answers from it are called hints

**The corpus is `knowledge/hints/`, the class that loads it is
`Knowledge\Hints`, and the tool that answers from it is `typo3_hint_lookup`.**

`architecture` no longer described what the corpus holds, and it was the only
place in the repository that still used the word for it.

## Evidence

- The corpus answers label rules, upgrade command sequences, test-harness setup,
  browser tests and changelog conventions. None of those is architecture, and
  the outward `topic` in `knowledge/server-scope.json` listed them under "Core
  architecture conventions".
- `hint` was already the word everywhere else. The glossary defines it, the
  `hints:probe` and `hints:coverage` subjects use it, `HintsTest` carries it,
  and `knowledge/test-suite-hints.json` is a second corpus of them.
- `typo3_rule_lookup` already answers the core contribution rules. So
  typo3_convention_lookup, the alternative the owner had on the table, would
  have put a synonym next to it in the registry.

## Decided

- The directory, the class and the tool name change in one commit. A rename of
  one of the three alone is the two-names-for-one-thing this repository spends
  commits to remove.
- `Result\Hints` became `Result\MatchedHints`, because the corpus takes the
  plain name and a file that uses both would otherwise need an alias.
- `typo3_task_guide` answers with `hints` where it answered with
  `architectureHints`. A client that validates against the old field breaks,
  which is the price here. The tool the field is about got its new name in the
  same commit. So a client that reads the guide's brief needed a correction
  anyway.
- What recorded a call under the former name keeps it and says so. The forward
  runs in `scenarios/runs/`, the recorded answer under
  `documentation/clients/tool-answers/`, the archived feedback. A recording says
  what it is of (`D-DOC-006`).
- The decisions that name `knowledge/architecture-hints/*.json` files stay as
  they are. Those paths were already stale from the move between files before
  this, and an entry is a dated record rather than a description of today.

## Assumed

- A client that calls the old name gets an error it can read. It gets no empty
  answer it mistakes for "nothing here says anything about this".
- The installed skills reach their projects with the next release. Until then a
  copy installed earlier routes to a tool this server no longer offers.

## Wrong if

- A session reports `typo3_hint_lookup` as unknown, which would mean a client
  holds a tool list from before the rename and nothing told it.
- A caller's brief comes back without hints because it reads `architectureHints`
  from the payload.
- The word `architecture` returns for part of this corpus. That would mean the
  subject it fell for is back and one of the two words is wrong.
