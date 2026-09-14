---
id: D-DOC-035
title: What the prose costs is counted beside how long a sentence is
date: 2026-08-18
status: open
coveredBy:
  - ProseTest::aCommentThatNamesAnEntryAndRetellsItAnywayIsReported
  - ProseTest::whatTheCommentsCostIsMeasured
  - ProseTest::whatTheMarkupCostsIsNotCountedAsProse
---

# D-DOC-035 — What the prose costs is counted beside how long a sentence is

**`bin/cli prose:check` counts what the comments cost. That is the share of the
PHP that is comment, and every comment that names an entry and retells it
anyway.**

The one counter this repository had measured length per sentence, which two
sentences pass, and it read no file in `src/` at all.

## Evidence

- Measured on 2026-08-18: 13875 of 37622 non-blank lines below `src/` are
  comment, 37%. By group it runs from `Tool/` at 22.5% to `Search/` at 58.6%,
  and `src/Search/Text.php` carries 76 comment lines to 40 of code.
- 3384 comments below `src/`, of which 297 name a decision or a requirement. 172
  of those run past ten lines and hold 3006 lines between them. Below `tests/`
  the same count is 380 and 110.
- AGENTS.md already forbids what those are: "The reason lives in one place.
  Where a decision or a requirement carries it, the comment names the id instead
  of retelling it."
- Nothing read them. `prose:check` measured the markdown corpus and the connect
  payload, and `grep` for `T_COMMENT` across `tests/` and `src/Upkeep/` found
  nothing. So no check and no test counted a comment.
- The counter that existed rewards a split: two sentences of twenty words pass
  where one of thirty-five does not. So the measure meant to hold "length is a
  symptom" pushes the number of sentences up.
- `decisions/` holds 44289 lines in 375 entries, a mean of 996 words each,
  against 41569 lines below `src/`. The record corpus is larger than the code it
  decides about.

## Decided

- The count covers the comments where the rule applies: `src/` and `tests/`.
  Both binaries stay out, because each locates an autoloader and hands its
  arguments to a class.
- The report is a share and not a count, because a count of something that grows
  is true on one day.
- A retelling is a comment that names an entry and runs past ten lines. Where
  the line stands moves the count and not the result: 216 such comments run past
  four lines, 205 past eight, 172 past ten.
- It reports and does not fail. A long comment that names an entry can be the
  right comment; it may rest on that decision while it explains something else.
  Only somebody who reads the block can tell the two apart.
- Rejected: a failure on the 240 comments the report finds today. That is a
  sweep in which somebody reads each one, and it sits in the queue as one.
- Rejected: a ceiling on the comment share per file. `Tool/` sits at 22.5%
  because its documentation is in the tool descriptions a client reads, and a
  ceiling would report that difference as a defect.

## Assumed

- What a retelling holds is in the entry it names, so a cut loses no evidence.
  Nothing has read the 240 to check that.
- A session writes in the register of what it read. Every one of them reads
  AGENTS.md, `judging.rst` and `skills/base.md` before its first change.
  AGENTS.md is second on this command's own list with 53 sentences past the
  measure. So a cut to those moves what sessions write more than a rule does.
  Nothing measures that either.

## Wrong if

- The share falls while the entries grow to hold the cut text. That is the same
  prose one directory over, and it would show the measure moved the volume
  rather than removed it.
- The list sits at roughly 240 for months. Then it reads as a fact about this
  repository rather than as work, and a report nobody acts on is noise this
  command adds to.
- The comments on it turn out, on a read, to be right more often than not. Then
  the shape is wrong: what such a comment names is a cross-reference and not a
  retelling.

## Since then

The measure had two reads and moved once. At ten lines the list no longer named
work. An id matched wherever it appeared, also where it was not a reason, so 113
of 141 entries were cross-references rather than retellings. A cut retelling did
not reliably take a comment off the list. Since 2026-08-19 the count is against
a comment's prose lines rather than its delimiters. So no number from before
that date is the same measure.

The floor is gone rather than worked around: a higher line would have kept a
list nobody could act on. The register did not move either; the files vary by
more in a day than the cut removed. What a session writes was already what the
cut reached for, and one wrote back a shape the cut had removed. So the second
**Assumed** is not held: the cut cost nothing and kept everything.
