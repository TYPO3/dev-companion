---
id: D-ANS-074
title: 'A path-narrowed suite list names what it withheld'
date: 2026-08-11
status: open
---

# D-ANS-074 — A path-narrowed suite list names what it withheld

**A suite list narrowed by paths says which domains no path reached, and that a
path in one of them means a second call.**

The answer named the two domains it kept and offered the whole list to anybody
who wanted it. It said nothing about a path set that changes. A rework that grew
a TypeScript file mid-session went to `runTests.sh -h` and a grep for a suite
name this server holds.

## Evidence

- Re-run on 2026-08-11 with the eight paths `feedback/2026-08-10-182435` reports
  and `targetVersion=15.0`. Domains `css` and `fluid`, and the same ten suites
  in the same order the feedback quotes. The feedback describes the server as it
  is now.
- What that answer says about its own scope is one sentence: "Narrowed to the
  css and fluid domain(s) the given paths touch. Suites outside them cannot fail
  on this change; call again without paths to see all of them." It names what it
  kept. Its escape hatch is for a caller who wants everything rather than for
  one whose paths have moved.
- The narrower answer withheld 17 of the 27 suites that branch offers, in five
  domains no path reached: `php`, `typescript`, `typoscript`, `docs` and
  `xliff`. `lintTypescript` and `unitJavascript`, the two the session looked
  for, are among them.
- The cost was two round trips against the checkout for a fact in
  `knowledge/hints/testing.json`. The session reports that nothing prompted the
  second call.
- [`R-ANS-030`](../../requirements/answers/ans-030-a-bound-on-an-answer-is-asked-for-and-never-applied-by-default.md)
  already states the rule on another payload. A caller asks for a bound, and the
  answer counts what it left out either way. The `paths` argument is the ask;
  the count is the half this answer does not carry.

## Decided

- Step 4 of the ladder, wording. The suites, their domains and their version
  bounds are all in `knowledge/hints/testing.json`, and the tool reached exactly
  the right ten. Only the sentence that says what the ten are a selection of is
  absent.
- The answer names the omission by domain and counts it, never lists it.
  Seventeen suite names is the answer the narrower list exists to avoid; five
  domain names and a number is one line.
- The condition sits where the narrower list does. A caller that reads the tool
  description once and then holds an answer for the rest of a session is the
  case this answers. So the answer is what has to carry it.
- Queued rather than closed on the spot. `src/Tool/TestRunGuide.php` builds the
  block, and the counterpart in `outputSchema()` is a contract, which
  [judging.md](../../documentation/records/judging.rst) puts on the reviewed
  side of the line.
- Priority `normal`, set here: one session and one report, against a change that
  needs nothing established first because `R-ANS-030` already carries the rule.
  What keeps it off `high` is that the answer was correct and the session paid
  two greps rather than shipped something wrong.

## Assumed

- One session. No other feedback in the corpus reports an answer that went stale
  under an input set that grew. `bin/cli feedback:list` on 2026-08-11 holds 13
  open, all from one checkout.
- That the withheld domains, named, are what prompts the second call. It is the
  lever `R-ANS-030` rests on, because a count is what makes a silence readable.
  No recorded run has measured a session that acts on one.

## Wrong if

- A session grows its path set, reads the withheld line, and still does not call
  again. Then the answer is not where that habit forms, and the tool description
  or the patch skill is.
- A report calls the withheld line noise, which it would be on a call whose
  paths already reach every domain. Then it belongs only where the answer
  withheld something.
- A session reads it as an offer and calls again with the same path set. That
  would say the sentence reads as the escape hatch beside it rather than as a
  condition.

## Since then

The sentence exists and is in the answer. Called on 2026-08-23 with one
TypeScript path at `targetVersion: 15.0`, `typo3_test_run_guide` says: "Narrowed
to the typescript domain(s) the given paths touch. … No given path reached php,
fluid, typoscript, xliff, docs and css, which leaves 20 suites out. A path
landing in one of those domains means calling again." Named by domain and
counted, never listed. `withheld` carries the same two fields in the data half.

All three **Wrong if** wait on a session and none has reported. No feedback
since 2026-08-11 describes a path set that grew mid-session, reads the withheld
line as noise, or calls again with the same paths. So nothing has measured what
the sentence does to a session's habit, which the **Assumed** above already
said.
