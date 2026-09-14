---
id: D-KNW-119
title: 'The corpus tells apart the failures one usage synopsis presents alike'
date: 2026-08-25
status: confirmed
coveredBy: []
---

# D-KNW-119 — The corpus tells apart the failures one usage synopsis presents alike

**A `typo3` command prints its usage synopsis and exits 255 for any uncaught
exception, so the corpus says what that surface hides.**

[`D-KNW-094`](knw-094-how-a-variable-reaches-a-console-command-is-a-subject-this-server-owns.md)
owns how a variable reaches the command inside the container, and its reading
measured the forms its own feedback named. The session judged here met a form
nobody had asked about, and it reported three failures rather than one because
all three came back looking like an argument error.

## Evidence

- The miss reproduces on the server as it is now.
  `bin/cli hints:probe "ddev exec multi-line command backslash continuation"`
  returns `installation-setup` and `project-configuration-files`. The run of
  `ddev exec` statements in `installation-setup` names the join, `--raw`, a
  parenthesis, a pipe and a value with a space. None of them names a command
  written across several lines, and neither `synopsis` nor `secure enough`
  appears anywhere below `knowledge/` or `skills/`.
- The password half holds, read from the checkout.
  `SetupCommand::getAdminUserPassword()` in `.checkouts/14.3` throws a
  `\RuntimeException` with `Administrator password not secure enough!` and the
  code `1669747614` wherever
  `SetupDatabaseService::getBackendUserPasswordValidationErrors()` returns
  anything.
- The surface belongs to Symfony rather than to TYPO3.
  `Application::renderThrowable()` writes the active command's synopsis for
  every throwable, and `Application::run()` replaces an exception code above 255
  with 255. That is symfony/console v7.4, the line `.checkouts/14.3` pins at
  v7.4.13 and this repository has installed at v7.4.15. A TYPO3 exception code
  is nine digits, so every uncaught one exits 255 and prints the synopsis.
- The empty flow mapping holds, read from the checkout.
  `SiteWriter::createNewBasicSite()` writes `errorHandling`, `routes` and
  `dependencies` as empty arrays, and `SiteWriter::write()` dumps without the
  `Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE` its sibling `writeSettings()` passes.
  `Yaml::dump(['dependencies' => []], 99, 2)` on the symfony/yaml installed here
  answers `dependencies: {  }`, with the two spaces the feedback quoted.
  `SetupService` fills `dependencies` only where `typo3/fluid-styled-content` is
  active, which an extension repository's installation commonly is not.
- What this run cannot read is the multi-line invocation. It started no
  container, and this checkout carries no `.environments/`.

## Decided

- Step 1a, on the subject `D-KNW-094` owns, and one card for all three reports.
  They arrived as one output, which is why the session could place none of them.
  A caller who meets one of the statements meets the other two in the same
  breath.
- Queued rather than closed on the spot. The multi-line form is a measurement
  nothing here can stand in for. This run read the two other halves on one
  checkout, while `installation-setup` binds its statements by TYPO3 major.
- The measurement is an `E-SITE` made here rather than the reporting session's
  project, which is `D-EVI-004`.
- The feedback's account is the observation and not the statement. That the
  command failed with the synopsis is what the session saw; what `ddev exec`
  does with a newline is what the reading has to establish.
- `normal` rather than `low`. The output names the arguments and none of them
  causes it. So the caller's next move is a rewrite of a command that was
  already right.
- `coveredBy: []`. What would show this wrong is what a statement says and what
  a later session does with it. No test in this repository reads either.

## Assumed

- That the multi-line command lost its variable assignments rather than failed
  in the caller's own shell. The report establishes the output and not the
  cause.
- That DDEV v1.25.1 is what the caller runs. `D-KNW-094` carries the same
  assumption and what it costs.
- That the password and the flow-mapping halves hold on 12.4 and 13.4 too. This
  run read only `.checkouts/14.3`.

## Wrong if

- The measurement finds that a multi-line command survives the join. Then what
  failed was the quotes on the caller's side. The statement belongs wherever
  this server tells a caller how to write the line rather than beside
  `ddev exec`.
- A caller still reads the synopsis as an argument error once the statements
  land. Then the gap was the wording of what is already there, and the fix is a
  rewrite rather than three statements.
- The two read halves turn out to differ across the covered majors far enough
  that one entry cannot hold them. Then they are separate statements and this
  entry's one card was the wrong shape.

## Confirmed on 2026-08-25

Measured rather than read, on an installation made here. The password half
answers with the policy error and exit status 255. The multi-line half came out
the other way round. A backslash continuation survives the join and the command
runs, because the newline reaches the container's bash as a newline. What breaks
is a line that ends without one, and every assignment above the break is then
spent on a command of its own. So the first **Wrong if** fired and its
consequence did not follow, and the statement says which of the two a caller is
looking at.

The password half gained what only a run could say. The command writes the
schema before it judges the password. So a rejected run leaves forty-two tables
and a check `--force` does not clear refuses the next attempt.
