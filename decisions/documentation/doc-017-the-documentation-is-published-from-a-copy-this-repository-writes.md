---
id: D-DOC-017
title: The documentation is published from a copy this repository writes
date: 2026-08-06
status: open
coveredBy:
  - SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex
  - SiteTest::everyLinkThePublishedCopyKeepsResolvesInsideIt
  - SiteTest::everyReferenceIntoAnotherPageIsAnsweredByALabel
  - SiteTest::noPublishedPageKeepsALinkToAFileTheSiteDoesNotCarry
---

# D-DOC-017 — The documentation is published from a copy this repository writes

**`documentation/` goes out as a site on GitHub Pages, generated from the copy
`bin/cli documentation:build` writes rather than from the sources.**

That directory serves a reader with the whole checkout in hand. A third of what
it points at is a directory the site does not carry.

## Evidence

- 87 of the 246 relative links below `documentation/` leave it. 39 into
  `decisions/`, 19 into `requirements/`, 9 at `AGENTS.md`, 6 into `scenarios/`.
  5 into `todo/`, 5 into `src/`, 2 into `skills/` and 2 at the root readme.
  Served as they stand, every one of them is a 404.
- Eleven of its directories carry their own page as `readme.md`, and a generator
  publishes a directory as `index.md`.
- Nothing else in the corpus needs a hand. No page carries front matter, none of
  the 582 fenced blocks holds a relative link, and the whole tree is 1.1 MB.
- GitHub Pages serves the repository root or `/docs` when it deploys from a
  branch, and this directory is neither.
- A Python renderer would be the only one in a repository whose whole upkeep is
  `composer` and `bin/cli`. Material for MkDocs and Zensical each build this
  corpus, and each brings a second toolchain into CI to do it.
- phpDocumentor Guides renders it in PHP: 47 pages, with the tables, the code
  blocks, the images and the page links. It is what renders docs.typo3.org, so
  the corpus and its ecosystem agree.
- It costs two things, both measured. There is no search of any kind, and a link
  that names a heading in another page drops whole. 33 of them, all into
  `answer-sources.md`, with the text left where the link had been.
- It cannot go in this package's `require-dev`. Resolving it there drags
  `symfony/string` to 8.1, which needs PHP 8.4.1, and CI runs 8.2 and 8.3.
- No theme it ships is publishable. The default one writes a bare document, 57
  files and not one stylesheet. `guides-theme-bootstrap` is a starter whose
  navbar carries the brand "Navbar" and whose menu is empty.
- A menu of its own is not on offer either. Guides builds one from a `toctree`,
  which is a reStructuredText directive that markdown has no form of. So every
  theme's navigation is empty against this corpus.
- What a template does get is the whole corpus: `env.allDocuments` holds all 47
  at render time, with a path and a title each.

## Decided

- Only `documentation/` is published. What must hold, what a change rested on
  and the order of the work stay entries in the repository. The site links to
  them there.
- The copy carries the two changes rather than the sources, so the paths a
  reader of the checkout follows are the ones `links:check` still reads. `Site`
  writes it and `guides.xml` renders it, so a reader can see a page the way the
  site publishes it with no deploy at all.
- The renderer is phpDocumentor Guides, installed from `build/guides/`. That is
  a manifest of its own, so the package's own dependencies do not bend to fit a
  renderer.
- The search comes from here as well, from `documentation:search`, and the
  browser filters it. The index covers the prose and not the fenced blocks. That
  holds it to 213 KB and keeps a page that answers a question above the one
  whose recorded answer happens to carry the word.
- The theme is this repository's own, and one file. It is a layout that shadows
  the default one, with the stylesheet inline. The navigation comes from
  `env.allDocuments` rather than from a `toctree` nothing here can write.
- Rejected: `t3docs/typo3-docs-theme`, which renders the docs.typo3.org look. It
  carries a dependency graph of its own for a site of 47 pages, and this server
  is not TYPO3 documentation.
- A link that names a heading in another page loses the heading rather than the
  link. The copy is where that happens, so the sources still name the section a
  reader of the checkout jumps to.
- The navigation is the file tree. `documentation/readme.rst` stays the one
  curated map, and a second one in `guides.xml` would be a list of 58 entries
  that drifts from it silently.
- Where the sources are is `composer.json`'s `support.source`, which already
  declares the package to everybody else.
- Every push to `main` deploys. A filter that names the paths that can change
  the site is a second statement of what the site consists of.

## Assumed

- That a reader follows a link into the repository rather than expects the entry
  on the site. Nothing measures which of the two they take.
- That what a reader looks for is in the prose. A term that this corpus only
  ever writes inside a fenced block is outside the index. `runTests.sh` was one
  keystroke away from such a term.
- That the way out stays cheap. `Site` writes plain markdown and a renderer only
  consumes it, so a second renderer costs `guides.xml` and the flags around it.
  That is what the two Python ones cost in the measure before this one.

## Wrong if

- A link on the site goes nowhere. A link that left points at a path on GitHub
  that nothing here re-reads, so a renamed decision breaks it silently.
  `fail-on-error` does not catch it either: the renderer reports an unresolved
  reference as a warning, and one warning stands that nobody will remove.
- A search returns the wrong page first, because nothing here ranks beyond the
  title, the headings and how often a word occurs in the prose.
- The repository moves and `support.source` still names where it was.
- A new page lands in the sidebar where nobody looks. The order there is
  alphabetical and the reasoned one is on the map page alone.
- A recorded tool answer reads differently on the site than in the checkout. A
  fenced block acquired a relative link and the rewrite treated it as one. That
  is what `Links` does everywhere else.

## Since then

Three parts of this no longer describe the site, and an entry of its own
replaces each. The stylesheet is a built file rather than inline, because what
weighed 4 KB reached 16 on every page (`D-DOC-019`). The site opens on the
readme with the map below it (`D-DOC-018`). The three commands are one, which
then lost the renderer entirely (`D-DOC-020`, `D-DOC-028`), and
`documentation:prepare` writes the copy and nothing else. The search index went
with the theme. What stays is everything about how `Site` writes the copy and
that it is a copy.
