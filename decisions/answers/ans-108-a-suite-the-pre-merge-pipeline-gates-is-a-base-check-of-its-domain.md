---
id: D-ANS-108
title: A suite the pre-merge pipeline gates is a base check of its domain
date: 2026-08-25
status: open
readings:
  - 2026-09-01
---

# D-ANS-108 — A suite the pre-merge pipeline gates is a base check of its domain

**`checkIntegrityPhp` and `checkExceptionCodes` are base suites of the `php`
domain, because the core's own pre-merge pipeline runs them over every patch
that writes PHP.**

A session reviewing a Gerrit change wrote a functional test fixture that threw
without an exception code. The `checks` array it was handed named `unit`,
`functional` and `cgl -n`; it ran `checkIntegrityPhp` only because the core's
own `AGENTS.md` lists it, and that is the run that failed.

## Evidence

- `feedback/2026-08-24-183319` reports it, and counts what it cost: the rule was
  established by reading two call sites in `Classes/` out of the checkout,
  because nothing here states it.
- Re-run on 2026-08-25 with the feedback's own arguments. `typo3_task_guide`
  returned `checks` of `unit`, `functional` and `cgl -n`, and a `testSuites` of
  `cglGit`, `checkIntegrityPhp`, `composerInstall` and `e2e` beside it.
  `typo3_test_run_guide` with `query="functional"` returned the functional suite
  and nothing else. So the report holds as filed.
- The rule is in no file here.
  `bin/cli hints:probe "exception code on every throw"` reached nothing on
  2026-08-25, and the only sentence in `knowledge/` carrying the words is about
  what symfony/console does to an exit status.
- Read in `.checkouts/` on 2026-08-25. `-s checkIntegrityPhp` runs
  `Build/Scripts/phpIntegrityChecker.php`, whose registered visitors are the
  annotation, namespace, test-method-prefix, final-test-class and exception-code
  checkers, over `typo3/sysext/*/Classes`, `typo3/sysext/*/Tests/Unit` and
  `typo3/sysext/*/Tests/Functional`. A fixture is read exactly as production
  code is, which is what the reporting session hit.
- `ExceptionCodeChecker` reports three kinds — undefined, duplicated and
  malformed, the last being a code that is not an integer of ten digits — and
  leaves a code taken from `$e->getCode()` alone.
- The oldest covered branch has no integrity checker and carries
  `-s checkExceptionCodes` instead, which runs
  `Build/Scripts/duplicateExceptionCodeCheck.sh` over every `*.php` below
  `typo3/`. Same rule, one script older.
- Both are in the core's own `Build/gitlab-ci/pre-merge/integrity.yml`, so a
  throw written without a code fails review before a person reads the patch.
- The feedback's own suggestion is wrong about the second suite.
  `-s listExceptionCodes` runs the same script with `-p`, and both of that
  script's failing branches are guarded by `if [ "$print" -ne "1" ]` — so it
  prints JSON and exits successfully whether a code is missing or used twice.
  The session read its green run as confirmation and it confirms nothing.
- The version-split pair already has a precedent in
  `knowledge/test-suite-hints.json`: `build` is `base` from one major and
  `buildCss` and `buildJavascript` are `base` until the one before it.
- `bin/cli versions:check` reads the two new ranges against all four checkouts
  and passes.

## Decided

- Step 1a for the rule and step 2 for the suite. The rule is in no file here;
  the suite that catches it has been in the corpus all along and reaches no
  caller who did not ask for it by name.
- Made in this run rather than queued — `D-FBK-052`. The reading is the four
  checkouts above and it is done; nothing here touches `src/`, a declared schema
  or a skill.
- `base: true` on both, rather than a sentence somewhere. `baseFor()` is what
  `checks` is built from, and `checks` is the list a caller runs; a suite named
  only in `testSuites` is a suite the caller has to decide about.
- `listExceptionCodes` is carried, marked as checking nothing. It is what
  `runTests.sh -h` offers a caller looking for the exception-code check, so
  withholding it leaves the misreading the feedback made available and unwarned.
- The rule itself is `core-exception-codes` in `knowledge/hints/php.json`, scope
  `core`. It states the exception code and the three suites; the checker's other
  four rules are a reading nobody has done and are not claimed here.
- The hint says a fixture is read as production code is, and leaves the
  directories to the suite entry. Carrying the word `test` at all, it tips the
  first query of `R-KNW-053` from its answer to none: that query holds two terms
  nothing in the corpus has, so its coverage sits just over `MIN_COVERAGE`, and
  one more `php` hint carrying `test` moves the weights under it. What the
  wording costs in precision is in `knowledge/test-suite-hints.json` instead,
  beside the command, which is where the same caller reads it.
- What is left open is the one the feedback puts second: `checks`, `testSuites`
  and `suites` are three different narrowings of one corpus and no schema says
  so. That moves a declared `outputSchema`, so it is queued.
- The withdrawn half is not judged here. `feedback/2026-08-24-183711` withdraws
  the working-tree paragraph down to one unattributed loss and carries its own
  card.

## Assumed

- That a caller runs what `checks` names and decides about what `testSuites`
  names. It is what the reporting session did, and it is one session.
- That the integrity checker's other four rules are worth their own reading. Two
  of them — the final test class and the test-method prefix — are already stated
  in `core-tests` without the check that enforces them being named.

## Wrong if

- `checkIntegrityPhp` comes back on changes it cannot answer for, so that a
  caller learns to skip the list — the third **Wrong if** of `D-ANS-099`, now
  with one more suite in it.
- A call this server cannot place on a branch hands over both suites and the
  caller runs the one their checkout does not have.
- A session reads the `listExceptionCodes` entry and still takes a green run as
  a code confirmed.
- The failures sessions actually hit are the checker's other four rules, which
  would say the hint states the cheapest fifth of what the suite reports.
- The next `php` hint tips that `R-KNW-053` query anyway. Then the floor is what
  needs looking at, rather than one hint's wording.
