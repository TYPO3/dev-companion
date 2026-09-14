---
id: D-GUI-014
title: Looking at a change is an intent of its own
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefRecognizesLookingAtAChangeInABrowser
  - HintsTest::aBriefRecognizesLookingHoweverTheSessionPhrasedIt
  - HintsTest::aWidenedNeedleReachesNeitherTheSuiteNorTheProbe
  - KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther
---

# D-GUI-014 — Looking at a change is an intent of its own

**`typo3_task_guide` recognizes a look at a change in a live installation as
work of its own, and names `any/testing/browser-check` on both sides of the core
boundary.** Four filed sessions did that work with the page in an answer they
had already read. The brief that knew what the task was saw nothing at all.

## Evidence

- Measured in this worktree on 2026-08-18, before the change. "Prove a rendering
  change in the browser after fixing a frontend crash" matched no intent with no
  path, with an extension path and with a core path. So the brief named no skill
  and no guide; "take a screenshot of the page module" matched none either.
- The four sessions are the same shape from four sides.
  `feedback/2026-08-18-074226` verified a render change in a browser in a
  project checkout. `feedback/2026-08-10-182417` reviewed a backend CSS patch in
  a core one and told its reader five times that it could not judge the change
  by eye. `feedback/2026-08-17-205945` and `2026-08-17-212218` shipped six
  backend previews with no check. Each had `any/testing/browser-check` in the
  `guides` key of `typo3_project_describe` and none called `typo3_rule_lookup`.
- `browser-tests` is the nearest intent and is other work. Its needles are
  `playwright`, `e2e`, `spec.ts` and `acceptance test`, and its first checklist
  item is to read the Playwright reference before the first spec. It routes
  `typo3-extension-testing`, a whole test layer for a session that wants to see
  something once.
- The same boundary settled once already, one step further in. `D-SKL-045` put
  the browser step in the content-element skill on the argument that "looking is
  not a test layer". That is what `browser-check` opens with: a spec asserts
  what somebody already knows.
- The other page the words reach is already routed. `D-KNW-071` built
  `core/testing/proving-a-rendering` and put its route in the scratch-probe
  paragraph of `typo3-core-patch-review` and the throwaway-test rules of
  `typo3-core-issue-triage`. So it is out of a brief's reach and in reach of the
  two skills whose work grants the probe.

## Decided

- **An intent rather than `browser-tests` widened.** A wider intent would hand
  that intent's checklist and its skill to a session that only looks. The words
  it would have to reach, a browser, a screenshot, are not the words a request
  for a suite uses.
- **The needles are the act and not the subject**: `in the browser`,
  `in a browser`, `browser check`, `screenshot`, `visually`. `browser` alone
  stays out, because it is `browser-tests`' subject as much as this one's. A
  needle that reaches both intents is the false route `D-SKL-013` watches for.
- **It names no skill on either side.** No published workflow owns the look. The
  content-element skill carries it as one step of a build, which is a step in a
  workflow rather than a workflow to load.
- **`changesNothing`, so the page survives a review brief.** The core session
  that needed it was on a patch review, and a brief that changes nothing routes
  only what changes nothing either (`D-SKL-039`).
- **One page on both sides.** `guide` and `guideCore` name
  `any/testing/browser-check`, which is the first `guideCore` any intent
  carries. What made the field empty everywhere was that a core intent's page
  would be the core's own contribution process. This page has scope `any` and
  writes up the core checkout in its own sections.
- **No intent names `core/testing/proving-a-rendering`.** It answers what a
  TypoScript snippet renders rather than how to look at a change. The needles
  here are about the look, and its two skill routes are the ones its own work
  arrives through. Whether the same question needs an `any` page outside the
  core is `feedback/2026-08-18-081100`.
- **No `rulesQuery`.** The three documents a brief searches are the core's
  contribution process, and a brief for a look has nothing to take from them.

## Assumed

- That a session which only looks says so in the words above. "Check the fix
  works" names no browser and nothing here recognizes it. That is the same limit
  every intent has and has no measure for this one.
- That the page serves the core side as well as the one it came from. The core
  session on record never opened it.

## Wrong if

- A brief names the page for a session that writes a suite. Then the needles
  reach the subject rather than the act, and `browser` is in the corpus
  somewhere this visit did not look.
- A session gets the page in the brief and needs the probe instead. Then the two
  questions are one kind of work after all, and the core side is
  `core/testing/proving-a-rendering`.
- A session reports that the guide arrived with the work and the view still
  shipped unverified. Then the route was not the gap, which is where
  `D-SKL-045`'s own first **Wrong if** would land as well.

## Since then

**The assumption does not hold: a session that only looks says so in words these
five needles do not reach.** Measured on 2026-08-24. The reported call
reproduces, and the needle that missed is this entry's own title. A needle is
one word-bounded phrase and an adjective inside it breaks the match. Four more
phrasings, each a filed session's own words, reach nothing, and across the 49
prompts in `scenarios/` the intent fires on none.

Ten needles join and each is the act rather than the subject; `browser` alone
stays out. Both of the first two **Wrong if** ran rather than stood as argument.
No suite wording reaches it and neither does the probe's, so the proof page
keeps what it owns. The reported call gets its answer in English and not in
German, correctly: the match is lexical against an English corpus.
