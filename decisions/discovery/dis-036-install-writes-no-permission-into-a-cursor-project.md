---
id: D-DIS-036
title: Install writes no permission into a Cursor project
date: 2026-11-06
status: open
coveredBy: []
---

# D-DIS-036 — Install writes no permission into a Cursor project

**`install --agent=cursor` leaves `.cursor/cli.json` alone, and
`documentation/usage/installing.rst` says how a project lists the tools.**

## Evidence

- `feedback/archive/2026-11-04-121348-a-cursor-install-pre-approves-no-tool-so-each.md`:
  a first task on Cursor met a prompt per tool. The run reached its first shell
  command after 52 minutes, against about 18 without the server. That gap is
  files and prompts together.
- Cursor's permissions page, read on 2026-11-06, documents
  `<project>/.cursor/cli.json` and the token `Mcp(server:tool)`.

## Decided

- **The maintainer chose documentation on 2026-11-06**, over a written file and
  over a printed block. A file in the project approves tools for everybody who
  checks it out.

## Assumed

- That a reader who meets the prompts looks in the install page.

## Wrong if

- A second session reports the same prompts with that page in reach. Then the
  printed block is the next step to weigh.
