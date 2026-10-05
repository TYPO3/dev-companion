---
id: D-DIS-027
title: An argument install does not take ends the run before a write
date: 2026-10-05
status: open
coveredBy:
  - InstallerTest::anArgumentTheCommandDoesNotTakeWritesNothing
---

# D-DIS-027 — An argument install does not take ends the run before a write

**`install` and `update` print the usage on `--help` or `-h` and write nothing.
On any other argument except `--agent=<client>`, they exit with 2 and write
nothing.**

## Evidence

- `feedback/archive/2026-09-30-065800-install-help-runs-the-install-instead-of-showing-help.md`.
  A session ran `install --help` in the checkout of this server to read the
  options. The command installed into that checkout, because `setUp()` read
  `--agent=` and passed over every other argument.
- Re-run on 2026-10-05 at `4efbdbd7`: the argument loop still read `--agent=`
  alone. The half about `AGENTS.md` was already gone with `D-DIS-026`.

## Decided

- Both commands write into the directory they run in. So the run ends before the
  first write, where an argument is one they do not know.
- Rejected: a refusal to install into the checkout of this server. That checkout
  is a legitimate project, and `D-DIS-024` sets it up through the container.
- Rejected for now: `--dry-run`. One session asked for it beside the help. The
  help names what `install` writes without `--agent`, and
  `documentation/usage/installing.rst` names it per client. A second session
  that needs the list before the write is the evidence for it.

## Assumed

- That the help text tells a reader enough to choose the client before a write.

## Wrong if

- A session reports that it ran `install` to see what it would write, and
  neither the help nor the install page told it. Then `--dry-run` has its second
  report.
