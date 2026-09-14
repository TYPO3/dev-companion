---
id: D-SKL-071
title: 'A probe is put back to the state it found'
date: 2026-08-24
status: open
coveredBy:
  - SkillTest::aProbeIsPutBackToTheStateItFound
---

# D-SKL-071 — A probe is put back to the state it found

**A session puts a scratch probe back to the state it found.
`git checkout -- <path>` restores that only where the file carries nothing but
the patch set.**

The file a review most wants to probe is the file with the change, once the user
has asked for the change. There the restore reports success by a clean file, and
clean is what the loss looks like.

## Evidence

- `feedback/2026-08-24-100329` is the loss. The session reworked change 95375.
  It edited a constant in `FormManagerController.php` to prove a new functional
  test fails without the fix. It ran the suite, and restored with
  `git checkout --` on that path. That took the probe and the rework together.
  `git status` reported the file clean as the paragraph said it should. The
  session found out from a grep for its own constant that returned nothing.
- Measured in a scratch repository on 2026-08-24, three cases. Unstaged work
  plus a probe on top: `git checkout -- <path>` leaves the file at the commit.
  The same work staged with `git add` first: the restore lands on the work, and
  `git status` reports the file modified rather than clean. A `git stash push`
  of that path on the probe state: the file lands at the commit as well.
- So the stash the report offers beside the copy does not restore the state the
  probe found. That state was never recorded anywhere git can read it, and a
  `pop` brings the probe back with the work.
- The instruction is the review skill's alone. `git checkout --` stood once
  below `skills/`, in `typo3-core-patch-review/SKILL.md`. The checklist the
  report also names carries no probe procedure, so the rewrite lands in one
  place rather than two.
- The collision is at the crossing rather than inside the review. Under review
  rules the file carries the patch set and nothing else, and the sentence was
  correct there. The skill's last section hands the rework to
  `typo3-core-patch-development`, which carries no probe paragraph of its own.
  The session took this one across.
- The corpus holds 39 open feedback and this is the only one about the restore.
  The other entry on the paragraph is
  [`D-SKL-032`](skl-032-a-probe-is-worth-what-the-session-can-run.md), where the
  probe is what carried the review.

## Decided

- **Step 4, wording, closed on the spot.** The rule arrived, the session
  followed it, and it was wrong about the tree the session had. That is the same
  diagnosis
  [`D-SKL-011`](skl-011-the-call-plan-a-skill-writes-down-is-measured.md) and
  [`D-SKL-043`](skl-043-a-rule-query-carries-two-subjects.md) made of two other
  sentences in this skill. Nothing here looked up anything about TYPO3. The
  skill's contract, its `description`, what it routes to, the ownership boundary
  it closes on, stands as it was.
- The rewrite says where the restore lands rather than only what to type. The
  command reads the index, and in a review the index holds the patch set. That
  is what makes the two routes under it one rule instead of a preference between
  two commands.
- Both routes stand in the skill, because they suit different moments. Copy the
  file aside before the probe, or `git add <path>` first so the restore lands on
  the work. The measure above covers both.
- **The stash is not written down.** It reads as the git-native answer and
  restores the commit, which is the failure under repair in another command.
- The check moves from `git status` to `git diff --stat <path>`. A clean status
  is the confirmation on a file that carried nothing and the loss on a file that
  carried work. One sentence cannot mean both.
- **`typo3-core-patch-development` gains nothing.** A probe paragraph there
  would be a second copy of what this skill owns, written from one session that
  had both loaded.
- `SkillTest::aProbeIsPutBackToTheStateItFound` holds the paragraph, so a
  reorganisation cannot take it out silently.

## Assumed

- That the session lost the work the way it reports. This entry measured the
  three git cases here and not that session's tree, and nothing re-ran the
  review.

## Wrong if

- A session under `typo3-core-patch-development` alone loses work to a probe
  restore. Then the paragraph is absent where the rework happens, and what to
  fix is the delivery rather than the wording.
- A debrief reports a probe that was not run because the restore now reads as a
  hazard. The permission is worth more than the caution
  ([`D-SKL-032`](skl-032-a-probe-is-worth-what-the-session-can-run.md)), and the
  paragraph would then be too long by the sentence that saved the work.
