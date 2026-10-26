---
date: 2026-10-26T12:10:54+00:00
category: wrong-answer
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/site10-run
---

# Sitepackage checklist says reference lib.fluidPage, which no installed package defines

## Observation

Task: create a sitepackage with a site set, a page template and two page layouts on TYPO3 14.3.5 (Composer project, DDEV).
The task_guide checklist said: "Reach lib.fluidPage and lib.contentElement with the reference operator =< rather than copy them."
The hint sitepackage-typoscript-reference repeats it: "page.<n> =< lib.fluidPage is a reference and not a copy."
I searched vendor/typo3 for lib.fluidPage in all *.typoscript files. There was no match.
The project does not require typo3/theme-camino. I think lib.fluidPage comes from that theme.
A reference to an object that does not exist renders nothing. The hint does not say where the object comes from.
I wrote page.10 = PAGEVIEW directly. The page rendered with HTTP 200.
lib.contentElement is correct. fluid_styled_content defines it.

## Query

typo3_task_guide task="Create a new sitepackage extension for a TYPO3 14.3 composer project ..." changeType=feature targetVersion=14.3 paths=[packages/site_package, config/sites/main/config.yaml]; typo3_hint_lookup id=sitepackage-typoscript-reference targetVersion=14

## Suggestion

Say which package defines lib.fluidPage. If only theme_camino defines it, tell a sitepackage without that theme to define page.<n> = PAGEVIEW itself. Or tell it to define its own lib.fluidPage first. Keep the =< advice for lib.contentElement. Read the installation and drop the lib.fluidPage item when no active package defines it.
