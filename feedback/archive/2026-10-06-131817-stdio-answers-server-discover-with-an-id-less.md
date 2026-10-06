---
date: 2026-10-06T13:18:17+00:00
category: bug
status: closed
closed: 2026-10-06
model: claude-opus-5-5
tool: stdio, transport, mcpsdk, ServerProtocol
directory: /home/ben/src/typo3-claudio
---

# stdio answers server/discover with an id-less -32600, so Antigravity never connects

## Observation

Task: find out why the server does not start in Antigravity (Google) for the TYPO3 core checkout.

Antigravity shows this error in /mcp: `connection closed: calling "initialize": client is closing: invalid request`.

A wrapper recorded the stdio traffic. Antigravity does not send `initialize` first. It sends `server/discover` with protocol revision 2026-07-28:

{"jsonrpc":"2.0","id":1,"method":"server/discover","params":{"_meta":{"io.modelcontextprotocol/clientCapabilities":{"elicitation":{"form":{},"url":{}},"roots":{"listChanged":true}},"io.modelcontextprotocol/clientInfo":{"name":"antigravity-client","version":"v1.0.0"},"io.modelcontextprotocol/protocolVersion":"2026-07-28"}}}

The server answers with this line only:

{"jsonrpc":"2.0","error":{"code":-32600,"message":"A valid session id is REQUIRED for non-initialize requests."}}

The answer has no "id" member. The stderr output is empty. The server does not crash.

Cause in mcp/sdk v0.8.1: over stdio, the server uses only the handshake-era Server\Protocol. Only StreamableHttpTransport routes modern-era requests (InboundClassifier) to the StatelessProtocol. Server/Protocol.php:699 treats `server/discover` as a non-initialize request without a session. It calls Error::forInvalidRequest() without the request id. Since 0.8.0, a null id omits the "id" member.

Antigravity uses a Go MCP client. That client cannot decode a message with no method and no valid id. It reports ErrInvalidRequest ("invalid request") and closes the connection. It never falls back to `initialize`.

A manual `initialize` (2025-06-18 and 2025-11-25) over stdio gets a correct answer. The defect is only the modern-era first request.

## Query

Start the server over stdio from Antigravity CLI. Or pipe the server/discover line above into `php bin/typo3-dev-companion` and read stdout.

## Suggestion

Make the stdio server serve both protocol eras, as StreamableHttpTransport does. Classify each stdio message (InboundClassifier) and route `server/discover` and other 2026-07-28 requests to the StatelessProtocol.

If the server keeps the handshake era only on stdio, reject `server/discover` with an error that keeps the request id. Use the code that the 2026-07-28 spec gives for an unsupported revision, or -32601. Then the client can fall back to `initialize`.

In all cases, an error answer to a request must carry that request's id. Preserve the id in Server\Protocol for the "session id is REQUIRED" and "session not found" errors. Fix it upstream in mcp/sdk, or wrap the protocol in this server until upstream does.

Add a test that pipes `server/discover` over stdio and checks the answer for "id":1.
