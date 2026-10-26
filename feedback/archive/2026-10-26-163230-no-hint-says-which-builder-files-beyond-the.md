---
date: 2026-10-26T16:32:30+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/site10-run7
---

# no hint says which builder files beyond the tree are dead on v14: config.*, RTE preset, ext_local...

## Observation

Task: create a sitepackage from the get.typo3.org builder (base_package fluid_styled_content, typo3_version 14.3).
The builder wrote TypoScript/config.typoscript with inlineStyle2TempFile, index_enable, index_externals, index_metatags, removeDefaultJS, admPanel, sendCacheHeaders and headerComment, most from constants.
It also wrote Configuration/RTE/Default.yaml, an ext_localconf.php that registers that RTE preset, an rte_ckeditor requirement, ext_conf_template.txt, Classes/ placeholders and a favicon.
The hint sitepackage-start covers the template tree and settings.yaml. It says nothing about these files.
I removed all of them from my own judgment. I did not verify which config.* keys TYPO3 14 still reads.
The skill says "read each generated file against the hints". For these files no hint exists to read them against.

## Query

typo3_hint_lookup id=sitepackage-start targetVersion=14.3; builder POST /api/v1/sitepackage/ base_package=fluid_styled_content typo3_version=14.3

## Suggestion

Extend sitepackage-start, or add a hint "sitepackage-builder-output", with one statement per builder file outside the template tree. Say which config.* keys are removed or default on v14, and whether a sitepackage needs its own RTE preset.
