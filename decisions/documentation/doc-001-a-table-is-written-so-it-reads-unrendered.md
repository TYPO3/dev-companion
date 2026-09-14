---
id: D-DOC-001
title: A table is written so it reads unrendered
date: 2026-08-01
status: confirmed
coveredBy:
  - ProseTest::aTableComesBackPaddedToItsWidestCell
  - ProseTest::whatIsNotATableIsNotPadded
---

# D-DOC-001 — A table is written so it reads unrendered

**A markdown table holds only cells that fit on one line, and its columns stand
aligned.**

The compact `| --- |` form is not used, and neither is anything that would make
a cell span lines.

Both forms render identically, so the compact one looks like the cheaper choice.
It is cheaper only for whoever writes it once. Readers meet the file many times
in the state the writer left it. That is in a diff, in an editor with no
preview, in a terminal, and as text a model receives. Unaligned, a row is a run
of words with pipes in it, and a reader has to count out which cell a value
belongs to.

## Evidence

- Every table in this repository stood compact, the group listings
  `bin/cli requirements:index` and `bin/cli decisions:index` generate among
  them. The glossary added on 2026-08-01 is the case that made it obvious. A
  reader uses a lookup table *only* by a scan down a column, which is the thing
  the compact form removes.

## Decided

- Each column has the width of its widest cell, and the separator row is dashes
  of that same width. The generators write the same shape, so a regenerated
  listing does not undo it.
- A cell that will not fit on a line means the content is not a table. It
  becomes a list — `- **term** — what it is`, wrapped like prose. What a table
  buys over a list is a column a reader can scan down, and multi-line cells take
  exactly that away. The rows no longer sit on one level and what remains is the
  border characters. Aligning first is what makes this visible, since an
  unaligned table hides how wide it really is.
- The multi-line forms stand rejected rather than unavailable. Pandoc grid
  tables solve it and GitHub does not render them. `<br>` renders everywhere and
  puts markup in the middle of a sentence, which is the thing this decision
  exists to prevent.

## Assumed

- That the cost of alignment by hand is small. Padding is mechanical, and a
  table wide enough to make it painful is the case above.

## Wrong if

- Alignment drifts because nothing holds it. Then it is a rule nobody can follow
  reliably and it needs a check in `bin/cli` rather than a decision. Or
  something genuinely tabular turns up whose cells cannot shrink. Then a list
  will read worse than the table did, and the exception has to say so where the
  writer takes it.

## Since then

Both halves of the **Wrong if** fired, read as a sweep on 2026-08-22. Of 19
tables, 18 carry a row whose pipes stand in a different column from the
separator's; the alignment drifted wherever somebody edited a cell. The widest
cells are the mapping tables of `D-FBK-021`. By this entry's second **Decided**
bullet they are a list rather than a table, and nothing said so where the writer
took the exception. So the rule is right and nobody can follow it by hand, which
asks for a check rather than a decision.

## Since then

The alignment is the formatter's. `prose:format` pads a markdown table to its
widest cell the way it rewraps a paragraph. What it pads is a block whose second
row is a separator. So a fenced table, a headerless row and a drawn diagram come
back as they stand. The pad over the corpus broke one reader, which the run did
not expect. A vocabulary reader matched a table head by the text of its row, and
reads cells now. The second half is a report, because no formatter can make the
judgement: nine of the 19 tables held a cell no line fits.

## Confirmed on 2026-08-22

The nine had their judgement one at a time and the count is zero. Seven were
mappings and are lists now: nothing lost, both halves still there and wrapped
like prose. Two serve as data and stayed tables, because their cells could
shrink. So the second **Decided** bullet decided all nine, and nobody has taken
the exception it reserves yet.
