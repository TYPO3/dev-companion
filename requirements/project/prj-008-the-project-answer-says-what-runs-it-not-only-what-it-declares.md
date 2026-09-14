---
id: R-PRJ-008
title: 'The project answer says what runs it, not only what it declares'
status: held
restsOn: [D-ANS-013]
heldBy:
  - ProjectTest::aVersionTheEnvironmentDoesNotStateIsNotAVersionItDoesNotHave
  - ProjectTest::anEnvironmentThatIsNotDdevIsSaidToBeUnread
  - ProjectTest::theAnswerSaysWhatRunsTheProject
---

# R-PRJ-008 — The project answer says what runs it, not only what it declares

**Where the repository configures a local environment of its own, the project
answer states the PHP that environment runs. It says that the commands it lists
run inside it.**

The answer reads that from the environment's own files. A DDEV project states
`php_version` in `.ddev/config.yaml`, and in every `.ddev/config.*.yaml` beside
it, and the last of them holds. So `R-PRJ-001` holds: no console, no database,
nothing started to find out, and an answer on a fresh clone.

An environment this server cannot read a version from is still an environment.
Where nothing in the files configures one and `TYPO3_DEV_COMPANION_CONSOLE`
names a command that reaches the installation outside the caller's shell, the
answer says so. It says that no file states the version. Silence there says
"these run where you are", which is the claim the requirement exists against.

Without it the answer offers one number where there are two. A review holds the
declared constraint against the interpreter its own shell has, and in a
containerised project those are two different machines. The command list makes
it worse rather than better. `skills/base.md` sends every task to run the checks
in it, and nothing beside them says the shell is not where they run.

## From

`feedback/2026-07-31-193611` (2026-07-31), a conformance audit in
`/home/benji/projects/site-new` whose first finding was "PHP version mismatch
blocks all tests". That was the host's 8.3.23 against a declared `^8.4`, while
the container the suite runs in has 8.4 and nothing ever blocked the tests.
Re-run on 2026-08-02: `typo3_project_describe` still answers "PHP ^8.4" and
lists `composer test:unit`, and names DDEV nowhere.

## Held by

A session measured what DDEV does with these files rather than recalled it,
against v1.25.1 on 2026-08-02 and its documentation. DDEV reads
`.ddev/config.yaml` first and every `.ddev/config.*.yaml` and
`.ddev/config.*.yml` after it in filename order. So the last statement of
`php_version` is the one the container runs; `config.override.yaml` takes no
special last place. The version is major.minor from the list DDEV ships (5.6
through 8.5); DDEV refuses `8.4.3` and `8` by name. Quotes are optional there,
so an unquoted `8.0` arrives as a YAML float. A cast to a string would answer
`8`.
