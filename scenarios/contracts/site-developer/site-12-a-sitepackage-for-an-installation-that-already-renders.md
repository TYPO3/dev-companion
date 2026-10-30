# SITE-12 — A sitepackage for an installation that already renders

**Environment:** `E-SITE` 14.3 with `typo3/theme-camino` required, and
[`site-11-design/`](site-11-design/index.html) copied to `design/` at the
project root · **Contract:** `open`
**Held by:** `HintsTest::aNewcomersDesignReachesTheHintsOnConvertingIt` and
`SkillTest::theSitepackageSkillConvertsADesignAndCountsTheDocument`, which hold
what the `sitepackage` intent and its skill say about a design, and
`ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout`, which holds
that the prompt below confirms that intent. That a session keeps the content
rendering, rather than writes rows into the database to see a layout, is **not
guarded**. That it leaves the project free of `theme_camino` is **not guarded**
either.

`SITE-11` is this case without the one thing that makes it ordinary. Three runs
of it on 2026-10-29 met all eleven criteria. None of them created a page or a
content element, and all three tested a layout by writing SQL straight into the
database. The installation they ran in had nothing to render, so each session
invented the same workaround.

This case gives the session a site that already answers. `theme_camino` ships
its own content, and the core's `setup` command imports it where the package is
already active. So the package is required before the setup runs, and
`--distribution` is for a package that is not active yet. Measured on v14.3.10,
the content is 7 pages and 50 content elements: a root page, three content
pages, and a folder holding the imprint and the privacy page.

Two things the import writes are worth knowing before a run. The site
configuration it creates carries `base: /camino/`, so the environment sets it to
`/` and the site answers at the project root. It also carries
`dependencies: typo3/theme-camino`, which is the line that puts the camino set
on the site.

**The content is the trap.** Eleven content types appear in it, and nine of them
are camino's own: `camino_hero`, `camino_hero_small`, `camino_hero_text_only`,
`camino_author`, `camino_linklist`, `camino_sociallinks`, `camino_textteaser`,
`camino_textmedia_teaser`, and `camino_textmedia_teaser_grid`. The other two are
`text` and `textpic`. Eighteen of the fifty elements stand on camino's nine.
`theme_camino` registers those types itself, in `Configuration/TCA/Overrides/`,
with fields of its own such as `tx_themecamino_link`.

**The comfortable answer is the wrong one.** A sitepackage that requires
`theme_camino` and extends its set keeps every element rendering. The
installation reports no defect. `sitepackage-layout` forbids it. The theme
belongs to one release line, and it is announced to move out of the core. A
project that depends on it buys that migration for nothing. A session that drops
the set takes the nine types away instead. The elements standing on them lose
what renders them, and what opens them in the backend. Neither way is free, so
this case asks the session to name the cost it takes.

The design is the one `SITE-11` uses. Reusing it keeps the theme and the content
as the only difference between the two cases, so a run that goes differently
says which of the two moved it. The design is a clinic and the content is a
hiking route, which no agency would deliver together. The 2026-10-30 run shows
that trade does not hold. The design has no counterpart for a hero, a teaser, an
author box, or a social links row. So it asks the session nothing about them.
Both runs layered over camino, and neither met the decision this case is about.
The next run needs a design whose shape meets the content.

> Our site runs on the default theme and the editors have already filled it.
> Our agency delivered a static HTML design in the design/ folder. Build it
> into TYPO3 as our sitepackage, so the pages render in our own design and the
> editors keep what they wrote.

**Run 2026-10-30:** two runs against `5fa29d7f`, one with this server and one
without. Both wrote `typo3/theme-camino` into the sitepackage's `composer.json`.
Both declared it a dependency of their own set. Neither said the project is now
tied to it. Both kept the 50 elements rendering. Both wrote one page template
for one of camino's three backend layouts, so five of the seven pages render
camino's page body. The run with this server read the statement that forbids the
dependency and followed none of it.
`feedback/2026-10-30-164400-the-statement-that-forbids-depending-on-theme.md`
records it.

**What the agent needs from this server**

- That the installation already has a theme, what the camino set defines, and
  what a sitepackage of its own does beside it.
- Which content types the pages use, and which package owns each one, so the
  session sees what it takes away before it takes it.
- What the design maps onto: a page template per page type, the header,
  navigation and footer as partials, and the variable parts as content.
- Where a sitepackage comes from and how the project requires it, as in
  `SITE-10`.

**What has to come out of it**

- Every page that rendered before renders after. No page comes back empty, and
  no element renders as an unstyled fallback.
- The sitepackage does not require `theme_camino` or declare it a set
  dependency. Where it does, the answer says why, and what that costs the
  project later. A dependency nobody states is a failure whatever renders.
- The session names what the eighteen camino-typed elements owe. The theme owns
  the types, so leaving it later is a content migration rather than a template
  change.
- The design's frame reaches the pages that are there. The session does not
  build a second site beside the content.
- The main navigation renders from the page tree, with the active page marked.
  No link of `design/` survives as a hardcoded URL.
- Each rendered page holds one `<html>`, one `<head>` and one `<body>`. The
  partials carry no document frame of their own.
- The stylesheet is an asset of the sitepackage and is included once. The design
  folder itself is not what the site serves.
- The answer names each part of the design and where it went, and what it leaves
  for the editors.

**How it fails**

- The session writes rows into the database to see a layout, where the content
  is already there.
- The sitepackage requires `theme_camino` or extends its set. The answer never
  says the project is now tied to a theme of one release line.
- The camino set is dropped and the nine content types it owns lose their
  templates, with nothing in the answer about it.
- A page comes back empty, or an element renders as raw fields, and the run
  reports success.
- The session builds a page tree of its own and leaves the imported one
  unrendered.
- The design pasted whole: a partial with its own `<!DOCTYPE html>`, `<head>` or
  `<body>`, or `config.disableAllHeaderCode` set to hide a second document.
- The navigation copied as static links from `design/`.
- The same failures as `SITE-11`.
