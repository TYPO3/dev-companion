---
id: D-EVI-009
title: A run is read against its own trace
date: 2026-09-01
status: open
coveredBy:
  - ScenariosTest::aRunIsReadAgainstItsOwnTrace
---

# D-EVI-009 — A run is read against its own trace

**`bin/cli scenarios:check` names every tool a run's evidence quotes that its
own trace does not carry, and reports it rather than fails on it.**

A recorded run holds the claim and what backs it in one file, and until now only
a reader compared the two.

## Evidence

- `REVIEW-03`'s second outcome carries the defect written out by hand. Two hints
  stand quoted as `typo3_hint_lookup`, `typo3_task_guide` returned them, and no
  such call happened. A judge found that; nothing here would have.
- The same read over the three recorded runs finds nothing else, so what it
  prints today is one line.
- `REVIEW-01` and `REVIEW-02` quote their tools under the names their traces
  carry, which is what the read is silent on.

## Decided

- The tools a run's evidence quotes and its trace does not carry print under the
  table, outside the exit code and outside `Scenarios::problems()`.
- It reports because the two cases read alike from here. A judgment that quotes
  a call the session never made, and one that names a tool in order to say it
  never ran. `REVIEW-03` is the second and points at the first.
- Rejected: a hold of it as a problem. That fails the suite on a judgment that
  does its job. The cheapest repair is to drop the tool's name from the sentence
  a reader needs.

## Assumed

- That a run quotes a tool under its own name. This does not read a judgment
  that writes "the hint lookup" in words, and it is not meant to.
- That the trace is complete. A run that recorded fewer calls than it made
  reports its evidence as unbacked, and both readings send somebody to the same
  file.

## Wrong if

- It never fires again, so what it holds is one run's defect that somebody
  already wrote down.
- Somebody edits a run to satisfy it: the name dropped from the sentence rather
  than the call added to the trace.
