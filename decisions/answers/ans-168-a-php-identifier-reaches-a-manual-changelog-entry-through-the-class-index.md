---
id: D-ANS-168
title: A PHP identifier reaches a manual changelog entry through the class index
date: 2026-09-23
status: open
coveredBy:
  - CoreChangelogTest::aClassNameWithoutAHumpIsNoIdentifierInTheClassIndex
  - CoreChangelogTest::aClassOrMethodReachesAManualEntryThroughTheClassIndex
  - CoreChangelogTest::aMissingClassIndexIsNamedInTheAnswer
  - CoreChangelogTest::aQueryTheNamesAnswerReadsNoClassIndex
---

# D-ANS-168 — A PHP identifier reaches a manual changelog entry through the class index

**`typo3_changelog_lookup` reads the class index docs.typo3.org publishes
beside the changelog, and a PHP class or member it names reaches the entry
from docs.typo3.org.**

`D-ANS-165` left the identifier search on disk, because the listing carries no
body. Every version docs.typo3.org lists is read from it, and that is every
covered major. So online, an identifier reached no entry of 12 to 15, whatever
the installation shipped.

## Evidence

- **Read on 2026-09-23**: `classes.json` in the `cms-core` manual, 2.1 MB and
  118 KB compressed, with an `ETag`. It maps 2079 class names to 7873 places.
  A place has `path`, `anchor` and `kind` (`role` or `use`), and `member`
  where one follows. It covers the changelog alone, from 7.3 to 15.0.
- The index keeps case variants apart: `\TYPO3\CMS\core\…` beside
  `\TYPO3\CMS\Core\…`. 132 names are outside `\TYPO3\`: PHP's own, Doctrine,
  and example namespaces such as `\MyExtension\Domain\Model\Item`.
- Through the `Changelog::named()` rule, the index is 6808 names over 1251
  pages. `Item`, `Blog` and `Site` are none of them.
- Against the core checkout at `/home/benji/projects/typo3-cms`, with
  docs.typo3.org up. `getTemporaryImageWithText` answered 8.0 and 7.1 and now
  also 13.0. `ContentObjectRenderer::getData` answered 8.2 and 7.1 and now also
  14.0 and 13.4.
- The cold read of the index is about 120 ms, parse included. A revalidation
  is 11 ms and no payload.

## Decided

- **The index is read in the pass that reads the bodies, and nowhere else.** A
  query the names answer asks for no index, so a hit costs what it cost before.
- **A class and its member are read as one inline literal, through
  `Changelog::named()`.** So the rule of `D-ANS-042` holds on both sides: a
  word with a hump or an underscore. The example classes an entry writes drop
  out by that rule, and the case variants merge in the lowercase index.
- **Both kinds count.** A `use` line in a migration example names the class to
  write instead, and a caller after that class asks for it.
- **An answer where the index did not come in says so**, on a hit and on a
  miss. The identifier search then reached the entries on disk alone.
- Left alone: a constant, a TCA or TypoScript key and a `$GLOBALS` path. The
  index carries none of them, so they reach a manual entry by its title alone.

## Assumed

- That the documentation team keeps publishing `classes.json` and keeps
  `path` the page path the listing uses.
- That the index lists every class the text of a page writes. Nothing here
  compared it with the RST.

## Wrong if

- An identifier an installed RST writes, in a version docs.typo3.org lists,
  reaches nothing although the class index should carry it. Then the index
  misses what the body read found, and the RST read comes back for those
  versions.
- The index grows a field for constants or configuration keys. Then the
  **Left alone** bullet is a read away.
- The cold read shows up as latency on a miss that used to be cheap.
