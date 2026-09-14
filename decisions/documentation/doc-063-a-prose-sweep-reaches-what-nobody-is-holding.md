---
id: D-DOC-063
title: 'A prose sweep reaches what nobody is holding'
date: 2026-08-27
status: open
coveredBy:
  - ProseFormatTest
---

# D-DOC-063 — A prose sweep reaches what nobody is holding

**`bin/cli prose:format` named no path rewraps what its own checkout has in
hand. In a worktree that is the files its branch changed. In the checkout `main`
stands on it is the corpus minus what the live claims changed.**

A rewrap of a file another branch holds is a conflict whichever side lands
first. The sweep is what put the whole corpus on both sides of that.

## Evidence

- Two claimed branches carried a corpus rewrap on 2026-08-27,
  `todo/T-260824-bd21` and `todo/T-260824-ea52`. Both stopped `todo:home` on a
  conflict with cards `main` had rewritten or deleted, and both resolved to what
  `main` said. After that one of the two rewrap commits reduced to empty and
  dropped out.
- What made it likely is that cards rewrap in bulk. The sweep of `d386831b`
  landed on 22 open cards at once. Every one without a judgement carried the
  same boilerplate paragraph a character past the column.
- The other direction conflicts too, measured in a scratch repository the same
  day. A branch that deletes a card the checkout rewrapped is
  `CONFLICT (modify/delete)` on the rebase. A rule about worktrees alone would
  have left that half open.
- The sweep is not the part to give up. On 2026-08-04, session after session had
  reverted 23 files and they stayed permanently behind the formatter. That is
  what a run over everything was for.

## Decided

- **Where the command stands decides what it sweeps.** `Todo::linked()` already
  tells a claim's checkout from the one it came from, and both halves fall out
  of it. The branch's own changed files, or the corpus minus every live
  worktree's.
- Changed means against `main`, committed or not, so the sweep holds a decision
  file a session has written and not added too.
- A named path stays the caller's word and reaches whatever it matches. The
  narrow form is the default, which is what a session runs without a thought
  about it.
- Rejected: a `prose:format` that only ever rewraps what the branch changed. It
  is the same command in the main checkout, where nothing is in hand and the
  answer would be nothing at all.
- Rejected: a `todo:home` that resolves the class. It aborts the rebase and
  reports. With the source closed this shape remains for a session that really
  did rewrite a card another one deleted. There the right answer is not the same
  every time.

## Assumed

- That a claim comes from `main` and rebases onto it, which is what a sweep
  compares a checkout against and what `todo:home` already assumes.
- That two claims on one file is the overlap `todo:claim` reports before the
  worktrees exist, and not something a formatter has to arbitrate.
- That two git calls per live worktree are cheap enough to spend on every sweep.

## Wrong if

- A rebase in `todo:home` conflicts on a file whose only difference is where the
  lines break.
- The corpus falls behind the formatter again, because a worktree left for weeks
  keeps its files out of every sweep.
- Somebody has to name a path to get a file rewrapped that no claim holds.
