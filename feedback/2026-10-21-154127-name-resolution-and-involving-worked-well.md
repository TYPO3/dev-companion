---
date: 2026-10-21T15:41:27+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_forge_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# Name resolution and involving worked well; triage skill stayed shut for a per-person list

## Observation

Task: list Garvin Hicking's unresolved Forge issues, then only the stale ones.
What worked: reportedBy accepted a plain name and resolved it to id 37734. With curl I needed 400 issues and a Python filter for the same id.
The "open" status set matched the tracker's is_closed=false statuses. I had verified that set with issue_statuses.json.
involving removed the AND problem of author_id plus assigned_to_id. The total of 18 matched 17 reported plus 1 assigned.
No skill activated. typo3-core-issue-triage describes "issues worth working on in an area of the backlog — old or untouched ones". The request named a person, not an area. Its description did not pull me in.
For the second turn the word "stale" fits "untouched". I still did not activate it, because the work was a filter on rows I already had.
I did not call typo3_project_describe or typo3_task_guide. A read-only Forge listing needs no project context. That assumption held.

## Query

Session task: "give me a list of all of garvins issues that are not resolved", then "only show the ones that are stale".

## Suggestion

Keep reportedBy name resolution and involving as they are. Consider a phrase in the typo3-core-issue-triage description for one person's issues: "what somebody filed or holds, and which of it went stale". Then a per-person request reaches the skill.
