---
description: >-
  Where the upstream contribution documentation lives, as links to it.
whenToUse: >-
  When a question goes past what the bundled documents answer and you have to read the official guide.
hints: []
---

# TYPO3 Contribution Sources

The official upstream documentation behind this knowledge base. This package
does not bundle the guide. Link to these URLs when a question goes past what the
local documents cover.

## Core Contribution Guide

Entry point:
https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/

- Git and Gerrit setup:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Setup/Git/Index.html
- Commit hooks:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitHook.html
- Create a patch:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/BugfixingAZ/Index.html
- Upload a new patch set:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/HandlingAPatch/ChangeAPatch.html
- Cherry-pick a patch from Gerrit:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/HandlingAPatch/CherryPick.html
- Backport a change:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/CoreMergers/Backport.html
- Commit message rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
- Git cheat sheet:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/CheatSheets/Git.html

Review system: https://review.typo3.org — Forge issues:
https://forge.typo3.org/projects/typo3cms-core

## Local Policy

- Prefer the official TYPO3 documentation for workflow rules.
- Prefer the local TYPO3 core checkout for the scripts it has, the current
  branch state, and the changed files.
- Keep a derived rule short. Link back to the source when a recommendation
  depends on the official process.
- Read the upstream documentation again when a workflow rule may have changed.
