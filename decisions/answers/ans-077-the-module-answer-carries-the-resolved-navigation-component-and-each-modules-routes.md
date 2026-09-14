---
id: D-ANS-077
title: The module answer carries the resolved navigation component and each module's routes
date: 2026-08-12
status: open
---

# D-ANS-077 — The module answer carries the resolved navigation component and each module's routes

**`typo3_backend_module_lookup` answers the navigation component each module
resolves to and the routes it registers, from a source that answers on every
covered line.**

The tool hands back what one console command exports. A session that needed
which modules use page-tree navigation and which have routes beyond `_default`
got neither. It read both out of the checkout instead, and its first read was
wrong.

## Evidence

- `feedback/2026-08-11-055242`, re-read against the server as it is now. Its
  first half asks for a description that opens with what the tool enumerates.
  That description has opened with "List the backend modules registered in the
  TYPO3 installation you are working in" since 2026-08-01, ten days before the
  report. The session judged from the name alone, because its client left the
  schema deferred for the whole session.
- The answer's six fields are the seven columns `debug:backend:modules` exports
  and nothing else: `Pkg`, three level columns, `Position`, `Labels` and `Path`
  (`DebugBackendModulesCommand.php:115-123`). Neither the navigation component
  nor a route beyond the module's own path is among them, and neither option the
  command takes adds one.
- `getNavigationComponent()` returns the parent module's value where inheritance
  is on (`BaseModule.php:99-103`). The declared value is not in the answer
  either, so no caller can compute the resolved one from what it gets.
- The command exists on 14.3 and main and on neither 12.4 nor 13.4. It arrived
  with `d9b6be7510` in v14.0. So on half the covered lines the tool answers
  `unsupported: installation-not-answering` while its description promises with
  no condition.
- `debug:backend:routes --json` came from that same commit. It lists every route
  with its options and whether it is a module, an ajax or a plain route. That is
  the routes half, on v14 alone.
- The container is reachable already. `src/Installation/probe.php` boots the
  installation and asks it. `ModuleInterface` exposes
  `getNavigationComponent()`, `getDefaultRouteOptions()`, `getAccess()` and
  `getAliases()`, and `D-DIS-005` is the same case decided for a registry with
  no command.
- `backend-routing-internals` states the inheritance as a rule since
  `D-KNW-070`. What no hint can carry is which modules in the installation in
  front of the caller resolve to which component.

## Decided

- Built as fields on this tool rather than as a second one. It stays the
  enumeration of what the installation registers; what a request resolves to at
  runtime stays the hint's.
- The description half has its answer and closes with this entry. The name keeps
  `lookup`, which is the verb `AGENTS.md` gives an enumeration that answers an
  optional query. It is the segment a client that defers schemas shows.
- Which source carries which field is the read the card owes. The console
  answers `Pkg` and the translated labels the module registry cannot; the core
  says so at `DebugBackendModulesCommand.php:100`. The container answers the
  rest, and answers on 12.4 and 13.4 as well.
- Whatever the source, the answer says which lines it holds on. A tool that
  cannot answer on two covered majors names them, rather than hands on the
  console's "command is not defined".

## Assumed

- That the container answers a question about modules at all. The command builds
  its own `ModuleRegistry` from `backend.modules` and the `ModuleFactory` rather
  than autowires one, and the probe would have to do the same.
- That a caller who asks about modules wants the routes beside them. They are
  two enumerations upstream, and one read is what this feedback needed.

## Wrong if

- The two fields turn out to need a request or a backend user. So they are
  available on v14 through the console alone, and the answer stays bound to a
  version rather than complete.
- A session with the fields still goes to the checkout for the same two facts.
  That would say the answer was the wrong half of the question.
- `debug:backend:modules` gains the columns upstream, which would make the
  console the whole source and the probe unnecessary.

## Since then

The read happened on 2026-08-12, and the container needs no console beside it
after all. `ModuleRegistry`, `backend.modules` and `LanguageServiceFactory` all
answer a booted CLI container on 12.4 and on main. That is a trial in the two
E-SITE installations rather than an inference from the service definitions.
`ModuleProvider` is the one that does not, private since 14.3. So the package
and the translated labels come from the same read as the rest, and the tool has
one source instead of two.

The navigation component turned out to be a value rather than a constant:
`@typo3/backend/page-tree/page-tree-element` on 12.4 and
`@typo3/backend/tree/page-tree-element` on main. A hint could not have carried
it, which is the sharpest form of the boundary this entry draws.
