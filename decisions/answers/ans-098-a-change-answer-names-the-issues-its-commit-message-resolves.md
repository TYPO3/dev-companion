---
id: D-ANS-098
title: 'A change answer names the issues its commit message resolves'
date: 2026-08-24
status: open
coveredBy:
  - GerritTest::aChangeCarriesTheIssuesItsCommitMessageNames
  - GerritTest::aTrailerWithNoNumberBehindItNamesNoIssue
  - GerritTest::anIssueSearchAsksForNoneOfTheReview
  - GerritTest::theCommitMessageIsReadByBothFormsAndHandedBackByAChangeReadByName
  - GerritTest::theTextHalfNamesTheIssuesAndWhatEachTrailerClaims
---

# D-ANS-098 — A change answer names the issues its commit message resolves

**`typo3_gerrit_lookup` answers a change with the Forge issues its commit
message names. Each carries the subject, tracker and status `R-ANS-029` asks of
any record an answer names.**

The message is what joins a patch to the tracker, and the answer carries
neither. A session that walks from a change to the issue behind it reads the
subject and finds one issue number in it or none. It has no way to see that the
message names a second.

## Evidence

- `feedback/2026-08-24-100458` reports a four-call walk, change 95375 to change
  95015 to issue #107080 to abandoned change 90176. It ended on a maintainer's
  stated reason not to do what the session was under pressure to do. It says the
  whole chain hung on a second `Resolves:` line it nearly missed. It asks for a
  list of the issues a commit message names, the way the relation chain stands.
- The walk was shorter than the session took it. Change 95375, the patch under
  review, carries `Resolves: #110493`, `Related: #110331` and `Related: #107080`
  in its own commit message, measured on 2026-08-24. The first call named the
  issue that ended the walk, and the session reached it through two further
  lookups.
- Re-run on 2026-08-24. `change: "95015"` answers subject, status, branch, patch
  set 4, commit `db06bbcc89f`, the fetch ref, both labels, comments and chain.
  Its seventeen data fields carry no commit message and no issue number. Neither
  `Resolves:` line is in any answer this tool gives, so the report's own account
  of where it read them cannot be right.
- The review log is not the other way to it. `messages` on 95015 carries no line
  that names an issue, so a request for the 20.7 KB log answers a different
  question.
- One option on the query the tool already makes. `Gerrit::change()` asks
  `o=CURRENT_REVISION`, `o=DETAILED_LABELS` and `o=DETAILED_ACCOUNTS`.
  `o=CURRENT_COMMIT` is what adds the message, and `Gerrit::changesForIssue()`
  already passes it, reads the message in `Gerrit::names()` and unsets it before
  it answers.
- What it costs, measured against review.typo3.org on 2026-08-24 on that same
  query. 95015 11.7 KB to 13.1 KB, 95375 15.9 KB to 17.5 KB, 91563 6.7 KB to 7.5
  KB, 90176 5.7 KB to 6.3 KB. No second endpoint and no second round trip.
- The tracker side is one call for the whole set. `Forge::issuesOf()` reads
  `/issues.json?issue_id=…&status_id=*`, which answered #110493, #110331 and
  #107080 with tracker, status and subject in 0.25 seconds and 5.4 KB on
  2026-08-24. That is the read `R-ANS-029` rests on for the relations.
- A trailer without a number is in the wild. The current patch set of 91563
  carries the line `Resolves: #` with nothing after it.
- The skill covers the other case only. `typo3-core-patch-review` has the
  reviewer read `Resolves:` and `Change-Id:` off the commit message in front of
  it. That is right where the patch is in a checkout. 95015 and 90176 were never
  in one, and there the review server is the only source.
- Nothing in the corpus says it.
  `bin/cli hints:probe "gerrit change commit message resolves issue numbers"`
  matched nothing on 2026-08-24.
- The same failure is on record one source over. `R-ANS-029` came out of
  `feedback/2026-08-07-231225`, where four relations arrived as bare numbers.
  The session skipped the one that answered the task for the same reason as the
  three that did not. This is that failure with no number at all.

## Decided

- Step 2, delivery, in the sense `D-ANS-068` gave it. The answer side of the
  tool the session did call is the lever. The fact is one flag on a query this
  tool already makes. Not step 1b — unlike the chain `D-ANS-094` took on, no
  endpoint here goes unreached.
- Queued rather than taken on. The tool, the query and the parser all exist, and
  what changes is a field on an answer. But it touches `src/` and the declared
  `outputSchema`, which get a review rather than an improvisation.
- Priority `normal`, which is what takes the card off the `low` it arrived at.
  Three core skills route a change handle here, and the failure is the one
  `D-ANS-094` named. The answer is not short, it is silently complete, and a
  session that does not know a second issue exists has nothing to notice by.
- The issues, not the message. What the walk needs is the handle, and a commit
  message in the answer is prose the caller can read on the review page anyway.
- `Resolves:` and `Related:` are told apart, because they are different claims:
  what the patch closes, and what it touches.
- Each issue carries the subject, tracker and status `R-ANS-029` demands, filled
  by the one bulk read that already fills a relation. A bare number is the
  failure that requirement exists against, and an answer here with numbers alone
  would write it a second time.
- Every change the answer carries, siblings included — the loop that already
  fills `comments` and `chain`.
- The `change` form alone. An issue search already knows the issue, and it reads
  the message there to drop the false positives the index answers with.
- A trailer that names no number names no issue. The empty one drops out rather
  than comes back as an issue nobody can look up.
- The field's name, how the text half prints it, and which seam reaches the bulk
  read belong to the work.

## Assumed

- That the trailers are where the tracker link is. Both walks on record read
  `Resolves:` and `Related:`, and an issue named in a sentence of the message is
  not counted here.
- That the second host is worth its call on the change path. It is 0.25 seconds
  measured once, against a lookup that already pays for comments and chain.
- That the issues named is enough. Nothing yet shows a session that acts on an
  issue number this server put in front of it. That is `D-ANS-068`'s own
  assumption about a named document.

## Wrong if

- A change lookup answers issues nobody named. That would say the trailer read
  went too wide: a number in a URL or in a quoted log line.
- The tracker read fails often enough that the issues come back as numbers
  anyway, which would put this back where `R-ANS-029` started.
- A session reads the issues in the answer and calls `typo3_forge_lookup` on
  each of them regardless, which would say the filled fields buy nothing.
- The change answer grows past what a review session can read, so that the
  issues need a request rather than come with the answer.
- A walk still costs four calls because what it needed was the other direction,
  which changes name this issue. That is `typo3_forge_lookup`'s `reviews` rather
  than this field.

The third **Wrong if** has its answer from the other side. A session called
`typo3_forge_lookup` on an issue this field had already filled. It reports that
it added almost nothing, and names it as the one call of that session it would
not make again. So the filled fields made the second call redundant rather than
bought nothing.
