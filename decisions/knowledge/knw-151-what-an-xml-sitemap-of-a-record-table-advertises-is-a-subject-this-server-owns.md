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
it never checks. `additionalWhere` writes the row selection a second time.**

An audit found 3101 advertised addresses that answer with a page that renders no
record, and nothing raised anything.

## Evidence

- `feedback/2026-09-03-235548`. A crawl of the sitemap and a read of the bodies
  found the defect, not any lookup. `record-routing` and `record-page-title`
  stop before the sitemap and no hint named the provider.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `RecordsXmlSitemapDataProvider::defineUrl()` takes `url.pageId` once per row
  and falls back to the page the request for the sitemap hit. Nothing compares
  it against what that page renders.
- The row selection is `pid`, `recursive` and `additionalWhere`, and the last
  defaults to nothing. So the filter the plugin applies on the page stands a
  second time here, in another language, with nothing that holds the two
  together.
- The feedback's claim about restrictions does not hold as stated. The query
  builder carries `DefaultRestrictionContainer` — deleted, hidden, start and end
  time — and the provider adds `WorkspaceRestriction` on top. What is absent is
  the frontend group restriction, and there is no translation overlay. The query
  constrains the language field to `-1` and the current language.
- A page that gets an argument it has no plugin for renders its own content and
  answers HTTP 200. That is why a green crawl says nothing. The check is one
  request per sample address read for the record rather than for the status.

## Decided

- Step 1a, and a hint of its own, `record-xml-sitemap`, rather than statements
  on `record-routing`. The route to a record and its advertisement are two
  configurations with two selections, and the drift between them is the subject.
- `record-routing` closes by naming it and what it prevents, bound `since: 14`
  like the rest of that hint.
- Nothing about the sitemap hint carries a bound. The provider, its keys and its
  restrictions are the same on all four covered branches.
- The restriction statement says what applies and then what does not, and
  refutes the read both reports made. `ConnectionPool` hands out a builder with
  `DefaultRestrictionContainer`, and `getRestrictions()->add()` adds to it. Two
  sessions took that one line for the whole restriction set.

## Assumed

- That `url.pageId` is how a project configures this. It is what the report
  used. The alternative, to leave it out so the sitemap's own page serves, is
  the same defect with a page nobody chose.

## Wrong if

- A later major verifies the address against the page, or resolves the detail
  page per row. That would make the first statement wrong rather than stale.
- A session reports that the query applies the frontend group restriction after
  all. That is the one half of the query this reads off the container rather
  than off the provider.

## Since then

2026-09-04, from `feedback/2026-09-03-235315`, the second session of the same
day on the same subject. It carries five facts beside the trap this entry came
from, all read against `.checkouts/13.4`, `14.3` and `main`. The sitemap arrives
with the site set `typo3/seo-sitemap` rather than with the extension, and that
set brings the routing from 14. The core reads `url.pageId` with `??`, so a
setting with a default of 0 counts as page zero. The configuration is a plain
array with no `stdWrap` anywhere, and the core builds a provider with positional
arguments.
