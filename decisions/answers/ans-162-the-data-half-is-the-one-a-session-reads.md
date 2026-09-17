---
id: D-ANS-162
title: The data half is the one a session reads
date: 2026-09-17
status: open
coveredBy: []
---

# D-ANS-162 — The data half is the one a session reads

**The server keeps both halves of every answer, and a client that hands the
model the data half alone hands it the preferred one.** The tokens the data
costs above the text are accepted for that.

`D-EVI-011` measured that Claude Code hands the model `structuredContent` and
drops the text, at 22 percent more tokens for one lookup. That left the question
which half a session should read, and `T-260917-cffb` carried it with three
options. The maintainer answered it on 2026-09-17.

## Evidence

- The maintainer's answer, in their words: the session reads what it can process
  better, and where the structured data is better it is preferred even at a
  slightly higher token cost.
- `R-ANS-002` already demands that nothing a caller needs in order to act lives
  in the text alone. So the data half is written to be read alone since
  2026-07-29, and the feedbacks behind that requirement came from Claude Code.
- opencode refuses an answer without `structuredContent` once a schema is
  declared, `D-EVI-011`. So a server that stopped to send the data half for one
  client would break the other.

## Decided

- **Both halves stay, unchanged, for every client.** No branch on
  `clientInfo.name`, no answer shape per client.
- **The data half is the answer a session acts on**, and the text is the same
  answer for a client that hands over text. `ToolResult` says so. The rule for a
  change to an answer follows from it: a fact goes into the data first, and into
  the text where the text renders it.
- **The cost is accepted as measured**, and `bin/cli tools:measure` prints it
  per tool, worst first. A trim of the data half is a trim of a field nobody
  composes on, not a move of a fact into the text.

## Assumed

- That a session composes better on a typed record than on prose of the same
  facts. Nothing here measures that. The measure is the sessions that read the
  data alone and reached the right conclusion, which is what `META-04` holds.

## Wrong if

- A feedback reports a conclusion a session reached from the data that the text
  would have prevented. Then a fact lives in the text alone, which `R-ANS-002`
  forbids, and the answer is that fact in the data rather than this entry.
- The share the data costs above the text grows past what a session accepts. It
  is per tool, and `tools:measure` prints both halves.
