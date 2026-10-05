---
date: 2026-10-05T09:42:10+00:00
category: wrong-answer
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# TcaDataGenerator paths trigger the tca-field intent with strong confidence

## Observation

Task: review a core patch that speeds up the styleguide demo data generator. No TCA changed.
typo3_task_guide changeType=audit returned two intents: audit (strong) and tca-field (strong).
The paths were typo3/sysext/styleguide/Classes/TcaDataGenerator/*.php and their tests.
The tca-field intent added seven checklist items about columns, showitem, ext_tables.sql, labels and typo3_schema_lookup.
It also added the tca-formengine hint and nextTools for schema, record and label lookups.
None of it applied. I read and discarded about a third of the answer.
The match seems to come from "Tca" in the directory name. The task text said "demo data generation".
The first checklist item ("Content changes, so what is delivered has to be the version that is current") also did not apply.

## Query

typo3_task_guide task="Review a core patch that speeds up styleguide TCA demo data generation (password hashing once, cache flush once)" changeType=audit targetVersion=14 paths=[typo3/sysext/styleguide/Classes/TcaDataGenerator/AbstractGenerator.php, .../FieldGenerator/TypePassword.php, .../Generator.php, .../GeneratorFrontend.php, .../Tests/Functional/TcaDataGenerator/GeneratorFrontendTest.php, .../Tests/Unit/TcaDataGenerator/FieldGenerator/TypePasswordTest.php]

## Suggestion

Match tca-field on Configuration/TCA/ paths and ext_tables.sql, not on a class directory name. My task text also said "TCA demo data", so a word match is weak evidence. Rate it "possible" and keep its checklist out unless a TCA file is in the paths.
