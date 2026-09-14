---
id: D-KNW-132
title: The changelog directory is stated where the file is written
date: 2026-08-27
status: open
coveredBy:
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - KnowledgeTest::theChangelogDirectoryArrivesWhereTheFileIsWritten
---

# D-KNW-132 — The changelog directory is stated where the file is written

**The step that writes a changelog file states the directory it goes into,
rather than leaves it behind the call that reads the page.**

The rule stood written down, verified and in reach, from a question about the
directory. That is a question a session asks after it has picked one.

## Evidence

- The maintainer reports on 2026-08-27 that sessions keep writing a backport's
  entry into the directory of the release under development.
- `Documents::search('write a changelog entry for a bugfix')` returned *Which
  Change Owes a Changelog File*, *What a Changelog File Carries* and four
  sections of other pages. It did not return *Where a Changelog File Goes*.
  Measured against this branch before the change.
- `Documents::search('add a changelog file')` returned twelve sections and none
  of them from `core/contribution/changelog`.
- The section carried "changelog", "entries" and "backport" and none of "write",
  "entry" or "add". The search matches a term as a prefix of a word, so the
  query word "entry" does not reach the corpus word "entries",
  [`TermSearch::carries()`](../../src/Search/TermSearch.php).
- `typo3_task_guide` on a bugfix stated the obligation, named
  `typo3/sysext/core/Documentation/Changelog/` and named no directory below it.
- `typo3-core-patch-development` said the directory "follows the branches the
  patch reaches rather than the branch it is written on". That says which
  directory is wrong and never which is right.
- The statement itself holds. `.checkouts/main`'s
  `typo3/sysext/core/Documentation/Changelog/Howto.rst`, read the same day, puts
  a backport into the `<lts>.x` directory of the oldest branch it reaches. It
  puts a copy into both where two lines take it.

## Decided

- Three surfaces state it, because a session that writes the file reads one of
  the three and not the page. Those are the section's own first bullet, the
  skill's changelog step, and the `bugfix` item of
  `TaskGuide::CHANGE_TYPE_CHECKLIST`.
- Each states the directory alone — the `<lts>.x` one of the oldest branch the
  `Releases:` trailer names, both where two maintained lines take the change.
  The four types, the file name and what checks it stay the page's, and the two
  surfaces still route to it.
- The section uses the words that write a file rather than the words that ask
  about one: "write the entry", "add the entry". That is what the two queries
  reach, and it is [`AGENTS.md`](../../AGENTS.md)'s affirmative rule at the same
  time.
- Rejected: a higher rank for the section with the surfaces left alone. A
  session that never searches for the directory is not ranked into finding it,
  and both measured queries are searches for something else.

## Assumed

- That a session reads the checklist item and the skill step it stands on. The
  report says the directory was wrong, not that the session read past the
  sentence that names it.
- That the wording holds the two queries. Nothing here scores a rewrite, so a
  later edit of the section can drop the words that reach it.
  `KnowledgeTest::theChangelogDirectoryArrivesWhereTheFileIsWritten` is what
  fails on that.

## Wrong if

- A session with the current skill installed still writes a backport's entry
  into the release under development. Then the session reads past the step, and
  the lever is where it stands rather than what it says.
- A feedback reports the directory as noise in a brief for a change that reaches
  `main` alone. That is the common case and the one the rule is silent about,
  since it names `main`'s own directory second.
- The core moves the rule. It is one sentence of `Howto.rst`, and three copies
  of it here go stale at once.
