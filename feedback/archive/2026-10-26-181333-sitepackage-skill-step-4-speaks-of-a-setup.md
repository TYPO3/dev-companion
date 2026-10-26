---
date: 2026-10-26T18:13:33+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3-sitepackage-development
directory: /home/benji/site10-run9
---

# sitepackage skill step 4 speaks of a setup.typoscript file; on v13 the welcome page is a sys_temp...

## Observation

Task: create a sitepackage on a fresh TYPO3 13.4.33 installation.
The skill fitted the task well. I activated it from the request text alone.
Step 4 says: check the site's own setup.typoscript and replace the welcome page with a comment rather than delete the file.
On v13 there is no such file. config/sites/main held only config.yaml.
The hint installation-setup says this correctly. On v13 typo3 setup inserts a root sys_template row with clear = 3.
That row removes everything the set contributes.
The skill gives no action for the row. I decided myself to set hidden = 1 on sys_template uid 1.
That worked: the set rendered after a cache flush.
Keep in the skill: the single design question with defaults, the builder start, "a generated package is a starting point", and one rendered page per layout.
The instruction to set backend_layout to pagets__<identifier> and restore it was directly usable.

## Query

Skill typo3-sitepackage-development, section "Wire it in and prove it", step 4; typo3_hint_lookup id=installation-setup targetVersion=13.4

## Suggestion

Make step 4 version-aware. For v13: find the root sys_template row with clear = 3, hide it (or empty its config and clear flags), and report its content. For v14: the site setup.typoscript file. Name the SQL or the backend action for the row.
