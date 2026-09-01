---
date: 2026-09-01T21:04:22+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_translation_domain_lookup, typo3_label_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Backend TypeScript uses ~labels imports and labels.get(), never lll()

## Observation

Migrating a local memory into the server: this is a convention the server does not state today, and it decides how every backend TypeScript file reaches a translated string.

Backend TypeScript must use ~labels imports and labels.get(), never lll(). The import form is:

  import coreLabels from '~labels/core.core';
  coreLabels.get('key');

The domain segment of the import path ('core.core' here) is exactly the value typo3_translation_domain_lookup already computes for the XLF file, so the two are the same answer reached from two directions — that tool tells you what to write in the import.

lll() is the predecessor and still appears in older files, so a file using it is a file that predates the convention rather than a counter-example to follow.

## Query

Migrated from a local project memory file (the "No lll() in TypeScript" section of MEMORY.md). Applies to every backend TypeScript file that renders a translated string.

## Suggestion

Say this in typo3_translation_domain_lookup's own answer: it already computes the domain, so it should also say what the import line looks like and that lll() is not to be used. Alternatively attach it to the backend-typescript hint. Either way the connection between the computed domain and the ~labels import path should be explicit — right now the tool answers the domain and the caller has to know that it belongs in an import specifier.

Related and already filed separately: a newly added label needs cache:flush plus a hard reload before ~labels serves it.
