---
id: D-SKL-095
title: The checkout skill knows no worktree tool but git
date: 2026-10-05
status: open
---

# D-SKL-095 — The checkout skill knows no worktree tool but git

**`typo3-core-patch-checkout` describes `git worktree` and nothing else. A
worktree tool that one checkout prescribes is that checkout's business.**

## Evidence

- `feedback/archive/2026-10-05-094211-checkout-skill-assumes-a-detached-worktree.md`.
  A session's checkout prescribed an external worktree tool that takes only a
  branch. The skill offered a detached worktree, and the session created a
  review branch at the fetched commit by itself. The undo the skill names fitted
  at the end.
- The maintainer decided on 2026-10-05 that the external tool has no relevance
  here.

## Decided

- No case in the skill for a tool outside git. The session that met one solved
  it from the skill's own branch convention, and the undo held.

## Wrong if

- A session follows such a tool past what the skill's undo covers, and leaves a
  branch or a worktree behind that it reports as removed.
