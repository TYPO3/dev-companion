---
id: D-ANS-126
title: The runnable form of a declared command is a field
date: 2026-08-28
status: open
coveredBy:
  - ProjectTest::theAnswerSaysWhatRunsTheProject
---

# D-ANS-126 — The runnable form of a declared command is a field

**`typo3_project_describe` says as data how to run each declared command from
where the caller stands, rather than only in the prose above the list.**

The prose has said it since 2026-08-04 and a session read past it, because the
half it read was the payload. What the payload carries is the command as the
manifest declares it, and the environment as a separate object.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001333`](../../feedback/archive/2026-08-28-001333-declared-commands-and-the-declared-environment.md).
  It ran `composer cgl:ci` as reported and got Composer's platform check: "Your
  Composer dependencies require a PHP version >= 8.4.1. You are running 8.3.23."
- **The text already answers it.** `ProjectDescribe::whereTheyRun()` prints,
  directly above the command list, "They are run in the DDEV project this
  repository configures, not in the shell you have: `ddev composer <name>` for a
  composer script, `ddev exec <command>` for the rest".
  `ProjectDescribe::startable()` warns where the caller's PHP is below the
  bound, which is `D-ANS-086`, for this same failure.
  `ProjectTest::theAnswerSaysWhatRunsTheProject` asserts both, and both landed
  before this session ran.
- **The report reads the payload.** It enumerates `commands`, `environment`,
  `installedPhpBound` and `phpRelation` as fields. It says the answer "never
  said" what the prose says three lines above the list it quotes.
- **In the payload the rule is in a description.** `commands[].command` declares
  "Where environment is not null, it is run inside that environment rather than
  in the caller's shell". That tells a reader of the schema what the value means
  and leaves the value itself one nobody can run.
- **`AGENTS.md` decided this shape in general.** What a reader takes as data
  lives where data lives. What a reader parses out of prose is a regex somebody
  has to write again.

## Decided

- **One field per command**, with the command as the caller runs it from where
  they stand. `environment.entered` decides whether it gets a prefix at all, and
  the manifest that declares it decides the prefix. That is
  `ddev composer <name>` for a composer script, `ddev exec` for the rest, which
  are the two forms the prose already names.
- **Queued.** It is a tool's declared schema, which the ladder keeps off the
  spot whatever the judgement run holds.
- **The prose stays.** It carries why, and the field carries what to run. A
  field that replaced the sentence would leave the caller who runs it on the
  host with no reason for the failure.
- **The report's second half is a separate step**, and it is a read rather than
  a shape. That is whether `ddev composer <script>` passes the stdout of a tool
  the script wraps. Nothing here can establish it, because it needs a DDEV
  project. An entry from the report alone would be a knowledge entry with a
  guess's substance.

## Assumed

- That every declared command is meant to run in the environment where there is
  one. The prose already says so without qualification, so the field states what
  the sentence states.

## Wrong if

- A repository declares a command that has to run on the host, one that calls
  `ddev` itself, or a git hook. The field's prefix turns it into something that
  cannot work. Then the composition needs a condition, and the sentence carried
  that ambiguity because it was a sentence.
- A report shows a client that acts on the prose, which would say the field pays
  for what already arrived.

## Since then

Built the same day as the field, composed from the two things the answer already
held. It is the declared command as it is where nothing can go in front of it.
Tests assert both sides of the flag where the prose half already had one.

The report's second half got a measurement rather than a place on the card. On a
script that fails the wrapper hands the caller exit code 1 and no stdout at all,
with stderr in its own error line. The exec form passes both streams and the
script's own code through. So the wrapper loses exactly the run whose output is
the finding. The field keeps it all the same, because it is what a script that
writes the manifest needs.
