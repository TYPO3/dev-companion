---
id: D-FBK-045
title: A feedback is queued by the call that records it
date: 2026-08-14
status: open
coveredBy:
  - FeedbackTest::aRecordedFeedbackArrivesWithTheCardThatAsksForItsJudgement
  - FeedbackTest::theToolReportsTheCardTheFeedbackWasQueuedAs
  - TodoTest::everyOpenFeedbackIsOnTheBoard
---

# D-FBK-045 — A feedback is queued by the call that records it

**`typo3_feedback_record` writes the card that asks for the judgement, in the
same call that stores the report.**

A command from a pre-commit hook used to write the card, so the board was right
on the checkout that committed and nowhere else.

## Evidence

- 13 feedback recorded on 2026-08-13 stood in the work tree with no card.
  `TodoTest::everyOpenFeedbackIsOnTheBoard` named all 13, and the repair was
  `bin/cli todo:sync` run by hand.
- The hook fires on a commit that touches `feedback/` in this checkout. A
  session that records a feedback and works on has its report on disk and
  nothing on the board until somebody commits here. That may be another session
  on another day.
- [`D-FBK-022`](fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md)
  rejected exactly this, on the grounds that `Upkeep/` already reads `Feedback/`
  and a card written from there would make that a cycle. The cycle is real; what
  it argues about is where the code sits.

## Decided

- `Channel::record()` writes the card after the feedback, so the pair is never
  apart and nobody has to know a step.
- What a card is moves to `Feedback\Card`: the step, the priority, the name it
  takes from the feedback. `Upkeep\Todo` reads it from there, so the arrow
  between the two groups still points one way.
- `bin/cli todo:sync` and the pre-commit hook that ran it are gone. A repair
  kept for a case the record call no longer produces is a second way for a card
  to come about. This repository writes cards one way.
- `.githooks/` goes with the hook, and so does the `composer install` step that
  pointed git at it. It held one hook and that hook was the sync.
- What remains of the relation is the report. `bin/cli todo:check` names each
  open feedback no todo answers for, and
  `TodoTest::everyOpenFeedbackIsOnTheBoard` fails on it. The repair is a card in
  `todo/open/` by hand, which is what a feedback that got here by hand costs.
- The card goes into the checkout that stored the feedback rather than
  `Paths::root()`. That is the same directory in an installation and is not one
  in a test that writes into a store of its own, `R-COD-003`.
- The tool answers with the card as `todo`, because the caller learns where its
  report waits rather than only where it landed.
- Rejected: the card from `Upkeep`, which is what `D-FBK-022` measured the cycle
  against.

## Assumed

- That every feedback belongs on the board. That is what the sync did for every
  open one; a report nobody wants judged has no state that says so.
- That a session which records and closes a feedback in one breath deletes the
  card with it. The judgement ladder already asks for that deletion, and it is
  now possible one commit earlier than before.
- That the store's parent is a checkout of this repository. Pointed elsewhere
  with `TYPO3_DEV_COMPANION_FEEDBACK_DIR`, the record call creates a
  `todo/open/` beside whatever directory the variable named.
- That a queue nobody can write to is a checkout that could not have stored the
  feedback either. The write throws where it fails, which fails the record call
  after the report is on disk.
- That nothing else wrote feedback files. A script or an editor that puts one
  below `feedback/` now leaves the board short, and what used to repair that is
  gone.

## Wrong if

- A record call reports failure while the feedback is there, because the card
  write failed. Then the card is what gets swallowed, not the report.
- Cards arrive that a session did not mean to bring. The same change should have
  trimmed or closed the feedback, and the card's deletion becomes routine enough
  that a real one goes with it.
- `bin/cli todo:check` starts to report feedback with no card again, often
  enough that a card by hand is a step people know by heart. Then the repair
  carried something, and what it carried is what to find.
