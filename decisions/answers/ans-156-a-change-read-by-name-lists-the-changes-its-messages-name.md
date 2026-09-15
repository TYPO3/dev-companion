---
id: D-ANS-156
title: A change read by name lists the changes its messages name
date: 2026-09-15
status: open
coveredBy: []
---

# D-ANS-156 — A change read by name lists the changes its messages name

**`typo3_gerrit_lookup` lists the changes a change's messages name beside its
chain, so an alternative pushed as a separate change is visible in one read.**

A review session compared a change with two alternatives its author pushed. The
chain was empty, because the three are not stacked, and the only link between
them was a number in free text.

## Evidence

- **The report.**
  [`feedback/2026-09-15-073817`](../../feedback/2026-09-15-073817-a-comparison-of-alternative-changes-and-a.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5`. Three lookups, one per
  change, and a fetch of two refs for the diff.
- **Read on `review.typo3.org` on 2026-09-15.** Changes 95814, 95817 and 95818
  carry no topic and no hashtag. Their messages name each other by number, and
  each one's subject is the same line. So the review server has no relation
  between them that the tool could read, and the messages are the only source.
- **What the tool answers today.** The chain, the Change-Id siblings and the
  Forge issues, and the messages where `messages` asks for them. No field says
  which other changes a message names.
- **What a cross-change comparison would cost.** The review server diffs a patch
  set against another patch set of the same change and against its parent. It
  does not diff against another change. A comparison per path is possible
  through the content endpoint, two reads per path over the union of the two
  file lists. That is a build with its own tests and fixtures, on one session's
  report.

## Decided

- **Taken on: the numbers a change's messages name.** Step 1b, a shape absent.
  The answer gains a list beside `chain`. Each entry is a change number the
  review server resolves to a subject and a status. A Forge number in the same
  digits drops out there. It is queued rather than made here, because it touches
  `src/` and the output schema.
- **Proposed, and waiting on an answer: `against`.** A second change to compare
  the current patch sets with. The answer is the paths that differ, with their
  sizes. One session asked, and it says the fetch it made instead was the right
  cost. What the call would buy is the verdict before the fetch, in one call
  instead of three. What it costs is a read per path on the review server and
  the test fixtures for both. The recommendation is to build it after the list
  above has shown whether alternatives recur, and not before.
- **Against parsing the commit message body for the same numbers.** The trailers
  there are Forge issues, and the tool already reads them.

## Assumed

- That an alternative is named in a message more often than it shares a topic.
  Three changes from one author say so once.

## Wrong if

- A second session reports the same comparison. Then `against` is the thing
  itself and the list above was the half measure.
- The list names a change nobody meant, because a message quoted a number that
  resolves to an unrelated change. Then the resolution is not enough of a
  filter.
