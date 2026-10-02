---
id: D-DIS-026
title: The project's instruction files belong to the project
date: 2026-10-02
status: open
coveredBy:
  - InstallerTest::theProjectsInstructionFilesStayAsTheProjectWroteThem
---

# D-DIS-026 — The project's instruction files belong to the project

**`install` and `update` write nothing into a file the project's agents read as
instructions. A block an earlier build wrote there stays as it is.**

From 2026-09-19 both commands wrote a marked block into `AGENTS.md`, `CLAUDE.md`
or `.junie/AGENTS.md`, and a rules file of its own for Antigravity. `D-SKL-033`
carries why.

## Evidence

- The maintainer decided on 2026-10-02 that this server modifies no project's
  `AGENTS.md`. The marks kept every line outside them intact, and the edit was
  still one in a file the project owns and commits.
- The block reached every session in that project, TYPO3 task or not. Its tool
  names and routes were a copy that went stale in the project on each release.

## Decided

- The block goes, and with it the column of instruction files per client in
  `Installer::AGENTS`. The Antigravity rules file goes too. It was a file of
  this package, but it was the last reason for the whole mechanism.
- What `install` writes is the client entry, the skills and the record. The
  `instructions` at initialize and the skill listing are the channels that reach
  a session before a call.
- Rejected: an `update` that removes a block it finds between its marks. That is
  a write into the same file, and the maintainer chose to leave a block there.
  `documentation/usage/installing.rst` tells a reader how to delete it by hand.

## Assumed

- That the `instructions` and the skill listing carry enough to activate a skill
  without a line in the project's own file. `D-SKL-033` records sessions where
  they did not, before the block existed.

## Wrong if

- Sessions in a project without the block again skip the skills and the tools
  for a route the project's own file prescribes. Then the channel this entry
  closed is what is missing. The answer is one the project's owner adds, and
  this server documents it rather than writes it.
- A project reports that a block left by an earlier build misroutes a session,
  because its tool names no longer exist. Then a one-time removal costs less
  than the stale block.
