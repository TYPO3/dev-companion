---
id: D-KNW-040
title: 'What asserts a rendered output is a subject this server owns'
date: 2026-08-03
status: revoked
revokedBy: D-KNW-044
---

# D-KNW-040 — What asserts a rendered output is a subject this server owns

**The search for the expectations that assert a rendered output, before a change
alters it, is inside this server's boundary and absent from it. So the feedback
goes to the queue rather than closes.**

A change to one URI shape moved about 141 expectations across 23 files. The
session found them one failed suite at a time, over roughly fifteen full
functional runs. The one statement this server has near the question tells a
caller to iterate narrowly.

## Evidence

- The tool the feedback names still answers the other question. Called on this
  branch on 2026-08-03 with `paths` set to
  `typo3/sysext/core/Classes/SystemResource/Http/CacheBustingUri.php` and a
  query that names rendered output, `typo3_test_run_guide` returns three suites.
  `e2e`, `functional` and `unit` with their targeted invocations. Which suite
  can fail is what it says; which expectations exist is not in the answer.
- The feedback's own query reaches nothing. `bin/cli hints:probe` with the query
  verbatim classifies it `php` and returns `system-extension-boundaries`,
  `project-build-and-scripts`, `routing-request-handling` and `caching`. None of
  the four is about a test expectation.
- The nearest statement in the corpus points the caller into the expensive loop.
  `core-tests` in `knowledge/hints/testing.json` says to run the single test
  file or method in the loop, because a full functional run costs minutes per
  round. `TestSuiteHints::invocation()` emits the same sentence with every
  answer. Both are right for the ordinary change and wrong for this one.
- No skill says it either. `typo3-core-patch-development` routes the blast
  radius question to `typo3_test_run_guide` twice, once for whether the
  reproduction can be a test, once for verification. Neither is about what
  already asserts the output.
- The shapes the feedback names are in the checkout. Read on `.checkouts/main`
  at `c71b2bdb2f`. `contentMatchRegExp` keys in `ImageConvertIMViewHelperTest`
  and `ImageConvertGMViewHelperTest`; a PCRE at `ImageViewHelperTest:159` whose
  capture group serves as a file path. Three `{$...}` placeholder fixtures under
  `backend/Tests/Functional/Template/Fixtures/`; `FluidEmailTest` in
  `core/Tests/Functional/Mail/`. `CacheBustingUri.php` is still at the path the
  feedback names.
- The same session reported the cost a second time from the other end.
  `feedback/2026-08-02-145128` names it step 9 of an assessment procedure. The
  blast radius belongs to the assessment, and it turned up piece by piece long
  after the change had its shape.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about the core's test corpus, and this run has read this
  repository and one checkout.
- No tool gets built for it. This server does not read the caller's repository,
  so it cannot enumerate what asserts anything. What it can carry is where those
  expectations hide and in which order to look, which is a statement.
- Not step 4, so the iterate-narrowly sentence is not rewritten. It is correct
  for the ordinary change, and the gap is the exception for a change whose
  rendered output has assertions elsewhere.
- The category is not the answer. It arrived as `tool-gap` and needs none.
- The feedback's grep recipe is not copied down. Its author counted eight shapes
  in one change, and which shapes the corpus actually uses is what the research
  settles.
- `normal` rather than `low`, because the cost has a count rather than an
  assertion: fifteen functional runs of several minutes, 23 files, about 141
  expectations. Not `high`, because one session reported it, twice.

## Assumed

- That the shapes generalise past one change. The count came from a move of a
  cache-busting URI, and another rendered value may hide in fewer of them or in
  others.
- That a recipe with shapes in it outlives one with files in it. Test files
  arrive every week, and the research has to write the durable half.

## Wrong if

- The research finds one search that reaches every shape. The gap is then a
  sentence on `core-tests` rather than a recipe, and this entry overstates it.
- A filtered full functional run turns out cheaper than any search. Then what
  the corpus owes is the order alone, and the shapes named are noise a caller
  pays for on every unrelated call.
- The shapes turn out to belong to the image and asset area rather than to the
  corpus. A session that changes some other rendered value would then reach a
  statement for somebody else's paths.

## Revoked on 2026-08-03

The first **Wrong if** is what happened. One search does reach them, over the
whole tree rather than the test files, and for the text around the changed
value. It reaches 24 of the 26 files, and the two it misses hold no expectation.
The shapes are eight forms of one value rather than eight places to look, so the
question this entry queued was the wrong one. What the research settled is where
the files are.

The statement no longer described this server from the same commit, and
`D-KNW-044` carries what holds instead.
