---
date: 2026-09-01T21:05:16+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Annotate as little as possible in TypeScript; never repeat a type narrowed upstream

## Observation

Migrating a local memory into the server: a standing correction from this user about TypeScript annotations. In his words, "wir wollen so wenig Auszeichnungen wie nötig" and "so viel ableiten wie es geht" — write an annotation only where inference genuinely cannot reach.

- Once a call is narrowed (querySelectorAll<HTMLInputElement>(...)), the callback parameter must not repeat it: .forEach((element) => ...).
- For lit's Task, annotate nothing on task: and put as const on args: (args: () => [this.a, this.b] as const).
- Narrow at the SOURCE — return type, type argument, collection type — rather than in every consumer.
- Removing a name is not the same as removing an annotation. If a widened type had a meaningful alias, keep the alias (type ActionDispatchDelegate = (...args: never[]) => void); do not inline the bare structural type.

Why it matters beyond taste: a repeated annotation is a second source of truth that drifts, and it actively HIDES defects — a callback annotated (x: any), or with a wrong interface, silently overrides the real element type. Removing such annotations in the TYPO3 core surfaced three real bugs: a copy-pasted response type, a number assigned to innerText, and node.expanded where the interface only has __expanded.

Application rule: when adding a type argument or narrowing a return type, delete the now-redundant downstream annotations in the same change. When a removal makes the compiler complain, first ask whether the ANNOTATION was lying before restoring it.

## Query

Migrated from a local project memory file (feedback_prefer_inferred_types.md). A standing correction from this user on backend TypeScript.

## Suggestion

Add to the backend-typescript hints. The last rule is the one with teeth and should be stated as a procedure rather than a preference: a compiler error appearing after an annotation is removed is evidence about the annotation, not proof it was needed. Worth noting that the core's ESLint config has no-inferrable-types OFF, so the linter will not push in this direction and the reviewer will.
