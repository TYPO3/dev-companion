---
id: D-DOC-027
title: The renderer's configuration sits with the pages it renders
date: 2026-08-12
status: open
coveredBy:
  - SiteTest::theRenderersConfigurationIsNotPublished
---

# D-DOC-027 — The renderer's configuration sits with the pages it renders

**`guides.xml` is `documentation/guides.xml`, named on the render step with
`-c documentation`, and it is the one file below `documentation/` the copy does
not publish.**

It stood at the root because the renderer looks there by default, and because
the corpus was a directory plus one file above it. Neither is true since
[`D-DOC-026`](doc-026-the-site-is-the-documentation-and-the-readme-stays-out-of-it.md).

## Evidence

- `Documentation/guides.xml` is where a TYPO3 extension keeps this file, and
  this repository's own knowledge says so in
  `knowledge/hints/documentation.json`. A reader who knows TYPO3 looks in the
  documentation directory first.
- The renderer's binary resolves the file in three places: beside its own
  `vendor/`, then under `--working-dir`, then under `-c`, which defaults to the
  work directory. Only the last one was in use.
- `input` and `output` resolve against the work directory rather than against
  the configuration. So the file moves and `.site/source` and `.site/html` stay
  as they were. That is what makes the move one flag.
- The corpus is one directory now, so there is no page above the file it
  configures.

## Decided

- Site::RENDER carries `-c` and `Site::SOURCE`, so the flag names the same
  constant the copy comes from and the two cannot move apart.
- `Site::sources()` skips it by name. The copy is the pages published, and a
  renderer's configuration is not one — published it would sit in the input
  directory it declares.
- The work directory stays the root of the checkout. Everything the two steps
  name is relative to it, and the configuration is now the only thing that is
  not.

## Assumed

- That the renderer still reads `-c` as a directory rather than a file. The
  binary appends `/guides.xml` to what it gets, which is its own contract and
  not one this repository can hold.

## Wrong if

- A second file below `documentation/` is not a page. The skip is by name, so
  the next one is a silent publish rather than a failure.
- The render is ever run from somewhere other than the root. Then `-c` resolves
  against that directory and the flag has to become an absolute path, along with
  everything else the two steps name.
