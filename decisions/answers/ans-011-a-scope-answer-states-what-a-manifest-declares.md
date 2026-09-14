---
id: D-ANS-011
title: 'A scope answer states what a manifest declares'
date: 2026-08-02
status: open
---

# D-ANS-011 — A scope answer states what a manifest declares

**`typo3_project_describe` and `typo3_extension_describe` each state what one
manifest declares. A comparison of two of them is the audit's work, and no tool
here judges whether they agree.**

A conformance review reports a version mismatch, and both numbers come from
answers this server already gives. What it does not give is the verdict that the
two disagree.

## Evidence

- `feedback/2026-07-31-190653`, re-run on 2026-08-02 through
  `bin/typo3-dev-companion` from `/home/benji/projects/site-new`, the directory
  it came from. `typo3_project_describe` opens with "composer-project, TYPO3
  14.3.5, PHP ^8.4". `typo3_extension_describe` with `printworks_sitepackage`
  carries "Requires: php ^8.3, typo3/cms-core ^14.3" and, on its own line,
  "Ships: manual none, readme none, tests Functional+Unit".
- Both things the feedback records as established elsewhere were in answers it
  says it already had. It lists both calls as made, then reports that it read
  composer.json for the PHP constraint and that the absent manual and README
  surprised it.
- Neither field arrived since. `requires` is in the extension answer from
  `9e06675` (2026-07-29 16:51) and `artifacts` from `fc80db8` (2026-07-31
  02:08). `main` stood at `77cd0e7` (18:42) when the report arrived at 21:06
  local. Both are ancestors of `main`, and `.mcp.json` in that project names
  `/home/benji/projects/typo3-dev-companion/bin/typo3-dev-companion`.
- What no answer states is that `^8.3` and `^8.4` disagree.
  `feedback/2026-07-31-193611` is that boundary from the other side: same
  directory, half an hour later. It compared the extension's declared constraint
  against the host's PHP 8.3.23 and reported "PHP version mismatch blocks all
  tests". The DDEV container the suite runs in makes that false.
- The comparison is already somebody's.
  `skills/typo3-extension-conformance/references/checklist.md` opens its
  surfaces with "Package: identity, Composer constraints, autoloading, extension
  metadata, and supported TYPO3/PHP range". The skill states that it owns
  assessment and prioritization.

## Decided

- This commit closes the feedback. The server as it stands and as it stood
  answers both costs it reports, so there is nothing to queue.
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes that the close answer rather than a special case.
- The strength half counts as evidence about a boundary, not as a confirmation
  of the conformance skill, which is
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md).
  `D-SKL-001` and `D-SKL-002` gain nothing.
- The boundary stays where it is. A tool that reported a disagreement between
  two declarations would judge rather than answer. `validate` is not one of the
  five verbs in [AGENTS.md](../../AGENTS.md), and the checklist surface above
  already owes that judgement.
- This entry names the runtime half and does not fill it.
  `feedback/2026-07-31-193611` asks what PHP the container runs, and it has a
  card of its own. An answer from this run would be the copy-down
  [judging.md](../../documentation/records/judging.rst) warns about, a guess
  with a read's authority.

## Assumed

- That the session called the server this checkout builds. The two commits are
  on `main` and predate the report, and nothing records what that working tree
  held at 21:06.
- That one session wrote it. The report credits the server with the PHP finding
  and reports the same finding as read from a file. That is one account rather
  than two runs, and [judging.md](../../documentation/records/judging.rst)
  declines to assess which of the two happened.

## Wrong if

- A session with both answers in hand still reports a mismatch it read out of a
  file. The two lines would then arrive and the session would not take them.
  That is step 4 of the ladder and a rewrite rather than a close.
- A recorded run of the conformance skill reaches the Package surface and
  produces no comparison of the declared ranges. The surface would then own the
  judgement in prose only.
- The judgement of `feedback/2026-07-31-193611` lands somewhere other than
  declared against effective. The pair above would then be a read of two files
  rather than a property of these answers.

## Since then

The same pair appeared a second time from the same directory 25 minutes later.
That report credits the orientation answer with the declared constraint and
reports the effective runtime as bash read it. So it compares a declaration
against a runtime rather than two declarations, and reached that on its own.
Nothing here changed: an answer from this read would be the copy-down that entry
already declines.

The first **Wrong if** arrived later: a session with the numbers in hand did not
take them, which is what it describes.
