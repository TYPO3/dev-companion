---
id: D-SKL-007
title: Every disposition a review makes carries its evidence
date: 2026-08-03
status: open
coveredBy:
  - SkillTest::aFindingSaysWhetherThePatchIntroducedIt
  - SkillTest::aReviewReportsWhatItDroppedAndWhatDroppedIt
  - SkillTest::aSurfaceReportedAsAssessedNamesWhatWasRead
---

# D-SKL-007 — Every disposition a review makes carries its evidence

**A review disposes of a thing in three ways. It reports it, it drops it, or it
declares it clean, and all three carry what backs them.**

Only the first of the three did. A dismissal nobody records reads like a surface
nobody opened. A surface reported as assessed with nothing under it is the
cheapest sentence in a review to write.

## Evidence

- The second recorded `REVIEW-03` run carries all three dispositions and asked
  for none of them. It discounted two `typo3_commit_message_guide` warnings by
  naming why they do not hold; it attributed twice, establishing that the
  parsing the patch adds already exists on `origin/14.3` and marking the
  empty-tab finding new; and it reported the security surface as assessed while
  the finding of the run before it — four fields written past the `$fieldList`
  guard — went unmade. Two behaviours that nothing required, and one failure
  that nothing caught, in one run judged as one that meets every criterion.
- That run's own judgement names the third as a shape rather than an accident:
  "a surface asserted clean rather than left unassessed, which costs more than a
  gap because the coverage table beside it is what a reader uses to decide what
  still needs looking at" — the shape `REVIEW-02` was partial for and
  `REVIEW-01` produced three times.
- A published multi-pass review pipeline for a large C project, read on
  2026-08-03, carries two lists through every pass rather than one. What the
  pass raised, and what it raised and disproved. Its consolidation pass learns
  that both lists are claims with no trust behind them and that neither side is
  correct by assumption. Its verification pass may discard a candidate only
  against concrete proof, and where it finds no such proof it reports the
  candidate.
- The same pipeline marks every finding as introduced or pre-existing, requires
  the reported wording to state it, and keeps pre-existing findings out of the
  report below its top two severities. It runs a benchmark of its own for that
  one property.
- Its severity guidance forbids a lower rank for a path that looks out of reach.
  The ground is that reach is the thing a diff establishes worst. A higher rank
  for input in reach stays permitted.
- The conformance checklist here already states the asymmetric bar for one
  subject. A security verdict "has to be disproved before it can be dismissed",
  and `SkillTest::aSecurityFindingIsNotEstablishedUntilItsSinkIs` holds it. The
  REVIEW-02 run that earned it did not drop a candidate; it reported one whose
  sink escapes. The bar stands for the subject and the reason under it is not
  about the subject. What makes a dismissal expensive is that its cost falls on
  the reader rather than on the review.
- Every verification step in that pipeline's own guide ends in an emission
  rather than in a judgement. "Output: quote the call chain with locations",
  "Output: subsystem invariants checked, or none", "Output: production code, or
  test code with the severity adjusted". A step that produces nothing was not
  performed, and the shape of the instruction is what makes that readable.

## Decided

- The checklist's first lines name the three dispositions as three, and state
  the demand once for all of them rather than per section.
- A surface reported as assessed names what the review read to assess it. Where
  the reading did not happen the word is unassessed, which costs the same line
  and says something a reader can act on.
- The checklist gains **What a dropped candidate owes**. The review names each
  candidate it lets go with the line that let it go. It drops a candidate only
  against something that concretely disproves it. One it can neither establish
  nor disprove it reports as open with the absent read named.
- The checklist names two dismissals, because they are the ones that go wrong. A
  drop on the strength of a comment, a docblock or an annotation with no read of
  the implementation it describes. And a drop because a path looks unlikely
  rather than because it is impossible.
- The author's case is made against a finding before it is reported, and what
  survives is reported together with what it survived.
- A finding carries a fifth obligation — whether this patch introduced it. What
  it did not stays in the review where it blocks submission on its own and goes
  to the issue tracker otherwise.
- Reachability may raise a rank and never lower one.
- A review reads a patch that is one of a set against the state at the end of
  the set. It opens the later patch rather than believes a message about it.
- This entry rejects the eleven-pass pipeline itself. What that shape buys is
  one lens at a time against a fixed schema, and what it costs is eleven reads
  of one diff. The checklist already enumerates the surfaces whole before the
  second read of the diff. The failure the surfaces exist for was a surface that
  went unreported rather than two lenses that interfere.

## Assumed

- That a reviewer who writes a dismissal down also holds it to the bar, rather
  than fills the section with candidates nobody seriously entertained.
- That a reviewer uses the pre-existing obligation to attribute a finding rather
  than to move an inconvenient one out of the report.
- That a core patch set is small enough that reading the later patches to settle
  a finding is affordable. The pipeline this came from reviews series of a size
  that made automation worth it.

## Wrong if

- A recorded review's dropped-candidate section carries dismissals with no line
  of code under them. That is the section as a place to look thorough, and it is
  visible in the report itself.
- A review reports fewer real findings after this than before, because the
  author's-case step is where a correct finding gets talked away. The forward
  runs in `scenarios/runs/` are where it would show.
- The pre-existing obligation appears on findings that the diff plainly
  introduced. That would mean a reviewer reads it as a way to soften a report
  rather than as a question about the diff.
- Reports start to mark surfaces as unassessed at a rate that says the word is
  an evasion. It avoids the name of a read rather than reports an absent one.
  The demand would then have bought a cheaper evasion than the one it closed.

## Since then

The section was scoped to the core patch review on the reading that an audit
would raise more candidates than a reader sits through. The two recorded
conformance runs say otherwise: each carries four dismissals in prose, none of
them asked for. So the requirement holds over both checklists and the
conformance one gained the same section, with one boundary the patch review does
not need. The coverage list answers a subsystem the package does not ship,
rather than a dropped candidate. The volume argument stays worth a watch in the
direction it came up for.
