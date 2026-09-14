---
id: D-ANS-069
title: A backlog row carries the review server and not the journal
date: 2026-08-08
status: open
coveredBy:
  - ForgeTest::aRowCarriesWhatTheOneCallAlreadyAnsweredAboutIt
  - ForgeTest::aRowSaysWhetherTheReviewServerHoldsAChangeThatNamesIt
  - ForgeTest::aRowSaysWhichStateEachOfItsChangesIsIn
  - ForgeTest::theFirstReadingNamesWhatTheRowLeavesToACall
  - GerritTest::aPageOfIssuesIsOneQueryAndEachHitLandsOnTheIssueItNames
---

# D-ANS-069 — A backlog row carries the review server and not the journal

**A backlog row says whether the review server holds a change, and the comment
count stays behind one read per issue.**

The session that reported it narrowed thirty rows to one with a read of nine
issues whole. Three of the four it decided cheaply rested on signals no row
carried. The measurement below disproves its own account of why that was
affordable. The two signals it called free are in the journal, and the
enumeration answer has no journal in it.

## Evidence

- The index endpoint answers no journal. `feedback/2026-08-08-224333` proposes a
  row that carries two things "without a second call". Those are "whether the
  issue has any change on the review server" and "how many human comments it
  has". Both come off `reviews` and `noteCount`, which `Forge::issueOf()`
  derives from the journal. Measured against forge.typo3.org on 2026-08-08:
  `projects/typo3cms-core/issues.json?…&include=journals,relations,attachments`
  answers rows with no `journals` key, and so does the bulk form
  `issues.json?issue_id=82228,83913,81102&status_id=*&include=journals`. The
  comment count is one issue read per row, which on that session's own set is
  36.
- What the same call does answer for free drops out today. The rows carry
  `relations`, `attachments`, `description` and the project's custom fields, and
  `Forge::entry()` keeps none of the four. Over the feedback's own query — open
  Bugs untouched since 2019-01-01, 36 of them — 19 rows carry a relation and 6
  carry a file.
- The tracker's own settleability field is empty where a caller would use it. Of
  those 36, `Complexity` is empty on 31 and reads `hard`, `nightmare` or
  `medium` on the other 5. `Sprint Focus` is empty on 35.
- The review server answers a whole page in a handful of calls. The same 36
  numbers, batched twelve to a query as `message:<issue> OR …` with
  `o=CURRENT_COMMIT`. Held against the commit message the way `Gerrit::names()`
  already holds a single-issue hit. That is 3 calls, 36 changes returned, 7 rows
  with a change that really names them. One of the 7 is `#82228 → change 53819`,
  the abandoned change the session spent a `git fetch` of the review server to
  rule out.
- The description is the wrong thing to widen a row with. Median 902 characters
  over those 36, 38.5 kB for the page.

## Decided

- The enumeration answer carries `relations`, `attachments` and whether the
  review server holds a change. The first two cost nothing beyond the call
  already made. The third is one further call per page. That is the same trade
  `D-ANS-064` made for a relation's subject and paid once rather than per row.
- The comment count is not carried. It is one read per row on a host behind bot
  protection, which is the cost the enumeration exists to take off a caller.
- The description stays out. A caller reads a page of thirty rows to choose
  from, and 38.5 kB of report text is the read it was meant to replace.
- `Complexity` is not the answer either, and that is worth one statement. It is
  the field a caller would ask this question of, and it is empty on exactly the
  backlog where the question arises.
- What makes a candidate settleable is the skill's to say, and it is research
  rather than a read of this repository. The row carries signals; the criterion
  that reads them is about how core work actually goes, which this judgement has
  established nothing about.

## Assumed

- Gerrit's `message:` index answers an alternation at page width. The
  measurement covered three queries of twelve. The URL bounds it rather than a
  documented limit, and a lower limit would make this one call per few rows
  instead of per page.
- A batched hit means the issue on the same rule a single hit does. The
  commit-message filter `changesForIssue` already applies carries over, and
  nothing else about the batched form has a check against a false positive.
- One session, one task shape. The nine issue reads it counted are its own, and
  the criterion it derived is one reader's.

## Wrong if

- A triage reads a row's `reviewed` flag as the verdict and stops. That would
  say the flag needs the same sentence the issue answer carries: a note says
  what was true on its date.
- The batched query answers a change for a row whose commit message never names
  it, which would say the filter does not survive the alternation.
- Sessions still read candidates whole once the rows carry the signals, which
  would say the read was never about what the rows lacked.

## Since then

Built on 2026-08-09, and the relations cost a call after all. Amended on
2026-08-25, because the enumeration drops a status it already has in hand. The
sentence above covers a field that costs a call rather than this one. What does
not move is the verdict: the session says an inline comment decided it, and that
is one call per change.

Read back on 2026-08-27, the third **Wrong if** did not fire and the first did.
A report calls an abandoned change most of what makes an old issue worth a
pick-up. So the row names the change and the state it stands in.
