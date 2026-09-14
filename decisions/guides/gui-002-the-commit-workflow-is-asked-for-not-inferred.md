---
id: D-GUI-002
title: The commit workflow is asked for, not inferred
date: 2026-07-29
status: revoked
revokedBy: D-GUI-010
---

# D-GUI-002 — The commit workflow is asked for, not inferred

**The commit workflow is an argument that defaults to `core`.**

A commit message carries no paths, and its subject text describes the change
rather than the repository it lands in.

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::of()` reads the paths and
the task text. This one does not.

## Decided

- An argument, with `core` as the default. A commit message carries no paths.
  The one thing it does carry, the subject text, describes the change, not the
  repository it lands in. An inference from it would be a guess from prose,
  which is exactly what R-SCO-001 exists to stop.

## Assumed

- The caller knows which repository they commit in. The pointer at the end of
  every answer is enough for them to find the other mode. The default is `core`
  because a drop of rules must be something the caller asked for, never
  something a typo achieves.
- `[SECURITY]` stays refused for core work. The keyword exists in the core's
  history, since the Security Team writes those commits, so its absence from the
  enum was a gap. But its acceptance for a contributor would be a wrong answer
  with worse consequences than an absent one.

## Wrong if

- Agents commit in a project repository and never pass the argument, so the hard
  `missing-issue` error stays the normal answer there. The next step would then
  be for `typo3_task_guide`, which does compute `outsideCore`, to hand the
  workflow to the commit guide. It would name it in the follow-up tool call it
  suggests.

## Since then

The **Wrong if** is two claims. The second settled on 2026-08-02. With the
argument left out in an extension repository the hard error is the answer, so
the draft is not one anybody can commit there. Whether agents leave it out is
about behaviour, and the four channels that could tell them had a read instead.
Two arrive at the moment of use, the routing entry did not name the argument,
and no published skill named the tool at all.

The run that would settle the first claim happened on 2026-08-04 and answers it
in a way this **Wrong if** did not anticipate. The session made 37 tool calls,
all of them Bash, Read, Edit or Write, and called none of the 26. So the route
decides this before the default does. That counts as a delivery failure rather
than a wrong default, and `D-SKL-014` is where the placement went.

## Revoked on 2026-08-04

The first half of the **Wrong if** is what revoked this entry, and the measure
recorded above is what settled it. Without the argument the answer in a project
repository is `Resolves: #ISSUE_NUMBER`, `Releases: RELEASE_TARGET` and a hard
`missing-issue` error. The judgement of 2026-08-04 read that as a delivery
failure rather than a wrong default, and the maintainer read it the other way.
Three audiences reach this server and one of them has a Forge issue.

What holds instead is
[`D-GUI-010`](gui-010-the-commit-workflow-defaults-to-the-repository-most-callers-are-in.md),
whose **Wrong if** is a different list. What can go wrong now is a core patch
whose absent trailer nobody names, not a project message nobody can commit. The
argument itself survives — the workflow is still asked for and still not
inferred from the subject text.
