---
date: 2026-10-26T20:44:20+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup
directory: /home/benji/site10-run10
---

# No hint names what the builder's v13 fluid_styled_content package ships that is obsolete

## Observation

Task: create a sitepackage from the get.typo3.org builder, base_package fluid_styled_content, typo3_version 13.4.
The skill says to read each generated file against the hints and the deprecation sweep. The hints and the sweep did not cover most of what I removed. I judged these from my own knowledge, not from the server:
- config.typoscript sets renderCharset, uniqueLinkVars, linkVars = L, prefixLocalAnchors, index_enable, index_externals, index_metatags, X-UA-Compatible and apple-mobile-web-app-capable.
- settings.yaml writes keys such as page.meta.viewport and config.no_cache. No settings.definitions.yaml defines them. The TypoScript reads them as {$...} constants. I did not establish whether an undefined key in settings.yaml acts as a constant on v13. I removed the file.
- Configuration/RTE/Default.yaml uses CKEditor 4 options: toolbarGroups, extraPlugins justify, removePlugins image.
- The backend layout file references Icons/BackendLayouts/example.svg. The zip does not contain that file. Its title label key backend_layout.example is not in locallang_be.xlf.
- The page template contains a yellow debug box. main.js logs to the console.
- PageView/ and ContentElements/ are two template roots. The sitepackage-templates hint asks for one root. I kept the hint convention.
The deprecation sweep is silent on these because they changed many majors ago. The skill says a silent sweep is no answer, but no lookup gave the answer either.

## Query

POST https://get.typo3.org/api/v1/sitepackage/ {base_package: fluid_styled_content, typo3_version: 13.4}; typo3_changelog_lookup type=deprecation version=13 tags=[TypoScript,TSConfig,Fluid,YAML,Frontend] limit=200

## Suggestion

Add a hint, for example sitepackage-builder-output, per builder base package and TYPO3 line. List each generated file with keep, change or remove and the reason. Name the obsolete config options, the undefined settings.yaml keys, the CKEditor 4 preset and the missing icon. Say if an undefined settings.yaml key acts as a TypoScript constant on v13.
