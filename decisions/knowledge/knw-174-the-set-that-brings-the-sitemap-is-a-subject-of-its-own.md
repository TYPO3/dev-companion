---
id: D-KNW-174
title: The set that brings the sitemap is a subject of its own
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::theSetThatBringsTheSitemapIsFoundByASitemapOfPages
---

# D-KNW-174 — The set that brings the sitemap is a subject of its own

**`xml-sitemap` states which set brings `/sitemap.xml` and its routing.
`record-xml-sitemap` keeps the record table and names it.**

## Evidence

- [`feedback/2026-10-28-074334`](../../feedback/2026-10-28-074334-the-page-sitemap-fact-sits-in-a-record-table.md).
  A sitepackage session on 14.3 required `typo3/cms-seo` and got a 404 on
  `/sitemap.xml`. The corpus stated that case, in a hint titled for a record
  table, and the session never reached it.
- The query of the feedback, re-run with `bin/cli hints:probe` on 2026-10-28,
  reached `record-xml-sitemap` at a coverage of 0.298 and nothing else on the
  subject. After the move it reaches `xml-sitemap` first.
- The two statements are the ones `D-KNW-151` recorded, read in
  `.checkouts/13.4`, `14.3` and `main`. They move with their bounds, and no
  statement about TYPO3 is new.

## Decided

- **Step 2 of the ladder, placement.** The knowledge was here and stood under a
  subject a caller with a sitemap of pages does not look for. It moves to
  `site-sets.json`, because a set brings it.
- **Taken on: an SEO pass for `typo3-sitepackage-development`, step 1b.** The
  skill ends on a page that renders and never asks whether a search engine can
  index it. The same project filed the same shape for accessibility on the same
  day, in `feedback/2026-10-28-080312`. So a review of the finished site is a
  gap of the skill rather than one session's. It changes a skill's contract, so
  `T-261028-5102` carries it.
- **Taken on: the head of a page, step 1a.** Nothing in `knowledge/` names the
  meta description, the canonical or hreflang. What TYPO3 renders for each is a
  read of the checkouts, and the same todo carries it.

## Assumed

- That the session would have asked for a sitemap of pages in words the new
  hint carries.

## Wrong if

- A session that wants a sitemap of pages still reaches only
  `record-xml-sitemap`, or neither.
