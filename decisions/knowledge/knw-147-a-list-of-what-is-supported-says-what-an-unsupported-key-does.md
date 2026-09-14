---
id: D-KNW-147
title: A list of what is supported says what an unsupported key does
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::aKeyTheSetDefinitionHasNoParameterForIsSaidToBeFatal
---

# D-KNW-147 — A list of what is supported says what an unsupported key does

**A hint that lists what a configuration file may hold says what a key outside
the list does. A bare list reads as "and the rest is ignored".**

The reader who takes that reading writes one key and takes a whole frontend
down.

## Evidence

- `feedback/2026-09-03-235234`. `site-sets` listed the six files a set directory
  may hold and said nothing about a key `config.yaml` may not carry. The session
  put `routes:` into a project set to find out, and every page of every site
  that depends on that set answered HTTP 500.
- Read in `.checkouts/13.4`, `.checkouts/14.3` and `.checkouts/main`.
  `YamlSetDefinitionProvider::createDefinition()` builds
  `new SetDefinition(...$setData)`, and `SetDefinition` is readonly with a fixed
  parameter list. `getObjectConstructionErrors()` turns the `\Error` that
  results into an `InvalidSetException` whose message ends in
  `Invalid properties: <key>`.
- The same reading found a second defect the feedback did not report. That list
  carried `route-enhancers.yaml` bound `since: 13`, and `routeEnhancers` is a
  `SetDefinition` parameter only from 14 — `13.4`'s provider reads no such file.
- `routes` is the site's own key on both branches:
  `StaticRouteResolver::process()` reads `$site->getConfiguration()['routes']`,
  and no `SetDefinition` parameter corresponds to it.

## Decided

- Step 1a of the ladder. `bin/cli hints:probe` reaches `site-sets`, and the
  statement the session needed is not in it.
- Closed on the spot rather than queued: the reading against the checkouts was
  made by the judging run, which is what `D-FBK-052` distinguishes.
- Four statements rather than one — the file list split at its version boundary,
  the v14 file, the fatal key, and the `routes` boundary — because the report
  and the reading it triggered are two findings.
- The hint names the parameters by their class rather than enumerates them. The
  enumeration is version-bound and grew between 13 and 14, and a list that grows
  is what `AGENTS.md` says to name rather than to count.

## Assumed

- That the other lists in the corpus do not have the same hole. Nothing swept
  them, and this reading covers one hint.

## Wrong if

- A session reads the new statement and still treats an unlisted key as ignored.
  Then the sentence is not what carries it and the placement is the question.
- A later major validates a set's keys and reports them instead of fails the
  site, which makes the HTTP 500 half of the statement wrong.
- A sweep of the corpus finds other allow-lists whose "and nothing else" is
  false. Then this was one instance of a sweep rather than one hint.

## Since then

2026-09-04, from `feedback/2026-09-03-235250` and the same reading. The v14
`route-enhancers.yaml` statement gained the two facts that make the file usable.
The core folds the enhancers of the dependencies with
`ArrayUtility::replaceAndAppendScalarValuesRecursive()` in dependency order and
merges the site's own over them. So a set contributes a fragment into an
enhancer another set declares. That fragment is only whole while that dependency
stands, or `EnhancerFactory` refuses it and every request answers HTTP 500.

The report asked for both `since: 13`. They are `since: 14`, which is the same
boundary this entry moved the file list to.
