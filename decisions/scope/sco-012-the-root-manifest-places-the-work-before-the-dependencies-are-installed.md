---
id: D-SCO-012
title: The root manifest places the work before the dependencies are installed
date: 2026-08-18
status: open
coveredBy:
  - InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne
  - ScopeTest::aBriefInAnExtensionRepositoryHandsBackNoCoreSuite
  - ScopeTest::aDotfileKeepsItsDotWhenAPathIsNormalised
  - ScopeTest::anExtensionRepositoryIsPlacedByItsRootManifest
  - ScopeTest::theDeclaredExtensionKeyPlacesAPath
---

# D-SCO-012 — The root manifest places the work before the dependencies are installed

**`Scope` places the repository the session stands in from its root
`composer.json` rather than from its installed packages. So the scope stands
before `composer install` has run.**

`feedback/2026-08-18-070358` reported that `typo3_task_guide` scoped every path
of an extension repository `uncertain`. It handed `runTests.sh` suites back into
a repository that has no `Build/Scripts/`. The scope is the whole of it: with
the scope wrong, the suppression `R-SCO-002` already performs had nothing to
fire on.

## Evidence

- Two fixtures of the same repository: a root `composer.json` of type
  `typo3-cms-extension` that declares `extra.typo3/cms.extension-key: blog`, and
  a `.ddev/config.yaml`. They differ only in whether `vendor/` holds Composer's
  metadata. Fresh, `Scope::of()` answers `uncertain` for `.ddev/config.yaml`,
  `composer.json` and `blog` alike, which is what the feedback recorded.
  Installed, the same three answer `project`, `project` and
  `extension`.
- Three rungs are silent on the fresh clone, and every one of them for the same
  reason. `Instance::isSystemExtension()` reads `packages()`, so an extension
  key passed as a path — the affordance the `paths` parameter documents —
  resolves only once Composer's metadata exists. `Instance::startedIn()` names
  two kinds and both need an installation: `typo3-cms-core` at the root, or a
  vendor directory with packages in it. An extension repository before its
  install is neither.
- The key is on disk and already parsed. `Instance::rootPackage()` reads
  `extra.typo3/cms.extension-key` from the root manifest, and
  `composerPackages()` withholds what it returns while `$packages === []`.
- `.ddev/` is dead as a marker. `Scope::of()` normalises with
  `ltrim($path, './')`, which strips the first dot of a dotfile, so no path can
  match the `.ddev/` entry in `PROJECT_WORK`. It reads `project` in the
  installed fixture from the last rung instead, which is why nobody has noticed.
  `Scope` reads the entry only where no installation places the session, and
  that is the one state that needs it.

## Decided

- Queued rather than closed. The change is in `src/`, and what it moves is the
  order `R-SCO-001` states. So the run that judges the feedback reviews it
  rather than makes it.
- The last rung reads the manifest at the root. That is a decision somebody
  wrote rather than a directory somebody has to fill. A root that declares
  `typo3-cms-extension` is not a core checkout, and that answer needs no path
  argument at all.
- The rung reads the manifest whether or not `vendor/` holds anything, so it
  places the repository the same way in both states. The vendor directory
  decides what the server can read from an installation and says nothing about
  which repository this is. A rung that turns as `composer install` runs is the
  blindness this entry is about rather than a second view of it. What moves with
  it: an installed extension repository is `extension` where it was `project`,
  which is what it was all along.
- Only `typo3-cms-extension` at the root. Every system extension of the core
  declares `typo3-cms-framework`. A rung that read it would place a contributor
  who stands in `typo3/sysext/backend/` outside the core. That is the back half
  of `D-SCO-005`'s first **Wrong if**, reached from the other side.
- A closed failure on `uncertain` is not the lever, and this entry declines the
  feedback's second suggestion. `D-SCO-008` settled that `uncertain` still
  carries the core's own answer, because there is no second body of conventions
  to hand over. Its second **Wrong if** describes this feedback exactly:
  `uncertain` became the common answer, so the last rung is wrong rather than
  the value. Withheld checks would have left the same repository unplaced and
  answered it with less.
- `D-DIS-001` stays as written. Its second **Assumed** — a root package alone is
  not an installation — is about what the server may read from an installation.
  The rung that places the work must not start to report an installation where
  there is a repository.

## Assumed

- The fresh clone is the ordinary state for this question rather than the exotic
  one. A session clones an extension repository before it installs it.
  `feedback/2026-08-18-070333` reports the same blindness from the other side:
  `typo3_project_describe` answered nothing on the clone, from files that were
  all present.

## Wrong if

- The root manifest, read for scope, makes an extension repository report an
  installation. Then the icon, label and package answers start to speak for a
  checkout with no console behind it. That is `D-DIS-001`'s line. The two
  questions have to stay apart the way `D-SCO-005` already keeps
  `TYPO3_DEV_COMPANION_ROOT` out of this one.
- A monorepo whose root declares a TYPO3 package type while the work is in a
  package below it. The root then places every path that carries nothing of its
  own, and `D-DIS-001`'s **Wrong if** already names the shape as unseen here.
- A path in an installed extension repository needs the project's answer rather
  than the extension's. The root now places every path that carries nothing of
  its own, and `.ddev/` is the only project marker such a repository has.
- `uncertain` stays common once the rung works. Then what places the work is not
  in the call at all, and the server has to ask for it at initialize time. That
  is where `D-SCO-008` pointed and this entry does not go.
