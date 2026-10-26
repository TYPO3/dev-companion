---
date: 2026-10-26T13:28:51+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5
tool: typo3-sitepackage-development
directory: /home/benji/site10-run4
---

# sitepackage skill has no branch for a session without a shell

## Observation

Task: create a sitepackage with a site set, header and footer, and two page layouts on TYPO3 14.3.5 under DDEV.
The skill fitted the task well. Its order, its defaults and the rule "one name in three places" carried the work.
Keep: the default "Default" layout name, the wiring steps 1-5, and the routing to hint ids by name.
The client denied every Bash call in this session.
The skill assumes a shell in four places: the builder zip, composer require, extension:setup and the render check.
The skill does not say what to do when no shell is available.
I wrote the tree by hand and left steps 1, 2 and 5 to the user.
I also had to decide alone whether to change the site configuration before the package was installed. That change makes the frontend answer HTTP 500 until composer require runs.

## Query

Skill typo3-sitepackage-development, task "Create a sitepackage for our site: the extension, its site set, a page template with header and footer, and two page layouts", Bash denied by client permission mode.

## Suggestion

Add a short section "Where you cannot run commands". It should say: write the tree by hand, list the exact commands for the user, and state the order. It should also say whether to edit the site dependencies before the package is installed, or to hand that edit to the user with the commands.
