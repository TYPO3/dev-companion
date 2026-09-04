---
id: D-KNW-152
title: The showitem grammar of a record type is a subject this server owns
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::theGrammarOfAShowitemItemIsStatedWithWhatItSkips
---

# D-KNW-152 — The showitem grammar of a record type is a subject this server owns

**The corpus states how a `showitem` item is read, where a palette's label
lives, and that an item naming no column is skipped without a word.**

Regrouping a form is exactly where a field disappears, and nothing reports one.

## Evidence

- `feedback/2026-09-03-235511`. A session regrouping the fields of a project
  table into palettes and tabs got `tca-formengine` and `tca-core-palette` at a
  best coverage of 0.52, wrote the strings from memory, and invented a
  functional test to prove them.
- `tca-core-palette` is the opposite direction — appending to a palette the core
  owns, from an override — and says nothing about writing the palettes section
  of a table of your own.
- Read in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `AbstractContainer::explodeSingleFieldShowItemConfiguration()` splits an item
  on `;` into field name, label and palette name. `PaletteAndSingleContainer`
  takes the palette legend from that middle segment and falls back to
  `palettes.<name>.label`, which is why the item is written with two semicolons.
  `TabsContainer` throws `A --div-- has no label` for a tab without one.
- The silent half is two `continue` statements in the same class: a field name
  that is not an array in `processedTca.columns` is passed over, in the type's
  own list and inside a palette alike, with nothing logged.
- `--linebreak--` is read in the palette's own `showitem` and produces a row
  break; nothing refuses a wide element inside a palette.

## Decided

- Step 1a, and a hint of its own — `tca-showitem`. Writing a form of your own
  and extending somebody else's are two questions, and each now closes by naming
  the other.
- The test the session invented is stated as the guard, because that is the
  cheapest thing that catches the silent skip and no core check does.
- Nothing is bound. All four covered branches read the item the same way, and
  the two `continue` statements are unchanged across them.

## Assumed

- That the label forms a project may write are the ordinary ones. The session
  asked which are allowed on v14, and `sL()` takes an `LLL:` reference or a
  literal on every branch, so there is nothing version-bound to say.

## Wrong if

- FormEngine starts reporting an item that names no column, which would make the
  silent skip the wrong reason for the hint.
- A palette gains a rule about which elements may sit in one, which the fourth
  statement says there is none of.
