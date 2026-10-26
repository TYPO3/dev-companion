---
date: 2026-10-26T16:05:29+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup, typo3_task_guide
directory: /home/benji/site10-run6
---

# sitepackage skill and its hints prevented four silent frontend failures on 14.3

## Observation

Task: create a sitepackage with a site set, a page template with header and footer, and two page layouts, on TYPO3 14.3.5 under DDEV.
The skill typo3-sitepackage-development fitted the task. Its order worked from the first call to the rendered pages.
These answers prevented errors before I wrote a file:
- installation-setup said that config/sites/main/setup.typoscript replaces the set's page object. The file was there. I removed it. Without the hint the page shows the welcome logo with HTTP 200.
- sitepackage-templates said to put the page layout in Layouts/Pages/. This prevents a clash with the Default layout of fluid_styled_content.
- page-content-areas said that an undeclared identifier gives exception 1770212183. So I kept Pages/Default on the single area main.
- sitepackage-backend-layouts said that the identifier, file and template are one name. It also said how PAGEVIEW changes the case.
- The skill told me to get the builder schema from doc.json. The POST worked on the first try.
- The design question with defaults took one user reply.
Keep these parts. Three pages rendered with HTTP 200 on the first try.

## Query

Skill typo3-sitepackage-development; typo3_hint_lookup ids=[sitepackage-layout, sitepackage-typoscript-reference, page-content-areas, page-variables-and-processors, sitepackage-fluid-styled-content, frontend-page-rendering, installation-setup, site-set-settings] targetVersion=14.3

## Suggestion

Do not shorten these hints. Keep the skill's step order, the builder schema rule and the single design question.
