---
date: 2026-10-26T18:13:52+00:00
category: idea
status: open
model: claude-opus-5-5
tool: typo3_project_describe, typo3_hint_lookup, typo3_task_guide, typo3_changelog_lookup
directory: /home/benji/site10-run9
---

# what worked: project_describe facts and the v13 sys_template clear warning prevented a silent fai...

## Observation

Task: create a sitepackage on a fresh TYPO3 13.4.33 installation under DDEV with SQLite.
The session used four server calls: project_describe, then task_guide, hint_lookup (nine ids) and the changelog sweep in one parallel round.
project_describe gave the version, the running DDEV, the site with no sets, the served flag and the SQLite file path. I needed all of them later.
installation-setup said a v13 install inserts a sys_template row with clear = 3. Without it, the set would have been wiped with no error.
sitepackage-templates made me put layouts under Layouts/Pages/ and partials under Partials/Page/. That avoids the fluid_styled_content collisions.
page-variables-and-processors warned about excludeDoktypes. I left the menu default for that reason.
sitepackage-backend-layouts told me where the layout files go and about the double underscore rule.
Overlap: task_guide returned four hints that hint_lookup by ids returned again. The skill asks for both. In parallel I could not use omittedHints.
The deprecation sweep with five tags returned 19 entries. Only two touched this package: INCLUDE_TYPOSCRIPT and addPageTSConfig. The tags filter is "any", so PHP hook entries with a Frontend tag came back too.
I loaded typo3_extension_describe and typo3_rule_lookup and did not call them. No extension existed, and I did not open a browser.
I read no guide document whole. The guide list in project_describe was visible.

## Query

Task text: "Create a sitepackage for our site: the extension, its site set, a page template with header and footer, and two page layouts the editors can choose from."

## Suggestion

Keep these hints and the project_describe fields. Let the skill run task_guide first and fetch only omittedHints by id. Consider a tag mode that requires all tags, or excludes PHP-API, for a sitepackage sweep.
