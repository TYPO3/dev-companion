---
id: D-ANS-184
title: A notification before the session is answered by nothing
date: 2026-10-22
status: open
coveredBy:
  - StdioServerTest::aClientOpeningWithServerDiscoverIsServedTheModernRevision
---

# D-ANS-184 — A notification before the session is answered by nothing

**On mcp/sdk v0.9.0 the server runs the SDK's own stdio transport. Its
`Server\StdioTransport` only drops a notification that arrives before the
connection has a session.**

## Evidence

- **mcp/sdk v0.9.0**, released 2026-10-18. Its `StdioTransport` settles the era
  on the client's first request and serves `2026-07-28` beside the handshake.
  That is what `D-ANS-175` built ahead of it. php-sdk#537 closed in favour of
  #546, #547 and #550, which the release carries.
- **Tried on 2026-10-22.** The raise alone failed `composer stan`, because the
  old class lacked the new `$accessToken` parameter. With the SDK's transport in
  its place, one test failed:
  `StdioServerTest::aClientOpeningWithServerDiscoverIsServedTheModernRevision`.
  A `notifications/cancelled` before the first request came back as `-32600`
  with no `id`. JSON-RPC answers no notification.
- The SDK's `route()` lets a line without an id settle no era. Such a line goes
  to the handshake dispatcher, which finds no session and answers.
- **Read over stdio the same day.** A request before `initialize` now gets its
  own `id`. An unknown method still gets `-32600`. The newest handshake revision
  is still `2025-11-25`.
- The SDK's transport skips the header check on its modern leg, so `Factory`
  needs no switch for it. The suite passes without it.

## Decided

- The maintainer chose on 2026-10-22 to raise now, with an override of the one
  case, over a wait for a fixed release.
- The override answers nothing to a notification while the session is unset. It
  reads the line itself, because `route()` is private.
- The bug goes upstream as a report the maintainer files.

## Wrong if

- An SDK release stops answering such a notification. Then the class goes, and
  `Entrypoint` takes the SDK's transport.
- A client sends a notification before its first request and relies on an error
  back. Nothing in the specification asks for one.
