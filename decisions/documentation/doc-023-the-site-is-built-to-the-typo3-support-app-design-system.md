---
id: D-DOC-023
title: The site is built to the TYPO3 Support App design system
date: 2026-08-09
status: revoked
revokedBy: D-DOC-024
coveredBy: []
---

# D-DOC-023 — The site is built to the TYPO3 Support App design system

**The site follows the TYPO3 Support App design system. Its tokens sit vendored
below `theme/assets/tokens/`, its icons below `icons/`, and `site.css` only
arranges them.**

What the site looked like changed three times in one afternoon and each time by
taste, because nothing here was the source for it. There is one, and it is the
product's own.

## Evidence

- The palette changed three times in one session: warm neutrals, then matte with
  a mint accent, then matte with a burnt orange. Each time with a contrast
  measure, and the choice itself rested on nobody.
- The design system is a design-system project on claude.ai/design, read with
  `DesignSync`. It carries six token files, 33 icons from `TYPO3/TYPO3.Icons`
  under the core's own identifiers, and two vendored families. Also a component
  layer, and build rules whose first line describes this server.
- The site broke four of its rules. The search dialog carried a shadow, and the
  magnifier came from here rather than from the set. The accent was a colour
  this repository picked, and the two families were absent.
- The system's own answer to light and dark is what this site already had, one
  set of `light-dark()` pairs against `color-scheme`. So the mechanism
  `D-DOC-022` settled survived the change and only the switch above it moved.
- The faces publish 245 KB across twelve files, of which a reader of this corpus
  fetches about 84 KB. The latin-ext half sits behind a `unicode-range` no page
  here has a character for.
- The stylesheet grew from 11.6 KB to 17.8 KB, and both are one request for the
  whole site — `D-DOC-019`.

## Decided

- The token files are copies, unchanged, and nobody edits them here. A value
  that differs from the system's is the one thing that directory exists to
  prevent, and `theThemeWritesNoColourOfItsOwn` holds `site.css` to their names.
  That check went with the file it read; the **Revoked on** section below says
  what stands in its place.
- The icons are copies, not drawings. The system contributes an absent icon
  upstream rather than invents one locally, and the identifiers are the core's
  own, the same strings `typo3_icon_lookup` returns.
- Every icon goes inline with Twig's `source()`, because an `<img>` cannot
  inherit `currentColor`. The favicon is the one exception, since a browser tab
  is not a place a page's colour reaches.
- `build.mjs` builds the faces out of the two `@fontsource` packages, latin and
  latin-ext, woff2 alone. The system vendors them rather than pulls from a font
  host. So a page sets in the right type behind a strict content policy or with
  no network. That reason holds here.
- The faces are `font-display: optional` and the two the header sets preload.
  `swap` re-laid the wordmark out when the face landed. That is a jump on every
  navigation, because each document runs its own font load whatever the cache
  holds. Optional forbids the swap after the paint, so the cost moves to one
  page. A reader who arrives with a cold cache reads it in the fallback, and
  every page after it sets in the face.
- A drawing is not prose. The renderer wraps a lone image in a paragraph, and
  the paragraph carries the 66ch measure. So a 1200px drawing arrived at 556px,
  0.46 of its drawn size, and its 13px floor at 6px on the screen. It takes the
  column now, 0.67, and the lightbox is where a reader sees it at size.
- The accent marks the current page in the rail and the pipe in the wordmark,
  and nothing else. The system's third place — the shell prompt — has no markup
  here, because a fenced block is one string the renderer hands over.
- The chosen hit in the search is the quiet accent surface rather than the
  accent. That is the system's treatment for a selected row and keeps the filled
  accent to one meaning on the page.
- highlight.js maps onto the three syntax colours the system declares. A fourth
  would be a colour nobody declared.
- The mode switch is the system's two segments, so the three-state button
  `D-DOC-022` decided is gone.
