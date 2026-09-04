---
date: 2026-09-03T10:58:14+00:00
category: idea
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# the warning that a suite can eat untracked test files reaches you only via test_run_guide, after ...

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it.

Early in the session typo3_task_guide gave me a testSuites array with ready-to-paste commands (CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path>) and a checks array. I used those for the rest of the session and ran roughly nineteen container suites with them.

Only much later, calling typo3_test_run_guide directly with my final paths, did I get invocation.notes[], which contains this:

  "runs: unknown is what a test suite carries, and it means nothing here reads
  what the core's own test code writes into the checkout the container mounts.
  So commit or copy out untracked work before starting one, and a new test file
  with its fixtures under a Tests/ directory is exactly that. One session lost
  four of them during a run and got them back only from a copy it had made on a
  hunch."

That describes my situation exactly, for the whole session. I had four untracked files under Tests/ — typo3/sysext/impexp/Tests/Functional/ColumnsOverridesRelationTest.php, its fixture CSV, the core fixture CSV, and at two points a throwaway probe test — and I ran suites over them repeatedly without ever committing (the repository's AGENTS.md says not to commit unless asked, so committing was not available as a mitigation). Nothing was lost, so this is a near miss rather than damage. But I was exposed for the entire session and learned of it only after the risk had passed.

The asymmetry is the finding: typo3_task_guide is what a session calls first — this server's own base.md makes it step 3 of the order — and it hands over runnable commands. typo3_test_run_guide is optional and I called it late, and only by chance. The command and the caveat about the command are in two different answers, and the one carrying the command comes first.

I would also note the caveat's own advice does not fit a core checkout under the "only commit when asked" rule the repository states. "Copy out" is the applicable half and is buried at the end of the sentence.

## Query

typo3_task_guide(task="Fix impexp export/import ignoring IRRE relations declared in TCA columnsOverrides", paths=[...impexp...], targetVersion="15", changeType="bugfix") returned testSuites[] with commands but no invocation notes. typo3_test_run_guide(paths=[...ReferenceIndex.php, tests, changelog...], targetVersion="15") returned invocation.notes[] carrying the warning — called near the end of the session.

## Suggestion

Carry the invocation notes that are about risk — not the whole notes array, just the destructive ones — into typo3_task_guide's answer wherever its testSuites array is non-empty. It is the call that hands over the command, so it is the call that owes the caveat.

Minimally: attach a short warning to any suite entry marked runs: "unknown" or "git", stated where the command is, e.g. "runs: unknown — this suite does not read what the container writes back; copy untracked files under Tests/ aside before running."

And reword the advice for a core checkout: lead with "copy untracked work aside" rather than "commit or copy out", since the core contribution rules discourage committing on a whim and a session following them cannot take the first option.
