---
date: 2026-10-26T17:26:18+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_task_guide
directory: /home/benji/site10-run8
---

# task_guide lists test and VCS steps for a project that declares no tests and no git

## Observation

Task: create a sitepackage on TYPO3 14.3.5.
typo3_project_describe returned commands: [] and the directory has no git repository.
typo3_task_guide still listed "Cover the new behaviour with functional tests, not only unit tests", "Where the project declares tests, run the targeted ones first", and a commit message step.
The first item has no condition. It contradicts the sitepackage item that says a rendered page is the proof.
I ignored these items. They cost reading time and make the checklist less trustworthy.

## Query

typo3_task_guide task="Create a sitepackage extension with site set, page template with header and footer, and two backend layouts" paths=[packages/site_package, config/sites/main/config.yaml] targetVersion=14.3 changeType=feature

## Suggestion

Read the repository's declared commands and VCS state, as project_describe does. Drop or condition the test and commit items where the project declares none. At least give the functional-test item the same "Where the project declares tests" condition.
