---
id: D-ANS-001
title: The unanswered result keeps its shape and gains a reason
date: 2026-07-29
status: revoked
revokedBy: D-ANS-005
---

# D-ANS-001 — The unanswered result keeps its shape and gains a reason

**The unavailable case keeps the result shape every other answer has and carries
its reason in an `unavailable` object, with `found` null rather than false.**

Two feedback asked that the unavailable case no longer look like an empty one.
One proposed to drop `matchCount`, `icons` and `found` altogether and return an
error-shaped object instead. Or to rename `answeredBy: "nothing"` to something
no reader takes as "no source had it".

## Decided

- The shape stays, an `unavailable` object carries the reason, and `found` is
  null rather than false when the tool asked nothing. A field a schema requires
  has to be present on every path through the tool. Keys dropped in one case
  would make the declared output schema a shape a client cannot rely on. That is
  the same defect one level up.

## Assumed

- A caller that reads `unavailable.reason` is better off than one that has to
  interpret an enum value. So a rename of `nothing` buys little and breaks every
  client that already matches on it.

## Wrong if

- Clients ignore `unavailable` and still read a miss as a registry answer.
  `isError: true` on the result is then the next lever. It is the bluntest, and
  it would make the answer an error rather than an answer. That is why this
  entry did not take it first.

## Since then

The session found no client that has met this shape at all. It read both
recorded runs, the open feedback and the archive for a miss reported as a fact,
and none carries one. The reason is not that clients handle it — every session
since came from a directory whose installation was reachable, so the shape never
appeared.

The server still answers as this entry decided, driven over stdio from an empty
directory. A contract case that names the two environments would settle the
**Wrong if**. Four unit tests hold it, and they read what the server emits
rather than what a client reads off it. So the evidence needs a session in one
of them.

## Revoked on 2026-08-02

The shape was not worth its keep, and the hold on it forced the nulls. A tool
that cannot ask has to emit every field its schema requires, and those fields
are the answer: a count, a flag, a list. To withhold what they said turned the
counts and the flags nullable, which is a result shape kept with the numbers
faked out of it. The successor replaces the result instead, and what this entry
got right is in it unchanged.

## Since then

The session ran on 2026-08-04, in the `E-NONE` this checkout now makes itself,
and what it met was `D-ANS-005`'s shape rather than this one. The inherited
**Wrong if** did not fire: the client reported that it could not ask the
installation and named the two settings that end it. The successor records what
the run established.
