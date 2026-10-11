---
id: D-ANS-177
title: A property answers from what its manual states of it
date: 2026-10-11
status: open
coveredBy:
  - DocumentationTest::aPropertyIsAnsweredFromWhatTheManualStatesOfIt
---

# D-ANS-177 — A property answers from what its manual states of it

**A `std:confval` hit of `typo3_documentation_lookup` reads the manual's
`confvals.json` once per manual and version. It answers with the type, the
default, whether it is required and the versions it changed in, as `property`
and in the excerpt.**

`D-ANS-144` reached the property and read the whole page it is a section of, for
two sentences of it. The TCA reference documents hundreds of properties on a
handful of pages.

## Evidence

- **render-guides 0.45.0, released 2026-10-05**, writes `confvals.json` beside
  every manual it renders. Read on 2026-10-11: TYPO3 Explained, TypoScript
  Explained and the TCA reference answer it at `main`, 14.3 and 13.4. The
  ViewHelper reference answers 404 and publishes `viewhelpers.json` instead.
- **The file keys each property by the anchor the inventory names.**
  `confval-types-columnsoverrides` is the anchor of
  `Types/Index.html#confval-types-columnsoverrides` and the key in the file. The
  entries carry `name`, `type`, `default`, `required`, `summary`, `versions` and
  `fields`, and every one of them is optional.
- **Read over stdio on 2026-10-11.** `columnsOverrides` at 14.3 answered its
  type and summary, and `opcache.save_comments` at 13.4 its default, without a
  page read.

## Decided

- `property` is a field of every result, null for a page. The excerpt says the
  same in a sentence, so a reader of the text half loses nothing.
- A manual without the file, or a file without the anchor, answers from the page
  as `D-ANS-144` decided. A version rendered before the release is the first
  case.
- `fields` stays out. Its keys are the manual's own labels, `TCA path` and
  `Scope` among them, and no schema can name them.
- The maintainer chose on 2026-10-11 to keep `knowledge/manuals.json`. The
  host's `manuals.json` keys the core changelog as `typo3/cms-core` rather than
  by its shortcode `changelog`, and it lists no render-guides manual.
  `T-260926-aefe` waits on that.

## Assumed

- That the anchor in the inventory and the key in the file come from the same
  writer and stay equal.

## Wrong if

- A property hit answers from the page where the manual publishes the file. Then
  the anchor and the key differ.
- A session reports a property answer that leaves out what the page said and the
  file did not, often enough that the page read was worth its cost.
