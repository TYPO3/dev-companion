---
id: R-FBK-014
title: 'A judgement takes the card it replaced with it'
status: held
restsOn: [D-FBK-040]
heldBy:
  - TodoTest::noJudgementLeavesBehindTheCardItReplaced
  - TodoTest::theCardAJudgementReplacedIsFoundByTheStepItStillCarries
---

# R-FBK-014 — A judgement takes the card it replaced with it

**A feedback that another todo serves has no card of its own left that asks for
the judgement. `bin/cli todo:check` reports the one that is.**

The call that records a feedback gives it one card, and never a second. That
bounds the relation one way only. A judgement that folds a feedback onto another
todo's `serves:` arrives after the card, and nothing looks back at it. The card
stays in the queue with the step every card carries, judge this feedback, for a
judgement somebody has already made.

Nothing about the two says they are a pair. A card takes its title from the
feedback and a judged todo from its work. So a listing prints them ten lines
apart with no word in common. The next session claims the card, reads the
feedback and spends itself to arrive where the repository already was. That is
what this costs when nobody catches it: a whole session, on a question somebody
answered.

The card is what goes, and it goes in the commit that folds the feedback. What
remains is the todo somebody wrote, which is the one that says what there is to
do. Only the report is automatic. The deletion belongs to the session that made
the judgement. A command that removes a claimed card is how a judgement gets
lost rather than found.

Two cards over one feedback are not the failure. A judgement may split a
feedback across two work items, and what is wrong is an unjudged card beside a
judged one. That is why the signal is the step the card still carries, verbatim,
rather than the count.

This is `R-FBK-010` from the other side. A claim says a piece of work is
somebody else's, and a card says a judgement is nobody's yet. Both are false the
moment the work behind them ends, and a session that has read nothing else reads
both.

## From

2026-08-03. One claimed session went on `todo/progress/2026-08-02-145217`, whose
feedback four decisions had already cited and another todo already served.
`bin/cli todo:check` reported `47 files, 0 problems` while both stood.

## Held by

- `TodoTest::noJudgementLeavesBehindTheCardItReplaced` for the board as it
  stands, and
  `TodoTest::theCardAJudgementReplacedIsFoundByTheStepItStillCarries` for what
  is a pair and what is a split. That a session sees it is `bin/cli todo:check`,
  which reports what `Todo::folded()` returns.
