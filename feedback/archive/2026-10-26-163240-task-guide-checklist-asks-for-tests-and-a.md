---
date: 2026-10-26T16:32:40+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_changelog_lookup
directory: /home/benji/site10-run7
---

# task_guide checklist asks for tests and a target branch for a project sitepackage with neither

## Observation

Task: create a sitepackage in a composer project. The directory is not a git repository. composer.json declares no scripts and no test harness.
typo3_task_guide (changeType feature) returned "Confirm the target branch and the issue context", "Add or update the narrowest useful test coverage", "Run targeted tests first" and "Cover the new behaviour with functional tests".
typo3_project_describe had already reported commands: [] for this project. I ignored these four items.
Separately, typo3_changelog_lookup type=deprecation version=14 with tags [TypoScript, TSConfig, Fluid, YAML, Frontend] returned 16 entries. Only two touched a sitepackage (107057 auto-render assets sections, 100887 useNonce). The rest were ext:form, ext:extbase and password generator entries, matched through the Frontend tag. That cost was small. The answer was correct.

## Query

typo3_task_guide task="Create a sitepackage extension with a site set, a page template with header and footer, and two backend layouts editors can choose" paths=[packages/site_package, config/sites/main/config.yaml] targetVersion=14.3 changeType=feature

## Suggestion

Where the project declares no test command and no harness, replace the test items with "the rendered page per layout is the proof" or point to typo3-extension-testing as optional. Drop the target-branch item where no git repository exists, or say that the server cannot know.
