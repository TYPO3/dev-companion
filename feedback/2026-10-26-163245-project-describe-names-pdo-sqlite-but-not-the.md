---
date: 2026-10-26T16:32:45+00:00
category: idea
status: open
model: claude-opus-5-5[1m]
tool: typo3_project_describe, typo3_record_lookup, typo3-sitepackage-development
directory: /home/benji/site10-run7
---

# project_describe names pdo_sqlite but not the database file the render proof writes to

## Observation

Task: prove two page layouts by writing pages.backend_layout for one request, as the skill says, without a backend.
typo3_project_describe returned database driver pdo_sqlite and source config/system/settings.php. It did not return the file path.
I read the path with php inside ddev (var/sqlite/cms-<random>.sqlite), then wrote the field with sqlite3 three times and restored it.
To list the pages I also used sqlite3. I passed over typo3_record_lookup, because the skill did not name it there and the write needed a shell anyway.
No tool says how to do that write. The skill names the field and the value, not the command.
I did not read any guide resource. My client did not show a resource list. I saw the guide ids only in the project_describe answer. I skipped any/testing/browser-check because I opened no browser.

## Query

typo3_project_describe; skill step 5 "set its backend_layout field to pagets__<identifier> for the request"

## Suggestion

Return the resolved sqlite path in project_describe. In skill step 5, name typo3_record_lookup to find a page and give the console or ddev command form for the temporary write and its restore.
