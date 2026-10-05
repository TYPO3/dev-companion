---
id: D-DIS-028
title: A DDEV container is entered only by its own project
date: 2026-10-05
status: open
coveredBy:
  - ProjectTest::theContainerOfAnotherDdevProjectIsNotThisProjectsShell
  - Typo3CliTest::aConsoleInsideAnotherDdevProjectSaysWhichProjectItIsIn
---

# D-DIS-028 — A DDEV container is entered only by its own project

**This server counts as inside a project's DDEV environment only where
`DDEV_PROJECT` names that project. `IS_DDEV_PROJECT` alone says that some DDEV
web container runs the server, and not whose.**

## Evidence

- `feedback/archive/2026-09-26-085929-is-ddev-project-reads-any-ddev-container-as-the.md`.
  The server ran in the web container of `dev-companion` on PHP 8.4.
  `typo3_project_describe` for `reference-tca` answered "8.5 in DDEV, which this
  server is already inside". It dropped `ddev` in front of each command.
  `Typo3Cli` took the same check and ran the console on the wrong PHP, with no
  caveat.
- DDEV's `pkg/ddevapp/app_compose_template.yaml` passes `DDEV_PROJECT` and sets
  `IS_DDEV_PROJECT=true` in every web container. Read on 2026-10-05 in the
  container of `ext-canvas` on DDEV v1.25.4: `DDEV_PROJECT` is `ext-canvas`, the
  `name` of its `.ddev/config.yaml`.

## Decided

- `Typo3Cli::enclosingDdevProject()` reads both variables, and
  `Project::ddevProject()` derives the name by the rules `Project` already reads
  `.ddev/config.yaml` with. `entered` is true where the two agree.
- In another project's container the console still runs through the PHP of that
  container, with a caveat. The caveat names both projects and says to start the
  server on the host. `ddev exec` is not there, so a container cannot reach
  another project's runtime.
- Rejected: `ddev exec -p <name>` from the container, as the feedback proposed.
  DDEV ships no `ddev` binary into a web container.

## Assumed

- That `DDEV_PROJECT` is set wherever `IS_DDEV_PROJECT` is. Where it is absent,
  the server now counts as outside the project. That costs a caveat, and the
  answer is still right.

## Wrong if

- A session inside its own project's container gets the caveat. That would mean
  the name DDEV passes differs from the one `Project::ddevProject()` derives.
