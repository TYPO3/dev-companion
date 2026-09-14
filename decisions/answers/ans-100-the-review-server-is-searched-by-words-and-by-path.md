---
id: D-ANS-100
title: The review server is searched by words and by path
date: 2026-08-24
status: open
coveredBy:
  - GerritTest::aPathIsMatchedAsItselfRatherThanAsAPattern
  - GerritTest::aSearchAsksForNothingBeyondThePatchSetEachHitStandsAt
  - GerritTest::aSearchWithNoWordsAndNoPathAsksTheServerNothing
  - GerritTest::aWordIsAValueRatherThanSyntax
  - GerritTest::anEmptySearchSaysWhatItCannotSeparate
  - GerritTest::theWordsAndThePathBecomeOneQueryTheCallerCanRerun
---

# D-ANS-100 — The review server is searched by words and by path

**`typo3_gerrit_lookup` takes a word query and a path beside its two handles. A
session asks before a patch which changes touch a file, and no number answers
that.**

`D-ANS-033` built the search over commit messages and `D-ANS-038` the word
search on the tracker. Neither covers the review surface itself: which changes
stand open on a file, and whether anybody ever tried a fix.

## Evidence

- The re-run is a read rather than a call, because the query that produced
  `feedback/2026-08-24-110833` never reached this server.
  `GerritLookup::inputSchema()` takes `issue`, `change`, `messages` and `limit`,
  and its `oneOf` requires one of the first two. Nothing takes a query or a
  path.
- Measured against `review.typo3.org` on 2026-08-24, anonymously and over the
  path `Http\Fetch` already reads. `status:open file:^typo3/sysext/impexp/.*`
  answers 22 open changes, and `file:` with no status answers ABANDONED, MERGED
  and NEW alike. So the path reaches what landed as well as what is open.
- A bare term searches, and more than the subject. `AssetCollector` answers ten
  changes, headed by the deprecation 95040, among them 94979, whose subject does
  not carry the word. `writePagesOrder` and `flatInversePageTree` answer none at
  all, which is the negative that says nobody has attempted the fix.
- The session that reported it paid three calls for those, by hand and to an
  endpoint it composed itself. Its checkout could answer none of them: a core
  clone carries what landed and says nothing about what is open.
- One of the 22 is that session's own patch, 95393. The surface this search
  answers is the one a triage tries to see.
- `D-ANS-038` built the same second way into `typo3_forge_lookup`, and its third
  **Wrong if** names this tool as where the split would show first.
- `D-ANS-033` assumes `message:` matches what a caller means. It does where the
  caller holds an issue number, and a triage that starts from a bug report and a
  file has none yet.

## Decided

- Built, and as a third way into `typo3_gerrit_lookup` rather than as a tool of
  its own. One subject, one verb, and the answer is the list of changes the
  issue search already returns.
- Two arguments this server composes into the query, rather than a Gerrit query
  passed through. Those are words over the commit message and a path a change
  touches. The operators, the anchors and the escapes stay here, and `query` in
  the answer says what they produced.
- Every state, with open as a narrower set. "Has anybody ever tried this" needs
  the abandoned and the merged ones. "Who is working on this file now" is the
  same search with `status:open`.
- The boundary per hit is the issue search's: number, Change-Id, subject,
  status, branch, patch set, commit and URL. The review, the comments, the chain
  and the issues are what a read of one change answers, which is where a hit
  goes back into.
- Nothing ranks. `impexp translation` answers ten changes of which one is about
  both words. So the order is Gerrit's own. A caller with a narrow set in hand
  asks again in other words rather than concludes that nothing exists.

## Assumed

- That the anonymous search index stays as the measurement found it. It is what
  the project's own web UI searches with, and a change to it arrives as a
  smaller answer rather than as an error.
- That a path is what the caller has. It is what a checkout hands it, and a
  subsystem nobody can spell as a path is a word query instead.

## Wrong if

- The three ways into the tool are no longer one question. That is `D-ANS-038`'s
  third **Wrong if** here. A search that grows its own filters and its own
  answer shape is what would revoke this entry.
- A caller reads an empty path search as "nobody works on this file". An
  anonymous read cannot see a private or work-in-progress change, which
  `D-ANS-033` states for the issue direction. So this one owes the caveat too,
  and `indistinguishable` is where it goes.
- The full-text match turns out not to reach the diff. It reaches the commit
  message body, which 94979 shows. Nothing measured whether it reaches the
  changed lines, and a caller who reads a zero as "this identifier appears
  nowhere" needs it to.
- The path search answers a sweep more often than a change about the file. Two
  of the 22 measured are one: a link adjustment across every manual, and a
  tree-wide PHP simplification. A list where most entries touch the path by the
  way is a count that says nothing.

## Since then

Built on 2026-08-24, with a measurement of what the composition costs. A hit at
this boundary is 1.6 KB, and the commit message left out, which nothing on this
path reads, saves 0.9 KB a hit.

The form the two arguments come to rests on a measurement rather than an
assumption. Each part of it is a query that fails without a failure. The match
takes a path whole, so the path itself and what is under it are two
alternatives. The second carries no anchor because that character is the
server's marker for a regex rather than part of one. The value stands in quotes,
because without them the alternation belongs to the server's own parser and
silently answers nothing.
