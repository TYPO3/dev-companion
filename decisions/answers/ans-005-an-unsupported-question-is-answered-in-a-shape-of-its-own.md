---
id: D-ANS-005
title: 'An unsupported question is answered in a shape of its own'
date: 2026-08-02
status: open
coveredBy:
  - StdioServerTest::aQuestionThatCannotBeAnsweredHereIsStillAnAnswer
  - ToolContractTest::aQuestionThatCannotBeAnsweredHereSaysOnlyThat
  - ToolContractTest::anInstallationBackedSchemaOffersEitherShape
  - ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer
---

# D-ANS-005 — An unsupported question is answered in a shape of its own

**A question this server cannot answer here gets `unsupported` and a reason,
which replace the result rather than label it. It is never an MCP tool error.**

`D-ANS-001` kept the result shape and added the reason beside it. A tool that
holds that shape emits every field the schema requires, and those fields are the
answer: a count, a flag, a list. The tool had to unsay what they said, so the
counts and the flags became nullable. That is a shape kept with the numbers
faked out of it. The shape was never the thing worth its keep.

## Decided

- The unsupported answer replaces the result. `Result\Unsupported` builds it and
  nothing else does: `unsupported` with a `cause`, the `reason`, the diagnosis,
  where discovery looked and which setting is wrong. Beside it stand only the
  caller's own arguments, which come back and claim nothing about anything.
- The output schema declares the two as `oneOf`, so a hit keeps every field it
  ever required and an answer with both shapes is invalid. The specification
  makes conformance a server MUST and validation a client SHOULD. So the schema
  is the promise, and it says which of the two arrived.
- `cause` is `no-installation`, `misconfigured` or `installation-not-answering`.
  Prose already carried the difference and only prose did. `META-02` requires
  "nothing found" and "found but not running" to be distinguishable, and a
  client cannot lexically match its way to that.
- `answeredBy` loses `nothing` and keeps `installation` and `packages`. Which of
  two sources answered is a question with no answer where neither did. The third
  case is now a different key rather than a third value of this one.
- No tool sets `isError`. Nothing failed: the question is simply not supported
  in this directory, and the server knows why and says so. `Result\ToolResult`
  has no such field. So the only route to an MCP error stays a thrown exception:
  an unknown tool, or arguments the schema rejected.

## Assumed

- A client shows a non-error answer as readily as an error one. An error is not
  more visible, only louder. A caller that has to tell "no icon" from "no
  installation" needs the reason rather than the severity.
- A client branches on the presence of a key before it reads the fields under
  it.

## Wrong if

- A client reads the answer fields without a check for `unsupported` and treats
  their absence as a crash or an empty answer. The nullable shape would have
  handed it something to misread; this one hands it nothing, which is the bet.
- A client swallows an answer it cannot act on and surfaces only errors. So the
  user hears nothing where an error would have reached them.
- A client validates `structuredContent` and cannot read `oneOf`. The schema
  declares the two shapes as alternatives, which keeps a hit's full promise in
  the document rather than only in a test here. A validator that ignores the
  keyword reads the relaxed outer required list instead and gets a weaker
  promise than the server keeps.

## Since then

A client met the shape on 2026-08-04, which the entry this inherited never had.
Both installation-backed tools answered `unsupported` with the cause, the reason
and the directories discovery walked.

Neither **Wrong if** about a client that reads the answer happened. The session
told the user that it could not ask the installation and named both variables as
the way out. It wrote that an identifier recalled from memory is the answer it
would not give. It branched on the key rather than read the fields under it. The
third was not exercised, so a validator that cannot read the schema is still a
client nobody here has met. That leaves the instructions lever unspent, and what
it would have said is what the client did untold.
