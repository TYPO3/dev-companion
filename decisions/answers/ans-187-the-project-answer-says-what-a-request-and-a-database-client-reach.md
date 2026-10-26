---
id: D-ANS-187
title: The project answer says what a request and a database client reach
date: 2026-10-26
status: open
coveredBy:
  - ProjectTest::aSiteWhoseBaseTheEnvironmentDoesNotServeIsSaidToBeUnreachable
  - ProjectTest::theDatabaseDriverIsTheOneTheLastConfigurationFileNames
---

# D-ANS-187 — The project answer says what a request and a database client reach

**`typo3_project_describe` marks each site whose base host the DDEV environment
does not serve, and it names the database driver the configuration files
state.**

## Evidence

- [`2026-10-26-121112-project-describe-shows`](../../feedback/archive/2026-10-26-121112-project-describe-shows-a-site-base-host-ddev.md).
  A run of `SITE-10` in a copy of a DDEV project under another name kept the
  original base. Its frontend answered "No site configuration found", and the
  session found a working request on the third attempt.
- [`2026-10-26-134307-project-describe-does-`](../../feedback/archive/2026-10-26-134307-project-describe-does-not-report-the-database.md).
  Two runs reached for `ddev mysql` on an installation that runs on SQLite. The
  answer named the DDEV project and nothing about the database.

## Decided

- **A field per site, `served`, and a sentence where it is false.** The
  hostnames were in the answer already, and the comparison is the step the
  session took by hand.
- **The driver read as text.** `settings.php` and then `additional.php`, the
  last literal winning. Both files are the installation's code, and this server
  includes none of it.

## Wrong if

- A project sets its driver through an environment variable, and a session reads
  the null as "no database".
