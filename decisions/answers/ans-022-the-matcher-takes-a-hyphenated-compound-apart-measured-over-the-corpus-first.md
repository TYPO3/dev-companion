---
id: D-ANS-022
title: The matcher takes a hyphenated compound apart, measured over the corpus first
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::aCompoundIsFoundWhicheverWayTheCallerJoinsIt
---

# D-ANS-022 — The matcher takes a hyphenated compound apart, measured over the corpus first

**A query that writes `content-element` reaches nothing that `content element`
reaches, and both halves of the hint match fail on the hyphen alone.**

The spelling is not exotic. It is how the session that reported it wrote its own
task down. It is how this repository spells the id of the hint it wanted.

## Evidence

- One measurement, one character apart.
  `bin/cli hints:probe "show assigned related groups in a backend content element preview template"`
  returns `content-elements` at `appliesTo(15) + text(181)`. The same sentence
  with `content-element` matches nothing and returns 40 hints as the index.
- The keyword half. `ArchitectureHints::scoreKeywords()` looks for the pattern
  in the query rather than the query in the hint. A pattern of bare words goes
  through `TermSearch::carries()`, which anchors it at a word boundary. So the
  search looks for the `appliesTo` pattern `content element` verbatim, and a
  hyphen in the query is a miss.
- The term half. `TermSearch::terms()` splits on `[^\p{L}\p{N}_.-]+`, so a
  hyphen stays inside the word, and `stem()` then cuts anything longer than six
  characters. `content-element` becomes `conten`, which is what `content`
  becomes on its own. `element`, the term that discriminates, never enters the
  query at all.
- It is general, and the shorter compounds fail the other way. `site-package`
  becomes `site-p` and `fluid-templates` becomes `fluid-`; those stems keep the
  hyphen, so they reach only text hyphenated the same way. Asked as a query,
  `content-elements` returns `extension-files` and `sitepackage-layout` ahead of
  the hint of that name, all three at
  `text only(52)`.
- The separators stay on purpose, and the rule is not that they should go.
  `mod.web_layout`, `list_type` and `tt_content` are one token each, and
  `tt_content preview template` reaches `content-elements` at
  `appliesTo(10) + text(129)` because of it. What has no owner is the compound a
  caller hyphenates where the corpus spells it apart.

## Decided

- Recorded and queued rather than fixed here. It is `src/`, and it is the
  matcher two tools and the whole hint corpus go through.
  [`D-KNW-009`](../knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md)
  is the standing evidence that a wider match trades a miss for a wrong hit
  unless a measurement over the corpus comes first.
- Not [`D-ANS-006`](ans-006-an-identifier-is-found-however-it-is-spelled.md).
  That rule is in `LabelSearch::carryingEvery()`. It removes separators so the
  search finds an identifier in every spelling, and neither the hint terms nor
  the `appliesTo` patterns go through it.
- Not the reason for the feedback. The hint the query missed carries no answer
  to it either — that is
  [`D-KNW-014`](../knowledge/knw-014-the-record-a-v14-preview-template-is-handed-is-a-subject-this-server-owns.md).
  This is what the same query would still have cost after that gap closes.

## Assumed

- A caller hyphenates a compound the corpus writes apart more often than the
  reverse. One feedback is the evidence for it. The queued step is a measurement
  over the 40 scenario prompts and the hint titles rather than the change.

## Wrong if

- The measurement finds the compounds are rare and the wider match moves an
  answer that was right. Then the corpus side is the cheaper half. The
  `appliesTo` of the hints that name a compound gains the hyphenated spelling,
  and the matcher stays as it is.
- Splitting on the hyphen costs an identifier. `tt_content`, `list_type` and
  `mod.web_layout` are what to measure again. A rule that also loosened those
  would trade this miss for the one `D-ANS-006` stands against.

## Since then

The measurement ran and found a third gate this entry did not name. The domain
keywords are compounds with a space too, and the matcher reads them before it
scores anything. So a hyphenated query detected no domain at all, and every
candidate was out before the rank. That is upstream of both halves above.

The measurement covers the whole curated vocabulary rather than one feedback. It
asked 195 multi-word patterns twice, spaced and hyphenated: 176 reach their own
hint spaced and 110 hyphenated. Of the four rules measured against that, the
term half alone loses three queries every hit they had. Both together reach not
one hint the keyword half does not.
