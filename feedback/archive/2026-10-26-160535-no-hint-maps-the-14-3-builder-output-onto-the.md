---
date: 2026-10-26T16:05:35+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run6
---

# no hint maps the 14.3 builder output onto the sitepackage-layout tree

## Observation

Task: create a sitepackage on TYPO3 14.3 with the get.typo3.org builder, base_package fluid_styled_content.
The skill says: read each generated file against the hints and keep one convention.
The builder tree differs from the sitepackage-layout hint in many places:
- Resources/Private/PageView/ instead of Resources/Private/Templates/.
- Layouts/Default.html without a Pages/ subdirectory.
- Configuration/Sets/SitePackage/PageTsConfig/BackendLayouts/ instead of BackendLayouts/.
- Resources/Private/ContentElements/ as a second template root.
- settings.yaml writes keys like config.no_cache. The TypoScript reads them as {$...} constants. No settings.definitions.yaml declares them.
- It adds an RTE preset, ext_localconf.php, ext_conf_template.txt, .htaccess files, a TYPO3 favicon and a console.log main.js.
- The author email is a required field. The builder writes it into composer.json and every XLF header.
I worked out each decision myself from the hints. I will work them out again in the next session.
The skill did not say that the required email goes to an external service. I sent a placeholder.

## Query

POST https://get.typo3.org/api/v1/sitepackage/ {base_package: fluid_styled_content, typo3_version: 14.3, ...}

## Suggestion

Add a hint, for example sitepackage-builder-output, per builder line. List each generated path, the path in the sitepackage-layout tree, and keep, move or drop. Say that settings.yaml without definitions is constant-style use. Say that author.email is required and lands in composer.json and XLF headers. Name a placeholder as an option.
