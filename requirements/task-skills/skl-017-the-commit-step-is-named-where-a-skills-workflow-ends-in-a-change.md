---
id: R-SKL-017
title: "The commit step is named where a skill's workflow ends in a change"
status: held
restsOn: [D-GUI-010, D-SKL-014]
heldBy:
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
  - SkillTest::theCommitStepIsNamedWhereASkillsWorkflowEndsInAChange
  - SkillTest::theWorkflowStepRunsInEverySession
---

# R-SKL-017 — The commit step is named where a skill's workflow ends in a change

**A published skill whose workflow ends in a change to a repository outside the
core names `typo3_commit_message_guide` with `workflow="project"`. It does so at
the point its own workflow ends.**

The argument is what the skill adds. Which repository the commit lands in is the
one thing the message itself cannot say. The core's half of the answer is of no
use to a session that commits in an extension. That half is the Forge issue, the
release branches and the trailers that go with them. So the skill that stands in
that repository is what says which it is. It states the argument rather than
leaves it to the default `D-GUI-010` set: a call site that means project says
so.

Six skills carry the step, read off each body. Backend modules, content
elements, the development installation, documentation, tests and the upgrade all
end in written files.

`typo3-extension-conformance` does not carry it. It is pure analysis. It reports
findings and hands every change to the skill that owns the area, so it has no
message to write. A commit line in a review's answer is the patch checklist
`R-GUI-006` exists to keep out of one.

The two core skills are not among them. Both name the guide already, both commit
in the core, and the argument's default is the answer there.
`workflow="project"` in either of them would drop the rules that hold a core
patch.

The step is in the skill body rather than in the order every task starts in.
Each published skill carries a copy of that order, review-only ones included. In
the two core skills it would restate what their own commit sections already say.

## From

A session in `/home/benji/projects/syntax` on 2026-08-04, told to reproduce a
frontend defect in the extension it stood in, fix it and commit it
(`feedback/2026-08-04-012644`). It had this server on stdio, all 26 tools in its
context and the nine published skills beside them. It made 37 tool calls, every
one of them Bash, Read, Edit or Write. It called none of the tools and activated
no skill. It committed from its own habits. `D-GUI-002` had waited for that run
since 2026-07-29. It counts four channels that could have carried the step: the
tool's own description, the covered topic in `knowledge/server-scope.json`,
`typo3_task_guide`, and the skills. The skills were the one channel that carried
nothing. `typo3-core-patch-development` and `typo3-core-patch-review` named the
commit guide, and none of the seven an extension author reaches for did. That is
what `D-SKL-014` decided to close.
