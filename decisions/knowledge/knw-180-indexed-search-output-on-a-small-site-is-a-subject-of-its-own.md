---
id: D-KNW-180
title: indexed_search output on a small site is a subject of its own
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::indexedSearchNamesThePageCountGuardItsPaginationPartialLacks
---

# D-KNW-180 — indexed_search output on a small site is a subject of its own

**The hint `indexed-search-output` states what the shipped indexed_search
templates render that a small site does not want, and which lever reaches each
part.**

## Evidence

- [`feedback/2026-10-28-060232`](../../feedback/archive/2026-10-28-060232-indexed-search-renders-its-page-browser-twice.md)
  came from the same 14.3 session as the feedback behind `D-KNW-176`. Two
  models shipped a page browser for one page of results.
- Step 1a. `bin/cli hints:probe` on the feedback's query reached
  `extbase-pagination` and `frontend-records` on text alone. No hint named the
  indexed_search Pagination partial or its guard.
- `Partials/Pagination` wraps its list in `{pagination}` alone in
  `.checkouts/13.4`, `14.3` and `main`. `SearchController` builds a pagination
  object for every result set there. `Templates/Search/Search` renders that
  partial twice in all three.
- `PaginationInterface::getLastPageNumber()` stands in all four checkouts.
- `.checkouts/12.4` has no Pagination partial. Its `PageBrowsingViewHelper`
  returns an empty string where the page count is 1. That is the `since: 13`
  boundary.
- `Partials/Searchresult` writes the `tx-indexedsearch-percent` span without a
  condition in all four checkouts. `RatingViewHelper` turns the sort order into
  the value, a percentage under `rank_flag`, the default in `setup.typoscript`.
- `settings.displayAdvancedSearchLink` guards the link in `Partials/Form` in all
  four checkouts. `D-KNW-176` already found it.

## Decided

- Closed on the spot under `D-FBK-052`. The run read the checkouts and holds the
  evidence, and no contract moves.
- A hint of its own beside `plugin-template-override`, because a session asks
  in the words of the search page rather than of the view roots. It names the
  setting where one exists and the one file to copy where none does.
- The feedback's "which a stylesheet can reach" became one statement: a class
  can be hidden, and a page count cannot be read.

## Assumed

- That a session which fits indexed_search to a small site uses words the
  `appliesTo` reaches. The probe reaches it on the feedback's query and on "hide
  the relevance percentage and the page browser".

## Wrong if

- A session ships the single-page browser after this hint was in its answer.
  Then the gap is delivery rather than knowledge.
- A core release adds the page-count guard to the partial. Then the first
  statement needs an `until`.
