---
id: D-KNW-159
title: A hint that names a suite carries its path
date: 2026-09-15
status: open
coveredBy: []
---

# D-KNW-159 — A hint that names a suite carries its path

**A hint that sends a reader to a test suite names the file, bound to the
branches that have it.** The worktree precondition says that a copied `vendor/`
can be behind the lock.

A review session got the suite that turned its review out of a hint and found
the file by `ls`. It compared two lock files by hand before its first run. Both
were one sentence short.

## Evidence

- **The report.**
  [`feedback/2026-09-15-073844`](../../feedback/archive/2026-09-15-073844-answers-that-stopped-one-step-short-and-what.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5`. Four answers that stopped
  one step short, and nothing that went wrong.
- **Read in `.checkouts/` on 2026-09-15.**
  `typo3/sysext/fluid/Tests/Functional/ViewHelpers/SvgImageViewHelperTest.php`
  is on `13.4`, `14.3` and `main`, and it arrived with the native SVG crop.
  `12.4` has no such file, and `ImageViewHelperTest.php` beside it holds the
  `EXT:` cases there. So the sentence that named the SVG suite unbound was wrong
  on the oldest branch, and the path goes in bound.
- **The lock is tracked.** `composer.lock` is in the core's tree on `main`, and
  `runTests.sh -s composer` dispatches a composer command into the container on
  every covered branch. So `-- install --dry-run` is a check a reader can run
  without a composer on the host.
- **Two of the four were already answered.** `typo3_project_describe` says for a
  core checkout that the suites are `runTests.sh`'s and that
  `typo3_test_run_guide` names them, in the sentence `suites()` prints. The task
  guide's `nextTools` sends a reader to the same tool, and the session read it
  there. Step 5 with nothing to change, twice.

## Decided

- **Closed on the spot under `D-FBK-052`.** Both changes are `knowledge/`, and
  the run read the branches.
- **The path is a bound statement of its own**, not a word in the unbound
  sentence, because the file exists on three branches of four. The unbound
  sentence says a suite exists and the bound one says which.
- **The precondition names the check and the repair.** `--dry-run` says whether
  the copy agrees with the lock, and `composerInstall` brings them together
  whether it did or not. A reader who skips the check and runs the install loses
  a minute, and one who skips both runs a fixer a release apart.
- **Against a sentence in `typo3_project_describe`.** It already says what the
  report asks it to say.

## Assumed

- That `composer install --dry-run` reports a lock a copied `vendor/` is behind.
  Read off Composer's documentation of the flag, not provoked in a worktree
  here.

## Wrong if

- A session in a provisioned worktree runs the dry run, reads "nothing to
  install", and a suite still reports a tool a release apart. Then the copy's
  `installed.json` agrees with the lock and something else is behind.
- A hint names a suite by name alone again and a session reports the `ls`. Then
  the rule is a habit rather than a check, and `HintsTest` should read every
  suite name a hint carries.
