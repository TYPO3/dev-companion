---
id: R-DIS-018
title: "A console command never inherits the client's stdin"
status: held
heldBy:
  - StdioServerTest::aRequestBehindOneThatRunsTheConsoleIsStillAnswered
---

# R-DIS-018 — A console command never inherits the client's stdin

**Every subprocess this server starts gets an stdin of its own, because the
server's own stdin is the client's request stream.**

A console command that reads stdin, and `ddev exec` does, otherwise consumes the
request the client wrote while the command ran. Nothing reports it. The server
never saw the request, the client waits for an answer to it, and the session
stops without an error on either side.

## From

Two runs of `REVIEW-02` that both stalled on the first pair of tool calls the
client dispatched at once (2026-07-31).
