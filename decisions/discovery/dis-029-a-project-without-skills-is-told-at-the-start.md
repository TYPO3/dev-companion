---
id: D-DIS-029
title: A project without skills is told at the start
date: 2026-10-05
status: revoked
revokedBy: D-DIS-034
coveredBy: []
---

# D-DIS-029 — A project without skills is told at the start

**Where `install` never recorded a client or a skill in the project, the
instructions open with `Installer::ABSENT`. That sentence says that no task
skills are installed there and names `typo3-dev-companion install`.**

## Evidence

- `feedback/archive/2026-09-28-112330-none-of-the-server-s-skills-were-in-this-client.md`.
  A session in the TYPO3 Explained manual read "activate the typo3-* skill in
  your listing" and had none of them in its listing. It could not follow the
  instruction, and from the server's side that looks like an instruction it
  ignored.
- The server already reads the record at the start, for `D-DIS-021`. A project
  without one was the case it said nothing about.
- The maintainer chose the notice on 2026-10-05, over a sentence in the docs
  alone.

## Decided

- The notice stands where the stale one stands and is shorter than it. So the
  largest assembly `R-ANS-013` measures stays the same, and `ScopeTest` holds
  the order of the two lengths.
- stderr gets the long form with the project path, as it does for a stale
  publication.
- Nothing is written. The server tells; `install` stays the caller's decision.
- Not built: a marker on each entry of the `guides` list for code or for prose.
  The same feedback asked for it. Each entry already says in its sentence when
  to read it, and one session reports the cost of the scan.

## Assumed

- That a project which carries the skills does so through `install`. A client
  that has them from somewhere else gets a notice that is wrong for it.

## Wrong if

- A session reports the notice where its listing had the typo3-* skills. Then a
  second way in exists, and the check has to read it.
- A second session that writes prose rather than code reports the scan of the
  `guides` list as a cost.

## Since then

On 2026-10-27 a start in `/` got this notice and was told to run `install`
there. `D-DIS-033` takes that case out: a start that lies in no repository gets
its own opening, and this notice stays where a repository is.

## Revoked on 2026-10-27

By `D-DIS-034`. The notice stays where it stood, and it names the
`typo3://skill` resource rather than `install`. Runs that day showed that a
Claude Code session without skill files read the skill when the sentence named
the URI, and read none when it named the command.
