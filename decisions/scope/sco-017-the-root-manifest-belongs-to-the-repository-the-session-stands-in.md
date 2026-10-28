---
id: D-SCO-017
title: The root manifest belongs to the repository the session stands in
date: 2026-10-28
status: open
coveredBy:
  - ScopeTest::theRootOfAComposerProjectIsPlacedAsTheProject
---

# D-SCO-017 — The root manifest belongs to the repository the session stands in

**`Scope` places `composer.json` and `composer.lock` by the repository the
session stands in, before any word in the task. A project directory without its
trailing slash reads as the directory.**

## Evidence

- **Call 9 of the REVIEW-01 run of 2026-10-28** in
  `scenarios/runs/REVIEW-01.json` gave `typo3_task_guide` the task "Audit the
  project and its sitepackage …" with `composer.json` and `config/sites` among
  its paths. Both came back `extension` in a `composer-project`.
- **The cause for `composer.json` is the prose rung.** The path carries no
  marker, so `Scope::of()` reads the task text next. The phrase "sitepackage" in
  `EXTENSION_WORK` matches there and returns `extension`. The same path with no
  task text answers `project` from the last rung.
- **The cause for `config/sites` is the slash.** `PROJECT_WORK` holds
  `config/sites/`, and the caller wrote the directory without it. So the path
  reached the prose rung the same way.
- `ScopeTest::theRootOfAComposerProjectIsPlacedAsTheProject` failed with
  `extension` for `composer.json` before the change.

## Decided

- The root manifest is a path shape, which `R-SCO-001` ranks above prose. A file
  at the root of a repository belongs to that repository. So the last rung
  answers for it early, and only where that rung places anything. Where nothing
  places the session, the manifest has no shape of its own and the prose still
  decides.
- The rung reads the repository the session stands in, not the project alone. In
  an extension repository `composer.json` stays `extension`, which
  `ScopeTest::anExtensionRepositoryIsPlacedByItsRootManifest` already holds.
- A manifest below `packages/` or `extensions/` keeps the extension marker,
  because those markers come first.
- `PROJECT_WORK` matches a path with a slash put back. A caller names a
  directory both ways, and the marker means the directory.

## Assumed

- A caller who passes a bare `composer.json` means the one at the root of the
  repository the session is in, rather than one inside a package.

## Wrong if

- A session in a project passes the bare `composer.json` of one of its packages
  and needs the extension's answer. Then the bare name is ambiguous, and the
  answer has to ask rather than place it.
