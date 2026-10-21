---
date: 2026-10-21T15:31:39+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3-core-issue-triage, typo3-core-patch-review, typo3_gerrit_lookup, typo3_forge_lookup, typo3_task_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# What worked: triage order, watch-it-fail rule, path history in Gerrit, cglGit worktree warning

## Observation

Task: triage an old Forge issue, then fix, review and push it. These parts worked and must stay. 1) The stale backlog rows carried cites with shipped state, so I picked #79571 fast. 2) The triage skill told me to search for an existing test first. ObjectStorageConverterTest already pinned 'foo' to an empty storage. 3) typo3_gerrit_lookup with path found the abandoned attempts 95440 and 37651 and showed they were about another case. 4) The rule "a green that ran over no files is not a green" made me repeat a red check that printed nothing. The repeat showed the 2 expected failures. 5) task_guide said cglGit reads nothing in a git worktree, so I used cgl -n. 6) The changelog rule (BUGFIX that changes rendered output owes Important) and the release rule (main and one line back) gave a clear answer. 7) commit_message_guide caught a 65-character subject. 8) The review checklist made me search for a precedent: CheckboxViewHelper has the same check since 2013. That precedent became the main argument of the fix.

## Query

Whole session: Forge #79571 triage, fix in SelectViewHelper, review, push to Gerrit as change 96975

## Suggestion

Keep these. The handover sentences between the three skills fired correctly in this session.
