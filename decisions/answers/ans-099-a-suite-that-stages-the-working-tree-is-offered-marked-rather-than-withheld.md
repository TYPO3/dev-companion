---
id: D-ANS-099
title: 'A suite that stages the working tree is offered marked rather than withheld'
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::aTypeScriptChangeIsOfferedTheSuiteThatStagesTheWorkingTree
  - HintsTest::everySuiteSaysWhatRunningItDoesToTheCheckout
  - HintsTest::theNoteOnATestSuiteSaysToSecureUntrackedWorkFirst
  - HintsTest::theNoteOnConfirmingASuiteSaysWhyGreppingTheCaseLabelMisses
  - HintsTest::thePreconditionSaysWhatABareWorktreeRunsWithoutASetup
---

# D-ANS-099 — A suite that stages the working tree is offered marked rather than withheld

**`typo3_test_run_guide` marks every suite it returns by what a run does to the
checkout. It offers the ones that run git rather than leaves them out.**

A task told not to change files runs the checks that hand the code back as they
found it and no others (`D-EVI-003`). It reads that property off `runs` on every
command `typo3_project_describe` lists. Where those checks are `runTests.sh`
suites, which is every core patch, no answer here carries it. So the caller has
to settle the instruction with a read of the script.

## Evidence

- `feedback/2026-08-24-100604` reports a Gerrit review whose whole point was to
  change nothing. It got `-s build` first and worked out by hand that the suite
  regenerates the committed JavaScript. It set up a detached worktree in a
  scratchpad to run it in. `checkGruntClean` it found only through a read of
  `Build/Scripts/runTests.sh`. It says the suite's `git add *` would have staged
  an untracked `response.json` at the repository root.
- Re-run on 2026-08-24 with the feedback's own arguments — the two form-wizard
  paths, `targetVersion="15"`. Seven suites come back: `build`,
  `lintTypescript`, `unitJavascript`, `e2e`, `e2e-prepare`, `e2e-browser` and
  `npm`. Each record carries `suite`, `command`, `targeted`, `description`,
  `whenToUse`, `domains` and `versions`, and none of those says what a run does
  to the tree. `checkGruntClean` is in neither the answer nor
  `knowledge/test-suite-hints.json`.
- Nothing in the corpus says it either.
  `bin/cli hints:probe "which runTests.sh suites rewrite tracked files"` and
  `bin/cli hints:probe "checkGruntClean"` both matched nothing on 2026-08-24.
- The class is three suites wide rather than one, measured in `.checkouts/` on
  2026-08-24, main at `v14.3.0-531-g3cbdea24dd`. `checkGruntClean`,
  `checkIsoDatabase` and `checkCharsets` each end in `git add *`. The last two
  open with `git checkout -- composer.json; git checkout -- composer.lock`,
  which discards uncommitted edits to those two files before anything else runs.
  The first two are on all four covered majors, `checkCharsets` from 14.
- `build` rewrites tracked files. Its body is
  `cd Build; npm install && npm run build`, and what that writes is the
  committed JavaScript below
  `typo3/sysext/*/Resources/Public/JavaScript/`.
- The property exists one tool over. `typo3_project_describe` carries `runs` per
  command as `check`, `change` or `unknown`, read off the declared body and
  never from a run of it — `R-PRJ-007`.
- The near-miss is a second gap in the same answer. The session tried to confirm
  `-s build` with `grep -n "^ build)" Build/Scripts/runTests.sh` and got
  nothing. It came one step from a report of a correct answer as wrong. The
  label is `build*)` on 13.4, 14.3 and main, and 12.4 carries `buildCss)` and
  `buildJavascript)` instead, so that grep finds nothing on any covered branch.
  The invocation note that settles it says `runTests.sh -h` lists what the
  branch supports. It does not say that this is how a caller confirms a suite
  exists, or that a case label is a glob pattern.

## Decided

- Step 1a, gap, with the shape absent beside it. The fact is in
  `Build/Scripts/runTests.sh` and in no file here, and
  `Schema::testSuiteRecord()` has no field to put it in. In `description` it
  would be prose, which is what a caller told not to change files cannot filter
  on.
- Queued rather than taken on. The tool exists and already returns the suites.
  What changes is a field on a declared `outputSchema` and entries in
  `knowledge/test-suite-hints.json`, which get a review rather than an
  improvisation.
- Priority `normal`, off the `low` the card arrived at. The harm sets it rather
  than a second report. Three suites run git against the caller's working tree,
  and two of them discard uncommitted edits to `composer.json` and
  `composer.lock`. `skills/base.md` sends every task to run the checks it gets.
- The values are `typo3_project_describe`'s, so that a caller who reads both
  answers reads one model. That is `check`, `change` and `unknown`, in the
  senses `R-PRJ-007` gives them.
- A fourth value for the suites that run git, rather than a fold into `change`.
  What is at stake there is the working tree and the index rather than the
  sources. A review free to rewrite generated files is still not free to stage
  them. Its name belongs to the work.
- A test suite is `unknown` for the reason `R-PRJ-007` already gives. It runs
  the core's own code, and nothing in the script covers what that code writes.
- `checkGruntClean` joins what a change below `Build/Sources/TypeScript` gets
  back, with the mark. It answers whether the committed JavaScript is in sync
  with its source, which is the standing obligation of such a patch. The one
  suite that answers it withheld leaves the caller to find it in the script,
  which is what happened.
- The mark is data before it is prose. The text half says it beside the command,
  because that is where a caller about to paste one reads.
- The near miss is the same card's second step and the cheaper one. The `-h`
  note changes to say how a caller confirms a suite on a branch. It says that a
  grep for the case label misses a glob.
- What the feedback asks for third goes to the queue as something to establish
  rather than as something to write. That is that the node suites run in a bare
  worktree while the PHP ones need `-s composerInstall` there first. The report
  states it as an inference that held once.

## Assumed

- That a suite body is readable the way a declared command is. `R-PRJ-007` reads
  a manifest line. A `runTests.sh` case is a shell body assembled into
  `COMMAND=` and handed to a container. That is the same kind of read and not
  the same code.
- That a read beats a run, which is `D-EVI-003`'s own **Assumed** for the
  project half. The claim is that the sources come back as they were, not that
  the filesystem does.
- That the four values are what a caller acts on. Nothing on record yet shows a
  session that filters a suite list by such a mark. For the project commands,
  three runs that ran nothing are what `D-EVI-003` came from.

## Wrong if

- A run reports a checkout modified, or an index staged, by a suite this answer
  marked as a report only.
- No caller ever separates the fourth value from `change`. That would say the
  distinction is this repository's rather than the caller's, and `change` with a
  sentence was enough.
- `checkGruntClean` comes back on changes it cannot answer for, so that a caveat
  is most of what the suite list carries.
- A branch adds a suite whose body no read of this kind covers, a case that
  dispatches to a script outside the file. That happens so often that `unknown`
  is the common answer, and then the field says nothing where a caller needs it
  most.
- A session that greps for a suite goes wrong again with the new `-h` note. That
  would say the note is not where the session looked.

## Since then

Built on 2026-08-24. The fourth value's name is `git`, because what the three
suites have in common is the command they run and not one effect of it. Two
suites stayed out of the list. Nothing narrows to them, and a php-domain entry
would offer them to every PHP patch, which is the third **Wrong if**. An
invocation note names them instead.

A read of the first **Wrong if** against a report shows it unmet. A session lost
four untracked test files across a run of six suites, none of which carries the
mark for a report only. The mark said what it could and the loss is what
`unknown` covers. Which suite did it is not established here and the report does
not claim one.
