---
description: >-
  What a patch loses when the code moved under it, in four steps: the commits main landed on its paths, the fixes that were carried into a rewritten file, the members it removes that still have callers, and the suites covering what it rewrote rather than what it changed.
whenToUse: >-
  Before reporting that a patch rebased onto current code still holds — a change hundreds of commits behind its base, or one that rewrites, moves or deletes a class rather than editing it. What a conflicting hunk means and where to stop is typo3-core-patch-checkout's; this is what survives a rebase that raised no conflict at all.
hints:
  - core-tests
---

# Rebasing a Stale Patch

A rebase that raised no conflict is not a patch that still holds. Where a change
rewrites a class rather than editing it, every fix `main` landed in that class
since the patch's base is at risk — and git reports nothing, because the code
moved to a different file and there is no text to collide.

The four steps below are what says whether that happened. They are cheap, they
are in this order because each narrows what the next one reads, and none of them
is what a conflict list shows.

`BASE` throughout is the commit the patch set was written on — the parent of the
fetched commit, which the review server answers as part of the change.

## 1. What Main Landed on These Paths

```bash
git log --oneline BASE..origin/main -- <every path the change touches>
```

The starting point and nothing more: it does not separate a bug fix from a
refactor, and a long list is normal on a change months behind. What it gives is
the set the next step reads.

## 2. Whether a Fix Survived the Rewrite

Do this for each file the change **rewrites, moves or deletes** — not for the
ones it edits, where git already speaks up.

```bash
git log -p BASE..origin/main -- <the rewritten file>
```

Read each commit and ask one question: is what it changed still there in the
replacement? A one-line fix is the dangerous shape, because it is exactly what a
rewrite reproduces from memory and gets wrong. Take the distinctive expression
out of the diff — a cast, a rounding, a guard — and grep for it in the files the
change puts in that class's place.

Where it is gone, the rebase reverts a merged fix. That is a finding about the
patch and not a conflict to resolve.

## 3. Removed Members That Still Have Callers

A class the patch guts loses members, and `main` may have added a caller for one
of them since.

```bash
git show BASE:<the file> | grep -oP '^\s+(?:public|protected)[\w\s]*function \K\w+' | sort -u > /tmp/before
grep -oP '^\s+(?:public|protected)[\w\s]*function \K\w+' <the file> | sort -u > /tmp/after
comm -23 /tmp/before /tmp/after
```

Then grep `typo3/sysext` for each name the third command prints. Most will have
none. The one that does is the breakage, and it is regularly a test `main` added
after the patch was written.

## 4. The Suites Covering What It Rewrote

`typo3_test_run_guide` answers for the paths a change touches. A path it guts or
deletes has tests on `main` that the change never touches, and those are the
ones that fail. Ask for the suites covering the **rewritten** paths as well as
the changed ones, and run them.

A test sitting in the change's own directory can be in neither file list. That
is the case worth the extra run: it is where a removed member and a reverted fix
both show up first.

## The Committed JavaScript

Where the change touches `Build/Sources/TypeScript` together with the generated
file below `Resources/Public/JavaScript` that belongs to it, the rebase raises
the question of whether the pair is still consistent.

```bash
git log --oneline BASE..origin/main -- <the source> <the committed output>
```

Empty on both means `main` touched neither, the pair is as consistent as the
patch author left it, and no rebuild is owed. Anything else is
`typo3_rule_lookup` with
`documentId="core/contribution/committed-build-output"`, which has the
procedure. Do not reach for `checkGruntClean` here: it runs `git add` over the
whole working tree, and a tree holding a half-finished rebase is the one place
that cannot be undone cheaply.
