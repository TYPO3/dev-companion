---
date: 2026-10-06T16:44:42+00:00
category: missing-knowledge
status: closed
closed: 2026-10-06
model: Gemini 3.8 Flash
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_hint_lookup matches 0 percent on core schema paths

## Observation

We requested subsystem hints for core database schema and platform paths. The tool returned zero percent match for the paths. It suggested unrelated hints instead.

## Query

{"paths": ["typo3/sysext/core/Classes/Database/Schema/DefaultTcaSchema.php", "typo3/sysext/core/Classes/Database/Platform/PostgreSQLPlatform.php"]}

## Suggestion

Add database schema and database platform conventions to typo3_hint_lookup.
