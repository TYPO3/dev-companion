---
id: D-EVI-013
title: A forward review is measured three times and graded blind
date: 2026-10-29
status: open
coveredBy: []
---

# D-EVI-013 — A forward review is measured three times and graded blind

**Three runs per forward review, each graded blind, judge a change to a review
skill. A criterion that fails in two of three is a defect.**

On 2026-10-28 five rounds ran one review each and a judge who knew what had
changed. The status moved between rounds without a change that explained it, and
the next change answered one run.

## Evidence

- Five rounds of 2026-10-28, against `c0a078b7` to `a0d270a5`, one run each.
  `REVIEW-02` went covered, partial, partial, covered, partial. `REVIEW-01`
  wrote its surface list first in the fourth run and not in the fifth, on the
  same wording.
- The judges of those rounds were told the changes to look for. The blind judges
  of 2026-10-29 passed criteria the informed ones had failed on similar runs.
- Measured on 2026-10-29 against `efa5a1e4`, three runs per review, each graded
  blind by its own judge:

  | Review      | Criteria met in all three | Criteria at 2 of 3 |
  | ----------- | ------------------------- | ------------------ |
  | `REVIEW-01` | 9 of 11                   | met 1, met 4       |
  | `REVIEW-02` | 9 of 9                    | none               |
  | `REVIEW-03` | 9 of 9                    | none               |

- Each miss of `REVIEW-01` fell in a different run: met 1 in the third, met 4 in
  the second. Most passes carry a doubt, so a stricter judge moves them.
- Outside the criteria, one of nine runs cited line numbers past the end of a
  file, and one cited a hint as `typo3_hint_lookup` that `typo3_task_guide` had
  carried.

## Decided

- **Three runs and a blind judge for a claim about a skill change.** One run
  still finds a defect, which is worth a todo where somebody can check it
  without the run.
- **A todo needs a criterion at two of three**, or a defect a reader confirms
  outside the run. Nothing on 2026-10-29 met the first, so the tuning stops at
  `efa5a1e4`.
- The run files below `scenarios/runs/` stay one run per review. This entry
  holds the rates.

## Assumed

- That three runs separate a defect from noise for a criterion that a skill
  change moves. Three runs cannot tell one failure in three from one in ten.

## Wrong if

- A criterion passes three of three and fails in the next two runs on the same
  server. Then three runs are too few, and the measure needs five.
- Two blind judges disagree on the same run for more than one criterion. Then
  the criteria need wording before the rates mean anything.
