---
id: D-SCO-005
title: The installation is evidence about the task, and the weakest kind
date: 2026-07-29
status: confirmed
coveredBy:
  - ScopeTest::inASiteInstallationTheWorkIsOutsideTheCore
  - ScopeTest::namingAnInstallationToReadDoesNotMoveWhereTheWorkIs
  - ScopeTest::theNamedInstallationIsTheEvidenceWhereNothingElseIs
---

# D-SCO-005 — The installation is evidence about the task, and the weakest kind

**`Scope` orders the signals for work outside the core by how specific they are.
The kind of installation the session sits in is the last of them.**

Phrases decided `outsideCore`. "bootstrap_package" as the area matched none of
them. So a third-party extension got the core's changelog rules until the caller
wrote "not TYPO3 core" into the prose. `Scope` now reads three structural
signals, and the installation this server started in is one of them.

## Decided

- An order rather than a vote. Core work named outright wins, then an
  outside-core marker, then an area the installation knows as somebody's
  extension. A path in extension layout comes after those, and the kind of
  installation last. Each step is more specific than the one below it, so the
  general signal never overrules a statement about the task.

## Assumed

- In a Composer project, work is not core contribution unless something says it
  is. That is what the checkout is: a contributor writes core patches in a core
  monorepo, and a site installation that vendors `typo3/cms-*` is not one.
- A path that starts with `Classes/`, `Configuration/` or `Resources/` is inside
  a package. From the core root nothing has that name — `typo3/sysext/<key>/` or
  `Build/` comes first.

## Wrong if

- A core contributor runs their client from a site installation that has the
  core checked out somewhere else. Or they pass paths relative to the system
  extension directory they stand in. Both then read as extension work, and the
  way out is to say `typo3/sysext/` once.
- `TYPO3_DEV_COMPANION_ROOT` points at a site installation for the label and
  icon lookups while the questions are about the core. The variable now moves
  the boundary too, which it was not introduced to do.

## Since then

The second **Wrong if** happened. One value answered two questions, which
installation to read and which repository the work is in, so a core path came
back `project`. The two are separate now: `Instance::startedIn()` walks up from
where the server started, and the variable keeps only what the server reads. Its
back half stands too. Inside a core checkout a `Classes/` path is one a
contributor typed from the system extension directory. So the package layout
counts only where the session does not stand in the core, which mirrors the gate
`Build/Sources/` already had. No order changed.

## Confirmed on 2026-08-22

A session read the statement against `Scope::of()`, and the order is as written.
`Instance::startedIn()` is the last `match`, below every marker, below what the
installation knows the path as, and below both layout gates. Both **Wrong if**
have been gone back to in the two sections above, and the three tests this entry
names still stand in `ScopeTest`.
