---
id: R-DOC-002
title: 'A manual search says what it matched on'
status: held
restsOn: [D-ANS-021]
heldBy:
  - DocumentationTest::aPageReadBackCarriesNoMatch
  - DocumentationTest::aResultNamesTheWordsOfTheQueryItWasMatchedOn
  - ToolContractTest::aToolCallAnswersWithTextAndMatchingData
---

# R-DOC-002 — A manual search says what it matched on

**A caller can tell an aimed manual answer from a confident one.**

`R-DOC-001` says what the index is: page titles and section paths, a table of
contents and never the text of a page. This says the answer tells the caller. A
search that ranks on titles alone answers a five-word question in the shape it
answers a two-word one. So the caller learns nothing from six results with
canonical URLs and excerpts. The answer owes one of two things. Either the rule
where the caller composes the call: words beyond the subject re-aim the search
rather than refine it. Or the match where the answer arrives, so that a query
whose subject contributed least shows as one.

The second answers it. Every search result names the query words the index
carried and the field each matched in. The answer says once that page titles and
section paths are all there is to match.

## From

`feedback/2026-08-01-002928`, re-run on 2026-08-02. Three queries that named the
Record API returned six results each, `status: answered`. They ranked the
present *Record objects* page 28th, 13th and 11th of 1230, behind pages matched
on `has`, `get` and `acces`. Two round trips went on a miss the answers gave no
sign of.
