---
id: D-DOC-024
title: "The site's theme is a package this repository keeps none of"
date: 2026-08-12
status: open
coveredBy:
  - SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage
---

# D-DOC-024 — The site's theme is a package this repository keeps none of

**`typo3/soul-guides-theme` renders the site, and the layout, the assets and the
search index come from that package rather than from files kept here.**

What this repository keeps is `guides.xml`, the pages and the drawings. The
design system came out as a theme of its own, and what stood here was a copy of
it that nothing re-pulled. That is the defect that had already caught
`D-DOC-023` once.

## Evidence

- The move deletes 1824 lines below `build/guides/`. A 190-line layout, a
  444-line stylesheet, a 314-line script, a 166-line asset build, two more
  templates and three manifests. What replaces all of it is one line of
  `require`.
- The npm half goes with it: esbuild, two `@fontsource` packages and the design
  system pinned to a commit. So `build/guides/` has one manifest where it had
  two, and the render no longer needs a package manager for the browser.
- `D-DOC-023`'s third **Assumed** was that the system moves under this checkout
  and tells it nothing. It did: `c2bef123` found two vendored token values that
  had been below WCAG AA for months. A package is re-read by `composer update`;
  a copy is re-read by nobody.
- The theme serves reStructuredText, and this corpus is Markdown. What that
  costs is the toctree. `automatic-menu` builds the same tree out of the
  directories instead, which is the mechanism the Markdown parser already marks
  its documents orphan or not for.
- Six pages sat in four directories with no `readme.md`. The renderer warns and
  attaches them to nothing, so each was in no menu at all — reachable only by a
  link inside another page.
- The theme's own finish step does the three things this repository had written
  for itself. It copies the drop-in beside the pages and draws every element
  ahead of the browser, so a page reads with no script. It writes the index the
  search bar fetches.
- `sds-image` and `sds-figure` reference a drawing rather than link it, which is
  what puts the page's own ink into it. Both work out its box from a table of
  three names inside `soul.js`: `answer-sources`, `installation-fallback` and
  `system-overview`. Those are this repository's own drawings and three of
  eleven.
- A Markdown image is an inline node, and the theme renders a figure for the
  reStructuredText directive alone. So every drawing here is a plain `<img>`: no
  frame, no caption, no lightbox, and no way to learn which mode the page is in.

## Decided

- The manifest requires the theme as `dev-main`, because the repository carries
  no tag. What pins it is `build/guides/composer.lock`, which names the commit
  and sits in git. That half no longer holds — see **Since then**.
- Everything the bar, the tab and the footer say stands in `guides.xml`, under
  the extension element that registers the theme. Nothing here copies a template
  to change a name.
- The mark is this repository's own drawing and lives with the pages, in
  `documentation/images/`, at the three optical sizes the system draws a signet
  at. It follows the artwork form the theme asks for, one `var()` with a hex
  fallback per shape. So a referenced mark carries the page's ink and the file
  still renders on its own.
- The search index is the theme's. What goes with it is `D-DOC-019`'s one
  hand-written piece: the index covers fenced blocks now, and this corpus's are
  mostly recorded tool answers.
- Every directory of `documentation/` has a page of its own, held by
  `SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`. That is the
  repository's own convention for a directory, and it is now also what makes its
  pages reachable.
- The dark twin of every drawing stays and still goes out beside the light one.
  Nothing asks for it today, and it is the dark half of a drawing rather than a
  build product. A delete would mean eleven files drawn again the moment they
  can follow the page.
- What the light files would become is a straight substitution, measured on
  `answer-sources.svg`, so it stands here rather than in a second measure.
  `#1C1A17` is `--text-primary`, `#4A453D` `--text-secondary`, `#8A8378`
  `--text-muted`, `#E3DFD6` `--border-subtle`, `#C9C3B7` `--border-strong`.
  `#FBFAF7` `--surface-canvas`, `#FFFFFF` `--surface-raised`, `#986200`
  `--status-warn` and `#FF8700` `--accent`. Each as a `var()` with the light
  value as its fallback. The three signets are already written that way.

## Assumed

- That `dev-main` and a lock file are a pin. The package carries no tag to ask
  for instead, so what names the commit this checkout renders with is the lock
  file and nothing else.
- That the package stays reachable where `build/guides/composer.json` names it.
- That the recorded answers do not bury a reader who searches this site. This is
  what `D-DOC-019` decided the other way with 582 fenced blocks counted.

## Wrong if

- A drawing shows in the wrong ink on a dark page, which is what it does today:
  the file is light and nothing swaps it.
- A rail item ends cut off rather than wrapped. `D-DOC-023` named the tool names
  as a departure from a specimen for exactly this reason.
  `typo3_backend_module_lookup` ends cut off in the rail as it stands.
- The theme moves and this checkout renders an older one for months, which is
  the copy's defect through a lock file nobody updates.
- A page goes out unstyled, because the finish step did not run and nothing said
  so.

## Since then

`build/guides/` is gone, manifest and lock with it. Whoever renders requires the
renderer and the theme outside the checkout (`D-DOC-028`), so the statement is
more true than it was. The first **Wrong if** became impossible with the lock,
since nothing here records which theme it rendered. Nothing guards the second,
because a page that goes out unstyled is something a reader sees rather than
something the suite says. The dark drawings are gone as well. No page named one,
so eleven files stood against a mechanism that does not exist, and a reader in
dark still reads a light drawing.
