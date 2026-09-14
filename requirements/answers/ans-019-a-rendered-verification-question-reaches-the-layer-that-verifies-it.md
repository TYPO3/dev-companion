---
id: R-ANS-019
title: 'A rendered-verification question reaches the layer that verifies it'
status: held
restsOn: [D-KNW-017]
heldBy:
  - HintsTest::aRenderedVerificationQuestionReachesTheLayerThatVerifiesIt
  - HintsTest::theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome
---

# R-ANS-019 — A rendered-verification question reaches the layer that verifies it

**A caller who asks whether something renders correctly learns which layer
establishes that, without Playwright, a browser or an end-to-end test in its own
words.**

The knowledge is not what is absent. `browser-tests` and its two neighbours say
what a browser test is for. They say that a functional test through
`executeFrontendSubRequest()` is a render test rather than a frontend one, and
what the core's own suite does. Every route to them opens on vocabulary the
caller has to supply first.

The caller who needs the layer is the one who has not yet decided that the
question involves a browser. A session that verifies an element from the HTML it
curled does not ask for Playwright. It asks whether the page came out right, and
that is the point at which the layer is still cheap to choose.

So the answers such a question does reach owe the crossing, and so do the
prompts this repository measures itself with. A scenario that asks for the
outcome, a smoke test before a deployment, browser coverage after a regression,
has to reach the cell as well.

## From

`feedback/2026-08-01-003533` (2026-08-01), a TYPO3 14 testimonials session in
`/home/benji/projects/site-new`. It verified the rendered frontend and the
backend page-module preview from curled HTML and vendor source, in a project
that already had a Playwright harness.

Measured on 2026-08-03: all four rendered-verification phrasings `D-KNW-017`
lists reached `content-elements` or `content-element-preview` and nothing that
named a test layer. `bin/cli hints:coverage` reported `browser-tests` among the
hints no scenario prompt reaches. `SKILL-06`, the scenario written for it,
reached no hint at all.
