---
id: D-KNW-134
title: What the functional harness does to the working directory is stated
date: 2026-08-28
status: open
readings:
  - 2026-09-01
---

# D-KNW-134 — What the functional harness does to the working directory is stated

**`project-extension-tests` says that a functional test runs with the working
directory at the instance path, because a test written without that passes with
the defect it was written to prove.**

The hint already carries what the harness does around a test — the databases,
the instance directory, the interpreter check. The working directory was the one
thing it did not carry, and it is the one a path-resolution test turns on.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001314`](../../feedback/archive/2026-08-28-001314-project-extension-tests-omits-that-testbase.md).
  It wrote the obvious regression test for a fix that resolves relative cache
  paths, and the test passed with and without the fix. For one round it
  concluded the reported defect did not reproduce, which is the wrong answer to
  a pull request; `grep -rn chdir` over the installed framework explained it.
  Two functional runs of about 25 seconds each, plus the wrong conclusion.
- **Read in `.checkouts/testing-framework` on 2026-08-28, on all three lines.**
  `Testbase::setUpInstanceCoreLinks()` calls `chdir($instancePath)` — line 222
  on `8`, line 215 on `9` and on `main` — so it holds for every covered major
  and the statement takes no binding.
- **It runs once per test case class.** `FunctionalTestCase::setUp()` calls it
  in the `isFirstTest` branch, and that flag is a static tracking the current
  class. Nothing changes the directory back, so every test after it runs there
  too.
- **The same path is the public path.** `setUpBasicTypo3Bootstrap()` sets
  `$_SERVER['PWD']` to the instance path on every test, and that is what
  `SystemEnvironmentBuilder::run(0, …)` resolves `Environment::getPublicPath()`
  from.
- **Nothing in the corpus said it.** The hint's eleven statements cover the
  instance directory, its `_ft<7 hex>` databases and what `tearDown()` leaves,
  and none of them names `chdir`.

## Decided

- One statement, beside the instance-directory statements the hint already
  carries, naming `Testbase::setUpInstanceCoreLinks()` and `chdir` so the
  symptom is greppable — which is how the reporting session eventually found it.
- **Made in this run rather than queued.** The reading is
  `.checkouts/testing-framework`, which this judgement did, and `D-FBK-052`
  bounds the queueing rule to a lookup still to be made.
- Written from the checkout rather than from the report's proposed sentence,
  which claims the working directory equals the public path without saying that
  two different calls set the two.
- Not bound with `since` or `until`: it is the same call on the `8`, `9` and
  `main` lines, which is every release line this repository pins.

## Assumed

- That a session reading the hint whole reads a statement in it. This one was
  read whole and the missing line is what the session said it needed.

## Wrong if

- The framework moves the `chdir` out of `setUpInstanceCoreLinks()` and the
  statement names a call that no longer makes it. `bin/cli checkouts:update`
  moves those lines, and nothing here re-reads the statement when it does.
- A session reports moving the working directory in a test and something else in
  the harness moving it back.
