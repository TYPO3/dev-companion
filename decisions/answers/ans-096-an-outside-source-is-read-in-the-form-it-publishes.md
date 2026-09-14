---
id: D-ANS-096
title: An outside source is read in the form it publishes
date: 2026-08-23
status: confirmed
coveredBy: []
---

# D-ANS-096 — An outside source is read in the form it publishes

**A host that publishes an API goes through `Fetch::decode()` and nothing else.
The manual, which publishes none, comes out of its rendered pages.**

`D-ANS-034` said the lookups that reach a host read JSON and nothing else. That
described `Contribution/`, and it was already untrue of the fourth source on its
date.

## Evidence

- Three of the four sources that reach a host publish JSON: the tracker
  (`Contribution\Forge`), the review server (`Contribution\Gerrit`) and the
  registry (`Publication\Ter`). All three go through `Http\Fetch::decode()`. It
  takes the XSSI guard off where there is one, decodes, and answers null for
  anything that is not an array.
- The fourth publishes none. `docs.typo3.org` serves rendered Sphinx pages, and
  what it publishes beside them is `objects.inv` — the inventory `D-ANS-065`
  made the index. `Manual\Documentation` reads the bodies with `DOMXPath` and
  has since `7d29c77a` on 2026-07-30, three days before `D-ANS-034`.
- What a reader is for is what a wrong guess costs. The tracker's protection
  answers 200 with a 7.5 kB HTML challenge page, measured on 2026-08-03.
  `decode()` turns that into "the question was not answered" instead of into an
  answer.
- The answer shape does not divide the four. `status` is `answered`, `empty` or
  `unavailable` on all of them and `Result\Unreachable` carries the causes,
  which is `D-ANS-007`'s and stays as it is here.

## Decided

- `Fetch::decode()` is the one JSON reader and every source with an API asks it.
  A parser exists only where the source publishes nothing else, which today is
  the manual and nothing beside it.
- A new source counts by what it publishes rather than by how much a caller
  wants it. Where the answer would have to come out of a page nobody maintains
  as a document, the recipe belongs in `knowledge/`. The read stays with the
  caller. That is what `D-ANS-034` decided and this keeps.
- `coveredBy: []`. What a test can hold is each reader, and `DocumentationTest`
  and `ForgeTest` do. Which reader a source gets is a judgement made once per
  source, and no failure can catch a wrong one.

## Assumed

- That a rendered manual stays readable. One toolchain builds the four books,
  and the reader takes the article and the inventory rather than a theme's
  navigation. That is the markup that did move, and `D-ANS-065` measured it.

## Wrong if

- `docs.typo3.org` changes what it renders and the reader answers with half a
  page, or with a navigation tree, rather than with nothing. `DocumentationTest`
  reads fixtures rather than the site, so the first report of that would be a
  session's.
- A second source without an API turns out to be worth a parser. Two make "the
  manual" an exception list rather than an exception. The rule then has to say
  what makes a page readable instead of name one host.

## Confirmed on 2026-08-28

A read of both **Wrong if** shows neither has happened. The manual still renders
what the reader takes. Asked at a covered version, the answer carries six pages
each with an excerpt of the article's own prose rather than a navigation tree.
That is the first watch for a theme that moves under the reader, and the read is
a live one, which the test is not. No second source without an API has arrived.
