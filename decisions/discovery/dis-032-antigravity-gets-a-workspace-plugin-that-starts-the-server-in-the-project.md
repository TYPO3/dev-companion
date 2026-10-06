---
id: D-DIS-032
title: Antigravity gets a workspace plugin that starts the server in the project
date: 2026-10-06
status: open
coveredBy:
  - InstallerTest::antigravityGetsAPluginThatStartsTheServerInTheProject
---

# D-DIS-032 — Antigravity gets a workspace plugin that starts the server in the project

**`install --agent=antigravity` writes `.agents/plugins/typo3-dev-companion/`
with `plugin.json`, `mcp_config.json` and a `.gitignore`. The entry names the
project's absolute path as `cwd`.**

Antigravity reads no `.mcp.json`. Before this, the client received the skills
and no server, and a person wrote the entry by hand.

## Evidence

- Two feedbacks on 2026-10-06 report the gap:
  `feedback/archive/2026-10-06-133248-install-agent-antigravity-writes-no-server.md`
  and
  `feedback/archive/2026-10-06-134055-install-agent-antigravity-writes-the-skills-but.md`.
  The second measured a working plugin of these two files in `agy -p`. A probe
  logged the start directory. With `cwd` the server started in the project root,
  without it in the plugin directory.
- **Read on 2026-10-06** from
  [Antigravity's plugin page](https://antigravity.google/docs/plugins/). A
  workspace plugin is a directory in `.agents/plugins/`, and `plugin.json` is
  the required manifest. `name` is required for the CLI, and `$schema` is
  `https://antigravity.google/schemas/v1/plugin.json`.
- **Read on 2026-10-06** from
  [Antigravity's MCP page](https://antigravity.google/docs/mcp/). An entry sits
  under `mcpServers` with `command`, `args`, `env` and `cwd`. The page names no
  variable for the project root. It also names `.agents/mcp_config.json` as a
  workspace configuration.

## Decided

- The plugin, not `.agents/mcp_config.json`. The plugin directory belongs to
  this package whole, so it ignores itself the way a published skill does
  (`D-DIS-010`). So the absolute `cwd` never reaches a commit, and the install
  prints no `HOST_SPECIFIC` line for this client.
- The entry carries no `type`. Neither page documents one, and the measured
  plugin worked without it.
- The maintainer asked for this shape in the session that recorded the first
  feedback.

## Assumed

- Antigravity loads a workspace plugin with no step of trust or approval.
  Neither page says so.

## Wrong if

- Antigravity documents a variable for the project root in `cwd` or `args`. Then
  the entry uses it, and the plugin becomes a file a project can share.
- A session in Antigravity does not list the server after the install. Then the
  plugin format, or a gate the documentation does not name, is the cause.
