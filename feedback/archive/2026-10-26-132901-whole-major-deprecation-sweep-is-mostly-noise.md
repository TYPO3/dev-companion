---
date: 2026-10-26T13:29:01+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3_changelog_lookup
directory: /home/benji/site10-run4
---

# whole-major deprecation sweep is mostly noise for a new YAML/TypoScript/Fluid package

## Observation

Task: create a new sitepackage on TYPO3 14.3 that ships only YAML, TypoScript, TSconfig, Fluid, XLF and CSS.
base.md step 5 and the task guide ask for one call per major with no query.
The call returned 75 entries, about 10,000 tokens.
Three entries mattered: ext_emconf.php (14.2), ext_tables.php (14.3) and auto-render assets sections (14.0).
The other entries are about PHP API, backend JavaScript, form and scheduler. The new package calls none of them.
The sweep was useful, because it stopped me from shipping ext_emconf.php. The cost was high for that result.

## Query

typo3_changelog_lookup type=deprecation version=14 limit=200

## Suggestion

For a new package, let the task guide name the tags that match the file kinds the package ships, for example Fluid, TypoScript, TSConfig, Frontend and ext:core without PHP-API. Or add a compact mode that returns only title, version and tags.
