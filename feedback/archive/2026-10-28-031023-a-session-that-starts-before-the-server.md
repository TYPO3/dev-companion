---
date: 2026-10-28T03:10:23+00:00
category: bug
status: closed
closed: 2026-10-28
model: claude-opus-5-5
tool: typo3_task_guide, typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms-mcp
---

# a session that starts before the server connects gets no workflow

## Observation

Task: review Gerrit change 97177 in the typo3-cms core checkout, Claude Code. Reported by session typo3-cms-f7 on 2026-10-27 and relayed by the session that changed the setup.

Setup at that time: a user-scope MCP entry, no .claude/skills in the project, and .typo3-dev-companion/state.json still listed antigravity.

What happened:
- At the first prompt, Claude Code still listed typo3-dev-companion as connecting. The server instructions were not in the context. The session did the whole first review with curl, git and runTests.sh and made no MCP call.
- The server answers initialize in 0.3 to 0.5 seconds, measured in typo3-cms. So the client connects late, not the server.
- After the connection, typo3_task_guide named typo3-core-patch-review and typo3-core-patch-development in skills. Neither was in the listing. The instructions said "Activate the typo3-* skill that covers the task". The step led nowhere.
- Installer::absent() reads the project record only. The record named antigravity, so the server sent no Installer::ABSENT and no typo3://skill URI. A record for one client hides the absence for another client.
- Consequence: the session checked out the patch with a detached HEAD in the main checkout and ran the build there. The defined flow uses a worktree.

A second session on 2026-10-28 had the skill files back and the server connected at the first prompt. It activated typo3-core-patch-review first and called the server tools.

The headless runs behind D-DIS-034 used claude -p, which waits for the MCP connection. They did not show this race.

## Query

"bitte review mir https://review.typo3.org/c/Packages/TYPO3.CMS/+/97177" in /home/benji/projects/typo3-cms; typo3_task_guide changeType=bugfix and changeType=audit

## Suggestion

Make the absence check per client: a record without the client that started the server counts as absent for that client. clientInfo at initialize names the client.
