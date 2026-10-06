---
date: 2026-10-06T13:40:55+00:00
category: tool-gap
status: closed
closed: 2026-10-06
model: claude-opus-5-5
tool: typo3-dev-companion, install, srcServerInstallerphp
directory: /home/ben/src/typo3-claudio
---

# install --agent=antigravity writes the skills but no .agents/plugins MCP entry

## Observation

Task: set up the server for Antigravity CLI (agy) in a TYPO3 core checkout.

`typo3-dev-companion install` puts the skills into .agents/skills. Antigravity finds them there. Antigravity does not find the MCP server. The user must write the server entry by hand.

In src/Server/Installer.php, the AGENTS entry for 'antigravity' has only 'skills' => '.agents/skills'. It has no 'mcp' key. Most other clients have one.

Antigravity loads a project plugin from .agents/plugins/<name>/. A working plugin has two files.

.agents/plugins/typo3-dev-companion/plugin.json:
{
  "name": "typo3-dev-companion",
  "description": "TYPO3 Dev Companion MCP server integration"
}

.agents/plugins/typo3-dev-companion/mcp_config.json:
{
  "mcpServers": {
    "typo3-dev-companion": {
      "command": "php",
      "args": ["<companion>/bin/typo3-dev-companion"],
      "cwd": "<project root>"
    }
  }
}

Antigravity honours the "cwd" key. A probe script logged its start directory during `agy -p`. With "cwd" set, the server started in the project root. Without "cwd", it started in .agents/plugins/typo3-dev-companion. In that directory, the server does not find the project. So the entry needs "cwd", or the server must find the project root itself.

Note: with this entry, Antigravity starts the server, but the handshake still fails. See the feedback "stdio answers server/discover with an id-less -32600".

## Query

typo3-dev-companion install --agent=antigravity in a TYPO3 project, then start agy in that project and run /mcp.

## Suggestion

Give 'antigravity' an 'mcp' definition in Installer::AGENTS. Let the installer write .agents/plugins/typo3-dev-companion/plugin.json and .agents/plugins/typo3-dev-companion/mcp_config.json, as it writes the skills.

Write "cwd" with the absolute project root. Do not rely on the plugin directory as the working directory. Find out if Antigravity expands a workspace variable in "cwd" or "args". If it does, use that variable, so the entry can be shared.

Give the plugin directory the same .gitignore treatment as the skill directories. Add the update and refresh paths and a 'remaining' text for Antigravity. Add a line to documentation/usage/installing.rst.
