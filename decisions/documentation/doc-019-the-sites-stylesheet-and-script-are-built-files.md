---
id: D-DOC-019
title: "The site's stylesheet and script are built files"
date: 2026-08-09
status: revoked
revokedBy: D-DOC-024
---

# D-DOC-019 — The site's stylesheet and script are built files

**The build puts the theme's CSS and JS into two hashed files every page links.
The syntax colours come from highlight.js rather than from a regular expression
written here.**

`D-DOC-017` weighed an inline stylesheet against 4 KB of assets. They are now 16
KB, and the hand-written things had grown from a stylesheet to a highlighter.

## Evidence

- The two inlined blocks are 16.1 KB on every page: 7.9 KB of CSS and 8.5 KB of
  JS. Across 48 pages that is 772 KB of the 2288 KB the site served — 34 % of
  everything, the same bytes 48 times.
- The median page was 34.8 KB, of which 16.1 KB was the repeated pair. Built as
  files it is 18.8 KB, and the site is 1521 KB plus 36.3 KB of assets fetched
  once.
- The layout template had reached 470 lines in three languages. 330 of them were
  the CSS and the JS.
- The highlighter written here was 60 lines of regular expression over three
  languages. highlight.js writes 25809 spans over the same 289 blocks where it
  wrote 12000, and neither changes a character of what a block says.
- The renderer copies an image a page names and nothing else. A file written
  into `.site/source` for a page to link never arrives in `.site/html`, which is
  what `documentation:search` already runs after the renderer for.
- Twig's `source()` reads a file below the theme at render time. So the layout
  can learn what this build called its assets, which is the cache buster
  `D-DOC-017` counted as a part that moves.
- `build/guides/` already carries a toolchain of its own with its own
  `composer.json`. So a second manifest beside it is not a new kind of thing in
  this repository.

## Decided

- `theme/assets/site.css` and `theme/assets/site.js` are the sources. The build
  writes `dist/site.<hash>.css`, `dist/site.<hash>.js` and a `manifest.txt` that
  names them, and the layout reads that manifest with `source()`.
- The hash is of the file's own contents, so a changed asset is a changed URL
  and nobody gets yesterday's stylesheet with today's markup.
- Git ignores `dist/`, and `bin/cli documentation:assets` fails with the command
  that writes it. A build product in a commit is one somebody edits.
- highlight.js does the colours, with `json`, `yaml` and `bash` registered and
  no other: 137, 50 and 20 of this corpus's blocks. The full build carries forty
  more languages nobody here writes.
- The palette stays this theme's: `site.css` maps highlight.js's classes onto
  the variables the page already uses. Its own themes carry a background and a
  foreground, which would be a second set of colours to keep against the first.
- The search stays this repository's. It is the one hand-written piece that
  knows something a package cannot. The 582 fenced blocks are evidence on
  record, and an index over them buries every prose match.
- Node is 24, which is the active LTS and what this machine runs.
- Site::useBuilt() is the seam a test hands its own directory in through,
  because `npm run build` is not something the suite runs.

## Assumed

- That a reader visits more than one page. The gain is a cache hit; one page
  read once costs 20 KB more than it did.
- That GitHub Pages serves `assets/` as it serves the pages. Nothing here has
  seen it do otherwise, and the same deployment carries both.
- That highlight.js stays maintained and small. A lock file pins it, and what
  replaces it is another package rather than the regular expression again.

## Wrong if

- A page goes out unstyled, because the assets never got built and the copy step
  skipped in a way that did not fail.
- A reader gets an asset from a previous deploy. The hash exists to make that
  impossible, and it would mean the manifest and the copied files disagree.
- The bundle grows past what it saves, because somebody added a language or a
  library this corpus does not use.
- A block's colours are wrong enough to change what somebody types. A test holds
  the text; nothing holds the colours.

## Since then

The build publishes a third kind of file: the two font families the design
system allows, vendored rather than fetched from a host. That is twelve woff2
files, of which a reader fetches about a third. They carry no hash because a
weight of a subset already says what it is. `documentation:assets` and
`documentation:search` are steps of one command now, and the failure this entry
gave the first is the render's. An absent build stops it rather than publishes a
site with no style.

## Revoked on 2026-08-12

There is no build here to write those two files. The theme is a package now,
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md).
It ships its own stylesheet, script and faces as files a page links, so the
statement above describes a build that no longer exists. The two tests this
entry named went with it.

What it decided that still holds is where it holds. An asset is a file rather
than a block in every page. The syntax colours come from a library rather than a
regular expression written here. The theme colours a fenced block on the server
with the same one. What goes is the search. The index is the theme's, so the 582
fenced blocks this entry counted are in it. The one hand-written piece that knew
something a package could not is the piece that went.
