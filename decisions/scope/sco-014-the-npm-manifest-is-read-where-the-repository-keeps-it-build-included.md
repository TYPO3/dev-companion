---
id: D-SCO-014
title: The npm manifest is read where the repository keeps it, `Build/` included
date: 2026-08-19
status: open
coveredBy:
  - ProjectTest::theManifestBelowBuildIsReadWhereTheRepositoryKeepsItThere
  - ProjectTest::twoManifestsDeclaringOneNameAreTwoCommandsThatCanBeToldApart
---

# D-SCO-014 — The npm manifest is read where the repository keeps it, `Build/` included

**`typo3_project_describe` reads `Build/package.json` beside the root one, and
every answer that came out of a manifest names the file it came from.**

The root manifest was the whole npm surface this server read, and the repository
the TYPO3 layout describes has none. The core keeps its `package.json`, its
`.nvmrc` and its Gruntfile one directory down. So the answer was silent about
both halves of what
[`D-SCO-013`](sco-013-a-declared-command-carries-the-interpreter-it-runs-on.md)
had just built. Nothing in it said whether there was no npm surface or none the
server had looked for.

## Evidence

- No core checkout has a root `package.json`. Read on 2026-08-19 in
  `.checkouts/12.4`, `.checkouts/13.4`, `.checkouts/14.3` and `.checkouts/main`:
  each carries `Build/package.json` and `Build/.nvmrc` and nothing at the root.
- Against the 14.3 checkout the answer was `node: null` and no npm command.
  Reading the second manifest, the same checkout answers 24.14 pinned in
  `Build/.nvmrc` against the `>=24.14.0 <25.0.0` its `Build/package.json`
  admits, and the grunt scripts as commands.
- `npm run` reads the manifest of the directory the caller runs it in, so a
  script below the root needs `--prefix`. `npm --prefix Build run <name>` runs
  it with `Build` as the working directory, measured on npm 12.0.1 on
  2026-08-19.
- The core's own `Build/Scripts/runTests.sh` spells it both ways. It runs
  `npm --prefix=${CORE_ROOT}/Build run playwright:run` for the browser suite and
  `cd Build; npm install && npm run build` for the asset build. So the prefix is
  that repository's own way in rather than one this server invented.
- `bootstrap_package`, where `feedback/2026-08-18-113501` came from, is the
  second repository of that shape. The session that lost five sixths of itself
  to its build got neither answer.

## Decided

- Two locations and no search: the root, and `Build/` beside it. A sweep for
  every `package.json` below the root would find `vendor/`, `node_modules/`,
  `.Build/` and every package a monorepo holds. Each of those is a dependency's
  manifest rather than something this repository declares.
- The tool reads both rather than the first that exists. The objection the card
  raised was two manifests that declare one script name, and the prefix answers
  it. `npm run build` and `npm --prefix Build run build` are two commands a
  caller can tell apart, so the list stays unambiguous with nothing dropped.
- The tool reads `engines.node` and the `.nvmrc` from the root first and
  `Build/` after it. The answer carries `enginesIn`, `nvmrcIn` and a
  `declaredBy` that names a path. A caller would read a number stated without
  its file as the root's, in a repository where it is not.
- The tool reads the `.nvmrc` at the root whether or not a manifest sits beside
  it. It is what a version manager on that machine selects either way.

## Assumed

- That a repository which moves its build puts it in `Build/`. That is the
  core's layout on every covered branch and what the sitepackages that copy it
  use; no other directory is on record.
- That where both manifests state an `engines.node`, the root's is the one to
  answer with. Nothing on record declares one in both.

## Wrong if

- A repository keeps its build under another name: `Resources/Private/Build/`,
  `.build/`, a workspace root. Then the answer reports no npm surface where
  there is one, which is the silence this entry removed, one directory over.
- Both manifests declare a Node and they disagree. The answer carries the
  root's. A caller who reads the number without `enginesIn` beside it holds the
  scripts of the other one against the wrong version.
- `npm --prefix` stops the switch of the working directory to the prefix. Then
  every command stated for a second manifest runs in the wrong place.
