---
id: D-DOC-032
title: A section heading is the label a contents list shows
date: 2026-08-13
status: open
coveredBy:
  - SiteTest::everySectionIsHeadedByALabel
---

# D-DOC-032 — A section heading is the label a contents list shows

**A section heading is a label of at most five words. A contents list shows the
heading itself and a section has no second name.**

`D-DOC-031` gave the page a label and left the heading a sentence. It answered
for the rail, the trail and the footer, which show a page. The list beside a
page shows its sections, and nothing had covered those.

## Evidence

- `records/judging` carried 24 entries in that list, five of them sentences:
  `5. Decision — everything worked as designed, and the design is the price` at
  72 characters. And
  `The answer names the gap, not the fix — where the fix is about TYPO3` at 68.
- The generated tool pages were worse and repeated it.
  `From the 14.3 core checkout below .checkouts/, whose console could not be reached`
  is 81 characters. It stood three times on each of nine pages, beside a second
  of 84.
- The theme reads the list out of the document; `Compiler\OnThisPage` inserts
  the node the core fills. So a section carries no `:navigation-title:` and
  there is nothing else to shorten.
- `_search.json` carries the page title and its first text and no heading below
  them, so nothing here matches and gets lost.

## Decided

- Five words, where a page label gets four. A section may state a claim, and the
  ones this corpus writes are of the form *judged, not executed*.
- 61 handwritten headings were rewritten. The longest entry any list now shows
  is 25 characters.
- What a heading said and its body did not now stands in the body. The five
  rungs of the ladder in `records/judging` and the seven answers under it each
  gained the line their heading used to carry. That is the rung's definition,
  and whether a session may reach the answer without a question.
- The generated half takes the same rule from the generator:
  `ToolAnswers::shortly()` names the root and stops. Where that root sits, which
  version it declares and whether its console answered stand in the page's first
  sentence already. Repeated into every heading they were longer than several of
  the answers under them.
- The page heading stays the sentence `D-DOC-031` left it as. What is short is
  what a reader meets in a column: the rail label, and now the section.

## Assumed

- That a reader picks a section from a label and reads the claim in the body.
  That is what `D-DOC-031` assumed of a page and nothing here measures either.
- That readers meet the twelve lines added to `records/judging` where they met
  the headings they replace. A lead line is not a heading, and a reader who
  skims the bold leads meets them in the same place.

## Wrong if

- A reader cannot find a section because its label dropped the word they
  searched for.
- A five-word label is still a sentence, which the count cannot tell.
- A claim that was in a heading is now in no body, so the cut lost it.
