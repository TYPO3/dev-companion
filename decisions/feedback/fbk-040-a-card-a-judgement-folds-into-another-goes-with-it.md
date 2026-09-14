---
id: D-FBK-040
title: 'A card a judgement folds into another goes with it'
date: 2026-08-03
status: open
coveredBy:
  - TodoTest::noJudgementLeavesBehindTheCardItReplaced
  - TodoTest::theCardAJudgementReplacedIsFoundByTheStepItStillCarries
---

# D-FBK-040 — A card a judgement folds into another goes with it

**A feedback on another todo's `**Serves:**` line loses its own card without a
judgement in the same commit. That card still asks a session for the judgement
already made.**

`D-FBK-017` bounds the relation from below: a commit that judges a feedback
leaves at least one todo that serves it. Nothing bounds it from above, and the
second card is not a duplicate anybody can see.

## Evidence

- This session is the cost. `todo/progress/2026-08-02-145217` went out on
  2026-08-03 with the step every sync card carries, judge this feedback. The
  judgement already existed. `D-ANS-034`, `D-ANS-038`, `D-FBK-027` and
  `D-SKL-005` each cite `145217`, and `todo/progress/2026-08-03-125637` names it
  in `Serves:` beside `144511`. It ends "archive both feedback in the commit
  that ships it".
- The guard runs one way only. `TodoSync` skips a feedback that `OpenFeedback`
  marks judged, so no second card is ever written. Nothing removes the card that
  was already there when a later judgement folded the feedback elsewhere.
- No check sees the pair. `Todo::serves()` counts one card and two alike, so
  `bin/cli todo:check` reported `47 files, 0 problems` while both stood.
- The two do not look related in a listing. A sync card carries the feedback's
  title, "Task: assess Forge #105403. Recording how Forge has to be operated". A
  judged card carries its work, "Search the issue tracker by words, and not only
  by number". `bin/cli todo:list` printed them ten lines apart with no word in
  common.

## Decided

- The fold deletes the card, in its own commit. This one does that for `145217`,
  which stays judged because `125637` serves it.
- `bin/cli todo:check` reports the pair, and the todo beside this entry is that
  step. A card whose body is still `TodoSync::STEP` verbatim, for a feedback
  another todo also serves, is the exact shape. The step is a constant, so
  nothing is a guess.
- Against "one feedback, one card". A judgement may legitimately split a
  feedback across two work items. What is wrong is a card without a judgement
  beside one with a judgement, not two cards.
- Against a `todo:sync` that deletes it. Sync repairs drift and anybody runs it;
  a command that removes a claimed card is how a judgement gets lost instead of
  found.

## Assumed

- That a card which still carries the sync step verbatim has no judgement yet. A
  judgement could leave the text and add its argument elsewhere, but a new step
  is what a judgement of a feedback does.
- That folding stays occasional. It arrives when one session judges a cluster,
  which is what `judging.md` asks for, so the rate follows the clusters.

## Wrong if

- The check reports cards somebody did judge. Then the signal is not the body,
  and the need is a marker the judgement sets.
- A pair appears again after the check lands. Reporting it would then be too
  late, and the deletion belongs inside whatever writes the `Serves:` line.
- The deletion loses a judgement rather than a duplicate: a card goes whose
  feedback the todo that stays does not actually carry. That is the failure
  `D-FBK-017` names, reached from the other side.
