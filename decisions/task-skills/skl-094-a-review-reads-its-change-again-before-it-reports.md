---
id: D-SKL-094
title: A review reads its change again before it reports
date: 2026-09-15
status: open
coveredBy: []
---

# D-SKL-094 — A review reads its change again before it reports

**The review skill's report step reads the change again where time has passed
since the first read, and `system-extension-boundaries` states the boundary.**

A review read its change on the first day. It found the CI vote and two
alternatives on the second, by a call it made for another reason. The same
session reports the boundaries hint as three sentences it would not fetch again.

## Evidence

- **The report.**
  [`feedback/2026-09-15-073730`](../../feedback/archive/2026-09-15-073730-the-review-skill-s-order-found-the-blocking.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5`. A strength report with two
  costs in it. What the strength is evidence of went into `D-ANS-146`.
- **The skill said nothing about a second read.** Its establish step reads the
  change once, and the report step holds the commit against `HEAD` and nothing
  against the server. Votes, messages and a change pushed beside it arrive there
  with no signal in the checkout.
- **The boundaries hint said nothing checkable.** "Keep changes inside the
  owning system extension unless a cross-extension contract really changes" is a
  sentence a reader agrees with and cannot apply. `D-ANS-146` names it as the id
  a session read past on 2026-09-03 too.
- **Read in `.checkouts/` on 2026-09-15, on `12.4`, `13.4`, `14.3` and `main`.**
  `typo3/sysext/impexp/composer.json` requires `typo3/cms-core` and nothing
  else, and `fluid_styled_content` requires `core`, `fluid` and `frontend`, on
  every branch. `Build/Scripts/checkIntegrityComposer.php` compares each
  extension's constraints against the root and reads no call. Nothing below
  `Build/Scripts/phpIntegrityChecks/` reads one either.
- **The two answers the report doubted were right.** `type=feature` on `14.3`
  answers nothing because the branch holds no feature entry, and the session's
  own read of the checkout agrees. Step 5 with nothing to change.

## Decided

- **One paragraph at the head of the report step.** Where time has passed since
  the first read, the session reads the change again before it writes. The
  maintainer set the measure on 2026-09-15: not a day, but how active the patch
  is, and hours on a busy one. The skill's contract moves by one step, which is
  why this entry exists beside the change.
- **The boundaries hint states the boundary as the two facts a reader can
  check.** The packages an extension's `composer.json` requires, and `@internal`
  on what another extension may not use. It says that nothing checks either, so
  a reviewer knows the finding is theirs to make.
- **Against a sentence on `review readiness` answering six sections.** One
  applied, the session says. What decides that is the query rather than the
  page, and `D-ANS-101` holds where the floor sits.

## Assumed

- That the root autoloader is why a cross-extension call works in a core
  checkout. Read off the layout rather than provoked: no session has installed
  one system extension without another and watched it fail.

## Wrong if

- A review reads the change twice within the hour and reports nothing new,
  session after session. Then the paragraph costs a call and buys nothing, and
  the condition is what to sharpen.
- A session fetches `system-extension-boundaries` and still reports it as
  generic. Then the two facts are not what a reviewer needs at that boundary.
