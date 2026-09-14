---
id: D-ANS-036
title: 'A query in Fluid tags is searched in the book for them'
date: 2026-08-03
status: open
coveredBy:
  - DocumentationTest::aQueryIsRoutedToABookOnlyWhileThatBookAnswers
  - DocumentationTest::aQueryWrittenInFluidTagsIsAnsweredFromTheFluidBook
---

# D-ANS-036 — A query in Fluid tags is searched in the book for them

**`f:` selects the Fluid ViewHelper Reference as the manual the search runs in,
the way `Domains::detect()` routes a hint to a category.**

Ten pages of the corpus carry `if` and every one of them carries it in the
title. Three have the title `if`, the two TypoScript function pages and
`Global/If.html`, and `security.ifAuthenticated` is three words. So all four
escape dilution and all four matched the same field. Neither the dilution
reference nor a field weight separates them
([`D-ANS-032`](ans-032-the-manual-ranking-is-diluted-by-an-ordinary-titles-length.md)).
What separates them is the book, and the query says which book in the prefix
[`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
already made a domain keyword for the hints.

## Evidence

- Measured over the four manual roots at 14.3, fetched once on 2026-08-03. The
  corpus is what `D-ANS-032` measured on 2026-08-02: 1419 pages, 10 with `if`,
  four of them free of dilution. Its seven queries rank the expected page 1, 12,
  1, 18, 1, 4, 4 before this change, which is what it recorded. The two
  measurements compare.
- `f:if` ranks `Global/If.html` **4th before and 2nd after**. Its six are pages
  of the ViewHelper reference rather than the two TypoScript pages and then four
  of them.
- `f:if f:then f:else condition ViewHelper` keeps it 4th and loses the two pages
  of other books from its six. Those are `TranslateViewHelper.html` of TYPO3
  Explained, one of the two answers behind
  [`D-ANS-023`](ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md),
  and `UsingSettingTSconfig/Conditions.html`.
- Nothing else moves. The other five of the seven rank identically. Over the 41
  scenario prompts of `Scenarios::load()` and `::contracts()` **not one changes
  its six or its first hit**, because none of them uses Fluid tags.
- The book as a query word was the other shape, and the measurement covers it.
  "Fluid" added to a query with the prefix lifts `f:if` from 4th to 2nd as well.
  But it lifts every page whose *title* carries the word by the title weight of
  4. The book's own pages gain only the manual weight of 2. On the longer query
  `TranslateViewHelper.html` rose from 5th to 3rd and `Global/If.html` fell from
  4th to 5th. The word is not the book.
- What the route does not reach is the tie inside the book. Its
  `security.ifAuthenticated` is three words and so free of dilution too, and it
  stands ahead of `Global/If.html` in the build order of the index. Both are in
  the book the query names.

## Decided

- The route selects the candidates rather than weighs them, which is how a hint
  routes: the filter drops `fluid.json` before the score reaches a hint. The
  term weights stay over the whole corpus, because what a term is worth is how
  few of all the pages there are carry it.
- Only `f:`. `be:` and `core:` are the two-letter risk `D-KNW-024` left out for
  the hints, and a template declares an extension's prefix per template rather
  than globally.
- Only a book that answered. The route is in front of the score. So a root that
  is down would otherwise leave such a query with no candidates and report
  `empty`. That is "no match" for a reason the caller cannot see. What said how
  many roots answered now says which ones did, so this costs no concept.
- The tool description says it, because it is the one thing the answer does not
  show. The way to the Fluid chapters of the other manuals is a query without
  the prefix.

## Assumed

- A caller who writes `f:` wants the book that documents the tags. `D-KNW-024`
  assumed the same for the hints, where both candidates sat in one category and
  the assumption only decided which won. Here the books are different.
- The seven pairs and the 41 prompts are the corpus of this measurement. No
  prompt uses Fluid tags, so the measure of what the route costs a real wording
  is two queries this repository wrote itself.

## Wrong if

- A question about how to write a ViewHelper carries `f:` and loses TYPO3
  Explained. `Developing a custom ViewHelper` is that manual's page and the
  ViewHelper reference has none. So "my own f:myTag renders nothing" now runs in
  a book that documents every tag except that one.
- One tag named inside a question about something else takes the whole query
  with it. `f:` is enough on its own, so "the f:image in my FLUIDTEMPLATE setup"
  gets an answer without TypoScript Explained.
- The tie inside the book is what callers actually notice. `f:if` is 2nd rather
  than 1st, and the page above it is a different ViewHelper.

## Since then

The half this left open landed and is
[`D-ANS-047`](ans-047-a-word-behind-a-namespace-prefix-is-searched-as-itself.md).
A query that names a tag whose name is in `TermSearch::STOPWORDS` reached the
route with no term for the book to rank. So `f:or` and `f:then` took the route
and came back empty. One number recorded above moved with it, measured on the
same roots at 14.3 on 2026-08-03. `f:if f:then f:else condition ViewHelper` now
ranks `Global/If.html` 5th rather than 4th, behind the `Global/Then.html` its
second tag reaches. Its six are pages of this book before and after, and `f:if`
alone answers as before.
