---
id: D-KNW-102
title: 'Proving a condition verdict against an installation is a procedure this server carries'
date: 2026-08-18
status: open
---

# D-KNW-102 — Proving a condition verdict against an installation is a procedure this server carries

**How a session makes a condition verdict observable in a live frontend is a
document below `knowledge/documents/any/testing/`, beside the browser check.**

A session that repaired an extension's own conditions on v14 had to prove the
break before the fix. A verdict emits nothing and logs nothing, so the proof is
indirect. The first marker the session grepped for was markup both templates
carry, a false positive it held for two round trips.

## Evidence

- The feedback's own query reaches nothing that answers it. Run in this checkout
  on 2026-08-18, `bin/cli hints:probe`
  `"prove that a TypoScript condition matches in the frontend"` reaches
  `typoscript-conditions` and `typoscript-condition-providers`.
  `"how do I check that a TypoScript condition matched on a rendered page"` adds
  `site-set-migration`. All three are about what a condition gets or how an
  extension registers one.
  `"verify a template swap in the rendered frontend output"` classifies as `php`
  and reaches `breaking-without-a-moved-member`.
- The two near documents have another boundary and each says so in its own
  `whenToUse`. `core/testing/proving-a-rendering` is a throwaway functional test
  below `typo3/sysext/frontend/Tests/Functional/Rendering/` run with
  `Build/Scripts/runTests.sh`, for what a snippet renders.
  `any/testing/browser-check` is how a browser in a container reaches a DDEV
  site, for a defect somebody has to see. Neither establishes that a branch ran.
- The trap that cost the round trips is absent from the corpus in every wording.
  `discriminator` occurs once below `knowledge/`, about the exclamation mark in
  a 404 message. Nothing says that two Fluid templates a condition switches
  between usually share their wrapper markup, and nothing anywhere names a
  negative control.
- The feedback's cache claim has the fact right and the conclusion backwards.
  `createPageCacheIdentifier()` puts `constantConditionList` and
  `setupConditionList` into the identifier on `.checkouts/13.4:255`, `14.3:337`
  and `main:338`, and `12.4` hashes the same two into `createHashBase()` at
  `TypoScriptFrontendController.php:1389`. Those lists are
  `IncludeTreeConditionMatcherVisitor::getConditionListWithVerdicts()`, the
  expression mapped to its verdict. So a page whose verdict flips lands on
  another identifier and the cache does not serve the old entry. What still
  needs a flush is the TypoScript itself, which the `caching` hint already
  states. The key of an `@import` target or an `include_static_file` set is the
  file name alone, so an edited `.typoscript` file keeps its parsed include
  tree.
- `D-KNW-101`, judged in this directory on the same day, records that it carries
  only what a condition can reach at evaluation time. It names this question as
  another card's.
- The cost is counted. The cost feedback from the same session puts this cluster
  at 7 of roughly 30 round trips. About 2 of them went to the marker that did
  not discriminate, which is what `D-FBK-027` measures.

## Decided

- Step 1a of the ladder, taken on as a document rather than as hints. The gap is
  a procedure with an order: derive a marker, control it with a negative, decide
  what to flush. A procedure written as statements is a set of sentences nobody
  can follow (`D-FBK-043`).
- Its boundary is the live installation, and `core/testing/proving-a-rendering`
  keeps its scope. Every step of that page is a file below `typo3/sysext/`, a
  fixture and a suite invocation, none of which a site developer can run. A
  wider scope would hand them a procedure that does not apply. What generalises
  is the question, not that page.
- Scope `any`. The session was an extension author at work inside a site
  installation, which is the case that belongs to neither `extension/` nor
  `project/` alone. Nothing in the procedure turns on which of the two the
  caller is.
- It has to carry three things. The marker from a diff of what the conditional
  branch renders against what it replaces. The trap that shared wrapper markup
  is what the obvious grep finds. And the negative control, a page the condition
  must not match, which is what turns one green result into evidence.
- What needs a flush between runs is the read's first question rather than the
  feedback's answer, because the identifier already carries the verdict. The
  document says which change needs which flush; it does not copy the report's
  reason.
- Routed from `typoscript-conditions`, where a caller who asks about a condition
  already lands. A document nothing routes to is the same gap one step further
  in, which is `D-KNW-071`'s own finding.
- `normal`, not the `low` the card arrived at. What the gap produces is a wrong
  verdict believed rather than a slow answer, and the session reports it nearly
  shipped one. Not `high`: one session in one directory reported it.
- This entry takes no card over. `D-KNW-101` answers a different question about
  the same subject, and the cost feedback beside it is cost data for the whole
  session.

## Assumed

- That the procedure is version-neutral. Nothing here started a frontend. What a
  verdict changes about rendered output is a property of the TypoScript rather
  than of the major. That is a reason rather than a measure.
- That one page covers both shapes — the condition that swaps a Fluid template
  and the condition that changes a value in place.

## Wrong if

- A flipped verdict turns out to serve the old page on some covered line. Then
  the flush is the first step rather than a caveat, and the identifier reading
  above is wrong about what it implies.
- A session with the document installed still greps for the first thing that
  looks related. Then the gap was the vocabulary rather than the procedure, and
  it belongs on `typoscript-conditions` as a hint.
- The two shapes turn out to need different steps far enough that the page reads
  as two procedures under one heading. That would say the boundary sits around
  the wrong thing.

## Since then

The document is where this put it,
`knowledge/documents/any/testing/proving-a-condition.md`, beside the browser
check.

The third **Wrong if** is the one a reading answers, and it has not fired. The
page runs as one procedure in six steps rather than as two under one heading.
What does not answer it, the marker only the branch produces, a marker put there
on purpose. Which URL to request, the negative control, and what stands between
two runs. The two shapes share every step and differ in which marker is
available, which is the boundary this sits around.

The other two wait on a session. Nothing since 2026-08-18 reports a flipped
verdict served from a cache on a covered line. Nothing reports a session that
had the document and grepped for the first thing that looked related.
