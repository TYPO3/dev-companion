---
id: D-GUI-025
title: A checklist item says that it does not decide everything
date: 2026-09-01
status: open
coveredBy:
  - HintsTest::aChecklistItemThatSummarizesARuleSaysItDoesNotDecideTheRest
  - SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched
---

# D-GUI-025 — A checklist item says that it does not decide everything

**An item that summarizes a rule carries a clause that says it does not decide
the whole of it. The page it already names carries the edge itself.**

Two sessions acted on an item and never opened the page behind it, which is what
a summary good enough to act on costs.

## Evidence

- `feedback/2026-08-27-145507`: a session read the changelog item's one-sentence
  summary and judged from it that a bugfix on only an `@internal` class owed no
  entry. It never opened `core/contribution/changelog`, where that edge has its
  decision. It says the summary "is good enough often enough that the document
  never gets opened".
- The second is the same shape one step further in. A session dropped the
  deprecation sweep on a three-statement diff because the item read as
  unconditional, and reports that it would drop it again.
- Neither the `when` line nor the `tool` field reaches them. Somebody on the
  search for a better answer reads both, and a caller who has one does not
  search.
- `knowledge/task-intents.json` holds 143 items across 32 intents, at a median
  of 248 characters, and every intent already names the document its rules come
  from.

## Decided

- Both places, and they say different things. The item says that it does not
  decide everything and names the id that does; the page states the edge.
- Asked on 2026-09-01, because it changes what every brief weighs, which
  `documentation/records/judging.rst` says never changes quietly. The maintainer
  took both places over either one alone.
- The clause is not the edge. An item that lists the cases it does not decide is
  the page in the brief, which is the size this stood against.
- Only items that summarize a rule take one. An item that states what to do,
  with no page behind it, has nothing to disclaim.

## Assumed

- That the two sighted items are representative of the ones that summarize. The
  sweep over the other items is what would show otherwise.
- That a reader who is told an item is partial opens the page. Neither sighting
  tested that, because neither item said so.

## Wrong if

- A session reads the clause, opens nothing, and acts on the summary anyway.
  Then the lever is delivery rather than wording, and what remains is the page
  handed over at the step rather than named.
- The two places drift, which is what `D-SKL-088` began to count on the same
  day. A clause that promises an edge the page no longer states.
- The clause lands on items nobody would misread, and every brief grows for the
  two that got a misread.

## Since then

Read over all 143 items on 2026-09-01, against the 23 pages in
`knowledge/documents/`. Four more take a clause, in the same shape as the two
sighted ones. The changelog directory in `deprecation`, `breaking` and
`changelog`, where the item names one directory and the page names two on a
backport. And the release target in `breaking`, which says to confirm with the
release managers and never says where the rule is. A fifth, `breaking`'s matcher
item, named `typo3_rule_lookup` with no `documentId`.

Everything else compresses a procedure rather than a judgement, and stayed as it
is. So the assumption above holds for the shape rather than for the two: six
items of 143 summarize a decidable rule.
