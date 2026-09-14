---
id: D-GUI-009
title: 'A stated change type keeps the skeleton'
date: 2026-08-04
status: open
coveredBy:
  - HintsTest::aReviewThatStatesThePatchTypeNamesWhatItRemoves
  - HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist
  - HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief
  - ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore
---

# D-GUI-009 — A stated change type keeps the skeleton

**A stated `changeType` decides which skeleton a brief composes into, and what
the task's words recognized joins it rather than drops.**

`D-GUI-006` filtered the intents that write no file out of a call that stated a
type. It took such a caller to author the change and review it in one sentence.
The feedback that produced the review shape is the instance against it: it
stated the type of the patch under review.

## Evidence

- The reported call, run again on 2026-08-04 in this repository: task "review
  the core patch replacing GD-based error thumbnails with a static SVG
  placeholder", `changeType` cleanup. It comes back "Recognized as: Review or
  audit", owned by `typo3-core-patch-review`. It carries the enumeration, the
  matcher entry, the changelog file, the `[!!!]` prefix and the two `.rst`
  checks. Those stand beside "Keep the patch focused on the stated task", "Keep
  the cleanup mechanical" and the commit-message step. Before this change it was
  the patch checklist and the two Gerrit steps, with no removal named, which is
  what `D-GUI-004` recorded.
- The Gerrit steps did not come from the type filter. The `submission` intent
  matched on the bare needle `review`, which subsumes all four needles of
  `audit`: `review the`, `review this`, `review of`, `reviewing`. So every
  review call confirmed Patch submission with or without a stated type.
  `D-GUI-006` saw that needle and routed around it with the type named `audit`
  rather than `review`.
- The two skills already draw the line the needle crossed.
  `typo3-core-patch-development` carries the way from an issue to a pushed
  patch, and its own description hands "reviewing a patch without writing it" to
  `typo3-core-patch-review`.
- The push survives the needle. `knowledge/documents/typo3-gerrit-workflow.md`
  holds it, and `typo3_rule_lookup` reaches it through the intent's `rulesQuery`
  "gerrit push patch set". The intent itself still matches on `gerrit`, `push`,
  `patch set`, `submit` and `backport`.

## Decided

- Both halves, rather than either. The skeleton says what kind of answer this is
  and only one of them can be right. The intents say what the answer is about
  and nothing forces those to be one. So the type keeps the first and the words
  keep the second.
- `review` leaves `submission`'s needles. The intent is the workflow for a push
  of a patch and an amend of a patch set. A read of one is not that.
- `D-GUI-006` is not re-opened. A stated type still decides the skeleton, which
  is the whole of what it decided. What changes is that the filter on the intent
  was never necessary to hold it.
- Rejected: review words that win the skeleton. It costs the caller who really
  authors the focused diff, the test coverage and the commit message. It flips
  the boot half of "fix the post-start hook so the import runs" the same way.
- Rejected: no change, on the view that a core review reaches the skill and the
  routing entry rather than this tool. The call the feedback reported is one a
  caller can still make, and what came back had no removal surface in it.
- `ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore` keeps its
  property on `push`. `D-SCO-002` is about an ordinary word read as the core's
  process, and `push` is that word and still not the wrong one.

## Assumed

- That a brief with both halves reads as one answer. A reviewer meets "Keep the
  patch focused on the stated task" and reads it as a criterion. That is what
  the item says from the other side rather than a second instruction.
- That `push` and `submit` carry the ambiguity `D-SCO-002` is about on their
  own. Both are as ordinary in extension maintenance as in the core's process.
  That is why the intent comes under its condition rather than as a statement.

## Wrong if

- A session reports the brief for a stated type as two answers at once. A
  reviewer told to add test coverage, or an author told to enumerate what the
  diff removes. The lever is then the skeleton after all, and which caller it
  belongs to has to come off something other than the type.
- A caller who wants to push a patch reaches no submission steps because the
  task said "put it up for review" and nothing else. `push` and `submit` are
  what carry it, and the needle would have to come back as a `matchWeak` one.
- A review of something that is not a change gets the removal surface anyway and
  reads it as noise. The item reads "Where the review is of a change", so that
  would be the wording rather than the shape.
