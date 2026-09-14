---
id: R-DIS-024
title: 'The published directories ignore themselves'
status: held
restsOn: [D-DIS-010]
heldBy:
  - InstallerRecordTest::neitherCommandWritesIntoTheProjectsGitignore
  - InstallerTest::gitReportsTheProjectsOwnFiles
---

# R-DIS-024 — The published directories ignore themselves

**Every directory `install` and `update` write into a project carries its own
`.gitignore` that says `*`. Neither command adds a line to the project's.**

A published skill is a generated file: the next run replaces it whole, and it
belongs in nobody's history. To say so from the project's own `.gitignore` meant
that a tracked, shared file gained a block that grew with every client. That was
32 lines in a project set up for three of them. Every install and update since
produced a diff in a file this package does not own.

A directory can say the same thing about itself. `*` covers everything below it
and that file with it, so git reports nothing there. A skill the project wrote
itself, in the same skills directory, stays visible. The record moves to
`.typo3-dev-companion/state.json` for the same reason. A file at the root cannot
ignore itself, and it was the one artefact that left no other option.

Merged agent and MCP configuration, `.mcp.json`, `.codex/config.toml` and the
rest, is in no ignore list, because the project may share it. That was already
true of those files and is now true of the `.gitignore` as well, which a project
shares more than any of them.

Nothing migrates what a development build left in a project. The package has no
release yet, so whoever ran it undoes what a run of it wrote.

## From

The remark that an install of the skills always means a change to the
`.gitignore`, 2026-08-03.

## Held by

The first asks git rather than the files. It installs into a repository of its
own, next to a skill the project wrote. It holds that `git status` reports the
project's files and none of this package's. The second holds the `.gitignore` a
project brought with it against an install and an update over the top of it.
