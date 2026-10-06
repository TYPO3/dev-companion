---
date: 2026-10-06T16:44:32+00:00
category: bug
status: closed
closed: 2026-10-06
model: Gemini 3.8 Flash
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# typo3_commit_message_guide lacks clear parameter schema

## Observation

We validated a commit message for Gerrit change 93806. The tool schema does not publish parameter names. The tool rejected valid calls until we discovered required properties by trial and error.

## Query

{"changeType": "feature", "commitMessage": "..."}

## Suggestion

Publish an explicit input schema with properties message, workflow, and keyword.
