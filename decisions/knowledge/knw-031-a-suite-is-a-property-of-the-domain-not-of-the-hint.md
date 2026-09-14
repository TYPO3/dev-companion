---
id: D-KNW-031
title: A suite is a property of the domain, not of the hint
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::theBaseSuitesOfADomainAreStatedWhateverTheTaskNames
  - HintsTest::theSuiteListItselfIsFilteredByTheBranchItIsAskedFor
---

# D-KNW-031 — A suite is a property of the domain, not of the hint

**The `checks` field leaves the architecture hints, and the suites any change in
a domain runs stand once on the suite itself.**

Fifty-nine of seventy-six hints carried a `checks` list, and twenty-eight of
them named nothing but `functional`, `phpstan` and `unit` in some combination.
That is not a property of the subject the hint is about but of the domain it
belongs to.

## Evidence

- The split by set: 28 generic, 31 that name a suite the subject actually
  implies. `lintScss` and `buildCss` on the CSS hints, `checkIntegrityXliff` and
  `normalizeXliff` on the labels one, `checkRst` on the changelog one.
  `lintServicesYaml` on dependency injection, `e2e` on browser tests.
- Removing the field alone made a core bugfix brief name no check at all.
  `typo3_task_guide` drew its `checks` from the intents and the hints. For «Fix
  that TSconfig field label overrides are not respected per record type in
  FormEngine select fields» the intents supply the XLIFF checks. The suite
  matcher supplies the label integrity ones. Neither reaches the functional
  suite, because that sentence contains no word about tests.
- So the generic entries were not decoration. They were the only place a brief
  said "run the functional suite", written out twenty-eight times.

## Decided

- `checks` is gone from every hint and from `Schema::hintRecord()`, and
  `withoutChecks()` with it. A hint is a convention; what a caller runs
  afterwards is a different question with its own tool.
- `knowledge/test-suite-hints.json` gains a `base` flag, and `baseFor()` returns
  the base suites of the domains a task is in. `unit` and `functional` for PHP,
  `build`, `buildCss` and `lintScss` for CSS. `build`, `buildJavascript`,
  `lintTypescript` and `unitJavascript` for TypeScript. The suite already
  carried its domains and its version range, so the declaration goes where the
  filter already is and `R-KNW-024` still holds.
- The brief states the base suites first and the intents' checks after them. The
  base ones hold whatever the task turns out to be; an intent's are what its
  words earned.
- Nothing outside the core gets them, as before: the brief drops every check
  where no path is the core's.

## Assumed

- The base list is short enough to stay true. `cgl` and `phpstan` are arguably
  base for PHP as well, and stay out. A brief that names five commands for every
  task reads as a preamble rather than as an instruction.
- A domain is the right granularity. It is what the 28 generic entries agreed
  on, which is the only evidence there is that it is neither too coarse nor too
  fine.

## Wrong if

- A brief names a suite that has nothing to do with the change. That would mean
  the domain is too coarse a key for the base list.
- Somebody puts a suite back on a hint because the brief did not name it. The
  hint is not where the fix goes. Either the suite is base for the domain or the
  task guide's suite matcher missed it.
