---
date: 2026-09-26T08:59:29+02:00
category: bug
status: open
model: claude-opus-5-5
tool: typo3_project_describe
directory: /var/www/html
---

# IS_DDEV_PROJECT reads any DDEV container as the described project's own

## Observation

Task: run this server for the TYPO3 manuals through DDEV instead of host PHP.

The server runs in the web container of the DDEV project dev-companion (PHP 8.4). A volume mounts the manuals directory into that container at the host path. The described project is reference-tca, which has its own DDEV project with php_version 8.5.

typo3_project_describe answered: "PHP unconstrained declared and 8.5 in DDEV, which this server is already inside". That is wrong. The server is inside the DDEV project dev-companion, not reference-tca. The PHP it runs on is 8.4.

Cause: src/Installation/Project.php sets environment.entered from getenv('IS_DDEV_PROJECT'). DDEV sets that variable in every web container. So it says "a DDEV container", not "this project's container". src/Installation/Typo3Cli.php uses the same check. So a console call for reference-tca runs in the dev-companion container on PHP 8.4, without "ddev exec" in front. The invocation fields in the answer also drop "ddev exec" for the same reason.

## Query

typo3_project_describe with no arguments, cwd /home/lina/projects/typo3/manuals/reference-tca. The server ran as: ddev exec -p dev-companion -d "$PWD" php /var/www/html/bin/typo3-dev-companion.

## Suggestion

Set entered only when DDEV_PROJECT in the environment equals the name in the described project's .ddev/config.yaml. Where the two differ, treat the server as outside that project: prefix "ddev exec -p <name>" or "ddev exec" run from the project root. Typo3Cli needs the same comparison.
