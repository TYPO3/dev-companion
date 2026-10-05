---
date: 2026-10-05T09:42:11+00:00
category: idea
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3-core-patch-checkout, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# checkout skill assumes a detached worktree; project worktree tools need a named branch

## Observation

Task: put change 96353 into a worktree that the project's own tool builds (DDEV add-on Branchery, required by CLAUDE.local.md).
The skill says a fetched patch set belongs on no local branch, so a detached checkout or a detached git worktree holds it.
Branchery worktree:add takes only a branch. It also copies vendor, provisions a database and serves a URL.
I created review/96353 at FETCH_HEAD, without a carry, as earlier review-N worktrees in the checkout did. Then I ran worktree:add with that branch.
The skill had no case for that. Its undo list (delete the branch you carried onto) did fit at the end, and worktree:remove deleted the branch.
The gerrit-workflow page did give the correct https fetch URL and the ref. That worked first time.

## Query

Skill typo3-core-patch-checkout with ARGUMENTS "Change 96353 into a branchery worktree (ddev branchery worktree:fork/add per CLAUDE.local.md), for review"

## Suggestion

Add a case: where the repository requires a worktree tool that takes a branch, create review/<change> at the fetched commit with no carry, say that the branch holds the unchanged patch set, and remove it with the tool. Ask the user before you create the branch only where no review-N precedent exists.
