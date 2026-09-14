---
id: D-ANS-032
title: "The manual ranking is diluted by an ordinary title's length"
date: 2026-08-02
status: open
coveredBy:
  - DocumentationTest::aPageTitledAfterItsSubjectOutranksALongerTitle
---

# D-ANS-032 — The manual ranking is diluted by an ordinary title's length

**`Documentation::UNDILUTED_WORDS` is 3, the ordinary field length of the manual
corpus, rather than 12, which is its longest title.**

`TermSearch::score()` weighs a term by how much other text stands around it. 12
was the value at which no title in the corpus is other text at all. So a page
titled after its subject and a page with a long event class name as its title
were worth the same. That held for the one word they share. The class name wins
every tie it is in, because it carries five more words a query can find. That is
the crowd dilution exists to answer, switched off by the constant that should
apply it.

## Evidence

- What the corpus's fields are, over the 1419 pages the four manuals index at
  14.3. A title is 2.66 words on the mean, 2 on the median and 12 at its
  longest. A path is 7.16 and 18. A manual is 2 words for three of the books and
  3 for the Fluid ViewHelper Reference. 12 was the maximum rather than the
  ordinary. That is why the docblock could say nothing here is long enough for
  dilution and be right.
- The seven queries this repository has already committed to an answer for,
  ranked over the live index. Not over the stub the unit tests carry. Rank of
  the expected page at 12 and at 3. `TCA inline …` → IRRE / inline 1 and 1.
  `Fluid AssetCollector css javascript ViewHelper` → Assets **17 and 12**.
  `FunctionalTestCase executeFrontendSubRequest …` → Functional tests **3 and
  1**. `Record API Fluid template access record.header` → Record objects **23
  and 18**. `page title event` → Page title API 1 and 1. `f:if` →
  `Global/If.html` **8 and 4**. `f:if f:then f:else condition ViewHelper` →
  `Global/If.html` **10 and 4**. Not one of them is worse at 3.
- The same sum of ranks across the range, which is flat above the corpus and
  falls into it. 63 at 24, 16 and 12; 62 at 10; 61 at 8; 59 at 5. Then 51 at 4;
  **41 at 3**; 32 at 2; 33 at 1. 12 is already dilution switched off. 14, 16, 20
  and 30 move 3 of the 43 queries between them and nothing above 16 moves
  anything.
- What it costs on the 41 scenario prompts, the only corpus of real wordings
  this repository has, each asked alone at 14.3 for six results. **All 43
  queries change their six, 20 change their first hit**, and 104 entries leave a
  top-6 answer while 104 arrive. It is not a marginal change and it is not
  presented as one.
- What arrives, read one prompt at a time. `CORE-01` is a DataHandler bug and
  gained `DataHandler` and `DataHandler basics`, neither of which was in its
  six. `CORE-04` deprecates `GeneralUtility::getUrl()` and gained Deprecation
  Handling. `CORE-05` is a functional test that fails locally and passes in CI
  and gained CI/CD Automation. What leaves is mostly event class names —
  `AfterPageUrlsForSiteForRedirectIntegrityHaveBeenCollectedEvent`,
  `ShouldUseCachedPageDataIfAvailableEvent`, five `ModifyRecordList*Event`
  pages.
- The counter that says the opposite, because it ran first and the choice not to
  follow it is a decision. The mean number of query words that reach a returned
  page falls from 2.00 at 12 to 1.72 at 3. Results that one word or none reaches
  rise from 79 of 258 to 114. That instrument counts a long class name as the
  better answer for the words it happens to contain. That is the failure under
  repair rather than a measure of it.
- What it does not buy, which is the question that picked it. `Global/If.html`
  is fourth and not first. Three of the ten pages with `if` have the title `if`:
  `Guide/TypoScriptFunctions/If/Index.html`, `Functions/If.html` and this one.
  `security.ifAuthenticated` is three words. So all four escape dilution, all
  four score 198, and the order among them is the build order of the index. No
  length reference separates identical titles. What separates them is the book,
  and the query says which book in the `f:` prefix.
  [`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
  made that a domain keyword for the hints, and nothing reads it for the
  manuals.
- The field weights, which were the other candidate and are now measured. All
  ten pages with `if` carry it in `title`, so a title weight scales all ten
  alike. The page titled `if` stands at 8 of 10 at reference 12 and 4 of 10 at
  reference 3. That holds under `title` 4, 6 and 8, under `path` 1, and under
  `manual` 4. Raising `title` to 8 makes the longer ViewHelper query worse — 4
  to 8.

## Decided

- 3 rather than 2, which scores marginally better on the seven. Three of the
  four books have two-word names and the Fluid ViewHelper Reference three. So at
  2 its 189 pages are the only ones with a diluted `manual` field. The length of
  their names weighs the books against each other. 3 is also where the corpus's
  own mean title lands.
- Not the field weights, on the measurement above. They cannot reach this at
  all: the tie is between pages that all matched the same field.
- The four books stay weighed as they are, and `path` stays at 2. A path is 7.16
  words against a reference of 3, so dilution now reaches it on nearly every
  page. That is the intended weight of a section name against a title, and it is
  what moves the deep-path event pages down.

## Assumed

- The seven pairs are ground truth. They are what this repository asserted
  before this change and nobody chose them for it. But they are seven, and four
  of them are ViewHelper or Fluid queries. A regression on a subject none of
  them names would not show here.
- A long title is a worse answer for the word it shares. That is what dilution
  says everywhere else in this server. On this corpus it is nearly the same
  claim as "a class name is a worse answer than a page name". That is narrower
  than it sounds. Every one of those pages is a real page about a real event.
- Measured against 14.3 as published on 2026-08-02, from the four manual roots
  fetched once. The rank is over a table of contents, so a book that retitles
  its pages moves this while nothing here changes.

## Wrong if

- A caller who names an event class no longer reaches its page. `EXT-04` asks
  for a backend module that lists records and lost all five
  `ModifyRecordList*Event` pages from its six. That is the trade this makes, and
  it is wrong if the class name is what people search by.
- A short title wins because it is short. One word of the query or none now
  reaches 114 of the 258 results over the 41 prompts, against 79 before. The
  mean words that reach a result fell to 1.72. The counter above is the one that
  would show this first.
- A fifth manual arrives whose name is four words or more. Every page of it is
  then diluted on the `manual` field against every page of the other four, on
  every query that names a book.
- The hint or prose corpora turn out to want the same change. They have their
  own references at 200 and 400, and this leaves them alone. A shared value
  would be the mistake this one is: the manual's fields are titles, and theirs
  are bodies.

## Since then

The corpus this measured over is gone. The index is the inventory each manual
publishes rather than the links of its rendered root. So a page's name is what
the manual states rather than what its navigation abbreviated it to. The
ordinary title is half as long again and the longest is longer, so the bold
sentence describes the corpus of the entry's date.

The constant did not move: swept over the new corpus on the same seven queries,
the committed page's rank is what it was.
