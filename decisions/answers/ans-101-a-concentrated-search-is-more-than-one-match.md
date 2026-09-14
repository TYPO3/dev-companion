---
id: D-ANS-101
title: A concentrated search is more than one match
date: 2026-08-24
status: open
coveredBy:
  - KnowledgeTest::aMatchedOpeningIsNamedForWhatItIs
  - KnowledgeTest::aPageRecordCarriesWhatTheSearchMeasured
  - KnowledgeTest::onlyMoreThanOneMatchedSectionHandsThePageOver
---

# D-ANS-101 — A concentrated search is more than one match

**`typo3_rule_lookup` hands over a whole page only where more than one section
matched. The record it returns carries the score and the coverage that search
measured.**

[`D-ANS-076`](ans-076-a-search-matching-one-page-answers-with-the-page.md) made
a search whose hits sit in one page an answer of that page. Its premise is that
such a search has established which page answers the task. A search with one hit
has established that one word landed somewhere.

## Evidence

- Re-run on 2026-08-24 through `RuleLookup::answer()`. `query="signed-off-by"`,
  `targetVersion="15.0"` answers `any/testing/proving-a-condition` whole, six
  kilobytes on the proof of a TypoScript condition, with
  `matchedHeadings: ["Which URL Is Requested"]`. That is what
  `feedback/2026-08-24-110851` reported, unchanged.
- One term reached it. `TermSearch::terms("signed-off-by")` is `["signed"]`.
  `meaningful()` keeps the hyphen as a word character, so the trailer is one
  word, and `stem()` cuts a word over six characters to six.
- One section of the corpus's 124 carries a word that starts `signed`: "a
  browser tab that is signed in", under `Which URL Is Requested`. Its weight is
  `log(124/1)`, 4.82. A one-term query has full coverage or none, so
  `Documents::MIN_COVERAGE` has nothing to cut.
- The number the session ranked the answer by is not a number.
  `Documents::search()` scores that match 48, and `Prose::pageRecords()` returns
  `'score' => 0` and `'coverage' => 1.0` as constants for every whole-page
  answer, the `documentId` path included. Reading `score 0` as "nothing matched"
  was right by accident.
- The two constants disagree, and the wrong one is not the one that read as
  wrong. `coverage: 1.0` asserts that the page covers the whole query, and a
  client that validates the declared `outputSchema()` may act on it.
- **The same shape in this server's own curated queries.** `rulesQuery: "icon"`
  in `knowledge/task-intents.json` reaches one section, `Changed Signatures` of
  `core/contribution/commit-messages`, score 48, coverage 1.00. It hands the
  whole commit-message page over because the word appears in it once.
- **What the floor costs, measured over `Documents::topics()` on 2026-08-24 at
  `targetVersion=15.0`.** Of the corpus's 103 subjects, 25 reach one page and 12
  of those reach exactly one section. A ninth of the subjects gets the section
  and the offer where the page came before. Those 12 score 191 to 618 against
  the 48 of both thin matches, because each names a heading outright. The count
  does not tell them apart and the score would.
- **The reported pair of `D-ANS-076` is below the floor.**
  `feedback/2026-08-10-182523`'s second call matches one section,
  `Release Targets`, at score 212. So the entry's own pair of origin is now two
  cuts. What removes the round trip there is the offer that names the page
  rather than the page.

## Decided

- A floor on how many sections matched, not on the score. Score and coverage are
  already what `search()` keeps a match by. What fails here is a page handed
  over on the evidence of one word. The floor is two sections, which is the only
  height a count has here. One is what both thin matches have, and the
  measurement above is what it costs.
- The measured score and coverage go into the page record. A field the schema
  declares is a claim, and a constant in place of a measurement is the one kind
  of wrong nothing downstream detects.
- Both in one change. The measurement reported without the floor answers this
  query with `score: 48, coverage: 1.0` on the same unrelated page. That asserts
  a strong match where the constant only failed to deny one.
- Below the floor the answer is the cut, with the offer to read the page whole
  that `Prose::sections()` already carries. Nothing stays withheld; the answer
  no longer pushes the page.
- The `documentId` path reports zero on both halves of the pair, and the schema
  says what that zero is: no search ranked this record. Nothing is nullable, so
  no client that reads a number gets null. The `coverage: 1.0` that asserted a
  full-query answer where nobody asked a query is gone. A page the caller named
  is the one record here whose zero score is not a measurement in place of a
  strong match.
- `D-ANS-076` stands. Two thirds of queries reach more than one page and stay as
  they were, and the round trip it removed stays gone. What moves is the bottom
  of its range.

## Assumed

- That a caller's queries look less like the corpus's own headings than the 12
  above do. Each of those names a heading word for word, which is what a subject
  list is and not what a session types. The queries this server has on record
  are the nine curated `rulesQuery` values, where the one that reaches a single
  section is the thin one.
- That the hyphen and the six-character stem are right elsewhere.
  `signed-off-by` is one word to `meaningful()` by the same rule that keeps
  `f:if` and `EXT:core` whole. This entry changes what happens with a thin match
  rather than how the tokenizer cuts a query.

## Wrong if

- A query with one strong match gets a cut and the caller pays the second call
  `D-ANS-076` removed. Then the floor is on the wrong quantity and the score is
  what should carry it.
- A caller acts on the coverage of a whole-page answer and it is still not that
  search's. Then one more place than this reached builds the record.
- The trailer query still answers with an unrelated page once the floor is in.
  Then the miss answer is what needed the work, and
  [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md) is where
  it goes.
- The floor hides the answer to a rare exact term, a class name that occurs in
  one section and nowhere else. A count cannot tell that from this query, and a
  session that reports it says the score had to carry the decision after all.
