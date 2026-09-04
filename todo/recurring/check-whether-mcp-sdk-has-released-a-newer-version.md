---
serves: [requirements/]
every: 7 days
checked: 2026-09-04
run: [composer outdated mcp/sdk]
---

# Check whether `mcp/sdk` has released a newer version

Take a patch or minor release on its own merit. `v0.8.1` shipped on 2026-08-29
and is what `composer.lock` holds; it carries two server fixes and moves no
revision. The line it sits on is `v0.8.0`, which brought the `2026-07-28`
specification, the protocol version negotiation this todo was watching for (PR
#403), and the extensions framework MCP Apps sits on. What that left where it
was is the revision this server speaks. `2026-07-28` replaced `initialize` with
per-request metadata and `server/discover`, which the SDK serves from
`StreamableHttpTransport` alone, so a stdio transport keeps the newest handshake
revision — `2025-11-25`, and `PROTOCOL_VERSION` in
`tests/Smoke/StdioServerTest.php` with it. The release to watch for now is one
that adds a handshake revision above that, or one that serves the modern era
over stdio; either moves the constant. `D-DIS-006` rests on the same release
notes carrying no undeprecated way for a client to state where the session is,
so read them for that too. It serves no single requirement because it serves the
precondition of all of them: every answer this server gives travels over the
protocol version the SDK speaks, and on the day a client stops offering that
version, every requirement fails at once and not one of them says so.
