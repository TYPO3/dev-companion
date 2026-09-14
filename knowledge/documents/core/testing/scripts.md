---
description: >-
  What runTests.sh starts, what an argument after -- reaches, and the options one run takes.
whenToUse: >-
  When you run a suite inside a core checkout. typo3_test_run_guide says which suite a change needs, and it filters them by version.
hints:
  - core-tests
---

# TYPO3 Core Script Help

Commands for the work on a TYPO3 core checkout. All paths are relative to the
checkout root.

## Invoking runTests.sh

`Build/Scripts/runTests.sh` runs every suite inside a container. You start it
from the core checkout root.

- Prefix a scripted or non-interactive run with `CI=true`. It drops the
  interactive container flags, skips the SIGINT trap, and picks the CI phpstan
  configuration. Without a TTY the script removes the interactive flags on its
  own, but `CI=true` is the explicit form.
- The script passes everything after `--` through unchanged. It goes to phpunit
  for the test suites, to npm for `-s npm`, to composer for `-s composer`.
- Run one file or one method while you iterate. A full suite costs minutes per
  round.
- `./Build/Scripts/runTests.sh -h` lists the suites and option values the
  checked-out branch supports.

```bash
# one unit test file
CI=true ./Build/Scripts/runTests.sh -s unit -- typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

# one unit test method
CI=true ./Build/Scripts/runTests.sh -s unit -- --filter fixPermissionsSetsGroup typo3/sysext/core/Tests/Unit/Utility/GeneralUtilityTest.php

# one functional test file, sqlite (default)
CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/impexp/Tests/Functional/Export/ExportTest.php
```

Frequently needed options:

- `-d sqlite|mariadb|mysql|postgres` selects the database for `-s functional`
  and for whichever installer suite the branch carries. sqlite is the default
  and the fastest.
- `-a mysqli|pdo_mysql` selects the driver for mysql and mariadb.
- `-i <version>` pins a database version, for example `-d mariadb -i 11.4`.
- `-p <php minor>` selects the PHP version of the container.
- `-n` turns the `cgl` suites into a dry run that only reports. The same holds
  for any other suite the branch lists under it.
- `-c <chunk>/<total>` splits `-s functional`, and the browser suite where the
  branch has one, into chunks.
- `-x` (with optional `-y <port>`) enables xdebug towards a listening IDE.
- `-b docker|podman` selects the container runtime. podman is the default.

## Common Commands

### Install Dependencies

```bash
CI=true ./Build/Scripts/runTests.sh -s composerInstall
```

A suite runs against the `vendor/` and `bin/` of the directory you start it
from. `runTests.sh` mounts that directory and nothing else. A fresh clone has
neither, and neither does a git worktree of a checkout that has them. Git
ignores `/vendor/*` and `/bin/*`, so it never brings them.

The first suite there stops at `exec: line 9: bin/phpunit: not found`. That
names phpunit rather than the directory, so you cannot read the cause from the
symptom. Run the install once in that directory first.

A symlink of `vendor/` and `bin/` from another checkout does not stand in for
it. The target sits outside the one mount and does not resolve inside the
container. That holds whether the link is absolute or relative.

`composer install` on the host installs the same dependencies, but it wants the
PHP the branch requires. The container form is why `runTests.sh` exists. Either
way this is a precondition and not a step. A checkout that already has `vendor/`
needs it again only after `composer.json` or `composer.lock` changed.

Each of those two changes has a symptom of its own, and neither names the
install. The section *When a Suite Fails for the Install Rather Than the Code*
has them.

### Run PHP Unit Tests

```bash
CI=true ./Build/Scripts/runTests.sh -s unit
```

Runs the TYPO3 core unit test suite. Add a path or `--filter` after `--` when
you work on a narrow area.

### Run Functional Tests

```bash
CI=true ./Build/Scripts/runTests.sh -s functional
```

Runs functional tests. Use these for changes that touch TYPO3 services,
persistence, configuration, or integrations. Add `-d mariadb` or `-d postgres`
to reproduce DBMS-specific behaviour.

### Run Coding Standards

```bash
CI=true ./Build/Scripts/runTests.sh -s cgl -n
```

Checks the coding guidelines for all core PHP files and reports without a change
to them. Drop `-n` to have the suite fix them. `-s cglGit` runs
`Build/Scripts/cglFixMyCommit.sh` over the latest commit alone and is quicker,
but only from a normal checkout.

That script asks git for its file list inside the container. A git worktree
keeps its gitdir outside the mounted directory, so git fails and the list comes
back empty. The suite then reports SUCCESS after it read no file. `-s cgl` asks
git nothing and works from either.

The file header is a separate check. `-s cglHeader` runs php-cs-fixer over
`Build/php-cs-fixer/header-comment.php`, and `-s cgl` carries no header rule at
all. `-s cglHeaderGit` is its latest-commit form. It takes its file list from
git the same way `-s cglGit` does. So from a git worktree it also reports
SUCCESS after it read no file.

### Run PHPStan

