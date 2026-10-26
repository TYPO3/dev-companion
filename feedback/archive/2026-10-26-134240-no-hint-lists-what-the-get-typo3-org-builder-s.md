---
date: 2026-10-26T13:42:40+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_hint_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run5
---

# no hint lists what the get.typo3.org builder's 14.3 output gets wrong

## Observation

Task: create a sitepackage on TYPO3 14.3.5 from the builder, as the skill tells.
The sitepackage-start hint says the builder output is a starting point with many empty files. It does not say which generated files are wrong on 14.3.
I found these defects myself by reading each file:
- ext_tables.php is generated. The 14.3 changelog deprecates ext_tables.php (issue 109438).
- Templates are under Resources/Private/PageView/ with .html files and an unprefixed Layouts/Default. sitepackage-templates says Templates/ with Layouts/Pages/, and .fluid.html.
- Content element roots go to a second tree, ContentElements/. sitepackage-templates says one template root.
- The page template renders a partial over content.main.records. f:render.contentArea is the v14 form.
- The backend layout uses the identifier "default" and the title key backend_layout.example. The xlf has no such key. The icon example.svg does not exist.
- The RTE preset uses CKEditor 4 options such as toolbarGroups.
- config.typoscript sets renderCharset, linkVars = L, uniqueLinkVars and index_* options.
- settings.yaml sets page.meta.* and config.* keys that no set defines.
- main.js logs to the console. The README says only "Add some explanation here".
The skill tells me to name what I removed. It gives no list to compare with. I would do the same diff again next session.
The rest of the skill fitted the task well. Keep "fetch doc.json first": the schema accepted typo3_version 14.3 as a JSON number and returned a zip on the first request.

## Query

typo3_hint_lookup id=sitepackage-start targetVersion=14.3; builder POST /api/v1/sitepackage/ base_package=fluid_styled_content typo3_version=14.3

## Suggestion

Add a hint, for example sitepackage-builder-output, per major. It lists each file the builder writes and what to do with it on 14.3: keep, delete, move, or rewrite. Include the reason, for example the deprecation, the template root, or the missing label key. Keep it current against the builder's version.
