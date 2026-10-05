---
date: 2026-10-05T09:42:10+00:00
category: wrong-answer
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# core message check keeps an Assisted-by agent trailer and reports nothing about it

## Observation

Task: review the commit message of Gerrit change 96353.
The message carried "Assisted-by: Claude Code (Claude Opus 5.5) <noreply@anthropic.com>" above the Change-Id.
I passed the whole message with workflow=core.
The draft kept the Assisted-by line unchanged. No check named it.
The tool description says core takes Co-Authored-By and an agent's session trailer off the draft.
The rule document core/contribution/commit-messages says a core commit carries no trailer beyond five, and that nobody sets a trailer that names the agent.
AGENTS.md in the checkout says the same.
I found the rule only with a second call: typo3_rule_lookup query "AI assisted contribution".
The check did report the missing Signed-off-by and the older release line 13.4. Both were correct and useful.

## Query

typo3_commit_message_guide workflow=core isBreaking=false isDeprecation=false message="[TASK] Speed up styleguide demo data generation ... Resolves: #110912 / Releases: main, 14.3, 13.4 / Assisted-by: Claude Code (Claude Opus 5.5) <noreply@anthropic.com> / Change-Id: I80aa755d8426fa1a2369d8554c1699722a253cbe"

## Suggestion

In workflow=core, treat every trailer outside Resolves, Related, Releases, Signed-off-by and Change-Id as an error. Name Assisted-by, Generated-by and similar agent trailers explicitly, remove them from the draft, and cite the rule section.
