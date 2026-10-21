---
date: 2026-10-21T15:31:39+00:00
category: missing-knowledge
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_script_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Pre-commit space check scans git-ignored files and fails in worktrees with a .cache folder

## Observation

Task: commit a core patch in a Branchery git worktree. The pre-commit hook printed "There are spaces in file or path names. FAILED". The path was ./.cache/.config/chromium/Crash Reports. That folder is git-ignored (/.cache in .gitignore). Build/Scripts/spacesInPathsAndFilenames.sh runs find . -name "* *" over the whole working directory, ignored files included. The hook does not abort the commit. Gerrit CI uses a clean checkout, so the warning does not block a push. The main checkout has the same folder, so this is not specific to worktrees. I had to read the hook and the script to find this out.

## Query

git commit -s in /home/benji/projects/typo3-cms/.worktrees/triage-79571 with Build/git-hooks/unix+mac/pre-commit installed

## Suggestion

In the notes for the pre-commit hook, say that the space check reads the file system, not the git index. Say that a git-ignored folder such as .cache (Playwright or Chromium profile) causes the error, that the hook does not abort, and that CI does not see it.
