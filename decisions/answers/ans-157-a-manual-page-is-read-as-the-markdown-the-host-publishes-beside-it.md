---
id: D-ANS-157
title: A manual page is read as the Markdown the host publishes beside it
date: 2026-09-15
status: open
coveredBy:
  - DocumentationTest::aManualWithoutMarkdownIsReadFromItsHtmlAndAskedOnce
  - DocumentationTest::aPageIsReadAsTheMarkdownTheHostPublishesBesideIt
  - DocumentationTest::aSearchExcerptIsTheLeadOfTheMarkdownPage
---

# D-ANS-157 — A manual page is read as the Markdown the host publishes beside it

**`Manual\Documentation` reads a page at its own URL with `.md` for `.html`, and
reads the rendered HTML only where the manual has no Markdown yet.**

The documentation team answered the five asks of `T-260808-48e2` with a rewrite
of `llms.txt` and `robots.txt`, and with an artefact nobody had asked for. Every
page is published as Markdown beside its HTML, complete after the build, with a
front matter that states its title.

## Evidence

- **Measured against `docs.typo3.org` on 2026-09-15.** `llms.txt` is 19 891
  bytes. It names `objects.inv.json`, `objects.inv`, `_sources/` and the `.md`
  of a page, in that order of preference. It recommends `/singlehtml/` only to a
  client `robots.txt` names, which is the contradiction the second ask was
  about. `robots.txt` allows `/*.md$` beside the inventories for the named AI
  bots, and disallows nothing of it for `User-agent: *`.
- **The five asks, as the host answers them now.** The first two are done. The
  third is declined. `searchindex.js` answers 404 on all four manuals at all
  four covered versions, and `llms.txt` says the host publishes no full-text
  search artefact. The fourth is not done: a released changelog page still
  answers `Cache-Control: public, no-cache`, as does every artefact. The fifth
  is moot: `objects.inv.json` is the inventory as JSON and lists the changelog
  pages under `std:doc`.
- **Where the Markdown is.** 200 with `text/markdown` on all four searched
  manuals at 14.3 and `main`. Three answer at 13.4, where TYPO3 Explained has
  none, and none at 12.4. Every page answers with an `ETag`. `llms.txt` says the
  host has it once a manual is rendered again since the format arrived, per
  manual and per version.
- **What it costs.** Four ranked queries return 23 pages at 14.3. Read as HTML
  those are 373 011 bytes on the wire and as Markdown 37 660, 9.9 times less.
  Plain they are 2 976 916 against 111 844 bytes, 26.6 times. A lookup reads up
  to six pages for its excerpts. So it paid about 160 kB of transfer and six
  `DOMDocument` parses of a quarter-megabyte each, for six sentences.
- **What the Markdown carries that the HTML reader dropped.** The definition
  lists of a TCA property come out as nested lists with their values. The
  ViewHelper reference, generated from JSON at build time, comes out whole.
  Links are absolute permalinks, which `typo3_permalink_lookup` reads.
- **The JSON inventory is no cheaper.** `objects.inv.json` is 342 196 bytes on
  the wire for TYPO3 Explained at 14.3, against 315 820 for `objects.inv`.
  `Inventory` already reads the zlib form. `llms.txt` prefers the JSON only
  where no Sphinx parser is at hand.

## Decided

- A page is fetched as `.md` first. A body that opens with the front matter is
  the page, and anything else is not. That is `D-ANS-065`'s rule for the
  inventory, applied to a page. The title is the front matter's, and the content
  is the body after it, as published.
- A page whose Markdown does not answer is read as HTML through the reader
  `D-ANS-096` describes, and nothing about that reader changes. So a manual at
  12.4 answers as it did.
- The miss is remembered per manual and per version, in the process, once a page
  of it answered as HTML alone. Every later page of that manual costs one read
  rather than a miss and a read. A manual rendered again mid-session goes
  unnoticed until the process restarts, and answers as HTML meanwhile.
- A result with an anchor is read as HTML still. The anchor names a section by
  its id, and the Markdown carries no ids. Those results are the property
  sections of the TCA reference.
- The excerpt of a search is the lead of the Markdown, which is its paragraphs.
  The front matter, the headings, the lists, the quotes and the code stay out.
  So does the section list every page opens with.
- The index stays `objects.inv`, and the changelog stays on `_sources/`. The
  entry RST is one file without includes, and `Changelog::parse()` reads it and
  the installation's own files alike.
- The canonical URL a result carries stays `.html`. It is what the inventory
  states, what a permalink resolves to, and what a caller hands back.

## Assumed

- That the front matter keeps its shape: a `---` fence, `title:` as a
  double-quoted scalar, a `---` fence. The reader falls back to the first
  heading for a title and to the HTML for a body it cannot recognise.
- That a `.md` miss is a property of the manual at that version rather than of
  one page. That is what `llms.txt` states.
- That the fourth ask stays open on the host's side and costs nothing here. The
  inventories revalidate under their entity tag, and a page is read on demand.

## Wrong if

- A manual's Markdown answers 200 with a body that is not the page, a challenge
  page or a redirect target. The reader treats it as no Markdown and reads the
  HTML, and the caller sees a page that is heavier rather than wrong.
- The Markdown of a page drops content the HTML carries. The two are one build,
  and `llms.txt` states them as equal. A page read shorter as Markdown than as
  HTML is the report that shows it.
- One page of a rendered manual answers 404 as Markdown while the rest answer.
  Then the memo marks the manual as one without, and every later page of it is
  read as HTML for the rest of the process.
