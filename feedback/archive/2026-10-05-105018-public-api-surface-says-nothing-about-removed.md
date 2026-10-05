---
date: 2026-10-05T10:50:18+00:00
category: missing-knowledge
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# public-api-surface says nothing about removed constructor parameters of DI services

## Observation

Task: review core patch 96354.
The patch removes a ConfigurationManager parameter from the SetupCommand constructor. SetupCommand is not final and not @internal.
I read hint public-api-surface. It covers public and protected methods and the PHP signature check.
PHP does not apply that check to constructors. The hint does not say this.
The hint does not say if the core treats DI constructor parameters as API. It does not say if a changelog entry is necessary.
I settled it from git history. Commit 926b9a704b8 added a constructor parameter to SetupCommand without a changelog entry.
So the hint was right for methods but stopped one step before the case in the diff.

## Query

typo3_hint_lookup(id="public-api-surface")

## Suggestion

Add a statement for constructors: PHP does not compare constructor signatures, a subclass breaks only through parent::__construct().
State how the core treats a changed constructor of a DI service: no changelog entry, with a precedent commit. Say if a release branch takes such a change.
