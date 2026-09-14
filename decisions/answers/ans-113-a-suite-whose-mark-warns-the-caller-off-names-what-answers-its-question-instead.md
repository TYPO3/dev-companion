---
id: D-ANS-113
title: 'A suite whose mark warns the caller off names what answers its question instead'
date: 2026-08-26
status: open
coveredBy:
  - HintsTest::everySuiteThatRunsGitNamesTheDocumentAnsweringItsQuestion
---

# D-ANS-113 — A suite whose mark warns the caller off names what answers its question instead

**A suite this server marks `runs: git` carries the way to the same answer that
leaves the checkout alone.**

`checkGruntClean` is the only suite that answers whether the committed
JavaScript still matches its TypeScript source. Its caveat is the last thing any
answer says about that question. Three sessions read the caveat, did not run it,
and each invented the same replacement.

## Evidence

- `feedback/2026-08-24-205223` reviewed Gerrit 95392 in the user's own checkout,
  which held 23 branches of their work. `typo3_task_guide` and
  `typo3_test_run_guide` both returned `checkGruntClean` with the `git add *`
  caveat. The session did not run it, and it reports the caveat as correct. What
  it did instead was `git show` of the committed `.js` across the change. Then a
  `difflib` match over the two minified one-line revisions that prints the
  region that differs with context. It took three attempts to get readable
  output out of a one-line 130 KB diff.
- `feedback/2026-08-25-110726` is the same review's strength report. It names
  the caveat as "the concrete disaster the server prevented". It says it
  answered the question with tokens of the minified file and a diff.
- `feedback/2026-08-25-110635` is the 14.3 backport of that same change, a
  different task on a different day. It names `feedback/2026-08-24-205223` and
  says the alternative is the same one. A throwaway worktree off the target
  branch, `-s build` inside it, then a token-level diff of the minified file
  against the branch's committed version.
- Read on 2026-08-26 in `knowledge/test-suite-hints.json` as it stands. The
  `checkGruntClean` entry carries `runs: git`. Its `whenToUse` ends "Run it in a
  checkout whose index you can throw away, and not in one holding work of your
  own. A git worktree is not the way out". Nothing in that entry and nothing in
  `invocation.notes` names another way to the answer.
- Nothing in the corpus names one either.
  `bin/cli hints:probe "verify committed JavaScript matches its TypeScript source without running a build"`
  reached `backend-typescript` and `backend-ui` on 2026-08-26. Neither says how.
  `bin/cli hints:probe "diff a minified generated file against the committed version"`
  reached `extension-repository-layout`, which is about a different subject.
- A read of `D-ANS-099`'s third **Wrong if** against this shows it unmet.
  `checkGruntClean` came back on a change it does answer for, and the caveat
  stopped a run that would have staged somebody's working tree.
- A read of `.checkouts/` on 2026-08-26 settled the feedback's second half.
  `Build/Scripts/runTests.sh` carries `PHP_VERSION="8.5"` on main and
  `PHP_VERSION="8.2"` on 13.4 and on 14.3. Main's `composer.json` has required
  `^8.5` since `195d480f44` of 2026-07-16. That is 187 commits behind main,
  which is the distance the report gives for the base it ran from. The pre-raise
  script accepts `-p 8.5`, so the option is a way past the platform check on the
  revision that hits it.

## Decided

- Step 1a, gap, for the alternative. The caveat arrived, the session read it and
  acted on it, and reported it as a save. The gap is the sentence after it,
  which is in no file here.
- The answer is a document rather than a hint. What the three sessions lacked is
  a procedure and not a statement. The caller is the one who was lost, so it is
  `knowledge/documents/` — `D-FBK-043`.
- Taken on rather than closed on the spot. What the document says about the core
  needs a check in `.checkouts/`. The run that judged this has read nothing
  about how `-s build` behaves from a worktree beyond one report.
- The two feedback are one gap, so one card carries both.
  `feedback/2026-08-25-110635` names `feedback/2026-08-24-205223` itself and
  reports the same replacement from another task. So the document covers the
  check of a committed artifact and the rebuild of one. The commit that writes
  this deletes the second card — `R-FBK-014`.
- Priority `normal`, off the `low` the card arrived at. Three sessions in two
  days from two task shapes reported it. A session that read a caveat avoided
  the one concrete harm on record, rather than anything this server could
  repeat.
- `feedback/2026-08-25-110726` keeps its own card and its own judgement. Three
  of the four saves it reports are about other answers, so it is evidence here
  and not this gap.
- The PHP half settles here rather than goes to the queue. The read is in
  `.checkouts/`, this run made it, and what it changes is a precondition string
  — `D-FBK-052`.
- That precondition states the mechanism and one measured instance. A default
  PHP version per covered branch is a number that turns on the next raise, and
  nothing would fail when it did.

## Assumed

- That the replacement is one procedure and not three. The three sessions
  differed in how they diffed: a match over the region, a split on separators, a
  token count per side. What they share is the shape rather than the command.
- That `-s build` in a throwaway worktree reproduces the branch's committed
  output. `feedback/2026-08-25-110635` reports one run on 14.3 where it did, and
  that is the claim the document has to verify rather than repeat.
- That a document is where the caller looks. A task reaches the procedure rather
  than a query, so it depends on a name from the `checkGruntClean` entry and
  from `build`'s.

## Wrong if

- A session that gets `checkGruntClean` with the alternative beside it runs the
  suite in a working checkout anyway. That would say the caveat rather than the
  dead end did the work.
- A session reads the document and still invents its own diff, which would say
  the gap is the route to it rather than the procedure.
- `-s build` from a worktree turns out not to reproduce the committed file. Then
  a throwaway clone is the only honest answer and the document says that
  instead.
- A branch drops `-p` or narrows the versions it accepts. So the way past the
  platform check named in the precondition does not exist where a caller needs
  it.
- No session ever reaches an older revision without a rebase first, which would
  say the platform check is a failure nobody meets.

## Since then

Measured in throwaway worktrees off the bare repository, one at each tip. The
build ran in both without an install, succeeded, and left the tree clean. So the
second **Assumed** holds and the third **Wrong if** does not — the procedure is
a worktree and not a clone.

One commit's hunk reverted and a second build modified the source and the one
generated file that belongs to it. The cleanliness suite ran in the same
worktree to see what its caveat is. The build succeeded, every git call failed
on the worktree's gitdir, and the suite reported failure over a clean tree.
