---
id: D-VER-004
title: A supported range is a property of the package, not of the checkout
date: 2026-07-31
status: confirmed
coveredBy:
  - VersionsTest::aSpellingFromTheWildAnswersForEveryMajorItServes
---

# D-VER-004 — A supported range is a property of the package, not of the checkout

**Where a repository declares `typo3/cms-core` for more than one covered major
and the caller states no version, the hint lookups answer for all of them. They
say so.**

`REVIEW-02` reported three things as accumulated debt that are the cost of one
codebase that serves TYPO3 13.4 and 14.3. Those were a deprecated upgrade-wizard
interface, an `ext_emconf.php`, and 73 suppressed deprecations. A read of the
tool trace showed that nobody told the session. `typo3_hint_lookup` filtered
every statement to the installed major. So the rule bound `until: 13`, the one
that says what that file is for, went out before the answer took shape.

## Evidence

- The transcript's own tool results. With `targetVersion: 14` the
  `extension-files` hint came back with nine statements and neither of the two
  bound to the older majors. The same call for the declared range returns both
  `ext_emconf.php` statements, each with its range beside it. The three findings
  are the three subjects where the two majors disagree.

## Decided

- Where a repository declares `typo3/cms-core` for more than one covered major
  and the caller states no version, the hint lookups answer for all of them.
  They say so, and name the majors and the declaration. A stated version still
  narrows to one, because somebody who says "14" asks about 14. A constraint the
  server cannot read falls back to the installed version rather than to a
  guessed range.
- The catalogs keep their filter by a single version. Their `targetVersion`
  withholds markup rather than qualifies it. A class that does not exist on the
  rendered version fails in a browser whichever other major the package also
  supports.

## Assumed

- To ask a constraint about each covered major is enough to read it. The
  comparators an extension writes, `^`, `~`, `>=`, `<`, an exact version, `*`,
  have coverage. Anything else yields nothing and falls back.

## Wrong if

- A form in the wild answers false for a major it does serve. That shows up as a
  statement absent from an answer rather than as an error. Or a repository
  declares a range far wider than it tests, and the answer then carries
  statements for majors nobody maintains. The second one is worth a watch. The
  declaration is a promise, and this now treats it as one.

## Confirmed on 2026-08-02

The first half of the **Wrong if** did not happen. Eight forms from the wild
answer for exactly the majors they serve, checked against composer/semver's own
intersection rather than against a read.
`VersionsTest::aSpellingFromTheWildAnswersForEveryMajorItServes` holds them row
by row. One shape to the side did fire. A space between operator and version,
which Composer takes and this did not, so a range in that form answered for no
major at all. `Versions` collapses the space now. Composer's hyphen range occurs
nowhere in the corpus and nothing exists for it.
