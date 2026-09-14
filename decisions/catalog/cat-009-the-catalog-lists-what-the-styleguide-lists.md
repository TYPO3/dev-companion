---
id: D-CAT-009
title: The catalog lists what the styleguide lists
date: 2026-08-24
status: open
coveredBy:
  - CatalogTest::aComponentNoStyleguidePageDemonstratesIsNotAnswered
  - CatalogTest::anElementIsNotOfferedBeforeItsDemoWroteIt
  - CatalogTest::anElementNoDemoWritesIsNotOffered
  - CatalogTest::anElementTheQueryNamesIsOfferedAsTheWayIn
  - CatalogTest::everyEntryNamesTheActionsThatDemonstrateIt
  - CatalogTest::noEntryAnswersForAMajorItsStyleguideDidNotListItOn
---

# D-CAT-009 — The catalog lists what the styleguide lists

**A component is in the catalog because the styleguide lists it, and the entry
names the actions that demonstrate it.**

`D-CAT-004` selected the index on what the core files as a component, a Sass
partial, a custom element. That is a structural criterion. The maintainer named
on 2026-08-24 what it admits. Some of what the backend styles it keeps to
itself, and nothing in the catalog said which.

## Evidence

Read on 2026-08-24 against `.checkouts/14.3` and the derived files.

- The rule is the maintainer's. What the styleguide lists is public API, and
  what it does not list is not for use or suggestion.
- The listing is `$allowedActions` in the styleguide's `ComponentsController`,
  31 components on 14.3 and 23 on 13.4, in a committed file.
- **The index is wrong in both directions.** Sixteen of the twenty-six entries
  match an action by name. Five entries have no page anywhere: `dropzone`,
  `module`, `note`, `popover`, `recordsearchbox`. Fifteen listed components have
  no entry at all.
- A count of what a page writes settles four of the other five. `btn-group`
  appears 16 times in the buttons page, `form-check` 177 times in checkboxes and
  61 in radio, and `typo3-backend-progress-bar` 26 times in progress indicators.
- The maintainer settles `alert` instead. It appears in no candidate page,
  because the flash messages page writes only its own container and the markup
  comes from a renderer. The class belongs to that.
- `callout` and `infobox` are two entries on one `rootClass`. The first carries
  eleven classes and no page, the second a page and no classes.
- Thirteen of the 137 custom elements stand literally in a styleguide template.
  A tag survives that read where a class name does not. A demo builds class
  names in an `f:for` loop and never builds a tag name.

## Decided

- An entry names the styleguide actions that demonstrate it, as a list. A
  component demonstrated on two pages is one entry that names both, which
  `form-check` is. A split would divide one `rootClass` between two entries.
- The five entries no page demonstrates come out. What they carried gets no
  answer rather than an answer with a warning. A mark that says "not public" and
  hands the class over anyway reads as the class.
- `callout` and `infobox` become one entry, under the name the styleguide uses,
  with the classes `callout` had.
- The fifteen listed components without an entry get one. Their titles,
  summaries and root classes come as drafts from the templates and the Sass, and
  the maintainer corrects them. A derivation supplies everything else
  (`D-CAT-008`).
- The custom elements become an answerable dimension of their own, out of
  `component/elements.json`. Nobody can attach an element to the wrong node, so
  where one exists it is the answer and the class is the way round it.
- Rejected: the five with a mark. Rejected too: one entry to one action, which
  would leave a caller who asks about radio buttons with nothing.

## Assumed

- That the listing is the whole of the boundary. A component the core offers and
  never demonstrates is not in it, and nobody has looked for one.
- That `alert` is the flash message markup. The template does not show it and
  nobody read the renderer. The maintainer answered it, and a read of the flash
  message renderer on a branch is what would confirm it.
- That a tag written in a template is a demonstrated one. Thirteen came back
  that way, and an element a page instantiates from JavaScript would not.

## Wrong if

- A caller asks about one of the five and the miss costs them. `module` is the
  likeliest. The chrome around a backend module is what a module's own template
  sits in, and a session that authors one has asked before.
- One of the fifteen new entries is wrong in a way the derivation cannot catch.
  A `rootClass` that is not the root, a summary that describes the page rather
  than the component.
- The listing turns out to be a menu rather than a boundary, and something on it
  is internal. Then what the styleguide lists is evidence and not the rule.

## Since then

The third **Wrong if** is partly real. Of the eleven uncovered actions two are
not components at all. Four demonstrate a web component and the element
dimension answers them, and two already had coverage under another name. So the
listing is a menu of pages with a few non-components among them, and the rule
holds with those counted. Two entries stayed to write.

The same read settled the three open questions. **A listed component makes its
classes answerable**, because the entry is the unit. A per-class mark existed,
measured wrong in both directions and went out again. **Below the oldest major a
styleguide ships on, the selection made above it applies.** And the boundary
binds by version, which
`CatalogTest::noEntryAnswersForAMajorItsStyleguideDidNotListItOn` now holds.
