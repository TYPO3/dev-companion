---
id: D-DIS-002
title: Discovery honours the declared vendor-dir and bin-dir
date: 2026-07-29
status: revoked
revokedBy: D-DIS-007
---

# D-DIS-002 — Discovery honours the declared vendor-dir and bin-dir

**Discovery finds the packages and the console through the `vendor-dir` and
`bin-dir` the root `composer.json` declares, rather than through their default
paths.**

Closes the feedback that `.build/bin/typo3` was out of reach and that the
extension checkout did not count as an installation at all.

## Evidence

- `bootstrap_package` on this machine: 21 packages found, and the console
  resolved as `ddev exec -- .build/bin/typo3` on PHP 8.5. The 29
  `content-bootstrappackage-*` icons the server had reported as non-existent are
  there.

## Assumed

- What the root `composer.json` declares is enough to find both the packages and
  the console. Composer's `config.vendor-dir` and `config.bin-dir` are the only
  two ways either moves in practice. Everything else, DDEV and the interpreter
  choice, was already right and never got a binary.
- Invoking the console through a path relative to the installation root works
  inside DDEV as it does on the host.

## Wrong if

- An installation that runs its console from somewhere other than the root. A
  DDEV project whose container work directory is the docroot rather than the
  project root would need an absolute path or a `cd`. Also an absolute
  `bin-dir`, which Composer accepts and this server ignores.

## Revoked on 2026-08-01

Both halves of the **Wrong if** ran and the second **Assumed** is false. A
relative path does not work inside DDEV as it does on the host. The server never
reads `working_dir` and calls `ddev exec` blind, so from a moved work directory
the same call is exit 127. It worked as long as nothing had moved it, which is
the default and not a guarantee. The absolute `bin-dir` half happened as written
and the decision held there. The gap is that absolute is a form of the same
directory. So the server writes one below the root relative to it now, which is
the form both DDEV and the host need. `R-DIS-003` holds both.

## Since then

The repair has its measure and not yet its change. Both
`/var/www/html/.build/bin/typo3` and `$DDEV_APPROOT/.build/bin/typo3` answer in
both work directories, and the second is the one that guesses nothing. DDEV sets
`DDEV_APPROOT=/var/www/html` in the container itself, and `ddev exec` hands its
arguments to the container's bash, which expands it. What that still needs is
the DDEV version from which the variable holds. A form that silently resolves to
`/.build/bin/typo3` on an older one is worse than the relative path it replaces.
It is v1.24.5, which makes the mount the form that guesses nothing and the
variable the one with a version on it. Made on 2026-08-02, with the account in
`D-DIS-007`.
