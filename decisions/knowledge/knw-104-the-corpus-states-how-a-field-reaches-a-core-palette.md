---
id: D-KNW-104
title: 'The corpus states how a field reaches a core palette'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::addingToACorePaletteIsStatedAsTheCall
---

# D-KNW-104 — The corpus states how a field reaches a core palette

**`tca-core-palette` states the call, what it does to the string core wrote, and
why the concatenation fails in silence. The rule is unbound, the new shape that
broke it bound.**

The gap is
[`D-KNW-103`](knw-103-how-an-extension-adds-a-field-to-a-core-palette-is-a-subject-this-server-owns.md),
and what closes it is a statement in `tca.json` rather than the per-version TCA
dump the feedback asked for.

## Evidence

- The remedy is the same on every covered branch. `addFieldsToPalette()`,
  `executePositionedStringInsertion()` and `removeDuplicatesForInsertion()` are
  byte for byte identical on `.checkouts/12.4`, `13.4`, `14.3` and `main`. A
  diff of the three bodies against `14.3` on 2026-08-18 read that. So the call
  carries no bound.
- What the call does to a palette that exists is the half that survives a new
  shape. It trims `", \t\n\r\0\x0B"` off the end of core's string before it
  joins the insertion on with `', '`. So a string with no comma at the end
  cannot fuse the appended item onto core's last one. The call deduplicates the
  insertion against what the palette already carries, and exempts
  `--linebreak--` from that by name. That is what lets one call add a line break
  and a field at once. It is what leaves two line breaks where two overrides
  each add one.
- The call creates a palette that does not exist rather than refuses it.
  `$paletteData = &$GLOBALS['TCA'][$table]['palettes'][$palette]` auto-vivifies,
  and the only guard is that the table is in `$GLOBALS['TCA']`. So a palette
  name core has changed is a second silent failure with the same symptom.
- Why nothing reports the loss.
  `PaletteAndSingleContainer::createPaletteContentArray()` reads the palette
  with `trimExplode(',', …, true)` and `continue`s over an item whose field name
  is no column of the table, and logs nothing. The fused item names no column,
  so it takes core's own last field out of the form with it. The same call drops
  the empty item a double comma leaves.
- The new shape goes far past `tt_content`'s `frames`. Counted on 2026-08-18
  over every shipped `Configuration/TCA/*.php` below `typo3/sysext`, with the
  fixtures under `Tests/` left out. Of the palettes that declare a `showitem`,
  38 of 73 carried a per-field label on `12.4` and 35 of 97 on `13.4`. Against
  that stand 5 of 110 on `14.3` and on `main`. Those five are the short form —
  `starttime;core.db.general:starttime` — and every `LLL:EXT:` path left on
  `14.3` is in a functional-test fixture.
- The end comma moved per palette rather than per version, which is why the hint
  states the rule and not the string. Six shipped palettes ended in one on
  `12.4` and on `13.4` and five do on `14.3` and on `main`. `tt_content`'s
  `general` and `frames` lost theirs, and `tx_scheduler_task`'s `execution`
  gained one. The multi-line shape thinned the same way and no further. From 19
  palettes on `12.4` to 15 on `13.4` and 11 on `14.3` and on `main`.
- The four queries `D-KNW-103` recorded the gap on all reach the statement,
  probed on 2026-08-18. The symptom a session arrives with, a field absent from
  the backend form after a TCA override, reaches it first.

## Decided

- `tca.json`, beside `tca-formengine`, rather than `content-elements.json`. The
  subject is a palette on a core table, `pages` as much as `tt_content`, while
  `content-elements` is the registration of a record type. `D-KNW-030` makes a
  hint one question.
- `appliesTo` carries `tt_content palette` beside the bare `palette` and
  `showitem`. Without it the own query of the session that reported put
  `content-elements` first, which answers a different question. With it the
  answer returns both and the palette statement leads.
- The rule is unbound and the new shape is `since: 14`. An `until: 13`
  counterpart says the append works there and is the thing that breaks on the
  upgrade. A caller on an LTS is the one who can still act on it.
- The hint quotes no palette string as what a table says today. It names what
  the rewrite did to the labels, the line breaks and the comma. That is
  `D-KNW-103`'s third **Wrong if** answered rather than accepted.
- The version question keeps the route it had. The bound statement names
  `typo3_changelog_lookup` and does not restate the entry, because a changelog
  number in a hint dates it against one branch.
- No TCA structure tool, unchanged from `D-KNW-103`. What the archaeology
  established is a rule, and the rule fits in a hint.

## Assumed

- That the fused item is the whole of the loss. This run read the drop off
  `PaletteAndSingleContainer` rather than rendered it. So no functional test
  stands behind the claim that the appended field itself survives while core's
  last one does not.
- That the caller arrives on the palette words. The probes are this session's
  own queries and those of the session that reported. A caller who writes
  neither reaches the hint through its body alone.

## Wrong if

- A next session with a field missing from a content element form reports that
  what it needed was the per-version palette after all. The rule would not have
  been the lever, and the tool `D-KNW-103` declines would be.
- Core reshapes its palettes again inside a major, and the bound statement
  describes a shape that is gone while reading as current.
- The `showitem` keyword pulls `tca-core-palette` above `content-elements` on a
  question about the registration of a record type. The two would be one subject
  after all, or the keyword belongs only to the palette hint's body.
- `addFieldsToPalette()` diverges on one covered branch — a position argument
  that moved, an insertion that deduplicates where another does not. The unbound
  half would then be wrong on one of the four.
