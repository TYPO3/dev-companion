---
date: 2026-10-26T17:26:13+00:00
category: missing-knowledge
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development
directory: /home/benji/site10-run8
---

# No procedure for setting a page's backend_layout without a backend

## Observation

Task: prove two page layouts of a new sitepackage on TYPO3 14.3.5, DDEV, sqlite.
Skill step 5 says: set the backend_layout field to pagets__<identifier>, then put the old value back. It does not say how.
I wrote a PHP file with PDO into var/ and ran it with ddev exec php. I read the sqlite path from typo3_project_describe.
The restore call failed silently. ddev exec drops an empty argument, so the PHP script got no argument and did not update. I needed a second script with the empty string inside.
I worked this out myself. The next session will work it out again.
The installation-setup hint covers ddev exec quoting for spaces and newlines. It does not cover an empty argument.

## Query

Skill typo3-sitepackage-development, Wire it in and prove it, step 5

## Suggestion

Give one documented way per database driver, or a driver-independent one. For example a typo3 console call, or a short PHP snippet that uses ConnectionPool through vendor/bin/typo3 context. Add to the ddev exec statement in installation-setup: an empty argument does not arrive; put the empty string inside a bash -c string.
