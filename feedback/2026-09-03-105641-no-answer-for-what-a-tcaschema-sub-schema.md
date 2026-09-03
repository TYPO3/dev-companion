---
date: 2026-09-03T10:56:41+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_schema_lookup
directory: /home/benji/projects/typo3-cms
---

# no answer for what a TcaSchema sub-schema contains and which core APIs resolve fields through it

## Observation

Task: find an open impexp bug on forge.typo3.org and fix it — Forge #108801, which turned out to be ReferenceIndex resolving relation fields from the table schema while DataHandler resolves them through the record's sub schema.

The whole design of the patch turned on two facts about the Schema API that I could not get from this server and read out of the checkout instead:

1. A sub-schema's getFields() contains ONLY the fields in that record type's showitem (plus palettes), not all columns. Read from TcaSchemaBuilder::findRelevantFieldsForSubSchema(). This is the fact that made the obvious fix wrong: I could not simply swap the table schema for the sub-schema in ReferenceIndex::getRelations(), because every relation field not in showitem would have stopped being indexed. The patch had to union the two and resolve per field. Nothing warned me; had I not opened TcaSchemaBuilder I would have shipped a silent regression that the core's own suites would not have caught, because core TCA puts its relation fields in showitem.

2. Which core APIs already resolve through the sub schema. I established this with a grep for getSubSchema across typo3/sysext/core/Classes: DataHandler::resolveFieldConfigurationAndRespectColumnsOverrides(), FlexFormTools, RecordFactory, VisibleSchemaFieldsCollector, PageDoktypeRegistry — and ReferenceIndex conspicuously absent. That grep is what turned "the reporter's TCA is unusual" into "the branch contradicts itself", which is the argument the whole bugfix rests on.

I also worked out from RecordFactory that resolving a record type must skip SchemaTypeInformation::isPointerToForeignFieldInForeignSchema(), because sys_file_reference declares type "uid_local:type" and resolving it costs a database query per record — in the reference index, that is a query per sys_file_reference row on a full index rebuild. I would work all three of these out again from scratch in the next session.

typo3_task_guide gave me four hint groups (impexp-import-placement, impexp-artifact, tca-formengine, datahandler-relations). datahandler-relations was genuinely useful — it told me the parent column of a foreign_field relation holds the child count rather than a uid list, which is exactly the mechanism behind the reported corruption. But none of the four covers the Schema API itself.

## Query

Not asked — this is the question I answered from the checkout instead. Would have been typo3_hint_lookup(paths=["typo3/sysext/core/Classes/Schema/TcaSchema.php","typo3/sysext/core/Classes/Database/ReferenceIndex.php"]) or a hint id along the lines of tca-sub-schema. Task: fix Forge #108801 on TYPO3 v15.0.0-dev.

## Suggestion

Add a hint (id something like tca-sub-schema) carrying: what a sub-schema contains versus the table schema (showitem + palettes only, columnsOverrides merged by array_replace_recursive); that the relation map is NOT overridable per type (TcaSchemaBuilder says so in a comment); the list of core call sites that resolve fields through a sub-schema, DataHandler among them; and the isPointerToForeignFieldInForeignSchema caveat with the sys_file_reference example and its per-record query cost. Match it on paths under typo3/sysext/core/Classes/Schema/ and on any task text naming columnsOverrides, record types or sub schema.

That one hint would have replaced four file reads (TcaSchema.php, TcaSchemaBuilder.php, RecordFactory.php, SchemaTypeInformation.php) plus two greps, and is the single answer that would have made this session materially shorter.
