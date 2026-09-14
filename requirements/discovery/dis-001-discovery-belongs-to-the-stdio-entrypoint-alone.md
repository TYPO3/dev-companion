---
id: R-DIS-001
title: 'Discovery belongs to the stdio entrypoint alone'
status: held
heldBy:
  - InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance
---

# R-DIS-001 — Discovery belongs to the stdio entrypoint alone

**Nothing derives the installation from `getcwd()` on its own. The directory a
search starts from comes from a caller, and only the stdio entrypoint hands one
in.**

`Instance` keeps that directory private and null. `discoverFrom()` is its only
setter, and `describe()` walks up from whatever it holds. With nothing handed in
there is no directory to walk from and the search finds nothing. That is what a
request-serving endpoint has to get. It has no such relationship to its callers,
and its document root may itself sit inside an installation.

`TYPO3_DEV_COMPANION_ROOT` names the root outright, which is a decision rather
than a derivation. So it holds for every entrypoint and is not what this
restricts.

The one call is in `Server\Entrypoint`, which `bin/typo3-dev-companion` runs and
nothing else does.
