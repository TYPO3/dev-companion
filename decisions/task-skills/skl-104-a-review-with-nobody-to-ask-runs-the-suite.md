---
id: D-SKL-104
title: A review with nobody to ask runs the suite
date: 2026-10-28
status: revoked
revokedBy: D-SKL-109
---

# D-SKL-104 — A review with nobody to ask runs the suite

**Where nobody can answer the question before a skip, `typo3-core-patch-review`
runs the suite in the worktree that stands. It creates no worktree without the
question.**

## Evidence

- The `REVIEW-03` run of 2026-10-28 (`scenarios/runs/REVIEW-03.json`) skipped
  `phpstan` and `e2e` for a time budget it set itself. It ran headless, so no
  question could get an answer. The skill said to ask before a skip and nothing
  about a session with nobody to ask.
- `D-SKL-102` makes the question the gate before a skip, and the run the
  default. Its **Wrong if** names a worktree created without the question as the
  risk in the other direction.

## Decided

- No answer means no skip, because the question gates the skip. No answer also
  means no new worktree, because the question gates that write.
- The question covers every suite a review leaves out, whatever the reason. The
  skill had tied it to the missing worktree alone.
- The `Not run:` line says that nobody was there to ask, in place of the
  question and its answer.

## Assumed

- That a headless run has the time a suite costs. `e2e` takes minutes, and a
  client with a wall-clock limit would end the run before the report.

## Wrong if

- A headless run hits a client limit while a suite runs, and loses the report.
  Then the default needs a bound, and the report has to come first.
- A headless review still skips a suite on a budget of its own. Then the
  sentence did not arrive, and its place in the section is the next read.

## Since then

The second **Wrong if** fired in the second `REVIEW-03` run of 2026-10-28. The
session gave "scoped to targeted suites" as its reason. A sentence above this
one in the section still said a review names the broader suites. `D-SKL-106`
rewrote it and closed the list of reasons.

## Revoked on 2026-10-28

The maintainer allowed a review to skip a suite to finish faster. So a headless
review may skip one, and the time budget this entry ruled out is a reason it
names. `D-SKL-109` carries what stands: no worktree without the question.
