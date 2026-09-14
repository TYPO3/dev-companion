---
id: R-SKL-018
title: 'A skill that hands over tells the session to invoke the next one'
status: held
restsOn: [D-SKL-022, D-SKL-053]
heldBy:
  - SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor
---

# R-SKL-018 — A skill that hands over tells the session to invoke the next one

**Where a skill's work ends and another skill's begins, it says to invoke that
skill by name at the point the crossing happens.**

A closing paragraph about ownership is documentation of a boundary. It competes
with everything else in the window. A session that holds exactly the handoff it
describes reads it and carries on. What fires is an instruction at the moment
the trigger occurs.

Where that moment is something the reader says, the crossing names the sentence
that fires it and the sentence that does not. An instruction to change the work
and a remark about what the work owes arrive in one register. A trigger with no
description beyond invisible fires on both.

## From

Three sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-065244`:
`typo3-core-issue-triage` activated, and the session states it read the closing
paragraph. It then wrote a full patch over roughly forty turns without a call to
`typo3-core-patch-development`. It decided the changelog obligation, the suites,
the databases, the trailers and the release branches for itself.
`feedback/2026-08-07-132559`: the same crossing out of
`typo3-core-patch-review`. That session edited `ColumnMap.php`, wrote a
functional test, ran seven suites and amended the commit while it still held "it
does not change the patch". `feedback/2026-08-07-130022` is the description
side. `typo3-core-patch-checkout` covers a rebase, and every noun around it is
about a change fetched from review. A session asked to rebase its own commit
correctly did not open it.

`feedback/2026-08-11-055317` is the second half, from the other direction. The
review crossing fired on "I think the tests should prove it". That was a reader
who reaffirmed the session's own finding that the patch adds no test. The
session invoked `typo3-core-patch-development` before it backed out
(`D-SKL-022`).

`feedback/2026-08-17-213027` is what carried this from the core skills to the
extension ones. `typo3-content-element-development` closed on an imperative that
named three successors and no moment. A session followed that workflow to
completion on a six-element sitepackage, quotes the sentence, and crossed none
of the three. There was no test at any layer, three README files by hand, and
the user audited the delivery himself afterwards. So the imperative is not what
fires a crossing, and the moment it stands at is the other half (`D-SKL-053`).

## Held by

- `SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`, which reads the
  imperative and where it stands. A crossing may not live in the paragraph that
  leaves the workflow. That the moment is the right one is not guarded, and
  neither is what does not fire it. Both are readings of the workflow rather
  than properties of the file.
