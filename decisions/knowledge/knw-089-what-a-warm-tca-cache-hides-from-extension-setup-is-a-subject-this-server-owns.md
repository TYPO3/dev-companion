---
id: D-KNW-089
title: 'What a warm TCA cache hides from `extension:setup` is a subject this server owns'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::theSchemaStepIsSaidToMigrateFromTheCachedTca
---

# D-KNW-089 — What a warm TCA cache hides from `extension:setup` is a subject this server owns

**The schema step of `typo3 extension:setup` migrates from the cached TCA and
reports success either way, and nothing below `knowledge/` says so.**

So the feedback goes to the queue at `normal`, on `extension-schema-sql`. The
corpus describes the command three times and each account is right about the
case it names. None of them names the case the feedback hit. That is a TCA file
added to a package that was already active, on an installation whose caches are
warm.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe` on
  the feedback's own query reaches `tca-formengine`, `installation-upgrade`,
  `sitepackage-initial-content` and `caching`. On
  `"table does not exist after extension:setup reported success"` it reaches
  `installation-upgrade`, `sitepackage-initial-content`, `impexp-artifact` and
  `upgrade-wizards`. No probe reaches `extension-schema-sql`, which is the hint
  the feedback proposes as the home.
- Nothing covers it. Five statements below `knowledge/` name `extension:setup`:
  two in `hints/project.json`, one in `hints/upgrade.json`, two in
  `hints/distribution.json`. They describe what the command migrates and what it
  imports, and none of them mentions the TCA it migrates from. `caching` is
  about how to declare and inject a cache in code.
- Two of them prescribe the opposite order. `installation-upgrade` says code,
  then database, then caches, and `installation-boot` puts `cache:flush` after
  `extension:setup` in the same sequence. Both are correct for the case they
  describe, because a package that arrived or left changes the cache identifier
  below.
- The success message is unconditional. `SetupExtensionsCommand::execute()`
  calls `$io->success(...)` and returns `Command::SUCCESS` after
  `PackageSetup::setup()`, whatever that returned. The only failure path is an
  extension key the system does not know, and the command prints the
  `FlashMessage`s that come back as warnings.
- The table list comes from the cached TCA and from nowhere else.
  `PackageSetup::updateDatabaseSchemaForAllPackages()` builds its statements
  from `SqlReader::getTablesDefinitionString()`, which reads each package's
  `ext_tables.sql` off disk, and hands them to `SchemaMigrator`.
  `SchemaMigrator::ensureTableDefinitionForAllTCAManagedTables()` then adds an
  empty `CREATE TABLE` for every name in `$this->tcaSchemaFactory->all()`. So a
  table declared only in TCA exists for the migrator exactly as far as
  `TcaSchemaFactory` knows it.
- What `TcaSchemaFactory` knows is what the cache held. `Bootstrap` loads it
  from `$GLOBALS['TCA']`, which is `TcaFactory::get()`, which returns the
  `cache.core` entry where there is one and reads `Configuration/TCA/` only
  where there is not.
- The cache entry does not turn over on a new file. Its identifier is
  `(new PackageDependentCacheIdentifier($this->packageManager))->withPrefix('tca_base')`,
  which is the active package list. So a new package requirement invalidates it
  and a new file in a package already in that list does not. The command's own
  `PackagesMayHaveChangedEvent` does not close the gap either:
  `PackageManager::packagesMayHaveChanged()` rescans the available packages and
  flushes nothing.
- That is also why the feedback's aside is right. A fresh clone has no cache
  entry, so the first run builds TCA from the files and creates the tables in
  one pass. The trap needs a warm cache to appear at all.
- The read is uniform across the covered lines it checked.
  `PackageDependentCacheIdentifier` builds `tca_base` on `.checkouts/12.4` at
  `31f881a212`, `.checkouts/13.4` at `fccbd407d8`, `.checkouts/14.3` at
  `627949e9dd` and `.checkouts/main` at `3a9f0b5e3c`. The success message is
  unconditional in the setup command on all of them.
- The session that reported paid two round trips for it and says the loud
  failure is what made them cheap. It seeded content at once. A session that did
  not would have gone on in the belief that the setup had succeeded.

## Decided

- Step 1a, and queued rather than closed on the spot. The statement is about
  TYPO3 rather than about the wording of a rule that exists.
  [`judging.rst`](../../documentation/records/judging.rst) puts that on the
  todo's side of the line even where the read is done.
- `normal` rather than the `low` the card arrived at. The corpus does not merely
  omit the precondition, it states the reverse order twice for the neighbour
  case. The failure is an absent table while every command answers `[OK]`.
- Not `high`. The repair is one `cache:flush`, the session found it in two round
  trips, and nothing else waits on it.
- `extension-schema-sql` is the home, which is the feedback's own argument and a
  good one. It is the hint a caller reads while it concludes that it needs no
  `ext_tables.sql`, which is the moment before this bites. Reaching it from the
  symptom is part of the work rather than a consequence of writing it, because
  no probe reaches it today.
- `installation-boot` and `installation-upgrade` keep their order. It is right
  for the case each describes, and what the todo decides is whether either owes
  a neighbour line rather than a correction.
- Neither archived nor trimmed. The feedback separates the half it blames itself
  for, a read of `typo3_schema_lookup` as evidence a table exists, from the half
  nothing documents. Only the first has an answer anywhere today, in that tool's
  own description.
- The todo words the discriminator rather than this entry invents it.
  `feedback/2026-08-17-212800` asks for one on every procedural hint and names
  this failure as its third example. This is that proposal's worked case, and
  its judgement stays its own card.

## Assumed

- That the statement carries no `since`. The mechanism reads the same on all
  four checkouts, but a table declared purely in TCA is a v13 behaviour.
  `extension-schema-sql` already binds that half with `since: 13`. On 12.4
  `DefaultTcaSchema` only enriches tables `ext_tables.sql` declares, so what a
  warm cache hides there is columns rather than tables. Which of the two the
  sentence is about is what decides the bound, and the todo establishes it.
- That the identifier is the whole of the invalidation. This run found nothing
  else that clears `cache.core` inside the command, and read no listener on
  `PackagesMayHaveChangedEvent` outside `PackageManager`.
- That one session wrote this feedback and the sixteen beside it. They share a
  directory, a model, a subject and three quarters of an hour, and nothing in a
  feedback records a session.

## Wrong if

- The run finds that `extension:setup` creates the table on a warm cache anyway.
  Something between `TcaFactory` and the migrator would rebuild TCA where the
  code says it reads the entry. The feedback would be about the session's own
  installation rather than about the command.
- A release makes the TCA cache identifier depend on the files rather than on
  the package list. The statement would become version-bound at that release and
  the hint would carry an `until`.
- The server delivers the corrected hint and a session still ships a package
  whose tables never came to be. The gap would be in the wording or in the
  delivery, and this entry would have answered the expensive rung for a cheap
  problem.
- A second feedback reports the same trap from `installation-upgrade` rather
  than from the schema hint. The home decided here would be the wrong one, and
  the statement would belong where the corpus states the upgrade order.

## Since then

The statement carries no bound, and the assumption that left the question open
settled the other way round. What a warm cache hides is not only tables. On the
oldest major the enrichment skips a table the schema file did not declare. But
it reads the same cached TCA, and TCA alone creates an MM table. So a stale
entry hides the derived columns and the MM tables there, and whole tables from
the next major on. The mechanism itself is one sentence on all four, so the new
statement says what the identifier derives from and stays unbound.

Neither neighbour hint took a pointer. Each describes a case that invalidates
the entry as part of its own procedure. What the trap needs is a TCA file that
changes while the package list does not.
