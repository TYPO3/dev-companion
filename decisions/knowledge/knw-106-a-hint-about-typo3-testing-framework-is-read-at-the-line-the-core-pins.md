---
id: D-KNW-106
title: A hint about typo3/testing-framework is read at the line the core pins
date: 2026-08-23
status: open
coveredBy:
  - PinnedPackageTest::aPinThatNamesOneReleaseLineIsThatLine
  - PinnedPackageTest::aPinThatNamesTwoLinesNamesNone
---

# D-KNW-106 — A hint about typo3/testing-framework is read at the line the core pins

**The covered branch's own `require-dev` pin decides which release a hint about
`typo3/testing-framework` stands verified against. That is the newest tag of the
line it names.**

`D-KNW-002` wrote the pair down and it was off by one line. It read v12 against
7.1.1, a release that answers for a major nobody covers. What replaced it stands
nowhere, and the class that holds it says so with the name of a revoked entry.

## Evidence

- `TestingFramework::pairing()` reads each covered branch's own `composer.json`.
  `bin/cli checkouts:status` on 2026-08-23: 12.4 pins `^8.3.1` and reads at
  8.3.3. 13.4 pins `^9.2.1` and 14.3 pins `^9.5.0` and both read at 9.6.1.
  `main` pins `dev-main` and reads at `main`.
- Nothing records a number, so nothing goes stale unwatched. `ref()` takes the
  line's newest tag, which `bin/cli checkouts:update` moves. `catalog:check`
  refuses a worktree that stands behind it rather than reads the older release.
- The command reports a pin that admits two lines instead of resolves it.
  `TestingFramework::line()` returns null, and the branch prints as
  `names no single release line`. A core major that admits two harnesses no
  longer says which one a statement bound to it came from. A pick would be a
  guess with a version number on it.
- What the check reads the tags for is the half of the statements that bears the
  load, which `CatalogCheck::TESTING_FRAMEWORK_EVIDENCE` names file by file.
  That is the gap `D-KNW-002`'s **Wrong if** left open and the reason its pair
  had to be right in the first place.

## Decided

- The pair derives per branch and lives in
  `TYPO3\DevCompanion\Upkeep\TestingFramework`, which `checkouts:status`,
  `checkouts:update` and `catalog:check` all ask. A number written into an entry
  is what went wrong once already.
- The pair names the line and not the tag. A release inside a line arrives with
  the next `checkouts:update`, and one that changes nothing relevant passes
  without a word.

## Assumed

- That a statement true at a line's newest tag is true at the release a project
  actually installed from that line. `^9.2.1` admits 9.2.1 and this reads 9.6.1.
  The four behaviours the needles cover have survived four majors unchanged,
  which is `D-KNW-002`'s assumption and comes down as it stands rather than
  re-measured.
- That the core's own `require-dev` is what a project resolves to as well. A
  line admits the major it serves and the one before it, so an extension on v12
  installs 8.x. That is where that read came from.

## Wrong if

- A covered branch pins a constraint that spans two lines. The pair then names
  none, and every statement bound to that major loses the harness it came from.
  What remains is a bound of the statements to the package version, which the
  hint format still has no field for.
- A behaviour moves inside a line and the older tag is the one a caller has. The
  needles read the newest tag only. So the answer would be right for the release
  this repository reads and wrong for the one the caller installed.
