---
id: D-FBK-017
title: A judgement turns a feedback into work, and the work closes it
date: 2026-08-02
status: open
coveredBy:
  - TodoTest::everyOpenFeedbackIsOnTheBoard
  - TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead
---

# D-FBK-017 — A judgement turns a feedback into work, and the work closes it

**A feedback goes to the archive when the change it asked for has landed, not
when somebody has decided about it.**

Between the two sits a third state, which is where most feedback are: judged,
with the work queued and the report still open. Archiving at the judgement would
tell an agent its report was dealt with while the thing it reported is still
there.

## Evidence

- The two states were about to become one. The board arrived on 2026-08-02 with
  a card per feedback
  ([`D-FBK-016`](fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md)),
  and the process around it read: judge, derive todos, archive. Under that order
  every derived todo would serve an archived feedback, which
  `Todo::unreadable()` reports as a problem. So the alternative was not merely
  worse, a check written for a different reason already refused it.
- What archiving says outward. `typo3_feedback_list` returns an archived
  feedback as `status: closed` with the commit that closed it.
  [`R-FBK-002`](../../requirements/feedback/fbk-002-a-feedback-that-was-worked-off-stays-answerable-for.md)
  records what a wrong answer there costs. A second report of a request that had
  shipped, because the agent read an absent file as lost.

## Decided

- Three states, and the middle one is not a place. Open with nothing that serves
  it awaits a judgement, open with a todo that serves it has one, and the
  archive means the improvement landed. AGENTS.md already said the third; what
  is new is the two names for the first two, because the board made the
  difference visible.
- **The invariant.** A commit that judges a feedback either archives it or
  leaves at least one todo that serves it. Anything else drops it back to
  unjudged, where the next `bin/cli todo:sync` writes a fresh card and the
  judgement is gone. "Nothing to do" is therefore the *close* answer rather than
  a special case: archive in the same commit.
- A feedback nobody can judge moves its card to `todo/waiting/` with the
  question, where it still serves the feedback. The card stays and the feedback
  stays open. A question nobody can answer is a state this repository already
  has a place for.
- `bin/cli todo:check` reports open feedback that no todo answers for, and names
  `bin/cli todo:sync`. `TodoTest::everyOpenFeedbackIsOnTheBoard` held it
  already. It stands here as well because a check is what a session runs and the
  suite is what somebody else runs.
- Against an archive at the judgement, and the reason is not order. It would
  have to weaken `Todo::unreadable()`, and it would move the derived todos onto
  the decision entry instead of the feedback. That is readable, but it makes
  `closed` mean two different things by what the judgement decided.

## Assumed

- That an agent reads `closed` as "the thing I reported is dealt with". Nothing
  has measured it. The measure covers the opposite error, an absent file read as
  lost, which is the same reader with the same kind of inference.
- That the middle state does not become where feedback go to die. It has no
  clock on it. A feedback judged into a `low` todo is open for as long as that
  todo waits, and nothing says how long that is.

## Wrong if

- The archive no longer fills while `feedback/` grows, because every judgement
  lands in todos nobody works. Then the middle state is a second backlog under
  the word "judged", and the gap is an age on it.
- Somebody archives a feedback to get `todo:check` quiet. The error it reports
  in that case is one todo per derived step, which is loud enough to invite it.
- An agent re-reports something already judged, because `open` looked to it like
  nothing had happened. That is the mirror of the evidence behind `R-FBK-002`.
  It would mean the middle state needs to be visible through
  `typo3_feedback_list` rather than only in `todo/`.

## Since then

`bin/cli todo:sync` is gone, and the two bullets above that name it read
differently without it —
[`D-FBK-045`](fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md). A
feedback dropped back to no judgement gets no fresh card from anything. It
simply stands there, and the next session to reach it starts from the report
again. What `bin/cli todo:check` names is the feedback rather than a command to
run. The invariant itself stands, and the same three states hold it.
