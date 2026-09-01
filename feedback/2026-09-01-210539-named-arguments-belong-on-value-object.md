---
date: 2026-09-01T21:05:39+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Named arguments belong on value object constructors only, not on regular method calls

## Observation

Migrating a local memory into the server: a standing correction from this user about PHP named arguments.

Use named arguments only when constructing value objects, never on ordinary method calls. Regular method calls take positional arguments.

Named arguments are reserved for value object construction, where they make a multi-parameter constructor readable. The same syntax on a service method call is not wanted.

This is a convention that cuts against a common default: a session that reaches for named arguments to make a boolean flag readable at the call site will be corrected.

## Query

Migrated from a local project memory file (feedback_named_args.md). A standing correction from this user on PHP call style.

## Suggestion

Add to the PHP conventions hints, with the value-object test stated plainly enough to apply without judgement: named arguments on new for a value object, positional everywhere else. Worth pairing with the reason, since the rule looks arbitrary otherwise — a value object's constructor parameters are its named fields, whereas a method's parameters are an argument list whose names are not part of what the caller is meant to depend on.
