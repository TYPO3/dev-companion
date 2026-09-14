---
id: D-GUI-022
title: The paths a brief is composed from name a subsystem rather than a diff
date: 2026-08-27
status: open
coveredBy: []
---

# D-GUI-022 — The paths a brief is composed from name a subsystem rather than a diff

**`typo3_task_guide` matches its hints against the paths and the task text. So
it can say which subsystems a change is in and never what the change did to
them.**

A review of a link swap across git hooks, docblocks, an XML reference and five
Fluid templates got four Fluid hint groups. It got the removal surface a review
of an API change owes. It reported that none of it changed a decision.
`coveredBy: []` because nothing here is a new promise: the entry records what
the judgement of that report established and what it declined to build.

## Evidence

- The feedback's own call reproduces, re-run on 2026-08-27 against this branch.
  `TaskGuide::answer` with the four paths it names, `changeType` `audit`,
  `targetVersion` `15.0`: `fluid-templates`, `fluid-viewhelpers`,
  `sitepackage-templates` and `system-extension-boundaries` come back, and
  `omittedHints` names five more. The brief is larger than the one reported. It
  now also counts as "Documenting a package for its readers" and carries that
  intent's six checklist items. The task text's only documentation word is
  "documentation URLs".
- `Hints::find($group['paths'], $task, …)` matches the hints. The `.fluid.html`
  path is what pulled the three Fluid groups; the task text names no template.
  So a filter on them takes a read of what the change did to that file.
  `TaskGuide::answersFrom()` is `Source::Knowledge` alone: the tool opens no
  checkout.
- The removal items are `D-GUI-004`'s and it priced the alternative already. An
  intent keyed on the task text cannot fire, because a review's task text never
  says what the diff takes away. This report is the first from the other side of
  that boundary: the surface on a diff that removes nothing.
- `TestRunGuide::answer`, run again with those paths and a unit test beside
  them, narrows to `php` and `fluid`. It withholds `typoscript`, `xliff`,
  `docs`, `typescript` and `css`, 13 suites.
  `typo3/sysext/backend/Resources/Private/tsref.xml` lands in `core` and reaches
  no domain; `Build/git-hooks/commit-msg` lands in `uncertain`. The answer names
  neither as a path no suite covers, which is what the session concluded by
  hand.
- The podman claim is one sentence's wording. `Build/Scripts/runTests.sh` probes
  on all four covered branches, identical blocks in `.checkouts/12.4`, `13.4`,
  `14.3` and `main`. Podman where `type podman` succeeds, docker otherwise, and
  `-b` overrides. The script's own `-h` calls podman the default, which is where
  the sentence came from. `printSummary` prints `Container runtime: <bin>`,
  which is the line the session read.
- No second session asks for either lever. `bin/cli feedback:list` on 2026-08-27
  reports 18 open in 4 directories. Neither they nor the archive hold another
  report of a brief whose items did not bear on a mechanical change.

## Decided

- The podman sentence gets its correction in the same commit, in
  `knowledge/test-suite-hints.json`. The script probes the runtime rather than
  defaults it, and the summary line is what a run used. It is a wording fix on a
  reading this run made, which is `D-FBK-052`.
- Letting the paths suppress hints is not built. It asks the brief for a
  property of the diff, and the brief has the path and the task text. A path
  names a subsystem, and every change to that subsystem looks the same from
  here.
- So the only slot in the input that can say a change is mechanical is
  `changeType`, which is what the feedback's second suggestion reaches for. That
  makes the two suggestions one lever rather than two.
- That lever sits in the queue and does not get taken on. It is a value on a
  declared input enum and a checklist arm beside the four of `D-GUI-008` and
  `D-SKL-065`. The corpus behind it is one session. The bar
  `documentation/records/judging.rst` sets for a new shape is two from different
  task shapes.
- What the card carries first is the other half: a `typo3_test_run_guide` that
  names the paths that reached no suite. That is `R-GUI-012`'s rule and
  `D-ANS-074`'s one level down, an answer that narrows says what it left out. It
  applies to paths where both applied it to hints and domains. It touches `src/`
  and the declared `outputSchema`, so it goes to the queue rather than happens
  here.
- The card leaves `low`. One session, but the first half is precise and rests on
  a rule this repository already wrote twice, which is what `normal` says.

## Assumed

- That a caller who knows the change is mechanical will state it. Nothing in the
  four paths of this call says so, and the session that filed the report
  classified its work as `audit`. That is correct, since it was a review.
- That the removal surface costs a reader of the wrong brief a read rather than
  a wrong finding. This report says it changed no decision; it does not say it
  sent the session anywhere.

## Wrong if

- A second session reports a brief whose items did not bear on a mechanical
  change. The change type is then the thing itself rather than a suggestion, and
  the card's second step is what to build.
- A session passes a `changeType` for a mechanical sweep and the brief is still
  the subsystem's. The lever was then the hints and not the checklist, and what
  `D-GUI-007`'s `HINTS_PER_GROUP` bounds is the size and not the fit.
- `typo3_test_run_guide` names the uncovered paths and a session still reports
  reading coverage into the silence. Naming them was then not the lever, and
  what to look at is where in the answer the sentence sits.

## Since then

**Built on 2026-08-27**, the first half. `typo3_test_run_guide` names the paths
that reached no suite, as `uncoveredPaths` in the answer and as a sentence
beside the narrow one. It stands before the suites rather than after them. The
call this entry rests on says: "No runTests.sh suite covers
Build/git-hooks/commit-msg and
typo3/sysext/backend/Resources/Private/tsref.xml." The promise is
[`R-ANS-036`](../../requirements/answers/ans-036-a-suite-list-names-the-paths-no-suite-covers.md).

The second half is where it was. `bin/cli feedback:list` on this branch that day
reports 15 open in 4 directories. None is a second report of a brief whose items
did not bear on a mechanical change. So `changeType` stays in the queue, and the
feedback shrinks to that half.
