---
id: D-KNW-190
title: A setup names no account a caller copies
date: 2026-11-06
status: open
coveredBy:
  - HintsTest::theWorkedExamplesNameNoAccountACallerCopies
---

# D-KNW-190 — A setup names no account a caller copies

**`installation-setup` names no account in its example. Its checklist has the
session choose one and commit `settings.php` without its secrets.**

## Evidence

- `SITE-11` from a pristine directory on 2026-11-06, one run each of
  `claude-opus-5-5` and `claude-fable-5-1` against `39fd0169`, graded blind.
  Both fetched the entry by id and named the account `admin`. Both wrote a
  literal default password into a script the project commits. The same happened
  on 2026-10-29.
- Both read `project-configuration-files` and ignored `settings.php`. Opus asked
  about it afterwards, and Fable called it deliberate.
- `.checkouts/12.4`, `13.4`, `14.3` and `main` carry `--admin` on
  `backend:user:create`, which asks for what no option supplies.
- The generated `.ddev/.gitignore` of DDEV v1.25.5 excludes `config.local.y*ml`
  and leaves `.env.web` out.

## Decided

- **The maintainer chose on 2026-10-30 that this repair waits for that run**,
  and it has run.
- `site-error-handling` gets its route from the `sitepackage` checklist, because
  neither run fetched it.
- `tca-core-palette` stays as it is. Neither run wrote a column that reaches no
  form, so `T-261029-bb2b` drops that step.

## Assumed

- That a checklist item moves what a hint read by id did not move.

## Wrong if

- The next pair still names `admin` or commits a default password. Then a
  statement is not the lever, and the setup needs a command the session runs.
