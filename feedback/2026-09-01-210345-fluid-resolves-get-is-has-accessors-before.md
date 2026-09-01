---
date: 2026-09-01T21:03:45+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# Fluid resolves get/is/has accessors before properties, so hasItems() shadows an items property

## Observation

Migrating a local memory into the server: this is knowledge the server does not answer today, and it belongs beside the existing fluid-object-access hint.

In TYPO3 Fluid, {obj.foo} is resolved by StandardVariableProvider::getByPath() in this order: getFoo(), then isFoo(), then hasFoo(), and only then the public property foo. (vendor/typo3fluid/fluid/src/Core/Variables/StandardVariableProvider.php, around lines 124-148.)

Two consequences that bite DTOs used in templates:

1. A method hasItems() or isOk() is NOT reachable as {obj.hasItems} / {obj.isOk} — Fluid looks for getHasItems / isHasItems / hasHasItems and never the literal hasItems(). The core convention is therefore getHasMorePages() (see SlidingWindowPagination), accessed as {pagination.hasMorePages}.

2. Worse, and the one that actually breaks a template: a bool helper hasItems() shadows a public array property items, because for {obj.items} Fluid tries has + Items = hasItems() BEFORE the items property. The property then yields the bool, and <f:for each="{obj.items}"> fails with "argument each ... is of type boolean". The same happens with hasIssues() shadowing issues.

Rule for DTOs rendered in Fluid: do not name a method has<Property>() / is<Property>() / get<Property>() when a property <property> exists and is accessed in templates. Prefer the property directly ({obj.items}), expose counts via get*Count(), and use non-colliding names for booleans (isOk() where no ok property exists).

## Query

Migrated from a local project memory file (reference_fluid_accessor_shadowing.md). Originally learned while building a DTO whose bool helper shadowed the array property a template iterated.

## Suggestion

Extend the fluid-object-access hint with the resolution order and the shadowing consequence. The searchable phrasing should include the symptom, not only the subject — "argument each is of type boolean", "f:for fails on an array property", "bool method shadows property" — because the reader arrives from the exception, not from the accessor rules. The naming rule for DTOs is the actionable half and should be stated as a prohibition on has<Property>() / is<Property>() beside an existing property.
