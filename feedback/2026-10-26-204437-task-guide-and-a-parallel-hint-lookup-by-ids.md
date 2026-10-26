---
date: 2026-10-26T20:44:37+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/site10-run10
---

# task_guide and a parallel hint_lookup by ids return the same hint texts twice

## Observation

Task: new sitepackage on TYPO3 13.4.
The skill names nine hint ids to fetch in one call. base.md says to run task_guide first and to fetch only the omittedHints. I ran both in parallel to save a round trip.
task_guide returned sitepackage-start, sitepackage-backend-layouts and sitepackage-templates in full. The hint_lookup returned the same three again. fluid-templates in task_guide repeats two sentences of sitepackage-templates.
installation-setup is long. One sentence applied: the v13 root sys_template row with clear = 3. The rest is about typo3 setup options, SQLite file names and DDEV quoting. I did not run typo3 setup.
This is partly my order. But the skill text invites the parallel call, and the cost is duplicated tokens, not a wrong answer.

## Query

typo3_task_guide task="Create a sitepackage extension with site set, page template with header and footer, and two backend layouts" changeType=feature targetVersion=13.4 paths=[packages/site_package/..., config/sites/main/config.yaml]; typo3_hint_lookup ids=[9 sitepackage ids, installation-setup]

## Suggestion

Let the sitepackage skill say: call task_guide first, then fetch only its omittedHints. Or let hint_lookup accept excludeIds. Split installation-setup so the sys_template row with clear = 3 is its own hint id that the skill can name.
