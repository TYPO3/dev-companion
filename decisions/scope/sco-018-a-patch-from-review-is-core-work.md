---
id: D-SCO-018
title: A patch from review is core work
date: 2026-11-27
status: open
coveredBy:
  - ScenariosTest::aPatchFromReviewReachesTheCoreCheckout
---

# D-SCO-018 — A patch from review is core work

**`Scope::CORE_WORK` counts "patch from review" as core work. A patch on review
is a change on review.typo3.org, because elsewhere it is a pull request.**

The reading of the contract cases on 2026-11-27 found that `SKILL-13` reached
the wrong skill. Its words carried no other core marker, and a session that is
about to fetch a patch names no path yet.

## Evidence

- The prompt of `SKILL-13` is "Pull down that patch from review and get it onto
  current main so I can run the tests against it."
- Before the change, `typo3_task_guide` confirmed `tests` and matched
  `patch-checkout` only weakly. The brief named `typo3-extension-testing`.
- After it, the brief names `typo3-core-patch-checkout` and
  `typo3-core-patch-development`.
- The guide places a call by its paths and its words alone. A root set to a core
  checkout does not change the answer.

## Decided

- The marker is the phrase the case uses and nothing wider. "review" alone stays
  out, because every audience reviews.
- The root of the installation stays out of the decision. Such a change reaches
  every brief and asks for a measure of its own.

## Wrong if

- A task outside the core says "patch from review" and gets the core workflow.
  Then the phrase needs a second marker beside it.
- A session in a core checkout fetches a patch in other words and reaches the
  testing skill again. Then the root of the installation has to count.
