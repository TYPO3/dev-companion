---
id: R-DIS-023
title: 'An install says what is left before a tool can be called'
status: held
restsOn: [D-DIS-009]
heldBy:
  - InstallerAgentSupportTest::everyClientWithAnEntryIsToldWhatIsLeftBeforeAToolCanBeCalled
---

# R-DIS-023 — An install says what is left before a tool can be called

**`install` and `update` say per client, in their own output, what the client
still owes before a tool in the new entry can answer.**

A write of `.mcp.json` puts the entry on disk. It does not register the server
with anything. A client that scopes project servers behind an approval has had
no question yet. A session that was already open at the write runs against the
configuration it started with. Both end with a published skill that names eleven
tools beside an entry that is entirely correct, and no tool in the session.

What the step is belongs to the client rather than to this package, so the
output names it per client. That is the way `documentation/usage/installing.rst`
already names `chat.useAgentSkills` for VS Code. It stands in the command's
output rather than only in a manual. The person who can finish the install looks
at a terminal at that moment.

## From

Two sessions in `/home/benji/projects/site-new`, on 2026-07-29 and 2026-07-31,
each with a valid `.mcp.json` from this installer and no callable tool. Both
reached the server in the end when they drove the stdio binary by hand. The
second did so only after it had already audited a site package without it.

## Held by

What the output has to say is a property of each client rather than of this
package. So that test holds that the command says something, per client, on both
commands, and on the line under the entry it is about. It does not hold that it
is true. Each line's claim has its source per client in
[installing.md](../../documentation/usage/installing.rst), from that client's
own documentation. A client whose documentation does not answer says so rather
than gets a guess.
