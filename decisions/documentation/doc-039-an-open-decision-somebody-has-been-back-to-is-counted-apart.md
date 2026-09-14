---
id: D-DOC-039
title: An open decision somebody has been back to is counted apart
date: 2026-08-22
status: open
coveredBy:
  - UnresolvedTest::anOpenDecisionSomebodyHasBeenBackToIsToldApart
---

# D-DOC-039 — An open decision somebody has been back to is counted apart

**`bin/cli unresolved:list` separates the open decisions nobody has opened from
the ones a visit left open, and names the oldest of the first kind.**

`open` is what a decision keeps when a visit settles its **Wrong if** neither
way, which is the ordinary outcome. Counted with the unread, half the pile
showed as untouched.

## Evidence

- Measured on 2026-08-22 over 439 decisions: 339 are open, and 168 of those
  carry a **Since then**. The report called all 339 "nobody has been back to
  their Wrong if".
- The entry it named as the oldest was `D-CAT-001`, which had been gone back to
  twice and said so in two sections. The oldest nobody has opened is `D-COD-001`
  of 2026-08-01, three days younger and never named.
- `D-GUI-001` and `D-CAT-003` had a visit on 2026-08-22 and neither could
  settle. One needs a placeholder seen in a pushed commit, the other state that
  exists only at runtime. Both are correct to stay open, and both would have
  gone back into the same undifferentiated count.
- The corpus spells the section two ways. 146 open entries carry
  `## Since then`; 28 more carry it as a bold paragraph, the form used before
  `D-DOC-003` moved the labels into sections. Reading only the heading reports
  those 28 as never read.

## Decided

- `Decisions::read()` carries `revisited`, true where the file has a **Since
  then** in either form. It comes from the body rather than from the front
  matter. The section is already the record of the visit, and a second place to
  say so is a second place to forget.
- The report says both numbers and names the oldest unread. That is the entry a
  session can still do something about; the oldest open one may be the most
  thoroughly read file in the directory.
- Rejected: a `checked:` date in the front matter, which is the first proposal.
  `confirmed` already means somebody went back and it held. So the date would
  have said what the status says for two of the three outcomes and repeated the
  **Since then** for the third.
- Rejected: a rewrite of the 28 bold-paragraph entries into sections. Their
  paragraphs sit above **Decided** rather than at the foot and refer to what is
  around them. So a move of them is an edit per file rather than a substitution,
  and nothing about this visit needs it done first.

## Assumed

- That a **Since then** means somebody read the **Wrong if**. It is what the
  format carries for what followed, and a session that adds one has been in the
  entry. But nothing says the visit covered the **Wrong if** rather than
  something else the entry touched.
- That the two spellings are all of them. Both had a count on 2026-08-22 and
  every open entry matched one or neither.

## Wrong if

- A **Since then** arrives for something other than a back-check, and the entry
  drops out of the count while its **Wrong if** stays unread. That is the cheap
  answer this makes available, the way a `judged:` date is for a requirement —
  `D-DOC-038`.
- The unread count no longer falls because the read half absorbs the attention.
  171 today, and what would show it is a number that stands still while **Since
  then** sections accumulate.
- ~~The bold-paragraph form spreads to new entries. Nothing fails on it and it
  now counts the same as the section.~~ Closed the same day; see **Since then**.

## Since then

The 51 bold labels became sections the same day, so `revisited()` matches one
pattern rather than two. Four were bullets, which the two-form match had not
read either. What kept the form alive is that no check could see it, and
`Decisions::labelAsAParagraph()` fails on it now.

The flag reads a second form since 2026-08-28. `D-DOC-066` sends a visit that
changed nothing to `readings:` in the front matter and leaves no section. So an
entry read that way showed as never opened, found on `D-CAT-007`, the first such
visit. What this decides stands: the flag is the file's own record of a visit,
whichever of the two the visit wrote.
