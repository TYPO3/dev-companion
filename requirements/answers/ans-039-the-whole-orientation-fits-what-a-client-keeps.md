---
id: R-ANS-039
title: 'The whole orientation fits what a client keeps'
status: held
restsOn: [D-ANS-194]
heldBy:
  - ScopeTest::theWholeOrientationFitsWhatAClientKeeps
---

# R-ANS-039 — The whole orientation fits what a client keeps

**Each half of the whole `typo3_server_scope` answer stays within
`ServerScope::ANSWER_BUDGET` characters.**

A client that keeps less puts the answer in a file and hands the model a path.
The orientation then costs a second call, and a session that does not make it
reads nothing. So length is a property of the answer. A new entry has to
displace words.

## From

The self-assessment of 2026-10-29. It read the Claude Code limit and measured
the whole answer at 65,786 characters of data.

## Held by

- The test counts the data half as the client counts it, as JSON with slashes
  and unicode unescaped.
