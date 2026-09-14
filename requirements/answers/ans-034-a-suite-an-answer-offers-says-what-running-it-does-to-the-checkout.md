---
id: R-ANS-034
title: 'A suite an answer offers says what running it does to the checkout'
status: held
judged: 2026-08-24
restsOn: [D-ANS-099]
heldBy:
  - HintsTest::aTypeScriptChangeIsOfferedTheSuiteThatStagesTheWorkingTree
  - HintsTest::everySuiteSaysWhatRunningItDoesToTheCheckout
---

# R-ANS-034 — A suite an answer offers says what running it does to the checkout

**Every test suite this server hands over carries what a run of it does to the
checkout, in the values a declared command already carries.**

A task told not to change files runs the checks that hand the code back as they
found it and no others (`D-EVI-003`). It reads that off `runs` on every command
`typo3_project_describe` lists, which `R-PRJ-007` puts there. Where the checks
are `runTests.sh` suites, which is every core patch, the caller had to settle
that from a read of the script.

The field is `runs` on the suite record, in the values `R-PRJ-007` gives them:
`check`, `change` and `unknown`. `git` joins them for a suite that runs git over
the working tree. The answer reads it off the suite's body in
`Build/Scripts/runTests.sh` and never runs it. The text half says it beside the
command, because that is where a caller about to paste one reads.

The name does not carry it and never will. `build` regenerates the committed
JavaScript and `lintTypescript` reads it, one word each. `checkGruntClean` is a
check by its name and runs `git add *` over the working tree.

## From

`feedback/2026-08-24-100604`, a Gerrit review on 2026-08-24 that had to leave
the tree under review untouched. The answer offered it `-s build` first. It
established by hand that the suite rewrites tracked files, and built a detached
worktree to run it in. It found `checkGruntClean` in a read of
`Build/Scripts/runTests.sh`, and never in an answer. `D-ANS-099` carries the
measurements, the three suites that run git and what the values are.
