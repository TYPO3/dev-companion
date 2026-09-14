---
id: R-SKL-012
title: 'A finding is attributed to the change under review'
status: held
restsOn: [D-SKL-007]
heldBy:
  - SkillTest::aFindingSaysWhetherThePatchIntroducedIt
---

# R-SKL-012 — A finding is attributed to the change under review

**Every finding says whether this patch introduced it. A review reads a patch
that is one of a set against the state at the end of the set.**

A finding about a line the diff only moved past sends the author to repair
something they did not change. Nothing in the report says otherwise. What the
patch did not introduce stays in the review where it blocks submission on its
own and goes to the issue tracker otherwise. What a later patch in the same set
removes is not a defect of the set. To establish that is a read of that patch
rather than of what a message promises about it.

Reachability is the other half of the same question and moves in one direction
only. What a diff shows about who reaches a path may raise a rank and never
lower one, because reachability is what a diff establishes worst.

## From

The second recorded `REVIEW-03` run attributed twice unasked, and both carried
weight. It established that the parse the patch adds already exists one line up
and is byte-identical on `origin/14.3`. That turns the finding from "the patch
parses TSConfig" into "the patch makes an already-parsed flag take effect", a
different report to the author. And it marked the empty-tab finding "cosmetic
and new" rather than inflated it. Nothing in the skill asked for either, so
nothing makes the next run do it.

`D-SKL-007` records where a session read the general form and what it rejected
with it.
