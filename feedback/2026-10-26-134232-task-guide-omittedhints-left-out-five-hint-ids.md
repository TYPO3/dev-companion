---
date: 2026-10-26T13:42:32+00:00
category: bug
status: open
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run5
---

# task_guide omittedHints left out five hint ids the sitepackage brief itself names

## Observation

Task: create a sitepackage with a site set, a page frame and two page layouts on TYPO3 14.3.5.
The brief returned seven hints: site-sets, sitepackage-start, site-set-labels-and-layouts, sitepackage-backend-layouts, tsconfig, sitepackage-templates, fluid-templates.
omittedHints listed seven other ids: frontend-page-rendering, site-set-settings, site-error-handling, initial-content-references, project-configuration-files, extension-declarative-files, extension-manifest.
Five ids were in neither list: sitepackage-layout, sitepackage-typoscript-reference, sitepackage-fluid-styled-content, page-content-areas, installation-setup.
The checklist of the same answer names three of them by id. The skill names all five.
base.md says omittedHints is the list of ids the caller still owes. Here that list was not complete.
I fetched all five by id in five calls. Each one was necessary. installation-setup and page-content-areas prevented real defects.
A caller that trusts omittedHints alone does not fetch them.
The brief also carried tsconfig, which tells a project sitepackage that a new option needs a Feature changelog entry. That statement is for core code and does not apply here.

## Query

typo3_task_guide task="Create a sitepackage extension with site set, page template with header and footer, and two backend layouts" paths=[packages/site_package/composer.json, packages/site_package/Configuration/Sets/SitePackage/config.yaml, packages/site_package/Resources/Private/Templates/Pages/Default.fluid.html, packages/site_package/Configuration/page.tsconfig, config/sites/main/config.yaml] targetVersion=14.3 changeType=feature

## Suggestion

Put every hint id the checklist or the matched skill names into hints or omittedHints. Do not leave one in neither list. Leave out core-only statements, such as the changelog entry for a TSconfig option, when the scope is extension or project.
