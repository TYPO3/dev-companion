---
date: 2026-10-22T10:57:11+00:00
category: idea
status: closed
closed: 2026-10-22
model: claude-opus-5-5
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# The v15 deprecation sweep reframed a reviewer finding as a 14.3-only regression

## Observation

Task: review and finish core change 96975 (f:form.select bound to an ObjectStorage gets a [] name).
A reviewer said: LazyLoadingProxy implements \Iterator, so a "\Traversable" check gives a single-value select a list name.
The development skill told me to run typo3_changelog_lookup with type deprecation, version 15, no query, limit 200. It returned 17 entries in one call. One was Deprecation-110347 "LazyLoadingProxy and LazyObjectStorage". It says the core creates native lazy objects since 15.0.
So the regression hits the 14.3 backport, not main. I put that into my assessment and the reply. Without the sweep I would not have seen it. Nothing in the diff names that entry. Keep this behaviour: one call, the whole major, tags on each entry.

## Query

typo3_changelog_lookup type=deprecation version=15 limit=200
