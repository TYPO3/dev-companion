---
id: D-KNW-166
title: A changed constructor is settled on who calls it
date: 2026-10-05
status: open
coveredBy: []
---

# D-KNW-166 — A changed constructor is settled on who calls it

**`public-api-surface` says that PHP compares no constructor signatures, and
that the core files a changed constructor on who calls it. A service only the
container builds needs no entry, while a class code constructs or extends gets a
Breaking one.**

## Evidence

- `feedback/archive/2026-10-05-105018-public-api-surface-says-nothing-about-removed.md`.
  A review of change 96354 met a removed parameter in the constructor of
  `SetupCommand`, which is neither final nor `@internal`. The hint covered
  methods and stopped one step before the case.
- PHP 8.3 on 2026-10-05: a subclass constructor with another signature loads, an
  abstract constructor is compared, and an extra argument to
  `parent::__construct()` is dropped without a word. The manual says the same of
  `__construct()`.
- Read in `.checkouts/main` on 2026-10-05. `926b9a704b`, a `[TASK]` with no
  changelog file, added a `FailsafePackageManager` to the constructor of
  `SetupCommand`. `Breaking-106405` files the removed constructor of
  `AbstractTypolinkBuilder`, a base classes extend, and `Breaking-105733` the
  regex argument code passed to `FileNameValidator`.

## Decided

- The PHP rule stands for every audience. The practice stands as a core line.
- The hint names no commit and no changelog file. Both examples are 14 and do
  not hold on the branches before it, which `HintsTest` refuses.
- Nothing about release lines. `926b9a704b` is older than the 14.3 branch, so it
  is no backport and says nothing about what a release line takes.

## Wrong if

- A reviewer asks for a changelog entry on a dependency change of a container
  service, and the core files one.
