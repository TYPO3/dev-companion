---
date: 2026-10-26T13:09:57+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_changelog_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run2
---

# v14 deprecation sweep returns 75 entries; few apply to a new sitepackage

## Observation

Task: create a new sitepackage on TYPO3 14.3.5. The base order requires a deprecation sweep at each declared major. One call returned 75 entries for v14. Most were PHP-API, Backend, form and scheduler changes. A sitepackage with only YAML, TypoScript, TSconfig, Fluid and XLF files touches few of them. Relevant were ext_emconf.php (108345), ext_tables.php (109438) and auto-render assets sections (107057). I read the list myself to find them. This was the largest answer of the session. I also had to guess the builder fallback: the outbound POST to get.typo3.org was denied by my client. The skill said to write the tree by hand where the builder "does not answer". That fallback worked.

## Query

typo3_changelog_lookup {"type":"deprecation","version":"14","limit":200}

## Suggestion

Let the sitepackage skill or task_guide name the tags that fit a sitepackage (Fluid, TypoScript, TSConfig, YAML, Frontend, ext:frontend, ext:fluid_styled_content), or list the few deprecations that apply to a new sitepackage on each major. A short list of "files a new v14 extension must not ship" would also settle it.
