---
id: D-KNW-165
title: A draft that moves a trailer says so
date: 2026-10-05
status: open
coveredBy:
  - CommitMessageTest::aDraftThatMovesATrailerSaysSo
---

# D-KNW-165 — A draft that moves a trailer says so

**Where the message had `Resolves:`, `Related:` and `Releases:` in another order
than the draft writes them, `typo3_commit_message_guide` reports
`trailers-reordered` as `info`.**

## Evidence

- `feedback/archive/2026-10-05-105018-core-check-keeps-an-assisted-by-trailer-and.md`.
  A review of change 96354 passed `Releases:` before `Resolves:`. The draft
  turned them round and no check said so, and a reviewer reads the checks rather
  than a diff of the draft.
- Read in `.checkouts/main` on 2026-10-05: of the last 200 commits, 198 put
  `Resolves:` before `Releases:` and 2 the other way round.
- The `Assisted-by:` half of the same feedback was answered before it was
  judged. Re-run on 2026-10-05, the draft drops the line and reports
  `refused-trailer`, which is `D-KNW-161`.

## Decided

- `info`, because the draft is right and only the move is news.
- The reason about merged core commits stands in the message under
  `workflow="core"` alone.

## Wrong if

- A reviewer strikes a patch for the order and the check had not said it.
