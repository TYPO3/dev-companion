---
id: D-ANS-004
title: The instruction budget is 2048 characters, on one client's evidence
date: 2026-07-31
status: open
coveredBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
---

# D-ANS-004 — The instruction budget is 2048 characters, on one client's evidence

**This server treats 2048 characters as the limit every client keeps, although
the measurement covers exactly one client.**

The session cut the instructions to fit it rather than made the limit
configurable.

Both release runs of 2026-07-31 logged
`Server instructions truncated from 3662 to 2048 chars`. Nothing in the protocol
states a limit, and no measurement covers another client.

## Decided

- Cut the text. The instructions had grown to 3253 stored characters and said
  several things twice. The tools' own answers state the version binding and
  `typo3_server_scope` the core-profile enumeration, so the cut removed mostly
  what already stood somewhere else. A server that fits the smallest known
  budget needs no negotiation with any client.

## Assumed

- 2048 is a floor rather than one client's number, and a client that keeps more
  loses nothing when it gets less. A wrong number in this direction costs a
  shorter statement than necessary. In the other it costs silence about the
  thing the cut took.

## Wrong if

- A client turns up that truncates below 2048. That makes the budget a property
  of the connection and `Coverage::INSTRUCTIONS_BUDGET` a negotiated value
  rather than a constant. Or clients start to report truncation to the server,
  and then the server can say what it lost instead of guess what it may spend.

## Since then

The number has become what blocks rather than what the server spends. The
longest assembly stands twenty characters under it, and a feedback asks for a
statement of the boundary that does not fit in what remains. That is the third
card in a row to end at this constant.

Nothing has re-measured it. This entry took the number from one client's release
runs. The client in the report above is a different one that delivered the block
whole and reports no truncation.

## Since then

Re-measured on 2026-09-01, on Claude Code 2.1.252 rather than on a log line. The
server's own block came back from a client session byte-identical at 1948
characters. A stdio server written for the measurement sent 8000 characters of
numbered markers. The client cut them at exactly 2048 with an ellipsis in place
of the rest. So the number now comes off the cut rather than from a client's
report of it. The largest assembly this server sends today is 2033, which leaves
fifteen characters.
