---
id: D-ANS-090
title: 'A matched set larger than a page is answered by its shape'
date: 2026-08-19
status: open
coveredBy:
  - ForgeTest::aBreakdownCountsTheWholeSet
  - ForgeTest::aBreakdownSaysWhereTheBoundCutTheRead
  - ForgeTest::aUnionIsTwoReadsMergedAndCountedWithoutTheIssuesBothCarry
  - ForgeTest::theAreasComeBackOnlyWhereAWordOfTheCallersNeedsCorrecting
  - ForgeTest::theCountedReadPagesUntilTheWholeMatchedSetIsRead
  - ForgeTest::theLargestBucketsAreAnsweredAndTheTailIsCounted
---

# D-ANS-090 — A matched set larger than a page is answered by its shape

**A matched set larger than a page gets its shape, and a person gets both sides
at once. The area list comes only where a word needs a correction.**

`typo3_forge_lookup` takes `breakdown` for the first and `involving` for the
second, and answers `categories` only on the call it corrects something on.

All three came out of one session on the day the person filters shipped. All
three are about a set rather than about a name. That is how big it is, which two
questions it is, and what an answer carries that nobody asked for.

## Evidence

- `feedback/archive/2026-08-19-134651`: `reportedBy` with `limit=50` answered
  `total=621` and 50 rows, and rows 51 to 621 are reachable by nothing. The
  session reported the count, listed the 4 open ones, and handed the user a
  forge.typo3.org URL. The answer to the literal request came from the web UI.
  Its own account is that what it needed was the shape of the set and not 621
  rows in its context.
- The tool's defence of the cap holds for a topic and not for a person. That
  defence is that other words answer a set a caller would have to page through.
  There are no other words: `tracker`, `category` and a date each answer a
  smaller question than the one asked.
- `feedback/archive/2026-08-19-134706`: "issues von Frank Nägler" is both sides
  at once, and the tracker ANDs its filters, so the pair answers issues somebody
  filed *and* holds. The session made two calls and merged them by hand. #89326
  is in one set and not the other, and neither answer says the other set exists.
- `feedback/archive/2026-08-19-134717`: three calls, none with `category`, each
  answered with all 54 area names. That is roughly 600 tokens three times, more
  payload than the four issues under report.
- Measured on 2026-08-19. The 621 issues one person had filed sit in 5 statuses,
  5 trackers, 38 areas and 19 years. The four largest areas hold two thirds of
  them. Reading them takes 7 requests and 4.3 seconds; the union of both sides
  is 764 and takes 13.
- Redmine answers no counts for a `group_by` over its JSON API, measured the
  same day. So the tool reads a shape rather than asks for one.
- The other vocabularies this server echoes already answer only where they do
  work, which the feedback asked for as a check. `typo3_schema_lookup` carries
  its table index on the call that named no table and on a name it does not
  have. `typo3_changelog_lookup` fills its tag list only where the caller passed
  a tag. The area list was the one that did not.

## Decided

- **`breakdown` answers the counts and no rows.** The session said the 50 rows
  were the one thing it did not need, and the rows are the expensive half of the
  answer. What it costs here is a read per hundred issues, which is the trade
  the caller cannot make on its side at all.
- **The dimensions are status, tracker, area and year filed.** The four the
  feedback named, which are the terms of "what has this person worked on".
- **The read stops at ten pages and says where the bound cut it.** Ten requests
  is about seven seconds; past that a caller waits on a shape it should narrow
  instead. `complete: false` and the text both say the counts are of one end of
  the set. Proportions read off the oldest thousand of three are a wrong answer
  with a right shape.
- **Twelve buckets per dimension, and a count of what stays out.** The tail of
  an area count is twenty subsystems with one issue each. An issue filed under
  no area is a bucket named `none` rather than a row left out. So the buckets
  add up to what the read covered.
- **No `offset`.** It was the feedback's own second choice and it says why. 621
  rows through a context is the wrong trade, and a dozen calls to get there is
  worse. Paging stays the thing this tool does not do.
- **`involving` is a third argument rather than a mode on the pair.** The pair
  answers two different questions and the description is right that they do. The
  gap was the broad question, which is the one a user says out loud. It takes
  the place of the two rather than combines with them.
- **A union is two reads, merged, and counted by a third.** The first `limit` of
  each read and then the first `limit` of what they merge to is the first
  `limit` of the union. Both come back in the same order. The count is the two
  totals less the issues both carry, which is one more read of one row. A caller
  with 621 and 588 in hand cannot tell what that is together.
- **The area list comes back only where a category word resolved to none or to
  several.** That is the call it does work on. Everywhere else it is vocabulary
  the caller did not ask for, and `typo3_server_scope` is where a caller that
  wants it without a question reads it.
- **The page sentence points at `breakdown` where a person is in the filters.**
  Telling a caller to narrow by tracker or date is the advice that sent the last
  one to the web UI.
- **`people` loses nothing.** `D-ANS-089`'s **Confirmed on** records that an
  empty `candidates` is what let a session state its result plainly. So the
  block stays whole on every answer that resolves a name.

## Assumed

- That a shape is what most callers want from a large set. One session says so
  about its own task, after a trial of the other way first.
- That ten pages is the right bound. It is a wait nobody has complained about
  yet, and what is on the other side of it is a caller who should narrow.
- That a union of more than two sides is nobody's question. A watcher, a
  commenter and a reviewer are all on a Redmine issue, and nobody asked for any
  of them.

## Wrong if

- A session asks for a breakdown and then asks for the rows anyway, twice. Then
  the two are one answer and the counts belong beside a page rather than instead
  of it.
- A session reads proportions off a bounded breakdown as if they were the set's.
  Then `complete: false` is not loud enough and a bounded read should refuse
  instead.
- A session passes `involving` and then needs to know which side a row came in
  on. The row's own `reportedBy` and `assignedTo` do not tell it. Then the union
  owes each row the reason it is in the set.
- A caller passes no `category`, needs the area names, and has to call
  `typo3_server_scope` for them. Then the echo did work nobody had measured.
