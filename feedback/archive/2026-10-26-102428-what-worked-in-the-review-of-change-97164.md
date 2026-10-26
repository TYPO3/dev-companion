---
date: 2026-10-26T10:24:28+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3_gerrit_lookup, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# what worked in the review of change 97164: gerrit fetch ref, suite list, commit checks

## Observation

Task: review and amend Gerrit change 97164.

typo3_gerrit_lookup gave the fetch ref, the current patch set, the commit hash and mergeable in one call.
The agent fetched the patch set with that ref and no guess. The hash matched the fetched commit.
After the push, the same lookup showed patch set 2 and the dropped Verified vote.

typo3_test_run_guide gave the targeted functional invocation and the warning about cglGit in a worktree.
It also named the suites the agent did not run. The agent could list them in the report.

typo3_commit_message_guide with workflow core found no error and a subject length warning only.
It kept the Change-Id unchanged.

The agent did not report every one of these results correctly. The tools gave correct data.

## Query

typo3_gerrit_lookup change=97164 messages=people; typo3_test_run_guide with the changed paths of change 97164; typo3_commit_message_guide workflow=core with the message of change 97164.

## Suggestion

Keep these answers stable.
Keep fetch.ref, patchSet, commit and mergeable in the typo3_gerrit_lookup answer.
Keep the worktree warning for cglGit and checkGruntClean in typo3_test_run_guide.
