---
id: D-GUI-036
title: A brief carries a hint for each path before a second for any
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::aBriefCarriesAHintForEachPathBeforeASecondForAny
---

# D-GUI-036 — A brief carries a hint for each path before a second for any

**A brief takes a matched hint first where it reaches a path that no hint ahead
of it reaches. The rest fill the slots that remain, in the order of the
matcher.**

## Evidence

- The `REVIEW-03` call of 2026-10-28 named nine paths, and seven of them are
  backend paths. Re-run at `55d9b79f`, the brief carried `backend-ui`,
  `backend-modules`, `backend-routing-internals` and `backend-lit-task`.
  `datahandler-testing` was in `omittedHints`, so no carried hint reached
  `DataHandler.php`.
- The keyword tier of `Hints::find()` sums the patterns over all paths
  (`D-ANS-124`). `backend-ui` reached seven paths. `backend-modules` and
  `backend-routing-internals` reached only `AjaxRoutes.php`, and
  `backend-lit-task` only the `.ts` file, which `backend-ui` already reached.
- With the change, the same call carries `backend-ui`,
  `documentation-changelog`, `datahandler-testing` and `backend-modules`. The
  four reach all nine paths. `backend-routing-internals` moves to
  `omittedHints`.
- `bin/cli hints:coverage` prints the same output before and after the change.
  `HintsTest` passes unchanged with its first-hit cases. The order of
  `Hints::find()` stays the same, because the change is in the brief alone.

## Decided

- The spread is in `TaskGuide` rather than in `Hints::find()`. The lookup has a
  limit the caller sets, and `D-ANS-124` rests on the order of the lookup.
- `matchedOn.paths` carries the given paths that a pattern of the hint reaches
  alone. A hint that the task text reaches and no path reaches waits for the
  fill.
- A lead hint (`D-GUI-035`) stays in front, and it counts for the paths that it
  reaches. The tier of `D-ANS-097` applies after the spread.
- Rejected: a strict round robin over the paths in the order the caller gives
  them. Four slots never reach nine paths that way, and the order of the paths
  then decides which ones get none.

## Assumed

- That a hint for a path nothing else covers is worth more than a second hint
  for a covered path. A hint for a subsystem the patch touches in one file can
  be weaker than a second backend hint.

## Wrong if

- A run of a case skips a hint that the spread brought in as off the subject,
  and that run uses the backend hint that the spread moved out.
- A path prefix that every core path matches, such as `typo3/sysext/`, takes a
  slot in place of a hint for one subsystem.
