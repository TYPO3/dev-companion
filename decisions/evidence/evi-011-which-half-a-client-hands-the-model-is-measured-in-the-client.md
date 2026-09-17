---
id: D-EVI-011
title: Which half a client hands the model is measured in the client
date: 2026-09-17
status: open
coveredBy:
  - ToolProxyRelayTest
  - ToolProxyTest
  - ToolSurfaceTest::theCompactFormIsWhatAClientHandsTheModel
  - ToolSurfaceTest::whatAToolAnswersWithIsCountedInBothHalves
  - ToolSurfaceTest::whatAToolDeclaresIsCountedInBothHalves
  - ToolTokensTest
---

# D-EVI-011 — Which half a client hands the model is measured in the client

**A client hands the model one half of an answer, and `bin/cli tools:tokens`
reads which half and what it cost off the client's own trace.** Claude Code
hands over the data half and drops the text. opencode hands over the text half.
Neither hands over the output schema, and opencode refuses an answer without
`structuredContent` once a schema is declared.

A thread on r/mcp asked whether `outputSchema` is a feature or a token tax. The
answer depends on what a client does with it, and no client documents that. So
the session read two clients' code and then measured both.

## Evidence

- `tools/list` from this server on 2026-09-17: 301,784 bytes over 31 tools, of
  which 220,476 are output schemas, 73 percent. `bin/cli tools:measure` prints
  it. `typo3_project_describe` is the largest at 27,853 bytes of schema beside
  1,483 of name, description and input schema.
- Claude Code 2.1.273, read off the binary. The tool definition it sends the API
  is `{name, description, input_schema}`. Its MCP result mapping, where
  `structuredContent` is present, hands the model `JSON.stringify` of it and
  keeps only the non-text blocks of `content`. So the text block reaches no
  model there.
- opencode 1.18.18 and 1.18.31, read off the binary. Its tool wrapper returns
  `content` where it has any block and falls back to `structuredContent` as text
  only where `content` is empty. Its code mode builds a typed function from
  `inputSchema` and `outputSchema`, the one place either client reads the
  schema. The MCP TypeScript SDK client it bundles throws where a tool declares
  a schema and answers without `structuredContent`.
- Measured in Claude Code with `bin/cli tools:tokens --runs=2` on Haiku 4.5,
  `typo3_hint_lookup` with `{"task":"backend module registration"}`, thinking
  off. As served, the model got JSON of 33,747 characters for 8,535 tokens.
  Without `structuredContent`, it got text of 27,370 characters for 7,010 and
  7,021 tokens. Without `outputSchema`, the same JSON for the same 8,535. The
  definition, loaded on demand after `ToolSearch`, cost 973 tokens with the
  6,437-byte schema and 973 without it.
- Measured in opencode 1.18.31 on `opencode/big-pickle` with the same call. As
  served, the model got the text, 27,370 characters, for 6,087 tokens on that
  model's count. Without `outputSchema`, the same text for 6,097, and the first
  request weighed 33,932 tokens against 33,931 with all 31 schemas in the list.
  Without `structuredContent`, the call failed:
  `MCP error -32600: Tool typo3_hint_lookup has an output schema but did not return structured content`.
- The two feedbacks of 2026-07-29 behind `R-ANS-002` came from a client that
  "surfaces the structured payload and drops the text block". That client was
  Claude Code, and `META-04` names it now.

## Decided

- **The measurement stands in the repository.** `bin/cli tools:proxy` relays a
  client to the server with `structuredContent` or `outputSchema` taken away,
  and `bin/cli tools:tokens` runs `claude -p` through it three ways and reads
  the traces. A reader who doubts a number in this entry runs it.
- **`tools:measure` counts the data half as the compact JSON a client hands
  over**, and counts each definition in its two halves. The record keeps the
  data pretty-printed, and the indentation reached no model.
- **The server keeps both halves and the schema.** The schema costs no token in
  either client. Dropping `structuredContent` under a declared schema breaks
  every client on the TypeScript SDK, which opencode showed. Dropping the schema
  would take the contract `tests/Contract/` validates every answer against and
  the typed function a code-mode client builds.
- **What follows for the text half is a question for the maintainer** rather
  than this entry. A Claude Code session reads the data half alone, at 22
  percent more tokens than the text it never sees. `todo/waiting/` carries the
  question with the options.
- **The proxy rewrites; the server does not switch.** A measurement that changes
  the thing it measures is not one.

## Assumed

- That Haiku 4.5 counts a token the way the models sessions run on count one.
  The ratio between the halves is what the entry rests on, and both halves went
  through the same tokenizer.
- That a Claude Code release sends what this one sent. Nothing in its
  documentation says either half, and the read was of one build's code.
- That a definition loaded on demand after `ToolSearch` costs what one sent with
  the list would cost. The API takes no output schema in a tool definition, so
  the list has nowhere to put one either.

## Wrong if

- A Claude Code release hands the model the text block beside the data. Then a
  session pays both halves, and the question in `todo/waiting/` changes shape.
  `bin/cli tools:tokens` shows it as a `tool_result` that is text where it was
  JSON, or as a cost that grew by the text's share.
- A client folds `outputSchema` into the description it sends the model. Then
  the 73 percent of `tools/list` is a token tax there, and this entry's "no
  token in either client" holds for two clients only.
- The ratio between the halves flips for the tools that matter. It is per tool:
  `typo3_test_run_guide` records 67,715 bytes of text against 65,866 of data.
  `tools:measure` prints both, worst first.
