---
id: D-ANS-004
title: The instruction budget is 2048 characters, on one client's evidence
date: 2026-07-31
status: open
coveredBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
---

# D-ANS-004 — The instruction budget is 2048 characters, on one client's evidence

**2048 characters is treated as the limit every client keeps, although exactly
one client has been measured.**

The instructions were cut to fit it rather than the limit being made
configurable.

Both release runs of 2026-07-31 logged
`Server instructions truncated from 3662 to 2048 chars`. Nothing in the protocol
states a limit, and no other client has been measured.

## Decided

- Cut the text. The instructions had grown to 3253 stored characters and said
  several things twice; the version binding is stated in the tools' own answers
  and the core-profile enumeration in `typo3_server_scope`, so what was removed
  is mostly what was already somewhere else. A server that fits the smallest
  known budget needs no negotiation with any client.

## Assumed

- 2048 is a floor rather than one client's number, and a client that keeps more
  loses nothing by being sent less. The cost of being wrong in this direction is
  a shorter statement than necessary; in the other it is silence about the thing
  that was cut.

## Wrong if

- A client is found that truncates below 2048, which makes the budget a property
  of the connection and `Coverage::INSTRUCTIONS_BUDGET` a negotiated value
  rather than a constant — or if clients start reporting truncation to the
  server, at which point the server can say what it lost instead of guessing
  what it may spend.

## Since then

The number has become what blocks rather than what was spent: the longest
assembly stands twenty characters under it, and a feedback asks for a statement
of the boundary that does not fit in what is left — the third card in a row to
end at this constant.

Nothing has re-measured it. This entry took the number from one client's release
runs, and the client reporting above is a different one that delivered the block
whole and reports no truncation.

## Since then

Re-measured on 2026-09-01, on Claude Code 2.1.252 rather than on a log line. The
server's own block came back from a client session byte-identical at 1948
characters. A stdio server written for the measurement, sending 8000 characters
of numbered markers, was cut at exactly 2048 with an ellipsis in place of the
rest — so the number is now read off the cut rather than taken from a client's
report of it. The largest assembly this server sends today is 2033, which leaves
fifteen characters.
