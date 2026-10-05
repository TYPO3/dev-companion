---
date: 2026-10-05T11:51:25+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_server_scope, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# the server does not check its upstream repository for updates, although it is a rolling release

## Observation

Task: finish TYPO3 core change 93806 and draft a Forge issue. During the session, a guide named a tool that the running server did not serve. A separate feedback covers that drift between the checkout and the running process.

This feedback covers the next layer. The server is a rolling release. Its maintainer pushes guides, hints, skills and tools to the upstream repository continuously. The local checkout of the server does not know when upstream moved on. No answer says that the checkout is behind upstream. The agent and the user find out only when an answer is wrong or a named tool is missing.

The server already says when its skills were refreshed. That notice only covers what is already in the local checkout. It does not cover what upstream has and the checkout does not have yet.

The user asked for this explicitly: because the system is rolling, the server should check upstream regularly and say when it must be updated.

## Query

Whole session: review and finish Gerrit change 93806 in a Branchery worktree, then draft a Forge issue with typo3_rule_lookup documentId core/contribution/reporting-an-issue.

## Suggestion

Check the upstream repository of the server at regular intervals. A git fetch and a comparison of HEAD with the upstream branch is enough. Keep the interval short, for example once per hour or at most once per session start. Cache the result, so that no call waits for the network.

When the checkout is behind upstream, add one notice to every answer. The notice names the number of commits behind and the commands that update the server: pull the checkout, restart the MCP server, reload the client session.

Show the same state in typo3_server_scope: local revision, upstream revision, time of the last check, and whether an update is necessary.

Do not update automatically without the user. A pull changes the guides that the current session already follows. Only report it, and let the user decide.

When the network is not available, say that the check could not run, with the time of the last successful check. Do not report "up to date" in that case.
