---
date: 2026-10-26T18:13:39+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run9
---

# no hint says what the get.typo3.org 13.4 builder output contains that is broken or obsolete

## Observation

Task: create a sitepackage from the get.typo3.org builder for TYPO3 13.4.
The skill says to read each generated file against the hints. The hints did not cover what I found:
- settings.yaml sets keys like page.pageview.paths, config.no_cache and page.meta.* that no settings.definitions.yaml defines, and TypoScript reads them as {$...} constants.
- config.typoscript carries obsolete options: renderCharset, linkVars = L, uniqueLinkVars, prefixLocalAnchors, X-UA-Compatible meta.
- The backend layout uses identifier "default" and title key backend_layout.example. The XLF has no such key.
- The backend layout icon points at Resources/Public/Icons/BackendLayouts/example.svg. The zip has no such file.
- Templates live under PageView/ and ContentElements/, not one Templates/ root as sitepackage-templates recommends.
- The layout file is Layouts/Default.html without the Pages/ subdirectory that sitepackage-templates requires.
I found all of this by reading the zip myself. The deprecation sweep did not find it, because these options have no change entry.

## Query

POST https://get.typo3.org/api/v1/sitepackage/ base_package=fluid_styled_content typo3_version=13.4; typo3_hint_lookup id=sitepackage-start

## Suggestion

Add a hint, for example sitepackage-builder-output, per builder version. List the files it writes, which ones to drop, and which keys are broken or obsolete. Return it from sitepackage-start.
