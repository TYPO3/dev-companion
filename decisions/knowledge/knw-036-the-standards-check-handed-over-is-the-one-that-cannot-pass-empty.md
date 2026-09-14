---
id: D-KNW-036
title: The standards check handed over is the one that cannot pass empty
date: 2026-08-03
status: confirmed
coveredBy:
  - KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold
---

# D-KNW-036 — The standards check handed over is the one that cannot pass empty

**`cgl -n` is the code-standards check this server hands over. `cglGit` stays
where the entry has room to name the checkout it needs.**

`cglGit` reports SUCCESS from a git worktree after it read no file. A session
that trusted it reported a standards check as passed after it ran none.

## Evidence

- Reproduced in this repository's own `.checkouts/main`, which is a git worktree
  of `.checkouts/typo3.git`. `CI=true ./Build/Scripts/runTests.sh -s cglGit`
  prints `fatal: not a git repository: (null)`, then
  `cglFixMyCommit.sh: No PHP files to check, all is well.`. Then `SUCCESS`, and
  it exits 0. It does the same after `composerInstall`, so the green does not
  depend on an absent fixer.
- The mechanism is the mount, not the worktree. `runTests.sh` runs the container
  with `-v ${CORE_ROOT}:${CORE_ROOT}`. The worktree's `.git` is a file that
  names a gitdir outside `CORE_ROOT`, and git inside the container cannot follow
  it. The same `git diff-tree --no-commit-id --name-only -r HEAD` run on the
  host in that worktree answers with the two files of the last commit.
- `cglFixMyCommit.sh:129` and `cglFixMyCommitFileHeader.sh:129` are the same
  five lines: an empty `DETECTED_FILES` prints "all is well" and exits 0.
- The control, in the same worktree: `-s cgl -n` reported
  `Found 0 of 6271 files that can be fixed in 14.330 seconds` and SUCCESS. It
  asks git nothing — it runs php-cs-fixer over the paths its config names.
- `checkGitSubmodule` from the same worktree fails rather than passes.
  `git submodule status` prints one line of `fatal:`, the script counts lines,
  and it reports "Found a submodule definition in repository". A false red,
  which a session can see.
- `checkExtensionScannerRst`, named as a suspect in the report, does not ask git
  anything: `runTests.sh` runs `extensionScannerRstFileReferences.php`, which
  reads the files itself. `checkGruntClean`, `checkIsoDatabase` and
  `checkCharsetTables` do run git in the container and cannot work from a
  worktree either. Nothing in `knowledge/` recommends any of the three, and
  `runTests.sh` already calls the first of them CI-only.

## Decided

- The `checks` of the patch-submission intent hand over `cgl -n`. A `checks`
  entry is a bare command string with nowhere to put a condition. So what goes
  there is the command that holds from either kind of checkout.
- `cglGit` stays in `knowledge/test-suite-hints.json` and in
  `typo3-core-scripts.md`, where the entry has prose around it, and both now
  name the worktree condition beside the command. It is the faster variant and a
  session in a normal checkout should be able to reach it knowingly.
- The prose document leads with `cgl -n` rather than `cglGit`, because the first
  command in a section is the one that gets copied.
- A direct run of `Build/Scripts/cglFixMyCommit.sh`, outside the container, had
  its weight and is not what goes out. It is what `cglGit` runs, and on the host
  git resolves the worktree, so the file list is correct. In `.checkouts/main`
  it found the commit's files and got as far as php-cs-fixer. What it needs
  instead is the branch's PHP on the host. `main` pins 8.5 and the host has 8.3,
  so it stopped at Composer's platform check. That trades the worktree condition
  for a host-PHP condition, which is the condition `runTests.sh` exists to
  remove. It is the same defect as an offer of a command the caller's checkout
  does not have (`R-KNW-024`).
- The todo asked for this in the `checks` array of the hint corpus. That array
  left the corpus with `D-KNW-031`, so the recommendation lives in the two JSON
  files and the one document instead. The todo had its read against the
  repository rather than a blind run.

## Assumed

- A normal checkout is the case `cglGit` serves, and CI runs it there, so it
  costs nothing to keep. Nobody has checked what it does in a
  container-in-container CI where the mount may differ again.
- `cgl -n` over the whole tree is cheap enough to be the default. Measured at
  fourteen seconds of fixer time on `main`, against a `cglGit` run whose cost
  was never measured because it read nothing.

## Wrong if

- A session reports a standards check as passed and the review rejects the patch
  for a format defect. That would mean the false green survived somewhere this
  did not reach.
- `cgl -n` starts to report files a session did not touch. That would make the
  full-tree dry run useless as a pre-push check and put `cglGit` back on the
  table with its condition.
- A caller in a normal checkout reads the worktree condition and no longer uses
  `cglGit` at all. That would mean the qualification cost the fast path rather
  than made it safe.

## Confirmed on 2026-08-24

A core session reached for the direct invocation this entry rejected and got a
correction to the container twice. It filed four feedback in thirty-five
minutes. None of the three **Wrong if** had happened. It had run the suite in
the review and still did not know the two were one command.

What it took four round trips to establish was the identity, which the corpus
stated in a document the task never passed. That is against the rule that
nothing carries a caller from the entry that offers a command to a sentence
elsewhere. The entry says what the suite runs now. The script named for the two
file lists the suite cannot pass stays out, because that puts the host-PHP
condition back in.

## Since then

A core session established this entire result again from scratch and asked this
server nothing on the way. Its own "single answer that would have shortened this
session most" is a paraphrase of the sentence the corpus already carried. That
is not a gap: re-run with the session's own words, both tools answer with the
mount contract and the false green.

What the second run did find is a sentence of the corpus that was wrong in this
entry's area. The prose named one suite as what settles the hook's file-header
error, and that suite runs a configuration with no header rule. What kept it
there is the rule that prose may name only a declared suite, and neither header
suite had a declaration. The worktree condition now stands beside every
git-driven suite rather than the one the test happened to read.
