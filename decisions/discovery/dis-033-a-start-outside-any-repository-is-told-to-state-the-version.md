---
id: D-DIS-033
title: A start outside any repository is told to state the version
date: 2026-10-27
status: open
coveredBy:
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - StdioServerTest::aStartOutsideAnyRepositoryIsToldToStateTheVersion
---

# D-DIS-033 — A start outside any repository is told to state the version

**Where no directory from the start directory up to the filesystem root holds a
`.git` or a `composer.json`, the instructions open with `instructions.outside`
from `knowledge/server-scope.json`. It replaces `Installer::ABSENT` and the
opening on `typo3_project_describe`. stderr says the same and names
`TYPO3_DEV_COMPANION_ROOT`.**

## Evidence

- A start in `/` on 2026-10-27 got `Installer::ABSENT`, which tells the agent to
  run `install` there. The opening then sent it to `typo3_project_describe`,
  which answered "no repository declaring TYPO3 was found to describe". Nothing
  named `targetVersion` or `TYPO3_DEV_COMPANION_ROOT`.
- Two clients start the server outside the session's directory. A bug report
  says Claude Desktop starts every MCP server in `/`. A user-scope entry in
  `~/.codex/config.toml` serves every project, and the Codex documentation names
  no default for `cwd`. That is the second half of the **Wrong if** of
  `D-DIS-006`.
- `D-DIS-029` came from a session in the TYPO3 Explained repository, which holds
  no installation and wanted the skills. So the criterion is a repository, not a
  TYPO3 installation: that repository keeps `Installer::ABSENT`.
- The budget leaves 11 characters at the largest assembly, measured on
  2026-10-27. So the opening replaces text rather than adds a sentence.

## Decided

- A `.git` file or directory or a `composer.json` anywhere up to the root makes
  the start directory a repository. `TYPO3_DEV_COMPANION_ROOT`, an installation
  the walk-up found and a record of `install` count as one too.
- Outside, no skill refresh runs and `Installer::ABSENT` stays out. Nothing
  there records a publication to read.
- The index entry for whole procedures sends the caller to `typo3_rule_lookup`
  by topic, because a match names the `documentId` that reads it whole.
  `typo3_project_describe`, which lists the guides, cannot answer there. The
  skills note names the `typo3://skill` resources, because no `install` put a
  skill in a listing.

## Assumed

- That a home directory is no repository. A home directory that is one, for its
  dotfiles, gets `Installer::ABSENT`, which costs one needless sentence.

## Wrong if

- A session started outside any repository reports that it ran `install`, or
  opened with `typo3_project_describe`.
- A session in a repository that holds neither marker, such as a Mercurial
  checkout, reports the outside opening.
