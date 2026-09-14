---
id: D-KNW-124
title: Frontend render pipeline state is a gap this server owns
date: 2026-08-25
status: open
coveredBy:
  - HintsTest::theFixtureThatCanAssertNoHeadOutputIsNamed
  - HintsTest::theStateAFrontendRequestLeavesInTheRendererIsAnswered
  - HintsTest::whatBecomesOfTheRenderedBodyIsStatedPerMajor
---

# D-KNW-124 — Frontend render pipeline state is a gap this server owns

**Where the frontend request pipeline fills and empties `PageRenderer`, and what
`reset()` leaves in place, is inside this server's boundary and absent from
it.**

The corpus answers what a page template receives and how to prove a rendering,
and nothing about the state the renderer holds between the two. A session that
reviews a change to the pipeline itself gets the middleware conventions, which
do not bear on the diff.

## Evidence

- Re-run on 2026-08-25 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own questions matches nothing and returns the whole index.
  Those are "when is PageRenderer bodyContent populated and cleared during a
  frontend request" and "does config.disableAllHeaderCode skip PageRenderer head
  assembly". The third, "which PageRenderer properties does reset() not reset",
  reaches `persistence-reading` on text alone.
- The vocabulary is absent. `bodyContent`, `renderPageWithUncachedObjects`,
  `disableAllHeaderCode`, `metaTagRegistry`, `jsLibs` and `JsonRenderer` occur
  nowhere below `knowledge/` or `skills/`.
- The path-scoped call is the one that missed. Probed with
  `typo3/sysext/core/Classes/Page/PageRenderer.php` and
  `typo3/sysext/frontend/Classes/Http/RequestHandler.php`, the first hit is
  `system-extension-boundaries`, whose `appliesTo` is `typo3/sysext/` — the hint
  that applies to every core file. `routing-request-handling` follows it on
  `/Http/`, and it states PSR-7, middleware registration and request scope.
- The neighbours are each about something else. `frontend-page-rendering`
  answers which template renders the page, `page-cache-flushing` which cache
  serves a stale one, and `proving-a-rendering` how to build a throwaway
  functional test.
- `knowledge/server-scope.json` puts this on the answer side. Its `doesNotCover`
  entry for PHP source as code excludes a signature and an `@internal`
  annotation and says what the server answers instead. "typo3_hint_lookup says
  what the subsystem is built to."
- The session read it out of the checkout instead, in ten methods across
  `RequestHandler.php` and `PageRenderer.php`, and it cost about a third of the
  session. What it found changed the commit message of the patch under review.

## Decided

- Built, as a hint of its own rather than more sentences on
  `frontend-page-rendering`. One hint is one question (`D-KNW-030`). That one
  answers which template renders a page, this one what the request does to the
  renderer while it runs. Different paths reach them: `Classes/Page/`,
  `frontend/Classes/Http/` and the callers of both, against `PAGEVIEW` and a
  backend layout.
- The boundary is the state the renderer holds across the phases of one request.
  Which phase fills `bodyContent`, and that `renderPageWithUncachedObjects()`
  empties it and on purpose does not `reset()`. That the uncached content
  objects run with the rest of the state still full, and what `reset()` clears
  against what it leaves in place. How to prove a rendering stays with
  `proving-a-rendering`, and how an asset reaches a page with
  `how-an-asset-reaches-a-page`.
- Bound per major rather than written flat, because the reading already shows a
  boundary the feedback did not. Read on all four covered lines: 13.4 clears
  `bodyContent` in `reset()` alone; 14.3 and `main` clear it in
  `renderPageWithUncachedObjects()` as well, at `PageRenderer.php:1068` and
  `:1000`. So the symptom the reported patch names is gone on 14 and up and
  stands on 13.4.
- `reset()` on `main` clears `bodyContent`, `jsFiles`, `jsInline`, `jsLibs`,
  `cssFiles`, `cssInline`, `inlineComments`, `headerData`, `footerData` and the
  JavaScript renderer. It touches neither `cssLibs` nor the meta tag registry.
  The asymmetry is real and the enumeration per major is the reading.
- The test fixture trap goes to `core-tests`, where the same silence already
  cost a session (`D-KNW-070`). What sets `disableAllHeaderCode = 1` is
  `EXT:core/Tests/Functional/Fixtures/Frontend/JsonRenderer.typoscript`, the
  shared base. It is not the `SiteHandling/Fixtures/JsonRenderer.typoscript`
  layered on it that the feedback names. So no test that loads that base can
  fail on anything the renderer assembles into `<head>`.

## Assumed

- That a review session reaches this by path. The call that missed was
  path-scoped, so the `appliesTo` paths decide whether the hint arrives at all.
- That the phase order is one hint rather than a document. It is a mechanism a
  reviewer holds during a read of a diff, not a procedure step by step. The
  documents below `knowledge/documents/` are for the latter.
- That the 12.4 shape is worth a statement. `reset()` never touches the property
  there, so the question a caller on that line asks has another answer rather
  than none.

## Wrong if

- A session with the hint installed still goes to the checkout for the same
  facts, which would say the gap was routing rather than knowledge.
- The enumeration turns out to differ on every covered line, so the hint is a
  table of versions rather than statements a reviewer can hold.
- `proving-a-rendering` turns out to be where sessions arrive anyway, which
  would make this a section of that document rather than a hint. Its "Printing
  What a Service Holds Mid-Request" section already carries one sentence of the
  lifecycle. That is the caveat that a `bodyContent` of length 0 is the probe at
  the wrong moment.

## Since then

The hint stands and the fixture trap stands beside it. The work corrected one
read above. The oldest major does have the property, and what is absent there is
its line in the reset. So on that line every render starts from what the one
before it left.

Two further findings the entry did not have, both unbound. The reset also
restores three more things and leaves one in place. What carries the serialised
state across the uncached pass moved class between the majors. The **Wrong if**
stay as they are, since they are a forward run's.
