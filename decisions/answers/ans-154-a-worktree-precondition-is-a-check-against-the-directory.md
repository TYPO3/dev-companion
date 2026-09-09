---
id: D-ANS-154
title: A worktree precondition is a check against the directory
date: 2026-09-09
status: open
coveredBy: []
---

# D-ANS-154 — A worktree precondition is a check against the directory

**What a suite needs in the directory it mounts is stated as a look at that
directory, not as a property of the kind of checkout it is.**

Tooling that provisions worktrees is common enough that "a worktree has no
`vendor/`" is a rule with a false case, and the same answer's other worktree
warnings have none.

## Evidence

- **The report.**
  [`feedback/2026-09-09-190318`](../../feedback/archive/2026-09-09-190318-nothing-knows-the-checkout-provisions-worktrees.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. The session ran in a
  worktree a DDEV add-on provisions, which copies the gitignored files across.
  `vendor/` and `bin/` were there and every suite ran without a
  `composerInstall`, against a precondition saying a worktree starts without
  both.
- **Nothing broke, and that is the size of it.** The composerInstall entry
  already says a checkout that has `vendor/` needs it again only after the lock
  changed, so a session reading both ran a redundant container at worst.
- **The other worktree warnings in the same answer hold whatever made the
  worktree.** `cglGit` and `cglHeaderGit` take their file list from git inside
  the container and a worktree's gitdir sits outside the mount, so the suite
  reports success having read nothing; `checkGruntClean` stages the whole
  working tree. The same session names both as the most valuable thing in that
  answer and reports two commands not run and one false green not reported.
- **The two axes are separable.** What the git-backed suites turn on is where
  the gitdir is, which is a property of being a worktree. What `composerInstall`
  turns on is whether two directories are present, which is a fact about the
  directory.

## Decided

- **The precondition and the `composerInstall` entry both say to look.** A fresh
  clone has neither, a worktree normally has neither because both are
  gitignored, and whatever made the worktree may have put them there — three
  cases under one check.
- **The git-backed warnings stay as they are.** Their axis is the gitdir and
  their wording is already on it, and one report names them as what saved it.
- **Nothing here learns one machine's add-on.** The report asks for none, and
  what a `.ddev/` that is gitignored carries is invisible to this server by
  construction.
- **Written on the spot.** It rewords `knowledge/test-suite-hints.json`, touches
  no `src/`, no schema and no skill, and needed nothing looked up about TYPO3 —
  the report supplies the false case and the answer already carried the true
  axis in its first sentence.

## Assumed

- That the add-on this session used is one of a kind rather than the only one.
  The report says worktree-provisioning tooling is common; nothing here counts
  it.

## Wrong if

- A session reports running a suite without a `composerInstall` because the
  directory looked complete and the install was stale anyway. Then presence is
  the wrong check and the lock comparison has to lead.
- A worktree is reported where the git-backed suites work, because the tooling
  put the gitdir inside the mount. Then those warnings are on a false axis too
  and this entry stopped one case short.
