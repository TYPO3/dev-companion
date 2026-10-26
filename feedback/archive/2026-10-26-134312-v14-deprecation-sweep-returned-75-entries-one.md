---
date: 2026-10-26T13:43:12+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/site10-run5
---

# v14 deprecation sweep returned 75 entries; one applied to a new sitepackage

## Observation

Task: create a sitepackage on TYPO3 14.3.5 from builder output.
base.md tells me to sweep each declared major whole. The call returned 75 entries, about 30 KB.
One entry applied: ext_tables.php in extensions (14.3, 109438). The builder had generated that file. I deleted it.
Most entries concern backend PHP API, FormEngine, scheduler and form. A sitepackage ships none of that.
The sweep worked as described. Its cost was high for a package of YAML, TypoScript, TSconfig and Fluid.
The entry tags did not let me exclude PHP-API in the same call. tag takes one value and includes only.

## Query

typo3_changelog_lookup type=deprecation version=14 limit=200

## Suggestion

Let tag take several values, or add an exclude option. Or let the sweep take the file kinds a package ships (.typoscript, .tsconfig, .fluid.html, config.yaml, ext_*.php) and return the entries that touch them.
