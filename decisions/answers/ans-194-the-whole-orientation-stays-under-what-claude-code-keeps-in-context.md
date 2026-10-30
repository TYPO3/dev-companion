---
id: D-ANS-194
title: The whole orientation stays under what Claude Code keeps in context
date: 2026-10-30
status: open
coveredBy:
  - ScopeTest::theWholeOrientationFitsWhatAClientKeeps
---

# D-ANS-194 — The whole orientation stays under what Claude Code keeps in context

**The whole `typo3_server_scope` answer stays below 50,000 characters in each
half. The prose in `knowledge/server-scope.json` is shorter, and the answer
keeps every part.**

Claude Code saves a longer result to a file and hands the model a path. The
model then pays a call to read what the server meant to say. `D-FBK-020` counts
that call as the cost.

## Evidence

- **Read on 2026-10-29** in the Claude Code documentation, section "MCP output
  limits and warnings". A text result over 50,000 characters goes to a file,
  unless the tool declares `_meta["anthropic/maxResultSizeChars"]`. A warning
  appears at 10,000 tokens.
- `D-EVI-011`: Claude Code hands the model `JSON.stringify` of
  `structuredContent`. opencode hands over the text, or the data in Code Mode.
- Measured on 2026-10-30 against `main` at `11d82bce`, the whole answer
  through `Registry::call()`: 65,786 characters of data and 60,473 of text. Both
  were over the threshold. `covers` held 35,258 characters of the data, and its
  45 `depth` texts about 22,000 of those.
- After the change, on this branch: 44,746 characters of data and 39,439 of
  text. Every entry stays, with its topic, its tools, its source and its scope.

## Decided

- `covers.depth`, `doesNotCover.why` and `doesNotCover.instead`, the longest
  `routing` entries and `purpose` are shorter. Each keeps what a caller decides
  on and drops the detail the named document or tool already holds.
- `ServerScope::ANSWER_BUDGET` is the measure, and
  `ScopeTest::theWholeOrientationFitsWhatAClientKeeps` holds both halves to it.
  A new entry has to displace words, as `R-ANS-013` makes it for the
  instructions.
- The default stays the whole answer, `D-ANS-087`.
- Rejected: `_meta["anthropic/maxResultSizeChars"]`. It keeps the answer whole
  in Claude Code alone and saves no token.
- Rejected: a narrower default. A caller chooses sections in its least informed
  moment, and `D-ANS-087` stands on that.

## Assumed

- That Claude Code counts the JSON as `JSON.stringify` writes it, with slashes
  and unicode unescaped. The test counts it that way.
- That the shortened prose still routes a caller to the right tool. The detail
  it dropped stands in the document or the tool description it names.

## Wrong if

- A session reports that it called the wrong tool after it read a shortened
  entry, and the dropped detail would have prevented that.
- Claude Code changes the threshold, or another client documents a lower one.
  Then the budget moves to that number.
