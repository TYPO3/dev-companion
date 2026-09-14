---
id: D-ANS-119
title: A permalink identifier is every inventory name that is not a page
date: 2026-08-27
status: open
coveredBy:
  - PermalinkTest::aPageOfAManualIsNotOneOfItsIdentifiers
---

# D-ANS-119 — A permalink identifier is every inventory name that is not a page

**`typo3_permalink_lookup` resolves a name against every role of a manual's
Sphinx inventory except `std:doc`, which is exactly the complement of what
`typo3_documentation_lookup` reads.**

`D-ANS-065` reads `std:doc` alone and says the other roles are "the addressable
objects inside the pages". Those objects are the permalink identifier space, so
the two tools take one artefact apart along one line.

## Evidence

- Read against `docs.typo3.org` on 2026-08-27, over the inventory of TYPO3
  Explained at `main`: 14292 objects in fifteen roles, of which 975 are
  `std:doc`. One name of each of the other fourteen roles went to
  `/permalink/t3coreapi:<name>` and every one answered 307 with the URI the
  inventory gives it. Those are `std:label`, `std:title`, `std:confval`,
  `std:confval-menu`, `std:option`, `std:accordion`, `std:console:command`,
  `std:console:command-list`, `typo3:file`, `php:class`, `php:interface`,
  `php:method`, `php:property`, `php:const`.
- `std:doc` is the one that does not. `t3coreapi:ApiOverview/Assets/Index` is
  404, and so are `t3coreapi:404` and `t3coreapi:Index`, which are the only two
  single-segment page names of that manual no other role shadows. 971 of the 975
  page names carry a `/`.
- The route is case-insensitive on both halves.
  `T3COREAPI:OPCACHE-SAVE-COMMENTS` and `t3coreapi:OpCache-Save-Comments` answer
  307 with the same URI as the lowercase spelling. `t3coreapi:About` looks like
  a page that resolves and is not: it answers `About.html#about`, which is the
  `std:label` named `about`.
- A `std:confval` and its generated label are both listed and both resolve.
  `columns-onchange` is the `std:confval` the TCA reference declares, and
  `confval-columns-onchange` is a `std:label` of the same anchor. Each is a line
  of the inventory rather than a prefix somebody strips.
- One name may carry more than one role at one target.
  `typo3-cms-core-page-assetcollector` is `std:label` and `php:class` on
  `ApiOverview/Assets/Index.html`, which is one identifier and not two.
- No URI in the three inventories read abbreviates itself with `$`, which
  version 2 of the format allows. Counted over TYPO3 Explained, the TCA
  reference and the Low Level manual at `main`: 0 of 17290.

## Decided

- The parse is shared. `Manual\Inventory` fetches, revalidates under the `ETag`
  and takes an inventory apart. `Documentation` filters it to `std:doc` and
  `Permalink` to everything else, so one place reads the format.
- The `$` abbreviation stays as it is, on the same grounds `D-ANS-065` left it
  alone. Nothing in the corpus uses it, and a branch nothing takes is one
  nothing checks either.
- An answer carries the roles rather than hides them. A name's registered role
  says whether a link to it lands on a section, on a configuration value or on
  API. A caller who chooses between two spellings has nothing else to choose on.
- Where a target has a `std:confval`, that name is the one the answer prefers.
  The manual declares it and Sphinx generates the rest from its anchor.
- The match ignores case, because the route does.

## Assumed

- That the permalink route accepts exactly what the inventory lists. The check
  covered fourteen roles in both directions, and no manual had a full read
  against the route.
- That `std:doc` stays outside the identifier space. It is what the route does
  today and nothing on the host states it as a rule.

## Wrong if

- A page name starts to resolve. Then the two tools no longer split the
  inventory along one line. A caller who asks `typo3_permalink_lookup` for a
  page hears it does not exist.
- A role arrives that the route does not accept. Then the answer reports an
  identifier as resolved that answers 404, which is the failure this tool exists
  to prevent.
- A manual publishes a URI abbreviated with `$`. Then the URL handed back is
  short by the object's own name and lands on the page rather than the anchor.
