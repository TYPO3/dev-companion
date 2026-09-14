---
id: D-KNW-017
title: A verification question is routed to the layer that verifies it
date: 2026-08-02
status: open
---

# D-KNW-017 — A verification question is routed to the layer that verifies it

**A question about whether something renders correctly reaches the hint that
says how to build it. Nothing on that path names the layer that would verify
it.**

`browser-tests` is reachable only by words that already name the answer. The
caller who needs it most is the one who has not yet decided that a browser plays
a part. That caller lands on `content-elements` instead.

## Evidence

- `bin/cli hints:probe "verifying the rendered testimonials frontend and the backend page module preview"`
  matches nothing. That is the feedback's own subject, in the words a task would
  use. Forty hints come back as the index.
- Three plainer forms of the same question reach `content-elements` and nothing
  else. "verify that the content element renders correctly on the live site" and
  "check the backend page module preview of a content element". And "how do I
  verify rendered output of a content element".
- The feedback's `Query` line does reach `browser-tests`, at
  `appliesTo(22) + text(232)`. It contains the words "Playwright" and "browser
  test". That is the debrief with the name of the layer it had already worked
  out it should have used.
- `browser-tests.appliesTo` is ten terms and every one of them is the answer.
  `playwright`, `browser test`, `end-to-end`, `end to end`, `e2e`,
  `acceptance test`, `accessibility`, `axe`, `wcag`, `contrast`, `spec.ts`. None
  of them is a word for the wish to know whether a page came out right.
- `bin/cli hints:coverage` lists `browser-tests` among the 44 of 66 hints that
  no scenario prompt reaches. So the miss is not measured either.
- The hint the query reaches says nothing about a check. `content-elements`
  carries fourteen statements about an element's registration and its preview's
  render, and none about which layer establishes that either works. It names
  `sitepackage-layout` for the template names and no cell of the test row.
- The answer was already written, in the cell nobody arrives at. `browser-tests`
  holds it: "A functional test with executeFrontendSubRequest() runs no
  JavaScript, applies no stylesheet and speaks no HTTP. It is a rendering test,
  and calling it a frontend test is how a suite ends up with no browser in it at
  all."
- The rule is in the skill as well. `skills/typo3-extension-testing/SKILL.md`
  says to use a browser test "for rendered user journeys, backend interaction,
  JavaScript, or accessibility behavior that cannot be established below the
  UI". It routes to `references/playwright.md` after the layer choice.
- What this server delivered for the task shape points the other way. The
  `content-element` intent in `knowledge/task-intents.json` closes with "Cover
  the persistence of the child records and the order they render in with
  functional tests. A unit test of mocks asserts the mock, and browser behaviour
  that was not tested is reported as untested." That sends rendered coverage to
  the functional layer and licenses a suite without the browser layer.
- The feedback's own premise is withdrawn by its author.
  `feedback/2026-08-01-003736` corrects three siblings, this one among them. The
  "never activated" claim came from a transcript that begins at an anchored
  summary, and the user reports that they saw the skill activated. So the
  trigger is not the lever here, and `documentation/records/judging.rst` would
  not have assessed the self-criticism in any case.
- This entry established nothing about TYPO3. Every probe above is a query
  against this repository as it stands on 2026-08-02.

## Decided

- Step 3 of the ladder, routing. The knowledge is here, the skill carries the
  rule, and a caller who has not already named the layer cannot reach either.
- Queued rather than closed on the spot. Which of the three surfaces carries the
  cross is open: `appliesTo` on `browser-tests`, a statement in
  `content-elements`, or the `content-element` intent's checklist. Each one
  costs something different.
- The judgement extends
  [`D-KNW-008`](knw-008-tooling-is-a-row-the-answer-crosses-not-a-dimension-the-corpus-stores.md)
  rather than contradicts it. That entry checked the cross from inside the
  test-tools row, where `typo3_test_run_guide` names the other cells. This is
  the caller who never enters the row.
- No fix is named. The todo that follows measures the candidates before it
  chooses one, because a term in `appliesTo` is a term every neighbour answer
  then pays for.

## Assumed

- That the four probe queries are what such a session would have asked. They are
  this run's paraphrases of the feedback's Observation, not its transcript,
  which nobody here has.
- That `content-elements` is the correct reach and the miss is the absent cross
  rather than a rank failure. `content-elements` is the right hint for a
  question about a content element; it is simply not a hint about tests.
- That the sentence in the `content-element` intent is a wording that can move.
  It may instead be a considered rule against claims of untested behaviour.

## Wrong if

- The cross exists and a rendered-verification query still reaches only
  `content-elements`. Then it is the rank rather than the route, and
  [`D-ANS-021`](../answers/ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md)
  and
  [`D-ANS-022`](../answers/ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md)
  are where it belongs.
- A wider `browser-tests.appliesTo` pulls it into answers that did not want it,
  so every backend-preview question pays for a test hint.
- The `content-element` checklist sentence turns out to be deliberate. Two
  surfaces of this server that say opposite things about the same layer is worse
  than the gap. Then this is step 5 and a question rather than a route fix.

## Since then

The three candidates had their measure against the four probe queries. The first
cannot win. For two of the four the domain gate drops the hint before any score.
The one term that carried the others put it into two answers it does not belong
in. That is the second **Wrong if** as it happens.

The third's premise stands withdrawn rather than confirmed. The checklist
sentence is a criterion's own words, and its second half is an honesty
obligation rather than the licence this entry read it as.

So the cross is the second candidate, twice, because one statement cannot carry
all four queries. The frontend half on one hint and the backend half on the
other. The prompts that reached no hint have their fix where they already are,
through two terms taken from them. A third had its measure with them and fell
because it answered an unrelated question.
