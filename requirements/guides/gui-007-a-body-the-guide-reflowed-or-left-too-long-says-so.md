---
id: R-GUI-007
title: 'A body the guide reflowed or left too long says so'
status: held
restsOn: [D-GUI-003]
heldBy:
  - CommitMessageGuideTest::aCheckedMessageStillSaysWhatTheWrappingJoined
  - CommitMessageTest::aBodyTheWrappingLeftAloneReportsNoReflow
  - CommitMessageTest::aLineOverTheWidthIsAnErrorForTheCoreAndAWarningOutsideIt
  - CommitMessageTest::aRunOfLinesTheWrappingJoinedIsNamed
  - CommitMessageTest::eachJoinedRunIsReportedOnItsOwn
---

# R-GUI-007 — A body the guide reflowed or left too long says so

**The checks name a body line `typo3_commit_message_guide` joins into a
paragraph, and a line it leaves over 72 characters is an `error` under
`workflow="core"`.** Under `workflow="project"` that second one stays a
`warning`, because no hook runs there.

The guide holds two rules: wrap at 72 characters, leave structure intact. Both
cannot hold for a block whose lines are long, and the caller is the only one who
can decide which to give up. So the answer says which one the draft gave up, in
the place the caller already reads.

## From

A core patch session that passed a four-line "Executed commands:" block and got
it back as one paragraph, with `no-issues-found` beside it. That happened twice
in the same session (`feedback/2026-08-02-144315`, 2026-08-02). A session
established the other half on 2026-08-03 from `Build/git-hooks/commit-msg` in
the `main` checkout. `checkForLineLength()` refuses every line of 73 characters
or more, an indented line, a fenced one or a URL among them.
