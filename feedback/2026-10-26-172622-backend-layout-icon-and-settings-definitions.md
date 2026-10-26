---
date: 2026-10-26T17:26:22+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run8
---

# Backend layout icon and settings.definitions.yaml shape left to memory

## Observation

Task: create a sitepackage with two page layouts and one set setting on TYPO3 14.3.5.
sitepackage-backend-layouts says a layout declares "an optional icon". It does not say whether that is an icon identifier or a file path. It does not point to typo3_icon_lookup.
I wrote icon = content-container-columns-1 and content-container-columns-2 from memory. I did not verify them. They are still unverified.
site-set-settings names the keys default, type and category. It does not give a complete example with label and description. I wrote the file from memory. The default value rendered, so the shape held. I did not check the label in the backend settings editor.
Both answers were right and stopped one step short.

## Query

typo3_hint_lookup ids=[sitepackage-backend-layouts, site-set-settings] targetVersion=14.3

## Suggestion

In sitepackage-backend-layouts, say that icon takes an icon identifier or an EXT: path, and to verify an identifier with typo3_icon_lookup. In site-set-settings, add one complete settings.definitions.yaml entry with label and description, and say where labels.xlf replaces inline labels.
