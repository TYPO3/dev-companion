---
id: D-ANS-146
title: What a brief still owes is said before the brief
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::aBriefNamesTheHintsItLeftBehind
---

# D-ANS-146 — What a brief still owes is said before the brief

**The hints a brief left uncarried are named on its first line, because a
pointer under a payload is read after the reader has decided what to do.**

The ids were already named, under the heading of the section they were missing
from, and a session read past them.

## Evidence

- `feedback/2026-09-03-105724`. The brief reported
  `omittedHints: [system-extension-boundaries]`, the session read the four
  carried blocks, went to the code, and made about a hundred further tool calls
  — a second `typo3_task_guide` among them — without the id coming back. The
  patch then crossed exactly that boundary: it began in `impexp` and landed in
  `core/Classes/Database/ReferenceIndex.php`.
- The session did not decide against the hint. It says the id fell out of its
  working set, which is what separates this from `R-GUI-012`, where the ids were
  not named at all.
- The server cannot tell the two apart from its side. A named hint that was
  fetched and found irrelevant and one that was never fetched are the same
  absence of a call, so nothing downstream can report it.
- `skills/base.md` already prescribes fetching them, and the session was
  following that order. Delivery inside the answer was what failed, not the
  order around it.

## Decided

- Step 2, delivery, of a rule this server had already placed once. The move is
  from under `Hints:` to the head of the answer.
- Said once. The `Hints:` section keeps the sentence that a brief carries the
  strongest few per group, which is a different statement, and the ids are only
  at the top.
- The count goes. A list of ids is countable, and a number in prose is what
  `AGENTS.md` says not to write beside the thing it counts.
- Not taken: a second call noticing that an id named earlier was never fetched.
  This server holds no session state, and building one to watch a caller is a
  larger thing than the answer it would improve.
- Not taken either: pinning `system-extension-boundaries` inline where the paths
  span more than one system extension. That is a ranking claim, nothing here
  measured it, and the general fix is the notice.

## Assumed

- That a line above the answer is read. A session that skims the head as readily
  as the tail is not reached by any placement, and the alternative is refusing
  to answer until the hint is fetched.

## Wrong if

- A session reports reading a brief whose first line named an id and not
  fetching it. Then placement was not the lever and what is missing is a
  mechanism rather than a sentence.
- The line becomes a run of notices at the head of every brief, which would make
  it the payload it was moved out of.
