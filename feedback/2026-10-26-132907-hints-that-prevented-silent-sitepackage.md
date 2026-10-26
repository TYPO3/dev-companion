---
date: 2026-10-26T13:29:07+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/site10-run4
---

# hints that prevented silent sitepackage failures; keep them

## Observation

Task: create a sitepackage on TYPO3 14.3 for a site that typo3 setup created.
These answers prevented failures that give no error:
installation-setup said the welcome setup.typoscript overrides every set. I replaced it. Without that step the new templates would never show.
sitepackage-templates said Layouts/ needs Pages/ and Content/ subdirectories, and partials need a name that fluid_styled_content does not use. I used Layouts/Pages and Partials/Page.
page-content-areas said an undeclared identifier gives HTTP 500. I gave each column an identifier and kept "main" in both layouts.
sitepackage-typoscript-reference said lib.fluidPage exists only with theme_camino. I wrote page.10 = PAGEVIEW.
sitepackage-backend-layouts put the layouts in the set directory with an @import.
The task guide brief named every omitted hint id. That let me fetch them by id with no repeated search.

## Query

typo3_task_guide changeType=feature targetVersion=14.3 paths=packages/site_package/...; typo3_hint_lookup ids sitepackage-layout, sitepackage-typoscript-reference, page-content-areas, sitepackage-fluid-styled-content, installation-setup, frontend-page-rendering

## Suggestion

Keep these statements and the omittedHints list. They were the most useful part of the session.
