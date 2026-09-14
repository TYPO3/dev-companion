---
id: R-SKL-027
title: A core patch covers every point its issue lists
status: held
restsOn: [D-SKL-075]
heldBy:
  - SkillTest::aPatchCoversEveryPointItsIssueLists
---

# R-SKL-027 — A core patch covers every point its issue lists

**A core patch covers every point its issue lists, or each point it leaves gets
its own issue before the session writes the code.**

The session enumerates the points while it assesses the issue, because a comment
regularly names more of them than the subject does. It decides the split there
too, since each part needs a number of its own. The `Resolves:` trailer and the
changelog file name both take one, and neither fits a patch the session has
already written.

A dropped point instead is invisible from outside the session. The trailer
closes the issue on every point it names, and nobody reopens a closed issue. So
a point that is riskier to change is an argument to give it its own issue rather
than to leave it out.

This is
[R-SKL-016](skl-016-the-assessment-before-a-core-patch-reads-the-issue-and-the-review-server.md)
read one step further into the same answer. That one reads the notes for the
status, the relations and the maintainer's reason. The fourth thing they carry
is what the issue requires.

## From

`feedback/2026-08-24-162543` (2026-08-24), a session on Forge #106584 with the
skill active. The subject names two ViewHelpers and a comment names three. The
session read all three and shipped two, and it reported the third as a follow-up
that needs its own issue. The user corrected it, which is the only place the
rule came from.
