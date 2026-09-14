---
id: D-ANS-084
title: 'A curated phrase crosses the domain gate'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aSymptomReachesTheHintThatExplainsItFromAnotherDomain
  - HintsTest::aTypeScriptTestPathIsNotAnsweredWithPhpunit
  - HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten
  - HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay
---

# D-ANS-084 — A curated phrase crosses the domain gate

**A hint outside the domains a query selects is a candidate where the query
spells out one of its multi-word `appliesTo` phrases. No selected hint may claim
that phrase.**

The gate asks the query where the work belongs, which is what a task description
says and the opposite of what a symptom says. A phrase somebody curated is the
query they wrote it for, so it crosses. The claim check keeps it to the measured
case, because a phrase the selected layers carry themselves is one they can
answer.

## Evidence

- The measurement `D-ANS-081` asked for, run on 199 queries. Those are the
  twelve of the sweep and the recorded misses beside them, every hint title, and
  every forward and contract prompt, before and after.
- Any curated pattern past the gate crosses on 39 of them. It displaces rather
  than adds, because an answer holds six. One word of
  `css-icon-text-layout-stability`, "label", "icon", pushes
  `site-set-labels-and-layouts` and `extbase` out of five label and language
  answers.
- A phrase of several words crosses on 9 and undoes `D-KNW-067`. "unit test"
  stands curated on `core-tests`, `project-extension-tests` and
  `unit-test-doubles` as well as on `javascript-unit-tests`. So a `.ts` test
  path gets PHPUnit test doubles again.
- The phrase no selected hint claims crosses twice. "the content elements render
  in reverse order" reaches `datahandler-placement`, and a sitepackage task that
  says "backend form" outright reaches `tca-formengine`. No answer lost an entry
  anywhere, and the sweep's two negative controls still return nothing.
- The comparison takes a phrase by its terms rather than as written, or two
  hints that claim one phrase in different word forms claim two.
  `javascript-unit-tests` carries "unit test" and `unit-test-doubles` carries
  "unit tests", and the `.ts` path failure came back through that gap.

## Decided

- Only the task text opens the gate. A path carries its domain in its extension,
  so a pattern that matches one says nothing the gate did not already have.
  `R-ANS-026` is the requirement that a path decides the subsystem.
- Only a phrase of several words. A one-word pattern is what a hint files under
  rather than a wording somebody anticipated, and the domain gate exists to
  place one.
- No field of its own reports the crossed gate. `domains` says what the gate
  selected and now says what may come back from outside it. That is one sentence
  in the schema rather than a second list on every answer.
- `typo3_hint_lookup` says on the `task` parameter and in the `routing` block
  that a symptom is a query it takes. That is what makes a caller in a debug
  session try it at all.
- Rejected: a gate open only where the paths say nothing. It keeps `D-KNW-067`
  as well. It costs the caller who stands in the file the symptom showed in,
  which is the caller this is for.

## Assumed

- That two crossings over 199 queries is the shape of it rather than an artefact
  of a corpus written by subject. A corpus curated with symptoms in its
  `appliesTo` would cross more often, and nothing here says how much more.
- That a claimed phrase means the selected layers can answer. It means the index
  holds them for the words, which is the same evidence the gate itself runs on.

## Wrong if

- A session reports a hint from a layer its query never meant, and the hint
  crossed on a phrase rather than came through the gate.
- A symptom whose mechanism is in another layer misses because a selected hint
  claims its phrase by the way. Then the claim check turns from a guard into the
  gate.
- `bin/cli hints:coverage` or the sweep shows crossed gates become ordinary as
  the corpus grows. That would make the rule a second matcher rather than an
  exception to the gate.
