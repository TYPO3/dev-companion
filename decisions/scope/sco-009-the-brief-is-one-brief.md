---
id: D-SCO-009
title: 'The brief is one brief'
date: 2026-08-02
status: open
coveredBy:
  - ScopeTest::aBriefForExtensionPathsAloneKeepsNoCoreStep
  - ScopeTest::aBriefForPathsOfDifferentAudienceSaysWhichStepsAreForWhich
---

# D-SCO-009 — The brief is one brief

**`typo3_task_guide` takes the paths of the work and places each of them, and
the brief it composes stays one brief.**

The tool matches the hints per group and names them per path. The checklist, the
checks and the discovery steps are one list, and it names the paths the core's
own steps are not for.

`D-SCO-008` left this tool with one verdict because a caller asked it about one
`area`, and called that an absent parameter rather than a rule. `paths` is here.
What it does not do is answer twice.

## Evidence

- `META-03` is the prompt: an extension file and a core file in one session,
  because the bug may be in either. It reached this tool as one question and got
  one answer, while the two path tools already answered per path.
- `Scope::isCoreOnly()` already filters the checklist, the checks and the
  discovery steps entry by entry. So a mixed call lacked no second filtered
  list. It lacked the paths the filtered one is for.

## Decided

- The tool matches the hints per group and renders them per group under a
  `# For <path>` heading. That is the shape `typo3_hint_lookup` already answers
  in. The payload merges them, as it does there.
- Everything else stays one list. Where every path is outside the core the brief
  drops the checks, the core-only checklist items and the discovery steps as
  before. Where one path is in the core it keeps them, and the notice names the
  paths they are not for.
- `scope` stays and means the call as a whole. It is the one group's scope where
  there is one group. Where the paths disagree it is what the task text says on
  its own. `scopes` is the per-path answer, in the shape the two path tools
  already carry.
- The `area` counts as one of the paths, so a call that names only an area gets
  exactly the verdict it got before. (Superseded on 2026-08-03: the parameter is
  gone — see **Since then**.)

## Assumed

- A caller that named two repositories wants one brief about its session, not
  two briefs to reconcile. `D-SCO-008`'s first **Wrong if** is the same worry
  from the other side, and it is why the checklist was not split.

## Wrong if

- A caller follows a mixed call's checklist for the extension path, changelog
  file and all. They read the notice above it as prose and the list as the
  answer. The fix is then to filter the list to what every path shares and hand
  the core-only steps over separately, not to answer twice.
- Callers pass `paths` and `area` at once with different subjects, so the area
  arrives in `scopes` as a path nobody named. Then `area` has to go, which is a
  removal `AGENTS.md` does not allow of a schema. So its description would
  deprecate it first.

## Since then

The second **Wrong if** happened at its harmless end. A review passed five
`typo3/sysext/` paths and an `area` in one call. The area came back as a sixth
entry of `paths` and of `scopes` (`feedback/2026-08-03-144410`). The two
subjects agreed, so no verdict about a string nobody named filtered a step. What
breaks is that a caller can no longer read `scopes` as the files. The maintainer
answered the same day. `area` goes rather than gets a deprecation, and the tool
reads what it alone could decide off the path instead.
