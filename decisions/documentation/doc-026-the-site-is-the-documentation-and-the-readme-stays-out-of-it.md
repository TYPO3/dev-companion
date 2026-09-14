---
id: D-DOC-026
title: The site is the documentation, and the readme stays out of it
date: 2026-08-12
status: open
coveredBy:
  - SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex
  - SiteTest::theSiteOpensOnTheDocumentationsOwnPage
  - VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine
---

# D-DOC-026 — The site is the documentation, and the readme stays out of it

**The published site is `documentation/` and nothing besides, so its front page
is `documentation/readme.rst`. The repository's `readme.md` is the front page of
the checkout alone.**

What the site owed a visitor stood in one file that also had to work as a GitHub
front page. [`D-DOC-018`](doc-018-the-site-opens-on-the-readme.md) published
that file rather than wrote the manual page it lacked.

## Evidence

- `D-DOC-018` rejected the page below `documentation/` on one ground: it would
  put the promise paragraphs in the checkout twice. That holds for a copy and
  not for a move, and this is a move. What the readme said about the three
  sources, the trust boundary and the conventions is now on the manual's front
  page and nowhere else.
- Most of what the readme carried was already written a second time below
  `documentation/`. The resource list is `server/resources/readme.md`, the tool
  list grouped by source is the generated `server/tools/answer-sources.md`, and
  the feedback workflow is `records/readme.md`. Those copies did drift. The
  grouped tool list went stale by five tools, and the test that watched it is
  what `D-SCO-011` and `D-KNW-035` named until this change.
- The config a renderer reads is `guides.xml`, and its own convention in TYPO3
  is that it sits beside the corpus as `Documentation/guides.xml`. That was
  unavailable while the corpus was a directory plus one file above it.
- One source, one root. `Site::sources()` began with a constant and then read a
  directory, and `Site::published()` had two special cases before its rule. The
  map of `documentation/` went out under a third name nothing else in the
  checkout used.

## Decided

- Site::FRONT and Site::MAP_PAGE are gone. Every page the site serves is a file
  below `documentation/`, and `documentation/readme.rst` goes out as `index.md`
  by the rule every other directory's page already followed.
- The front page carries what the readme's first paragraphs carried, and the
  four sections below it. It is the page `AGENTS.md` now names as the promise —
  the first thing that becomes false when a capability changes.
- `readme.md` at the root keeps the title, the experimental note, the covered
  lines, the quickstart and the way into the manual. That is what somebody who
  arrives at the repository needs before they decide to read further, and it is
  the whole of the deliberate overlap.
- A link from the manual to the readme leaves the tree. `Site` rewrites it to
  the file on GitHub, like any other link out of it. Nothing about
  `Site::page()` changes for it.
- Both places name the covered lines, because both are somewhere somebody
  arrives. `VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine` holds both
  to `knowledge/versions.json`.
- The grouped tool list is not moved. `answer-sources.md` is the same statement
  generated from the `Source` enum. So the test that watched the hand-written
  one is gone rather than retargeted, and `ToolSurfaceTest` is what holds the
  surface to the registry now.

## Assumed

- That nobody has a deep link into the site. `index.html` is another page than
  it was and `how-the-work-is-done.html` is gone. This is a 0.x package whose
  surface has moved before, and `D-DOC-018` assumed the same thing eleven days
  earlier when it moved these two.
- That the readme stays short. It is the one file where somebody who does not
  know the front page carries the promise can restate it, and nothing measures
  the overlap.

## Wrong if

- The readme grows back. Two statements of what the server is, each false on its
  own day, is what `D-DOC-018` was right about. Only the direction it fixed it
  in has changed.
- Somebody who arrives on GitHub cannot tell what the server does. The front
  page is now four paragraphs and a link, and what it leaves out is everything
  the site opens with.
- The front page reads as the map it replaced. It has to answer "is this for me"
  above "how is the work done here", and the four sections come last for that
  reason.
