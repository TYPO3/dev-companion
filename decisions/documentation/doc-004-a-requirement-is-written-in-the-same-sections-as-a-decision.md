---
id: D-DOC-004
title: A requirement is written in the same sections as a decision
date: 2026-08-02
status: open
coveredBy:
  - RequirementsTest::everyRequirementIsWrittenInTheSectionsTheFormatHas
  - RequirementsTest::everyRequirementNamesWhatHoldsIt
---

# D-DOC-004 — A requirement is written in the same sections as a decision

**A requirement consists of `## From` and `## Held by`, the second a list of
tests, and its three states are the `RequirementState` enum.**

`D-DOC-003` gave decisions sections because a label repeated up to seven times
in one entry. That reason does not exist here, and a different one does.

## Evidence

- Requirements carry a median of two labelled fields and a maximum of two. Those
  are `From`, on 115 entries, and `Held by`, on 123, and neither ever appears
  twice in one file. Nothing like the repetition `D-DOC-003` stands against.
- `Held by` names more than one test on 60 of the 123 entries, three or more on
  34, and nine on `R-AUD-004`. All stand inside one comma-separated sentence
  with the prose that qualifies them run through it. That is a list written as a
  paragraph.
- The three states were bare strings in 25 places across `src/` and `tests/`,
  the last of them after `DecisionStatus` took the decision side.

## Decided

- Sections anyway, for consistency rather than for repetition. The same people
  read both directories and both name tests. So `## Held by` and `## Covered by`
  are the same shape in the same place. The cost is four lines on a minimal
  entry, paid once.
- `Held by` is a list, one item per line. A whole test class stays a legitimate
  item. `VersionsTest` in full is a claim about every method in it. A list of
  them one at a time would go stale on the next one.
- `not guarded` is not a written state and never was. It is what a claim of
  `held` turns out to be when the entry names no test. So `RequirementState` has
  three cases and only two of them may appear in front matter;
  `RequirementState::written()` is that list.
- How to write a requirement moved to
  `documentation/feedback/writing-a-requirement.md`, which is what
  `documentation/readme.rst` already says the split is. `requirements/readme.md`
  went from 107 lines to 62.

## Assumed

- That the 123 entries survived a scripted rewrite. The converter split
  `Held by` on the backticked test names, which assumes the prose between two of
  them belongs to the first. That held in every entry read by hand, and no check
  would notice if it were wrong somewhere.
- That one paragraph of body prose between `From` and `Held by` was the only
  one. A count found it, in an entry written earlier the same day. It moved
  above `## From` rather than became part of it.

## Wrong if

- An entry appears that wants a second `From`: a requirement that two separate
  sessions demanded, where one date would drop the other. The sections allow it
  and nothing here has needed it. So the first one to try it is the test of
  whether the shape was right.
- The four extra lines make a short requirement look heavier than it is, and new
  entries start to arrive without a `From`. That section is optional today and
  present on 115 of 123.

## Since then

The page named above moved out of the feedback group, to
`documentation/records/writing-a-requirement.rst`, and the decision page with
it. Neither directory is a feedback's residue: a requirement is what must hold
and a decision is what backs it, whatever route the demand arrived by. The shape
this entry settled stays. Both still consist of the same sections, which is what
made one page per kind worth a split at all.
