---
id: D-ANS-140
title: A kind of work names the procedure it owes
date: 2026-09-02
status: open
coveredBy:
  - KnowledgeTest::aChangeThatEndsInABackendUiIsNamedTheBrowserCheck
---

# D-ANS-140 — A kind of work names the procedure it owes

**A procedure a task owes is not the write-up of it, so it is named beside the
intent rather than by widening the intent's own words to reach it.**

## Evidence

- `feedback/2026-09-02-135138`. A session had `any/testing/browser-check` in the
  guides list its first call returned, called `typo3_rule_lookup` zero times,
  and wrote its own throwaway browser spec roughly fifteen times. Its brief for
  the backend module returned `guides: []`.
- The document is in `knowledge/task-intents.json` as the `browser-check`
  intent's own guide, and that intent matches on fifteen phrases about looking:
  "in the browser", "screenshot", "renders correctly". A backend module task
  says none of them, which is the whole of why it did not fire.
- Widening those phrases to reach it was tried and measured here: adding
  "backend module", "content element", "page module" and two more confirms the
  browser-check intent on a backend module brief, and
  `KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther` fails on
  two of its cases. `D-SKL-051` is what that test holds: a second confirmed
  intent arrives stated as fact, with a checklist, a skill and its own tools.

## Decided

- An intent may declare `owes`: the procedures that kind of work owes, whatever
  the task text says. They are named among the guides and confirm nothing — no
  checklist, no skill, no tool line.
- `backend-module`, `backend-ui` and `content-element` owe
  `any/testing/browser-check`. Each ends in something an editor looks at, and
  none of them says so in the words a task is written in.
- The two halves are kept apart in the field's own description, because a caller
  reading a guide list has to know whether it is the write-up of the work or a
  thing the work owes.
- Nothing else is given an `owes` yet. Three intents is what the evidence
  reaches, and a fourth is added when a session reports the same shape rather
  than by sweeping the catalogue.

## Assumed

- That a guide named without a confirmed intent is read. The list is rendered
  the same way either half arrives, and one session's silence about the
  orientation list is what says the placement matters rather than the wording.
- That the three intents are the ones that end in something to look at. A
  frontend rendering change is answered by the same procedure and is not one of
  the three, because no intent names that work today.

## Wrong if

- A session reports the browser check arriving on a brief where nothing was
  rendered. The `owes` would then be attached to the kind of work rather than to
  what the work produces, and the distinction is finer than an intent.
- The same session skips it again. What is left then is the delivery of a named
  guide rather than the naming, and `D-FBK-054` is where that reading sits.
