---
id: D-SKL-067
title: "Maintaining a package's asset build earns a task skill"
date: 2026-08-21
status: open
---

# D-SKL-067 — Maintaining a package's asset build earns a task skill

**An extension's asset build is a task this server orders, and the TYPO3 half of
it is the domain. That is the committed output, the import map, the borrowed
backend class.**

Two sessions in two repositories spent most of themselves on a TYPO3 package's
frontend build with nothing from this server. One of them shipped two unverified
assumptions to `master`. The knowledge that would have bounded both is here;
nothing carries a word either session typed, so nothing fired.

## Evidence

- Two sessions, two repositories, both unassisted. `feedback/2026-08-19-090200`
  reports zero tool calls through the work portion of a Dependabot and
  dependency-update task in `EXT:blog`. `feedback/archive/2026-08-18-113501`
  reports five sixths of a `bootstrap_package` session on Grunt, fantasticon and
  a Node defect. It asked outright whether the boundary includes a package's
  build surface.
- The cost is not only unassisted work. The same `EXT:blog` session re-attached
  the core backend class `table-fit` and dropped `import $ from 'jquery'`. It
  verified neither against the majors the extension declares, per its own second
  report, `feedback/2026-08-19-090231`.
- `bin/cli hints:probe "update the npm dependencies and close the dependabot pull requests"`
  reaches nothing at all, and returns 100 hints as the index. Read on
  2026-08-21.
- `bin/cli hints:probe "package.json webpack build"` reaches
  `project-build-and-scripts` alone. `extension-asset-build` exists, its title
  is "Building Assets in a Project Extension", and its `appliesTo` carried none
  of `npm`, `package.json`, `webpack` or `vite`. Only the text score reached it,
  on a query that named all four.
- No entry in `knowledge/task-intents.json` matches any of those words. The
  nearest, `installation-upgrade`, names no skill.
- No skill description names the build. The only mention anywhere below
  `skills/` is three lines about lint scripts in
  `typo3-extension-testing/references/static-quality.md`.
- The `EXT:blog` session read the skill listing and called nothing. So the
  description in that listing is the only lever that reaches a session which
  never asks. Activation is the client's, and
  [`D-SKL-033`](skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md)
  is where that boundary stands.

## Decided

- This entry takes the domain on, as a workflow of its own, named in the words
  the task arrives with. Those are `npm`, `package.json`, a dependency update, a
  bundler, the built assets under `Resources/Public/`.
- It owns the TYPO3 half of that task. The commit that changes the source
  rebuilds the output committed below `Resources/Public/`.
  `Configuration/JavaScriptModules.php` and the import map are how built backend
  JavaScript reaches the backend. A session verifies a backend CSS class or icon
  a built asset borrows from the core against the majors the package declares.
  It does not assume it from its name. The build commands are the ones
  `typo3_project_describe` reports from the manifest where the repository keeps
  it,
  [`D-SCO-014`](../scope/sco-014-the-npm-manifest-is-read-where-the-repository-keeps-it-build-included.md).
- It stops at the third-party library's own migration: a bundler's configuration
  format, a JavaScript library's API change, a Node defect. Both sessions drew
  that line themselves, and behind it is another project's manual.
- Rejected: a fold into `typo3-extension-upgrade` or `typo3-extension-health`.
  The first opens on the TYPO3 and PHP versions a package declares, and an
  opening clause is what narrows hardest —
  [`D-SKL-061`](skl-061-the-upgrade-description-is-reachable-from-a-defect.md).
  The second gates on an audit with an agreed list first. That is what the
  `EXT:blog` session named as the reason it did not fit a change already asked
  for.
- Publication asks the listing budget and not this entry, which is where
  [`D-SKL-054`](skl-054-the-listing-budget-is-what-a-client-reads.md)
  already puts it.
- Repaired in the same commit: `extension-asset-build` gains the words that name
  what it is about. Those are `npm`, `package.json`, `webpack`, `vite`, the
  build artefacts and their rebuild. Not `Dependabot` and not
  `dependency update`. The hint says nothing about dependency maintenance, and a
  hit that answers a neighbouring question costs more than a miss.

## Assumed

- That the two sessions are the domain rather than one person's repositories.
  Both are Composer projects that maintain a sitepackage or extension, and no
  third has given a debrief on it.
- That the TYPO3 half is separable from the library half in practice. Both
  sessions separated it unprompted, which is the only evidence for it.

## Wrong if

- A baseline run of either recorded task with the workflow installed goes the
  same way, `node_modules` read by hand, the borrowed class shipped unverified.
  That would say the order was never the gap.
- The published description does not fire on "update the npm dependencies",
  which is the sentence both sessions arrived with.
- A third session reports the build and its whole cost is the library rather
  than the TYPO3 half. That puts the domain outside this server after all. That
  is the question `feedback/archive/2026-08-18-113501` asked and this answers.
- The thirteenth description costs the twelve more than it buys, measured at
  publication against the arithmetic in
  [`D-SKL-026`](skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md).

## Since then

A read of the borrowed class on 2026-08-21 showed the session's unverified
assumption was right. What **Decided** gives the workflow includes a
verification of such a class against the declared majors, and on this example
that verification does not answer. The catalog withholds the entry at the older
major. One custom property in it arrived later and an entry has one bound as a
whole. So the instruction has a miss behind it on the case that produced it, and
what a withheld entry means is part of the work.

Read against the session call by call, the order reaches two of the three
decisions it shipped unverified. The class bullet predates `D-CAT-006` and this
read corrects it. A class the query names outright gets an answer on a target
the catalog withholds its own entry for.
