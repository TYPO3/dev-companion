---
date: 2026-10-26T13:28:56+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/site10-run4
---

# page layout hints stop before the case of the identifier and the full TSconfig shape

## Observation

Task: write two page layouts and their PAGEVIEW templates for a sitepackage on TYPO3 14.3.
frontend-page-rendering says the template name is the identifier without pagets__. Its example lowercases "landingpage" to "Landingpage".
No hint says whether PAGEVIEW changes the case itself, or whether Fluid finds the uppercased file.
I read PageViewContentObject.php and the fluid-templates hint to settle it. PAGEVIEW passes the name unchanged. Fluid tries the name, then the name with the first character uppercased.
No hint gives a complete backend layout TSconfig block with title, colCount, rowCount, colspan, colPos and identifier.
I took that shape from Feature-104974 in vendor/typo3/cms-core/Documentation/Changelog/14.2.
sitepackage-backend-layouts said where the file goes. It did not say what must stand in it.

## Query

typo3_hint_lookup id=frontend-page-rendering targetVersion=14.3; id=sitepackage-backend-layouts targetVersion=14.3

## Suggestion

Add one statement to frontend-page-rendering: the identifier passes unchanged, and Fluid also tries the first character uppercased, so "Default" and "default" both reach Pages/Default.fluid.html. Add a minimal two-column layout block to sitepackage-backend-layouts, with identifier on every column.
