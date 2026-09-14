---
id: D-DOC-066
title: A dated section says what the reading changed
date: 2026-08-28
status: open
coveredBy:
  - DecisionsTest::everyReadingAnEntryRecordsIsReadAsADate
  - DecisionsTest::noDatedSectionRunsPastTheMeasure
---

# D-DOC-066 — A dated section says what the reading changed

**A visit that changed nothing is a date in `readings:`. A dated section is what
a visit that changed something leaves, in twelve lines or fewer.**

The form invited an account of each visit, so an entry the repository applies
often became a journal of its own applications.

## Evidence

- Measured on 2026-08-28: 558 entries, 68 396 lines, of which 17 595 — a quarter
  of the corpus — sit below a dated heading. 564 sections, 522 of them
  confirmations and 42 revocations.
- The median section is 23 lines, 115 run past 40, the longest is 354.
- Two entries are journals: `D-FBK-018` carries 20 sections and 1 485 lines of
  them, `D-FBK-021` ten. Every strengths feedback went onto the first, which
  `documentation/records/judging.rst` rules out in as many words: "There is no
  journal beside the archive".
- `D-DOC-041` counted this per entry and reported it, on 2026-08-22. Six days
  and 117 entries later the corpus had grown the same way. So a report nobody
  can act on line by line was not the lever.
- The maintainer asked for it on 2026-08-28: the confirming "nimmt überhand",
  and the corpus gets a compaction rather than only a bound from here.

## Decided

- **`readings:` in the front matter** takes the date of a visit that changed
  nothing. It is data: a listing counts it, a check reads it, and the reader
  learns that somebody went back to the entry and when. That is all such a visit
  says.
- **A dated section is at most twelve lines** and says what changed. A **Wrong
  if** that fired, a statement that no longer describes this server, a boundary
  that moved. What does not fit is not prose to trim. It is a finding that
  belongs in `Decided`, in `Wrong if`, or in an entry of its own.
- **A status change keeps its section.** A move to `confirmed` or `revoked`
  changes what a reader may build on, so the first one earns its twelve lines.
  Every later visit to the same entry is a date.
- **A strengths visit goes to the entry whose boundary it is about.**
  `D-FBK-018` keeps only what it learned about its own rule.
- **Both forms count as a return to the entry.** `Decisions::revisited()` read
  the section alone. So a visit recorded the new way left
  `bin/cli unresolved:list` with the entry as unopened. It sent the next session
  to what had just had its read. Found on 2026-08-28 by the first such visit,
  `D-CAT-007`'s.
- **Reported rather than failed on** until the sweep ends. 352 sections ran past
  the measure on its first day. A check that failed on them would fail every
  branch until the last one is compact.

## Assumed

- That twelve lines is enough for a finding worth its place. The median section
  is 23 and most of that is the account of the visit rather than the finding.

## Wrong if

- Somebody reads a compact entry and cannot reconstruct the visit that produced
  it from what remains, in a case where they needed it.
- The sweep ends and sections run past the measure again, which would say the
  cap is under what a finding costs rather than over it.
- A date in `readings:` stands for a visit that did change something, because a
  date is cheaper than a sentence. That is the failure mode the twelve lines
  exist to make affordable.

## Since then

The sweep ended the same day it started, over 352 sections in thirteen groups,
and `bin/cli decisions:check` fails on one now. `decisions/` went from 68 396
lines to 57 120, of which 4 703 sit below a dated heading against 17 595. That
is a quarter of the corpus to eight per cent.

Two things the sweep found. The measure is a target rather than a fact: nineteen
compactions came out at 13 to 18 lines and lost a sentence each. And `feedback/`
stayed out when the work split into cards. Its two journal entries had a card of
their own and the group went with them. A card derived from a measurement
inherits whatever the measurement grouped by.
