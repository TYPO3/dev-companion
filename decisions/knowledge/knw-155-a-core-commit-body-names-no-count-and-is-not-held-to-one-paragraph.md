---
id: D-KNW-155
title: A core commit body names no count and is not held to one paragraph
date: 2026-09-09
status: open
coveredBy:
  - CommitMessageTest::aCoreBodyCountingWhatItTouchedIsToldSo
---

# D-KNW-155 — A core commit body names no count and is not held to one paragraph

**A core commit body does not say how many files or occurrences a change
touched, and the length it is written at is two or three paragraphs rather than
one.**

A session was corrected on both by its reviewer and reported one convention it
had met and one it had inferred. The corpus carries the first and refutes the
second.

## Evidence

- **The report.**
  [`feedback/2026-09-09-182524`](../../feedback/archive/2026-09-09-182524-commit-message-guide-existed-was-listed-never.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. Five amends of one
  message, every one on style. The last two corrections were "bitte kuerze
  soweit wie moeglich, nicht pauschal 3 absaetze" and, against "in 68 files" and
  "in four spellings", "wir wollen auch nicht zaehlen, das machen wir nicht".
  From those it proposes the rules say "no counts of files or occurrences" and
  "as short as the change allows, one paragraph is normal".
- **The counts, measured in `.checkouts/main` on 2026-09-09.** Over the 3396
  commits since 2025-01-01, twelve bodies name a number of files, occurrences,
  places, spellings, classes, methods, instances, usages or call sites. Three of
  those are the narrower "in N files" shape. The convention holds.
- **The length, measured over the same population.** Bodies with the trailers
  dropped run to a median of 45 words and a ninetieth percentile of 143. By
  paragraph the median is three and the mode two: 1116 bodies of two, 797 of
  three, 501 of four, and 296 of one. One paragraph is the fourth most common
  shape, not the normal one.
- **What the page already says.** `core/contribution/commit-messages` asks for a
  brief body focused on what changed and why, written as short precise prose
  rather than as a list. It says nothing about counts.

## Decided

- **The counts rule is written into the page**, under Body, because the
  measurement is what a session cannot make for itself and a reviewer had to
  make the correction by hand.
- **The one-paragraph rule is not.** It would be this server stating as the
  core's convention what was one reviewer's preference on one patch, against a
  corpus that says the opposite. `D-FBK-052`'s licence is to make a change the
  run holds the evidence for, and the evidence here refutes half of what was
  suggested.
- **The brevity the reviewer wanted stays a review matter.** The page's existing
  "keep the body brief" is the general rule and a shorter body for a particular
  patch is a judgement about that patch.
- **Written on the spot.** It touches no `src/`, no schema and no skill, and the
  lookup it needed is the one this run made — so queueing it would send the next
  session to the same 3396 commits.
- Whether `typo3_commit_message_guide` checks a body for counts is queued
  separately, that being `src/`.

## Assumed

- That commits since 2025-01-01 on `main` represent the convention on the
  maintained branches too. Read on `main` alone, where the backports of the same
  patches carry the same bodies.
- That a count written as a numeral is how such a body would write one. A body
  spelling "sixty-eight files" is not in the twelve and was not searched for.

## Wrong if

- A session reports a reviewer asking for a count in a core commit body. Then
  the twelve are a convention nobody holds rather than one nobody breaks.
- The measurement is repeated on a later population and the counts rise. Then
  what was read here was a period rather than a rule.
- A reviewer is reported correcting a two-paragraph body to one on the grounds
  that one is the convention. Then the corpus and the practice disagree and the
  practice is what a contributor meets.
