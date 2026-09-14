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

**The record holds what the installer published, not only its names. A server
started there says before the first call where it no longer matches.**

A published skill is a copy. It reads as current whatever version wrote it, and
the client loads what it finds. A tool name renamed since fails at the call
rather than at the load. Nothing on either side changes when this package moves.
The names in the record are the names in the project, and both keep the same
twelve words. `update` was always the answer and nothing ever said it was due.

So the record carries a digest of what a publication writes. That is each
skill's own files and `skills/base.md`, which goes into every one of them as a
copy.

The check says what it says twice, because the two readers are different people.
The line on stderr names what differs and the command that fixes it, for whoever
is at a terminal. The instructions carry one short sentence, for the agent that
is about to load a skill. It is short because
[`R-ANS-013`](../answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
holds the whole assembly to what a client keeps.
[`D-DIS-013`](../../decisions/discovery/dis-013-the-record-holds-a-digest-of-what-was-published.md)
names the sentence it displaced.

Three things make it speak. A skills directory that no longer holds what the
installer published there. A digest that no longer matches, and a record from
before the digest existed. The first is not hypothetical. The published
directories ignore themselves
([`R-DIS-024`](dis-024-the-published-directories-ignore-themselves.md)), which
is also what `git clean -xdf` takes with them. A project this package never
installed into is silent, because there is nothing there it has anything to say
about.

## From

The remark that the installed skills should always be current, and that nothing
points out that an update is due (2026-08-06). The checkout it came from had a
record that named twelve skills and no `.claude/skills` at all.

## Held by

- `ScopeTest::theInstructionsFitWhatAClientKeeps`, which measures the notice
  beside the prefix that names excluded tools, since both fire at once
