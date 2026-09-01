---
id: D-DOC-057
title: 'The decision corpus carries no duplicate pair'
date: 2026-08-23
status: open
readings:
  - 2026-09-01
---

# D-DOC-057 — The decision corpus carries no duplicate pair

**All 457 entries were read on 2026-08-23 and no two settle the same question,
so what looks duplicated is one recurring pair-shape rather than a defect.**

A sweep was asked for and found nothing, which is a result: without it recorded,
the next reader of a listing full of near-identical titles asks for the same
sweep again.

## Evidence

- Every entry was read — its title, its bold statement and its **Decided**
  section — group by group. The corpus is 2.9 MB, of which the statement and
  **Decided** halves are 615 KB.
- A lexical pass ran first and produced 91 candidate pairs over titles and
  statements, 12 over **Decided** sections and 6 over **Wrong if** sections.
  Each was opened. Every one was two entries.
- The pairs that scored highest are the shape rather than the defect.
  `D-FBK-005` → `D-FBK-012` at 0.54 is a revocation and its successor;
  `D-KNW-018` and `D-KNW-023` at 0.48 share a sentence template and answer for a
  datamap and for record placement.
- The recurring shape is a gap judged and then the corpus statement that fills
  it: `D-KNW-014` and `D-KNW-020`, `D-KNW-015` and `D-KNW-021`, `D-KNW-072` and
  `D-KNW-073`, `D-KNW-074` and `D-KNW-078`, `D-KNW-076` and `D-KNW-079`,
  `D-KNW-103` and `D-KNW-104`. Two entries each, one judging and one writing.
- Several families read as repetition and are not. `D-GUI-006`, `D-GUI-008` and
  `D-GUI-011` each add one `changeType`; `D-DOC-041`, `D-DOC-043`, `D-DOC-052`
  and `D-DOC-053` each hold a different half of what a test declares;
  `D-ANS-031` answers a core checkout and `D-ANS-092` a repository outside it.
- Where an entry does restate a sibling, the sibling is revoked and the
  restatement is what a reader is owed. `D-FBK-012` carries `D-FBK-005`'s
  reasoning almost word for word, and `revokedBy` exists so nobody has to open
  the dead entry to find it.

## Decided

- No entry is merged and none is deleted. There is nothing to merge, and
  [`writing-a-decision.rst`](../../documentation/records/writing-a-decision.rst)
  forbids the second either way.
- The sweep is recorded here rather than left in a session. Reading 457 entries
  is the expensive half, and a reader meeting the near-identical titles the
  templates produce would otherwise pay it again.
- Nothing checks it. Whether two entries settle one question is a reading, and a
  lexical measure over the corpus is what produced 91 candidates of which none
  was one.
- What the sweep did find is written where it belongs rather than here:
  `D-DOC-055` for the status the gap-shape was closed out under, `D-DOC-056` for
  the titles that named a deficiency, and `todo/open/2026-08-23-120000` for the
  four revocations whose surviving half no entry carries.

## Assumed

- That a duplicate would have shown in a statement or a **Decided** section. An
  entry whose **Evidence** repeats a sibling's while its decision differs is not
  a duplicate and was not looked for.
- That reading each entry once is enough to place it. Two entries settling one
  question in vocabularies that share no words would survive both the lexical
  pass and a single reading.

## Wrong if

- A session finds two entries settling one question and neither names the other.
  The reading missed it, and what it says about the method is that a sweep over
  statements is not a sweep over subjects.
- The corpus grows past the point where one session can read it whole, and the
  next sweep has to sample. Then this entry is the last one that can say the
  whole of it was read, and what replaces it is a subject index rather than a
  reading.
