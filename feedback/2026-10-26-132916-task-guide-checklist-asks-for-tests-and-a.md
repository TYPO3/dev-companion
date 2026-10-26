---
date: 2026-10-26T13:29:16+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_task_guide
directory: /home/benji/site10-run4
---

# task guide checklist asks for tests and a branch in a project with neither

## Observation

Task: create a sitepackage in a Composer project on TYPO3 14.3.
typo3_project_describe reported no declared commands and the directory is not a git repository.
The task guide checklist still asked me to confirm the target branch, run targeted tests, and cover the behaviour with functional tests.
None of these applied. The checklist did not say that, so I had to decide it alone.

## Query

typo3_task_guide task="Create a sitepackage extension with site set, page template with header and footer, and two backend layouts" changeType=feature targetVersion=14.3 paths=[packages/site_package/Configuration/Sets/SitePackage/config.yaml, ..., config/sites/main/config.yaml]

## Suggestion

Leave out or qualify the test and branch items when the project declares no commands and no test layers. For a sitepackage, replace them with the render check per page layout and the browser-check guide.
