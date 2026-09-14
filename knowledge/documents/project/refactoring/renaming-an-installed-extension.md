---
description: >-
  What a rename of an extension key, its tables or its CTypes touches in an installation that already holds content, in the order you do it, and the query that proves it rather than a green suite.
whenToUse: >-
  When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.
hints:
  - installation-boot
  - content-elements
---

# Renaming an Extension That Already Holds Content

A rename is a data migration in a substitution's clothes. A search and replace
finishes the directories, the namespaces, the extension keys and the file paths.
The other half is every place TYPO3 stored one of those names as a value.
Nothing on the code side reports it.

## What Moves Itself and What Does Not

`extension:setup` creates the new tables and columns and removes nothing. Its
schema step selects the `add`, `change`, `create_table` and `change_table`
suggestions and never a drop. So after a rename the installation has both
tables: the new one, empty, and the old one, with every record. You move the
rows, and you drop what remains. A person confirms a destructive change in the
install tool's database analyzer.

`INSERT INTO new SELECT * FROM old` is the wrong instrument for it. `SELECT *`
maps by position, and two tables from the same TCA need not have the same column
order. TYPO3 derives the columns it adds itself, and their order depends on what
each `ext_tables.sql` declared and when. Where the shifted columns disagree, the
failure is a type error. Where they happen to line up, it is silence, which is
the expensive case. Write the column list out on both sides.

## Where TYPO3 Stores an Identifier as a Value

Each of these is a name in the database rather than in a file. A rename that
does not carry them leaves records that point at something nothing declares:

- `tt_content.CType`, one row per content element. A value nothing declares any
  more shows in the backend as an invalid-value badge. In the frontend it
  renders as nothing at all. On TYPO3 13 and below, `tt_content.list_type`
  beside it stores a plugin's own identifier. v14 has no such column, and a
  plugin is a CType there like any other element.
- `pages.backend_layout` and `pages.backend_layout_next_level`. A layout from
  page TSconfig stands there with the `pagets__` prefix in front of its
  identifier.
- `sys_file_reference.tablenames` and `.fieldname`, which say what a file
  reference belongs to.
- `sys_refindex.tablename`, `.field` and `.ref_table`, the index's own copy of
  the same relations. You rebuild it rather than rewrite it:
  `referenceindex:update`, and `--check` first says what it would change.
- `sys_registry`, where an initialisation import records that it has run.
  `extensionDataImport` holds `<extension key>:Initialisation/dataImported`. The
  new key has no such entry. So a package that ships `Initialisation/data.t3d`
  or `data.xml` imports it a second time on the next setup.
- The FlexForm a record stores in `pi_flexform`. The record's own type selects
  its data structure. A structure keyed by a CType that no longer exists
  resolves to nothing. The stored XML stays as it is.

## The Trap That Is Not a Substitution

**A command that seeds, imports or reconciles content composes identifiers as
readily as it spells them.** A migration that builds a CType from a prefix and a
suffix survives a rename of every literal. The next run writes the old values
back over the ones you corrected. Read every such command for concatenation
before you run it again. Search for the old prefix in a string the code
assembles, not only in a string literal. The substitution looks finished when
you have replaced the literals.

## What Proves It

A test suite that passes proves its assertions. What proves a rename is a query
for the old prefix over the columns that carry a name as data. Those are
`CType`, `backend_layout`, `tablenames` and `ref_table`. A reference index that
comes back clean proves the rest, and a read of the backend afterwards proves it
once more. The record list and the page module are where TYPO3 draws an invalid
value. A frontend that renders is no evidence about either.
