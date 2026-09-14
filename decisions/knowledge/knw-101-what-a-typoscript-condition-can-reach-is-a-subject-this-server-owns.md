---
id: D-KNW-101
title: 'What a TypoScript condition can reach is a subject this server owns'
date: 2026-08-18
status: confirmed
coveredBy:
  - HintsTest::aConditionIsAnsweredWithWhatItIsHanded
  - HintsTest::whichGlobalsAConditionCanReadIsBoundToItsMajor
---

# D-KNW-101 — What a TypoScript condition can reach is a subject this server owns

**What a TypoScript condition gets, and which globals have a value when it runs,
is inside this server's boundary and absent from it. The feedback goes to the
queue.**

A session that repaired an extension's own condition on v14 read the order out
of three core branches by hand. The migration the changelog prescribes, read the
page off the request, has no request to read it from where a condition runs.

## Evidence

- The feedback's own query still reaches nothing. Run in this checkout on
  2026-08-18, two probes classify `typoscript`. Those are
  `bin/cli hints:probe "typoscript condition variables page request v14"` and
  `bin/cli hints:probe "TSFE removed TypoScript condition ExpressionLanguage provider"`.
  They take 21 hints as candidates and match none of them.
- The subject is absent from the corpus rather than badly worded. `knowledge/`
  and `skills/` carry no occurrence of `ExpressionLanguage`, `TYPO3_REQUEST`,
  `$GLOBALS['TSFE']` or `AfterPageAndLanguageIsResolvedEvent`.
- The report's decisive claim holds. On `.checkouts/13.4`,
  `frontend/Classes/Middleware/TypoScriptFrontendInitialization.php:123` assigns
  `$GLOBALS['TYPO3_REQUEST']`, and that middleware runs before the one that
  compiles TypoScript. On `.checkouts/14.3` and `main` the frontend's only
  assignment is `frontend/Classes/Http/RequestHandler.php:98` and `:103`, which
  the middleware stack wraps. So the global is unset while the matcher evaluates
  conditions. A fix that rests on it passes on 13 and fails in silence on 14.
- Two corrections to how the report gets there, neither of which changes that
  verdict. `PrepareTypoScriptFrontendRendering` is not gone on 14.3; what is
  gone is `TypoScriptFrontendInitialization`, whose work moved into it. And 13.4
  assigns the global a second time, in
  `PrepareTypoScriptFrontendRendering.php:184`, after the `frontend.typoscript`
  attribute is already set. So it is not what a condition on 13.4 sees either.
- Two places assemble the variable set. `.checkouts/14.3`'s
  `PrepareTypoScriptFrontendRendering::prepareConditionMatcherVariables()`
  returns `request`, `pageId`, `page`, `fullRootLine`, `localRootLine`, `site`
  and `siteLanguage`; 13.4 returns those plus `tsfe`. Then
  `IncludeTreeConditionMatcherVisitor::initializeExpressionMatcherWithVariables()`
  wraps `request` in a `RequestWrapper` and adds `context` and `tree`. It adds
  `frontend`, `backend` and `workspace` where the aspect is there, and unsets
  `pageId`, `localRootLine` and `fullRootLine` before it constructs the
  `Resolver`.
- The version-neutral way in is real. `AfterPageAndLanguageIsResolvedEvent` is
  byte-identical on `.checkouts/13.4`, `14.3` and `main`.
  `PageInformationFactory::create()` dispatches it, `13.4:116`, `14.3:127`. That
  runs in `TypoScriptFrontendInitialization` on 13.4 and inside
  `PrepareTypoScriptFrontendRendering` on 14.3, before the condition match in
  both.
- The third covered LTS is a different reading and was not made here.
  `.checkouts/12.4` carries the event class but no `PageInformationFactory`, so
  where the core dispatches it relative to the condition match is open.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3 across a version boundary, and writing it is the
  todo's work.
- `normal`, not the `low` the card arrived at. The failure the gap produces is a
  wrong condition verdict with no error, no log and no red build. It has a
  version bound, so a session that verifies on one major ships the bug on the
  other. Not `high`: one session in one directory reported it.
- The changelog half of the suggestion is not built. `typo3_changelog_lookup`
  answers from the changelog an installation ships and from docs.typo3.org. A
  note per entry where no request is in scope would mean a commentary corpus
  keyed by changelog number. This server would keep it against text it does not
  own. It would say once what a hint says once. The hint is also what a
  TypoScript task reaches with no issue number in hand.
- Inside the boundary. `doesNotCover` excludes PHP source as code. What the
  feedback asks for is when a global gets its value relative to the middleware
  order, a version-bound fact rather than a signature.
- This entry does not copy the feedback's own account down. The two corrections
  above are what the checkouts say, and this run confirmed its `RequestWrapper`
  and unset-again claims rather than took them.
- This entry takes no other card over. The two further TypoScript feedback from
  the same directory ask different questions. How an extension registers a
  condition provider, and how a session proves a verdict against a live
  frontend. So this one carries only what it reports.

## Assumed

- That the session's account of its own installation holds. Nothing here started
  a frontend or evaluated a condition. The order comes out of the checkouts.
- That this is one statement rather than two. What a condition gets, and what an
  extension does to get a page record into one, may turn out to be a hint each.

## Wrong if

- A hint reaches either probe query once the corpus grows around it. Then the
  gap was the placement rather than the statement, and this was step 2.
- Something in the default 14 frontend stack populates
  `$GLOBALS['TYPO3_REQUEST']` before the TypoScript compile. A middleware this
  run did not read, or a path other than the default stack. The statement is
  then a default rather than a rule.
- 12.4 turns out to dispatch `AfterPageAndLanguageIsResolvedEvent` after the
  condition match. The hint binds `since: 13` rather than holds on every covered
  major, and the recommendation is wrong for the oldest one.

## Confirmed on 2026-08-18

Written as a hint of its own, and both probe queries that reached nothing now
reach it. The read the entry left open came out the other way from the **Wrong
if**. The core dispatches the event ahead of the condition match on every
covered major, so the recommendation has no bound. What binds is the accessor a
listener takes the record off, since the event carries different objects across
the boundary. Both globals have a value before the match on the oldest major
too, so that statement binds a major later than the entry had it.
