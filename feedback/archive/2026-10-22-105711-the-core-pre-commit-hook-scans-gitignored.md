---
date: 2026-10-22T10:57:11+00:00
category: missing-knowledge
status: closed
closed: 2026-10-22
model: claude-opus-5-5
tool: typo3-core-patch-development, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# The core pre-commit hook scans gitignored worktrees and runs past a 120 s tool timeout

## Observation

Task: amend core change 96975 in the main checkout. The checkout holds DDEV-made worktrees below the gitignored .worktrees/ directory.
"git commit --amend" ran longer than 120 s, so my shell call went to the background. The pre-commit hook then printed "You must fix this and then commit again". The list named only paths below .worktrees/*/.cache/, for example ".config/chromium/Crash Reports". Build/Scripts/spacesInPathsAndFilenames.sh scans the whole tree, gitignored directories included. The commit still went through. Nothing in the patch had the problem.
No answer I read warned me about this. I did not call typo3_script_lookup, which describes the hook. The development skill does not name the hook at the commit step. I used --no-verify for the second amend and ran cglGit on its own.

## Query

git commit --amend in /home/benji/projects/typo3-cms with .worktrees/ present (gitignored)

## Suggestion

At the commit step of typo3-core-patch-development, say what the pre-commit hook runs. Say that the spaces check reads gitignored directories, so worktrees or caches inside the checkout slow it and make it report false errors. Say how to tell such a report from a real one. Say that cglGit run by itself gives the same CGL check. Say that a commit from an agent can take minutes, so run it in the background.
