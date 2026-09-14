---
id: D-DOC-030
title: The front page is a landing page, in the theme's marketing layout
date: 2026-08-12
status: open
coveredBy:
  - SiteTest::theSiteOpensOnTheDocumentationsOwnPage
  - VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine
---

# D-DOC-030 — The front page is a landing page, in the theme's marketing layout

**`documentation/readme.rst` stands in the theme's `marketing` layout: a run of
bands, each with one claim, with the four sections as cards at the foot.**

[`D-DOC-026`](doc-026-the-site-is-the-documentation-and-the-readme-stays-out-of-it.md)
moved the promise onto the front page and left it in the shape of a manual page.
One column beside a rail, and the answer to "is this for me" as prose a visitor
has to read to the end of.

## Evidence

- The shape is the theme's and nothing here had to build it.
  `:layout: marketing` renders a run of full-bleed bands with no rail. `band`,
  `grid`, `teaser`, `card-grid` and `card` are directives
  `typo3/soul-guides-theme` ships, and the manual for them is
  [the theme's own](https://typo3.github.io/soul-design-system/guides-theme/directives.html).
- The field list has to stand above the title. `FieldListRule` takes one as
  document metadata only while it has found no title. Below the title the same
  three words render as a definition list in the body of the page.
- A band opens a section rather than wraps one. What follows it belongs to it up
  to the next band, so the page stands unindented and reads as the page it
  makes. `Bands::of()` is what does the split, through the one section a page
  with an `h1` puts everything inside.
- An unresolved `:href:` on a teaser fails the render. Measured on 2026-08-12
  with one pointed at a tool page that does not exist. The theme's finish step
  named both occurrences and `bin/cli documentation:preview` exited 1.
- The statements are the ones the page already carried. The three sources, the
  fallback, the trust boundary, the English rule and the feedback loop move into
  bands rather than get a rewrite. The four sections stay last.

## Decided

- The page carries no reStructuredText section heading of its own. A band's
  title is its argument, because a heading inside a directive is not a section;
  reStructuredText parses those at document level. The one `h1` is the title of
  the document.
- The six questions a session no longer searches for are a `grid` of teasers.
  Each carries the question as its name and a link to the tool page that answers
  it. A tool name is what the reader learns on that page, not what gets them to
  it.
- What the server will not do is a `grid` of its own, one card per promise. It
  reads, it starts nothing, it stays where it started, it takes queries in
  English. Those were four paragraphs a visitor read past.
- The four sections stay at the foot, as cards. That is `D-DOC-026`'s account as
  it stands: a visitor of the site is a user before they are a contributor.
- `SiteTest::theSiteOpensOnTheDocumentationsOwnPage` holds the field list
  together with the title, because the order of the two is what makes the shape.

## Assumed

- That the theme keeps this vocabulary. The layout, the two grids and the two
  card elements are its own. A release that renames one of them changes this
  page rather than degrades it.
- That a front page does not become a page that announces a capability. Every
  statement on it is one the server has to keep true. `AGENTS.md` names this
  file as the first thing that goes false when one changes.

## Wrong if

- A band title states more than the server does. A page written for a quick read
  is where a claim loses its qualification, and a heading is the line nobody
  rereads against the code.
- A teaser's `:href:` goes stale in a way the render does not catch. It holds
  the ones that no longer resolve, and nothing holds one that resolves to the
  wrong page. `bin/cli links:check` does not read a directive option.
- The prose the bands hold gets shorter every time somebody edits the page, and
  what remains is a brochure the manual has to answer for.
