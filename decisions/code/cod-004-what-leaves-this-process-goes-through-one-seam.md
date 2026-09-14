---
id: D-COD-004
title: What leaves this process goes through one seam
date: 2026-08-03
status: open
coveredBy:
  - TodoTest::aWorktreeIsToldApartFromTheCheckoutItWasCutFrom
  - TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim
  - Typo3CliTest::everyArgumentReachesTheContainerAsTheShellLeavesIt
  - Typo3CliTest::theDdevConsoleIsNamedByAPathTheWorkingDirectoryCannotMove
---

# D-COD-004 — What leaves this process goes through one seam

**A call that leaves this process goes through an interface a caller can
replace.**

A command, an executable lookup, an HTTP request: a unit test stubs each of them
instead of an arrangement of the real thing.

## Evidence

- `Typo3CliTest` proved an invocation with a build of one. It wrote a `ddev`
  script into `sys_get_temp_dir()`, `chmod`ed it to 0755, and put the directory
  in front of `PATH`. It let `Typo3Cli::execute()` fork a shell to reach it. One
  of its two cases went further and reproduced DDEV's own argument join through
  `bash -c`, to show that `--regex=/(save)/i` survives. No DDEV had to exist,
  which is why this passed for a mock for as long as it did.
- The same class walked the real `PATH` in `locateBinary()`. So whether the test
  saw a `ddev` at all was a property of the machine that ran the suite.
- `proc_open` stood three times: `Typo3Cli::execute()`, `Environments::run()`
  and `Checkouts::run()`. Two of them get the same three things right, and one
  of them writes the reasons down. Those are stdin not inherited, the exit code
  read off `proc_get_status()` rather than `proc_close()`, and both streams
  drained before the close. The third differs on purpose: git wants the stdin it
  inherits.
- `TodoTest` was the worse of the two offenders and the one no search for
  `proc_open` in a test file would have found. It went through
  `Checkouts::run()`. To hold `Todo::linked()` it ran `git worktree add -b` in
  whatever checkout the suite was in, and asserted. Then it ran
  `worktree remove --force` and `branch -D` in a `finally`. A unit test that
  writes to the developer's repository.
- HTTP was already behind a seam. `Http\Fetch` takes a
  `(\Closure(string): ?string)|null $transport` in its constructor. Its own
  docblock says every test passes one, which is what keeps the suite the same
  offline. It is a closure rather than this interface because it is a different
  boundary, and the same one gains nothing.

## Decided

- `TYPO3\DevCompanion\Process\CommandRunner` is the seam: `run()` for a command,
  `locate()` for whether the machine has an executable. Both are the same
  boundary, because to ask whether `ddev` exists is to ask the machine. So one
  interface for both is what lets a test stub the whole question.
- `SystemRunner` is the only implementation outside a test, and it is where the
  three `proc_open` details now live once.
- `Typo3Cli::useRunner()`, `Environments::useRunner()` and
  `Checkouts::useRunner()` are how a test hands one in. Static, because all
  three classes are. `Typo3Cli::forget()` on purpose does not reset it. A test
  drops the memoized resolution between installations and would otherwise drop
  the stub with it. To put it back belongs to the test's own teardown, which is
  where `QueuedTodo` does it.
- Inherited stdin is a parameter on `run()` rather than a second implementation.
  git wants it, and the console and the build must not have it. One flag says
  which is which where two classes said it as two classes.
- What a test hands in is `self::createStub(CommandRunner::class)`, configured
  with `willReturn()`, `willReturnCallback()` or
  `willReturnOnConsecutiveCalls()` as the case needs. A `FakeRunner` class came
  first, with a table keyed by the command line and a `commands()` recorder.
  That is `willReturnCallback()` and `expects()->with()` written again by hand.
  It is gone.
- `Todo::useDirectory()` is the same seam for the queue. The cases that hold
  claims and releases write into a `todo/` of their own below the system
  temporary directory. A todo's path is relative to the root that directory sits
  in, so the redirect carries the root with it.
- Rejected: a test that reads the other tests and fails where one starts a
  process. A session wrote it, it worked, and the session took it out. The rule
  is the practice rather than something a check polices, and the seams are what
  make the asked-for shape the cheaper one to write.

## Assumed

- That the three `useRunner()` seams are all of them. A sweep of `tests/Unit`
  and `tests/Contract` on 2026-08-03 found no other call that starts a process,
  opens a socket, writes an executable or touches `PATH`. What that sweep cannot
  say is that nobody will write the next one.

## Wrong if

- A stub drifts from what the real thing does. A test goes on to pass against a
  `ddev describe -j` that no DDEV has answered that way for a year. Nothing here
  checks a stub against the thing it stands for.
- The static seam leaks between tests, because one forgets to put it back and a
  table written for something else answers the next.
- A fourth caller writes its own `proc_open` rather than takes the runner. That
  is the state this replaced, and nothing prevents it on purpose. The check that
  would prevent it is the one `R-COD-003` declines.
