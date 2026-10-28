---
id: D-ANS-190
title: A PHP identifier reaches a manual changelog entry through its listing
date: 2026-10-28
status: open
coveredBy:
  - CoreChangelogTest::aClassNameWithoutAHumpIsNoIdentifierInTheListing
  - CoreChangelogTest::aClassOrMethodReachesAManualEntryThroughItsListing
---

# D-ANS-190 — A PHP identifier reaches a manual changelog entry through its listing

**`typo3_changelog_lookup` reads the PHP classes and members of a manual entry
from the `classes` of its entry in `Changelog-<major>.json`. It reads no class
index any more.**

`D-ANS-168` read `classes.json` as a third read, because the listing carried no
identifiers. Since render-guides#1464 the listing carries them, so the third
read is gone, and with it the answer that names a missing index.

## Evidence

- **Read on 2026-10-28**: 283 of the 459 entries in `Changelog-14.json` carry
  `classes`. A class maps to its places, each with `section`, `kind` (`inline`
  or `code`) and `members` where some follow.
- The same day, over `Changelog-12.json` to `Changelog-15.json` and
  `classes.json`: 1469 listed pages, and every page gives the same identifiers
  through `Changelog::named()` from both sources. No identifier stands in one
  source alone.
- `classes.json` is 2.9 MB on that day. The four listings are 2.2 MB together,
  and the lookup reads them on every call already.

## Decided

- **The identifiers are a field of a listed entry**, `named`, built when the
  listing is read. A search reads them only in the pass where the names answered
  nothing, as before.
- **A class and its member are read as one inline literal, through
  `Changelog::named()`**, the rule `D-ANS-168` took over from `D-ANS-042`.
- **No note for a missing index.** A listing that came in carries the classes. A
  listing that did not come in has its own note already.
- Left alone, as `D-ANS-168` left it: a constant, a TCA or TypoScript key and a
  `$GLOBALS` path. The listing names none of them.

## Assumed

- That docs.typo3.org keeps `classes` in the listing as render-guides#1464
  writes it.

## Wrong if

- An identifier that `classes.json` names reaches no manual entry. Then the
  listing has fallen behind the index, and the index read comes back.
- The listing loses `classes` in a redeploy. Every PHP identifier then reaches
  the installed entries alone, and nothing in the answer says so.
