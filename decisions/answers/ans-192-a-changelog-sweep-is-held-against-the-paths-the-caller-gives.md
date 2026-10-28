---
id: D-ANS-192
title: A changelog sweep is held against the paths the caller gives
date: 2026-10-28
status: open
coveredBy:
  - ChangelogLookupTest::aSweepHeldAgainstPathsKeepsTheEntriesNamingAClassTheCodeUses
---

# D-ANS-192 — A changelog sweep is held against the paths the caller gives

**`typo3_changelog_lookup` takes `paths`, the caller's own PHP. It keeps the
entries that name a class this PHP imports or writes fully qualified. It also
keeps the entries that name no class. Each entry carries `touches`: the class
and the files that use it.**

## Evidence

- **The miss.** The fourth `REVIEW-02` run of 2026-10-28
  (`scenarios/runs/REVIEW-02.json`, server `49b0adbc`) asked for the breaking
  entries of 15 as `D-SKL-108` asks, and got 41. It held 10 against the code.
  Breaking-110702 was among the rest. `Classes/Service/ImageInfoService.php`
  imports the `AfterFileProcessingEvent` that entry names.
- **Measured on 2026-10-28 against `/home/benji/projects/bootstrap_package` at
  `244d65a9`**, with the v15 listing of docs.typo3.org. `Classes/` holds 86 PHP
  files. The class match kept 24 of the 41: 17 name a class the code uses, and 7
  name no class. Breaking-110702 came back with `ImageInfoService.php` as the
  file.
- **A method name is the wrong key.** Every humped word of the code against
  every identifier of an entry matched 19 entries, and `makeInstance` alone
  carried several of them. The class names matched 17 and kept 110702.
- **A class match cannot see 7 of the 41.** Those entries name no PHP class:
  jQuery, a TSconfig option, the PHP minimum. So the answer keeps them, and the
  text says to read them against the rest of the code.

## Decided

- **One call, not one per identifier.** `D-SKL-108`'s first **Wrong if** named
  the identifier query. That is 61 calls for this package (`D-FBK-020`). The
  todo that queued this proposed the paths instead.
- **The name without its namespace.** It is the form `Changelog::named()` gives
  an entry on both sides. So an entry from docs.typo3.org and one from disk
  match by one rule.
- **Relative paths resolve against the directory the session started in**, which
  `Instance::startedFrom()` holds. An absolute path is taken as it is.
- **The text shows three files per class**, and the data carries every one.
  `GeneralUtility` is in 32 files of the measured package.

## Assumed

- That a breaking entry about PHP names the class it breaks. The listing names
  the classes per section, so an entry that names its subject only in prose
  drops out.
- That a session reads 24 entries with the files beside them, where it did not
  read 41 without them.

## Wrong if

- A review passes `paths` and misses a breaking entry the code calls. Then the
  class match is the wrong key, and the entry shows which key it needed.
- A short class name the core shares with another package matches entries the
  code has nothing to do with, often enough that a review reads them as noise.
  Then the match takes the namespace.
