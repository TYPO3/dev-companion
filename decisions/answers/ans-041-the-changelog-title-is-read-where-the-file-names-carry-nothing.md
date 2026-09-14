---
id: D-ANS-041
title: The changelog title is read where the file names carry nothing
date: 2026-08-03
status: open
coveredBy:
  - ChangelogTest::aMethodNameOnlyTheStatedTitleSpellsReachesTheEntry
  - ChangelogTest::aMissCountsEachWordOverTheTitlesItSearched
  - ChangelogTest::theTitlesAreReadOnlyWhereTheFileNamesCarryNothing
---

# D-ANS-041 — The changelog title is read where the file names carry nothing

**The file names are what `typo3_changelog_lookup` scans, and the title stated
inside each file is what an empty answer falls back to.**

[`D-ANS-030`](ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md)
settled that the field the matcher runs over has to change and left open where
the read goes. The read is per file and the scan is per name.

## Evidence

- Measured on 2026-08-03 against `/home/benji/projects/typo3-cms`, whose core
  package ships 3795 entries, on a warm page cache. Scanning the names is 71 ms.
  Opening every one of them for its title is 103 ms on top, against the 818 ms
  `D-ANS-030` measured cold the day before. Narrowed to the 352 entries of 13 it
  is 14 ms.
- `query: "getTemporaryImageWithText"` returns
  `7.1/Deprecation-46770-LocalImageProcessorGraphicalFunctions` in 103 ms, alone
  and in one call. The same query returned nothing before this change, which is
  the miss behind the answer to `feedback/2026-08-01-115112`.
- 573 of the 3795 entries carry a word of four letters or more in the stated
  title that their file name does not. That splits the title on everything that
  is not a letter or a digit. `D-ANS-030` counted 708 of 3794 by its own split
  the day before. So the two agree on the size of the gap rather than on a
  number.
- A call the names answer costs what it did. `query: "deprecation"` matches 972
  entries in 54 ms and opens only the 20 it prints, exactly as before.
- Punctuation around the identifier does not block it. The match is containment,
  so the bare method name reaches the title
  `Deprecate LocalImageProcessor::getTemporaryImageWithText`. The third **Wrong
  if** of `D-ANS-030` does not hold for the title.

## Decided

- **The read is the fallback and not the scan.** Where the names carry the whole
  query the answer is what it was and costs what it cost. Where they carry
  nothing there is no answer for the read to slow down. Reading always was the
  alternative and it puts 818 ms cold on the call that already succeeds, which
  is the second **Wrong if** of `D-ANS-030`.
- **This entry states what that gives up rather than leaves it for somebody to
  find.** A query the names answer in part never reaches the entry only a title
  carries. The caller gets the smaller answer and nothing says a larger one
  existed.
- **The counts and the subsets a miss prints run over the titled entries too.**
  So what the answer says a word reaches is what the search covered for it.
- **The whole-changelog scan a narrowed miss makes stays on the names.** What it
  establishes is which filter emptied the answer (`D-ANS-016`). The call the
  caller then makes without that filter pays the whole-file read.
- **The title is a field of the entry rather than more words in `source`.**
  `LabelSearch::haystack()` is the one place a corpus says which of its fields a
  query matches against. So a second field enters there and not through a second
  matcher.

## Assumed

- That a caller whose query reaches something stops there rather than suspects a
  better entry behind the one it got. This is what the fallback rests on and
  nothing measured here says how often it holds.
- That the stated title carries the identifier a caller types. It does for
  `Deprecation-46770`, which is one entry; an identifier a title abbreviates is
  the same miss one field further in.
- That 818 ms cold on a miss is affordable. It is paid by a call that would
  otherwise return nothing, and the caller's alternative is another call.

## Wrong if

- A feedback reports an entry unreached whose title carries the query, because
  the names reached a different entry first. That is the case this shape gives
  up, and only a read of everything answers it.
- A miss becomes the slow call. One with no filter opens every file in the
  changelog before it says that nothing matched. A caller who narrows by version
  pays it once per version they try.
- A query that reaches by title returns entries about another subject, because a
  title names what a change replaces as well as what it deprecates.
- The names are no longer the cheap field, because a checkout ships enough
  versions that one scan of them is no longer 71 ms.
