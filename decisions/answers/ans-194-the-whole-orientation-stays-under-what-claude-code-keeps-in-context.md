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
- Measured on 2026-10-30 against `main` at `11d82bce`, the whole answer through
  `Registry::call()`: 65,786 characters of data and 60,473 of text. Both were
  over the threshold. `covers` held 35,258 characters of the data, and its 45
  `depth` texts about 22,000 of those.
- After the change, at `843c9a52`: 44,746 characters of data and 39,439 of text.
  Every entry stays, with its topic, its tools, its source and its scope.
- Measured on 2026-10-30 with
  `bin/cli tools:tokens typo3_server_scope '{}' --runs=2` on Haiku 4.5, against
  `11d82bce` and against `14e2fde6`, plus three runs of the "as served" mode on
  `14e2fde6`. Haiku did not call the tool in seven of the fifteen sessions. The
  table holds the sessions that called it.

| State      | Mode                 | The model received                          | Tokens           |
| ---------- | -------------------- | ------------------------------------------- | ---------------- |
| `11d82bce` | as served            | a 2,246-character preview and a file path   | 637              |
| `11d82bce` | without outputSchema | the same preview, then a `Read` of the file | 655, then 16,111 |
| `14e2fde6` | as served            | the whole answer, 44,905 characters of JSON | 11,603           |
| `14e2fde6` | without outputSchema | the whole answer, twice                     | 11,595, 11,589   |

- Before, the preview said "Output too large (64.3KB)". One session answered
  from it and never read the answer. The other paid a second call and 16,766
  tokens for the whole answer. After, every session that called the tool read
  the whole answer in that one call.

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

