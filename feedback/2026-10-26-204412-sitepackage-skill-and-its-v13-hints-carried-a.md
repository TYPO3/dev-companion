---
date: 2026-10-26T20:44:12+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_project_describe, typo3_hint_lookup
directory: /home/benji/site10-run10
---

# Sitepackage skill and its v13 hints carried a fresh install to two rendered layouts

## Observation

Task: create a sitepackage with set, page template with header and footer, and two page layouts, on a fresh TYPO3 13.4.33 DDEV/SQLite install.
The skill typo3-sitepackage-development activated on its description and fitted the task end to end.
These parts worked and must stay:
- The single bundled design question with defaults. The user answered "use the defaults" and no second question was needed.
- The "wire it in and prove it" order. Step 4 told me to hide the root sys_template row. The installation-setup hint said the v13 row has clear = 3 and resets the sets. The database confirmed the row. Without this, the set would not have rendered.
- The pagets__<identifier> write to prove each layout without a backend. Two requests proved both layouts. The sidebar area proved the set page TSconfig loaded.
- typo3_project_describe gave the SQLite path inside the container, the DDEV hostname and served:true. I needed no further discovery.
- sitepackage-backend-layouts: the first-letter upper-case fallback. A file default.tsconfig with identifier default rendered Pages/Default.html.
- sitepackage-templates: the Layouts/Pages and Partials subdirectory rule. I put partials under Partials/Page/ to avoid the fluid_styled_content collision.
- page-variables-and-processors: the websiteTitle fallback chain with f:variable and f:else. It worked as written. The fresh site has no websiteTitle, so the set setting showed.

## Query

Skill typo3-sitepackage-development; typo3_hint_lookup ids=[sitepackage-templates, sitepackage-backend-layouts, sitepackage-typoscript-reference, page-content-areas, page-variables-and-processors, sitepackage-fluid-styled-content, sitepackage-start, sitepackage-layout, installation-setup] targetVersion=13.4

## Suggestion

Keep the skill order, the design question, the sys_template step and the pagets__ proof as they are. Keep the identifier case-fallback and websiteTitle chain hints.
