---
id: R-KNW-049
title: 'A check that can pass without reading anything says so'
status: held
restsOn: [D-KNW-036]
heldBy:
  - KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold
---

# R-KNW-049 — A check that can pass without reading anything says so

**A check whose file list comes from git carries, in the entry that offers it,
the condition under which it inspects nothing and passes anyway.**

`cglGit` and `cglHeaderGit` take the files of the last commit from
`git diff-tree`, and their script treats an empty answer as "all is well".
`runTests.sh` mounts the checkout and nothing else. So from a git worktree,
whose gitdir sits outside that mount, git fails, the list is empty, and the
suite reports SUCCESS. A session that trusts it reports a standards check as
passed after it ran none, and nothing in the output it keeps says otherwise.

The condition goes in the same entry as the command. A caller reads one entry
and gets one command. Nothing carries them from the entry that offers it to a
warning somewhere else in the corpus. Where an entry hands a check over without
room for the condition, the command is the one that holds everywhere. The
`checks` of a task intent are bare command strings. For coding standards that is
`cgl -n`.

This is the neighbour of
[`R-KNW-024`](knw-024-a-check-is-offered-only-where-the-command-exists.md).
That one keeps a check off a checkout that does not have the command. This one
keeps a check that is there and green from a read as a check that ran.

## From

A core patch session in a git worktree, with `cglGit` among the checks for the
`fluid-viewhelpers` hint. It watched it print `fatal: not a git repository`,
then `No PHP files to check, all is well`, then `SUCCESS`. It noticed only
because the fatal line was still on screen (2026-08-02).
