---
id: D-ANS-030
title: The changelog matcher runs over the title it prints
date: 2026-08-02
status: open
coveredBy:
  - ChangelogTest::aMethodNameOnlyTheStatedTitleSpellsReachesTheEntry
---

# D-ANS-030 — The changelog matcher runs over the title it prints

**`typo3_changelog_lookup` matches the entry's file name and the words that name
spells. Nothing searches the title the same answer prints, and nothing searches
the body.**

The `query` field says "matched against its title" and the answer lists that
title back. One entry has the title *Deprecate
LocalImageProcessor::getTemporaryImageWithText*, and that method name cannot
reach it.

## Evidence

- `feedback/2026-08-01-115112` re-run on 2026-08-02 from
  `/home/benji/projects/typo3-cms`, against the 3794 entries that checkout ships
  in `Documentation/Changelog/7.5` through `15.0`.
- The reported cause fails. `query: "GifBuilder placeholder preview thumbnail"`
  with `version: "15"` returns nothing and reports that "preview" reaches 1
  entry, which is the count inside `15.0`. Drop the version and every word
  reaches: gifbuilder 4, placeholder 10, preview 28, thumbnail 9.
- The entry the session wanted is in `13.0`, not in 15.
  `query: "image generation"` with no version returns
  `13.0 Breaking: Removed public methods related to Image Generation (#101955)`,
  alone, in one call. So the title is what would have reached it, and the
  version filter is what emptied the answer.
- What the re-run does find is the reverse of the report.
  `7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions` has the title
  *Deprecate LocalImageProcessor::getTemporaryImageWithText* inside the file,
  and the tool prints that title. `query: "getTemporaryImageWithText"` reaches
  nothing. `Changelog::entries()` matches `key` and `source`, both derived from
  the file name. `Changelog::read()` opens the file for the title, the removal
  and the tags and hands none of it to the matcher.
- 708 of the 3794 entries carry a word of four letters or more in their in-file
  title that the file name does not. `15.0/Breaking-110196` has the title *PHP
  class Rfc822AddressesParser removed* against `Rfc822AddressesParserRemoved`.
  `14.3/Deprecation-109517` spells the full
  `TYPO3\CMS\Setup\Event\AddJavaScriptModulesEvent` where its name says
  `AddJavaScriptModulesEvent`.
- The body carries more again. `getTemporaryImageWithText` is in the body of
  three entries: `13.0/Breaking-101955`, `8.0/Breaking-72426` and
  `7.1/Deprecation-46770`. It is in the title of one of them, and in no file
  name at all. Across the corpus there are 10842 distinct `:php:` roles in 1951
  of the 3794 entries.
- What each field costs, measured in the same run. Scanning the names of all
  3794 entries is 48 ms. A read of every one of them is 818 ms cold and 45 ms
  against a warm page cache. That is the read `D-ANS-006` priced at 600 ms for
  the tag filter. Narrowed it is small: 4 ms for the 352 entries of 13, 1 ms for
  the 75 deprecations of 14. Extracting the `:php:` roles is 30 ms on top of a
  warm read.

## Decided

- **Step 1b of the ladder, and not step 4.** No rewrite of the schema makes
  `getTemporaryImageWithText` reach the entry whose printed title carries it.
  The field the matcher runs over has to change, so the gap is the shape rather
  than the wording.
- **Queued rather than closed on the spot.** Both halves are `src/`, and the
  `query` description is a declared schema, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line.
- **The suggestion stands on its subject and fails on its diagnosis.** The
  report asks for an index over the `:php:` names and the Removed-lists because
  the match is "lexical against titles only". A match against titles is what it
  is not. The smaller half of the same gap sits one rung below the suggestion.
  The answer already opens and reads the title for every entry it returns.
- **The title half does not answer this feedback on its own.** It reaches
  `Deprecation-46770`, which names the method and points at
  `GraphicalFunctions`. The entry the session was after, `Breaking-101955`,
  carries the method in a Removed-list and nowhere else. Two todos, because the
  reads and the failure modes differ.
- Recorded here rather than against `D-ANS-006`. That entry decided how a term
  spells and left the matcher right. This is about which field the matcher gets,
  a question no entry states.
- `D-ANS-016` records the version-filter half of the same re-run, where the
  miss's wording already stands.

## Assumed

- That a caller after a removed method types the method name. The feedback says
  so outright in its own suggestion, and it is still one session.
- That the read stays affordable as the changelog grows. It is 3794 entries here
  against the 3766 `D-ANS-016` measured in `.checkouts/14.3`. The cost is per
  file rather than per matched entry, so a miss with no filter pays it whole.
- That the `:php:` roles are worth an index rather than only the Removed-lists
  the report names. 10842 distinct roles is a wide index, and nothing measured
  here says how much of it is a class an entry merely mentions.

## Wrong if

- A query that names one identifier comes back with entries about another,
  because the body names every class the change touches. `Breaking-101955`
  writes `GraphicalFunctions` 44 times under a title about image generation, so
  that query would gain an entry whose subject is a different class.
- The read shows up as latency where the names answer today. The unfiltered miss
  above costs 88 ms off the names alone and would carry the whole-file read on
  top of it.
- The title lands and a later feedback reports the identifier still unreached,
  because the file spells it `->getTemporaryImageWithText()` and the caller
  types it bare.
- A caller narrows by version, gets nothing, and reads the silence as the
  answer, exactly as this session did. Then the field the matcher reads was not
  what cost this feedback its call, and `D-ANS-016` carries the whole of it.

## Since then

The title half is in the code. The stated title goes into a field, and the
matcher runs over it beside the file name and the words that name spells. The
reported query now returns its entry.

Where the read goes was the open half, and `D-ANS-041` settles it. The tool
scans the names on every call and opens the titles only where those names carry
nothing. So a call that already answers costs what it did. The **Wrong if**
about a spelling the caller does not type does not hold for the title, because
the match is containment. The other two are open, restated against the read as
it now stands.
