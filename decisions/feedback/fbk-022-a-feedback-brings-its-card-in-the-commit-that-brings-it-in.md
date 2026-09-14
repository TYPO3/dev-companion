---
id: D-FBK-022
title: A feedback brings its card in the commit that brings it in
date: 2026-08-02
status: revoked
revokedBy: D-FBK-045
coveredBy: []
---

# D-FBK-022 — A feedback brings its card in the commit that brings it in

**A commit that touches `feedback/` runs `bin/cli todo:sync` in a tracked
pre-commit hook and stages the cards it wrote.**

Two guards held the relation and neither of them runs at a commit. A hook is
where the repair belongs, because that is the moment the pair comes apart.

## Evidence

- Commit `8ef3bba` brought 20 feedback in with no card. `bin/cli todo:check`
  reported them the next time somebody ran it, which was the following session.
- Both guards existed at the time. `bin/cli todo:check` is a command somebody
  has to type. `TodoTest::everyOpenFeedbackIsOnTheBoard` runs in `composer ci`,
  which is what CI runs on a push rather than what a commit runs.
- [`D-FBK-016`](fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md)
  named this under **Assumed** — "That the sync is run. Nothing calls it on a
  schedule" — and under **Wrong if**. Something calls it now.
- The hook costs 3 ms on a commit that touches no feedback, because it reads the
  staged names before it reads anything else.

## Decided

- It repairs rather than refuses. `bin/cli todo:sync` is idempotent and writes
  exactly what the session would have to write anyway. A failed commit would
  cost a second attempt and produce the same files.
- It runs only where the staged change touches `feedback/`, which is the only
  way the pair can come apart.
- `.githooks/` is in git and composer's `post-install-cmd` sets
  `core.hooksPath`. So the hook arrives with the dev dependencies rather than in
  a setup step somebody has to hear about.
- A failure of the command is not a failure of the commit. `vendor/` may be
  absent and `bin/cli` may exit nonzero. CI holds the same relation either way,
  and a hook that blocks a commit it cannot repair is worse than the drift.
- Rejected: the card from `typo3_feedback_record`, which would remove the step
  instead of guard it. `src/Feedback/` is the channel both halves share and
  `src/Upkeep/`, which owns what a todo is, already reads it. A card from there
  would make that a cycle.

## Assumed

- That `composer install` has run in every checkout somebody commits from. One
  that never installed the dev dependencies has no hook, and nothing says so.
- That the hook is not bypassed. `git commit --no-verify` is one flag away.
- That a worktree has the directory. `core.hooksPath` sits in `.git/config`,
  which every worktree shares, and it is relative. So a worktree from before
  this commit points at a `.githooks/` that is not there, and git runs nothing
  and says nothing.
- That `bin/cli todo:sync` still prints one written path per line. The hook
  reads that output to know what to stage, and no test holds the format.

## Wrong if

- Feedback arrive with no card again and the commit came from a checkout where
  the hook was never enabled. Then the step that enables it is what to remove.
  The hook would have to be something without which nobody can commit to the
  repository.
- The stage surprises somebody. A commit comes out with a file the session did
  not write. The card is wrong because the same change should have trimmed or
  closed the feedback.
- `bin/cli todo:check` no longer reports unserved feedback on the assumption
  that the hook handled it. The hook covers the commits made here; the check
  covers the ones made anywhere else.

## Revoked on 2026-08-14

The rejection at the foot of **Decided** fell and took the entry with it.
[`D-FBK-045`](fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md) has
`typo3_feedback_record` write the card in the call that records the feedback. So
there is no sync to run and no commit for the hook to run it on; both are gone,
and `.githooks/` with them. What the cycle argument settled was where the code
sits rather than whether the card comes from there. The write moved to
`Feedback\Card`, which `Upkeep/` reads like the rest of the channel.

The three **Assumed** are what this cost while it stood. Every one of them was
about a checkout where the hook was off, bypassed, or pointed at a `.githooks/`
that was not there. What replaces them is that the call that writes the feedback
writes the card, wherever that call came from.
