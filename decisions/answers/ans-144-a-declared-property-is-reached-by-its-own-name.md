---
id: D-ANS-144
title: A declared property is reached by its own name
date: 2026-09-04
status: open
coveredBy:
  - DocumentationTest::aDeclaredPropertyIsReachedByItsOwnName
---

# D-ANS-144 — A declared property is reached by its own name

**The manual index carries the `std:confval` objects beside the pages, so a
question naming a property answers with the section documenting it and the
anchor it sits at.**

A manual written as a few large pages has a table of contents that carries none
of the hundreds of names it documents.

## Evidence

- `feedback/2026-09-03-105623`. Three calls for one sentence about
  `columnsOverrides`: the first returned six pages none of which mentions the
  property, the second surfaced the page at rank 4, and the third read it whole.
  The session says a less patient one stops after the first and writes the patch
  on the reporter's premise.
- The TCA reference's inventory at 14.3 lists 92 `std:doc` objects and 754
  `std:confval` ones. `columnsOverrides` is one of them, published as
  `types-columnsoverrides std:confval -1 Types/Index.html#confval-types-columnsoverrides columnsOverrides`
  — the property name as its display, and the anchor of the section that
  documents it.
- Admitting every `std:confval` to the ranking degrades a prose question.
  Measured live on 2026-09-04 over the seven queries `D-ANS-032` and `D-ANS-065`
  rank: `TCA inline foreign_field child records` lost its page from the first
  four to `overrideChildTca` twice,
  `FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14` gained
  `csvDelimiter` and `csvQuote` at 2 and 3, and
  `Record API Fluid template access record.header` gained `template` and
  `templateName`. Every one of those is a property named like a word of the
  question.
- Admitting one only where a word of the question is written the way code is —
  an inner capital, an underscore, a dot — leaves six of those seven byte for
  byte as they were. The seventh gains `foreign_field` at rank 1 and keeps
  `IRRE / inline` at 2, for a question that names that property outright.
- The excerpt has to come from the section. Every property of `Types/Index.html`
  otherwise answers with the same two sentences about record types.

## Decided

- `std:confval` objects join the same candidate pool as the pages, so one
  ranking, one coverage and one order serve both.
- A property is offered only for a name the question names: a word written the
  way code is, or a question that is nothing but that name. The second is the
  only way `showitem` is reachable, and it is what keeps `label` out of every
  sentence carrying the word.
- The section is placed under the path of its page, so it sits in its chapter as
  a page does and the anchor's slug adds no words to match against.
- `excerpt()` takes the section the anchor names, and the article where the
  anchor names nothing on the page.

## Assumed

- That the display name of a `std:confval` is the property as a caller writes
  it. It is in the TCA reference, and the four searched manuals publish the same
  role from the same writer.

## Wrong if

- A session reports a property question answered with the wrong section — two
  manuals declaring the same name, where nothing here prefers either.
- A prose question comes back led by a property, which would mean the identifier
  shape admits ordinary words after all.
- A manual starts publishing `std:confval` for something other than a documented
  property, which would put objects in the ranking that answer no question.
