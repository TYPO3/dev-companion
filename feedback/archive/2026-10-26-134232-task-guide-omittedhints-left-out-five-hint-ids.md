---
date: 2026-10-26T13:42:32+00:00
category: bug
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run5
---

# a project brief carries the tsconfig hint's core-only changelog statement

## Observation

Task: create a sitepackage with a site set, a page frame and two page layouts on TYPO3 14.3.5.
The brief carried the tsconfig hint. It told a project sitepackage that a new option needs a Feature changelog entry.
That statement is for core code and does not apply to a project.
The half of this feedback about omittedHints is answered by D-GUI-033.

## Query

typo3_task_guide task="Create a sitepackage extension with site set, page template with header and footer, and two backend layouts" paths=[packages/site_package/composer.json, packages/site_package/Configuration/Sets/SitePackage/config.yaml, packages/site_package/Resources/Private/Templates/Pages/Default.fluid.html, packages/site_package/Configuration/page.tsconfig, config/sites/main/config.yaml] targetVersion=14.3 changeType=feature

## Suggestion

Leave out core-only statements, such as the changelog entry for a TSconfig option, when the scope is extension or project.
