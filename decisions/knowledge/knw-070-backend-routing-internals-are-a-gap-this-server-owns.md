---
id: D-KNW-070
title: Backend routing internals are a gap this server owns
date: 2026-08-12
status: open
---

# D-KNW-070 — Backend routing internals are a gap this server owns

**How a backend route carries its module, the shape of a sub-route identifier,
and that a module inherits `navigationComponent` are facts this server owns. The
corpus lacks them.**

The corpus answers how an extension registers a module and how the core handles
an HTTP request, and nothing in between. A session that reviews a change to the
routing itself gets the registration conventions, which do not bear on the diff.

## Evidence

- Re-run on 2026-08-12 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own task, "backend breadcrumb provider route identifier
  for module nodes", matches nothing. It returns all 85 hints as the index. The
  same probe on the words the session needed reaches `extension-boot-files` on
  text alone, which is about `ext_localconf.php`.
- The vocabulary is absent. `navigationComponent`, `sub-route`,
  `ModuleRegistry`, `ExtbaseModule`, `_identifier` and `page-tree-element` occur
  nowhere below `knowledge/` or `skills/`.
- The two neighbour hints are each about something else. `backend-modules`
  answers what a `Configuration/Backend/Modules.php` declares: the keys, the
  `routes` map, `_default`, `moduleData`, the labels domain. Those file names
  match it. `routing-request-handling` matches `/Routing/` and states PSR-7,
  middleware registration and request scope.
- The path-scoped call the session actually made returned
  `system-extension-boundaries`, whose `appliesTo` is `typo3/sysext/`. That is
  what a path under `backend/Classes/Breadcrumb/` reaches today: the hint that
  applies to every core file.
- The session read the four facts out of the checkout instead, and names where:
  `ModuleRegistry.php`, `Module.php`, `ExtbaseModule.php` and
  `BaseModule.php:96-104`. The fourth it found with a probe. Two `Route` classes
  have different constructors, so a route identifier passed where `$defaults`
  goes makes `getOption('_identifier')` return null. Two functional tests had
  never reached the branch their names came from.

## Decided

- Built, as a hint of its own rather than more sentences on `backend-modules`.
  One hint is one question (`D-KNW-030`). That one answers how an extension
  declares a module, this one what the router does with it at runtime. Different
  paths reach them: `Classes/Routing/`, `Classes/Module/` and the callers of
  both, against declarative file names.
- The boundary is what the core's own classes do with a route. That is the
  `module` option a module route carries and a `Routes.php` route does not, and
  the two sub-route identifier shapes. And the inheritance of
  `navigationComponent`, and the two `Route` classes. Registration stays with
  `backend-modules` and the request with `routing-request-handling`.
- The `Route` confusion goes to `core-tests` as well, because a test file is
  where it bit and where the silence is total. The wrong class makes
  `getOption()` return null with no error, and the test passes while it asserts
  nothing.
- What any of it says about TYPO3 waits for the research. This judgement read
  this repository and nothing else. So a session establishes the statements
  against `.checkouts/` on all four covered lines and binds them there. That is
  the todo's first step and not a sentence copied out of the feedback.

## Assumed

- That the four facts hold widely enough to be one hint. The session read them
  on the development line, and a fact that turns out to be one major's is a
  `since` rather than a statement.
- That a review session reaches this by path. The call that missed was
  path-scoped, so the `appliesTo` paths are what decide whether the hint arrives
  at all.

## Wrong if

- A session with the hint installed still goes to the checkout for the same
  facts, which would say the gap was routing rather than knowledge.
- `typo3_backend_module_lookup` turns out to answer the resolved
  `navigationComponent` from the installation, which would make that one an
  answer rather than a statement. `feedback/2026-08-11-055242` is the report
  that asks it, and its own card carries the judgement.
- The research finds each fact bound to a different major. So the hint is a
  table of versions rather than four sentences a reviewer can hold.

## Since then

The second **Wrong if** does not hold. The tool answers six columns that carry
neither the declared nor the resolved component. So the inheritance stays a
statement and the hint stays the answer to it.

The hint left a second identifier shape inside this boundary out. A route has
three spellings and the hint said none of them. That is not the first **Wrong
if**, which describes a session that goes to the checkout for a fact the hint
carries. This one went for a fact it does not. Nor is it routing: the hint ranks
first and arrives with nothing to say. What the enumeration lacked is one
mechanism. The registration key is bare, the builder takes the prefixed name,
and the renderer strips it again. The neighbour hint's pair of the two files was
the general sentence written before the exception existed.
