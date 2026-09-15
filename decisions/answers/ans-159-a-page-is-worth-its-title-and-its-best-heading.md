---
id: D-ANS-159
title: A page is worth its title and its best heading
date: 2026-09-15
status: open
coveredBy:
  - DocumentationTest::aHeadingThatRepeatsTheTitleAddsNothing
  - DocumentationTest::aQuestionTheTitleDoesNotCarryReachesThePageWhoseHeadingDoes
---

# D-ANS-159 — A page is worth its title and its best heading

**`Manual\Documentation` adds to a page's score half the score of its best
heading, over the words the page carries nowhere else.**

The inventory lists every heading of a page with its anchor, as `std:title`. The
search read none of them. So a question the manual answers under a heading
reached the page only where the title carried a word of it. "apache htaccess
configuration" reached nothing in twenty results.

## Evidence

- **Read from `objects.inv.json` at 14.3 on 2026-09-15.** TYPO3 Explained lists
  984 pages and 3745 headings. TypoScript Explained lists 170 and 1175, the TCA
  Reference 92 and 382, the ViewHelper Reference 193 and 390. A page carries 3.9
  headings on the mean. The first heading of every page is its title again, and
  the reader drops that one.
- **Twenty-five questions, ranked live at 14.3 by the real class.** The seven
  `D-ANS-032` committed to, and eighteen whose answer stands under a heading.
  The sum of ranks, each capped at 20, is 134 with the headings ignored. It is
  54 at a heading weight of 0.35, **53 at 0.5**, 58 at 0.75 and 62 at 1.0.
- **What moves, at 0.5.** "apache htaccess configuration" 99 to 2, and "inline
  style temp file config" 99 to 1. "page tsconfig list module items per table"
  19 to 2. "FunctionalTestCase executeFrontendSubRequest CSV fixture" 17 to 1,
  and "functional test database fixture csv import" 12 to 1. Three questions
  lose: "f:if f:then f:else condition ViewHelper" 3 to 6, "flush cache by tag" 3
  to 5, "extbase persistence classes mapping" 2 to 3. The other seventeen stay
  where they were, fourteen of them first.
- **The sum, not the bonus.** The first version added the best heading's whole
  score, and `f:if` fell from 1 to 3. The IfAuthenticated page carries "if" in
  its title and again in a heading, and took the word twice. Scored over the
  words the page carries nowhere else, the heading adds nothing there and the
  question stays first.
- **The weights, not the fields.** A term is worth how few pages carry it. With
  the headings searched and not weighed, a word only headings carry counted as
  if the square root of the corpus held it. Weighed over the headings too, the
  sum falls from 67 to 53 at the same weight.
- **The excerpt.** A heading's anchor is an empty `<a>` at the top of its
  section, where a property's anchor is the section itself. The excerpt of a
  heading result was empty until the reader took the section around it.

## Decided

- The bonus is the best heading and not the sum of them. A long page carries a
  heading for every word of a question and would win on length.
- A heading is scored over the terms the page's title, path and manual carry
  nowhere. A word the title already carries is worth nothing again.
- The weight is 0.5, the minimum of the sweep. The measure is the twenty-five
  questions and nothing else, so the next session that adds a question re-runs
  the sweep before it moves the number.
- The result names the heading as its `section` and carries its anchor in the
  URL, where the heading outscores the page on its own. Where the title answers
  the question, the caller is sent to the page and not into it. A word matched
  in a heading is reported with the field `section`, which `R-DOC-002` asks for.
- The excerpt of a heading result is the prose of the section around the anchor.
  That is the same read a property result gets, and the same reader.
- `std:label` stays out. It lists 7433 labels of TYPO3 Explained. Most are the
  same anchors again under a second name, and the rest are words like "Example"
  and "API".

## Assumed

- That a heading names its section the way a title names its page. The
  twenty-five questions say so where the heading is a subject, and say nothing
  about the pages whose headings are "Example" and "Introduction".
- That the weight holds across the four manuals. The sweep ran at 14.3 and the
  four inventories at the other covered versions carry the same roles.

## Wrong if

- A question a title answers loses its page to one whose heading repeats the
  question's words. The three losers above are at 3, 5 and 6, and a fourth that
  leaves the six is the report.
- A heading result's excerpt reads the page's lead rather than the section. Then
  the host moved the anchor out of its section, and the reader takes the article
  as before.
