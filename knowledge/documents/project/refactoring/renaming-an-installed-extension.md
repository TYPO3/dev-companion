---
description: >-
  What a rename of an extension key, its tables or its CTypes touches in an installation that already holds content, in the order it has to be done, and the query that proves it rather than a green suite.
whenToUse: >-
  When an extension key, a table name, a CType or a vendor prefix changes in a project whose installation already has records — the mirror of booting a clone, where the code moved out from under a database that stayed.
hints:
  - installation-boot
  - content-elements
---

# Renaming an Extension That Already Holds Content

A rename is a data migration wearing a substitution's clothes. Directories,
namespaces, extension keys and file paths are the half a search and replace
finishes; the other half is every place TYPO3 stored one of those names as a
value, and nothing on the code side reports it.

## What Moves Itself and What Does Not

`extension:setup` creates the new tables and columns and removes nothing. Its
schema step selects the `add`, `change`, `create_table` and `change_table`
suggestions and never a drop, so after a rename the installation has both
tables: the new one, empty, and the old one, holding every record. Moving the
rows is the caller's, and so is dropping what is left over — the install tool's
database analyzer is where a destructive change is confirmed by a person.

`INSERT INTO new SELECT * FROM old` is the wrong instrument for it. `SELECT *`
maps positionally, and two tables generated from the same TCA do not have to
have the same column order: TYPO3 derives the columns it adds itself, and the
order they arrive in depends on what each `ext_tables.sql` declared and when.
The failure is a type error where the shifted columns disagree and silence where
they happen to line up, which is the expensive case. Write the column list out
on both sides.

## Where an Identifier Is Stored as a Value

Each of these is a name in the database rather than in a file, and a rename that
does not carry them leaves records pointing at something nothing declares:

- `tt_content.CType`, one row per content element. A value nothing declares any
  more shows in the backend as an invalid-value badge on the record and renders
  as nothing at all. On TYPO3 13 and below a plugin's own identifier is stored
  in `tt_content.list_type` beside it; v14 has no such column, and a plugin is a
  CType there like any other element.
- `pages.backend_layout` and `pages.backend_layout_next_level`, where a layout
  from page TSconfig is stored with the `pagets__` prefix in front of its
  identifier.
- `sys_file_reference.tablenames` and `.fieldname`, which say what a file
  reference belongs to.
- `sys_refindex.tablename`, `.field` and `.ref_table`, which are the index's own
  copy of the same relations. It is rebuilt rather than rewritten:
  `referenceindex:update`, and `--check` first says what it would change.
- `sys_registry`, where an initialisation import records that it has run:
  `extensionDataImport` holds `<extension key>:Initialisation/dataImported`. The
  new key has no such entry, so a package that ships `Initialisation/data.t3d`
  or `data.xml` imports it a second time on the next setup.
- The FlexForm a record stores in `pi_flexform`, whose data structure is
  selected by the record's own type. A structure keyed by a CType that no longer
  exists resolves to nothing, and the stored XML stays as it is.

## The Trap That Is Not a Substitution

**A command that seeds, imports or reconciles content composes identifiers as
readily as it spells them.** A migration that builds a CType from a prefix and a
suffix is untouched by a rename of every literal in the repository, and the next
run writes the old values back over the ones that were corrected. Read every
such command for concatenation before running it again — the search is for the
old prefix in a string being assembled, not only in a string literal, and the
substitution looks finished when the literals are done.

## What Proves It

A test suite passing proves its assertions. What proves a rename is a query for
the old prefix over the columns that carry a name as data — `CType`,
`backend_layout`, `tablenames`, `ref_table` — and a reference index that comes
back clean. Read the backend afterwards as well: the record list and the page
module are where an invalid value is drawn, and a frontend that renders is not
evidence about either.
