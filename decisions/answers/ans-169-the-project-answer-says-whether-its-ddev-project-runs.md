---
id: D-ANS-169
title: The project answer says whether its DDEV project runs
date: 2026-10-05
status: open
coveredBy:
  - ProjectTest::theAnswerSaysWhetherTheDdevProjectRuns
---

# D-ANS-169 — The project answer says whether its DDEV project runs

**`typo3_project_describe` reads `ddev describe -j` for a DDEV project and
reports `environment.running`. Everything else in that answer still comes from
the project's files.**

`D-ANS-071` decided that a stopped project reads exactly like a live one here.
This entry moves that boundary for the one fact the next call depends on.

## Evidence

- `feedback/archive/2026-09-28-112243-project-describe-does-not-say-whether-the.md`.
  The answer named the DDEV project `reference-coreapi` and said nothing about
  its state. `typo3_backend_module_lookup` then refused three times with "the
  DDEV project is stopped". The session ran `ddev describe -j` and `ddev list`
  itself before it understood that a different project was up.
- Re-run on 2026-10-05 against `/home/benji/projects/site-events`, a paused DDEV
  project. The answer named `events-site`, its containers and its hostnames, and
  no field or sentence said that it was paused.
- `Typo3Cli::viaDdev()` already reads `ddev describe -j` before every console
  call. The read starts nothing, so `R-DIS-006` holds.
- The maintainer decided on 2026-10-05 to read the state, with `D-ANS-071`
  revised for it.

## Decided

- `Typo3Cli::ddevState()` is the one read of `ddev describe -j`. `viaDdev()` and
  `Project` both take it, and nothing keeps it (`R-DIS-009`).
- `running` is true, false or null. Null where the environment is not DDEV,
  where this machine has no `ddev`, and where `ddev describe -j` gives no
  status. Inside the project's own container it is true without a read.
- One sentence beside the project name says the state. Where the project is
  down, it names `ddev start` and what a tool that answers from the installation
  loses.
- `R-PRJ-001` stands: on a fresh clone without `ddev` the answer is complete and
  `running` is null.

## Assumed

- That `status` in `ddev describe -j` is `running` for a project that answers.
  `Typo3Cli::viaDdev()` rested on the same field before this entry.

## Wrong if

- A session acts on `running: true` and the next lookup refuses with a stopped
  project. Then the state changed between two calls, or the field reads the
  wrong project.
- The `ddev describe -j` read makes the first call of a session slow enough that
  a session reports it.
