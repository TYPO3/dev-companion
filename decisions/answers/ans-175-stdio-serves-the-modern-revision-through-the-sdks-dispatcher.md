---
id: D-ANS-175
title: Stdio serves the modern revision through the SDK's dispatcher
date: 2026-10-07
status: open
coveredBy:
  - StdioServerTest::aClientOpeningWithServerDiscoverIsServedTheModernRevision
  - StdioServerTest::aModernRequestAfterTheHandshakeIsRefused
---

# D-ANS-175 — Stdio serves the modern revision through the SDK's dispatcher

**`Server\StdioTransport` takes the SDK's `StatelessProtocol`. The client's
first request settles the era of the connection, as php-sdk#537 makes the SDK's
own stdio transport do.**

The Skills extension puts its declaration in the answer to `server/discover`,
which is a `2026-07-28` method. Over stdio the server refused that revision. So
a host that reads the extension there found no skills.

## Evidence

- **Read on 2026-10-07**: the overview at
  modelcontextprotocol.io/extensions/skills/overview and the stable
  specification in the ext-skills repository. Both put the declaration in
  `server/discover` and specify the extension against `2026-07-28` or later. The
  client matrix lists `mcpc` with full support, and ChatGPT, fast-agent and the
  MCP Inspector with partial support.
- **mcp/sdk v0.8.1**, still the newest release on 2026-10-07. `Server::run()`
  connects the modern dispatcher to a transport that implements
  `StatelessAwareTransportInterface`. `StreamableHttpTransport` implements it,
  and the stdio transport does not.
- **modelcontextprotocol/php-sdk#537**, open on 2026-10-07 with review required.
  It makes the SDK's `StdioTransport` serve both eras. The first request settles
  the era, and a request from the other era is refused. It adds
  `StatelessProtocol::handleInline()`, which skips the header check and does not
  pause a listen stream.
- **Read over stdio on 2026-10-07.** `server/discover` answered `2026-07-28` and
  the capabilities with the Skills extension. `skills/list`, `tools/call` and
  `resources/read` answered. A reference nothing serves answered `-32602`, as
  the revision requires.

## Decided

- The maintainer chose on 2026-10-07 to serve the modern revision over stdio
  ahead of an SDK release. That reverses the choice `D-ANS-172` records.
- The transport copies the behaviour and the names of the pull request, so its
  release replaces the class whole. The maintainer asked for that on 2026-10-07.
- A bare `initialize` on a modern connection gets `-32022` with the modern
  revisions. A claimed request on a handshake connection gets `-32600` with its
  id. `notifications/cancelled` ends an open listen stream.
- v0.8.1 has no `handleInline()`, and three differences follow. The transport
  polls a listen stream on a tick that read nothing, because the SDK pauses 250
  ms per poll. `Factory` turns the header check off. A handler's progress is
  dropped rather than streamed, and no handler here sends any.
- One difference is deliberate. A notification before the first request goes to
  the modern dispatcher, which acknowledges it with nothing. The pull request
  hands it to the handshake dispatcher, which answers with an error without id.
- The class is `StdioTransport`. `HandshakeStdioTransport` named the one era it
  no longer serves alone.

## Assumed

- That a modern client opens with a request that claims its revision in `_meta`.
  The revision requires the claim on every request. A first request without it
  opens a handshake connection.

## Wrong if

- An SDK release carries php-sdk#537. Then the class and the header switch in
  `Factory` go, and `Entrypoint` takes the SDK's transport again.
  `aClientOpeningWithServerDiscoverIsServedTheModernRevision` then shows whether
  the release answers a notification with an error.
- A host reads the skills from `server/discover` and lists none. Then something
  between the dispatcher and the host drops the extension.
