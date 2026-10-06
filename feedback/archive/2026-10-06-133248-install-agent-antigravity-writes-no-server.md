---
date: 2026-10-06T13:32:48+00:00
category: tool-gap
status: closed
closed: 2026-10-06
model: claude-opus-5-5
tool: install, Installer, antigravity
directory: /home/ben/src/typo3-dev-companion
---

# install --agent=antigravity writes no server entry, so Antigravity starts without the server

## Observation

Task: make the server work in Antigravity (Google) out of the box, after the server/discover fix of D-ANS-171.

`Installer::AGENTS` declares antigravity as ["skills" => ".agents/skills"] with no "mcp" entry. So `typo3-dev-companion install --agent=antigravity` publishes the task skills and registers no server. Antigravity does not read `.mcp.json`, which the default install writes. A project set up with either command has the skills in Antigravity and no tool behind them. The maintainer has to register the server by hand.

## Query

typo3-dev-companion install --agent=antigravity, in a TYPO3 project, then open the project in Antigravity

## Suggestion

Have install (and update) write a project plugin for Antigravity at `.agents/plugins/typo3-dev-companion/`: a `plugin.json` manifest and an `mcp_config.json` with the stdio entry the other clients get (the command, its arguments, the DDEV route where the project has one). Antigravity then finds the server with no step by hand. Verify the two file formats against the Antigravity documentation before writing them, and add the entry as `mcp` in `Installer::AGENTS` the way the other clients carry theirs.
