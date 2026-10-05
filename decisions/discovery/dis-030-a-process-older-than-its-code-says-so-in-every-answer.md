---
id: D-DIS-030
title: A process older than its code says so in every answer
date: 2026-10-05
status: open
coveredBy:
  - CodeAgeTest
---

# D-DIS-030 — A process older than its code says so in every answer

**Where the PHP below `src/` or `composer.lock` changed after the server
started, every tool answer opens with `CodeAge::NOTICE`. The notice says to
restart the MCP server.**

## Evidence

- `feedback/archive/2026-10-05-115026-a-guide-names-typo3-issue-report-guide-but-the.md`.
  `core/contribution/reporting-an-issue` named `typo3_issue_report_guide` an
  hour after the tool was committed. The session's server had started before, so
  it served no such tool, and `typo3_server_scope` said nothing about it.
- The knowledge, the guides and the skills are read at the call. The tool list
  and the PHP that answers it are fixed at the start. So the two halves drift
  apart on every pull.
- Measured on 2026-10-05 over the 213 files below `src/`: 2.3 ms per check.

## Decided

- A fingerprint of path, size and change time per file, taken at the start and
  compared at each call. No git, because a dependency install has no `.git`.
- The notice stands in front of the text half. The data half keeps its schema,
  so a client that validates it sees no new field.
- No `notifications/tools/list_changed`. The process cannot load the new code,
  so a new list would offer tools the old code cannot answer.
- Where the checkout stands against its upstream is a question of its own, the
  card `T-261005-c73d`.

## Wrong if

- A session reports the notice where nothing changed, such as a checkout whose
  file times move on a sync without an edit.
- A session restarts as told and the tool is still missing.
