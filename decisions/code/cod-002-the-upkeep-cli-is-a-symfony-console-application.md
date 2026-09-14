---
id: D-COD-002
title: The upkeep CLI is a Symfony Console application
date: 2026-08-01
status: open
coveredBy:
  - UpkeepCommandTest::everyArgumentOfACommandIsOneTheConsoleBinds
  - UpkeepCommandTest::everyCommandClassIsOnTheApplication
---

# D-COD-002 — The upkeep CLI is a Symfony Console application

**`bin/cli` is a `symfony/console` application, one invokable class per command
below `src/Upkeep/Command/`, and a command's name is `<subject>:<verb>`.**

What it replaces is a dispatcher of its own. A `Subject` interface whose
`commands()` declared a usage string, a description and a callable, and a
`help()` that rendered them. A `usage()` each command reached for by hand when
an argument was absent. It worked. What it could not do is bind an argument. A
command read `$arguments[0] ?? ''` and decided for itself what to tell a caller
who passed nothing.

## Evidence

- Written on 2026-08-01, with all 24 commands converted at once. The console was
  already in the tree as a dev dependency of php-cs-fixer, so the cost was a
  `require-dev` entry rather than a new dependency. The session captured every
  read command's output before the change and compared it after. The only
  differences are the command names and what an absent argument reports.

## Decided

- `symfony/console` in `require-dev`, because `bin/cli` is the upkeep of this
  checkout and Composer exports it as no `bin`. What it needs is not what an
  installation of this package needs. Commands are invokable classes with
  `#[AsCommand]`, with their arguments on the parameters of `__invoke` under
  `#[Argument]`. That is the only arrangement where a command declares what it
  takes where it uses it. `Upkeep\Cli` registers every one of them and is the
  only place that switches a command on.

## Assumed

- That the console's own `list` and `help` say enough for the subjects to need
  no one-line description of their own. The `about()` line each subject carried
  is gone, and what a subject is is now the sum of what its commands say they
  do.

## Wrong if

- The console stops to read `#[Argument]` off a command's parameters at the
  moment it does now. It reads them once, before anything merges the application
  definition in. A command it stops to ask keeps every argument in its signature
  while it refuses the caller who passes one.
  `UpkeepCommandTest::everyArgumentOfACommandIsOneTheConsoleBinds` is what
  notices. The fallback is `addArgument()` on each command's definition, which
  is the older API and does not depend on that moment.

## Since then

`feedback/2026-07-31-183652` asked for a `feedback:record` command so an agent
could report without a call to a PHP class. The question went up on 2026-08-02
and came back no. Composer exports `bin/cli` as no `bin`, so a project that
requires this package has no such command to call. A command would sit behind
`Channel::isAvailable()` exactly as `typo3_feedback_record` already does. What
that session hit is that its client could call none of this server's tools at
all.
