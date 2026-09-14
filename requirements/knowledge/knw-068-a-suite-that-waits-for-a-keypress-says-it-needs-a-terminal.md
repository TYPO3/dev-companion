---
id: R-KNW-068
title: 'A suite that waits for a keypress says it needs a terminal'
status: held
restsOn: [D-KNW-068]
heldBy:
  - KnowledgeTest::aSuiteThatWaitsForAKeypressSaysItNeedsATerminal
---

# R-KNW-068 — A suite that waits for a keypress says it needs a terminal

**A suite whose script waits on a read from `/dev/tty` says so. It says that a
run with no terminal reports SUCCESS after it tore down what it installed.**

`runPlaywright()` ends in `read ... </dev/tty`. That is not a container flag, so
`CI=true` does not remove it. Without a terminal in control the redirect fails,
the wait ends at once, and the cleanup removes the instance the suite exists to
leave up. The exit code is still the one from before the wait. The banner and
the exit code then both say the opposite of what happened. The URL printed above
them is dead by the time anybody reads it.

The entry says in as many words that `CI=true` does not stand in for a terminal.
The note that declares `CI=true` says the same from its side. That note is for
scripted and non-interactive runs. So a session that has set it has already
answered the question the suite is about to fail on. It reads the failure as
something it did wrong.

A false green is the failure a session cannot see. It is the same shape
[`R-KNW-049`](knw-049-a-check-that-can-pass-without-reading-anything-says-so.md)
holds `cglGit` to. It is the reason the condition sits in the entry that offers
the command rather than one entry away.

The entry names what tears the instance down rather than only that it happened.
The cleanup at the end of every run kills the containers on that run's network.
So the outcome holds for a run that reaches that cleanup and for no other. A run
killed earlier, a wait that failed or a wrapper that ended, leaves
`ac-web-<suffix>` and `ac-phpfpm-<suffix>` up with the instance still served. A
session that reads the outcome as unconditional reports a live instance as gone.

The invocation notes carry both ways out, because a session that needs an
instance that stays up has no other. The way through is a terminal from
util-linux `script`, and stdin from something that stays open and never writes.
The way back is the container runtime. `docker ps` says what a run left behind,
not its exit code, and a stop by name removes it.

## From

A core patch review that ran the prepare suite from a background shell with
`CI=true` set. It lost the instance to the failed redirect after several minutes
of composer work, and worked the pty out for itself. One failed attempt and a
2.2 MB log of NUL bytes lay on the way (`feedback/2026-08-13-214729`,
2026-08-13). A session verified the read in `runTests.sh` on `.checkouts/main`,
`14.3` and `13.4`.

A headless session that took backend screenshots then hit the other half. Its
run ended before the banner and both containers still served. It nearly reported
the instance gone on the strength of this entry's own sentence
(`feedback/2026-08-24-225044`, 2026-08-24). `cleanUp()` at the end of the script
is what removes them, on the same three checkouts.
