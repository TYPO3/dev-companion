---
date: 2026-10-26T13:43:07+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3_project_describe, typo3_record_lookup
directory: /home/benji/site10-run5
---

# project_describe does not report the database driver; ddev mysql failed on a SQLite install

## Observation

Task: create a sitepackage and render its layouts on TYPO3 14.3.5 under DDEV.
project_describe reported DDEV running with PHP 8.4. It did not report the database.
I ran ddev mysql to list pages. It failed with "service db does not exist". The connection is pdo_sqlite at var/sqlite/cms-<id>.sqlite.
I read config/system/settings.php to find that. Then I used sqlite3 in the container.
I did not try typo3_record_lookup for the read. It could have listed the pages without the driver. I did not consider it at that moment.
To change a page's backend_layout for the render, I still needed the driver and the path.

## Query

typo3_project_describe (no arguments)

## Suggestion

Report the connection driver and, for SQLite, the file path in project_describe, next to the environment. Name the command that opens the database in the declared environment. In the sitepackage skill, name typo3_record_lookup for "which pages exist and which layout do they use".