- The drawings are the system's three where it has one, and the other eight move
  onto its palette. The category colours they carried, a blue stroke, a green
  one, become hairlines. The system's diagram vocabulary is neutral nodes, one
  orange, and status colour only where the drawing is about status.
- Every drawing ships twice and the dark file is a straight token swap of the
  light one. Which one shows is the script's decision rather than a `<picture>`
  query. A `media` query reads the machine, and this page can stand in the other
  mode against it. Site::publishDrawings() puts the twin nobody named beside the
  one a page did.
- A drawing goes inline into the page rather than as a link. An `<img>` is a
  document of its own and cannot see this page's `@font-face` rules. So the type
  inside every drawing was the reader's own fallback. Measured on a machine with
  neither family installed, the same file rendered 169px wider as an `<img>`
  than inline. A column layout against a face nobody has is what made the gaps
  look wrong however often somebody corrected them. The markup keeps the `<img>`
  until the script runs, so a browser without one still gets the drawing.
- A drawing has the width the page shows it at. The system's floor is 13px *at
  drawn size*, and 1200px of drawing in an 804px column is 0.67 of it. No
  coordinate inside the file can repair that. The redrawn ones are 800 wide; the
  rest await a redraw and the list says so.

Two things depart from a specimen, which the system asks a writer to name rather
than forbids:

- The rail is 210px and the tool names do not fit it:
  `typo3_system_extension_lookup` is 29 characters of mono at 13px. They wrap
  rather than truncate, because a truncated identifier is not the identifier.
- The wordmark reads `TYPO3 | Dev Companion`. The system's own says
  `Support App`, which is this product under the name the design system serves.

## Assumed

- That the design system is this product's. Its build rules describe a local MCP
  server in plain PHP that helps coding agents implement, review and verify
  TYPO3 work. That is what this repository is.
- That a reader of this corpus needs latin alone, and that latin-ext beside it
  costs nothing because the browser fetches by `unicode-range`.
- That the system moves under this checkout and tells it nothing. Nothing
  re-reads it; a token that changed there is a copy somebody has to make again.

## Wrong if

- A token below `tokens/` differs from the system's, which is what makes the
  vendored copy worse than no copy.
- A reader fetches a face for a subset no page here uses, which would mean the
  `unicode-range` did not survive the build.
- A whole site reads in the fallback, because `optional` needs the face in the
  cache and the host serves it without a cache header. `php -S` is one such
  host, so the local preview shows exactly that and says nothing about the
  deploy.
- The accent appears somewhere that is not the current page or the pipe.
- A drawing loses what its colours carried. Eight of them grouped by hue and now
  group by position and label alone. That is the system's own vocabulary and is
  also the change most likely to have taken meaning out.
- A reader takes the mark or the signet as TYPO3's endorsement of this package.
  The system keeps the Soul out for exactly that reason, and nothing here may
  put it back.

## Since then

The first **Wrong if** happened. Two vendored tokens sat below WCAG AA for
months after the system had raised them. So the published documentation was
below it in both modes for as long as the copy stood. The third **Assumed** is
what it cost: a copy nobody re-pulls is wrong and says nothing. The stylesheet
imports the tokens from the package now, pinned to a commit, so the statement's
token clause no longer describes this site. The two assertions went back on the
theme that exists. The token check runs in the bundled stylesheet where the
imports resolve. The colour scan strips comments, because an explanation that
named the two broken values read as the breach.

## Revoked on 2026-08-12

By its own third **Assumed**, and then by the move it made necessary. Nothing
below `theme/` remains to vendor, so no part of the statement describes this
site (`D-DOC-024`). The two tests are gone with the files they read, and what
holds in their place is the package. Nobody can redeclare a token where there is
no stylesheet. Three findings outlive it and go forward as open. A drawing has
to appear at its drawn size, and a truncated identifier is not the identifier. A
drawing that cannot tell which mode the page is in reads in the wrong ink.
