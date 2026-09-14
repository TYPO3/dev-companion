---
id: D-ANS-104
title: The maintained release lines are placed where a task names a branch
date: 2026-08-24
status: open
coveredBy:
  - GerritTest::theBranchesThatTakeAPatchStandBesideTheOneAChangeTargets
---

# D-ANS-104 — The maintained release lines are placed where a task names a branch

**The lines that take a patch today reach a session through the call its task
makes, and not only through the message it hands `typo3_commit_message_guide`.**

So `feedback/2026-08-24-122348` is step 2 of the ladder, queued. The answer was
here nineteen days before the session that went to `git branch -r` for it.

## Evidence

- The feedback re-run on 2026-08-24 through `CommitMessageGuide::answer()`, on
  the change it covers. With `Releases: main, 14.3, 13.4` and `workflow="core"`
  the answer is "13.4 is maintained, and a BUGFIX is released on main, 14.3. An
  older line takes a priority bug fix and a grave or security-relevant defect,
  so naming it claims the severity earns it". With the trailer left out it names
  "main, 14.3, 13.4" as the lines that can take a patch at all. It names "main,
  14.3" as where a bug fix goes.
- That is the fact the session asked for, stated better than the fact it built.
  It separates what can take a patch from where this one goes, which a count of
  remote branches cannot.
- Both halves predate the feedback. `knowledge/release-lines.json` landed on
  2026-08-05 with `D-ANS-058`, and `ReleaseLines::ordinary()`, which is what
  narrows a bug fix to two lines, on 2026-08-10. The feedback carries the stamp
  2026-08-24T12:23:48.
- The routing covers this exact task shape. The `routing` block of
  `knowledge/server-scope.json` ends the review entry with
  `typo3_commit_message_guide with workflow="core"`.
  `knowledge/task-intents.json` names it for a core patch before the push, and
  `skills/typo3-core-patch-review/SKILL.md` gives it the section *Commit shape
  and target branch*.
- None of it fired. `feedback/2026-08-24-122413`, the same session twenty-five
  seconds later, records no skill activation in the whole session and no
  `typo3_task_guide` call. That is three server calls, `typo3_gerrit_lookup`,
  `typo3_project_describe`, `typo3_forge_lookup`, and Bash after that.
- What the session did instead is in its **Query**: `git branch -r`, a listing
  of `Documentation/Changelog/`, and `Typo3Version.php`. It reached the right
  three lines, the same way the session behind `D-ANS-058` reached them with a
  count of trailers on forty commits.
- `bin/cli hints:probe` with the feedback's own subject reaches
  `extension-ter-release` and nothing else. The fact is not in the hint corpus
  at all: it is a JSON file and a class behind one tool's checks.
- This run established nothing about TYPO3. The branch facts are the ones
  `knowledge/release-lines.json` already carries, and its windows are what the
  re-run printed.

## Decided

- Step 2, delivery. Not 1a, because `release-lines.json` holds the windows and
  the guide prints them. Not 1b, because no verb is absent. The session had a
  commit message in hand and the tool that owns the trailer would have answered
  it. Not 3, because the routing entry exists and names that tool for this task
  shape. It sits behind a tool the task did not go to, which is what step 2
  describes.
- Queued rather than closed on the spot. Any carrier adds a field to an answer,
  which changes `src/` and a declared output schema.
  [judging.rst](../../documentation/records/judging.rst) puts both beyond a run
  that has read only this repository.
- Which call carries it is the todo's first step, and the session named both
  candidates itself. One is `typo3_project_describe`, the one call it did make
  and the one the `instructions` tell every session to start with. It already
  reports the installed version. The other is `typo3_gerrit_lookup`, which
  already returns the change's target branch.
- The set stays out of it.
  [`D-ANS-073`](ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md)
  is the boundary a placed answer has to keep. It states the lines and their
  windows, never which of them a change belongs on.
- The requirement comes with the placement rather than before it, because the
  demand has the carrier's shape.
  [`R-PRJ-008`](../../requirements/project/prj-008-the-project-answer-says-what-runs-it-not-only-what-it-declares.md)
  and `R-ANS-018` each stand on the answer that carries them. It is
  [`R-ANS-035`](../../requirements/answers/ans-035-an-answer-that-names-a-target-branch-names-the-lines-that-take-a-patch.md).
- The feedback shrinks rather than goes to the archive. Its first claim, that
  nothing names the maintained branches, has its answer; the placement it asks
  for in the same breath does not.
- Nothing on another branch changed. The changelog half it names,
  `feedback/2026-08-24-122249`, the `.x` folder for the oldest backport branch,
  is in hand in another worktree. The skill-activation half `2026-08-24-122413`
  has its own card in the queue. Neither `Serves:` line gained this feedback.

## Assumed

- That the session would have called `typo3_commit_message_guide` had a skill
  opened. Nothing records why a session does not call a tool. Its sibling says
  the three lookups answered so completely that the work no longer looked like a
  workflow.
- That a session which names a release branch has a commit message in hand. This
  one did, because it rewrote somebody else's trailer. A session that decides a
  changelog `.x` folder does not, and would have to invent a message to reach
  the fact.

## Wrong if

- A session with an answer that names the lines goes to `git branch -r` all the
  same. The fact would then arrive and the session would not take it, which is
  step 4 and a rewrite rather than a placement.
- Skill activation works and this observation does not recur. That would say the
  placement was never the lever and `2026-08-24-122413` owned the whole of it.
- The carrier turns out to serve more than the trailer. A site developer who
  hears that 13.4 is in regular support until 2027-12-31 gets an answer this
  judgement priced only for a core patch. The boundary would then be wider than
  the feedback that moved it.

## Since then

**2026-08-24.** The carrier is the review lookup and the placement is a field of
its answer, beside the branch every change already carried. The orientation
answer was the other candidate and this entry did not take it. It is the call
every session starts with. But its subject is the repository the server started
in, and the release calendar is a fact about TYPO3. That choice would also have
handed the core's release branches to every site developer who calls it. That is
the third **Wrong if**, taken on purpose.

**2026-08-27.** The first session to run against the placement did not reach it,
and it says why it passed over the carrier.
