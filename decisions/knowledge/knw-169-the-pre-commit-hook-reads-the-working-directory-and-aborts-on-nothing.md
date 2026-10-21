---
id: D-KNW-169
title: The pre-commit hook reads the working directory and aborts on nothing
date: 2026-10-21
status: open
---

# D-KNW-169 — The pre-commit hook reads the working directory and aborts on nothing

**The core's pre-commit hook runs a space check over the whole working
directory, ignored folders included. Its abort switch never takes effect, so it
aborts on nothing. "The Pre-Commit Hook" in `core/testing/scripts` says both.**

## Evidence

- [`feedback/2026-10-21-153139-pre-commit-space-check`](../../feedback/archive/2026-10-21-153139-pre-commit-space-check-scans-git-ignored-files.md).
  A commit in a worktree printed "There are spaces in file or path names" for
  `./.cache/.config/chromium/Crash Reports`. `.cache` is git-ignored. The
  session read the hook and the script to learn that.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main` on 2026-10-21.
  `Build/Scripts/spacesInPathsAndFilenames.sh` is the same file on all four,
  `find . -name "* *"` with no git in it. The hook calls it after the two
  scripts the section named.
- The hook sets `ABORT_ON_ERROR` from `TYPO3_GIT_HOOK_ABORT_ON_ERROR` only where
  that variable is unset. Run on 2026-10-21, the test leaves `no` where the
  variable is `yes` and empty where it is unset. The section said the hook
  aborts where the variable is `yes`, which no branch does.

## Decided

- The section gains the third script, its message and what it reads, and the
  abort sentence is corrected. Both hold on every covered line, so neither has
  a bound.
- What CI sees is said from the script rather than from the pipeline. A fresh
  clone carries no ignored folder, which is all the sentence claims.

## Wrong if

- A core branch fixes the abort test, and the section says the hook aborts on
  nothing while it aborts.
