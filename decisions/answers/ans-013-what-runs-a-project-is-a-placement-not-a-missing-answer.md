---
id: D-ANS-013
title: What runs a project is a placement, not a missing answer
date: 2026-08-02
status: open
coveredBy:
  - ProjectTest::theAnswerSaysWhatRunsTheProject
---

# D-ANS-013 — What runs a project is a placement, not a missing answer

**This server can read the PHP a DDEV project runs twice over, and neither read
reaches `typo3_project_describe`. So `feedback/2026-07-31-193611` is step 2 of
the ladder, queued as
[`R-PRJ-008`](../../requirements/project/prj-008-the-project-answer-says-what-runs-it-not-only-what-it-declares.md).**

[`D-ANS-011`](ans-011-a-scope-answer-states-what-a-manifest-declares.md) named
this half and left it open: what the container runs is what that feedback asks
for. This is the entry it said the card would produce.

## Evidence

- The feedback re-run on 2026-08-02 through `bin/typo3-dev-companion` from
  `/home/benji/projects/site-new`, the directory it came from.
  `typo3_project_describe` opens with "composer-project, TYPO3 14.3.5, PHP ^8.4"
  and lists "composer test:unit (composer.json) — unknown: phpunit -c
  Build/phpunit/UnitTests.xml". Nothing in the answer names DDEV.
- The three numbers, measured in that project the same day. The host interpreter
  is 8.3.23, `.ddev/config.yaml` states `php_version: "8.4"` on line 4, and
  `composer.json` requires `^8.4`. The container satisfies the constraint, so
  the reported mismatch was the host's and blocked nothing.
- The number is already read here, in a file. `.ddev/config.yaml` states it, and
  a file is what
  [`R-PRJ-001`](../../requirements/project/prj-001-the-project-is-describable-from-its-files-alone.md)
  allows as a source of the project answer.
- And a second time, live. `Typo3Cli::viaDdev()` takes `raw.php_version` out of
  `ddev describe -j`. `typo3_server_scope`, run from the same directory on the
  same day, printed "Its console is reachable via ddev on PHP 8.4". It carried
  `console.php` "8.4" and `console.command` "ddev exec --
  /var/www/html/vendor/bin/typo3".
- Neither reaches the tool that gets the task. The `instructions` returned at
  initialize open with "Start every task with typo3_project_describe", and the
  routing line for a review names it first as well. There `typo3_server_scope`
  is what says which installation the server reads, not what says which PHP runs
  it.
- This server asked for the run that went wrong. `skills/base.md` says "Where
  one of the project's own commands would settle it, run it", pointed at the
  list `typo3_project_describe` returns. That list says of each command what it
  does to the sources, never where it runs. `ddev composer` runs one in the web
  container, on DDEV v1.25.1 here; the shell the agent has runs it on 8.3.23.

## Decided

- Step 2, delivery. The answer is here and is reachable only through a tool the
  task is not sent to. Not 1a, because the number is in a file this server
  already opens. Not 1b, because no verb is absent: `describe` is the verb and
  it is the one the caller calls.
- Queued rather than closed on the spot. It changes `Project::describe()` and
  the declared output schema of `typo3_project_describe`, and
  [judging.md](../../documentation/records/judging.rst) puts a schema beyond a
  run that has read only this repository.
- The answer reads the runtime from `.ddev/config.yaml` rather than from
  `ddev describe`, so `R-PRJ-001` stands. The project answers from its files, on
  a fresh clone, and the server starts nothing to find out —
  [`R-DIS-006`](../../requirements/discovery/dis-006-nothing-is-started-as-a-side-effect-of-a-lookup.md).
- The feedback's own suggestion is not adopted as written. It asks for the
  version inside the container that runs. A stopped project cannot answer that
  and a fresh clone cannot either, and both are states an audit runs in.
- What the answer says beyond that is not settled here. A judgement ends at the
  diagnosis, and which environments past DDEV the answer covers is research the
  todo owns.

## Assumed

- That `php_version` in `.ddev/config.yaml` is what the web container runs. It
  matched `ddev describe` in this project on 2026-08-02, and the session read no
  project where the two disagree. An image override could produce one, since a
  `webimage` of somebody's own carries whatever PHP its build put in, and no
  file here says which.

  The second `.ddev/config.*.yaml` this named beside it is no longer assumed.
  The work on `R-PRJ-008` measured it against DDEV v1.25.1. Those files do
  override `php_version`: DDEV merges `.ddev/config.yaml` first and the rest
  after it in filename order, and the last statement holds. So the answer takes
  all of them rather than the base file alone. A project that keeps its version
  in the `config.local.yaml` DDEV gitignores gets the version it runs.
- That the session ran the shell's interpreter rather than the container's. The
  feedback says so itself, and nothing else records that run.

## Wrong if

- The same finding follows a project answer that names DDEV and its PHP. The
  number would then arrive and the session would not take it, which is step 4
  and a rewrite rather than a placement.
- A recorded review run in a DDEV project puts a declared command through the
  host shell after the answer has said where it runs.
- The two sources disagree in a project somebody checks: `.ddev/config.yaml`
  says one version and `ddev describe` another. `R-PRJ-008` would then demand
  the number from the wrong file.

## Since then

A session read the field this produced in practice on 2026-08-03. It booted a
Composer project from a fresh clone in
`/home/benji/projects/site-demo-typo3-org` (`feedback/2026-08-03-154501`). None
of the three **Wrong if** fired: the interpreter arrived, it was right, and the
report credits it. That session went to `.ddev/config.yaml` by hand for the
other thing the file states. Those are the hooks that install dependencies on
start, update the schema on import and create the backend user. And the pull
provider that makes the data import reproducible. So the placement holds and the
field is short rather than wrong, which is
[`D-ANS-044`](ans-044-the-environment-answer-carries-the-lifecycle-it-declares.md)
and `R-PRJ-009` after it.
