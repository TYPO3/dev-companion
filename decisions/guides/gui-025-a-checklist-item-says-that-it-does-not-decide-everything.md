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

**An item that summarizes a rule carries a clause saying it does not decide the
whole of it, and the page it already names carries the edge itself.**

Two sessions acted on an item and never opened the page behind it, which is what
a summary good enough to act on costs.

## Evidence

- `feedback/2026-08-27-145507`: a session read the changelog item's one-sentence
  summary, judged from it that a bugfix touching only an `@internal` class owed
  no entry, and never opened `core/contribution/changelog`, where that edge is
  decided. It says the summary "is good enough often enough that the document
  never gets opened".
- The second is the same shape one step further in: a session dropped the
  deprecation sweep on a three-statement diff because the item read as
  unconditional, and reports that it would drop it again.
- Neither is reached by the `when` line or the `tool` field. Both are read by
  somebody looking for a better answer, and a caller who has one is not looking.
- `knowledge/task-intents.json` holds 143 items across 32 intents, at a median
  of 248 characters, and every intent already names the document its rules come
  from.

## Decided

- Both places, and they say different things. The item says that it does not
  decide everything and names the id that does; the page states the edge.
- Asked on 2026-09-01, because it changes what every brief weighs, which
  `documentation/records/judging.rst` says is never changed quietly. The
  maintainer took both places over either one alone.
- The clause is not the edge. An item that lists the cases it does not decide is
  the page in the brief, which is the size this was weighed against.
- Only items that summarize a rule take one. An item stating what to do, with no
  page behind it, has nothing to disclaim.

## Assumed

- That the two sighted items are representative of the ones that summarize. The
  sweep over the remaining items is what would show otherwise.
- That a reader who is told an item is partial opens the page. Neither sighting
  tested that, because neither item said so.

## Wrong if

- A session reads the clause, opens nothing, and acts on the summary anyway.
  Then the lever is delivery rather than wording, and what is left is handing
  the page over at the step rather than naming it.
- The two places drift, which is what `D-SKL-088` began counting on the same
  day: a clause promising an edge the page no longer states.
- The clause lands on items nobody was going to misread, and every brief grows
  for the two that were.
