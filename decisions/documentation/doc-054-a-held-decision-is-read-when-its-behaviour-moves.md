---
id: D-DOC-054
title: A held decision is read when its behaviour moves
date: 2026-08-23
status: open
restsOn: [D-DOC-044, D-DOC-048, D-DOC-053]
coveredBy:
  - UnresolvedTest::anOpenDecisionATestHoldsIsNotWaitingForAReader
---

# D-DOC-054 — A held decision is read when its behaviour moves

**`bin/cli unresolved:list` names the oldest open decision that no test holds,
because whoever makes a test fail reads the one it declares.**

Going back to an entry was a scheduled task while nothing pointed at it. The
attribute is the pointer, and the failure is the appointment.

## Evidence

- Read on 2026-08-23. 336 of 454 decisions are open and 155 have no **Since
  then**, which is the pile this listed until today. A test declares 120 of
  those 155, and none declares 35.
- What a declared entry gets is better than a scheduled visit and arrives at the
  right moment. `Tests\Support\HeldEntries` prints the id, the title and the
  path of every entry a failed test held. So the session in the changed
  behaviour is the one who reads it, `D-DOC-044`.
- A scheduled visit arrives at no moment in particular. It was 155 entries deep
  this morning and the oldest was three weeks old, which is what a queue nobody
  owes looks like.

## Decided

- The listing counts all three — open, never revisited, held by nothing — and
  names the oldest of the last kind. The other two numbers stay, because a
  reader who sees only the third cannot tell whether the corpus shrank or the
  bond grew.
- The recurrent todo keeps its job and loses the pile. What it asks for is the
  same judgement; what it points at is the 35 rather than the 155.
- Nothing changes for a requirement. `not guarded` already means no test holds
  it, so its listing was never about the ones that do.

## Assumed

- That a test which declares an entry fails when the entry's own claim moves,
  rather than only when something near it does. It is `D-DOC-048`'s assumption,
  and this entry spends it. An entry held by a test that watches something else
  is now also an entry nobody has on a schedule.
- That behaviour a decision describes gets touched eventually. Nobody reads an
  entry about code nobody edits for a year under this rule, and the listing read
  it before.

## Wrong if

- An entry held by a test goes stale anyway, and what showed it was somebody's
  read of the corpus rather than a failure. Then the bond is thinner than this
  rests on, and the pile it removed did something.
- The 35 stay 35. The point of the oldest's name is that somebody answers it; a
  number that does not move means the visit went rather than narrowed.
