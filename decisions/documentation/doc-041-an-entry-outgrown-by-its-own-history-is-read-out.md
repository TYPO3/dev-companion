---
id: D-DOC-041
title: An entry outgrown by its own history is read out
date: 2026-08-22
status: revoked
revokedBy: D-DOC-066
---

# D-DOC-041 — An entry outgrown by its own history is read out

**`bin/cli decisions:check` names the entries that carry more later account than
decision, and fails on none of them.**

A rule the repository applies often collects a **Confirmed on** per application.
Nothing about that is wrong, which is why nothing had counted it.

## Evidence

- Measured on 2026-08-22 over 441 entries, 51 684 lines. The median is 105 lines
  and 373 entries carry no dated section or one, so the format is not the cost.
  30 entries carry more later account than decision, and 13 carry four or more
  dated sections.
- `D-FBK-018` is the shape at its extreme: 107 lines of decision and 1283 of
  later account, in nineteen sections. Twelve of those stamp a date another
  already stamped. It states how a session reads a strength, and each section is
  one more strength read by that rule: eighteen visits, eighteen holds. That is
  what **Confirmed on** is for, and a reader who wants the rule pays for all of
  it.
- Two kinds sit in that one entry. Four sections open "the reading held a fourth
  time", "a fifth time", "a sixth time" and add no account. The rest fire a
  **Wrong if** or add a clause. Only the second kind is a decision under a
  visit.
- The strikethrough that retires a bullet a visit overtook stands in 4 entries
  of 441. `D-SKL-001` carries 57 lines of decision under ten readings and not
  one strike, so what of **Decided** still stands is the reader's arithmetic.
- The cost sits at the top but not only there. The 13 hold 30% of every
  later-account line, and the other 428 average 110 lines of which 20 are later
  account.

## Decided

- A report in `bin/cli decisions:check`, beside the problems and not among them.
  A count of its own carried it, with a test of its own to hold it, both gone
  with the revocation below. The command still exits 0, because a long history
  is legitimate. A check that fails on one would get shorter accounts of the
  same visits as its answer.
- The report names the longest three and counts the rest, so it does not become
  a list that grows with the corpus.
- The measure is lines rather than sections. Nineteen short confirmations and
  three long ones cost a reader the same, and it is the reader this is about.
- Nothing is rewritten by this. What the 13 need is a judgement per entry, which
  sections established something and which only counted. That is the todo this
  leaves, not a sweep.
- Rejected: a failure on a threshold. The number of visits an entry has had is
  not something its author controls. The entry that would fail first is the one
  whose rule the repository leans on most.

## Assumed

- That a reader of an entry wants the decision and reaches the history second.
  Where the entry states a rule still in use, the newest section may be what
  they came for. This reports the file as costly either way.
- That the split between account and count is visible per section. Their first
  words told four apart in `D-FBK-018`, and nothing says the other entries mark
  it as plainly.

## Wrong if

- Somebody reads the report and nothing follows, and the count climbs while the
  file says it every time. It stands at 30, and what would show this is 40 with
  no entry judged.
- An entry stays short because a visit goes unwritten, which is the failure this
  trades against and the more expensive one. An account nobody wrote is not
  shorter than a long entry, it is gone.
- The measure names the wrong entries. A 1283-line history that a reader
  genuinely wants to read is not a cost, and a feedback that says so would be
  the evidence.

## Since then

The thirteen got their judgement one commit each, and the corpus went from 5337
lines of later account to 4377. The second **Assumed** is what the sweep
corrected. The split is visible per section only where the section says what it
is. Per entry the share runs from all of it to none. Two had nothing to
collapse. One because every visit carries a mapping its own **Decided** says to
write down, the other because every visit measures a different run.

What the readings had overtaken was mostly a bullet nobody had struck. Twice a
bullet had to split first, because it held two falsifiers in one. Twice a visit
had written a new **Wrong if** into its own prose where a reader had to find it.
No collapsed visit needed an entry of its own: each already pointed at what
carried its result, which is what made it collapsible.

## Revoked on 2026-08-28

The report is per dated section now rather than per entry, and the ratio this
measured is gone with it. What the two counted is the same cost. What changed is
that a reader can act on the new one. It names the section to compact rather
than the entry to feel bad about. `D-DOC-066` carries the measure and the rule
under it.
