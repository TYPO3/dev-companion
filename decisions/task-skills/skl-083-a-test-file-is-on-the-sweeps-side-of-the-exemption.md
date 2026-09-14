---
id: D-SKL-083
title: A test file is on the sweep's side of the exemption
date: 2026-08-28
status: open
coveredBy:
  - SkillTest::theBaseFixesTheOrderEveryTaskStartsIn
---

# D-SKL-083 — A test file is on the sweep's side of the exemption

**A session skips the deprecation sweep only where the change calls no TYPO3
API, and a test file calls the API it exercises.**

The rule in `skills/base.md` already decided it and no illustration said it, so
a session drew the line itself and drew it the other way.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001428`](../../feedback/archive/2026-08-28-001428-two-routes-this-server-offered-and-the-session.md).
  It wrote `Tests/Functional/Parser/CacheWorkingDirectoryTest.php` and never
  called `typo3_changelog_lookup` with `type="deprecation"`.
- **Its own account of why**: "on my own judgement that a functional test
  touching only FunctionalTestCase, GeneralUtility::writeFile and the package's
  own CompileService has no surface a deprecation sweep could land on".
- **`GeneralUtility::writeFile` is TYPO3 API**, so the file is on the sweep's
  side by the rule as it already stood. That is "Skip the sweep only where the
  change touches no TYPO3 API", read in `skills/base.md` on 2026-08-28.
- **The illustrations are all non-code**: a code style fixer, a CI file, an
  `.editorconfig`. What the rule says about the other side is general, "one PHP
  file edited along the way puts it back among the ordinary ones". The session
  did not read its test file as that PHP file.
- **The skip was silent as well**, which the same step asks against: the report
  names every step the order did not reach. That half stands and the session did
  not follow it, so nothing here is the lever for it.
- **The brief said it too.** `typo3_task_guide` with `changeType="test"` carried
  the sweep in its checklist, which the report quotes.

## Decided

- The illustration names the test file, in the sentence that already draws the
  line off the files a change touches. Two sentences, because the report asks
  about fixtures as well and a fixture divides. Data the suite reads is exempt
  and a fixture class is not.
- `SkillTest::theBaseFixesTheOrderEveryTaskStartsIn` holds it, which is where
  each sentence of the base that carries weight has its assertion.
- Against a second lever for the silent skip, and against a restatement of the
  sweep in the testing skill. The installer copies `base.md` into every
  published skill, so one sentence there reaches all of them.
- **Against the report's frame** that the rule makes the sweep pointless for a
  test file. A test that calls `GeneralUtility::writeFile` is exactly what a
  deprecation of that method breaks, so the sweep has a surface to land on.

## Assumed

- That a session that reads an illustration that names its own case follows it,
  where the general sentence above it did not carry.

## Wrong if

- A session reports a sweep of two majors for a test file that turned out to
  call nothing the core owns. The sweep came back empty both times. That is the
  cost of an illustration drawn broadly, and the report predicted it.
- A session reports the sweep skipped on a fixture class because the sentence
  reads as about fixtures rather than about what a fixture calls.
