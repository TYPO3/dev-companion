---
id: D-DIS-007
title: The DDEV console is named by the mount, not by the variable
date: 2026-08-02
status: confirmed
coveredBy:
  - Typo3CliTest::theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove
---

# D-DIS-007 — The DDEV console is named by the mount, not by the variable

**`ddev exec` gets `/var/www/html/<binary>`, the place DDEV mounts the project
in its web container, rather than the relative path or
`$DDEV_APPROOT/<binary>`.**

`D-DIS-002` measured the relative path as exit 127 in a project whose
`working_dir.web` is the docroot. It left the repair to the queue with one
question open: from which DDEV version `DDEV_APPROOT` holds in the container.

## Evidence

- DDEV sets it from **v1.24.5** (2025-05-15). `DDEV_APPROOT=/var/www/html` is a
  literal line in the web service's `environment:` in
  `pkg/ddevapp/app_compose_template.yaml`. Commit `8662f76` added it on
  2025-04-12, "add DDEV_APPROOT variable to web container", for ddev#7198. The
  commit is not in v1.24.4 and is in v1.24.5, by GitHub's own compare.
- Before that the variable is the host-side project path and exists on the host
  only. In the container it expands to nothing, so
  `$DDEV_APPROOT/.build/bin/typo3` is `/.build/bin/typo3` there — a failure
  where the relative path worked.
- The same file mounts the project at `target: /var/www/html`, which is the
  value of the variable. So the two forms name one directory, and one of them
  needs a version.

## Decided

- The literal mount. A variable whose value is a constant DDEV hardcodes buys
  nothing over the constant, and costs a version boundary this repository would
  have to carry.
- No `${DDEV_APPROOT:-/var/www/html}` either. It is the same string with a
  fallback for a case in which the fallback is the answer.
- The test drives a `ddev` on the PATH that describes a project that runs. No
  test run may depend on containers, and what failed here is the command that
  would run, which a test can read without one.

## Assumed

- Every DDEV project this server meets mounts its files at `/var/www/html`.
  DDEV's own template hardcodes it, so a project where it differs has overridden
  DDEV's default in `.ddev/docker-compose.*.yaml` — the case
  `TYPO3_DEV_COMPANION_CONSOLE` exists for.

## Wrong if

- A DDEV project answers `No such file or directory` for a console that is
  there. That would mean the mount is not what its own template says.
- DDEV stops the mount at that path, or starts to derive it. Then the variable
  is the right form and the version floor is whatever this repository still
  supports.

## Since then

The path was one half of what `ddev exec` does to an invocation. The other is
that it joins its arguments back into a line and gives that to bash inside the
container. So an argument that carries a character bash acts on never reaches
the console either. `typo3_label_lookup` builds one, `--regex=/(save)/i` for
`language:domain:search`, where the parentheses are a subshell. It came back
exit 2 in every DDEV project, silently, as a fallback to the package files.
`Typo3Cli::run` now quotes for this transport and only for this transport. The
direct one has no shell between, and what `TYPO3_DEV_COMPANION_CONSOLE` names
may or may not. Measured against DDEV v1.25.1 on 2026-08-02, by the first
recorded run against an installation of this repository's own; `D-DOC-006` has
that run.

- `Typo3CliTest::everyArgumentReachesTheContainerAsTheShellLeavesIt`

## Confirmed on 2026-08-02

Both halves held in a project this repository did not make. A session on an
audit of an extension elsewhere had filed the console path as broken, from a
syntax error beside its labels. Re-run there on 2026-08-02,
`Typo3Cli::resolve()` answers the mount path autodiscovered and with no caveat.
The lookup comes back `answeredBy: "installation"` with the labels and the
parenthesised regex as built. That run answers the feedback.
