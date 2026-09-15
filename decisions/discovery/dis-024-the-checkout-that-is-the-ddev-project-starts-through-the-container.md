---
id: D-DIS-024
title: The checkout that is the DDEV project starts through the container
date: 2026-09-15
status: open
coveredBy:
  - InstallerTest::theCheckoutThatIsTheDdevProjectStartsItsOwnEntrypointInTheContainer
---

# D-DIS-024 — The checkout that is the DDEV project starts through the container

**`Installer::installedEntrypoint()` answers the entrypoint relative to the
project where the entrypoint sits below the project, after the bin directory and
`vendor/bin`. So an install in this server's own checkout with a DDEV project
gets `ddev exec php bin/typo3-dev-companion`.**

Whether the container can see the entrypoint and whether the project depends on
this server are two questions, and the installer answered the first with the
second.

## Evidence

- **The report.**
  [`feedback/2026-09-10-202624`](../../feedback/archive/2026-09-10-202624-install-in-this-server-s-own-checkout-ignores.md),
  this checkout, `claude-opus-5[1m]`. `install --agent=claude` in the checkout,
  with a started DDEV project beside it, wrote the host PHP and the absolute
  path. The host PHP had no ext-curl, and every network-backed tool failed. An
  entry rewritten by hand to `ddev exec php bin/typo3-dev-companion` answered
  the same lookup whole.
- **Run again on 2026-09-15** as `InstallerTest`'s new case, an `Installer`
  built on a project that holds the entrypoint at `bin/` beside a
  `.ddev/config.yaml`. It wrote the absolute path, as the report says.
- **Composer links no binary of the root package**, so `vendor/bin` and the bin
  directory hold nothing in the checkout. That is why the read of those two
  answered null and the DDEV branch never ran.

## Decided

- **A third place to look, after the two the dependency case has.** The
  entrypoint the installer was started from, where it sits below the project,
  relative to it. `Entrypoint` hands the installer `realpath($argv[0])` and
  `getcwd()`, both canonical, so a prefix comparison settles it.
- **The look is on the path and never on `composer.json`.** A checkout is the
  project where the entrypoint is below it, whatever the name of the root
  package.
- **The `${workspaceFolder}` shape gets the same path**, because the answer is
  the entrypoint relative to the project and every branch of `startedBy()` reads
  it. `D-DIS-016` says which clients that shape holds for.
- **The requirement widens by the same case.** `R-DIS-015` names the checkout
  beside the bin directory, and its host-path clause names the project that is
  neither.

## Assumed

- That a project below this checkout is not one. The prefix comparison answers
  null for it, and the absolute path stands, which is what held before.

## Wrong if

- A client starts the entry in a working directory that is not the project root,
  and `bin/typo3-dev-companion` does not resolve. `ddev exec` runs in the
  container's project root, so this is the same bet the dependency case makes.
- An install in a checkout with no DDEV project writes a relative path for a
  client whose documentation resolves nothing. Then the third branch of
  `startedBy()` took a path it may not.
