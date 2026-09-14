---
id: D-GUI-006
title: A task that changes nothing is a change type of its own
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist
---

# D-GUI-006 — A task that changes nothing is a change type of its own

**`typo3_task_guide` answers a task that changes nothing from a `changeType`
value and an intent of its own, and a stated type overrules the task's words.**

The enum offered six kinds of change and `unknown`, and all eleven intents were
kinds of change as well. So a review fell through to the patch checklist.

## Evidence

- The second run recorded in `R-GUI-006` on 2026-08-02:
  `task="review the TYPO3 project and site package"` with `changeType=unknown`
  matches no intent. What comes back is "Keep the patch focused on the stated
  task" and "Add or update the narrowest useful test coverage". And "Write the
  commit message with typo3_commit_message_guide".
- `feedback/2026-07-31-194826`, from a model that had loaded
  `typo3-extension-conformance` for a conformance review of a site package in
  `site-new`. The brief restated the skill's own checklist and added little for
  a pure audit. The session said it would not call the tool again after that
  skill.

## Decided

- A value on the enum **and** an intent, because the change type goes to the
  intent matcher. The caller who classifies the work and the caller who
  describes it reach the same brief. That is the shape `fb35b61` gave the
  deprecation type, and the reason the change-type block stays empty.
- The word is `audit` rather than `review`. `review` is a strong needle of the
  `submission` intent, so a caller who states it would have got the Gerrit push
  steps. That is the failure this entry is about, in a second shape.
- The checklist skeleton is what changes, and not only what joins it at the end.
  An intent can add items and the three the requirement names are in the
  skeleton, so no intent could ever have satisfied it.
- The commit-message step leaves the follow-up calls together with the
  checklist. One list that names a step the other dropped is one answer in
  disagreement with itself.
- Rejected: an answer that acknowledges the skill and routes to it, which is
  what the feedback suggested. The tool is reachable without a skill and cannot
  see what the client loaded. So that half is the question the two `D-SKL-001`
  cards in `waiting/` carry, and nothing here answers it.

## Assumed

- The words that select the intent name the work rather than its subject:
  `audit`, `conformance`, `code review`, `review the`, `review this`,
  `review of`, `reviewing`. None of them is a `matchWeak` needle, so each one
  flips the shape of the whole brief on its own.
- A caller who states a change type and describes a review authors the change
  and reviews it in one sentence. That is why the stated type wins.

## Wrong if

- A brief comes back in the review shape for work that does change something.
  "reviewing the failing test and fixing it" is the form it would take. The
  answer is then to move that needle to `matchWeak` with a condition, rather
  than to give up the shape.
- A session that has the conformance skill loaded reports the two as copies of
  each other again. What this hands over should be the difference from a patch
  brief rather than a second audit workflow. If it reads as one, the question is
  `D-SKL-001`'s.

## Since then

The statement holds and the enum was one value short of it: a task that changes
nothing is not only a review. A session that booted a Composer project from a
fresh clone had no value to state, and `unknown` gave it this skeleton. That is
the patch checklist, reached from the other side. `D-GUI-008` is where that
lands. It takes the mechanism this entry established and keeps the skeletons
apart. "report what the review did not reach" is a step a boot does not take.

The second **Assumed** met its instance and is half true. A caller who states a
type and describes a review can be a reviewer who names the type of the patch
under review. `D-GUI-009` follows from that.
