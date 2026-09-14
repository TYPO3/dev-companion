---
id: D-ANS-063
title: An option list is what the caller did not know to ask for
date: 2026-08-07
status: open
---

# D-ANS-063 — An option list is what the caller did not know to ask for

**An answer keeps the option a caller did not know to ask for and the fact no
checkout can supply. It does not shrink to the question the caller asked.**

Three strength reports out of one day of core work name those same two kinds as
load-bearing. One is an option a session would not have known to ask for, the
other a fact only the project holds.

Read as a boundary rather than as a confirmation
([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).

## Evidence

- `feedback/2026-08-07-065401`, from the triage and patch session.
  `typo3_forge_lookup` returned a note, "i work on that", posted a day earlier.
  That is the one fact that changed what the session recommended, and it is in
  the comments and in nothing else the session called. `typo3_test_run_guide`
  returned `-d sqlite|mariadb|postgres` and the warning that a suite which
  reports success over no files is not a green. The session ran every check on
  three databases because the option was there, and added a row count because of
  the warning. `typo3_changelog_lookup` matched nothing and returned
  `termCounts` and `termSubsets`, so the retry had a target.
  `typo3_project_describe` said the only declared scripts are Gerrit hooks, so
  the session never looked for a `composer test` that does not exist.
- `feedback/2026-08-07-065419`, same session, on `typo3_commit_message_guide`.
  Four calls, each after the change had moved. `summary-length-preferred` caught
  a 62-character subject for one round trip. `breaking-not-assessed` said the
  classification rested on an assumption rather than a check. That is why the
  session passed `isBreaking` and `isDeprecation` after it read the diff. The
  Releases validation is the one it ranks highest. It had drafted "main, 13.4"
  from its own read, and the guide held all three branches against what takes a
  patch today.
- `feedback/2026-08-07-132520`, a different session that reviewed the commit
  which resulted. `typo3_project_describe` gave `typo3Version 15.0.0-dev`, the
  only way it could know the branch it stood on was not v14. It gave
  `extensions: []` explicitly, which closed a prescribed step instead of left it
  to inference. `typo3_test_run_guide` again. `-b docker` is why the first run
  did not fail on a host that has no podman. `-d postgres` is why two findings
  rest on a measurement rather than an argument. The session could show
  `SQLSTATE[22008]` on postgres where mariadb returned zero rows. It says a
  guide that returned only the suite name would have left that as "this would
  presumably throw".
- Both sessions name the Releases validation and the DBMS option list. Neither
  could have produced either from the checkout.

## Decided

- The boundary these three describe runs between a fact the checkout holds and a
  fact only the project holds. Four things are on the second side. Which
  branches take a patch today, which container runtime is the fallback, which
  databases a suite can point at, and what a subject line may weigh. The reports
  name all four as what changed what the session did.
- The second kind is the option list. `-d postgres` and `-b docker` are not
  answers to what the caller asked; they are what the caller did not know to
  ask. The review session says plainly that its strongest finding exists because
  a switch of database was one flag away. An answer trimmed to the question
  would have cost it.
- So neither goes for brevity. The suites keep their invocation notes, and the
  guide keeps the runtime and database options. `typo3_project_describe` still
  answers `extensions: []` rather than nothing. `typo3_commit_message_guide`
  still validates the releases list rather than only formats the message.
- `D-ANS-059` read a comparable split and put the reported costs on the network
  side. This one does not repeat that. `typo3_forge_lookup` is a network reader
  and the reports name its comments here as load-bearing, which is the same turn
  `D-ANS-059` recorded at its foot.

## Assumed

- Three reports from one model in one checkout on one day. They are three tasks
  rather than three sessions of one task: a triage, a patch, a review. But the
  reader is the same, and its habits are the variable nothing here controls.
- A strength names what a session would lose if it went. None of these is a
  recorded run, so what they carry is where the boundary is, not that any
  decision holds.

## Wrong if

- A session reports the option lists as noise it had to read past. That would
  say the second kind is worth less than these three make it.
- The Releases validation turns out to have an answer in a checkout after all,
  which would move it across the boundary.
- A debrief names a computed answer as the one that misled it. That is the first
  **Wrong if** of `D-ANS-059` and would be the same event here.

## Since then

The counter-case inside this boundary arrived the same day: a network reader
whose answer misled a review, which another entry came out of. The split holds:
none of the three reports what the server computes as wrong. Two more reports
cover the network side, one of them from a task the three above do not cover.
