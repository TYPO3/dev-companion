---
date: 2026-10-05T10:50:18+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# core check keeps an Assisted-by trailer and reorders trailers without a check

## Observation

Task: review core patch 96354. I checked its commit message with workflow core.
The message had "Assisted-by: Claude Code (Claude Opus 5.5) <noreply@anthropic.com>".
The core AGENTS.md says "Do not credit tooling or assistants in commit messages". origin/main has 0 commits with an Assisted-by trailer.
The draft kept the Assisted-by line. No check named it. The description says the core workflow removes Co-Authored-By and an agent session trailer. Assisted-by is the same kind of trailer.
The message had "Releases:" before "Resolves:". The draft put "Resolves:" first without a check. In the last 200 main commits, the order is Resolves then Releases 198 times, and the other order 2 times.
A reviewer reads the checks, not a diff of the draft. So the reorder was not visible as a finding.
The other checks were correct and useful: the missing Signed-off-by error and the 13.4 older-release-line warning.

## Query

typo3_commit_message_guide(workflow="core", keyword="TASK", message="[TASK] Speedup e2e install by respecting initCommands in setup command ... Releases: main, 14.3, 13.4 / Resolves: #110911 / Assisted-by: Claude Code (Claude Opus 5.5) <noreply@anthropic.com> / Change-Id: Ib4a8bb948ef2836ca02c980c284a786f9498b1bb")

## Suggestion

In workflow core, treat Assisted-by (and similar tool-credit trailers) like Co-Authored-By: remove it from the draft and add a check that cites the AGENTS.md rule.
When the draft changes the trailer order, add an info check that says so.
