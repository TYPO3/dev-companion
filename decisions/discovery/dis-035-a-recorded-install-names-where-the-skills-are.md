---
id: D-DIS-035
title: A recorded install names where the skills are
date: 2026-10-28
status: open
coveredBy:
  - StdioServerTest::aProjectWithoutSkillsIsToldHowToGetThem
---

# D-DIS-035 — A recorded install names where the skills are

**Where `install` recorded clients, the instructions name the directories that
hold the skills. They send every client that reads another directory to the
`typo3://skill` resource. The server does not learn which client it runs in.**

`D-DIS-034` sent a project without a record to the resource. A record for one
client said nothing to another client in the same project, so that client got no
workflow.

## Evidence

- `feedback/archive/2026-10-28-031023-a-session-that-starts-before-the-server.md`: the
  record named Antigravity alone, and a Claude Code session got neither a skill
  nor the URI.
- Logged on 2026-10-28: Claude Code 2.1.312 sends no `initialize` to this
  server. mcp/sdk v0.9.0 answers its `server/discover` from the static
  instructions, before any request handler of this server.
- No MCP configuration a client reads is this server's file to extend. So a name
  in the start line is not a way in either.

## Decided

- **The sentence names directories, not clients.** It holds for every client in
  the project at once, Claude Code and Codex side by side included. Each runs a
  process of its own and gets the same answer.
- **Rejected: reading the client's name.** A transport of our own that rewrites
  the SDK's answer to `server/discover` is a second departure from the SDK. A
  change to the SDK serves only to tailor the answer, and the answer needs no
  tailoring. The maintainer answered on 2026-10-28 that the client does not
  matter where the answer holds for all of them.
- A stale record keeps `Installer::NOTICE`, and a project without a record keeps
  `Installer::ABSENT`.

## Wrong if

- A session whose client reads one of the named directories skips its own skill
  for the resource. Then the sentence reads as an instruction to every client.
- A session in a client that reads none of them neither has a skill nor reads
  the resource. Then the sentence does not reach the case it is for.
