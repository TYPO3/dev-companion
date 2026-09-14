---
id: D-ANS-017
title: A union-typed argument gets the wording a client can compose against
date: 2026-08-02
status: open
coveredBy:
  - FeedbackTest::severalToolsStaySeveralTools
  - FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares
  - StdioServerTest::aListOfToolNamesIsRefusedWithTheTypeItWanted
  - ToolContractTest::everyToolDeclaresSchemasAndAnnotations
  - ToolContractTest::noArgumentDeclaresMoreThanOneType
---

# D-ANS-017 — A union-typed argument gets the wording a client can compose against

**An input schema is a grammar a client generates against. The surface's only
union-typed argument is the only one a client never managed to send.**

It is not only a contract a client reads. `typo3_feedback_record` declares
`tool` as `["string", "array"]`, the one union in any input schema the server
offers. One model recorded four feedback in 49 seconds with none.

## Evidence

- Re-run on 2026-08-02 against the server as it is now —
  `bin/typo3-dev-companion` over stdio from this worktree.
  `tool: "typo3_extension_describe"` lands as `tool: typo3_extension_describe`;
  `tool: ["typo3_extension_describe", "typo3_feedback_record"]` lands as
  `tool: typo3_extension_describe, typo3_feedback_record`. The tool accepts both
  forms and stores both as names. The server does not drop the value, on either
  branch of the union.
- The message the feedback quotes has no producer in this checkout. A `tool` the
  schema refuses comes back as JSON-RPC `-32602`,
  `Property '/tool': Invalid type. Expected `string|array`, but received `integer`.`
  — a validation error with a pointer, not a parse error, and nothing renders a
  value as a dot. "JSON Parse error Unexpected EOF" is the client's failure to
  produce the call, before it sent anything.
- The call never reached the server, and the session otherwise talked to it
  fine. `feedback/2026-07-31-194459`, `feedback/archive/2026-07-31-194504`,
  `feedback/2026-07-31-194510` and the report itself at `194548` are four writes
  from `/home/benji/projects/site-new` inside 49 seconds. All four are on disk.
  None of the four carries a `tool:` line.
- Nothing else in the surface has this shape. Walking every offered tool's
  `inputSchema` — properties, items and the `oneOf` branches under them — finds
  exactly one union: `typo3_feedback_record` `/properties/tool`. Every other
  union in the code is `[X, "null"]` and lives in an output schema.
- The corpus says which clients it stops. Of 207 feedback, 201 carry a `tool:`
  line, across six distinct model identifiers. That is
  `opencode/deepseek-v4-flash-free` 31 times, `nemotron-3-ultra-free` 9,
  `deepseek-v4-flash-free` 8, `big-pickle` and `GPT-5 mini` 3 each,
  `opencode/ling-3.0-flash-free` 2. And 139 from before the model field existed.
  Six carry none: four are this session's, and the other two sit in the archive
  and predate the model field. `opencode/mimo-v2.5-free` is the only model that
  has ever recorded feedback and never once sent `tool`.
- The union buys nothing the string branch does not. `Channel::toolNames()`
  splits a string on `[\s,;]+`. The argument description already says "Several
  may be named, as a list or separated by commas", and
  `FeedbackTest::severalToolsStaySeveralTools` covers the comma form on its own.
  `15405e5`, which introduced the list, wanted names that stay apart — not a
  second declared type.
- This is not a delivery gap. `documentation/clients/tools.md:1812` renders the
  argument as *(string or array)* and `tools/list` carries the union unaltered,
  so what the session read was accurate.

## Decided

- The judgement is **step 4 of the ladder**, wording. That is the sense of
  [`D-ANS-012`](ans-012-an-oneof-alternative-is-stated-where-the-caller-composes-the-call.md),
  where a declaration reached the caller and the caller could not act on it. No
  tool is absent, every route is right, and the reference says the truth.
- The feedback is **queued whole**, not trimmed and not closed on the spot. It
  makes one claim, the claim is about a declared schema, and
  [judging.md](../../documentation/records/judging.rst) puts a tool's contract
  on the reviewed side of that line.
- Its own suggestion fails as a diagnosis: serialization drops no value, and
  `tool` collides with no internal variable. What it got right is the parameter
  and the symptom, which is what a report from outside can see.
- Two candidates, and this entry chooses neither. Declare `tool` as a plain
  `string` and let the comma form carry the several, or keep the union and
  accept that it costs a client. The first gives up a machine-readable list and
  would start to refuse an array over the wire. The unit case that covers the
  list, since renamed
  `FeedbackTest::theRecorderStillTakesAListTheSchemaNoLongerDeclares`, stays
  green, because that test calls `Channel::record` directly. `R-FBK-001` is what
  either has to keep held.
- [`D-ANS-005`](ans-005-an-unsupported-question-is-answered-in-a-shape-of-its-own.md)
  and `D-ANS-012` both bet that a client which reads less of a schema than it
  declares gets a weaker promise and nothing worse. This is the counter-example:
  the degradation was an argument the client could not send at all.

## Assumed

- That the union type is what the client failed on. Nothing here can run that
  client, so the evidence is circumstantial. The call never arrived, the session
  wrote four notes without it, and `tool` is the only argument in the surface
  with that shape. The same pattern is what `D-ANS-012` reasoned from.
- That `opencode/mimo-v2.5-free` is one client's tool-call layer rather than one
  bad session. Four attempts across three subjects is what makes it look like a
  property; four attempts is still one session.

## Wrong if

- The schema declares `tool` as a plain `string` and a feedback from
  `opencode/mimo-v2.5-free` still arrives without one. Then the union was not
  what stopped it, and the failure is somewhere this repository has not looked.
- A feedback arrives from `opencode/mimo-v2.5-free` with a `tool:` line while
  the union still stands. Then the schema was never the obstacle and this entry
  read one session's failure as a property of the shape.
- Another argument in this surface gets a union type and every client composes
  calls for it. Then a union is not a shape that costs a caller, and the
  diagnosis here was about `tool` alone.

## Since then

The first of the two candidates landed, and four reads decided it rather than a
preference. The union really was on the wire and really was alone there. A
client that read the schema saw both branches, and one still could not produce
the call. A second walk over every offered tool's input schema finds no second
union. The unions in the output schemas are all nullable fields rather than an
alternative a caller has to choose between. So nothing else in this package says
the opposite about the shape.

The string branch gives a client nothing less. The split is on whitespace and
punctuation, and the description said so before and says so more plainly now.
The wire holds the requirement for the first time.
