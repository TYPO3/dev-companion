---
id: D-DIS-019
title: A project root is found from what its manifest declares
date: 2026-08-18
status: open
coveredBy:
  - InstanceTest::aNamedInstallationThatIsNotThereIsNotWalkedPast
  - InstanceTest::aPackageInsideAnInstalledProjectIsNotTheProjectRoot
  - InstanceTest::aProjectRootIsRecognisedByWhatItsOwnManifestDeclares
  - InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne
  - ProjectTest::aRepositoryThatSaysNothingAboutTypo3IsNotDescribedAsOne
  - ProjectTest::theRepositoryIsDescribedBeforeAnythingIsInstalledInIt
---

# D-DIS-019 — A project root is found from what its manifest declares

**The project answer finds its root by what the repository's own `composer.json`
declares about TYPO3, and a root it finds is not an installation.**

`D-ANS-085` decided that the caller gets the file-only half of
`typo3_project_describe` wherever the search finds a project root. It named the
root rule as where that goes wrong. The search walks up twelve directories, so a
rule that admits any manifest reports a TYPO3 project for every PHP repository
above the caller.

## Evidence

- What a clone declares, from the repository `feedback/2026-08-18-070333` came
  from: `"type": "typo3-cms-extension"`, a required
  `typo3/cms-core: "^13.4.15 || ^14.3"`, and
  `extra.typo3/cms.extension-key: blog`. Three declarations for one repository,
  and `D-SCO-012` reproduced the same shape as a fixture.
- What a site declares, read in `.environments/e-site-13.4/composer.json` on
  2026-08-18, the `typo3/cms-base-distribution` every environment here starts
  from. `"type": "project"`, twenty-six required `typo3/cms-*` packages, and no
  `extra` block at all. The package types alone would walk past a site
  installation.
- What the rule has to refuse, in this repository's own manifest:
  `"type": "library"`, `mcp/sdk` and two Symfony components, and nothing of
  TYPO3's. A session at work here reaches the same discovery.
- `typo3/coding-standards` and `typo3/tailor` are the shape a rule on the vendor
  name would claim. Both are tools a repository uses on itself, and neither
  installs TYPO3.
- What `Instance::locate()` walked past: `composerPackages()` recognised a
  Composer project by entries in `vendor/composer/installed.json`, the one file
  a clone does not have. The walk recognised a core checkout by its own `type`
  and so answered on a clone all along.

## Decided

- **Three declarations, all in the root's own manifest**. It is a TYPO3 package
  itself, it requires a `typo3/cms-*` package, or it carries the
  `extra.typo3/cms` block TYPO3's Composer installer reads. Each is something
  somebody wrote down, which is what a directory with a `composer.json` in it is
  not.
- `require-dev` counts as much as `require`. A package that installs the core
  for its test setup alone is a TYPO3 repository. It is the shape an extension
  that supports two majors takes.
- The search looks for the installation first and over the whole walk. So a
  package inside an installed project never displaces the project it sits in.
  Where no installation exists anywhere up the walk, the nearest root that
  declares answers, the same rule the walk already followed.
- A root the walk recognises is not an installation. `Instance::describe()`
  keeps its meaning, so the icon, label and schema answers still say
  `cause: no-installation`. They do not speak for a checkout with no packages
  and no console, which is `D-DIS-001`'s line.
- A root named by `TYPO3_DEV_COMPANION_ROOT` and unusable is not walked past
  here either. The caller said which repository it means, and an answer about
  another one is the failure the variable reports.
- Nothing is remembered. The walk is one manifest read per directory. The answer
  has to change the moment the install this state exists to prompt has run,
  which is `R-DIS-009` for the same reason.
- The state stands in `installed` rather than in `kind`. `kind` stays what the
  root declares itself to be. A third value there would have carried the state
  at the price of the layout. A caller could no longer tell a site project from
  an extension repository, which is what the boot workflow decides on.

## Assumed

- That a session stands in the repository it means, or below it. The walk
  prefers the nearest root that declares, so a clone below another project's
  tree answers for itself.
- That every repository which requires a `typo3/cms-*` package is a TYPO3
  repository. The prefix is TYPO3's own namespace for the CMS, and everything
  else it publishes has a name outside it.
- That `extra.typo3/cms` stands only where somebody installs or ships TYPO3.
  `typo3/cms-composer-installers` reads it and nothing else this repository
  knows of does.

## Wrong if

- A session reports an answer that describes a repository with no relation to
  TYPO3. Then one of the three declarations admits too much. The
  installed-metadata gate did that work as well as the one this entry blamed it
  for.
- A session in `packages/<key>` of a clone gets an answer for that package. The
  environment, the document root and the sites it needs are one directory up.
  Then nearest-wins is wrong for an uninstalled tree and the outermost root that
  declares is the answer.
- A repository that declares TYPO3 only in `require-dev` gets a description as a
  project and a reader takes the answer as one about a site. Then the two kinds
  of declaration are not one question.
- A tool other than `typo3_project_describe` starts to answer from a root with
  nothing installed below it. Then the two questions have run together and
  `D-DIS-001`'s second **Assumed** no longer holds.
