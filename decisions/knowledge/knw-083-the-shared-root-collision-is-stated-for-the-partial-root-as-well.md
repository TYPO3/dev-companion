---
id: D-KNW-083
title: The shared-root collision is stated for the partial root as well
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::theSharedRootCollisionIsStatedForPartialsBesideLayouts
---

# D-KNW-083 — The shared-root collision is stated for the partial root as well

**The corpus states that `partialRootPaths` carries the same name collision as
`layoutRootPaths`, and names `Header/Header` as the one a sitepackage hits.**

`sitepackage-templates` warned about the layout root alone, so a reader who
followed it subdivided `Layouts/` and put its own
`Partials/Header/Header.fluid.html` beside the core's.

## Evidence

- `feedback/2026-08-17-205830`. A session that built a v14 demo site acted on
  the layout sentence and avoided that bug by construction. It hit the partial
  one when it registered `partialRootPaths.20` on `lib.contentElement`. Its
  frontend and every page with a `text` element answered HTTP 500. It paid three
  calls, a directory diff of the two partial roots among them, to find out why.
- The collision is in the tree on every covered major, read in `.checkouts/` at
  the branch heads this worktree carries.
  `typo3/sysext/fluid_styled_content/Resources/Private/Partials/Header/` holds
  `All`, `Date`, `Header` and `SubHeader` on 12.4, 13.4, 14.3 and `main`. They
  are bare `.html` on the first two, `.fluid.html` on the others. Beside them
  stand `Footer/All`, `Media/`, `Bullets/Type-0` to `Type-2`, `Table/Columns`
  and `DropIn/`.
- `Header/All` is what renders it, so the failure is not local to the element
  the partial was for. It renders `Header/Header` with `header`, `layout`,
  `positionClass`, `link` and `default`, the same five arguments on 12.4 and on
  14.3. Every core element renders its header through it.
- The root the extension outranks is the core's own.
  `Configuration/TypoScript/Helper/ContentElement.typoscript` gives
  `lib.contentElement`
  `partialRootPaths.0 = EXT:fluid_styled_content/Resources/Private/Partials/`
  and `10 = {$styles.templates.partialRootPath}`, unchanged across the covered
  majors. So an extension that adds `20` wins over both.
- The general rule was already stated and does not answer this.
  `fluid-templates` carries that `TemplatePaths::resolveFileInPaths()` walks the
  root paths backwards and that the highest integer key wins — `D-KNW-052`. That
  says which file renders once somebody suspects two. It does not say that the
  core ships a partial under the name a content element frame has.
- Delivery and routing were not the failure.
  `bin/cli hints:probe "adding partialRootPaths 20 to lib.contentElement for my sitepackage partials"`
  reaches `sitepackage-templates` on text alone. The session read the hint by id
  before it wrote its TypoScript.

## Decided

- Step 1a of the ladder, and stated where the layout half already is. The
  partial half was absent rather than misplaced. One sentence in the same hint
  is what the reader who followed the layout advice would have read.
- Closed in this run rather than queued, on `D-KNW-082`'s reading of the same
  line: the checkout reading the ladder owes a feedback claiming something about
  TYPO3 was done here, on all four branches and named by file above, and nothing
  in `src/`, in a schema or in a skill moves.
- This is the second judgement to take that exception in two days, both on this
  hint. It is recorded rather than acted on — a third would be evidence that the
  line `documentation/records/judging.rst` draws is in the wrong place, and one
  more case is not.
- The statement carries no version boundary. What differs across the majors is
  the file name extension. A template addresses a partial by name, so the
  collision is the same on all of them.
- The hint gains no `appliesTo` needle. The session that reported reached the
  hint, and a needle added on a delivery that worked is a guess about a reader
  nobody has seen.
- The feedback's own suggestion of a general rule stays where it already is, in
  `fluid-templates`. That rule is that the root path lists are one name space
  the index resolves. Repeating it here would be the third copy of the
  resolution order.

## Assumed

- That a sitepackage is the reader. The statement is for one that adds a root of
  its own to `lib.contentElement`, which is what the hint around it describes.
- That naming the neighbouring directories is worth its line. `Header/Header` is
  the one a sitepackage hits. The rest is what makes "put them under a directory
  nothing else uses" a rule rather than a rename of one file.

## Wrong if

- A session reports the same collision on a directory this statement names with
  no caveat, `Media/` or `Table/` under an extension's own partials. Then naming
  `Header/Header` narrowed a rule that had to stay general.
- `fluid_styled_content` renames or drops `Partials/Header/Header` on a later
  branch. The statement then names a file that no longer exists, and the trap it
  describes moved with it.
- A session subdivides its partial root as told and still collides, because the
  directory name it chose is one another extension in the project ships. Then
  what the statement owes is how to pick the name, not that it has to be its
  own.
