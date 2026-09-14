---
id: D-ANS-140
title: A kind of work names the procedure it owes
date: 2026-09-02
status: open
coveredBy:
  - KnowledgeTest::aChangeThatEndsInABackendUiIsNamedTheBrowserCheck
---

# D-ANS-140 — A kind of work names the procedure it owes

**A procedure a task owes is not the write-up of it. So it stands named beside
the intent rather than reached through wider words in the intent itself.**

## Evidence

- `feedback/2026-09-02-135138`. A session had `any/testing/browser-check` in the
  guides list its first call returned. It called `typo3_rule_lookup` zero times,
  and wrote its own throwaway browser spec roughly fifteen times. Its brief for
  the backend module returned `guides: []`.
- The document is in `knowledge/task-intents.json` as the `browser-check`
  intent's own guide. That intent matches on fifteen phrases about a look: "in
  the browser", "screenshot", "renders correctly". A backend module task says
  none of them, which is the whole of why it did not fire.
- Wider phrases to reach it had a trial and a measurement here. "backend
  module", "content element", "page module" and two more confirm the
  browser-check intent on a backend module brief.
  `KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther` fails on
  two of its cases. `D-SKL-051` is what that test holds: a second confirmed
  intent arrives stated as fact, with a checklist, a skill and its own tools.

## Decided

- An intent may declare `owes`: the procedures that kind of work owes, whatever
  the task text says. They stand among the guides and confirm nothing: no
  checklist, no skill, no tool line.
- `backend-module`, `backend-ui` and `content-element` owe
  `any/testing/browser-check`. Each ends in something an editor looks at, and
  none of them says so in the words of a task.
- The field's own description keeps the two halves apart. A caller who reads a
  guide list has to know whether it is the write-up of the work or a thing the
  work owes.
- Nothing else gets an `owes` yet. Three intents is what the evidence reaches. A
  fourth joins when a session reports the same shape rather than through a sweep
  of the catalogue.

## Assumed

- That a session reads a guide named without a confirmed intent. The list
  renders the same way either half arrives. One session's silence about the
  orientation list says the placement matters rather than the wording.
- That the three intents are the ones that end in something to look at. The same
  procedure answers a frontend render change, and it is not one of the three,
  because no intent names that work today.

## Wrong if

- A session reports that the browser check arrived on a brief where nothing
  rendered. The `owes` would then hang on the kind of work rather than on what
  the work produces, and the distinction is finer than an intent.
- The same session skips it again. What remains then is the delivery of a named
  guide rather than the name, and `D-FBK-054` is where that read sits.
