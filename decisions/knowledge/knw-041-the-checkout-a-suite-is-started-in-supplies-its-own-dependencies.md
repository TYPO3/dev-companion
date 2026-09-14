---
id: D-KNW-041
title: The checkout a suite is started in supplies its own dependencies
date: 2026-08-03
status: open
---

# D-KNW-041 — The checkout a suite is started in supplies its own dependencies

**The test-suite corpus states that `runTests.sh` runs against the `vendor/` of
the checkout it starts from, and names `composerInstall` as what puts one
there.**

A session on a core bug in a fresh git worktree spent four round trips and one
long install before a single suite ran. Nothing in `knowledge/` says that a
worktree starts without dependencies.

## Evidence

- `bin/cli hints:probe` on the feedback's own query reaches four hints:
  `system-extension-boundaries`, `core-tests`, `fluid-viewhelpers` and
  `project-build-and-scripts`. None of them names `vendor/`, a worktree or an
  install.
- `knowledge/test-suite-hints.json` offers 25 suites and `composerInstall` is
  not one of them. Its `invocation.notes` say where the script runs and how
  arguments reach the tool, and say nothing about what the directory has to hold
  first.
- `knowledge/documents/typo3-core-scripts.md` has an *Install Dependencies*
  section. It offers host `composer install` for "after cloning TYPO3 core or
  changing PHP dependencies", which a worktree is neither of. The host form also
  wants the branch's PHP, which is the condition `runTests.sh` exists to remove
  (`D-KNW-036`).
- `.checkouts/main/.gitignore:55` is `/bin/*` and `:56` is `/vendor/*`. The same
  two lines are `:58` and `:59` on 12.4. A worktree therefore starts with
  neither directory, and git will never bring them.
- `composerInstall` is a suite on every covered branch — 12.4, 13.4, 14.3 and
  main. On main it is `Build/Scripts/runTests.sh:1203` and it runs
  `composer install --no-progress --no-interaction` in the PHP container.
- The symlink repair the report tried cannot work, by the mechanism `D-KNW-036`
  already established. `CONTAINER_COMMON_PARAMS` at
  `Build/Scripts/runTests.sh:983` and `:988` is
  `-v ${CORE_ROOT}:${CORE_ROOT} -w ${CORE_ROOT}`, and `CORE_ROOT` at `:808` is
  `${PWD}`. A link whose target sits outside that one mount does not resolve
  inside the container.
- Two sessions of the 2026-08-02 core-checkout batch report it. The second names
  it in one clause while it files its other halves apart, which is the corpus
  signal `feedback:list` exists for.
- Run on 2026-08-03 in a detached worktree of `.checkouts/typo3.git` at main's
  `c71b2bdb2f`, outside `.checkouts/`, with neither `vendor/` nor `bin/` in it.
  `-s functional` on
  `typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php`
  printed
  `/usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found`
  and `Result of functional … FAILURE`. It exited 127, the report's message
  verbatim. `-s composerInstall` in the same directory reported SUCCESS in 11
  seconds. It wrote `vendor/`, `bin/` (with `bin/phpunit` in it) and a 47 MB
  `.cache/composer` the worktree did not have. The same test then reported
  `OK (70 tests, 146 assertions)`. Docker rather than podman, PHP 8.5, sqlite.

## Decided

- Step 1a of the ladder: the knowledge is absent. Not delivery and not wording,
  because there is no sentence anywhere in `knowledge/` to move or to rewrite.
- Queued rather than closed on the spot. The change writes a new statement about
  the core's own build script. `judging.md` puts anything that needs a lookup
  about TYPO3 on the todo side, however small.
- `normal` rather than `low`, and not `high`. Two sessions reported it, and the
  cost is the four round trips `D-FBK-027` measures by. It stays under the false
  green of `D-KNW-036` because this failure is loud: the run stops and the
  message is on screen.
- The research above stands here so that the todo does not pay for it again.
  What the todo still owes is the run rather than the sources.
- It goes into the corpus rather than into `typo3-core-patch-development`. A
  skill is a copy in somebody else's project that no release corrects, and this
  is a fact about a versioned script.

## Assumed

- That `composerInstall` is what a worktree should be told to run, rather than
  host `composer install`. It is the containerised form and needs no PHP on the
  host, which is why `D-KNW-036` rejected the host path for `cglFixMyCommit.sh`.
  The 11 seconds above is one measure on one machine with warm image and package
  mirrors. It says nothing about the host form, which nobody has timed.
- That the symlink stays refuted by a read. The run reproduced the failure and
  the install, and did not repeat the symlink attempt. The mechanism is
  `CONTAINER_COMMON_PARAMS` above, and the report already watched it fail.

## Wrong if

- A fresh worktree turns out to run a suite without an install, which would mean
  the absent `vendor/` was not what stopped that session. Checked on 2026-08-03
  and it does not: the run above is the evidence.
- `composerInstall` fails in a worktree for a reason of its own. Then the
  statement names a command that does not work at the place of need. Checked on
  the same worktree and it does not.
- A session reads the note, runs the install in a normal checkout that already
  had one, and pays the long install for nothing. That would mean the condition
  stood as a step rather than as a precondition. This is the one still open. The
  corpus and the document say "once, in a checkout that has no `vendor/`", and
  nothing checks that a reader hears it that way.

## Since then

The third **Wrong if** has a statement against it rather than a reader's guess,
in the document and in the tool's own note. The install is a precondition and
not a step, due again only after the manifest changed.

What arrived beside it came from a session rather than from this entry. A vendor
directory that exists and predates a manifest change, which surfaces as an
exception that names a class plainly in its file. The document carries that
symptom now and names the cheap command rather than another install. Nothing
reports the failure the bullet describes.
