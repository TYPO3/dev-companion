---
id: D-ANS-111
title: A change answer says which comment thread is open
date: 2026-08-26
status: open
coveredBy:
  - GerritTest::aReplyToACommentNobodyCanSeeStandsAsAThreadOfItsOwn
  - GerritTest::everyCommentSaysWhichThreadItIsInAndWhatThatThreadStandsAt
  - GerritTest::theTextHalfListsOneThreadAtATimeAndSaysWhatEachStandsAt
---

# D-ANS-111 — A change answer says which comment thread is open

**Every comment carries the thread it is in and what that thread stands at. The
text half lists one thread at a time under a heading that says which.**

The answer printed two counts of one thing eight lines apart. The caller had to
work the threads out of the reply ids for itself.

## Evidence

- Gerrit's own REST documentation, read on 2026-08-26.
  `unresolved_comment_count` is the "Number of unresolved inline comment threads
  across all patch sets". `CommentInfo.unresolved` says "The state of resolution
  of a comment thread is stored in the last comment in that thread
  chronologically". So the field and the flag are a thread and a comment, and
  this answer counted them as one thing.
- Measured against `review.typo3.org` on 2026-08-26, anonymously and over the
  same path everything else here reads (`D-ANS-033`). Change 91127 carries seven
  comments in five threads, the review server states none unresolved, and a
  tally of the flag says one. 85224 is twelve comments in nine threads, two
  against four. 95179 is four in two, none against two.
- A read of each thread's last comment reproduces the review server's count on
  all three. It does so on the forty open core changes that carry an unresolved
  thread, read the same day. Nothing measured disagrees with it.
- The other candidate, the unresolved comments nobody replied to, agrees on
  thirty-nine of those forty. It is wrong on change 84448, whose one branched
  thread it counts twice. That is the case the documented rule and this one come
  apart on, and the documented rule is the one the review server follows.
- `feedback/2026-08-24-183447` ranked the threads itself, from "a top-level
  comment flagged unresolved with two resolved replies under it". It asked that
  the answer mark what is unresolved **and** top-level. `D-ANS-079`'s section of
  2026-08-25 measured that pair: it selects a settled thread on 91127 and misses
  the open one on 85224.
- The comments payload already carries `in_reply_to` and the order, so the
  thread costs no further call. It is the read the answer already paid for.

## Decided

- Two fields on each comment, `thread` and `threadUnresolved`, rather than a
  list of threads with lists of comments in them. The comments are a flat list
  clients already read, and a field can join where a shape cannot.
- **The thread is the head the reply chain reaches**, and a reply whose parent
  is not in the answer opens one of its own. A reader without credentials cannot
  see a draft (`R-ANS-027`), so its replies arrive with an id nothing here
  carries. In no thread at all they would drop out of the list.
- **The state is the flag on the thread's last comment**, which is Gerrit's rule
  rather than this side's derivation. The leaf rule had its measurement beside
  it and is the one that breaks on a branch.
- The text half lists a thread per heading with its state, the author who opened
  it and where it sits. The comment lines under it carry neither a state nor the
  reply relation the order already says. Seven states on a change with five
  threads is the contradiction this replaces.
- **This side derives the count in that heading** rather than prints the review
  server's field a second time. So the list and its count are one read. It
  reproduces `unresolvedCommentCount` beside it, and a change where the two
  disagree is visible rather than silently reconciled.
- `standing()` says "unresolved threads" where it said "unresolved of n
  comments". The field's own name counts comments and its value counts threads,
  and this answer is not the place that repeats the mistake.
- **The refusal stands**: this server hands over the state and does not judge
  whether a question got an answer. A resolved thread can hold an open question,
  and an unresolved one can carry the reply that settled it —
  `D-ANS-079`.

## Assumed

- That `updated` is the order Gerrit means by "chronologically". It is the one
  date the comment payload carries. A comment somebody edited later would sort
  by the edit rather than by its origin.
- That a caller reads `threadUnresolved` as a flag somebody set. It is the same
  read `unresolved` needed and did not get, one level up.

## Wrong if

- The derived count and `unresolvedCommentCount` come apart on a change somebody
  reads. Forty changes and the documentation say they do not, and a thread shape
  nothing here has seen is what would show it. The reply relation is the whole
  of what this side knows.
- A caller reports a resolved thread as settled and reads no further. That is
  the judgement `D-ANS-079` refuses to make, moved from the flag to the thread.
  The paragraph under the comments stands against it.
- The threads turn out to be what a caller wants nested. That is a review that
  answers them one at a time, or a client that renders the tree. The two fields
  are then a shape everybody rebuilds.
