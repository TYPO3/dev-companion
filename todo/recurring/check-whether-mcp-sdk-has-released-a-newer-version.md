---
serves: [requirements/]
every: 7 days
checked: 2026-11-27
run: [composer outdated mcp/sdk]
---

# Check whether `mcp/sdk` has released a newer version

Take a patch or minor release on its own merit. `v0.10.0` shipped on
2026-11-10 and is what `composer.lock` holds since 2026-11-27. It split the SDK
into `Mcp\Schema`, `Mcp\Server` and `Mcp\Client`, and its own stdio transport
answers nothing to a notification before the session. So the server runs that
transport without an override (`D-ANS-184`).

Watch for three things. One that adds a handshake revision above `2025-11-25`
moves `PROTOCOL_VERSION` in `tests/Smoke/StdioServerTest.php`. The server
answers an unknown method with `-32600`, where JSON-RPC defines `-32601`, read
again on 2026-11-27. `D-DIS-006` rests on the release notes with no undeprecated
way for a client to state where the session is. Read them for that too.

It serves no single requirement because it serves the precondition of all of
them. Every answer this server gives travels over the protocol version the SDK
speaks. On the day a client stops to offer that version, every requirement fails
at once and not one of them says so.
