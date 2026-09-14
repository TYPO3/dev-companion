---
id: D-ANS-055
title: 'A change answers for an issue its commit message names'
date: 2026-08-05
status: open
coveredBy:
  - GerritTest::aChangeMatchedByItsNumberIsNotAnswered
  - GerritTest::aChangeWhoseMessageDidNotComeBackIsJudgedByItsNumberAlone
  - GerritTest::anAnswerOfNothingButFalsePositivesIsEmpty
  - GerritTest::theCommitMessageIsReadByBothFormsAndHandedBackByAChangeReadByName
  - GerritTest::theNumberInAReviewUrlIsNotTheIssueBeingNamed
---

# D-ANS-055 — A change answers for an issue its commit message names

**The tool hands a change back for an issue only where its commit message names
that issue. It holds back what the review server matched some other way.**

`message:<number>` is the right question and not the whole answer. Gerrit
indexes the change number under the same operator. So the search returns the
change that carries the number as its own whatever it is about.

## Evidence

- `feedback/2026-08-05-033826`: five of seven calls in one session came back
  with a MERGED core change. Its number equalled the issue number and its
  subject had nothing to do with it. The truthful answer for all seven was
  nothing.
- Re-measured against `review.typo3.org` on 2026-08-05. `message:81676`,
  `message:87400`, `message:88690` and `message:93409` each answer with the
  change of that number and with nothing else. `message:88556` answers with
  change 88556 and with change 95108, which resolves the issue.
- Change 88556's commit message names issue 106318 and carries `88556` in its
  `Reviewed-on:` trailer alone. That is the trailer a merged change gains, which
  ends in the change's own number.
- A narrower query does not settle it. `message:"#88556"` still answers with
  both, and `message:"Resolves: #88556"` answers with the right one and would
  miss every `Related:` and every issue named in a sentence.
- Both skills that call this say what the false positive costs.
  `typo3-core-issue-triage` calls its cheapest outcome the one that ends the
  work, and `typo3-core-patch-development` the one that cancels it. The reported
  session was two clicks from the abandonment of a patch that did not exist.

## Decided

- The filter is on this side. `o=CURRENT_COMMIT` comes back with the search, and
  the tool drops a change whose message does not carry the number outside a URL.
- The tool removes URLs and the `Change-Id:` line before it reads the message.
  The trailer that carries the number and does not mean it is a URL that ends in
  it.
- Everything else in a reference position stands: `Resolves: #88556`,
  `Related: #88556`, an issue named in prose. What drops out is the match no
  human wrote.
- A change whose message did not come back meets the one rule that needs none.
  It is the false positive where its own number is the number the caller asked
  for.
- A `change:` lookup filters nothing. A caller who names a change has named it.
- The answer says what it held back, with the reason. The `query` field exists
  so a caller can ask the question by hand, and a hand-run query answers with
  more than this one.

## Assumed

- A change that resolves an issue names it in its commit message. That is what
  the core's own process requires, and it is what `message:` reached for before
  the index widened it.
- The number is not carried in a further place that means nothing. A version
  string like `12.4.88556` would read as a reference; none of the five measured
  messages carried one.

## Wrong if

- A session reports a change this dropped that really was about the issue. That
  shows as a `dropped` count beside an answer a caller then finds by hand.
- The core no longer writes the issue into the message, and the filter empties
  the answer for a whole class of change.
- `o=CURRENT_COMMIT` no longer answers anonymously, which would leave the number
  rule alone to decide every answer.
