---
id: D-DIS-015
title: The installed entrypoint is named relatively wherever it exists
date: 2026-08-08
status: revoked
revokedBy: D-DIS-016
---

# D-DIS-015 — The installed entrypoint is named relatively wherever it exists

**Where the project has this server as a dependency, the client entry names it
relative to the project root, with or without a DDEV configuration. Only a
standalone checkout has no such path, and what to write for it is a question
nobody here can answer.**

This is the judgement of
`feedback/2026-08-08-184226-install-writes-a-machine-specific-absolute.md`,
which reported that `install` writes a host path into files eleven clients
document as shared and committed.

## Evidence

- The report is right about the outcome and wrong about the condition. It read
  `installedEntrypoint()` as a relative path whenever the server is a
  dependency. `jsonServer()` uses that return **only** where `.ddev/config.yaml`
  exists, and writes `$this->entrypoint`, the absolute host path, on every other
  route.
- Measured 2026-08-08 in a fixture project that declares `typo3/dev-companion`,
  with `vendor/bin/typo3-dev-companion` present and no `.ddev`:
  `install --agent=claude` wrote
  `/home/benji/projects/typo3-cms-mcp/bin/typo3-dev-companion` into `.mcp.json`.
  So the shareable case the feedback assumed already worked does not.
- The three checkouts this repository installs into got their repair the same
  day and all three carry the absolute path. That agrees with the account: in
  those the server genuinely is not a dependency.
- This entry did not verify the report's account of the client again, and this
  half does not need it. Whether those files belong in a commit decides what the
  standalone case should write. The dependency case is wrong under either
  answer. The relative path is both shareable and correct, and it is the one the
  DDEV branch already writes.
- Nothing in `decisions/` settles this. `D-DIS-009` is about what an install
  says *remains* to do, not about what it writes. `D-DIS-002` is about the
  declared `bin-dir`, which is the very path this defect discards.

## Decided

- Ladder step: **repair to something that exists**, not a gap. The command, the
  targets and the relative path are all here, and one branch does not use them.
- The judgement takes the two halves apart, because only one of them is ours.
  The dependency case is a defect and sits in the queue. The standalone case is
  a boundary. The alternatives include a write into the client's private
  per-project configuration outside the project. What this command may touch is
  the maintainer's to decide, so it waits.
- Fixing the dependency case first is not a partial answer. It is what removes
  the reported harm from every project that installed this server the ordinary
  way. It narrows the open question to the checkout-elsewhere setup.
- The feedback is not archived by this commit. Two todos serve it and it stays
  open until the change lands.

## Assumed

- That a client spawns the server with the project root as its work directory,
  which is what makes a relative `args` entry resolve. The DDEV branch already
  rests on it, so this decision does not add the assumption. But it extends it
  to every client rather than to DDEV projects alone, and it is the first thing
  the todo has to establish.

## Wrong if

- A client resolves the command against something other than the project root,
  and a relative entry starts a server for nobody. That is worse than a
  host-specific path, because it fails on the machine that ran the install too.
- The absolute path turns out to be what somebody wanted in the dependency case:
  a project that deliberately points at a checkout it develops against. Then the
  entry needs a choice rather than a rule, which is the `--scope` argument the
  feedback proposes.

## Revoked on 2026-08-08

The second half of the first **Wrong if** fired before any build, out of the
clients' own documentation. A relative `args` entry resolves against a work
directory the MCP specification says nothing about. Four targets read on
2026-08-08 gave three answers. The client this server runs from most warns
against the assumption outright, and sets a project-directory variable so a
server need not depend on it. That also explains why the DDEV branch is correct:
`ddev exec` supplies the work directory the client does not. A general rule
would have removed the thing that made it right. `D-DIS-016` states the question
as the per-client one it is.
