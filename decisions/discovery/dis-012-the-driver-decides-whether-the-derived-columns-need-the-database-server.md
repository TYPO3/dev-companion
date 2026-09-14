---
id: D-DIS-012
title: The driver decides whether the derived columns need the database server
date: 2026-08-04
status: open
coveredBy: []
---

# D-DIS-012 — The driver decides whether the derived columns need the database server

**`DefaultTcaSchema::enrich()` reaches the server only where the driver asks for
a version: MySQL, MariaDB and PostgreSQL do, SQLite does not.**

`D-DIS-008` settled this against a MySQL project and named a database server
that responds as the condition the answer has. Every installation this
repository makes runs `pdo_sqlite`, and there the derivation runs with the
database out of reach entirely.

## Evidence

- The platform is where a connection opens or does not.
  `Connection::getDatabasePlatform()` builds a version provider —
  `StaticServerVersionProvider` where `serverVersion` sits in the connection
  parameters, the connection itself otherwise — and hands it to
  `Driver::getDatabasePlatform()`. Read in doctrine/dbal 4.4.4, which
  `.environments/e-site-13.4`, `e-site-14.3` and `e-site-main` install.
- `AbstractSQLiteDriver` ignores that provider and returns a `SQLitePlatform`.
  `AbstractMySQLDriver` and `AbstractPostgreSQLDriver` open with
  `$versionProvider->getServerVersion()`, which is `Connection::connect()`.
- 3.10.6, which `e-site-12.4` installs, splits the same way through
  `VersionAwarePlatformDriver`. MySQL and PostgreSQL implement it, SQLite does
  not. So `detectDatabasePlatform()` asks for a version only for the first two,
  and `serverVersion` in the parameters answers before any connection there too.
- TYPO3's `Core\Database\Connection` overrides neither method on 12.4 or on
  14.3, so Doctrine's path is the whole of it.
- The drivers TYPO3 has a check for are `mysqli`, `pdo_mysql`, `pdo_pgsql` and
  `pdo_sqlite` — `DatabaseCheck` on 12.4 and on 14.3, with no mssql among them.
- Measured on 2026-08-04 against `.environments/e-site-14.3`: `pdo_sqlite`, no
  `serverVersion`, and a database file at a path inside the DDEV container.
  Driven through this machine's PHP 8.3.23, whose PDO carries `mysql` alone,
  `typo3_schema_lookup` answered `pages` with its 69 derived columns. There was
  no driver on that host to open a database with.

## Decided

- The description states no precondition. It says a boot of the installation
  asks the core for the columns. And that the tool says so rather than answers
  empty when it cannot. That holds on every driver. The other
  installation-backed descriptions name fallbacks and never preconditions, and a
  condition stated there makes a SQLite caller skip a call that would have
  answered.
- The per-installation truth arrives at call time instead, measured rather than
  predicted. The enrichment throws, `probe.php` reports the topic `unavailable`,
  and `SchemaLookup` answers `unsupported` with the exception in it.
- `probe.php`'s comment above the topic names this entry for the same reason,
  since that is where the connection starts.
- This entry revokes `D-DIS-008` rather than leaves it `confirmed`. Its title
  and its statement both name a server that responds as the condition. The
  reader of a listing has the title and the status and nothing else.

- Nothing runs over the split: it needs a MySQL installation with its server
  stopped, which no environment here holds. `ToolContractTest` covers the two
  unanswerable paths and not which driver produced one.
## Assumed

- The installation runs one of the four drivers TYPO3 checks for. A connection
  configured with a `driverClass` of its own decides this for itself, and
  nothing here reads which way it decided.
- Nothing else on the probe's path opens the connection. The enrichment reaches
  it once per table, for the platform, before the field loop.

## Wrong if

- A SQLite installation loses this answer while its project is down. That is a
  DBAL release where `AbstractSQLiteDriver` asks the version provider, or a
  TYPO3 release whose `Connection` overrides `getDatabasePlatform()` and
  connects.
- A MySQL, MariaDB or PostgreSQL installation whose parameters state a
  `serverVersion` loses it too. Then the provider is not what decides this and
  the split above is one condition short.
- TYPO3 gains a driver none of this covers; mssql was an assumption once and is
  in neither branch. Then these four no longer say which installations pay.

## Since then

Re-read on 2026-08-23 and the split holds where a reader can see it. The SQLite
driver returns its platform and never asks the version provider, in both DBAL
versions the environments install. No covered branch overrides
`getDatabasePlatform()`, so Doctrine's path is the whole of it. The driver list
reads more precisely than the third bullet. The check map is what "has a check
for" means. The wider map beside it carries a removal mark with no check
registered for any of its three. The second bullet is the one no environment
here can produce, since every one is `pdo_sqlite`.
