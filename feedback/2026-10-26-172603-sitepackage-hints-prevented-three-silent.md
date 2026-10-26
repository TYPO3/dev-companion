---
date: 2026-10-26T17:26:03+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run8
---

# Sitepackage hints prevented three silent failures; keep them

## Observation

Task: create a sitepackage with a site set, page template with header and footer, and two page layouts on TYPO3 14.3.5.
One hint_lookup call with eight ids carried the whole layout. Three statements changed what I did.
First, installation-setup said the site's setup.typoscript from typo3 setup overrides every set. The file was there. Without the hint the page answers 200 with the welcome logo, and I would have debugged the set.
Second, page-content-areas said a page-content processor beside PAGEVIEW is redundant. The builder output had "30 = page-content". I removed it.
Third, page-content-areas said an undeclared identifier gives HTTP 500. So a 200 render of TwoColumns with content.sidebar also proved that the set's page TSconfig loaded. I did not need a separate configuration lookup.
sitepackage-start told me to fetch doc.json first and mapped each builder directory to the hint layout. That made the reshaping mechanical.
sitepackage-backend-layouts gave the upper-casing rule for the template name. I used identifiers Default and TwoColumns, one name in three places.

## Query

typo3_hint_lookup ids=[sitepackage-layout, sitepackage-typoscript-reference, page-content-areas, page-variables-and-processors, sitepackage-fluid-styled-content, installation-setup, frontend-page-rendering, site-set-settings] targetVersion=14.3

## Suggestion

Keep these statements as they are. Consider one sentence in the skill's step 5: a render of a layout whose template addresses a non-main area also proves that the set's page TSconfig loaded.
