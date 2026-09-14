---
id: D-EVI-005
title: 'A registration nothing can reach is cleared with its database'
date: 2026-08-02
status: open
coveredBy:
  - EnvironmentsTest::aRegistrationWhoseCheckoutIsGoneHoldsNothingBack
  - EnvironmentsTest::clearingARegistrationTakesTheDatabaseThatWouldOutliveIt
---

# D-EVI-005 — A registration nothing can reach is cleared with its database

**`environment:create` clears a DDEV registration whose approot is gone rather
than refuses in its name. It uses `ddev delete`, so the database it still owns
goes too.**

`D-EVI-004` made the environment creatable and left the second machine that runs
the build stuck. The name is global and the directory is per checkout. So a
worktree that made an environment and went away holds the name on behalf of a
directory nobody can visit.

## Evidence

- Measured on 2026-08-02, on this machine, before any change. `typo3-mcp-e-site`
  stood registered at
  `.worktrees/record-what-the-installation-backed-tools-answer-when-a-console-answers/.environments/e-site`,
  a directory that did not exist. DDEV's own `status` for it was the string
  `project directory missing`. `bin/cli environment:create E-SITE` refused,
  named that path and said nothing about what to do.
  `bin/cli environment:status` said
  `missing — run bin/cli environment:create E-SITE`, which was advice that could
  not work.
- The database is a volume named after the project, not after the directory.
  `typo3-mcp-e-site-mariadb` sat on the machine beside the dead registration,
  with `ddev-typo3-mcp-e-site-snapshots`. That is why a build under the same
  name after an unlist meets the tables the last installation left. It reports
  `The selected database contains already 42 tables.`
- DDEV 1.25.1's own help separates the two commands. `stop` is "a
  non-destructive operation and will leave database contents intact", and
  `--unlist` only removes the project from the global list. `delete` "Removes
  all DDEV project information (including database)".
- `ddev delete --omit-snapshot -y typo3-mcp-e-site` ran against the registration
  whose directory was already gone. Exit 0, containers removed, both volumes and
  the two built images with them. The absent directory is not an obstacle to it.
- `--force` reaches the settings file and nothing else. In `.checkouts/14.3` at
  `v14.3.5-81-gfaf60eea22` its declaration reads
  `Force settings overwrite - use this if TYPO3 has been installed already`.
  `prepareSystemSettings($force)` is its only use in `SetupCommand`. The table
  check is `$dbNameValidator` in `selectAndImportDatabase`. It throws whenever
  `tables !== 0`, and the non-interactive path calls it as much as the asked
  one. No option of the setup gets past it.
- The fix ran end to end afterwards, on a registration reproduced the same way:
  configured, started so it had a volume, then its directory removed.
  `environment:create E-SITE` cleared it and built a working installation in 32
  seconds. The frontend answered 200, and `site:list`, `configuration:show` and
  `language:domain:search` all answered through the console.

## Decided

- The predicate is whether the registered approot is a directory, not whose
  checkout it was. An `rm -rf .environments` in this checkout leaves the same
  orphaned volume as a removed worktree does, and one rule covers both.
- The command clears rather than prints. `environment:create` exists to change
  the machine, and every step of it is a `ddev` command printed before it runs.
  An environment nothing can reach is not one it takes from anybody. A print
  would also have to print the right command, and a step nobody would decline is
  not a decision worth a handover.
- The command is `ddev delete --omit-snapshot -y`, not `ddev stop --unlist`. An
  unlist frees the name and leaves the volume, which moves the failure three
  minutes later into the setup step, where nothing gets past it.
- Where the approot **is** there, the refusal stands unchanged. That is a live
  checkout's environment, and a takeover would stop it in silence.
- `environment:status` reports and never clears. It names the other checkout
  where one holds the name, and otherwise keeps its name of the create command.
  That is now true because create clears what it can reach past.
- The promise above `build()` gets a correction rather than a keep. "Every one
  of them is idempotent or forced" does not hold at the setup step against a
  populated database. The sentence `environment:create` printed on failure,
  "this command carries on from it", was that same promise at the moment
  somebody reads it.
- Rejected: recognition of TYPO3's `contains already` message in the build
  output. It is a hardcoded English string in `SetupCommand` with no translation
  behind it. A guard bound to it would fail in silence on the day somebody
  rewords it. The failure message names the exception instead.
- Rejected: a create that stops where it finds a database it did not put there.
  Nothing here can ask that question before the containers are up. The place it
  belongs is TYPO3's setup, which is not this repository's.

## Assumed

- That an approot which is gone means an environment which somebody wants gone.
  A directory absent because of an unmounted drive or a moved worktree would
  lose its database to this, and nothing would say so. That stands against
  `D-EVI-004`, which puts the environment in the class of things made on demand
  and re-creatable. It stands against the build at 32 seconds on a warm cache.
- That the volume keeps its name after the project. It is what makes an unlisted
  name a populated database later, and it is DDEV's convention rather than a
  documented promise.

## Wrong if

- ~~A session loses a database it wanted to an approot that was only temporarily
  absent. Then the clearing needs to ask, or to keep the snapshot `delete` would
  otherwise take.~~ Priced out on 2026-08-22: the default driver is SQLite and
  the build runs `omit_containers: [db]`, so the registration holds no database
  to lose.
- `ddev delete` stops to work on a project whose directory is gone. That would
  put the two-command sequence back and leave the volume to the build.
- TYPO3's setup gains a way past a populated database. Then the create has an
  option rather than a deletion, and the failure message names the wrong way
  out.

## Since then

The first **Wrong if** is struck rather than answered. Every environment runs
`omit_containers: [db]` on sqlite, so a cleared registration nothing can reach
takes a name and no data. `discard()` still deletes the project for one built on
MySQL. The second has not fired and no test here can try it without a
registration taken apart. The third is an outside event and stays open.
