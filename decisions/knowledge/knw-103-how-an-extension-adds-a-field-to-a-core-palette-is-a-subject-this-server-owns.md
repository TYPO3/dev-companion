---
id: D-KNW-103
title: 'How an extension adds a field to a core palette is a subject this server owns'
date: 2026-08-18
status: confirmed
---

# D-KNW-103 — How an extension adds a field to a core palette is a subject this server owns

**Nothing below `knowledge/` says that a core palette's `showitem` is core's
string, or that `ExtensionManagementUtility::addFieldsToPalette()` is the way to
add to one.**

So an extension appends to it with `.=`, which held for years and stopped on 14,
and nothing reports the loss. The appended field and its line break leave the
backend form and nothing raises an error anywhere. The feedback goes to the
queue at `normal`.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  reaches `content-elements` on `"add a field to a core tt_content palette"` and
  on `"addFieldsToPalette tt_content frames"`. It reaches `tca-formengine` on
  `"appending to core TCA showitem string breaks"`. It reaches nothing at all on
  `"extend core palette showitem with a linebreak"`, where all 98 hints come
  back as the index.
- The subject is absent rather than thin. The word `palette` occurs in no file
  below `knowledge/hints/`, and `addFieldsToPalette` occurs nowhere below
  `knowledge/` or `skills/`. The two hints the probe reaches say nothing about a
  palette. `content-elements` states how a record type registers its own
  `showitem`, and `tca-formengine` states four conventions for a TCA change in
  the core.
- The route exists and lands where the statement is not. `task-intents.json` has
  `tca-field`, whose `match` carries both `palette` and `showitem`. Its
  checklist sends the caller to `typo3_schema_lookup`, `typo3_label_lookup` and
  the three TCA hints. None of them tells a palette the extension owns from one
  the core does.
- The version claim holds, read on the checkouts. In
  `typo3/sysext/frontend/Configuration/TCA/tt_content.php` the `frames` palette
  is a multi-line string on `13.4`. Every item carries a
  `;LLL:EXT:frontend/…_formlabel` suffix and the last one a comma at the end. On
  `14.3` it is one line of four bare field names that ends in
  `space_after_class`, with neither. Concatenating a `--linebreak--` item fuses
  `space_after_class` and `--linebreak--` into one item that names no field.
- The remedy holds on all four covered checkouts. `addFieldsToPalette()` is
  there on `12.4`, `13.4`, `14.3` and `main`, and it routes a palette that
  exists through `executePositionedStringInsertion()`.
  `removeDuplicatesForInsertion()` exempts `--linebreak--` from deduplication on
  every one of them.
- The tool already answers the changelog half of the suggestion, and the
  feedback asked for a check of it. Measured with `TYPO3_DEV_COMPANION_ROOT` on
  `.checkouts/14.3`. `typo3_changelog_lookup` with `query: "showitem"` and
  `version: "14"` returns exactly
  `14.0 Breaking: Core TCA and user settings showitem strings use short form references (#107789)`.
  `query: "space_after_class"` returns the same entry through the identifiers in
  its body. The feedback's own query, `"tt_content palette showitem labels"`,
  misses and names `showitem labels` as the query that would hit. That is
  `D-ANS-016` at work.
- That entry lists the removal this bug ran into,
  `space_after_class;LLL:…:space_after_class_formlabel` → `space_after_class`,
  among some fifty field-label overrides. It states the impact as code that
  copies or rewrites core `showitem` strings. What it does not state is the new
  shape around them. The palette became one line and lost its comma at the end,
  which is the half that fuses the appended item.
- The session could have reached it from where it stood.
  `todo/reference/which-checkout-plays-which-environment.md` records TYPO3
  14.3.0 below `.build/vendor` in `/home/benji/projects/bootstrap_package`, so
  the installation `typo3_changelog_lookup` needs was there.

## Decided

- Step 1a, taken on, and a todo rather than the spot. This entry read what holds
  about TYPO3 as evidence. The statement, its bound and its test are the
  curation, which [`judging.rst`](../../documentation/records/judging.rst) puts
  on the todo's side whatever its size.
- `normal` rather than the `low` the card arrived at. Nothing in the corpus
  states the rule, and the failure it prevents is silent.
- Not `high`. One session reported it, and the gap is a statement rather than a
  capability.
- Nobody builds a TCA structure tool. What the feedback asks for is a core
  table's types and palettes as a version defines them, in a diff across two
  majors. That needs per-version TCA that a published package does not have.
  `.checkouts/` is this repository's own and gitignored, and an installation can
  only answer for the version it is.
- The version question keeps the route it has. One call to
  `typo3_changelog_lookup` returns the entry, which is the round-trip test
  `D-FBK-027` sets. What the archaeology actually established, that a core
  palette is not a stable string, is a rule rather than a dump.
- `typo3_schema_lookup` stays where `D-DIS-008` put it. It answers what the core
  derives for a table from its TCA, and a palette is the form side of the same
  configuration. A wider tool would answer two questions with one output schema.
- The report stays whole rather than trimmed. This entry records what the
  verification answered. A feedback is a session's account, and a sentence cut
  out of it removes the evidence rather than the claim.

## Assumed

- That one statement covers it. This run read only `tt_content`'s `frames`
  palette across the two majors. Whether every core palette lost its end comma
  and its label suffixes in the same commit is unread.
- That the caller arrives on the palette words. The session that filed this held
  a `Configuration/TCA/Overrides/tt_content.php` and a field that had vanished
  from the form. Where the writer curates the statement decides whether the next
  one reaches it.
- That the changelog measurement transfers. This run made it against a core
  checkout that stood in for an installation. The assumption is that a Composer
  installation of 14.3 ships the same `Documentation/Changelog` its core package
  does.

## Wrong if

- A next session with a field missing from a content element form reports that
  what it needed was the per-version palette after all. The rule would not have
  been the lever, and the tool this entry declines would be.
- `addFieldsToPalette()` turns out to behave differently on a covered version —
  a position argument that moved, an insertion that deduplicates where another
  does not. A statement written unbound would then be wrong on one of the four.
- Core reshapes palettes again inside a major. A statement that names what `14`
  looks like would go stale between two minors. The hint would have to state the
  rule and never the string.
- A query naming a content element or the TCA conventions stops reaching
  `content-elements` or `tca-formengine` once the statement lands. The boundary
  between the three would be wrong.

## Confirmed on 2026-08-18

The first **Assumed** was the open one, and the sweep it asked for settles it in
the statement's favour. A session read every shipped core palette on all four
checkouts, not only `tt_content`'s `frames`. The per-field labels went from 38
of 73 palettes on `12.4` and 35 of 97 on `13.4` to 5 of 110 on `14.3`. `main`
has the same 5 of 110. All five are the short form. The end comma did not move
with them. Six palettes carried one on both older branches and five do on both
newer ones. `tt_content`'s two lost theirs and `tx_scheduler_task`'s `execution`
gained one. So a statement that names the comma as a property of a major would
have been wrong on the day of its write.

The second **Wrong if** does not hold. `addFieldsToPalette()` and the two
functions under it are byte for byte identical on `12.4`, `13.4`, `14.3` and
`main`, so the remedy needed no bound.

## Since then

The work this entry queued landed: `tca-core-palette` names the call, and
[`D-KNW-104`](knw-104-the-corpus-states-how-a-field-reaches-a-core-palette.md)
is what states it. Its **Wrong if** is a different list. What can go wrong from
there is a statement gone stale as core reshapes again, and a keyword that
claims a neighbour's question.
