---
id: D-KNW-151
title: What an XML sitemap of a record table advertises is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::whatARecordsSitemapAdvertisesIsStatedAgainstWhatThePageRenders
---

# D-KNW-151 — What an XML sitemap of a record table advertises is a subject this server owns

**The corpus states that a records sitemap builds every address from one page id
it never checks, and that the row selection is written a second time in
`additionalWhere`.**

An audit found 3101 advertised addresses answering with a page that renders no
record, and nothing raised anything.

## Evidence

- `feedback/2026-09-03-235548`. The defect was found by crawling the sitemap and
  reading the bodies, not by any lookup. `record-routing` and
  `record-page-title` stop before the sitemap and no hint named the provider.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `RecordsXmlSitemapDataProvider::defineUrl()` takes `url.pageId` once per row
  and falls back to the page the sitemap was requested on. Nothing compares it
  against what that page renders.
- The row selection is `pid`, `recursive` and `additionalWhere`, and the last
  defaults to nothing. So the filter the plugin applies on the page is written a
  second time here, in another language, with nothing holding the two together.
- The feedback's claim about restrictions does not hold as stated. The query
  builder carries `DefaultRestrictionContainer` — deleted, hidden, start and end
  time — and the provider adds `WorkspaceRestriction` on top. What is absent is
  the frontend group restriction, and there is no translation overlay: the
  language field is constrained to `-1` and the current language.
- A page handed an argument it has no plugin for renders its own content and
  answers HTTP 200, which is why a green crawl says nothing. The check is one
  request per sample address read for the record rather than for the status.

## Decided

- Step 1a, and a hint of its own — `record-xml-sitemap` — rather than statements
  on `record-routing`: reaching a record and advertising it are two
  configurations with two selections, and the drift between them is the subject.
- `record-routing` closes by naming it and what it prevents, bound `since: 14`
  like the rest of that hint.
- Nothing about the sitemap hint is bound. The provider, its keys and its
  restrictions are the same on all four covered branches.
- The restriction statement says what is applied and then what is not, and
  refutes the reading both reports made: `ConnectionPool` hands out a builder
  carrying `DefaultRestrictionContainer`, and `getRestrictions()->add()` adds to
  it. Two sessions took that one line for the whole restriction set.

## Assumed

- That `url.pageId` is how a project configures this. It is what the report
  used, and the alternative — leaving it out so the sitemap's own page is used —
  is the same defect with a page nobody chose.

## Wrong if

- A later major verifies the address against the page, or resolves the detail
  page per row, which would make the first statement wrong rather than stale.
- A session reports the frontend group restriction being applied after all,
  which is the one half of the query this reads off the container rather than
  off the provider.

## Since then

2026-09-04, from `feedback/2026-09-03-235315`, the second session of the same
day on the same subject. It carries five facts beside the trap this entry was
written from, all read against `.checkouts/13.4`, `14.3` and `main`: the sitemap
arrives with the site set `typo3/seo-sitemap` rather than with the extension,
that set brings the routing from 14, `url.pageId` is read with `??` so a setting
defaulting to 0 is taken as page zero, the configuration is a plain array with
no `stdWrap` anywhere, and a provider is built with positional arguments.
