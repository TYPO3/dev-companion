---
id: D-ANS-042
title: An identifier reaches the changelog entries whose body names it
date: 2026-08-03
status: open
coveredBy:
  - ChangelogLookupTest::aQueryTheNamesAnswerIsNotWidenedByTheBodies
  - ChangelogLookupTest::aRemovedMethodReachesTheEntriesNamingItInTheirBody
  - ChangelogLookupTest::aWordThatIsAlsoWrittenAsCodeIsNotAnIdentifier
  - ChangelogLookupTest::anIdentifierIsReachedInEverySpellingACallerHasIt
---

# D-ANS-042 — An identifier reaches the changelog entries whose body names it

**`typo3_changelog_lookup` reads the entry bodies where the file names carried
nothing of the query. A class, method or constant a body writes reaches that
entry whole.**

A caller after a removed method types the method name, and the name is in the
body. `getTemporaryImageWithText` is in three entries and in no file name, which
is the half of `D-ANS-030` the title alone does not answer.

## Evidence

- Measured on 2026-08-03 against `.checkouts/main`, which ships 3793 entries in
  `7.0` through `15.0`.
- Indexing the `:php:` roles the feedback asks for reaches one of the three
  entries. `13.0/Breaking-101955` writes the method as a role;
  `8.0/Breaking-72426` and `7.1/Deprecation-46770` write it in single backticks,
  because the role postdates them. Every inline literal, in whatever markup,
  reaches all three.
- A whole role is not a searchable word. The class path is part of it, so a
  split of one leaves `CMS` at 1080 entries and `Core` at 530.
- Restricted to the words with a hump or an underscore, the index is 8561 names
  across 3037 entries. 4885 of them reach exactly one entry and 96% reach at
  most five. None of `image`, `form`, `core`, `backend`, `text`, `page`, `file`,
  `preview`, `placeholder`, `request`, `true`, `false`, `null`, `this`, `and`,
  `default`, `content` or `event` is in it.
- What the read costs: 39 ms warm to open all 3793 entries and 29 ms to take the
  identifiers out of them. Through the tool, the miss with no filter that pays
  it is 112 ms against the 44 ms a query the names answer costs. Narrowed to
  `13` it is 20 ms.
- What the tool answers with it. `getTemporaryImageWithText` returns the three
  entries above. `image generation` returns the one entry it returned before.
  `GraphicalFunctions` returns the 6 entries named after it and not the 18 whose
  bodies write it.

## Decided

- **The names answer, and the tool reads the bodies only where they answered
  nothing.** A call that hits pays no read, and nothing that matched before
  misses now. The order keeps out the first **Wrong if** of `D-ANS-030`: the
  entries named after one class answer the query for it.
- **An identifier is a word an inline literal writes with a hump or an
  underscore.** That rule alone keeps the words of a query out of an index the
  tool compares a term to whole. `Request`, `Event` and `File` are class names
  spelled exactly like what a caller means by them. Rejected: the `:php:` roles
  alone, which reach one entry of three. The Removed-lists the feedback names,
  which are narrower again. And every word of a literal, which brings the
  ordinary words back.
- **The tool compares a term to the name it ends in, whole.** A caller types the
  identifier at whichever specificity they have it, and the last segment of
  `\TYPO3\CMS\Core\Imaging\GraphicalFunctions->getTemporaryImageWithText()` is
  what the query is about. Rejected: containment, which is how `image` would
  reach every entry that names `imageCreateFromFile`.
- **The answer says which of the two carried the query**, in the text and as
  `matchedIn`. A body match names the identifier and is not about it.
- Left alone: the miss advice. The per-term counts and the largest subsets that
  reach still run over the names. So a miss that mixes an identifier with a word
  it cannot place says nothing about the identifier.

## Assumed

- That a caller after a removed method types the identifier rather than a
  description of it. The feedback says so in its own suggestion, and it is still
  one session.
- That an entry which names an identifier is worth a return where no entry has
  its name. Nothing measured here says how much of the index is a class an entry
  merely mentions, and `E_USER_DEPRECATED` reaches 329 entries.
- That the read stays affordable as the changelog grows. It is per file and on a
  miss, so an unfiltered miss pays it whole.

## Wrong if

- A query that names an identifier comes back with entries about another
  subject. The order keeps that out for a name some entry carries in its file
  name and not for one no entry does. `E_USER_DEPRECATED` answers with 329
  entries, and almost none of them is about it.
- The read shows up as latency where a miss used to be cheap. The unfiltered
  miss is 94 ms here, against the 48 ms the names alone cost.
- A caller types a method spelled as one lowercase word, `crop`, `scale`,
  `output`, and reaches nothing. The index carries no word without a hump.
- A caller reads a body match as an entry about the identifier, although the
  sentence beside it says that it is not.
