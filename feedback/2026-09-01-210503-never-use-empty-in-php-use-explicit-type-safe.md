---
date: 2026-09-01T21:05:03+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Never use empty() in PHP; use explicit type-safe checks instead

## Observation

Migrating a local memory into the server: a standing correction from this user about PHP style.

Never use empty() in a PHP condition — it is not strict enough. It treats 0, '', null, false, '0' and [] alike, which masks defects.

Replace it with an explicit check matching the expected type: ($x ?? false), ($x ?? null) !== null, $x !== [], $x !== '' and so on. When simplifying an existing !isset($x) || empty($x), collapse it to !($x ?? false) or similar rather than to empty().

Worth noting for the server: the core itself still contains a great deal of empty(), so a session that infers style from surrounding code will reproduce it. This is a case where the repository's habit and the reviewer's expectation differ, which is exactly the "a rule is read in both directions" situation — existing empty() calls at a path being changed are a finding, not a local style to preserve.

## Query

Migrated from a local project memory file (feedback_no_empty.md). A standing correction from this user on PHP written for the TYPO3 core.

## Suggestion

Add this to the PHP conventions hints, and state the tension explicitly: the core's existing code uses empty() widely, so the hint has to say that its presence nearby is not licence to write more. Pairing each replacement with the type it assumes would make it directly usable — the substitution is not mechanical, it depends on whether the value is an array, a string or a nullable object.
