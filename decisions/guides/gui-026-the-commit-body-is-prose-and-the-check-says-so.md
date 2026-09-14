---
id: D-GUI-026
title: 'The commit body is prose, and the check says so'
date: 2026-09-02
status: open
coveredBy:
  - CommitMessageTest::aBodyWritingItsArgumentAsBulletsIsReported
---

# D-GUI-026 — The commit body is prose, and the check says so

**The check reports a body whose argument stands as bullets, and not a list that
enumerates what the change touched.**

`typo3_commit_message_guide` measured the subject, the width and the trailers
and passed a body of three dashes with "no issues found".

## Evidence

- The maintainer ruled the prose body a core rule on 2026-09-01, `D-FBK-053`,
  where the migrated memory it arrived in has its judgement.
- The core writes it that way. Of the last thousand merged commits on `main`
  with a body, read on 2026-09-02, 115 hold a list line. One is half or more
  list lines.
- That one is the shape the rule allows: `19a47d9521a` explains the ruleset
  change in a paragraph and then lists the rule names it dropped. So the
  threshold cannot be "a list line" and cannot be "half the lines" either.
- Counting only the items that carry four words or more separates them, and over
  the same thousand bodies it fires on none. A shorter item is a class, a path
  or a rule name; a longer one is a sentence somebody wrote as a bullet.

## Decided

- `body-written-as-a-list`, at `warning` in both workflows. Nothing refuses such
  a commit, since the core's hook measures lines and writes the `Change-Id`. So
  this is what a reviewer strikes, which is the level `summary-length-preferred`
  already uses for a preference (`D-GUI-003`).
- The message says what a list in a body is for rather than only what to stop.
  The check fires on the shape and cannot see the intent.
- The rule stands in `core/contribution/commit-messages` as well, in the section
  that already carries the blank line and the width. The document is what a
  session reads before it writes a message and the check is what fires on one.
  That is the split the 52 and 72 limits already have.
- The count stands in that document with its day, because it is a measure rather
  than a rule. It stands in this entry too, which is where a number belongs.

## Assumed

- That four words is where an enumeration stops and a sentence begins. Its
  calibration is one branch's last thousand bodies, and a project that writes
  its items longer would get a report for a list that enumerates.

## Wrong if

- A session moves its argument into a prose paragraph and leaves the bullets as
  a summary above it. The check would pass that and a reviewer would still
  strike it.
- The check fires on a body somebody wrote on purpose, often enough that the
  threshold is what is wrong rather than the body.
