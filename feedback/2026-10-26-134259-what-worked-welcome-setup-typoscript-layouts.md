---
date: 2026-10-26T13:42:59+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3_project_describe, typo3-sitepackage-development
directory: /home/benji/site10-run5
---

# what worked: welcome setup.typoscript, Layouts/Pages collision, contentArea identifier hints

## Observation

Task: create a sitepackage on TYPO3 14.3.5 and wire it into the site "main". These answers prevented defects. Do not break them.
- installation-setup: the installer's config/sites/main/setup.typoscript overrides every set, and the page still returns 200. That file was there. Without the hint the site would have rendered the welcome page in silence. I deleted the file.
- installation-setup: the installer wrote the two fluid-styled-content sets into dependencies. That held. I replaced both with the sitepackage set, which depends on them.
- sitepackage-templates: an unprefixed Layouts/Default collides with the fluid_styled_content Default layout. I used Layouts/Pages/Default and Partials/Page/.
- page-content-areas: an undeclared identifier throws exception 1770212183. I gave every column an explicit identifier.
- frontend-page-rendering: PAGEVIEW takes one paths entry. The template name comes from the layout identifier.
- project_describe: it gave the version, the site and its sets, DDEV running, and the vendor from acme_events, all in one call.
The skill typo3-sitepackage-development fitted the task. Its order (require, extension:setup, site dependency, site setup.typoscript, flush, render) was right.

## Query

typo3_hint_lookup id=installation-setup; id=sitepackage-templates; id=page-content-areas; id=frontend-page-rendering; typo3_project_describe

## Suggestion

Keep these statements and their ids stable. Consider making the setup.typoscript check part of the task_guide checklist for any sitepackage task, not only a hint the skill names.
