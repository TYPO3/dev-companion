---
id: R-DIS-025
title: 'A publication that went stale says so before the first call'
status: held
restsOn: [D-DIS-013]
heldBy:
  - InstallerRecordTest::aPublicationThatIsNoLongerTheCurrentOneSaysWhichWayItDrifted
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack
---

# R-DIS-025 — A publication that went stale says so before the first call

**The record holds what was published, not only its names, and a server started
there says before the first call where it no longer matches.**

A published skill is a copy. It reads as current whatever version wrote it, the
client loads what it finds, and a tool name that has been renamed since fails at
the call rather than at the load. Nothing on either side changes when this
package moves: the names in the record are the names in the project, and both
keep saying the same twelve words. `update` was always the answer and nothing
ever said it was due.

So the record carries a digest of what a publication would write — each skill's
own files and `skills/base.md`, which is copied into every one of them.

What the check says is said twice, because the two readers are different people.
The line on stderr names what differs and the command that fixes it, for whoever
is at a terminal. The instructions carry one short sentence, for the agent that
is about to load a skill; it is short because
[`R-ANS-013`](../answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
holds the whole assembly to what a client keeps, and the sentence it displaced
is named in
[`D-DIS-013`](../../decisions/discovery/dis-013-the-record-holds-a-digest-of-what-was-published.md).

Three things make it speak: a skills directory that no longer holds what was
published there, a digest that no longer matches, and a record written before
the digest existed. The first is not hypothetical — the published directories
ignore themselves
([`R-DIS-024`](dis-024-the-published-directories-ignore-themselves.md)), which
is also what `git clean -xdf` takes with them. A project this package never
installed into is silent, because there is nothing there it has anything to say
about.

## From

The remark that the installed skills should always be current, and that nothing
points out that an update is due (2026-08-06). The checkout it was made in had a
record naming twelve skills and no `.claude/skills` at all.

## Held by

- `ScopeTest::theInstructionsFitWhatAClientKeeps`, which measures the notice
  beside the prefix naming excluded tools, since both fire at once
