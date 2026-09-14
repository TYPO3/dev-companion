---
id: D-ANS-097
title: A bounded answer spends its slots on the hints that bind its caller
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aCoreBriefSpendsItsSlotsOnTheCoreHints
  - HintsTest::anExtensionBriefSpendsItsSlotsOnTheExtensionHints
---

# D-ANS-097 — A bounded answer spends its slots on the hints that bind its caller

**Where an answer carries only the strongest few hints, one that declares a
scope the call's paths are not ranks below one that declares theirs.**

`D-KNW-007` decided that no declared scope filters anything, and it decided it
for an answer that lists what it matched. A brief carries four hints per group
of paths, and there the same hint takes a place from one that binds the caller.

## Evidence

- `feedback/2026-08-24-100427` re-run on 2026-08-24 against the corpus as it is,
  with the two paths and the task text it names. Both paths count as `core`, and
  the brief carries `extension-asset-build` (`scope: extension`),
  `public-assets` (none), `project-build-and-scripts` (`scope: project`) and
  `backend-typescript` (`scope: core`). The session used the fourth.
- What it left is what the paths name. `omittedHints` is `backend-ui` and
  `javascript-unit-tests`, both `scope: core`: six hints matched, and the slice
  of four kept two that bind nobody in the call.
- The order is the tier `D-ANS-060` installed. `backend-typescript` matches 63
  characters of curated vocabulary and scores 0 on its own words. So it sorts
  below `extension-asset-build` at 31 and 23 and `project-build-and-scripts` at
  6 and 10. A hint about somebody else's repository answers the query's words
  because a build has the same words wherever it runs.
- The mirror direction is worse, measured the same day. Two paths under
  `packages/<key>/` and a functional-test task rank `core-tests` (`scope: core`)
  **first**, at 33 and 168. That is above `project-extension-tests`
  (`scope: extension`) at 15 and 113, which is the hint that binds there.
- `feedback/2026-08-24-140340` is that call as the session that made it reports
  it, from another checkout. `typo3_task_guide` in a distributed extension
  returned `project-extension-tests`, `core-tests`, `site-sets`,
  `environment-placeholders` and `extension-manifest`, and the report names two
  of the five as what it used.

## Decided

- The order changes and nothing drops out. `D-KNW-007` stands as it is. An
  off-scope hint is somebody else's convention rather than inverted advice, and
  a project layout in a core answer is still worth a read. What it did not weigh
  is a payload with a ceiling, where one kept is not free.
- A hint the order moves down stays reachable by name.
  `feedback/2026-08-24-140340` reports `omittedHints` and `availableHints` as
  what made a hint it could not have guessed from the title arrive at all. So a
  name there and one call away by id is the whole of what the demotion may cost.
- Where the tier sits needs a measurement rather than a settlement here.
  `D-ANS-060` is what happens otherwise. A sweep disproved the corpus prune it
  proposed, and the rank change that did land left `bin/cli hints:coverage`
  byte-identical.
- The repair touches `src/`, which is the line
  [judging.rst](../../documentation/records/judging.rst) draws around what a
  judging run may improvise. So it goes to the queue rather than into this
  entry.
- The feedback's second half has its answer and comes off rather than joins the
  queue with it. `site-setting` matched weakly on the bare `setting` in its
  `matchWeak`. That is what that field is for: a word that names a subject and
  not the work. Each of its four checklist items arrived with the condition it
  holds under in front. A gate on a weak intent by the paths is what the report
  asks for. It would suppress a correct conditional item whenever a caller named
  a subset of the files the task touches.

## Assumed

- The group's scope is the comparison rather than the call's. `Scope::groups()`
  already splits a call's paths by scope and the brief matches hints per group,
  so each block has one scope to measure against. `D-SCO-009` is why a call that
  names a core path and an extension path is two questions.
- The ceiling is what turns an order into a loss. `typo3_hint_lookup` returns
  the same set at its own limit in either order. So where the tier goes decides
  whether that lookup's order moves and not whether its answer does.

## Wrong if

- The reordered brief loses a hint a caller used. Two calls are the whole of the
  evidence above, and `bin/cli hints:coverage` byte-identical before and after
  is what would say no hint became unreachable.
- The scope-bearing hint turns out to be the one the caller wanted, which is the
  case `MatchedHints::scopeNotice()` exists for. A project that builds a backend
  module wants the backend's design system. Then the label was the whole answer
  and the order is wrong.
- The order changes and the payload does not — the same four ids in the same
  four slots. That would say the ceiling rather than the rank is what has to
  move.

## Since then

**Built on 2026-08-24, and the payload moved.** The core call carries four hints
and names two in the omitted list. The extension call carries the harness hint
and leaves the core one there. So the third **Wrong if** stands settled and the
two above it do not, because both turn on a later caller's report.

What the tier demotes is what the notice above a block has something to say
about, so the order and the notice answer one question. The rule that nothing
tells the two outside-core scopes apart holds for both.