```bash
CI=true ./Build/Scripts/runTests.sh -s phpstan
```

Useful for type-sensitive PHP changes and API contract changes.

### Check ReST Documentation

```bash
CI=true ./Build/Scripts/runTests.sh -s checkRst
```

Required for every changelog entry below
`typo3/sysext/core/Documentation/Changelog/`.

### Check and Normalize XLIFF

An edit to a language file calls for two runs. One checks that the XLIFF is
valid, and one normalizes its format. Then the diff carries no noise. Which
suites those are is a property of the branch, and some branches have neither.
Ask `typo3_test_run_guide` with the `targetVersion` for the commands that exist
on yours.

### Run TypeScript/Frontend Checks

The frontend build covers backend UI, JavaScript, TypeScript, Sass, contrib and
generated assets. Whether it is one suite or two changed inside the covered
range. So ask `typo3_test_run_guide` with the `targetVersion` rather than copy a
command from here.

### Run SCSS Linting

```bash
CI=true ./Build/Scripts/runTests.sh -s lintScss
```

Runs the TYPO3 Core stylelint setup for Sass sources. Inside, this runs
`grunt stylelint` in the `Build` directory.

### Run CSS Build Only

```bash
CI=true ./Build/Scripts/runTests.sh -s npm -- run build-css
```

Runs the focused CSS build from `Build/package.json`, which maps to `grunt css`.

### Dispatch Composer Commands

```bash
CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload
```

Runs Composer commands inside the TYPO3 core test environment.

### Dispatch NPM Commands

```bash
CI=true ./Build/Scripts/runTests.sh -s npm -- run build
```

Runs npm commands inside the TYPO3 core test environment.

Useful package scripts for frontend and CSS work include `run build`,
`run build-css`, `run lint`, and `run watch:build`.

## When a Suite Fails for the Install Rather Than the Code

A `vendor/` that exists but predates a change to `composer.json` or
`composer.lock` fails in classes the patch never touched. Each of the two has a
symptom that points somewhere else, and they take different fixes.

A changed `composer.json` fails as a Symfony dependency-injection
`InvalidArgumentException` that names a class plainly present in its file. It
reads "Expected to find class … while importing services from resource
../Classes/\*, but it was not found".

The generated `vendor/composer/autoload_psr4.php` has no mapping for it. The
mapping arrived with the fixture and nothing regenerated it. It shows up first
on the `TYPO3Tests\*` fixture extensions, for which `composer.json` declares one
PSR-4 entry each. The message points at the fixture rather than at the
autoloader, and the fix is not a reinstall:

```bash
CI=true ./Build/Scripts/runTests.sh -s composer -- dumpautoload
```

A changed `composer.lock` fails as assertions about behaviour the checkout does
not state, which reads like a broken patch. The installed package is an older
release than the lock names. `typo3_project_describe` says whether that is what
happened. It holds the lock against `vendor/composer/installed.json` and names
the packages the two disagree about. Here the fix is the reinstall:

```bash
CI=true ./Build/Scripts/runTests.sh -s composerInstall
```

## The Pre-Commit Hook

This is the git hooks side. It says what runs on `git commit` before git creates
the commit and what it checks. It names the one error it reports that is not
true. `composer gerrit:setup` installs both hooks, the commit-message one that
adds the Change-Id and this one.

The repository's own pre-commit hook runs the same two scripts. It runs them on
the host rather than in a container. `Build/git-hooks/unix+mac/pre-commit` calls
`cglFixMyCommit.sh` and `cglFixMyCommitFileHeader.sh` directly, and they resolve
`php` off the `PATH`. So on a checkout whose declared PHP is newer than the
host's, the second script dies in `vendor/composer/platform_check.php`. It exits
non-zero. The hook then reports what a non-zero exit means to it:

```text
>> ERROR: There was a missing or wrong php file header in one or more
          of your php files.
   You must fix this and then commit again (git commit --amend)
```

The hook prints that message for every failure of that script, whatever the
cause. So here it is false: the script never got as far as a file.

Git creates the commit anyway, because the hook aborts only when
`TYPO3_GIT_HOOK_ABORT_ON_ERROR` is `yes`. Do not amend on the strength of it.
The suite that runs the same config as the script that failed settles whether
there is a real finding. For this message that is `-s cglHeader` rather than
`-s cglGit`.

`-s cgl` settles the coding-guideline message the hook prints for the other
script. Both suites run in the container on the PHP the branch asks for, and
both ask git nothing. Their quicker latest-commit forms, `-s cglHeaderGit` and
`-s cglGit`, take their file list from git. So from a git worktree they report
SUCCESS after they read nothing. `composer gerrit:setup:preCommitHook:disable`
turns the hook off where the host PHP will not match for a while.

## Script Notes

- Prefer a targeted test while you iterate.
- Run broader checks before you mark a task as ready for review.
- For Sass changes, run `lintScss` for stylelint and `npm -- run build-css` for
  generated CSS.
- Keep command output snippets short in summaries. Include the command and the
  pass/fail result.
