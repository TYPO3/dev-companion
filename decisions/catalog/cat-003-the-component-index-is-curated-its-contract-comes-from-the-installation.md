---
id: D-CAT-003
title: The component index is curated; its contract comes from the installation
date: 2026-07-30
status: open
---

# D-CAT-003 — The component index is curated; its contract comes from the installation

**Where the caller targets the active installation and its backend CSS is
present, the installed files decide the component contract.**

The curated index stays the searchable subset and the complete fallback.

The component catalog sat pinned to one core revision on purpose, with
`bin/cli catalog:check` as its guard. In the first `EXT-04` run that safeguard
was not enough. The installation was TYPO3 14.3.5 and the snapshot described
main. The session made about twenty-five shell reads into the installed core
before it trusted the markup and classes it already had.

## Evidence

- Composer installations ship `EXT:backend/Resources/Public/Css/backend.css`.
  They do not ship the repository-level `Build/Sources/Sass/` tree. The compiled
  artifact is also the contract the browser receives, and it includes classes
  generated from Sass maps that a source-text search misses.

## Decided

- When `targetVersion` is the active installation and its backend CSS is
  present, installed package files win. The compiled CSS decides component
  presence, classes, and custom properties. Installed JavaScript decides
  custom-element presence. The first installed styleguide example that matches
  replaces bundled markup. Every answer names the files and exact TYPO3 version
  it read.
- Derivation does not replace curation. Names, summaries, keywords, the
  component boundary, and the association with a styleguide page are judgments
  rather than an inventory a stylesheet contains. The bundled catalog stays that
  searchable subset. It stays the complete fallback when no installation is
  readable, its package evidence is incomplete, or the caller targets another
  major.

## Assumed

- An `sg:example` with the root class or custom element in it is a copyable
  example of that component. On a dedicated component page whose examples render
  through a ViewHelper instead of spell the class, its first example is the
  installed usage contract. Where the installed template has no example at all,
  the answer keeps the bundled markup and labels it as fallback. It does not
  pretend that it came from the installation.

## Wrong if

- Component state comes into existence only at runtime and appears in neither
  the compiled CSS nor installed JavaScript. Or a styleguide template's first
  example that matches is page scaffold rather than component markup. The former
  needs another installed source. The latter needs an explicit selector in the
  curated index rather than a more permissive extractor.

## Since then

The second half happened and the extractor was the wrong place to look. Run over
all entries against two checkouts, five demos hand back scaffold as the
installed markup. Every one of them carries the root class correctly, so there
is nothing to be stricter about. `demoSelector` is the entry's say instead, with
the same `carries()` check as the root class. It narrows and never widens. A
selector no example carries derives nothing, and the answer keeps the bundled
markup as a fallback rather than reverts to the scaffold.

## Since then

A selector cannot fix four of the five. They name the component nowhere outside
the demo layout, so a selected example would only move which scaffold the answer
hands over. `demoDerives` is what says the demo shows the component nowhere
copyable. It is a second field rather than a `demoSelector` of `false`, which
would be two rules read off one place. The check does not read an entry that
derives nothing at all, so its demo is not among its `sourceFiles`. The
permissive extractor and a scaffold blocklist both lost. They would decide by a
pattern what the index decides by a read. Nobody has tried the first half of the
**Wrong if**.

## Since then

Re-read on 2026-08-22. `bin/cli catalog:check` reports every demo as its entry
recorded it, the four `demoDerives` entries included. So the second half of
**Wrong if** stays where the two sections above left it. Nobody has tried the
first half, for the reason it always was. State that exists only at runtime
needs an installation to look in, and a checkout cannot show it.
