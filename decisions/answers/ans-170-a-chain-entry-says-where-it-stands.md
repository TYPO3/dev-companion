---
id: D-ANS-170
title: A chain entry says where it stands
date: 2026-10-05
status: open
coveredBy:
  - GerritTest::aChainEntrySaysWhetherItIsAboveOrBelowTheChange
---

# D-ANS-170 — A chain entry says where it stands

**Each chain entry of `typo3_gerrit_lookup` carries `place`: above, this or
below. Whether a patch reaches the branches its `Releases:` names is one
`git merge-tree` per branch, which the Gerrit workflow page gives.**

## Evidence

- `feedback/archive/2026-10-05-105018-chain-does-not-say-which-change-sits-below-and.md`.
  The answers for 96354 and 96353 listed the same two changes child first. The
  session did not read the order and went to `git log` for the parent. The
  header line already counted what stood above and below.
- The same session checked `Releases: main, 14.3, 13.4` with a temporary index
  and `git apply --check -3`, three calls outside the server. It found a missing
  file on both release lines and conflicts on both.
- Read on 2026-10-05: `mergeable?other-branches` on review.typo3.org answers
  `mergeable_into: []` for five open changes meant for `14.3`. The project
  config names no `branch_order`, which is the list Gerrit checks.
- `git merge-tree --write-tree --merge-base FETCH_HEAD~1 <branch> FETCH_HEAD` on
  git 2.55 reproduced the session's result for 96354 against `14.3` and `13.4`.
  It exits 1 on a conflict and 0 on a clean merge, and it touches no tree.

## Decided

- `place` is a field, because the order is a convention a reader has to know.
  The text line says it in words as well.
- The server reads no branch. `doesNotCover` keeps git with the caller, and the
  review server has no answer for another branch. So the page gives the one
  call, and the review skill points at it.

## Wrong if

- The project gains a `branch_order`, and `mergeable_into` starts to answer.
  Then the server can say it without git.
- A session runs `git merge-tree` on a git older than the `--merge-base` option
  and reads the error as a conflict.
