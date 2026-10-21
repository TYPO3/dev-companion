---
date: 2026-10-21T15:31:39+00:00
category: idea
status: closed
closed: 2026-10-21
model: claude-opus-5-5
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# Class-name query returns one unrelated strict-types entry instead of saying no rework exists

## Observation

Task: triage Forge #79571, check if the core reworked ObjectStorageConverter since 2017. The query returned one entry, Important-87603 "Classes use strict mode and scalar type hints" (10.0), matched in the body. That entry only lists the class among many. The answer did not say that no entry changes the behaviour of the class. I read git log on the file to learn the real history: 89660 (2025) and the 2024 cleanup.

## Query

typo3_changelog_lookup(query="ObjectStorageConverter")

## Suggestion

Mark body matches where the identifier occurs only in a long list, so the reader knows the entry is not about the class. Point to typo3_gerrit_lookup with the file path for the change history, which answered the question well in this session.
