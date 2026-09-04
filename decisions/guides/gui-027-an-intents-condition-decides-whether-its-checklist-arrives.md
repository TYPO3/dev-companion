---
id: D-GUI-027
title: An intent's condition decides whether its checklist arrives
date: 2026-09-04
status: open
coveredBy: []
---

# D-GUI-027 — An intent's condition decides whether its checklist arrives

**A checklist item arrives only from an intent whose own `condition` the call
meets, and a stated change type is not evidence that it does.**

The condition is written, correct and read by nobody: it reaches the answer as
prose beside a weak match and decides nothing.

## Evidence

- The reported call, re-run on 2026-09-04:
  `typo3_task_guide(task="Group the fields of this TCA file better", changeType="cleanup", paths=["…/Configuration/TCA/tx_animals_domain_model_animal.php"], targetVersion="14")`
  returns `cleanup` as recognized work and its six repository-audit items in the
  checklist, beside the four `tca-field` items that answer the task.
- `TaskGuide::answer()` detects on `$task . ' ' . $changeType`, and the enum
  value `cleanup` is also a needle in the cleanup intent's `match`. Stating the
  change type confirms the intent whatever the sentence says.
- The cleanup condition already excludes the call: *only if the task asks for
  the repository as a whole to be changed rather than reviewed, or for the
  findings of a review to be worked off*. `TaskIntents` reads it for nothing;
  only a conditional match carries it into the answer.
- `feedback/2026-09-03-235330` is the same gap from the other side, and by words
  in the task text rather than by the change type: `site-setting` confirmed
  strong on a `Sets/` path and four items about defining a setting, for a task
  that added none and whose own condition says the site configuration is not
  covered.
- The change type had already contributed its own item — *keep the cleanup
  mechanical; avoid mixing behavioural changes into the same patch* — and that
  one was right for this call. The six are a second contribution on top of it.

## Decided

- Step 3 of the ladder, routing: the intent exists, is worded correctly and
  fired on a call it excludes itself from.
- Queued rather than closed on the spot, because the change is in `src/`.
- `normal` rather than the `low` a card arrives at: two sessions reported it
  from two task shapes.
- What the fix may not do is drop the change type out of detection and leave it
  there. `audit`, `triage`, `operations` and `diagnosis` reach their intents by
  the same appended word, and that route is deliberate.

## Assumed

- That the appended change type is the only route from `changeType` to an
  intent, so the four reading types lose their brief if it goes unreplaced.

## Wrong if

- A call that does ask for a repository to be put right — no paths or a
  directory, and a task text asking for the findings to be worked off — stops
  getting the six audit items.
- Stating one of the four reading change types stops producing the brief that
  change type names.
- The site-setting half turns out to need something else, so gating the change
  type answers one feedback and leaves the other where it was.
