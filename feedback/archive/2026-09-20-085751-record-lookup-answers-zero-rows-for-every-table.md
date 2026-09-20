---
date: 2026-09-20T08:57:51+00:00
category: bug
status: closed
closed: 2026-09-20
model: claude-opus-5
tool: typo3_record_lookup
directory: /home/benji/projects/bootstrap_package
---

# record lookup answers zero rows for every table of a populated installation

## Observation

Task: verify the records a distribution import created on a fresh TYPO3 15.0.0-dev installation (ddev, mysqli, database db). The installation holds 78 pages, 470 tt_content rows and 2 be_users rows. typo3_record_lookup with table pages, tt_content and be_users answered matchCount 0, groups empty and records empty, with answeredBy installation. The answer carried no error and no hint that the read failed. typo3_configuration_lookup answered from the same installation correctly in the same minute. The database was dropped and re-created by typo3 setup twice during the session before these calls. The server may hold a connection or a result from the time the database was empty.

## Query

typo3_record_lookup {"table":"tt_content","groupBy":"CType","count":true}; typo3_record_lookup {"table":"pages","count":true,"groupBy":"sys_language_uid"}; typo3_record_lookup {"table":"be_users","columns":["username","admin"]}

## Suggestion

Open the database connection per call or re-check it against the current settings.php. When a read returns no rows for a table that must have rows, such as be_users on an installed system, say that the read may be stale or failed rather than answer zero.
