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
own trace does not carry, and reports it rather than failing on it.**

A recorded run holds the claim and what backs it in one file, and until now only
a reader compared the two.

## Evidence

- `REVIEW-03`'s second outcome carries the defect written out by hand: two hints
  are quoted as `typo3_hint_lookup`, `typo3_task_guide` returned them, and no
  such call was made. A judge found that; nothing here would have.
- The same reading over the three recorded runs finds nothing else, so what it
  prints today is one line.
- `REVIEW-01` and `REVIEW-02` quote their tools under the names their traces
  carry, which is what the reading is silent on.

## Decided

- The tools a run's evidence quotes and its trace does not carry are printed
  under the table, outside the exit code and outside `Scenarios::problems()`.
- It reports because the two cases read alike from here: a judgment quoting a
  call the session never made, and one naming a tool in order to say it was
  never called. `REVIEW-03` is the second and points at the first.
- Rejected: holding it as a problem. That fails the suite on a judgment doing
  its job, and the cheapest repair is to drop the tool's name from the sentence
  a reader needs.

## Assumed

- That a tool is quoted under its own name. A judgment writing "the hint lookup"
  in words is not read by this and is not meant to be.
- That the trace is complete. A run that recorded fewer calls than it made
  reports its evidence as unbacked, and both readings send somebody to the same
  file.

## Wrong if

- It never fires again, so what it holds is one run's defect that was already
  written down.
- A run is edited to satisfy it: the name dropped from the sentence rather than
  the call added to the trace.
