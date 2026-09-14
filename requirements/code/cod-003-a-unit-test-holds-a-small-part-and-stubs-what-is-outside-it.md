---
id: R-COD-003
title: 'A unit test holds a small part and stubs what is outside it'
status: held
judged: 2026-08-22
restsOn: [D-COD-004]
---

# R-COD-003 — A unit test holds a small part and stubs what is outside it

**A unit test exercises one small part and nothing outside this process.**

What the part reaches outside is a console, a container, an HTTP endpoint, an
executable on the machine. The test stubs it at a seam in the code. It never
starts it, and never arranges for it on the machine either.

Four things follow from it, and the fourth is the one that reaches the code
rather than the test:

- **Nothing starts.** No unit test runs a process, opens a socket, or waits on
  something to come up. `tests/Smoke/` is where a subprocess is the subject, and
  what it starts is this repository's own CLI. The one exception is the
  installation `Upkeep\Fixture` writes. What starts there is `php` over files
  this repository produced. So every machine holds an answer only an
  installation can give, rather than the author's alone. It takes nothing off
  the `PATH`, which is what keeps the sentence above true of everything else.
- **Nothing gets arranged for on the machine either.** An executable written
  into a temporary directory and put on the `PATH` is still a dependency. It
  depends on a writable directory, on `chmod`, and on a `/tmp` nobody mounted
  `noexec`. The test hands a stub to the code, and does not leave it where the
  code will find it.
- **The double is PHPUnit's.** `self::createStub()` where it only has to answer,
  `self::createMock()` where the call itself is the assertion. A hand-written
  class that implements the interface re-invents `willReturn()` and `expects()`,
  and somebody wrote one here before this said so.
- **A data provider carries the cases.** Where one behaviour has several inputs,
  they are a provider with a named case each. So a failure names the input
  rather than a position in a loop. Two loops are not that and stay loops. One
  walks a corpus: every requirement, every hint, every file in a directory. One
  checks several aspects of a single result, where a provider rebuilds that
  result per aspect and says nothing more. A session read the suite for both on
  2026-08-03, and most of what looked like a case table was the second kind.
- **The seam is the code's, not the test's.** A class that reaches outside takes
  what it reaches through as something a caller can replace. Where there is no
  such seam, the seam is part of the work rather than a reason to write the
  other kind of test.

## From

Three shapes in this repository, none of which read as a test against a live
thing and all of which were one. `Typo3CliTest` wrote a `ddev` into a temporary
directory, made it executable and put that directory in front of the `PATH`. So
`Typo3Cli::execute()` forked a real shell to reach it, and
`Typo3Cli::locateBinary()` walked the real `PATH` beside it. `TodoTest` ran
`git worktree add -b` in whatever checkout the suite was in and removed it
afterwards. It wrote its fixtures into the real `todo/`, where a run that died
in between left them, in the queue the next session reads. The third was the fix
for the first: a hand-written `FakeRunner` that did what `self::createStub()`
does.

The suite runs the same with nothing but `php` and `sh` on the `PATH` now. No
git, no ddev, no docker, not even `env`. That is the whole of what this is for,
and the check worth a repeat. A `PATH` that still carries `/usr/bin` says much
less than it looks like it does.

## Held by

- It is **not guarded**, on purpose. A session wrote a test that reads the other
  tests for this and took it out again. The rule is what the practice is rather
  than what a check polices. A suite that greps itself reports on its own shape
  instead of on this server's.
- What stands in for a guard is that the seams exist:
  `TYPO3\DevCompanion\Process\CommandRunner` for what leaves the process,
  `Todo::useDirectory()` for a queue to write into. So the cheaper way to write
  the test is the one this asks for, and PHPUnit's own double fills the seam.
  `D-COD-004` holds the reasons.
