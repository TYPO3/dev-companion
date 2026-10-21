---
date: 2026-10-16T07:47:33+00:00
category: idea
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_rule_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# what worked: build-output page, Gerrit workflow page and chainedAt kept a 13-commit rebase safe

## Observation

Task: rebase the 13-change chain 95425..95437 onto main and rebuild the build output in each commit.
These answers worked. Keep them.
committed-build-output: it said to build the target branch first and to expect an empty status. I did that, and the baseline was clean. So no stale main output went into link 95425.
committed-build-output: it said the build can rewrite Build/package-lock.json and Sass sources. My script restored the lock file and refused other changes.
committed-build-output: it said checkGruntClean fails in a worktree and stages the whole tree. I did not run it.
committed-build-output: it said a build deletes no orphan output. I checked the chain for renames and deletions first. It had none.
gerrit-workflow: it gave the anonymous https fetch URL, the refs/changes form, and the review/NUMBER branch name. The fetch worked the first time.
gerrit-workflow: it said a plain push keeps the WIP state. After the push, all WIP changes stayed WIP.
rebasing-a-stale-patch: its committed JavaScript section gave the exact git log check for main on source and output.
gerrit_lookup: chain with chainedAt equal to patchSet on all 13 links told me that the chain was consistent before the move.
All four documents carried their procedure end to end. None sent me back to a search.

## Query

typo3_rule_lookup documentId="core/contribution/committed-build-output"; documentId="core/contribution/gerrit-workflow"; documentId="core/contribution/rebasing-a-stale-patch"; typo3_gerrit_lookup change="95437"

## Suggestion

Keep these sections as they are. The measured lines ("Measured on 2026-08-26 ...") made me trust the steps without a test run of my own.
