---
id: D-DOC-028
title: 'The renderer is a build tool this repository carries none of'
date: 2026-08-12
status: confirmed
coveredBy:
  - DocumentationPrepareTest::oneCallWritesTheCopyAndSaysWhereItWent
  - DocumentationPrepareTest::whatTheDocumentationNoLongerHasIsReported
  - DocumentationPreviewTest::aFailedStepStopsThePreviewAndQuotesTheCommand
  - DocumentationPreviewTest::aRendererThatIsAlreadyThereIsNotFetchedAgain
  - DocumentationPreviewTest::onePreviewFetchesTheRendererThenRendersThenFinishes
---

# D-DOC-028 — The renderer is a build tool this repository carries none of

**`bin/cli documentation:prepare` writes the copy and is the whole of what this
repository does to a render. The renderer and its theme are required into a
directory outside the checkout by whoever renders, so `build/` is gone and
nothing about them is committed.**

What this repository owns is the copy: 76 files, and every link that leaves the
tree rewritten to the file on GitHub. The renderer is a build tool that turns
that copy into HTML, and it was carried here as a manifest, a lock, a gitignored
vendor directory and four steps of a command.

## Evidence

- Requiring the theme into an empty directory resolves in 3.4 seconds on a warm
  cache and 5.1 from nothing, measured on 2026-08-12. Whatever the committed
  manifest and lock were buying, it was not time.
- The copy needs no renderer, no theme, no node and no network. It was the one
  step of `documentation:render` that could run anywhere, and it was behind the
  three that could not.
- Nothing on the rendered site references a dark twin: zero `<img>` sources
  across 51 pages name one. Site::publishDrawings() was a step that copied
  eleven files nobody asks for, and the twins are in `documentation/images/` and
  reach the copy like every other file.
- Resolved into this package's own `require-dev` the renderer would add 34
  packages to every `composer install` — 84 in the root, 56 in the renderer, 118
  together, and no conflict. A directory outside the checkout costs none of that
  and needs no manifest here to say so.

## Decided

- `documentation:render` is gone, and `documentation:prepare` is what replaces
  it. `documentation:publish` was written for the dark twins in the same change
  and deleted again when they turned out to be referenced by nothing.
- `Site` keeps the copy and loses the rest: `RENDER`, `FINISH`, `finish()`,
  `installs()`, `run()`, `publishDrawings()` and the `CommandRunner` seam. It
  starts no process at all now.
- The workflow runs the steps itself: install this package, prepare, require the
  renderer into `${{ runner.temp }}/renderer`, render and finish with that path.
- `documentation:preview` is the same sequence for one machine, and is what
  keeps a local render one command. It fetches the renderer into
  `.site/renderer` where there is none and reuses it after, because a preview is
  run again after every paragraph. Deleting `.site/` is how a preview takes the
  theme as it stands; a deployment always does.
- The theme is required as `dev-main` and no lock is kept, so a render takes it
  as it stands. It is this account's own repository, and a constraint replaces
  `dev-main` the day it carries a tag.

## Assumed

- That a render is reproducible enough without a lock. Two renders a week apart
  can differ, and what tells them apart is the theme's own history rather than
  anything recorded here.
- That whoever renders locally reads the page that says how. `D-DOC-020` made
  one command out of six for exactly the reason this splits it again, and what
  it feared was a recipe nobody could follow.

## Wrong if

- The two sequences drift. `documentation:preview` and the workflow run the same
  three steps, and nothing compares them.
- A preview shows a theme nobody is deploying, because `.site/renderer` is
  months old and nothing says so.
- The site breaks and the checkout cannot say what changed, because there is no
  recorded commit of the theme to go back to.

## Confirmed on 2026-09-05

The theme carries tags now, `v0.3.1` the newest, so the fetch names it with no
constraint and takes its newest release — in `documentation:preview` and in the
workflow alike. A constraint would have to be raised by hand each release, and
the day one was proposed the maintainer said to name none. What this changes:
the third **Wrong if** is answered, because a release is a commit somebody can
go back to, and `rm -rf .site/renderer` is still how a preview takes the newest
one.
