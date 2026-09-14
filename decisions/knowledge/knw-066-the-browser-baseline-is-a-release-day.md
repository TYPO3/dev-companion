---
id: D-KNW-066
title: 'The browser baseline is a release day'
date: 2026-08-10
status: open
coveredBy:
  - HintsTest::aQueryNamingAModernCssFeatureReachesTheBrowserTarget
  - HintsTest::theBrowserTargetKeepsTheArgumentsItRefuses
---

# D-KNW-066 — The browser baseline is a release day

**`css-browser-target` states the policy as a release day and names what is not
evidence of it; it does not carry a feature-by-feature support table.**

A session that reviewed a core patch asked the hint before it chose a technique
and got the policy alone. It then took the checkout as the answer, built on CSS
anchor positioning because the core already ships it, and shipped it. The
developer found it broken in Firefox.

## Evidence

- `feedback/2026-08-10-101627`. The session verified in the headless Chrome of
  the unitJavascript suite and in a Chromium-driven backend, and both passed. It
  measured Firefox afterwards through Playwright: `CSS.supports` false for
  `anchor-name`, `position-anchor`, `anchor()` and `anchor-size()`. Cost was one
  wrong architecture decision carried through several rounds of rework, found by
  the developer rather than by the session.
- The precedent it read is real. `Build/Sources/Sass/component/_dropdown.scss`,
  `element/typo3-backend-workspace-selector.scss` and
  `element/typo3-formengine-element-datetime.scss` on `main` all use
  `anchor-name` and `position-anchor`, one of them `anchor-size(width)` as well.
  So the checkout says yes and the baseline says no, which is exactly the trap
  the hint has to name.
- `Build/.browserslistrc` is identical on `main`, `14.3`, `13.4` and `12.4` and
  was last touched in 2019. Nothing in `Build/package.json` or the Gruntfile
  configures it; `autoprefixer()` in the Gruntfile's postcss task picks it up
  from the build directory. It therefore decides the prefixes and gates nothing.
  A feature no engine implements passes the build unchanged.
- The policy itself came from the maintainer of this repository during the
  judgement run. The target is the browser versions that were current on the day
  the release in question appeared, per release line rather than per LTS. The
  hint said "on the day of the corresponding TYPO3 LTS release" and "of that
  target release year". Both are the same rule read coarsely.

## Decided

- The hint carries the sharpened policy and the three engines, with Gecko named
  as the one that usually decides. It says that core usage is not evidence, and
  that a Chrome-only verification is not evidence either. That is what would
  have stopped this session, and it holds for every feature rather than for the
  six the feedback listed.
- No per-feature table. Which of anchor positioning, `@starting-style`,
  `popover`, `:has`, container queries and `light-dark()` is inside a given
  release day is a fact. This repository cannot verify it from `.checkouts/`. It
  would be a copy of support tables nobody here reads again, and it turns while
  nothing fails. The prose rule against a snapshot that reads as a fact long
  after it stopped to be one is
  `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch`. A table of six is
  that rule's worst case.
- No tool either. A `lookup` that answers "is feature X inside the v14 baseline"
  needs a support corpus this server does not have. It needs a release-date axis
  it does not carry. So it would report `unavailable` for most of what callers
  ask it, the case
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  says buys the caller nothing.
- The hint names `Build/.browserslistrc` as what it is. A session that looks for
  the target finds that file. It reads as a declaration of support while it is a
  prefix configuration from before any covered branch.

## Assumed

- That Gecko is the engine that decides often enough for the sentence to be
  worth its line. It was here, and it was for `:has`, container queries and the
  Popover API before it. WebKit takes that place occasionally, which is why the
  hint says every engine has to ship it rather than naming one to check.
- That the reported Firefox measurement is correct. The session made it in its
  own Playwright harness and nothing here re-ran it. The core call sites above
  are what makes it plausible rather than what confirms it.

## Wrong if

- A session reports that it read this hint and still shipped a feature outside
  the baseline. Then the rule is not what was absent and the concrete table is,
  whatever it costs to keep.
- Somebody establishes that the core's declared target is a file rather than a
  date, a maintained browserslist or a documented support statement. That file
  answers the question directly. Then the hint should name it as the source
  instead of a procedure around it.

## Since then

A second report of the hint came from the other side. It stopped a Blink-only
feature on its way into a core patch with a rationale attached. That is the
first **Wrong if** with the sign reversed.

**A policy hint has to be reachable by the vocabulary of the thing it governs,
and this one was reachable by its own.** Both sessions reached it by id, and the
session's own task text returned five other CSS hints and not this one. Worse, a
query that names a feature reached a second copy of the policy in the coarse
wording this entry sharpened out of the first. That read as permission. So the
duplicate defers, and the vocabulary gains the feature words a session types
when it is about to adopt one.

The press gets a refusal. This hint is core-scoped where every caller reads the
instructions.
