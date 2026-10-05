---
date: 2026-10-05T11:50:26+00:00
category: bug
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3_rule_lookup, typo3_server_scope, typo3_issue_report_guide
directory: /home/benji/projects/typo3-cms
---

# a guide names typo3_issue_report_guide, but the running server does not serve it and says nothing

## Observation

Task: draft a TYPO3 core Forge issue for a TcaPreparation @todo (oneToOne relation stores 0 instead of NULL).

typo3_rule_lookup with documentId core/contribution/reporting-an-issue returned a guide. The guide tells the agent to use typo3_issue_report_guide. It says that this tool drafts the report, checks it, searches for duplicates and returns a prefilled form link.

The session has no tool with that name. A deferred-tool search for "typo3_issue_report_guide" found nothing. The client lists all other typo3_* tools of this server.

typo3_server_scope with sections ["versions"] says that its answer keeps "the tools your list lacks". The answer had no such entry. excludedTools.names was empty. So the server itself does not report the tool as missing.

The probable cause: the server reads its guides from the checkout at call time, but it registered its tools when the process started. The checkout is newer than the process. The server instructions of this session also said that the task skills "were stale and have just been refreshed". So the server knows about a version drift for skills, but not for tools.

Result: the agent follows a guide that names a tool it cannot call. Nothing tells the agent or the user that the session or the server must restart. The agent wrote the report by hand. The user had to guess the cause.

## Query

typo3_rule_lookup {"documentId":"core/contribution/reporting-an-issue"}; ToolSearch "typo3_issue_report_guide" -> no match; typo3_server_scope {"sections":["versions"]} -> no missing tools, excludedTools empty.

## Suggestion

Compare the tool names that a guide or hint mentions with the tools that the process serves. Do this when the answer is built.

When a guide names a tool that the process does not serve, add a notice to the answer. Example: "This guide names typo3_issue_report_guide. This server process does not serve it. The checkout is newer than the running server. Restart the MCP server and reload the client session."

Also add the checkout revision and the revision at process start to typo3_server_scope. When they differ, say so in every answer, as the skill refresh notice does now.

If the client supports it, send notifications/tools/list_changed after a reload. Then the client can refresh its tool list without a new session.
