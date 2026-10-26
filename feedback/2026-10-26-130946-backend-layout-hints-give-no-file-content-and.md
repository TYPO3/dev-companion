---
date: 2026-10-26T13:09:46+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run2
---

# backend layout hints give no file content and no identifier-to-template casing rule

## Observation

Task: write two page layouts and their PAGEVIEW templates on TYPO3 14.3.5. The hint sitepackage-backend-layouts says one file per layout and one name in three places. It does not show what stands in the file. I had to grep vendor/typo3/cms-backend/Classes/View/BackendLayout/Grid/GridColumn.php to confirm the column keys (name, colPos, identifier, colspan). A grep for "identifier = " in core .tsconfig files found no example. The hint frontend-page-rendering says pagets__landingpage renders Pages/Landingpage. It does not say if the identifier is passed through ucfirst or must match the file name case. I read PageViewContentObject.php and still did not find the rule. I chose UpperCamelCase identifiers (Default, TwoColumns) to avoid the question. The render then proved that choice works. Both answers stopped one step short: they named the rule, but not the file content or the case rule.

## Query

typo3_hint_lookup {"id":"sitepackage-backend-layouts","targetVersion":"14.3"}; typo3_hint_lookup via task_guide "frontend-page-rendering"

## Suggestion

Add to sitepackage-backend-layouts a minimal complete BackendLayouts/<Name>.tsconfig for TYPO3 14 with title, colCount, rowCount, rows and columns that have name, colPos and identifier. State the case rule: how the identifier after pagets__ becomes the template file name, and if a lowercase identifier resolves an UpperCamelCase file.
