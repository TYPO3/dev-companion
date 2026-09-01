---
date: 2026-08-31T23:37:57+00:00
category: missing-knowledge
status: closed
closed: 2026-09-01
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_rule_lookup, typo3_schema_lookup
directory: /home/benji/projects/site-tierheim
---

# Nothing covers renaming an extension key, table and CType in an instance that already holds content

## Observation

Task: relaunch tierheim-wiesbaden.de on TYPO3 v14. At 22:47 the user asked that the word "tierheim" not appear in code — "wir sollten im code das wort tierheim auch nciht verwenden sondern animalshelter". At 23:06 the vendor changed too: "der package name kann bk2k/animalshelter-* sein". The installation at that moment held 42 pages, 136 content elements, 3101 animal records and 3051 files, all imported.

This was the single most expensive hour of the session and I had nothing to ask for. I asked this server nothing during it. Three MCP calls in the 23:00 hour, none about the rename.

What the rename actually touched, learned by breaking each one:

1. Directories, composer names, PSR-4 namespaces, site set names, extension keys, XLF paths. Mechanical, fine.
2. Table names tx_tierheim_* and seven tt_content columns. New tables are created by extension:setup; the old ones stay, and the data has to be moved. I wrote INSERT INTO new SELECT * FROM old. It failed on a type error, which is the only reason I looked: the two tables were generated from the same TCA but the column ORDER differed, so the positional insert would have shifted every value one column along. Explicit column lists fixed it. Nothing warned me; an error did.
3. sys_file_reference.tablenames and sys_refindex carry table names as data. I caught the first, missed the second until a query showed 104 stale rows.
4. tt_content.CType values are data, not configuration. I updated them and verified: all 136 correct.
5. And then I re-ran the migration command to prove it was idempotent — and it wrote 118 of them back to `tierheim_*`. Cause: the command composed the CType from a prefix plus a suffix rather than spelling it, so my literal-by-literal replacement never touched it. The elements built with literal CTypes (home page teasers, animal lists) survived; everything from the generic path did not. The user found it, in the backend, and sent me a screenshot of "Invalid value (tierheim_text)". 107 browser tests had stayed green through it.

The class of defect: after renaming an identifier that is both code and data, any command that seeds or reconciles content will undo the data half if it composes that identifier rather than spelling it. That is knowable in advance and I did not know it.

Two smaller things in the same hour I also had to establish by hand: that `database:updateschema` is not a core command (I ran it, got "There are no commands defined in the database namespace", and only afterwards called typo3_hint_lookup id=installation-boot, which says exactly that — right answer, arrived after the failure because I called it late); and that DDEV's YAML parser rejects a duplicate `web_environment` key where Python's accepts it, so a block I appended made `ddev restart` fail.

## Query

Whole-session task; the rename stretch 22:47-23:15. No MCP call was made about renaming. Queries that would have been made had I expected an answer: "rename extension key and table in an installation with content", "tx_ table rename existing records", "CType value migration tt_content". Files: packages/*/Configuration/TCA/tx_tierheim_*.php, packages/tierheim_migration/Classes/Command/MigrateLegacyContentCommand.php, config/sites/main/settings.yaml, .ddev/config.yaml.

## Suggestion

A rule_lookup document, `project/refactoring/renaming-an-installed-extension`, carrying the order and the traps:

- Which identifiers live in the database rather than in code, so a rename is a data migration and not only a substitution: tt_content.CType, tt_content.list_type, pages.backend_layout, sys_file_reference.tablenames and .fieldname, sys_refindex.tablename and .ref_table, sys_registry keys, and any FlexForm data structure keyed by CType.
- That extension:setup creates the new tables and leaves the old ones, so the move is the caller's, and that INSERT ... SELECT * maps positionally: two tables generated from the same TCA can differ in column order, and the failure mode is silent where the types happen to line up.
- That sys_refindex has to be rebuilt afterwards (referenceindex:update), and how to check it is clean.
- The one that cost me the hour: any command that seeds, imports or reconciles content must be read for composed identifiers before it is run again. A search for the old prefix in string concatenation, not only in string literals. State it as the check, because the substitution looks complete when the literals are done.
- What proves it: a query for the old prefix across CType, list_type, backend_layout, tablenames, ref_table — not a green test suite. Mine was green.

The neighbouring hint installation-boot already owns "a clone is missing the database"; this is its mirror — the database is present and the code moved out from under it.
