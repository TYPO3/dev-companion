---
id: R-DIS-009
title: 'A negative is never remembered'
status: held
heldBy:
  - InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound
  - Typo3CliTest::aStoppedProjectIsAskedAgainAfterItStarts
  - Typo3CliTest::aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts
  - Typo3CliTest::anUnsupportedAnswerReadsTheCaveatOnce
  - Typo3CliTest::theScopeAnswerDescribesAStoppedProjectOncePerHalf
---

# R-DIS-009 — A negative is never remembered

**The server remembers nothing short of a whole answer: neither "there is no
installation" nor a console reached outside the runtime the project declares.**

The process memoizes a successful resolution. It retries a failure on every
call. The caller who reads that answer is the one likely to install, migrate or
start something and ask again in the same session.

It retries a success that carries a caveat for the same reason. That is the
weaker of two answers, the console of a stopped DDEV project reached through an
interpreter of this machine. The stronger one arrives during the session, by the
`ddev start` the caveat asked the caller for. What it costs is one
`ddev describe -j` per call while the project stands still, 0.25s against
`.environments/e-site-13.4` on 2026-08-04. A failed resolution on that same path
already pays that.

The price falls per read, so a caller reads the state once and writes its whole
answer from it rather than resolves per sentence. Two is where that stops.
`reason()` and `caveat()` each resolve for themselves, so one resolution answers
for the invocation and a second for whichever of the two applies. A session
weighed a fourth accessor that hands all three back from one resolve on
2026-08-04 and did not take it. The saving is 0.25s on one tool in one state,
against a fourth way to ask a class whose three accessors each mean one thing.

## From

A session lost to a cached negative. The agent ran `composer install`, started
DDEV and verified that `bin/typo3` answered. Every tool kept its report of no
installation until a restart of the client (2026-07-29).
