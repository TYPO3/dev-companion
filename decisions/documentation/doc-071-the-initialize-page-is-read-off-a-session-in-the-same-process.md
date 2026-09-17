---
id: D-DOC-071
title: The initialize page is read off a session in the same process
date: 2026-09-17
status: open
coveredBy:
  - ToolSurfaceTest::everyPageIsWhatTheServerDeclares
---

# D-DOC-071 — The initialize page is read off a session in the same process

**`documentation/server/initialize.rst` shows the `initialize` result, the
instructions, the prompts and the weight of the tool list. `bin/cli tools:index`
reads all of it from a session it runs against `Factory::create()` in its own
process, through a transport that keeps the answers.**

The maintainer asked for the handshake and the descriptions it delivers to stand
in the documentation. The tool pages stood there already, one per tool from the
registry. What a client gets before its first call stood nowhere: not the
`serverInfo`, not the `capabilities`, not the instructions in their wording, not
the two prompts.

## Evidence

- **Three sources could have produced the page.** The constants and
  `Coverage::instructions()` give the name, the version and the text, and
  nothing here spells out the `capabilities` or the `protocolVersion`. Both are
  the SDK's: `Builder::detectCapabilities()` derives them from what `Factory`
  registered, and the negotiation picks the revision. A page written from the
  constants copies the SDK's answer by hand. A subprocess over stdio gives the
  wire itself, and `ToolSurfaceTest` holds every generated page in a unit test
  that starts nothing — `R-COD-003`.
- **The SDK's `InMemoryTransport` drops what the server answers.** Its `send()`
  keeps the session id and nothing else, and the protocol queues a session's
  answers for the transport to drain. `CapturingTransport` keeps both and
  returns them from `listen()`, in about forty lines.
- **The page and the resources page carry counts the other pages avoid.** The
  weight of `tools/list` moves with every description and every schema, which
  already regenerate the tool pages. A size per resource moves with every edit
  to a knowledge document, so the page names no resource count.

## Decided

- The page is a standing page of `ToolSurface`, so `tools:index` writes it,
  `tools:check` reports it and `ToolSurfaceTest` fails on it, the way the index
  and the answer-sources page already work.
- The generated half shows the `initialize` result as JSON with the
  `instructions` shown as text after it, the prompts and the resource template
  as the wire carries them, and the tool list as a count and its bytes per
  member. The prose above the marker says what each member is for and names the
  three things that change the instructions from one start to the next.
- The page shows the standalone checkout's form. The write sentence and the
  `debrief` prompt stand on it, and the prose says a dependency install gets
  neither.

## Wrong if

- A reader mistakes the checkout's form for what their project gets. Then the
  page renders both forms, with `Channel::isAvailable()` given a seam.
- Regenerating the page after an edit to a description becomes the step sessions
  skip. Then the weight moves out of the page and into a report, beside
  `bin/cli tools:measure`.
