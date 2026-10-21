---
id: D-ANS-181
title: The stale end reads when a person last commented
date: 2026-10-21
status: open
coveredBy:
  - ForgeTest::theStaleEndSaysWhenAPersonLastCommented
---

# D-ANS-181 — The stale end reads when a person last commented

**On `backlog="stale"`, every row carries `lastNoteOn`, the date of the last
comment a person wrote, read off the issue's journal. Every enumerated row
carries `reviewAbandoned`, true where the issue stands Under Review and every
change under it is ABANDONED.**

## Evidence

- [`feedback/2026-10-21-154127-updatedon-counts`](../../feedback/archive/2026-10-21-154127-updatedon-counts-a-bulk-edit-as-activity-so.md).
  Four of one person's open issues share an `updatedOn` within minutes. The
  journal of #109690, read on 2026-10-21, shows why. Georg Ringer changed a
  custom field on 2026-08-06 and wrote no note. The last comment was on
  2026-04-23. The stale list read those four as fresh.
- The same feedback names #108821 and #109256: Under Review, with only
  ABANDONED changes. The user found that useful, and nothing said it.
- The tracker's issue list carries no journal. One read per row is the only
  source, checked on 2026-10-21 with `include=journals` on the list endpoint.
- The maintainer chose the stale end alone on 2026-10-21, between that, every
  backlog row, and the flag and wording only.

## Decided

- The read takes the address an issue read takes, so the next call for one of
  the rows is held rather than read again.
- The bots `Forge::BOTS` names are left out, and so is a journal entry that
  carries no note.
- `stale` stays ordered by the tracker's date. An order by the comment date
  would read the journal of the whole set rather than of a page.

## Assumed

- That a comment is what attention looks like. A status change without a word
  by the person who works on it counts as no attention here.

## Wrong if

- A session reads a row with an old `lastNoteOn` as untouched while a status
  change or an assignment since then shows that somebody is on it.
- The page read on the stale end gets slow enough that a session reports the
  wait. Then the reads belong on request rather than by default.
