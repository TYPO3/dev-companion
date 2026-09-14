---
id: D-DOC-018
title: 'The site opens on the readme'
date: 2026-08-09
status: revoked
revokedBy: D-DOC-026
coveredBy: []
---

# D-DOC-018 — The site opens on the readme

**The published site's front page is the repository's own `readme.md`, and
`documentation/readme.rst` sits below it as `how-the-work-is-done.md`.**

Somebody who arrives at the site decides whether this server is for them. What
they saw was the map of how this repository works on itself.

## Evidence

- The site published `documentation/` alone, so its front page was
  `documentation/readme.rst`. Its first sentence is "One page per procedure that
  is long enough to get wrong from memory". Nothing on it says what the server
  is, what it answers or how to install it.
- All of that stands in `readme.md` at the root. What the server does, the three
  sources it answers from, the trust boundary, the quickstart, the tool surface
  and the resources. The site carried none of it.
- Writing a second entry page below `documentation/` would put the promise
  paragraphs in the checkout twice. Those are what goes false first when a
  capability changes, and two copies of them go false separately.
- The readme's own links are eight. Every one of them is either external, a page
  the site serves, or `AGENTS.md`, which the copy already rewrites to GitHub.
- The readme named three of its links by their path —
  `documentation/server/tools/` as the text of a link to
  `documentation/server/tools/readme.rst`. On the site those paths do not exist,
  because `documentation/` sits at the root.
- Which TYPO3 lines the server answers for was in no sentence of the readme, and
  it is the first thing a user checks. `knowledge/versions.json` declares it, so
  a test can hold the sentence to it.

## Decided

- Site::FRONT is `readme.md`, published as `index.md`. The map keeps its place
  in the tree and goes out under its own title. So every directory here can
  still call its own page `readme.md`.
- A link resolves against the repository and gets its name again from the
  published path, rather than a swap of the last segment in place. The front
  page sits a directory above the tree the rest of the site is. So the old rule,
  only the last segment moves, held for no link it writes.
- The three links the readme named by path now carry the name of what they are.
  That is the better sentence in the checkout too, which is why it is a rewrite
  and not a rule about the copy.
- The quickstart names the covered lines, and
  `VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine` holds them to
  `knowledge/versions.json`.
- The sidebar runs front page, then the pages that belong to no subject, then
  each subject. Sorted by path alone a loose page lands between two directories
  and reads as part of whichever one it fell into.

## Assumed

- That a visitor of the site is a user first. Nothing measures who arrives
  there; what stands known is that the readme serves them and the map does not.
- That nobody has a deep link into the site. `documentation/readme.rst` went out
  as `index.html` and is now `how-the-work-is-done.html`, and this is a 0.x
  package whose surface has moved before.

## Wrong if

- A reader of the checkout no longer finds the map, because prose now names it
  as a published page rather than as `documentation/readme.rst`.
- The front page grows the sections a site wants and the readme no longer reads
  as a GitHub front page. One file that serves two places is what makes that
  possible and what would make it a compromise.
- A new covered line arrives and the readme names it while the server does not
  answer for it. The test holds the sentence to the declaration in one direction
  only.

## Revoked on 2026-08-12

By its own second **Wrong if**, read the other way round. It saw that one file
for two places would become a compromise and expected the readme to grow the
sections a site wants. What happened instead is that the site kept a front page
written for GitHub. The manual had no page that says what the server is.

The ground it stood on was that a second entry page would duplicate the promise
paragraphs. It would have, as a copy: a move of them costs nothing twice, and
`D-DOC-026` is that move. What outlives it is everything it found absent on the
map, and the account that put it there. A visitor is a user before they are a
contributor. Its rule for how a link resolves outlives it too.
