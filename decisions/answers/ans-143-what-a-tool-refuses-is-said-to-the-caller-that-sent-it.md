---
id: D-ANS-143
title: What a tool refuses is said to the caller that sent it
date: 2026-09-04
status: open
coveredBy:
  - StdioServerTest::whatAToolRefusesIsSaidToTheCallerThatSentIt
---

# D-ANS-143 — What a tool refuses is said to the caller that sent it

**A tool that throws answers with a result marked as an error carrying its own
message, rather than with a protocol error the caller reads as "something
failed".**

Every refusal this server writes is addressed to the caller and says what to
send instead, and none of them was reaching one.

## Evidence

- `feedback/2026-09-03-235530`. A session filing five feedback got
  `Error while executing tool` with nothing else, dropped the `suggestion`
  parameter to get past it, and spent four further calls bisecting which
  parameter had done it. Two of those calls are still on the board as probe
  feedback.
- Measured over the stdio transport on 2026-09-04: `typo3_feedback_record` with
  a blank observation answered
  `{"error":{"code":-32603,"message":"Error while executing tool"}}`, and so did
  the one refusal that reaches a `suggestion` — `Channel::assertNoCallFrame()`,
  whose message names the parameter and says how to close the call. Both
  messages were dropped by the SDK.
- The tool was never the failure. The same payloads — the five reports of that
  session, passed as `suggestion` through the tool and over the transport — are
  written today, which is what says the refusal was reached rather than the
  writing.
- A schema violation is answered differently and always was: `-32602` carries
  the pointer and the type it wanted. Only what a tool itself refuses was
  silent.

## Decided

- `Sdk\ToolHandler` catches every `\Throwable` and answers `isError: true` with
  the message as the content, which is where the protocol puts a tool's own
  failure and the only place the model that made the call reads.
- The message is handed over whatever the class. Every one raised here is
  written for the caller, and a failure carrying none is named by its class so
  that "nothing was said" is still searchable.
- No `structuredContent` on that path. The output schema describes an answer,
  and a refusal is not one.

## Assumed

- That a message a `RuntimeException` carries is safe to hand over. The ones on
  this path name paths inside this server's own checkout, which the successful
  answer names too.

## Wrong if

- A refusal turns out to carry something the caller may not see — a path outside
  this checkout, or a value read from an installation.
- A client treats `isError: true` as an outage rather than as an answer, and the
  session stops instead of correcting its call.
