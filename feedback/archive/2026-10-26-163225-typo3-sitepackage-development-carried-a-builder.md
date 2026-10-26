---
date: 2026-10-26T16:32:25+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup
directory: /home/benji/site10-run7
---

# typo3-sitepackage-development carried a builder-based sitepackage end to end on 14.3

## Observation

Task: create a sitepackage with a site set, a page template with header and footer, and two page layouts, on TYPO3 14.3.5 under DDEV.
The skill activated from its description. It fitted the task exactly.
The order worked: design question once, builder, clean-up, Composer, extension:setup, site dependencies, setup.typoscript, render per layout.
The step "set backend_layout to pagets__<identifier> and put the old value back" let me prove both layouts on a site with one page.
The hint sitepackage-start said the builder writes PageView/ and undeclared constants. I moved and removed them before I kept them.
The hint sitepackage-templates made me put the page frame in Layouts/Pages/. That prevented the collision with the fluid_styled_content Default layout.
The hint page-content-areas said the page-content processor is redundant. The builder emits it. I removed it.
The hint installation-setup said the welcome page in config/sites/main/setup.typoscript overrides the set silently. Without it, I would have seen the TYPO3 welcome page and debugged the set.
The builder schema advice (fetch doc.json first) worked. The POST returned HTTP 200 on the first try.
All five server calls returned usable answers. I would make each of them again.

## Query

Skill typo3-sitepackage-development; typo3_hint_lookup ids=[sitepackage-layout, sitepackage-typoscript-reference, page-content-areas, page-variables-and-processors, sitepackage-fluid-styled-content, frontend-page-rendering, installation-setup, site-set-settings] targetVersion=14.3

## Suggestion

Keep the skill order, the pagets__ write step, the welcome-page warning and the builder clean-up hints. Do not shorten them.
