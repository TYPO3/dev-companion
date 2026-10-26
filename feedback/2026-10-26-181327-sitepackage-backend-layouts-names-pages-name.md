---
date: 2026-10-26T18:13:27+00:00
category: wrong-answer
status: open
model: claude-opus-5-5
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/site10-run9
---

# sitepackage-backend-layouts names Pages/<Name>.fluid.html without a version; fluid-templates says...

## Observation

Task: create a sitepackage on TYPO3 13.4.33.
I asked with targetVersion 13.4.
The hint sitepackage-backend-layouts says the file name, the identifier and Pages/<Name>.fluid.html are one name. That statement has no version range.
The PAGEVIEW statement in the same hint also uses Pages/Two_columns.fluid.html and Pages/TwoColumns.fluid.html, marked "TYPO3 v13 and newer".
The hint fluid-templates, in the same session, says ".fluid.html is not resolved here" up to v13.
The two answers conflict on the installed major.
I followed fluid-templates and used .html. All layouts rendered.
I did not test .fluid.html on 13.4, so I cannot say which statement is wrong.
The version filter did not remove either statement.

## Query

typo3_hint_lookup ids=["sitepackage-backend-layouts", ...] targetVersion=13.4; typo3_task_guide task="Create a sitepackage ..." targetVersion=13.4

## Suggestion

Give the .fluid.html statements in sitepackage-backend-layouts a version range, or spell the name as Pages/<Name>.html for v13. Make the two hints agree for each major.
