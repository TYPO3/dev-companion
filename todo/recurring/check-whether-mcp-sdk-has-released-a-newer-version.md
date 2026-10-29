---
serves: [requirements/]
every: 7 days
checked: 2026-10-29
run: [composer outdated mcp/sdk]
---

# Check whether `mcp/sdk` has released a newer version

Take a patch or minor release on its own merit. `v0.9.0` shipped on 2026-10-18
and is what `composer.lock` holds since 2026-10-22. It serves both protocol eras
over stdio in the SDK's own `StdioTransport`. `Server\StdioTransport` is left
with one override: it drops a notification that arrives before the session,
which the SDK answers with an error without id (`D-ANS-184`).

Watch for four things. A release that stops answering such a notification
retires the override, and `Entrypoint` takes the SDK's transport. One that adds
a handshake revision above `2025-11-25` moves `PROTOCOL_VERSION` in
`tests/Smoke/StdioServerTest.php`. `Server\Protocol::doProcessInput()` answers
an unknown method with `-32600`, where JSON-RPC defines `-32601`, read again on
2026-10-22. `D-DIS-006` rests on the release notes with no undeprecated way for
a client to state where the session is. Read them for that too.

It serves no single requirement because it serves the precondition of all of
them. Every answer this server gives travels over the protocol version the SDK
speaks. On the day a client stops to offer that version, every requirement fails
at once and not one of them says so.
