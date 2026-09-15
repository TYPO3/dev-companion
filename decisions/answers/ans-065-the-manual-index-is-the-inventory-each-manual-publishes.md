---
id: D-ANS-065
title: The manual index is the inventory each manual publishes
date: 2026-08-08
status: open
coveredBy:
  - DocumentationTest::aBodyThatIsNotAnInventoryIsNotAnIndex
  - DocumentationTest::aPageIsIndexedUnderTheTitleTheInventoryStates
  - DocumentationTest::anApiIdentifierReachesThePageThatIsNotNamedAfterIt
  - DocumentationTest::theNotFoundPageIsNotOneOfTheAnswers
---

# D-ANS-065 — The manual index is the inventory each manual publishes

**`Manual\Documentation` searches the `objects.inv` Sphinx writes beside every
manual, held under its `ETag` and revalidated, rather than the links of the
rendered root.**

What the root gave was the link text of a navigation tree, which is a theme's
markup and an abbreviation of the title. The page TYPO3 Explained calls "Assets
(CSS, JavaScript, Media)" stood in the index as "Assets", and no question that
named CSS or JavaScript reached it.

## Evidence

- Measured against `docs.typo3.org` on 2026-08-08, over the four manuals this
  server searches at 14.3. `Manuals::searched()` lists them since `D-ANS-120`,
  and they were a constant of `Documentation` until then. Coverage: the rendered
  roots index 1420 pages, the inventories 1431, 1416 of them shared. What only
  the root has is `singlehtml/Index.html`, one per manual, which `robots.txt`
  disallows for `User-agent: *`. What only the inventory has is each manual's
  own entry page, its `404.html`, and the pages its navigation omits.
- The titles disagree on **505 of the 1416 shared pages**. The ViewHelper
  reference agrees on 30 of 188: it navigates to `Global/If.html` as "if" and
  publishes it as "If ViewHelper <f:if>".
- The seven queries `D-ANS-032` ranked, re-ranked over both indexes at
  `UNDILUTED_WORDS` 3. The rank of the page each query expects, root then
  inventory. `TCA inline …` 1 and 1.
  `Fluid AssetCollector css javascript ViewHelper` **12 and 2**.
  `FunctionalTestCase executeFrontendSubRequest …` **1 and 17**.
  `Record API Fluid template access record.header` **18 and 11**.
  `page title event` 1 and 1. `f:if` **2 and 1**.
  `f:if f:then f:else condition ViewHelper` **5 and 3**. Sum 40 against 36.
- The one that got worse is a chapter, not a miss.
  `Testing/FunctionalTesting/Index.html` carries the title "Functional testing
  with the TYPO3 testing framework", seven words against the two of the link
  text. So dilution costs it the rank, and
  `Testing/FunctionalTesting/Introduction.html` of the same chapter is third.
- Wire size, compressed both ways. The TYPO3 Explained root is 19 951 B and its
  inventory 307 986 B. The four roots together are 41.6 kB against 494 kB of
  inventories. Revalidation answers **304 with a zero-byte body** on all four,
  in about 60 ms each.
- `objects.inv` answers 200 for all four manuals on all four covered versions —
  12.4, 13.4, 14.3 and main. Over the 5498 `std:doc` lines those sixteen
  inventories carry, not one abbreviates its URI with `$` or carries an anchor.
  None abbreviates its display name to `-`. Three pages of the ViewHelper
  reference carry the title `<Unknown>`.
- The host offered compression and nothing asked for it. `Vary: Accept-Encoding`
  and `Content-Encoding: gzip` on every artefact tried, and the root that is
  19.9 kB compressed is 169 693 B plain. So every manual lookup took 8.5 times
  the payload it had to.

## Decided

- `Http\Fetch` asks for compression on every read, and returns the `ETag` beside
  the status and the body. Both are one policy for every host this server reads,
  which is what that class is for.
- The index reads only `std:doc` out of the inventory. The other roles are the
  objects inside the pages: 748 TCA properties at 14.3, and every label and
  section title. What this searches is a table of contents (`R-DOC-001`).
- The reader holds the index per URL with its entity tag and revalidates it on
  every lookup. It does not sit in `Http\Recent`, which holds an answer for a
  chosen while because its source cannot say whether it is still current. This
  source can, so there is no while to choose and nothing goes stale. That branch
  has a measurement and no test. A transport is a body without a status or a
  tag. So a test that hands one in never holds an index and never revalidates
  one. That is the same gap `Http\Fetch` already names for the map of a status
  onto an answer.
- A body that is not an inventory is not an index. It is the same answer as a
  host that said nothing, which is `D-ANS-034` applied to an artefact that is
  not JSON. A 200 with a challenge page in it would otherwise empty the corpus
  and read like a search that found nothing.
- `404.html` is dropped. Sphinx renders the "content was removed" template as a
  document. So it is in every inventory, in no navigation tree, and its two-word
  title is ordinary enough for a query to reach.
- `UNDILUTED_WORDS` stays 3. Swept over the new corpus on the same seven
  queries. The sum of ranks is 31 at 1, 35 at 2, 36 at 3 and 35 at 4. It is 36
  at 5 and 6, 39 at 8 and 49 at 12. That is flat where the corpus is, and the
  reason for 3 stands: a book name is two or three words and must not dilute.

## Assumed

- That the inventory stays beside every manual, in version 2 of the format. It
  is a build artefact of the same run that renders the pages. Sphinx writes it
  for the cross-project references other manuals resolve through it.
- That the entity tag holds across representations. The host serves the same one
  for the compressed and the plain body and answered 304 for it. So a request
  for compression does not cost the revalidation.
- That a session asks more than once. The first lookup costs 494 kB against the
  41.6 kB the roots cost, and every later one costs nothing. The break-even
  against what this replaced is twelve lookups in a session.
- That the stated title is the better index term where the two disagree. Five of
  the seven queries say so and one says the opposite.

## Wrong if

- A manual no longer publishes `objects.inv`, or publishes a format this cannot
  read. The book disappears from the search while its pages render fine, and the
  caller is told the source did not answer.
- A session that makes one lookup is the ordinary one. Then this costs the host
  ten times what the navigation tree cost and the held index never pays it back.
- The longer stated titles cost more ranks than they buy. The functional testing
  chapter already answers with a sibling page rather than its index. A second
  query that does the same is the pattern rather than the exception.

## Since then

On 2026-09-15 the index reads two more roles out of the same inventory.
`D-ANS-158` admits the classes, methods and commands a manual declares, for a
query word in code form. `D-ANS-159` scores every page with its best heading.
What this searches is still the inventory, and the read costs no second
artefact. Neither **Wrong if** above has happened: every manual publishes the
inventory, and the stated titles cost no rank the sweep of `D-ANS-159` would
show.
