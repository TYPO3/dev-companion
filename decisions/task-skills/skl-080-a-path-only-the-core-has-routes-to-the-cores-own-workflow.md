---
id: D-SKL-080
title: 'A path only the core has routes to the core''s own workflow'
date: 2026-08-27
status: open
coveredBy:
  - ScopeTest::aPathInTheCoresOwnBuildSetupRoutesTheCoresOwnWorkflow
---

# D-SKL-080 — A path only the core has routes to the core's own workflow

**`typo3_task_guide` names the core's own skill where every path in the call is
one only the core repository has. `Build/Scripts/` and `Build/Sources/` count as
much as `typo3/sysext/`.**

A session at work in `Build/Scripts/` gets `scope: "core"`, a checklist marked
core-only, and a route to the workflow for extensions.

## Evidence

- **The session.** `/home/benji/projects/typo3-cms` on 2026-08-25,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-25-114802`](../../feedback/archive/2026-08-25-114802-no-skill-activated-typo3-core-patch-development.md).
  It opened as a diagnostic question about `runTests.sh -s cglGit` in a git
  worktree. It ended in a core patch with a Forge issue and a `Releases:`
  trailer. The session invoked no skill at any point. All three files it changed
  are in
  `Build/Scripts/`.
- **The route names the extension workflow on a core answer.** Measured through
  `TaskGuide::answer()` in this worktree on 2026-08-27. "Add a test for the
  suite dispatcher" with `paths: ["Build/Scripts/runTests.sh"]` answers
  `scope: "core"`, matches the `tests` intent and names
  `typo3-extension-testing`. The same brief with
  `typo3/sysext/core/Classes/Utility/GeneralUtility.php` names
  `typo3-core-patch-development`. The session's own two scripts and its own
  words match `coding-standards` and name the extension workflow as well.
- **The intents are not what is wrong.** `coding-standards`, `tests` and
  `browser-tests` each carry `skillCore: typo3-core-patch-development` in
  `knowledge/task-intents.json`. `TaskGuide::answer()` reads `skillCore` only
  where `Scope::isCoreWork()` is true, and the one path in `Scope::CORE_WORK` is
  `typo3/sysext/`.
- **The same class knows better everywhere else.** `Scope::CORE_LAYOUT` places
  `build/scripts/` and `build/sources/` in `Scope::Core`, which is where the
  answer's own `scope` comes from. `Scope::CORE_ONLY_ARTIFACTS` names
  `build/scripts/` and `runtests.sh` as what exists in no other repository,
  which is what marks the checklist lines. One answer therefore says the core
  three times and routes elsewhere once.
- **The gate got wider once before, for the same failure one signal over.** The
  comment above `CORE_WORK` names
  [`D-SKL-023`](skl-023-a-skill-no-intent-names-is-one-the-brief-cannot-route-to.md).
  There a triage in a checkout `typo3_project_describe` had just reported as
  `core-checkout` got the extension side of every intent it matched.
  `core issue`, `core bug`, `core checkout` and their neighbours are what that
  added.
- **The workflow named by the repair owns the work.**
  [`typo3-core-patch-development`](../../skills/typo3-core-patch-development/SKILL.md)
  routes `typo3_test_run_guide` and `typo3_script_lookup` for the suites and the
  scripts, and owns the commit message, the Forge issue and the push. A change
  to the core's own build tooling is a core patch like any other. That is what
  the session's own commit turned out to be.
- **The report's own suggestion is already decided against.** It proposes
  trigger words on the two core patch descriptions for a fix that is already in
  the work tree.
  [`D-SKL-033`](skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md)
  weighed the wording and declined more request shapes, and
  [`D-SKL-062`](skl-062-the-workflow-question-is-asked-again-on-a-new-subject.md)
  declined mid-task entry points in descriptions and placed the moment in the
  `instructions` instead.
- **That moment answers with nothing on this session's own sub-steps.** The
  re-ask sends a session to `typo3_task_guide` at the first commit the task did
  not name. Measured the same way. "write the commit message for a core bugfix"
  with the same path matches no intent and names no skill. "commit and push this
  core fix to Gerrit" matches `submission`, which routes to no skill because it
  spans two (`D-SKL-013`).

## Decided

- **`Scope::isCoreWork()` answers true for a path the same class already places
  in the core's own layout.** Which of two shapes it takes is the todo's first
  step. Either `build/scripts/` and `build/sources/` join `CORE_WORK`, or the
  gate reads the scopes `TaskGuide` has already computed for the call. The
  second matches no path a second time.
- **Queued rather than made here**, because the gate is `src/` and every tool
  that places work reads it. `documentation/records/judging.rst` is what keeps
  that off the spot.
- **At `normal`.** A measure showed the route wrong rather than a report. The
  answer it spoils is the one route this server has into a workflow
  (`D-SKL-013`).
- **Against the report's trigger words**, on the two entries above rather than
  on a read of its own.
- **`coveredBy` is empty because the test comes with the repair.** The card
  names the assertion, and until it lands nothing here would catch a move of the
  route.
- **Against a tail on `typo3_commit_message_guide` naming the two core patch
  workflows.** The moment is real, since the session called that tool at the
  turn it names as the review workflow's own. The route is upstream of it.
  `bin/cli tools:measure` in this worktree on 2026-08-27 reads the tool at 871
  bytes of text over two calls, the smallest recorded here. The Gerrit tail cost
  721 bytes per answer (`D-SKL-038`), so a tail of that shape is most of the
  tool.

## Assumed

- That the session received the re-ask clause. It landed on `main` at 2026-08-24
  21:39 and the session ran the next morning. Nobody can read from here which
  revision the checkout behind that project's `.mcp.json` stood on.
- That a brief written mid-task carries its paths. The measurements above pass
  them, and a session that asks again at an act writes its own call. Where it
  names only words, the gate reads the task text and `Build/Scripts/` never
  reaches it.
- That a session given the right workflow name loads it, which is `D-SKL-013`'s
  assumption unchanged.

## Wrong if

- A session reports `typo3_task_guide` called with a `Build/Scripts/` path,
  answered with `typo3-core-patch-development`, and the skill left shut. Then
  the route was not the obstacle and what remains is the activation, which
  `D-SKL-033` stops at.
- A session routed there reports the patch workflow as the wrong owner for a
  change to the core's own build tooling. Then that tooling wants an owner of
  its own rather than the workflow that ends in Gerrit.
- A session reports the re-ask made at the commit act and answered with no
  skill. Then the route has its repair and the moment still lands on nothing,
  and the tail declined above is what remains.

## Since then

The repair is the second of the two shapes, and the first is what settled it.
`Scope::isCoreWork()` reads each path through the layout the same class already
applies, guard included, rather than takes the two directories into the marker
list. The gate matches the markers against the paths and the task text folded
into one string. The manifest at the root and the path itself both go there. So
a sitepackage brief that writes the directory in prose clears the gate too.
`CORE_ONLY_ARTIFACTS` stays as it is. It answers whether a caller can follow a
line outside the core. The one file that writes the directory carries the script
and the core checkout anyway.
