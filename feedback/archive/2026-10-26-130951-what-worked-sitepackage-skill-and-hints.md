---
date: 2026-10-26T13:09:51+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup, typo3_task_guide
directory: /home/benji/site10-run2
---

# what worked: sitepackage skill and hints prevented three silent failures

## Observation

Task: create a sitepackage on TYPO3 14.3.5. The skill typo3-sitepackage-development fitted the task well. Its order (describe, wire in, render each layout) was correct. Keep the step "check the site's own setup.typoscript". The hint installation-setup said the installer's welcome page object is read after every set and overrides it silently with HTTP 200. Without it, the set would not have rendered and nothing would have reported it. The hint sitepackage-templates made me put header and footer partials under Partials/Page/ to avoid fluid_styled_content's Partials/Header/Header collision. The hint page-content-areas said an undeclared identifier throws HTTP 500. So I declared main and sidebar identifiers. The hint sitepackage-fluid-styled-content gave a clear cost for dropping the dependency. typo3_project_describe gave the site, its sets, the DDEV environment and the vendor in one call. task_guide listed the skill under "staleSkills" without a reason. I did not know what to do with that flag.

## Query

Session task: create sitepackage with site set, page template with header and footer, two page layouts.

## Suggestion

Keep these hints and the skill order. In typo3_task_guide, say why a skill is in staleSkills and what the agent should do about it.
