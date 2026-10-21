---
id: D-ANS-180
title: A backlog larger than a page is read by topic or by page
date: 2026-10-21
status: open
coveredBy:
  - ForgeTest::anOffsetReadsThePageAfterTheOnesBeforeIt
  - ForgeTest::wordsNarrowTheWholeSetToTheIssuesAboutThem
---

# D-ANS-180 — A backlog larger than a page is read by topic or by page

**A Forge backlog takes `words` for a topic inside the set and `offset` for the
page after 50. `words` matches the subject and the description. `breakdown`
counts a fifth dimension, the year an issue was last touched.**

## Evidence

- [`feedback/2026-10-21-154706-backlog-cannot-return`](../../feedback/archive/2026-10-21-154706-backlog-cannot-return-a-set-over-50-rows-or.md).
  A triage of stale Extbase issues about translation. `breakdown` reported 110
  issues over two areas. The topic sat inside the plain Extbase area too, so the
  session had to read all 110. `backlog` stopped at 50 rows, and `query` takes
  no category. The session went to the tracker with curl, two pages and a regex
  of six stems, and lost the cites and the review states each row carries.
- The maintainer chose both ways in on 2026-10-21, when asked between `words`,
  `offset` and both.
- Re-run on 2026-10-21 with the new arguments. `category="extbase"` with
  `words="translat locali l10n overlay fallback"` matched 29 of the 110, in one
  call.

## Decided

- `words` matches any of its words at the start of a word, so a stem reaches
  its forms. The words of a topic are its synonyms, which is why it is any
  rather than every. The tool reads the whole set for it, up to the thousand
  `breakdown` already reads, and `wordsScan` says how many and whether that was
  all.
- `offset` goes to the tracker on one read. A union of two sides has no order
  the tracker can page, so a later page of `involving` reads the whole set and
  cuts it here.
- The rows of a whole-set read come back without relations and files. The
  page that is handed on is read again with them, one request for the page.
- `touched` beside `year` in `breakdown`. It says which `updatedBefore` makes
  the set fit a page, which the session could not know in advance.
- No threshold for stale in the answer. `stale` is an order, and
  `updatedBefore` is the threshold a caller sets.

## Assumed

- That a topic is a few words a reporter would write. A reporter who wrote
  about the subject in other words is outside the match, as with `query`.
- That a thousand issues bound every area worth a topic triage. The largest
  area is far below that on the open set.

## Wrong if

- A session passes `words` and reads `total` as the set of issues about the
  topic, while the match left out reports worded otherwise.
- A session pages with `offset` through a set it could have narrowed with
  `words`, and pays a context per page for rows it then filters itself.
