---
id: D-ANS-102
title: The project answer says whether the installed tree matches the lock
date: 2026-08-24
status: open
coveredBy:
  - ProjectTest::aCheckoutWhoseLockedPackagesAreNotOnDiskIsToldWhatInstallsThem
  - ProjectTest::theAnswerNamesThePackagesTheInstallAndTheLockDisagreeOn
  - ProjectTest::theInstallIsHeldAgainstTheLockPackageByPackage
---

# D-ANS-102 — The project answer says whether the installed tree matches the lock

**`typo3_project_describe` reports where `composer.lock` and
`vendor/composer/installed.json` name different package versions, beside the
`installed` field that answers `true` either way.**

`installed` is `Instance::packages() !== []`. A vendor directory that exists and
predates the lock satisfies it. The suite run that follows fails in classes the
caller's own change never touched.

## Evidence

- `feedback/2026-08-24-110908` reports it from `/home/benji/projects/typo3-cms`.
  A review of an impexp patch, a `git reset` onto `origin/main` that brought a
  changed `composer.lock`, `vendor/symfony/yaml` a month old. Then three
  failures in `SiteWriterTest` over expanded versus inline YAML sequences. Four
  extra calls and two full suite runs to establish "not my patch, stale vendor".
- `feedback/archive/2026-08-07-130007` is the same drift from another task
  shape. A full functional run of 12477 tests for Forge 109572, two errors that
  name a fixture class plainly present in its file. Then a 45-minute suite run
  plus two targeted runs to reach the autoloader. Its answer was a paragraph in
  `knowledge/documents/core/testing/scripts.md`, for the symptom of a changed
  `composer.json`, which prescribes `dumpautoload`. That is not the command a
  changed lock needs.
- The rule is here twice and neither session could act on it.
  `knowledge/test-suite-hints.json` says a checkout that has `vendor/` "needs it
  again only after composer.json or composer.lock changed", and the scripts
  document repeats it. Both sessions learned that the lock had changed from the
  failures rather than before them.
- Re-run on 2026-08-24 against that same checkout, through
  `bin/typo3-dev-companion` over stdio with `TYPO3_DEV_COMPANION_ROOT` set to
  it. It answers `installed: true`, `typo3Version: 15.0.0-dev`, and neither the
  text nor the data says anything about the lock.
- Both files are already open here. `Instance::locate()` recognises a Composer
  project by `vendor/composer/installed.json`, and nothing below `src/` reads
  `composer.lock` at all.
- The modification times the feedback proposes report a drift that is not there.
  In that checkout on 2026-08-24 `composer.lock` dates from 14:50 and
  `vendor/composer/installed.json` from 12:41. Every one of the 129 packages the
  lock names carries the version `installed.json` records for it.

## Decided

- Step 1b of the ladder, the shape. What both sessions lacked is not the rule
  but the fact that the lock had moved. Two files on disk carry it and no call
  here returns it.
- Queued rather than closed on the spot: it changes `Project::describe()` and
  the tool's declared `outputSchema`.
- The comparison is the package versions the two files name: `packages` and
  `packages-dev` in the lock against `packages` in `installed.json`, by name and
  by version. Not the modification times, which the re-run above shows apart
  while the installed tree matches the lock exactly.
- `installed` stays a boolean and the drift is a field beside it. Clients
  validate against the published `outputSchema`, so a third state inside that
  field is a type change where an added field is not.
- `typo3_project_describe` and no other tool. `typo3_test_run_guide` answers
  from `Source::Knowledge` and reads no checkout, and `skills/base.md` opens
  every task with the describe call.
- Not the autoload drift `feedback/archive/2026-08-07-130007` reported: a PSR-4
  mapping added to `composer.json` leaves the two package lists identical.
  Whether one field carries both is the todo's to settle.
- Nothing holds this yet, so it carries no `coveredBy`. The test arrives with
  the change.

## Assumed

- That every install which changes what sits below `vendor/` rewrites
  `installed.json`. Composer writes that file there and nothing here has driven
  an install to watch it happen.
- That a difference the two files agree about is rare enough to leave
  unreported. A package patched through `extra.patches` carries the version the
  lock names and not the behaviour, and this answer already says that
  separately.
- That the two sessions that reported it counted their own cost correctly. Each
  names the runs it spent, and neither can be re-run from here.

## Wrong if

- A caller is told the install is behind the lock and reinstalls where Composer
  would have done nothing. Then the two files are the wrong pair to compare, and
  what says an install is due is something else.
- The next stale-vendor report is the autoload flavour again. Then the field
  covers half the drift and the scripts document carried the other half alone
  all along.
- A session gets the line and still spends a suite run to attribute the
  failures. Then the fact arrived and was not taken, which is step 4 and a
  rewrite.

## Since then

Built the same day as a field beside the installed flag: a state of four values
and the packages the two files disagree about. A package the lock names and no
install carries is one of those entries with a null. So the absent package and
the wrong version are one field and one sentence. A locked dev package counts
only where the install took them.

The autoload drift stayed out, as the decision had it. The pair of files that
would carry it is the manifest against generated PHP this server would have to
execute to read.
