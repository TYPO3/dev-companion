---
date: 2026-10-05T10:50:18+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_test_run_guide, typo3_commit_message_guide, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# what worked in a core patch review: cglGit worktree warning, release-line check, setup hint

## Observation

Task: review core patch 96354 in a git worktree.
These answers changed what I did. Do not break them.
1. typo3_test_run_guide said cglGit reads no file in a git worktree and still reports SUCCESS. I ran "cgl -n" instead. It checked 6520 files.
2. typo3_test_run_guide said e2e-prepare needs a terminal. I did not start it without one.
3. typo3_commit_message_guide warned that a TASK does not go to 13.4. This became the main finding of the review.
4. typo3_commit_message_guide reported the missing Signed-off-by as an error.
5. Hint installation-setup explained the sqlite path, the random file name and --force. I used it to plan a probe of the patch.
6. typo3_gerrit_lookup gave the fetch ref, the commit of the current patch set and the votes in one call.

## Query

Whole session: review of Gerrit change 96354 in a ddev branchery worktree.

## Suggestion

Keep these answers as they are. Add regression checks for the cglGit worktree warning and the older-release-line warning.
