---
id: D-KNW-152
title: The showitem grammar of a record type is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::theGrammarOfAShowitemItemIsStatedWithWhatItSkips
---

# D-KNW-152 — The showitem grammar of a record type is a subject this server owns

**The corpus states how the core reads a `showitem` item and where a palette's
label lives. It states that the core skips an item that names no column without
a word.**

Regrouping a form is exactly where a field disappears, and nothing reports one.

## Evidence

- `feedback/2026-09-03-235511`. A session that regrouped the fields of a project
  table into palettes and tabs got `tca-formengine` and `tca-core-palette` at a
  best coverage of 0.52. It wrote the strings from memory, and invented a
  functional test to prove them.
- `tca-core-palette` is the opposite direction, an append to a palette the core
  owns, from an override. It says nothing about how to write the palettes
  section of a table of your own.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `AbstractContainer::explodeSingleFieldShowItemConfiguration()` splits an item
  on `;` into field name, label and palette name. `PaletteAndSingleContainer`
  takes the palette legend from that middle segment and falls back to
  `palettes.<name>.label`, which is why the item carries two semicolons.
  `TabsContainer` throws `A --div-- has no label` for a tab without one.
- The silent half is two `continue` statements in the same class. The container
  passes over a field name that is not an array in `processedTca.columns`, in
  the type's own list and inside a palette alike. It logs nothing.
- The container reads `--linebreak--` in the palette's own `showitem` and
  produces a row break. Nothing refuses a wide element inside a palette.

## Decided

- Step 1a, and a hint of its own — `tca-showitem`. To write a form of your own
  and to extend somebody else's are two questions, and each now closes with the
  name of the other.
- The hint states the test the session invented as the guard. That is the
  cheapest thing that catches the silent skip and no core check does.
- Nothing is bound. All four covered branches read the item the same way, and
  the two `continue` statements are the same across them.

## Assumed

- That the label forms a project may write are the ordinary ones. The session
  asked which v14 permits. `sL()` takes an `LLL:` reference or a literal on
  every branch, so there is nothing version-bound to say.

## Wrong if

- FormEngine starts reporting an item that names no column, which would make the
  silent skip the wrong reason for the hint.
- A palette gains a rule about which elements may sit in one, which the fourth
  statement says there is none of.
