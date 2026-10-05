---
date: 2026-10-05T10:50:18+00:00
category: idea
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# review skill requires the checkout skill, but a project can require its own worktree tool

## Observation

Task: review core patch 96354 "in a ddev branchery worktree".
The skill typo3-core-patch-review fitted the task. Keep: the order (Gerrit and Forge before the diff), the list of removals, the dropped-candidate rule, the probe-and-restore rule, and the surface table.
The skill says: "invoke typo3-core-patch-checkout for that one change". The local CLAUDE.local.md says that only Branchery (ddev branchery worktree:add) makes worktrees. No git worktree add.
I followed the local rule. I did not activate the checkout skill. So I lost what that skill knows about a fetch and a clean exit.
The skill text has no exception for a project that requires its own worktree tool.
The skill also has long passages on the deprecation sweep and changelog precedent. A review is exempt from the sweep. For a TASK patch, these passages cost reading time and gave nothing.

## Query

Skill typo3-core-patch-review with args "Gerrit 96354 ... review in a ddev branchery worktree"

## Suggestion

Say that a project rule for worktrees has priority. The checkout skill then supplies only the fetch ref and the branch name, and the project tool makes the worktree.
Move the deprecation sweep and precedent detail into a reference file. Keep one line in the skill that says when to read it.
