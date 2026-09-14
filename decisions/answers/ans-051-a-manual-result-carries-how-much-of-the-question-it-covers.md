---
id: D-ANS-051
title: 'A manual result carries how much of the question it covers'
date: 2026-08-04
status: open
coveredBy:
  - DocumentationTest::aPageReadBackCoversNoQuery
  - DocumentationTest::aResultCoveringLessThanHalfTheQueryIsStillReturned
  - DocumentationTest::aResultCoversTheQueryItIsKeptFor
  - DocumentationTest::anAnswerThatCoversTheQuestionCarriesNoSuchSentence
  - DocumentationTest::everySearchResultSaysHowMuchOfTheQueryItCovers
  - DocumentationTest::theAnswerSaysWhereNothingCoversHalfTheQuery
---

# D-ANS-051 — A manual result carries how much of the question it covers

**Every live-manual result carries the share of the query it covers, and the
answer says above the results where nothing covers half. The floor is not
shipped: no value of it empties the reported queries and keeps a page that
answers today.**

`D-ANS-046` decided the floor and the miss as one change. Its own **Since then**
then measured that the floor has no value. At 0.5 the three queries of
`feedback/2026-08-03-164734` no longer return six collisions, and
`login screen layout` and `login form template` go empty with them. Those are
the two queries that entry offers as proof that *LoginProvider* is reachable at
all.

## Evidence

- Every number below comes from 2026-08-04 against the live 14.3 index this
  lookup builds, 1419 pages from the four manuals. It is the coverage
  `TermSearch::score()` returns divided by `array_sum($weights)`, which is the
  share `Documents::search()` compares. `D-ANS-046`'s figures reproduce to the
  second decimal, a day later.
- The three reported queries. `fluid.html file extension templates` clears 0.5
  once, at 0.598 on *Multi-language Fluid templates*.
  `Fluid template file naming convention v14` clears nowhere at a best of 0.399
  on *Naming*. `layout root paths login screen override` clears nowhere at a
  best of 0.217 on *be.pagePath*. All six collisions the feedback reported come
  back, in the order it reported them.
- The three that price the floor. `login screen layout` returns *LoginProvider*
  fifth at 0.344 and tops out at 0.386. `login form template` returns it seventh
  at 0.37 and tops out at 0.422. `login provider` returns it first at 1.00. So a
  floor that returns the first two is at 0.34 or under. One that empties
  `Fluid template file naming convention v14` is above 0.40.
- Two queries `DocumentationTest` already holds go the same way.
  `TCA inline foreign_field foreign_sortby localization children` returns *IRRE
  / inline* first at 0.43.
  `FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14` returns
  *Functional tests* first at 0.19. Both would go empty at 0.5.
- Coverage is not aboutness on this corpus, and one of these six shows it. The
  page that clears 0.5 on the first query is *Multi-language Fluid templates*,
  which is the first collision the feedback reported. The query's weight sits in
  `fluid.`, `extens` and `templa`, and a page about localized Fluid templates
  carries all three.
- The sentence fires on four of the six queries above. It is silent on
  `fluid.html file extension templates` and on `login provider`, which is the
  one query of the six whose first result answers it.
- `bin/cli feedback:list` on 2026-08-04: 3 open in two directories, each with a
  todo that names it. The 25 of `D-ANS-046` closed in between.

## Decided

- **The second of the three options the card priced, chosen by the maintainer on
  2026-08-04.** It is what the reported session could not see, and it costs no
  query that answers today. It leaves the floor available once there is a
  measure for it.
- **A schema addition, and additive.** `coverage` joins the properties of a
  result and its required list. Every field that exists keeps its name and its
  meaning, and the schema stays open. So a client that validates against the old
  one sees no change. Nothing drops out, so the population of an answer is
  exactly what it was. The same six results, and each now says how much of the
  question it carries.
- **Null in page mode rather than zero.** No search ran there, and a zero would
  say the page answers nothing. It is the null beside the empty `matched`, which
  is the same statement about the same mode.
