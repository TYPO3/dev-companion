---
date: 2026-11-27T19:42:25+00:00
category: tool-gap
status: open
model: claude-opus-5-5
tool: typo3_gerrit_lookup, typo3-core-patch-checkout
directory: /home/benji/skill13-core
---

# mergeable=false names no paths, so a branch and a cherry-pick were needed to see the conflicts

## Observation

Task: cherry-pick Gerrit change 87873 onto current main to run tests. The answer said mergeable=false. The conflicts field was an empty list. That field is about conflict markers in the patch set, not about the merge. So I had to create review/87873, fetch the patch set and cherry-pick it. Nine files conflicted, five of them generated JavaScript. Then I aborted, switched back and deleted the branch. The gerrit-workflow page has a git merge-tree command that lists conflicting paths without a change to the checkout. That command sits in the backport section only. The skill did not route me to it for the main branch. The name "conflicts" made me expect the conflicting paths.

## Query

typo3_gerrit_lookup change="87873" returned mergeable=false, conflicts=[]

## Suggestion

Where mergeable is false, tell the caller to run git merge-tree --write-tree --name-only --merge-base FETCH_HEAD~1 origin/<target> FETCH_HEAD first. Put this as a step in typo3-core-patch-checkout before the branch is created. Rename or describe the conflicts field so that it clearly means markers in the patch set. Optionally, compute the conflicting paths on the server.
