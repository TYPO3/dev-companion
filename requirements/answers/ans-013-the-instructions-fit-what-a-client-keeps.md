---
id: R-ANS-013
title: 'The instructions fit what a client keeps'
status: held
heldBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::theServerAnnouncesItselfWithItsBoundary
---

# R-ANS-013 — The instructions fit what a client keeps

**The statement sent at initialize stays within the budget a client keeps for
certain, counted over everything assembled.**

The profile prefix and the write sentence are part of that count.

A client that truncates says so to its own debug output and to nobody else. So
the sentences past the limit are absent in a way nothing reports. The server
believes it said them and the agent never read them. What falls off is the end,
which is where the statements that qualify everything before them sit.
[`R-AUD-006`](../audience/aud-006-the-query-language-is-english.md) stood as
"the entire mitigation" and was the first thing cut. Length is therefore a
property of the instructions rather than of the client. This holds it here, so
that the next sentence added has to displace one.

## From

Two release runs in `E-EXT` (2026-07-31) whose client cut 3662 characters to
2048. That dropped the English-query sentence, the version bound and
`typo3_server_scope` from every session it ran.

## Held by

- `ScopeTest::theInstructionsFitWhatAClientKeeps` for every profile, and
- `StdioServerTest::theServerAnnouncesItselfWithItsBoundary` on the string a
- Client receives over the wire
