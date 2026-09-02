---
id: D-ANS-128
title: An answer's index buys recovery and its foot is read last
date: 2026-09-01
status: open
readings:
  - 2026-09-02
coveredBy: []
---

# D-ANS-128 — An answer's index buys recovery and its foot is read last

**The `availableHints` index is how a caller finds an entry by name, and what an
answer places after it reaches only a caller already looking for it.**

The index and the `documents` array are both size an answer spends on things the
caller did not ask for, and one debrief reported the first as the recovery path
and the second as the thing it read past.

## Evidence

- The strength, from the debrief of 2026-08-31: at 21:57 the user wrote "all das
  steht im mcp", and the session fetched `language-files`, `content-elements`,
  `content-element-preview` and `content-element-shape` by id in sequence. Those
  ids are unguessable, and the index is where they were.
- The cost, from the same debrief four cards later: the `documents` array sits
  after the hints and after an index of roughly 150 entries, and was read
  top-down for what the caller came for. `extension/testing/phpunit` was found
  there and is the session's best call; `any/testing/browser-check`, in the same
  array, was not fetched.
- Three further strengths of the same shape. An empty `typo3_changelog_lookup`
  answer carrying `termCounts` and `termSubsets` turned a wasted call into the
  fix on the next one; `typo3_icon_lookup` reporting the `EXT:` source is a
  different fact from the SVG existing; the phpunit guide's per-major
  duplication and its "what a run leaves" section were both load-bearing.

## Decided

- The index stays and its size is what the recovery costs. Nothing trades it
  away for length, and `D-FBK-020` is the budget it is paid out of.
- Where the boundary runs: the index is a **recovery** surface, read on purpose
  by a caller who knows there is something to find. The foot of an answer is not
  a **delivery** surface, so anything that has to arrive unasked belongs beside
  the hit rather than after the list — which is what `D-ANS-127` decides for the
  documents that yield a judgement.
- The three other strengths are kept as they are, and this entry is where that
  was read rather than a note in each tool.

## Assumed

- That the recovery is worth what the index costs every answer that carries it.
  One session reported using it once, at the moment it was told the answer was
  here, and nothing counts how often that moment comes.

## Wrong if

- A session reports the index as the reason it read no further, which would make
  the size the cost rather than the price of the recovery.
- Moving what has to be delivered beside the hit makes the hit itself unread,
  and the answer ends up with two surfaces nobody finishes.
