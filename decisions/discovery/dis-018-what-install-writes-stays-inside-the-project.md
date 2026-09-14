---
id: D-DIS-018
title: What `install` writes stays inside the project
date: 2026-08-12
status: open
---

# D-DIS-018 — What `install` writes stays inside the project

**Every file `install` touches is inside the project the caller pointed it at,
in the standalone case as much as in the dependency case.**

A checkout elsewhere has no path but the absolute host one, and the client files
that receive it are the shared, committed ones. To say so is the answer; to
write somewhere else is not.

## Evidence

- The floor is in place since 2026-08-09 and is right under every answer. The
  entry says its command is valid on this machine only, wherever it names this
  checkout. It says the file is a candidate for the project's `.gitignore`.
  `D-DIS-016` is the per-client account that settled the dependency half.
- One client of the eleven documents a private per-project scope at all. That is
  Claude Code's local scope in `~/.claude.json` under the project path, what
  `claude mcp add --scope local` writes. The other ten offer the shared file and
  nothing else.
- This is how this repository installs into `E-CORE` and both `E-EXT` checkouts,
  where the entry is correct on the machine that wrote it.

## Decided

- The project directory is the boundary of what this command touches, and it
  stays a property of the command rather than of an argument.
- Rejected: a write into the client's private per-project configuration. It
  moves what `install` touches outside the project the caller pointed this
  server at, for one client of eleven. Everything else the command does stays
  inside.
- Rejected: a `--scope` argument. It changes the behaviour of a command people
  have already run, to offer a choice one client documents.

## Assumed

- That a machine-specific entry in a shared file is caught by the sentence
  `install` prints beside it. Nothing enforces the `.gitignore` it names.

## Wrong if

- A machine-specific entry lands in a commit anyway and a teammate gets a client
  that cannot start the server. That is the harm the report opened with.
- Several clients document a private per-project scope, so "one of eleven" is no
  longer the argument against a write there.
- Somebody wants both, a shared entry for the team and a private one for their
  own checkout. That is the choice the rejected argument would have carried.

## Since then

The floor is still under every answer, read on 2026-08-23. The entry's command
is valid on this machine only, in a file that client documents as shared and
committed. Nothing reports the first **Wrong if** — no feedback describes a
committed entry that would not start on somebody else's machine.

The second is a survey nobody has run. `Installer::AGENTS` names thirteen
clients where this entry counted eleven. The read of 2026-08-29 covers Claude
Code alone, which was already the one of them it counted.

The third fired on 2026-08-29 and the entry survived it. Somebody wanted both
and got them while this command stayed as it is. `claude mcp add --scope user`
wrote the machine-wide entry, and that client's precedence puts a project entry
above a user one. Nothing here needed a `--scope` argument.
