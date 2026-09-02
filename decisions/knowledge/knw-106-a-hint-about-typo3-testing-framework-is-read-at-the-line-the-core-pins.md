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

**Which release a hint about `typo3/testing-framework` is verified against is
derived from the covered branch's own `require-dev` pin, at the newest tag of
the line it names.**

`D-KNW-002` wrote the pairing down and it was off by one line: v12 was read
against 7.1.1, a release answering for a major nobody covers. What replaced it
is written nowhere, and the class that holds it says so by naming a revoked
entry.

## Evidence

- `TestingFramework::pairing()` reads each covered branch's own `composer.json`.
  `bin/cli checkouts:status` on 2026-08-23: 12.4 pins `^8.3.1` and is read at
  8.3.3, 13.4 pins `^9.2.1` and 14.3 pins `^9.5.0` and both are read at 9.6.1,
  `main` pins `dev-main` and is read at `main`.
- Nothing is recorded, so nothing goes stale unwatched. `ref()` takes the line's
  newest tag, which `bin/cli checkouts:update` moves, and `catalog:check`
  refuses a worktree standing behind it rather than reading the older release.
- A pin that admits two lines is reported instead of resolved —
  `TestingFramework::line()` returns null, and the branch prints as
  `names no single release line`. A core major admitting two harnesses no longer
  says which one a statement bound to it was read in, and picking one would be a
  guess wearing a version number.
- What the tags are read for is the load-bearing half of the statements, which
  `CatalogCheck::TESTING_FRAMEWORK_EVIDENCE` names file by file. That is the gap
  `D-KNW-002`'s **Wrong if** left open and the reason its pairing had to be
  right in the first place.

## Decided

- The pairing is derived per branch and lives in
  `TYPO3\DevCompanion\Upkeep\TestingFramework`, which `checkouts:status`,
  `checkouts:update` and `catalog:check` all ask. A number written into an entry
  is what went wrong once already.
- The line is what is paired and the tag is not. A release inside a line arrives
  with the next `checkouts:update`, and one that changes nothing relevant passes
  without a word.

## Assumed

- That a statement true at a line's newest tag is true at the release a project
  actually installed from that line. `^9.2.1` admits 9.2.1 and this reads 9.6.1;
  the four behaviours the needles cover have survived four majors unchanged,
  which is `D-KNW-002`'s assumption and is inherited rather than re-measured.
- That the core's own `require-dev` is what a project resolves to as well. A
  line admits the major it was cut for and the one before it, so an extension on
  v12 installs 8.x, which is where that reading came from.

## Wrong if

- A covered branch pins a constraint spanning two lines. The pairing then names
  none, every statement bound to that major loses the harness it was read in,
  and what is left is binding the statements to the package version — which the
  hint format still has no field for.
- A behaviour moves inside a line and the older tag is the one a caller has. The
  needles read the newest tag only, so the answer would be right for the release
  this repository reads and wrong for the one the caller installed.
