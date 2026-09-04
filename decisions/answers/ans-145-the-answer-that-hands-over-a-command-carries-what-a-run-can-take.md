---
id: D-ANS-145
title: The answer that hands over a command carries what a run can take
date: 2026-09-04
status: open
coveredBy:
  - HintsTest::theBriefThatHandsOverASuiteCommandCarriesWhatARunCanTake
  - HintsTest::theNoteOnATestSuiteSaysToSecureUntrackedWorkFirst
---

# D-ANS-145 — The answer that hands over a command carries what a run can take

**The warning that a suite can take untracked work with it stands beside the
suite commands in `typo3_task_guide`, because that is the answer a session runs
from.**

It lived only in the optional tool, so a session read it after nineteen runs.

## Evidence

- `feedback/2026-09-03-105814`. A core bugfix session took its commands from
  `typo3_task_guide`'s `testSuites`, ran about nineteen container suites over
  four untracked files under `Tests/`, and met the warning only when it called
  `typo3_test_run_guide` near the end. Nothing was lost; the exposure lasted the
  session.
- The asymmetry is in this server's own order. `skills/base.md` makes the brief
  step 3 of every task, and `typo3_test_run_guide` is a call a session may never
  make. The brief already prints the command and the targeted form.
- The advice did not fit the checkout it was written for. It read "commit or
  copy out untracked work", and the core's own rules tell a session not to
  commit unasked, so the half that applies was the second one and it was at the
  end of the sentence.

## Decided

- Step 2 of the ladder, delivery. The rule was written, correct and unreachable
  from where the task passed.
- The sentence moves out of `invocation.notes` into `invocation.beforeYouRun`,
  so it is one sentence with a name rather than a copy in each tool. Both tools
  print it: the run guide leads its invocation block with it, and the brief puts
  it under the suite list.
- It leads with the copy. Committing is named as the other way out and as one a
  core session does not have.
- `beforeYouRun` is empty in a brief that matched no suite. A caveat about
  running something, in an answer that offered nothing to run, is what teaches a
  reader to skim the next one.

## Assumed

- That a session reads a sentence printed under the commands it is about to
  paste. Nothing measures that, and the alternative — a line per suite entry —
  repeats it four times in one answer.

## Wrong if

- A session reports losing untracked work after reading a brief that carried the
  sentence. Then the placement is not what was missing and the wording is.
- The note grows into a second list of caveats under the suites, which would
  mean the risk one was not what the brief was missing.
