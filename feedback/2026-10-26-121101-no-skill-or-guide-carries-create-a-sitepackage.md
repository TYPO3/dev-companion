---
date: 2026-10-26T12:11:01+00:00
category: tool-gap
status: open
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_project_describe
directory: /home/benji/site10-run
---

# No skill or guide carries "create a sitepackage" end to end; task_guide returned skills: []

## Observation

Task: create a sitepackage (extension, site set, page template with header and footer, two page layouts) on TYPO3 14.3.
The user request was: "Create a sitepackage for our site: the extension, its site set, a page template with header and footer, and two page layouts."
No typo3-* skill description matched. None mentions a new sitepackage, a page template or a page layout. So I activated no skill.
typo3_task_guide gave the intent "sitepackage" with strong confidence. It returned skills: [] and no matching guide.
project_describe listed 27 guides. None is about the creation of a sitepackage.
My client did not show the MCP resource list. I read no guide whole.
I assembled the procedure from 6 hint_lookup calls by id. Then I read 7 core source files in vendor/ to fill gaps.
The steps I worked out myself: composer.json, set config.yaml, page.tsconfig import, one tsconfig file per layout, PAGEVIEW TypoScript, layout/partials/pages tree, removal of the setup welcome file, the site dependency, composer require, extension:setup, cache flush, a frontend request.

## Query

Session task: "Create a sitepackage for our site: the extension, its site set, a page template with header and footer, and two page layouts the editors can choose from." TYPO3 14.3.5, Composer, DDEV, site "main" with fluid-styled-content sets.

## Suggestion

Add a guide, for example "project/sitepackage/starting-a-sitepackage". It should give the minimal file tree for v14 with the content of each file. Include the site dependency edit, the removal of the welcome setup.typoscript, composer require of a path package, extension:setup, and a frontend check. Or extend typo3-development-installation or a new typo3-sitepackage-development skill whose description names "sitepackage", "page template", "page layout" and "backend layout". Let task_guide name that guide when the intent is "sitepackage".
