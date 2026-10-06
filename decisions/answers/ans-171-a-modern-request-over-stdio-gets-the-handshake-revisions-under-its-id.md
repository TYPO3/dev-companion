---
id: D-ANS-171
title: A modern request over stdio gets the handshake revisions under its id
date: 2026-10-06
status: open
coveredBy:
  - StdioServerTest::aClientOpeningWithServerDiscoverIsToldWhichRevisionsToFallBackTo
---

# D-ANS-171 — A modern request over stdio gets the handshake revisions under its id

**`Server\HandshakeStdioTransport` answers a request in the `2026-07-28`
envelope with `-32022`, the handshake revisions and the request's own id. The
server keeps the handshake era alone over stdio.**

On `mcp/sdk` v0.8.1 the stdio transport hands such a request to the handshake
dispatcher. That dispatcher answers `server/discover` with `-32600` and no `id`.
Antigravity's Go client cannot read that answer and closes the connection.

## Evidence

- **Read on 2026-10-06** from
  `feedback/archive/2026-10-06-131817-stdio-answers-server-discover-with-an-id-less.md`.
  The server answered the line from that feedback with
  `{"jsonrpc":"2.0","error":{"code":-32600,…}}`. v0.8.1 is the newest release,
  and `StdioTransport` on the SDK's `main` still carries the handshake era
  alone.
- `StreamableHttpTransport::handleModernRequest()` sends this same error where
  no `StatelessProtocol` is connected. So the answer is the SDK's own, on the
  transport it has for the case.
- A prototype served both eras over stdio. It connected the SDK's
  `StatelessProtocol` with the header validator off. `server/discover`,
  `tools/list` and `tools/call` answered correctly. `subscriptions/listen`
  answers with a stream the stdio loop cannot serve. A notification carries no
  claim, so it still reaches the handshake dispatcher.

## Decided

- The maintainer chose the refusal on 2026-10-06 over two options. The first
  served both eras in this server, ahead of the SDK. The second waited for an
  SDK release and left Antigravity unable to connect.
- The transport answers a modern notification with nothing. It forwards a
  refused batch's error, which carries no id because a batch has none.
- A request in the handshake era before `initialize` still gets the SDK's
  `-32600` without an id. The recurring SDK todo watches for that fix.

## Assumed

- A client that opens with `server/discover` falls back to `initialize` on
  `-32022` with the handshake revisions in `data.supported`. The specification
  names that error for the case. Nobody here has run Antigravity against the
  change.

## Wrong if

- Antigravity, or another client that opens with `server/discover`, still does
  not connect. Then the server serves the modern era over stdio, as the
  prototype did.
- An SDK release serves the modern era over stdio or answers with the id itself.
  Then the class goes and `Entrypoint` takes the SDK's transport again.
