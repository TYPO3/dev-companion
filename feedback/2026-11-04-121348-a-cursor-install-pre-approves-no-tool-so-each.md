---
date: 2026-11-04T12:13:48+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3-dev-companion install
directory: /home/lina/projects/typo3/tools/dev-companion
---

# a cursor install pre-approves no tool, so each lookup stops the session

## Observation

The task was a trial on 2026-11-03. Two cursor-agent runs built a TYPO3 site from the static design in design/.

`install --agent=cursor` wrote .cursor/mcp.json and the 15 skills into the arm. The entry used plain php, which is correct for a project with no DDEV configuration yet.

cursor then asked the operator before each MCP tool of this server. `--approve-mcps` approves the server and not its tools, so it did not help.

The operator answered "always allow" per tool. cursor appends that answer to ~/.cursor/cli-config.json, under permissions.allow. An entry there reads `Mcp(typo3-dev-companion:typo3_hint_lookup)`. The file's approvalMode was "allowlist".

Before the run, that list held five tools of this server, left by an earlier session. This server declares 32 tools. So a first task on cursor meets a prompt for most of them.

The run reached its first `ddev start` 52 minutes after the prompt. It spent that time on files and on approval prompts. The control arm had no server and met no such prompt. It reached the same point in about 18 minutes.

The installer's own output says cursor "asks for approval before using an MCP tool". So the install states the cost and writes nothing against it.

## Query

`typo3-dev-companion install --agent=cursor` in an empty project directory. Then cursor-agent 2026.10.22 with `--model grok-4.7-high --sandbox enabled --approve-mcps` and the prompt: "I'm a PHP developer and I have never used TYPO3. There is a static HTML design in design/. I want to build this site in TYPO3."

## Suggestion

`install --agent=cursor` writes the read-only tools into the client's allowlist. Where it cannot write that file, it prints the one command that writes it.

It leaves `typo3_feedback_record` out, because that tool writes.

A project-level file is the better place than the user's global config. A session has to confirm from cursor's own documentation whether `.cursor/cli.json` carries permissions. This session could not confirm it, because the bundled binary holds no such string.

documentation/usage says that `--approve-mcps` approves the server and not its tools. A reader who knows that can prepare the allowlist by hand.

The repair is measured by the time to the first shell command in a fresh cursor arm.
