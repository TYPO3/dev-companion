---
id: D-ANS-167
title: The text block is prose rather than the serialized data
date: 2026-09-19
status: open
coveredBy:
  - StdioServerTest::aToolCallReturnsTextAndStructuredContent
---

# D-ANS-167 — The text block is prose rather than the serialized data

**A tool's text block is the answer rendered for a reader, and
`structuredContent` is the same answer as data. The text block does not carry
the serialized JSON.**

The specification says a tool that returns structured content SHOULD also return
the serialized JSON in a text block, for a client that reads no
`structuredContent`. This server departs from that SHOULD on every tool, and
nothing said so until this entry.

## Evidence

- **Read on 2026-09-19**: the tools page of the specification at 2025-11-25 and
  at 2026-07-28 carries the sentence in the same words, under **Structured
  Content**, with the reason "for backwards compatibility".
- `D-EVI-011` measured what the two recorded clients hand the model. Claude Code
  hands over the data half and drops the text. opencode hands over the text
  half. Neither hands over both. So a text block that repeated the data would
  reach the model as JSON in opencode, where the prose reaches it today.
- `R-ANS-002` demands that every fact a caller acts on stands in the data, and
  `AGENTS.md` demands that the text says what the schema says. So the two halves
  carry one answer, and a client that reads the text alone reads the whole of
  it. That is what the SHOULD protects, met the other way round.
- `D-ANS-162`: the data half is the one a session reads where a client hands
  both over. The text is for the client that hands over text, and there prose
  with the emphasis and the caveats is worth more than the record again.

## Decided

- `Sdk\ToolHandler` sends the tool's rendered text as the one text block and the
  data as `structuredContent`, and no tool serializes its data into the text.
- A client that reads the text alone gets the full answer as prose. A fact
  missing from the text is a fact missing from the answer, and the answer is
  that fact in the text rather than a JSON copy beside it.

## Wrong if

- A client shows the model the text block alone and its session reads nothing
  out of it. A feedback that names a fact the data carried and the text did not
  is the sign, and the answer is the fact in the text.
- A recorded client validates a text block against the output schema. Then the
  SHOULD is a MUST for that client, and the handler adds the JSON block after
  the prose.
