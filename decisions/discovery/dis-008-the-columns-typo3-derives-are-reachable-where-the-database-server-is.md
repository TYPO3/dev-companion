---
id: D-DIS-008
title: The columns TYPO3 derives are reachable where the database server is
date: 2026-08-02
status: revoked
revokedBy: D-DIS-012
coveredBy: []
---

# D-DIS-008 — The columns TYPO3 derives are reachable where the database server is

**`DefaultTcaSchema::enrich()` needs a booted container and a reachable database
server, and nothing else — not a populated schema, not SQL, not a log.**

`REVIEW-02` run 5 left the v13 delta-only rule for `ext_tables.sql` unraised,
"since I did not verify each column against the schema analyzer's TCA-derived
output". Whether that output can be had at all is what this settles.

## Evidence

- Read in `.checkouts/13.4` and `.checkouts/14.3`. `enrich()` is three passes:
  the ctrl-derived columns, the TCA-column-derived ones, and the MM tables. Only
  the middle one touches a connection, at `456` on 13.4 and `497` on 14.3. It
  fetches the platform per table before the field loop.
- Exactly one branch uses that platform, `891` and `902`, a SQLite workaround
  that stores a decimal-formatted number field as a string. Every other column
  it derives is the same on every platform, and `quote()` writes backticks
  unconditionally.
- `getConnectionForTable()->getDatabasePlatform()` needs the server, not the
  schema: on MySQL and MariaDB the platform follows from the server version. So
  an installation whose database has no TYPO3 tables yet still answers, and one
  with no database at all does not.
- `enrich()` demands a `Table` object per TCA table and throws otherwise, which
  is what makes the derived set readable. Handed empty tables, what it adds is
  exactly the columns an `ext_tables.sql` may leave out.
- The way in exists. `probe.php` already boots the container and reads `$TCA`
  out of it, which is `D-DIS-005`. On 13.4 the class reads `$GLOBALS['TCA']`
  itself and takes no constructor argument; on 14.3 it takes a
  `TcaSchemaFactory` and defaults it through `makeInstance`.

## Decided

- Reachable, under one condition more than the other runtime answers have: a
  database server that responds. A stopped project therefore answers this with
  the `unsupported` shape the installation-backed tools already carry. The
  reason is the one `Typo3Cli` already reports for a container that is down.
- The tool is a step of its own and sits in the queue, bounded to the derived
  side. That is the columns TYPO3 adds for a table, and nothing about the live
  schema.

- Nothing holds it yet: what this entry settles is that the answer exists, and
  the todo that builds it is what a test can hold.
## Assumed

- The class is `@internal` on both branches and the probe would call it anyway,
  which is the same bargain `D-DIS-005` took for the registries. What makes it
  bearable is that the question goes to the installation under read rather than
  to a bundle. A signature that moves fails on that installation and the answer
  reports it, rather than makes a bundled statement wrong everywhere.

## Wrong if

- `enrich()` starts to need the schema rather than the server. That is a read of
  a table that must already exist, or a doctrine platform that connects for more
  than its version. Then the answer is available only after an installation. The
  rule stays unraisable in a fresh extension checkout, which is where the
  question comes from.
- The one SQLite branch grows into several. Then the derived set is no longer a
  property of TCA and becomes one of the platform the caller happens to run.

## Confirmed on 2026-08-02

Run rather than read: `DefaultTcaSchema::enrich()` answered through `probe.php`
with 26 TCA tables in and 27 out, `tt_content` with 72 derived columns. So one
empty `Table` per TCA table is what makes the derived set readable. What it
needed was a server that answers rather than a schema in it. What no test here
reaches is the filled path, since nothing injects a runtime answer. The one
thing the account did not predict: the answer names the relation tables as such.
TYPO3 creates them, and a list that showed them beside the others would read as
work somebody has to do.

## Since then

The condition is the driver's rather than this answer's, and the statement above
is a MySQL project generalised. `AbstractSQLiteDriver` ignores the version
provider; `AbstractMySQLDriver` and `AbstractPostgreSQLDriver` open on it, which
is the connection. Read in both DBAL versions the environments install. So a
MySQL, MariaDB or PostgreSQL installation loses this answer while its server is
down, and a SQLite one never does. Even the first three keep it where the
parameters state a `serverVersion`. Measured on 2026-08-04 with no driver on the
machine to open the database with. What holds is the rest: no schema, no SQL,
and an empty `Table` per TCA table.

## Revoked on 2026-08-04

By the account above, once the work took it up. The title and the statement name
a database server that responds as this answer's condition, and that turned out
to belong to the driver. A listing shows the title and the status, so
`confirmed` beside this sentence reads as a claim that every installation pays
it. `D-DIS-012` names the split. The evidence stays — the answer exists, needs
no schema and no SQL — and the successor's **Wrong if** is a different list.
