---
id: D-ANS-026
title: 'The ViewHelper reference is indexed'
date: 2026-08-02
status: open
coveredBy:
  - DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase
  - DocumentationTest::aViewHelperQuestionReachesTheManualOutsideTheCollection
---

# D-ANS-026 — The ViewHelper reference is indexed

**`typo3_documentation_lookup` searches four manuals, and where one lives is
part of what the index knows about it.**

[`D-ANS-023`](ans-023-a-viewhelper-question-is-answered-by-widening-the-manual-index.md)
is the finding: three books, and none of them documents a ViewHelper. This entry
took its place. The index carries the book, and that cost the one thing every
base used to have in common.

## Evidence

- The reference lives like the others and has versions like them.
  `https://docs.typo3.org/other/typo3/view-helper-reference/<version>/en-us/`
  answers 200 on 12.4, 13.4, 14.3 and main, the four branches
  `knowledge/versions.json` covers. So the version a caller asks for is the
  branch every other lookup already asks for. The engine keeps no axis of its
  own
  ([`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)).
- Its root is a table of contents `Documentation::index()` reads: 189 pages at
  14.3, `Global/If.html` among them. That was the first **Wrong if** of
  `D-ANS-023` and it did not hold.
- The pages excerpt. The If page opens with "This ViewHelper implements an
  if/else condition"; the Then page is 184 characters and still says what it is
  for.
- What differs from the three of the core is the base and nothing else. Every
  one of them is `/m/<document>/<version>/en-us/`; this one is `/other/`.
- One question that merely says Fluid is not taken over by it. Measured live at
  14.3, `Fluid template layout partial section` still puts Multi-language Fluid
  templates first, with the `section` and `layout` pages second and fifth. All
  189 pages carry "Fluid" in the `manual` field, and a term everything carries
  separates nothing.
- The other manuals keep their questions: TCA `inline`, the TypoScript content
  objects and the functional-testing pages come back as before.

## Decided

- A manual is `{title, collection}` and one method builds every base from it.
  The collection says where a manual is, rather than a constant of the host. So
  a fifth book is one entry in whichever collection publishes it.
- The book keeps the title its publisher gives it, "Fluid ViewHelper Reference",
  measured rather than feared — see the fifth piece of evidence.
- What has to hold on is
  [`R-DOC-003`](../../requirements/documentation/doc-003-a-viewhelper-question-is-answered-from-the-manual-that-documents-viewhelpers.md),
  and the two tests under **Covered by** hold it.
- Not decided here: the tokenizer. `f:if` reaches `Global/Else.html` and not
  `Global/If.html`, because `TermSearch::terms()` drops every word under three
  characters and `then` is a stopword. The lever is `TermSearch`, which the hint
  and prose corpora go through as well, so it carries a todo of its own.

## Assumed

- The book stays where it lives: same collection, same slug, same versions the
  covered releases ask for.
- One book more is worth one fetch more per call. The lookup already fetches
  every indexed root before it scores anything.
- What one afternoon measured holds for questions nobody asked that day.

## Wrong if

- `docs.typo3.org` moves the book — another collection, another slug, one
  version that is not published. A base that is wrong is silent. The root does
  not answer, the index lacks the book, and the answers go back to what
  `D-ANS-023` recorded, and nothing fails anywhere.
- Its short titles start to win questions that are not about a ViewHelper. What
  says they do not is a handful of queries at one version, not a suite.
- The single-file page becomes what a search pays for. Every manual root links
  `singlehtml/Index.html`, the whole book as one page, and `isDocumentPage()`
  lets it into the index as an ordinary candidate. For this book it is 2.9 MB,
  where in the three under `/m/` it has been cheap enough to go unnoticed.

## Since then

The tokenizer this entry left undecided is
[`D-ANS-028`](ans-028-a-two-letter-query-word-is-searched-for.md).
`TermSearch::terms()` admits a two-letter word, so `f:if` reaches
`Global/If.html` rather than nothing — and it reaches it eighth. Ten of the 1419
pages carry `if` as a whole word and all ten score the same. So the build order
of the index decides between the page titled "if" and Should Use Cached Page
Data If Available Event. That is this entry's own ground rather than the
tokenizer's. `UNDILUTED_WORDS` is 12 and no title in any of the four books is
that long. So how much else is in a title never weighs on it. Nothing about it
changes here. At a reference of 3 the six results of all 41 scenario prompts
change, and that needs its own read.
