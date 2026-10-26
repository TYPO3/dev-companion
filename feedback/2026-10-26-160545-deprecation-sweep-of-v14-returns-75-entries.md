---
date: 2026-10-26T16:05:45+00:00
category: tool-gap
status: open
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/site10-run6
---

# deprecation sweep of v14 returns 75 entries; only three touch a sitepackage

## Observation

Task: deprecation sweep before writing a sitepackage on TYPO3 14.3.
The skill says: keep entries tagged TypoScript, TSConfig, Fluid, YAML or Frontend, and those about a shipped file.
The tag parameter takes one tag. So I read the whole major: 75 entries, about 20000 tokens.
Only three were relevant: ext_emconf.php (108345), ext_tables.php (109438), and auto-render assets sections (107057).
Many TypoScript- or Frontend-tagged entries were PHP-API changes, for example 109575 and 106618. A tag filter alone would still return them.

## Query

typo3_changelog_lookup type=deprecation version=14 limit=200

## Suggestion

Let tag take a list with OR. Better: add a filter for the kind of file a package ships, for example surface=[typoscript, tsconfig, fluid, yaml, extension-files], that leaves out PHP-API-only entries. Return the title, issue and tags only, without the two URLs per entry.
