---
id: R-DIS-020
title: 'The project records which clients are installed in it'
status: held
restsOn: [D-DIS-014]
heldBy:
  - InstallerRecordTest
---

# R-DIS-020 — The project records which clients are installed in it

**Install records every client it set up in `.typo3-dev-companion/state.json`,
and an update without `--agent` refreshes all of them.**

More than one client usually works on a project, and which ones is knowledge
only the project has. To name them one at a time meant to remember a list nobody
keeps. So a second client kept the skills of the version that installed it, in
silence. Clients that share a skills directory get one publication.

To name no client is a setup of its own, recorded as `generic`. That is the
`.mcp.json` entry and the skills at `.agents/skills`, the two locations a client
finds without configuration for it. The record holds and refreshes it like any
named client, and it needs no case of its own. `--agent=` does not take it,
because it is nobody's name. An update in a project where nothing sits installed
says so rather than reports work it did not do, and it succeeds. It is the
command a project wires into Composer's `post-update-cmd`, where a non-zero exit
fails the whole run. The record is not in anybody's checkout
([`D-DIS-014`](../../decisions/discovery/dis-014-the-refresh-is-wired-by-the-project.md)).

The refresh is what reads the record. A skill this package no longer ships goes
out of every client it reached, whichever of them the run named. Where the
record sat before, and why it sits below a directory that ignores itself now, is
[`R-DIS-024`](dis-024-the-published-directories-ignore-themselves.md).

## From

An update that needed a repeat per client, in a project set up for two of them
(2026-07-31).
