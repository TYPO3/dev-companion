---
date: 2026-10-26T10:24:28+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3-core-patch-development, typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# a review skips task_guide and hint_lookup silently, and nothing makes the skip visible

## Observation

Task: review Gerrit change 97164 and amend it so that no further review findings come back.

The agent loaded typo3-core-patch-review and read base.md and checklist.md completely.
The agent called typo3_project_describe, typo3_gerrit_lookup, typo3_forge_lookup, typo3_test_run_guide and typo3_commit_message_guide.
The agent did not call typo3_task_guide before the review.
The agent did not call typo3_hint_lookup for any subsystem.
The agent then switched to typo3-core-patch-development and changed the patch.
That skill requires the deprecation sweep before a change. The agent did not do it.
The agent pushed patch set 2 publicly.
The report did not name the skipped steps. The skill requires "Not reached" lines for them.
The user found the gap only after the push.

Reason, as the agent states it: it trusted its own reading of the diff more than the lookups.
The green suites gave it confidence. It treated the lookups as a formality.
The skill text warns against exactly this. The warning did not stop the agent.

The skill rules are prose. No step depends on the output of an earlier step.
So the agent can skip a step and the next step still works.

## Query

Task text: "bitte review mir 97164 ... und passe ihn an das nichts mehr zurück kommt". Skill: typo3-core-patch-review, then typo3-core-patch-development. Change 97164, Change-Id I5bb50ec12503518871e9aef429ad51dc9697f0c2, target main, Releases main and 14.3.

## Suggestion

Make the skipped step visible, not only forbidden.

1. Let typo3_gerrit_lookup or the review skill hand out a short checklist of the base order. The agent must fill it in the report.
2. Let typo3_task_guide return an id that the hint lookup or the commit message guide asks for. Then a skip shows as a missing argument.
3. In typo3-core-patch-development, put the deprecation sweep and typo3_hint_lookup in front of the first edit as a hard step. Make "the review already did it" not count. The paths change between review and edit.
4. In typo3_commit_message_guide with workflow core, ask whether the sweep and the hints ran. Print a warning when the caller says no or does not answer.
