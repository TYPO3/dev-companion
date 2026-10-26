---
date: 2026-10-26T12:11:24+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/site10-run
---

# What worked: welcome setup.typoscript override, contentArea 500, Layouts/Pages subdirectory

## Observation

Task: create a sitepackage on TYPO3 14.3.5. These answers prevented real errors. Do not break them.
1. installation-setup: typo3 setup writes config/sites/<id>/setup.typoscript, and it silently overrides every set. This project had that file. I deleted it before I tested. Without the hint, the page shows the TYPO3 logo with HTTP 200, and I would have searched the set for an error.
2. page-content-areas: an undeclared identifier in f:render.contentArea gives HTTP 500 on every page. So I gave every column an explicit identifier.
3. sitepackage-templates: put page layouts under Layouts/Pages/ so the frame does not render inside each content element.
4. frontend-page-rendering: PAGEVIEW takes one paths entry. The template name is the layout identifier without pagets__.
5. sitepackage-backend-layouts: one tsconfig file per layout in the set, imported from page.tsconfig.
6. site-sets: the site key is "dependencies", not "sets".
task_guide listed the omitted hint ids. I could call each by id. That was efficient. The first frontend request gave HTTP 200 with the new template.

## Query

typo3_task_guide (sitepackage, feature, 14.3); typo3_hint_lookup ids installation-setup, page-content-areas, frontend-page-rendering, sitepackage-fluid-styled-content

## Suggestion

Keep these statements. Consider putting the setup.typoscript warning directly in task_guide for intent "sitepackage" when project_describe sees that file beside a site config.yaml. I found it only because I asked for installation-setup by id.