- **The warning is a sentence and the share is a number.** A number in a payload
  is not a warning. What the feedback reports as the expensive kind of wrong
  answer is six results in the shape a good answer has. A client that reads only
  the text half is the one that gets there. So the text says it once above the
  results, and the data half carries no second flag for a client to interpret.
- **0.5 is `Documents::MIN_COVERAGE` and not a number of this tool's own.** The
  two corpora this server matches prose against say "half the query" with one
  value. There it drops a section and here it labels a page.
- **The corpus sentence that names `typo3_changelog_lookup` does not ship.**
  `D-ANS-043`'s shape hangs on a miss, and this change produces no miss. It
  belongs to the floor whenever that ships.
- **`feedback/2026-08-03-164734` is not archived.** Its suggestion's "at
  minimum" half landed. Its second sentence, return the fact rather than the
  best six collisions, did not, and that is the floor. The feedback shrinks to
  it and the card still waits on the measure.
- **A reader a test hands in, so the text half is driven by one.** The tool
  builds its own `Documentation`, so a fixture had nowhere to go. Only a
  docs.typo3.org that answers during a test run would have held the sentence.
  `R-COD-003` says a unit test stubs what is outside it, and this is the seam
  `Typo3Cli::useRunner()` already is for the console. It sat on `Documentation`
  until `D-ANS-119` gave the two manual readers one inventory, and is
  `Manuals::useReader()` since.

## Assumed

- That a caller reads the sentence and the share. Nothing here measures it.
  `D-ANS-021`'s **Since then** is the nearest evidence in the other direction. A
  session did read the match line this one sits beside, described the rank from
  it correctly, and still went to its vendor tree.
- That a label is worth more than an empty answer to a caller that has already
  paid for the call. The measurement covered the alternative and did not prefer
  it; neither had a trial on a session.
- That the share is the right measure to report even where it is not aboutness.
  The 0.598 above is a wrong page that clears the half. So a caller who reads
  the number as "this page is about my question" reads more into it than it
  says.
- That the numbers hold as the manuals grow. They are the 14.3 indexes on one
  day. `D-ANS-040` recorded a query that fell from 0.508 to 0.462 because
  another corpus gained four sections elsewhere.

## Wrong if

- A session gets a 19% answer and reads it as a full one. It reports the same
  end: the question settled with a read of installed source. That is the case
  this entry is most exposed to. A number in a payload is not a warning, and
  neither, perhaps, is a sentence above six confident results. Then what remains
  is the floor or the index, and the floor still needs its measure.
- The sentence fires on nearly every call. Four of the six queries measured here
  carry it. A corpus where most calls do is one where the sentence is wallpaper
  and the half stands against the wrong corpus.
- A feedback reports the opposite cost — the sentence above an answer whose
  first result is the right page. `login screen layout` is that case in wait:
  *LoginProvider* is reachable there at 0.344 and the answer now says nothing
  covers half the question.
- Somebody sets the floor from this number without the measure the card asks
  for, and `login screen layout` no longer returns *LoginProvider*. Two things
  would have to exist first. One is a share that survives a three-word question
  over a table of contents whose ordinary field is 2.66 words. Its measure is
  what a page could carry rather than the whole query's weight. The other is an
  offer that carries what the floor drops, which
  `Search\Subsets::largestReaching()` does not today. It offers `layout login`,
  which returns three of the six collisions and not *LoginProvider*.

## Since then

The measurement took the rank gap in three forms over eight queries. The share
the fourth **Wrong if** asks for became concrete as the covered weight over the
weight of the terms at least one page carries. A term no page carries is weight
nobody could have covered.

The drop went to the maintainer on 2026-08-04 with the two options priced, and
the answer was to close it out. So nothing remains in wait on a measure. The
half of **Decided** that now fails is the return of the fact that nothing clears
a threshold rather than the best collisions. No threshold over this index tells
the two cases apart.
