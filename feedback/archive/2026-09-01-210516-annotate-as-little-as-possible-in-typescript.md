---
date: 2026-09-01T21:05:16+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# Annotate as little as possible in TypeScript; never repeat a type narrowed upstream

## Observation

Answered on 2026-09-02. `backend-typescript` states the rule beside the two
switched-off lint rules — an annotation goes where inference cannot reach, what
narrows a value is where it is produced, and a second annotation on the same
value is what the code under it is checked against. The procedure this report
called the half with teeth is there with it: removing a redundant annotation is
the change, and the compiler error that follows is evidence about the annotation
before it is a reason to restore it, while an alias that meant something
survives the annotation that repeated it. The `@lit/task` half went to
`backend-lit-task`, where the core carries both forms and the difference is the
rule: `args: () => [...] as const` pins the tuple and the task destructures it
unannotated, and an unpinned `args` makes every task function repeat the types.

Two of the three defects are not written, because they could not be established
against the checkouts. No covered branch assigns a number to `innerText`, and
the property spelled two ways is two types rather than a defect —
`form-editor-tree.ts` declares its own node with `expanded` while the node it
writes `__expanded` on is the backend's, which declares it. `D-KNW-139` carries
that reading.
