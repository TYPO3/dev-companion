---
id: D-SKL-077
title: 'The crossing out of a review is recognised on the first edit meant to survive'
date: 2026-08-25
status: open
coveredBy:
  - SkillTest::theCrossingOutOfAReviewNamesTheEditThatBeginsTheRework
---

# D-SKL-077 — The crossing out of a review is recognised on the first edit meant to survive

**The crossing out of a core review names the act that begins the rework beside
the sentence that asks for it. The act is the first edit meant to survive.**

The section asks for recognition in what the reader says. A session with it in
hand reports that it noticed its own next act instead.

## Evidence

- **The sighting.**
  [`feedback/archive/2026-08-24-183420`](../../feedback/archive/2026-08-24-183420-the-review-skill-predicts-the-crossing-into.md),
  `/home/benji/projects/typo3-cms`: Gerrit change 91127 reviewed under
  `typo3-core-patch-review`. The session quotes the crossing section and states
  that it read it. Then it removed three `markTestSkipped` calls from
  `SiteRequestTest.php`, added six body assertions and a fixture page, ran five
  checks and drafted an amended commit message. It never invoked
  `typo3-core-patch-development`.
- **What the session tracked instead is the register.** It names the question it
  asked at that moment, "does this belong in this commit", and says it answered
  that one carefully. The section asks it to notice a property of the reader's
  sentence. It noticed a property of its own next act.
- **The section is today's.** Read on 2026-08-25: `db92a9ea`, 2026-08-24
  17:22:40 +0200, is the newest commit to
  [`typo3-core-patch-review`](../../skills/typo3-core-patch-review/SKILL.md),
  and the feedback arrived at 18:34:20+00:00 the same day. Nobody can read from
  here whether the copy published into that project carried `db92a9ea`.
- **The trigger sentence names no patch and no verb the section lists.** "wir
  sollten sie hier wieder mit aufnehmen", we should take them back in here. That
  was about three tests the review had just reported as skipped for a stale
  reason. The section enumerates "finish it", "fix it", "amend it", "write the
  test".
- **The one sighting where it fired names one of them.**
  [`feedback/2026-08-24-225243`](../../feedback/archive/2026-08-24-225243-both-patch-skills-handle-a-linear-task-this-one.md),
  same directory and same pair of skills. "kannst du ihn fertigstellen das er
  backgeportet werden kann?", and the session reports that it invoked the patch
  skill rather than carried on. That is "finish it", in the reader's own words.
- **The first sighting is the one the section came from.**
  [`feedback/archive/2026-08-07-132559`](../../feedback/archive/2026-08-07-132559-the-review-skill-has-no-marker-for-the-point.md):
  `ColumnMap.php`, a fixture column, a functional test, seven suites and an
  amend, all under review rules. Its account is that the reader picked a scope
  and asked for the change. That is a sentence the enumeration would reach, from
  before the enumeration existed.
- **The other direction already has its answer, and it is a read of the same
  sentence.**
  [`feedback/archive/2026-08-11-055317`](../../feedback/archive/2026-08-11-055317-the-review-skill-s-handover-rule-fired-on-a.md)
  crossed on "I think the tests should prove it". The paragraph that excludes a
  remark about a finding's weight came from it,
  [`D-SKL-022`](skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md).
  So the sentence is now read for two properties, and both failures on record
  are readings of it.
- **What the crossing carries could not arrive.**
  [`D-SKL-072`](skl-072-a-workflow-handover-names-the-calls-the-next-order-restarts-with.md)'s
  three calls hang off the crossing. So the deprecation sweep the session names
  as its concrete cost was unreachable whether or not the published copy carried
  them. It states the exemption in that clause's own terms and did not run the
  sweep.
- **The session owed the sweep, and [`base.md`](../../skills/base.md) already
  says so.** Step 5 skips only where the change touches no TYPO3 API. It reads
  which side a change falls on off the files it touches "and never off the task
  it started as". A functional test that renders the frontend is on the ordinary
  side. The session's own "probably nothing for a test file" is the reason that
  sentence stands against.
- **The act was observable, and the probe is what it is not.** The review skill
  permits a scratch probe that writes files. It says the boundary is that the
  session does not edit the patch under review and puts the tree back. That
  sentence stands in the verification section, and the feedback reports the
  crossing as having nothing that tells a probe from rework.
- **The strength is the same boundary from the other side.** The same session
  reports that the checklist's dropped-candidate rule and the severity rubric
  changed its output. A session reads both while it forms a finding, an act it
  already performed,
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
- **The corpus.** `bin/cli feedback:list` on 2026-08-25 reports 49 open, 45 of
  them from `/home/benji/projects/typo3-cms`.
  [`feedback/2026-08-25-114802`](../../feedback/archive/2026-08-25-114802-no-skill-activated-typo3-core-patch-development.md)
  is the same skill that did not fire from the client's own listing. There the
  patch emerged from a diagnosis and no sentence announced it.

## Decided

- **Step 4 of the ladder, on the trigger rather than on the paragraph.** The
  rule arrived, the session read it and quoted it, and it did not take. A
  recommendation to recognise something is the register that failed, not the
  length. The section is already the most explicit in either skill, and the
  feedback says so.
- **The crossing names the act beside the sentence.** The first edit to a file
  meant to survive is what asks the question. A session can observe it from
  inside, and it cannot observe a sentence to recognise.
- **The clause names the probe there as what the act is not**, restored and with
  no diff left, against a change meant to survive. The discriminator is in the
  skill already, so the crossing names where it stands rather than carrying a
  second copy of it.
- **The enumerated phrases stay.** One sighting has them fire on
  "fertigstellen". Their removal would trade a trigger that works on the
  sentences it names for one nothing has run.
- **Queued rather than closed on the spot.** The change is a clause in a
  published skill, which [judging.rst](../../documentation/records/judging.rst)
  reviews rather than improvises. It needs no lookup about TYPO3.
- **`normal` rather than `low`.** Two sessions seventeen days apart report this
  crossing failed. The correction that landed between them served the other
  direction, and the lever is one clause.
- **Trimmed: this entry refuses the ask to state the sweep's obligation in
  `typo3-core-patch-development`'s entry, and it comes off the feedback.**
  `base.md` step 5 answers it in the property a session reads a change by. That
  skill's own step 1 routes to `base.md`, and a second copy in one skill is the
  one that goes stale (`R-GUI-006`). `D-SKL-072` rejected the same shape.

## Assumed

- That a session can tell a probe from a change at the moment of its first edit.
  This one could, since it drafted an amended commit message, and no session has
  reported that it could not.
- That an act-shaped check fires where a sentence-shaped one did not. The
  feedback asserts it about itself, and nothing has measured a session with one
  in hand.
- That the false-positive cost stays what `D-SKL-022` measured, one turn under
  the wrong skill's rules. A check attached to every first edit reaches every
  probe, and the probe clause is the whole of what bounds it.

## Wrong if

- A session reports that the check fired on a scratch probe and it backed out.
  The discriminator does not separate the two at the moment of the act, and what
  remains is a trigger on the sentence alone.
- A session edits a file meant to survive with the act-shaped clause in front of
  it and does not cross. This skill's prose cannot hold the crossing, and
  [`D-SKL-049`](skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md)'s
  gate is what remains.
- A session crosses on the act and makes none of the three calls the crossing
  names. That is `D-SKL-072`'s own first **Wrong if**, which nothing could reach
  while the trigger was the thing that failed.
