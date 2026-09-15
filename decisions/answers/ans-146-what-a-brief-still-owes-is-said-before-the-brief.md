---
id: D-ANS-146
title: What a brief still owes is said before the brief
date: 2026-09-04
status: confirmed
coveredBy:
  - HintsTest::aBriefNamesTheHintsItLeftBehind
---

# D-ANS-146 — What a brief still owes is said before the brief

**The first line of a brief names the hints it did not carry. A reader meets a
pointer under a payload after they have decided what to do.**

The ids already stood there, under the heading of the section they were absent
from, and a session read past them.

## Evidence

- `feedback/2026-09-03-105724`. The brief reported
  `omittedHints: [system-extension-boundaries]`. The session read the four
  carried blocks and went to the code. It made about a hundred further tool
  calls, a second `typo3_task_guide` among them, and the id never came back. The
  patch then crossed exactly that boundary: it began in `impexp` and landed in
  `core/Classes/Database/ReferenceIndex.php`.
- The session did not decide against the hint. It says the id fell out of its
  working set, which is what separates this from `R-GUI-012`, where no answer
  named the ids at all.
- The server cannot tell the two apart from its side. A named hint a session
  fetched and found irrelevant and one it never fetched are the same absence of
  a call. So nothing downstream can report it.
- `skills/base.md` already prescribes the fetch, and the session followed that
  order. Delivery inside the answer was what failed, not the order around it.

## Decided

- Step 2, delivery, of a rule this server had already placed once. The move is
  from under `Hints:` to the head of the answer.
- Said once. The `Hints:` section keeps the sentence that a brief carries the
  strongest few per group, which is a different statement. The ids are only at
  the top.
- The count goes. A list of ids is countable, and a number in prose is what
  `AGENTS.md` says not to write beside the thing it counts.
- Not taken: a second call that notices an id named earlier never got a fetch.
  This server holds no session state, and building one to watch a caller is a
  larger thing than the answer it would improve.
- Not taken either: `system-extension-boundaries` pinned inline where the paths
  span more than one system extension. That is a rank claim, nothing here
  measured it, and the general repair is the notice.

## Assumed

- That a session reads a line above the answer. No placement reaches a session
  that skims the head as readily as the tail. The alternative is a refusal to
  answer until the session fetches the hint.

## Wrong if

- A session reports that it read a brief whose first line named an id and did
  not fetch it. Then placement was not the lever and the gap is a mechanism
  rather than a sentence.
- The line becomes a run of notices at the head of every brief, which would make
  it the payload it moved out of.

## Confirmed on 2026-09-15

[`feedback/2026-09-15-073730`](../../feedback/archive/2026-09-15-073730-the-review-skill-s-order-found-the-blocking.md)
is the other side. The brief named four omitted ids, the session fetched them
before it read the diff, and one of them decided the review. The hint said which
suite covers an `EXT:` path in `f:image`. That suite failed on the patch before
CI voted, and the patch's own tests were green. The session calls the ids the
most valuable calls it made.

