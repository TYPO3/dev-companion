---
id: R-GUI-006
title: 'A review is not answered with a checklist for changing something'
status: held
restsOn: [D-GUI-006, D-GUI-008]
heldBy:
  - HintsTest::aTaskThatChangesNothingIsNotAnsweredWithAPatchChecklist
  - HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief
---

# R-GUI-006 — A review is not answered with a checklist for changing something

**A brief for a task that changes nothing does not tell the caller to focus the
patch, add test coverage and write the commit message.**

`typo3_task_guide` composes its checklist from `changeType` and the intents in
`knowledge/task-intents.json`, and both enumerated kinds of change alone until
`D-GUI-006`. The enum offered bugfix, feature, cleanup, test, documentation and
`unknown`. None of the eleven intents was an audit, and a review fell through to
the generic patch checklist. Those items are not redundant beside a review's own
workflow; they are steps a review may not take. The `audit` change type and the
intent of the same name are the shape it gets instead. A task that describes a
review reaches it without the type stated.

A review is not the only work that writes no file. `D-GUI-008` added
`operations` beside it for the boot of an installation. That one owes neither
the patch steps nor the review's "report what the review did not reach". So the
skeleton forks three ways rather than two.

## From

A conformance review of a site package in `site-new` that ran the call and
reported it added little for a pure audit (`feedback/2026-07-31-194826`,
2026-07-31). Re-run on 2026-08-02 with
`task="review the TYPO3 project and site package"` and `changeType=unknown`. No
intent matches. The checklist that comes back is "Keep the patch focused on the
stated task" and "Add or update the narrowest useful test coverage". Its third
item is "Write the commit message with typo3_commit_message_guide".
