---
id: D-DIS-006
title: The installation stays worked out from the directory the server was started in
date: 2026-08-01
status: open
---

# D-DIS-006 — The installation stays worked out from the directory the server was started in

**Which installation a session is in stays worked out from the server's start
directory, because MCP has no mechanism to state it instead.**

The one candidate was `roots`, where a client declares the directories it
considers relevant and a server asks for them with `roots/list`. It is
**deprecated as of protocol revision `2026-07-28`** ([SEP-2577][sep]): new
implementations *should not* adopt it. The same revision downgrades it from a
boundary to "informational guidance rather than an access-control mechanism",
which the protocol does not enforce. Under the feature lifecycle policy it stays
in the specification twelve months before removal is possible at all.

[sep]: https://github.com/modelcontextprotocol/modelcontextprotocol/pull/2577

## Assumed

- What it tells implementations to do instead is what this server already does.
  The migration the deprecation names is "tool parameters, resource URIs, or
  server configuration", and `TYPO3_DEV_COMPANION_ROOT` is the third of those.
  It is a value someone decides and every entrypoint honours, which is why
  `R-DIS-001` restricts the derived answer and not this one.
- The walk-up is out of band and stays that way. A work directory is not a
  protocol concept, so no revision can deprecate it, and nothing in the
  specification speaks against it. The cost stands stated rather than hidden:
  `typo3_server_scope` reports the root, how it found it, and every directory
  the search walked.
- The bundled SDK could not have sent the request anyway. `Mcp\Schema\Root`,
  `ListRootsRequest` and `RootsListChangedNotification` exist, but
  `ClientGateway::request()` is private and the only server-to-client calls it
  exposes are `sample()` and `elicit()`. Reaching past that for a feature marked
  deprecated is work with a withdrawal date on it.

## Wrong if

- A later revision adds a way for a client to state where the session is, one
  that is not deprecated on arrival. The deprecation registry is where that
  would show. Also wrong if a client emerges that starts this server somewhere
  other than the session's directory. That would break the walk-up and nothing
  the specification promises. `R-DIS-022` is what would fail, and
  `TYPO3_DEV_COMPANION_ROOT` is what such a setup would have to state.

## Since then

The revision arrived: `mcp/sdk` v0.8.0 exposes the roots this entry declined, so
the third **Assumed** has fallen and the decision stands on the first two. The
mechanism arrived with the deprecation this entry wrote about, and `listRoots()`
raises there. The other half of the **Wrong if** is the corpus and it is silent:
no feedback reports the server started anywhere but the session's directory.
What would settle it is still an outside event, a revision that adds a mechanism
not deprecated on arrival.
