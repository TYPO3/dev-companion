---
id: D-EVI-010
title: A made installation carries an extension of the project's own
date: 2026-09-02
status: open
coveredBy:
  - EnvironmentsTest::theProjectsOwnExtensionIsRequiredThenSetUpThenFilled
  - SiteExtensionTest::everyStateTheCountSeparatesHasRowsInIt
  - SiteExtensionTest::theRecordedCallAsksForTheTableThisExtensionRegisters
  - SiteExtensionTest::theTableIsAttributedToTheExtensionByItsCtrlTitle
---

# D-EVI-010 — A made installation carries an extension of the project's own

**`bin/cli environment:create E-SITE` writes one extension into `packages/`,
with a table and rows in it, and installs it after the distribution is up.**

An installation from TYPO3's base distribution owns no package, so every answer
about what this project registers was empty in the one environment that could
answer at all.

## Evidence

- `typo3_record_lookup` reads the rows of a table a project-owned extension
  registers, and that answer was recorded nowhere. The recording takes its
  installation half from two roots and neither could produce it: `E-SITE` had
  the database and no extension, and the fixture below `.fixtures/` has the
  extension and no TYPO3 to open a connection with — `D-DOC-012`. The recorded
  page therefore showed a refusal as the example of what the tool is for.
- The queries were run by hand against the running `E-SITE` 14.3 on 2026-09-01
  and answered, so what was missing was the environment rather than the code.
- Contract cases name `E-SITE` with a package of the project's own in it — "in
  the project's site package", "with the extension under `packages/`" — and
  `META-03`'s prompt names `packages/acme_events/Classes/` outright. The
  environment this repository makes had none, so every one of those was run
  against something the runner had to arrange.
- Written and installed against the running 14.3 on 2026-09-02: three `ddev`
  steps, seconds each, and the tool then answered 125 rows on one page with the
  verdict that names the page count.

## Decided

- **The extension is `acme_events` with `tx_acme_events_event`**, which is the
  key and the table the fixture registers and the recorded call asks for. One
  call is answered from both roots that way — for real in the environment, and
  as the boundary in the fixture, which holds no rows.
- **The rows go in through the installation's own connection**, by a seed script
  the package ships and `ddev exec php` runs. It boots the way
  `src/Installation/probe.php` does and for the same reason, and it writes
  nothing where the table already holds a row.
- **120 live rows on one page**, with 3 hidden and 2 deleted beside them. The
  count is what makes the recorded answer the case the tool exists for: one page
  of the record list would be the table that needs no tool, and the two flags
  are the states the count separates.
- **The steps run after a resume as well as after a build.** An environment made
  before this gains the extension by being asked for again, and each of the
  three says so and changes nothing where it is already done — which is cheaper
  than a state read off the directory that can be wrong about a run that stopped
  halfway.
- **After the build rather than inside it**, because `composer create-project`
  runs into that directory and refuses one that already holds `packages/`. What
  it costs is a second `extension:setup`.

## Assumed

- That an installation with one package of its own is closer to what a case
  meets than one with none. `D-EVI-001` is about not scaffolding the repository
  a review then finds its defects in, and this environment is never that subject
  — `D-EVI-004`.
- That the root page the setup creates is uid 1, which is what the rows are
  written to.
- That a table of one shape is enough. What it shows is a label, the two flags
  and a page; a table whose label column is a code, or whose rows are spread
  over a tree, is a shape nothing here records.

## Wrong if

- A case run in `E-SITE` reads `acme_events` as the project's real work and
  reports about it, which would make the environment the subject rather than the
  ground.
- The three steps turn out to cost more than seconds on a resume, which is what
  keeps asking for an environment again cheap.
- A recorded answer somewhere else changes because this table is now in the
  installation, and that change reads as a property of TYPO3 rather than of what
  this repository put there.
