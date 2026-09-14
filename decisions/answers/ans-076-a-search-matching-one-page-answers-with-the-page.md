---
id: D-ANS-076
title: 'A search matching one page answers with the page'
date: 2026-08-11
status: open
coveredBy:
  - KnowledgeTest::aCutAnswerNamesTheHeadingsOfThePageItLeft
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - KnowledgeTest::everyToolThatRendersASectionOffersThePageAsACall
---

# D-ANS-076 — A search matching one page answers with the page

**`typo3_rule_lookup` returns the whole document where every section it matched
is in one, instead of the excerpts and an offer to fetch it.**

The server carries two attempts to tell a session what a cut left of a page.
[`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md)
records that both were in the session that then searched the same page twice.
What goes here is the round trip rather than the sentence about it.

## Evidence

- **The reported pair, re-measured on 2026-08-11 at `targetVersion=15.0`.**
  `feedback/2026-08-10-182523` made two calls minutes apart, and each matched
  one heading of `core/contribution/commit-messages`. That is 2940 and 3346
  bytes over two calls, against 11742 for the page in one. The first answer's
  foot named `Release Targets`, which is what the second search looked for.
- **A call is the cost and the text is not.**
  [`D-FBK-020`](../feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)
  measured 718 million cached input tokens read back over 5414 calls against 5.9
  million written out. Two calls at 6286 bytes is the expensive shape and one at
  11742 is the cheap one.
- **How often it fires.** The corpus declares 56 topics as its own subjects,
  every `##` heading of every document, which is what `Documents::topics()`
  answers with. 17 reach exactly one document and 39 reach several. So roughly a
  third of queries, and two thirds keep the cut and the line under it.
- **What a page costs beside the excerpts.** Median 2.6 times the cut answer,
  worst 6.1, and no query where the page was the shorter of the two. The ceiling
  is the corpus rather than the ratio: ten documents, the largest
  `core/contribution/commit-messages` at 11742 bytes and the median at 4328.
- **The cut is not always even whole.** `Documents::MAX_SECTION_LENGTH` cuts a
  section at 2400 bytes, and the excerpt then says so and names the same second
  call. `KnowledgeTest::aCutScriptSectionSaysHowToReadThePageWhole` holds that
  for the script lookup.

## Decided

- **The condition is one document, not a share of it.** A search whose hits all
  sit in one page has established which page answers the task. How much of it
  the query happened to name says nothing about what the next question needs.
- **The page as written**, front matter and sections outside the target
  included. That is what `documentId` already returns, and for the reason stated
  there. A section left out for a major it does not hold on is a hole in a page.
  Every bound section carries its own range under its heading. One behaviour,
  not two.
- **`matchedHeadings` joins the answer; `matches` keeps its shape.** The caller
  still learns what the query hit, and `matches` carries the one record for the
  page. That is the shape the `documentId` path already answers in, and one
  place now builds it for both.
- **`typo3_rule_lookup` alone.** `typo3_script_lookup` and `typo3_task_guide`
  render the same corpus through `Prose::sections()` and keep the cut. Nothing
  has reported the round trip there, and `Prose::wholePage()` is one line away
  when something does.
- **The cut answer keeps everything it had.**
  [`R-ANS-028`](../../requirements/answers/ans-028-an-answer-that-names-a-document-says-how-to-read-it-whole.md)
  and the foot line of `D-ANS-070` hold for an answer that really is a cut. That
  is the two thirds of queries that reach more than one page.

## Assumed

- **That a session reads a page it gets whole.** Nothing here shows it. What the
  evidence shows is two sessions that did not act on a sentence that named the
  page. That is why the sentence is not what got stronger a third time.
- **That the corpus stays small enough for a page to be an answer.** Ten
  documents on the date above, the largest under 12 kB. Nothing caps a document,
  and `bin/cli tools:record` is where the answer lengths are visible.

## Wrong if

- A session reports an answer of this tool as too long to read, or reports that
  it skipped one. That is the second **Wrong if** of
  [`D-ANS-061`](ans-061-an-answer-that-names-a-document-hands-it-over.md), which
  nothing could satisfy until now. It would say the cut was right and only its
  label was wrong.
- A session gets a page whole and searches into it a second time anyway. Then
  the cut never caused the round trip.
- A caller on an LTS acts on a section of a handed-over page that holds on main
  alone. Then the page as written is wrong for a search that carried a target,
  whatever it is right for on `documentId`.
- A document grows to where the page is no longer an answer. Then the
  concentrated case needs the ceiling the section cut already has.

## Since then

The mechanism stays as it was, and which recorded call demonstrates it moved
when the corpus gained a document. A term's weight comes from the sections in
front of the query. So eight more of them carried two sections of the first call
over the coverage floor. The first call is the cut now and the second is the
page, and the round trip is still saved.

This is the fourth **Wrong if**, arrived from the other side. It names a
document that grows until a page is no longer an answer. What happened is a
corpus that grew until a query is no longer concentrated.
