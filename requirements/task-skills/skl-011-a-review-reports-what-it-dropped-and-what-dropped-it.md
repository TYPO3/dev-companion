---
id: R-SKL-011
title: 'A review reports what it dropped and what dropped it'
status: held
restsOn: [D-SKL-007]
heldBy:
  - SkillTest::aReviewReportsWhatItDroppedAndWhatDroppedIt
---

# R-SKL-011 — A review reports what it dropped and what dropped it

**A review reports a candidate it raised and let go with the evidence that let
it go. It lets one go only against something that disproves it.**

A dismissal nobody records leaves the same trace as a surface nobody opened. So
the report cannot tell the reader which of the two happened. To raise a
candidate costs a read. To drop one costs the author a finding and announces
nothing, which is why the two directions do not share a bar. The review reports
a candidate it can neither establish nor disprove as open, with the read that
would settle it named beside it.

Two dismissals carry their own line because they are the ones that go wrong. One
rests on a comment, a docblock or an annotation without a read of the
implementation it describes. One rests on a path that looks unlikely rather than
one that is impossible.

It holds over both reviews this server owns, the core patch review and the
conformance audit. What makes a dismissal expensive is the reader who pays for
it rather than the surface it rests on. What the audit adds is one boundary. A
subsystem the package does not ship gets not applicable on the coverage list and
is not a dropped candidate. So the list holds what the review entertained as a
defect and then found was not one.

## From

The second recorded `REVIEW-03` run did this once, unprompted, and it is the
only recorded instance. `typo3_commit_message_guide` returned two warnings that
are artifacts of its own rewrite. The answer named them, said why they do not
hold, and discounted them rather than reported them as findings. The judgement
calls that "the behaviour the corpus keeps asking for and rarely records".
Nothing in the skill asked for it, so nothing makes the next run do it. Every
dismissal that run made in silence is unreadable either way.

The conformance checklist stated the bar for a security verdict alone. It "has
to be disproved before it can be dismissed". The reason under it is about who
pays for a wrong dismissal rather than about security. `D-SKL-007` records where
a session read the general form and what it rejected with it.

A session measured whether an audit can carry the same demand on the two
recorded conformance runs (2026-08-03). It did not argue it from the size of the
surface. Both write dismissals into the answer already, four each, and nobody
asked either for one. `REVIEW-01` discounts the Content Security Policy and
RTE-sanitizer settings as core defaults rather than project regressions. It
places the consent checkbox outside what this server answers, and clears the
dotless translation domain against `LocalizationUtility:66`. `REVIEW-02` leaves
the `ext_tables.sql` delta-only rule unraised for want of a schema-analyzer
comparison. It keeps the `event.listener` and `console.command` YAML tags and
the `getMajorVersion() < 13` branch as required by the declared range. It reads
`provider` and `source` among the returned icon identifiers as an artifact of
the tool. Four is a section a reader sits through, and the first of
`REVIEW-02`'s is already the open case in the shape this asks for.
