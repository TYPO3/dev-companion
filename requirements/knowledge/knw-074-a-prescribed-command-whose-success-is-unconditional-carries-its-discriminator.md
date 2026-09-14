---
id: R-KNW-074
title: 'A prescribed command whose success is unconditional carries its discriminator'
status: held
restsOn: [D-KNW-093]
heldBy:
  - HintsTest::aCommandThatAlwaysSucceedsCarriesItsDiscriminator
---

# R-KNW-074 — A prescribed command whose success is unconditional carries its discriminator

**Where a hint prescribes a command that reports success without an action, the
hint says what a correct result looks like outside that output.**

In this domain a success message is not evidence. The hint that hands the caller
the command is the only place they read the discriminator. Nothing carries it
there from a warning elsewhere in the corpus. What it names sits outside the
command: the artifact, the database, the directory the command was meant to
write.

The rule binds to a command whose success is unconditional, read off that
command's own class rather than off its message. A command that reports its own
failures gets nothing. A discriminator there warns about a trap the reader does
not walk into, and every sentence costs in the answer that carries the hint.
[`R-KNW-024`](knw-024-a-check-is-offered-only-where-the-command-exists.md) is
the same economy on the other side.

Its neighbour is
[`R-KNW-049`](knw-049-a-check-that-can-pass-without-reading-anything-says-so.md).
That one keeps a check that inspected nothing from a read as a check that ran.
This one keeps a command that acted on nothing from a read as a command that
acted.

## From

A session that built a demo site and hit four of them in three quarters of an
hour. `impexp:export` shipped no image bytes, and the same command wrote to a
path nobody gave it. `extension:setup` migrated from a cached TCA, and
`DataHandler` discarded every inline relation. It paid between two and twelve
round trips for each
(`feedback/2026-08-17-212800-four-commands-reported-success-while-doing.md`,
2026-08-17). The sweep those four earned found `language:update`,
`backend:user:create` and `upgrade:run` answer the same way.
