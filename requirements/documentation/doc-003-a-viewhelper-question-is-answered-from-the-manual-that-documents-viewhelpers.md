---
id: R-DOC-003
title: 'A ViewHelper question is answered from the manual that documents ViewHelpers'
status: held
restsOn: [D-ANS-026, D-ANS-032, D-ANS-036, D-ANS-047]
heldBy:
  - DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase
  - DocumentationTest::aQueryWrittenInFluidTagsIsAnsweredFromTheFluidBook
  - DocumentationTest::aTagNamedAfterAStopwordIsReachedByItsOwnName
  - DocumentationTest::aViewHelperQuestionReachesTheManualOutsideTheCollection
---

# R-DOC-003 — A ViewHelper question is answered from the manual that documents ViewHelpers

**The reference that documents ViewHelpers answers what a ViewHelper does, at
the version the caller asks for.**

`R-DOC-001` says the manuals in the search are the ones a question can be about.
This names the one that was absent and what it costs to carry it. The Fluid
ViewHelper Reference sits under `/other/` rather than the `/m/` the three
manuals of the core sit in. So where a manual is depends on the collection that
publishes it, and not on one base built for all of them. A base built wrong is
silent. The root does not answer, and the book is absent from the index. The
question comes back answered from whichever of the other manuals carries the
word. So three things have to hold. Its pages are in the index, they are there
under its own base, and the same call takes a URL back that it handed out.

What this does not promise is where in the answer that page comes. Three steps
took it from eighth of the ten pages with `if` to second. The first was a name
too short for a search: `TermSearch::terms()` admits a two-letter word, so
`f:if` reaches `Global/If.html` at all. The second was the rank constant,
`D-ANS-032`, which weighs a title by its length. The third is the book, which
the query names by its `f:` prefix and `Documentation` now reads (`D-ANS-036`).
The two TypoScript pages with the title `if` are not candidates for a query in
Fluid tags. What remains is the tie inside the book. `security.ifAuthenticated`
is three words and so undiluted as well. It scores what the one-word title
scores. No field weight separates them because both matched in the title, and
the order among them is the build order of the index. A tag named after a
stopword now gets that far too. `or` and `then` are in the list for what they do
in a sentence. A word behind a namespace prefix is not in one (`D-ANS-047`). So
`f:or` and `f:then` reach `Global/Or.html` and `Global/Then.html` first rather
than come back empty.

## From

`feedback/2026-08-01-003000`, judged as `D-ANS-023` on 2026-08-02. One session
lost a task to three Fluid mistakes. The search read three manuals, and none of
them documents a ViewHelper. `f:if f:then f:else condition ViewHelper` came back
with Developing a custom ViewHelper and the Translate ViewHelper.
