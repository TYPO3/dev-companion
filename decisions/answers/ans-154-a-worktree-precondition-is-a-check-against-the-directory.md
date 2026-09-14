---
id: D-ANS-154
title: A worktree precondition is a check against the directory
date: 2026-09-09
status: open
coveredBy: []
---

# D-ANS-154 — A worktree precondition is a check against the directory

**The entry states what a suite needs in the directory it mounts as a look at
that directory. It is not a property of the kind of checkout.**

Tools that provision worktrees are common enough that "a worktree has no
`vendor/`" is a rule with a false case. The same answer's other worktree caveats
have none.

## Evidence

- **The report.**
  [`feedback/2026-09-09-190318`](../../feedback/archive/2026-09-09-190318-nothing-knows-the-checkout-provisions-worktrees.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. The session ran in a
  worktree a DDEV add-on provisions, which copies the gitignored files across.
  `vendor/` and `bin/` were there and every suite ran without a
  `composerInstall`, against a precondition that says a worktree starts without
  both.
- **Nothing broke, and that is the size of it.** The composerInstall entry
  already says a checkout that has `vendor/` needs it again only after the lock
  changed. So a session that read both ran a redundant container at worst.
- **The other worktree warnings in the same answer hold whatever made the
  worktree.** `cglGit` and `cglHeaderGit` take their file list from git inside
  the container, and a worktree's gitdir sits outside the mount. So the suite
  reports success after it read nothing. `checkGruntClean` stages the whole
  working tree. The same session names both as the most valuable thing in that
  answer and reports two commands not run and one false green not reported.
- **The two axes are separable.** What the git-backed suites turn on is where
  the gitdir is, which is a property of a worktree as such. What
  `composerInstall` turns on is whether two directories are present, which is a
  fact about the directory.

## Decided

- **The precondition and the `composerInstall` entry both say to look.** A fresh
  clone has neither, and a worktree normally has neither because git ignores
  both. Whatever made the worktree may have put them there: three cases under
  one check.
- **The git-backed warnings stay as they are.** Their axis is the gitdir and
  their words already sit on it, and one report names them as what saved it.
- **Nothing here learns one machine's add-on.** The report asks for none, and
  this server cannot see by construction what a `.ddev/` git ignores carries.
- **Written on the spot.** It changes the words in
  `knowledge/test-suite-hints.json`, touches no `src/`, no schema and no skill,
  and needed no lookup about TYPO3. The report supplies the false case, and the
  answer already carried the true axis in its first sentence.

## Assumed

- That the add-on this session used is one of a kind rather than the only one.
  The report says tools that provision worktrees are common; nothing here counts
  it.

## Wrong if

- A session reports that it ran a suite without a `composerInstall` because the
  directory looked complete and the install was stale anyway. Then presence is
  the wrong check and the lock comparison has to lead.
- A report names a worktree where the git-backed suites work, because the tool
  put the gitdir inside the mount. Then those warnings are on a false axis too
  and this entry stopped one case short.
